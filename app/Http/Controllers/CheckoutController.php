<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Forklift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
    const GST_RATE = 0.05; // Saskatchewan GST 5%
    const PST_RATE = 0.06; // Saskatchewan PST 6%

    // ─────────────────────────────────────────────────────────────
    // GET /checkout?forklift_id=X&tz=America/Regina
    //
    // Renders the checkout form (checkout/create.blade.php).
    // ─────────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $forkliftId = $request->query('forklift_id');

        if (!$forkliftId) {
            return redirect()->route('bookings.create')
                ->with('error', 'Please select a forklift first.');
        }

        $forklift = Forklift::with('location')->findOrFail($forkliftId);

        $tz    = $request->query('tz', 'America/Regina');
        $start = Carbon::now($tz)->addHour()->startOfHour();
        $end   = $start->copy()->addHours(4);

        return view('checkout.create', compact('forklift', 'start', 'end'));
    }

    // ─────────────────────────────────────────────────────────────
    // POST /checkout/intent
    //
    // Called by JS before stripe.confirmCardPayment().
    // 1. Validates input
    // 2. Checks for booking overlap (with DB lock)
    // 3. Creates a PENDING booking inside a transaction
    // 4. Creates a Stripe PaymentIntent
    // 5. Returns clientSecret to the frontend
    // ─────────────────────────────────────────────────────────────
    public function createIntent(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'forklift_id'     => 'required|integer|exists:forklifts,id',
            'start_time'      => 'required|string',
            'end_time'        => 'required|string',
            'name'            => 'nullable|string|max:255',
            'service_address' => 'nullable|string|max:500',
            'postal_code'     => 'nullable|string|max:20',
            'city'            => 'nullable|string|max:100',
            'province'        => 'nullable|string|max:100',
            'country'         => 'nullable|string|max:100',
        ]);

        $forklift = Forklift::findOrFail($data['forklift_id']);

        // ── Parse & normalise times to UTC ──────────────────────
        try {
            $start = Carbon::parse($data['start_time'])
                ->setTimezone('America/Regina')
                ->utc();
            $end   = Carbon::parse($data['end_time'])
                ->setTimezone('America/Regina')
                ->utc();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid date/time format.'], 422);
        }

        if ($start->isPast()) {
    return response()->json(['message' => 'Start time cannot be in the past.'], 422);
}
if ($end->lte($start)) {
    return response()->json(['message' => 'End time must be after start time.'], 422);
}

        // ── Calculate amounts ───────────────────────────────────
        $amounts = $this->calculateAmountsFromTimes($forklift, $start, $end);

        // ── Create booking inside a transaction with overlap lock ─
        $booking = null;

        try {
            DB::transaction(function () use ($forklift, $data, $start, $end, $amounts, &$booking) {
                $overlap = Booking::where('forklift_id', $forklift->id)
                    ->whereIn('status', [
                        Booking::STATUS_PENDING,
                        Booking::STATUS_AWAITING,
                        Booking::STATUS_CONFIRMED,
                    ])
                    ->where('start_time', '<', $end)
                    ->where('end_time',   '>', $start)
                    ->lockForUpdate()
                    ->exists();

                if ($overlap) {
                    throw new \RuntimeException('Selected time slot is already booked.');
                }

                $booking = Booking::create([
                    'user_id'         => Auth::id(),
                    'forklift_id'     => $forklift->id,
                    'start_time'      => $start,
                    'end_time'        => $end,
                    'service_address' => $data['service_address'] ?? null,
                    'postal_code'     => $data['postal_code']     ?? null,
                    'city'            => $data['city']            ?? null,
                    'province'        => $data['province']        ?? null,
                    'country'         => $data['country']         ?? null,
                    'payment_method'  => 'card',
                    'payment_status'  => 'pending',
                    'status'          => Booking::STATUS_AWAITING,
                    'amount_subtotal' => $amounts['subtotal_cents'],
                    'amount_gst'      => $amounts['gst_cents'],
                    'amount_pst'      => $amounts['pst_cents'],
                    'amount_total'    => $amounts['total_cents'],
                    'currency'        => 'CAD',
                    'invoice_number'  => 'INV-' . now('UTC')->format('Ymd') . '-' . Str::upper(Str::random(6)),
                ]);
            });
        } catch (\RuntimeException $e) {
            // Business-logic errors (overlap etc.) → 422
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Booking creation failed in createIntent', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Booking could not be created.'], 500);
        }

        // ── Create the Stripe PaymentIntent ────────────────────
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::create([
                'amount'      => $amounts['total_cents'],
                'currency'    => 'cad',
                'description' => 'Forklift Booking #' . $booking->id,
                'metadata'    => [
                    'booking_id'     => $booking->id,
                    'invoice_number' => $booking->invoice_number,
                    'forklift'       => $forklift->name,
                    'customer_name'  => Auth::user()->name,
                    'customer_email' => Auth::user()->email,
                ],
                'receipt_email'             => Auth::user()->email,
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            $booking->update(['payment_intent_id' => $paymentIntent->id]);

            Log::info("PaymentIntent created for Booking #{$booking->id}", [
                'pi'     => $paymentIntent->id,
                'amount' => $amounts['total_cents'],
            ]);

            return response()->json([
                'clientSecret'   => $paymentIntent->client_secret,  // camelCase for JS
                'client_secret'  => $paymentIntent->client_secret,  // snake_case alias
                'payment_intent' => $paymentIntent->id,
                'booking_id'     => $booking->id,
                'amounts'        => $amounts,
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Roll back the pending booking so it doesn't pollute the DB
            $booking->delete();

            Log::error('Stripe PaymentIntent creation failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Payment setup failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Calculate subtotal / GST / PST / total from a time range.
     * All dollar amounts are returned both as floats and as cents (int).
     */
    private function calculateAmountsFromTimes(Forklift $forklift, Carbon $start, Carbon $end): array
    {
        $hours = max(1, (int) ceil($start->diffInMinutes($end) / 60));

        $hourlyRate = (float) ($forklift->hourly_rate
            ?? (($forklift->daily_rate ?? 0) / 8));

        $subtotal = round($hourlyRate * $hours, 2);
        $gst      = round($subtotal * self::GST_RATE, 2);
        $pst      = round($subtotal * self::PST_RATE, 2);
        $total    = round($subtotal + $gst + $pst, 2);

        return [
            'hours'          => $hours,
            'hourly_rate'    => $hourlyRate,
            'subtotal'       => $subtotal,
            'gst'            => $gst,
            'pst'            => $pst,
            'total'          => $total,
            'subtotal_cents' => (int) round($subtotal * 100),
            'gst_cents'      => (int) round($gst      * 100),
            'pst_cents'      => (int) round($pst      * 100),
            'total_cents'    => (int) round($total     * 100),
        ];
    }

    /**
     * Convenience wrapper when you already have a Booking model.
     * Used by any other code that passes a Booking instead of raw times.
     */
    private function calculateAmounts(Booking $booking): array
    {
        $forklift = $booking->forklift;
        $start    = $booking->start_time;
        $end      = $booking->end_time;

        if ($start && $end) {
            return $this->calculateAmountsFromTimes($forklift, $start, $end);
        }

        // Fallback: treat as 1 hour
        $hourlyRate = (float) ($forklift->hourly_rate ?? 0);
        $subtotal   = round($hourlyRate, 2);
        $gst        = round($subtotal * self::GST_RATE, 2);
        $pst        = round($subtotal * self::PST_RATE, 2);
        $total      = round($subtotal + $gst + $pst, 2);

        return [
            'hours'          => 1,
            'hourly_rate'    => $hourlyRate,
            'subtotal'       => $subtotal,
            'gst'            => $gst,
            'pst'            => $pst,
            'total'          => $total,
            'subtotal_cents' => (int) round($subtotal * 100),
            'gst_cents'      => (int) round($gst      * 100),
            'pst_cents'      => (int) round($pst      * 100),
            'total_cents'    => (int) round($total     * 100),
        ];
    }
    // ─────────────────────────────────────────────────────────────
// POST /checkout/confirm
// Called after Stripe payment succeeds in browser.
// UPDATEs existing booking — never INSERTs a duplicate.
// ─────────────────────────────────────────────────────────────
public function confirm(Request $request): \Illuminate\Http\JsonResponse
{
    $data = $request->validate([
        'booking_id'        => 'required|integer|exists:bookings,id',
        'payment_intent_id' => 'nullable|string|max:255',
    ]);

    $booking = Booking::findOrFail($data['booking_id']);

    // Security: only the booking owner can confirm it
    if ($booking->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    // Idempotent: already confirmed = just redirect, no error
    if ($booking->status === Booking::STATUS_CONFIRMED) {
        return response()->json([
            'id'       => $booking->id,
            'redirect' => route('bookings.thankyou', $booking->id),
        ]);
    }

    // Only allow confirming awaiting_admin or pending statuses
    if (!in_array($booking->status, [Booking::STATUS_AWAITING, Booking::STATUS_PENDING])) {
        return response()->json([
            'message' => 'Booking cannot be confirmed in its current state.',
        ], 422);
    }

    $booking->update([
        'status'            => Booking::STATUS_AWAITING, // awaiting_admin = paid, needs admin approval
        'payment_status'    => 'paid',
        'payment_intent_id' => $data['payment_intent_id'] ?? $booking->payment_intent_id,
    ]);

    Log::info("Booking #{$booking->id} payment confirmed, awaiting admin approval", [
        'pi' => $data['payment_intent_id'],
    ]);

    return response()->json([
        'id'       => $booking->id,
        'redirect' => route('bookings.thankyou', $booking->id),
    ]);
}
}
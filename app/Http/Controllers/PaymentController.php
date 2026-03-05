<?php

namespace App\Http\Controllers;

use App\Mail\BookingReceiptMail;
use App\Models\Booking;
use App\Models\Forklift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

class PaymentController extends Controller
{
    /** Adjust to your tax rules (or set to 0) */
    private const GST = 0.05; // 5%
    private const PST = 0.06; // 6%

    /**
     * Create a Stripe PaymentIntent for a forklift booking.
     * Recalculates amount server-side, verifies availability, creates a pending booking.
     */
    public function intent(Request $r): JsonResponse
{
    abort_if(!Auth::check(), 401);
    dd($r->all());
    $user = Auth::user();


    $data = $r->validate([
        'forklift_id' => ['required', 'integer', 'exists:forklifts,id'],
        'start_time'  => ['required', 'date'],
        'end_time'    => ['required', 'date', 'after:start_time'],
        'address'     => ['nullable', 'string', 'max:255'],
        'postal_code' => ['nullable', 'string', 'max:32'],
        'notes'       => ['nullable', 'string', 'max:2000'],
    ]);

    $forklift = Forklift::findOrFail($data['forklift_id']);
    $start    = Carbon::parse($data['start_time']);
    $end      = Carbon::parse($data['end_time']);

    $hours = max(1, (int) ceil($start->diffInMinutes($end) / 60));

    // Pricing in cents (recommended)
    $rateCents = (int) round(((float)$forklift->hourly_rate) * 100);
    $subtotalCents = $rateCents * $hours;

    $gstCents = (int) round($subtotalCents * self::GST);
    $pstCents = (int) round($subtotalCents * self::PST);
    $totalCents = $subtotalCents + $gstCents + $pstCents;

    // Overlap check
    $overlap = Booking::where('forklift_id', $forklift->id)
        ->whereIn('status', ['pending', 'confirmed'])
        ->where(function ($q) use ($start, $end) {
            $q->where('start_time', '<', $end)
              ->where('end_time',   '>', $start);
        })
        ->exists();

    if ($overlap) {
        return response()->json([
            'error'   => 'slot_unavailable',
            'message' => 'The selected time overlaps with an existing booking.',
        ], 422);
    }

    return DB::transaction(function () use ($user, $forklift, $start, $end, $hours, $data, $subtotalCents, $gstCents, $pstCents, $totalCents): JsonResponse {

        $booking = Booking::create([
            'user_id'         => $user->id,
            'forklift_id'     => $forklift->id,
            'start_time'      => $start,
            'end_time'        => $end,
            'notes'           => $data['notes'] ?? null,
            'address'         => $data['address'] ?? null,
            'postal_code'     => $data['postal_code'] ?? null,
            'status'          => 'pending',   // pending payment
            'currency'        => 'CAD',

            // cents columns
            'subtotal_cents'  => $subtotalCents,
            'gst_cents'       => $gstCents,
            'pst_cents'       => $pstCents,
            'total_cents'     => $totalCents,
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $pi = PaymentIntent::create([
                'amount'   => $totalCents,
                'currency' => 'cad',
                'metadata' => [
                    'booking_id' => (string) $booking->id, // ok to keep as reference
                    'user_id'    => (string) $user->id,
                ],
                'automatic_payment_methods' => ['enabled' => true],
            ], [
                'idempotency_key' => 'pi_booking_'.$booking->id,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe PI create failed', ['e' => $e->getMessage()]);
            $booking->delete();

            return response()->json([
                'error'   => 'payment_intent_failed',
                'message' => 'Unable to create payment intent.',
            ], 502);
        }

        // ✅ Option A: store PI id in payment_intent_id
        $booking->update([
            'payment_intent_id' => $pi->id,
        ]);

        return response()->json([
            'clientSecret' => $pi->client_secret,
            'booking_id'   => $booking->id,
            'total_cents'  => $totalCents,
            'hours'        => $hours,
            'currency'     => 'CAD',
        ]);
    });
}


    
}

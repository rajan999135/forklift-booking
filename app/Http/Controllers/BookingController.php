<?php

namespace App\Http\Controllers;

use App\Models\{Booking, Forklift, Location};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\BookingConfirmed;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;

class BookingController extends Controller
{
    /** Show booking creation page */
    public function create(Request $request)
    {
        $forklifts = Forklift::with('location')->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $defaultForklift = (int) $request->input('forklift_id', optional($forklifts->first())->id);

        return view('bookings.create', compact('forklifts', 'locations', 'defaultForklift'));
    }

    /** Redirect /bookings → create */
    public function index()
    {
        return redirect()->route('bookings.create');
    }

    /** Calendar events for shading */
    public function calendar(Request $request)
    {
        $q = Booking::query()
            ->when($request->filled('forklift_id'), fn($qq) => $qq->where('forklift_id', (int) $request->forklift_id))
            ->when($request->filled('location_id'), fn($qq) => $qq->whereHas('forklift', fn($f) => $f->where('location_id', (int) $request->location_id)))
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_AWAITING, Booking::STATUS_CONFIRMED]);

        if ($request->filled('start') && $request->filled('end')) {
            $q->where('start_time', '<', Carbon::parse($request->input('end'))->endOfDay()->utc())
              ->where('end_time', '>', Carbon::parse($request->input('start'))->startOfDay()->utc());
        }

        $bookings  = $q->get(['status', 'start_time', 'end_time']);
        $rank      = [Booking::STATUS_PENDING => 1, Booking::STATUS_AWAITING => 2, Booking::STATUS_CONFIRMED => 3];
        $dayStatus = [];

        foreach ($bookings as $b) {
            $d    = $b->start_time->clone()->startOfDay()->utc();
            $last = $b->end_time->clone()->startOfDay()->utc();

            while ($d->lte($last)) {
                $key = $d->toDateString();
                $cur = $dayStatus[$key] ?? null;
                if (!$cur || $rank[$b->status] < $rank[$cur]) {
                    $dayStatus[$key] = $b->status;
                }
                $d->addDay();
            }
        }

        $events = [];
        foreach ($dayStatus as $day => $status) {
            $events[] = [
                'start'   => $day,
                'end'     => Carbon::parse($day)->addDay()->toDateString(),
                'allDay'  => true,
                'display' => 'background',
                'color'   => $status === Booking::STATUS_CONFIRMED ? '#bbf7d0' : '#fecaca',
            ];
        }

        return response()->json($events);
    }

    /** Availability for hourly slots */
    public function availability(Request $request)
    {
        $data = $request->validate([
            'date'        => ['required', 'date'],
            'forklift_id' => ['nullable', 'integer', 'exists:forklifts,id'],
            'tz'          => ['nullable', 'string', 'timezone'],
        ]);

        $tz = $data['tz'] ?? config('booking.display_tz', 'America/Regina');
        $dateLocal  = Carbon::parse($data['date'], $tz)->startOfDay();
        $startLocal = $dateLocal->copy()->setTime(5, 0);
        $endLocal   = $dateLocal->copy()->setTime(23, 0);

        $startUTC = $startLocal->clone()->utc();
        $endUTC   = $endLocal->clone()->utc();

        $bookings = Booking::query()
            ->when(!empty($data['forklift_id']), fn($q) => $q->where('forklift_id', (int) $data['forklift_id']))
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_AWAITING, Booking::STATUS_CONFIRMED])
            ->where('start_time', '<', $endUTC)
            ->where('end_time', '>', $startUTC)
            ->get(['start_time', 'end_time', 'status']);

        $slots = [];
        for ($h = 5; $h < 23; $h++) {
            $slotStartLocal = $dateLocal->copy()->setTime($h, 0);
            $slotEndLocal   = $dateLocal->copy()->setTime($h + 1, 0);
            $slotStartUTC = $slotStartLocal->clone()->utc();
            $slotEndUTC   = $slotEndLocal->clone()->utc();

            $overlapping = $bookings->filter(fn($b) => $b->start_time < $slotEndUTC && $b->end_time > $slotStartUTC);

            $slotStatus = 'free';
            if ($overlapping->isNotEmpty()) {
                $hasPending = $overlapping->contains(fn($b) => in_array($b->status, [
                    Booking::STATUS_PENDING,
                    Booking::STATUS_AWAITING,
                ]));
                $slotStatus = $hasPending ? 'awaiting' : 'booked';
            }

            $slots[] = [
                'label'  => $slotStartLocal->isoFormat('h A') . ' - ' . $slotEndLocal->isoFormat('h A'),
                'start'  => $slotStartUTC->toIso8601String(),
                'end'    => $slotEndUTC->toIso8601String(),
                'status' => $slotStatus,
            ];
        }

        return response()->json([
            'date'  => $dateLocal->toDateString(),
            'slots' => $slots,
        ]);
    }

    /** Create booking + Stripe PaymentIntent */
    public function store(Request $request)
    {
        $isAjax = $request->ajax() || $request->wantsJson() ||
                  $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                  str_contains(strtolower($request->header('Accept', '')), 'application/json');

        $tz = $request->input('tz', config('booking.display_tz', 'America/Regina'));

        $v = $request->validate([
            'forklift_id'     => ['required', 'exists:forklifts,id'],
            'start_time'      => ['required', 'date', 'after:now'],
            'end_time'        => ['required', 'date', 'after:start_time'],
            'payment_method'  => ['required', 'in:cash,card'],
            'service_address' => ['required', 'string', 'max:255'],
            'postal_code'     => ['nullable', 'string', 'max:20'],
            'city'            => ['nullable', 'string', 'max:80'],
            'province'        => ['nullable', 'string', 'max:80'],
            'country'         => ['nullable', 'string', 'max:80'],
            'lat'             => ['nullable', 'numeric'],
            'lng'             => ['nullable', 'numeric'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ]);

        if (!empty($v['notes'])) {
            $v['notes'] = strip_tags($v['notes']);
        }

        $forklift = Forklift::findOrFail($v['forklift_id']);
        $start = Carbon::parse($v['start_time'])->utc();
        $end   = Carbon::parse($v['end_time'])->utc();

        $minutes   = max(0, $start->diffInMinutes($end));
        $hours     = max(1, (int) ceil($minutes / 60));
        $rateCents = (int) round(((float) $forklift->hourly_rate) * 100);
        $subtotal  = $hours * $rateCents;
        $gst       = (int) round($subtotal * 0.05);
        $pst       = (int) round($subtotal * 0.06);
        $total     = $subtotal + $gst + $pst;

        $booking = null;
        $paymentIntent = null;

        try {
            DB::transaction(function () use ($forklift, $v, $start, $end, $subtotal, $gst, $pst, $total, &$booking) {
                // FIX: Include STATUS_COMPLETED in overlap check so completed bookings
                // still block the slot and prevent the unique constraint violation.
                $overlap = Booking::where('forklift_id', $forklift->id)
                    ->whereIn('status', [
                        Booking::STATUS_PENDING,
                        Booking::STATUS_AWAITING,
                        Booking::STATUS_CONFIRMED,
                        Booking::STATUS_COMPLETED, // ← ADDED
                    ])
                    ->where('start_time', '<', $end)
                    ->where('end_time', '>', $start)
                    ->lockForUpdate()
                    ->exists();

                if ($overlap) {
                    throw new \RuntimeException('Selected time slot is already booked.');
                }

                $booking = Booking::create([
                    'user_id'         => auth()->id(),
                    'forklift_id'     => $forklift->id,
                    'start_time'      => $start,
                    'end_time'        => $end,
                    'status'          => $v['payment_method'] === 'card' ? Booking::STATUS_AWAITING : Booking::STATUS_PENDING,
                    'notes'           => $v['notes'] ?? null,
                    'service_address' => $v['service_address'],
                    'postal_code'     => $v['postal_code'] ?? null,
                    'city'            => $v['city'] ?? null,
                    'province'        => $v['province'] ?? null,
                    'country'         => $v['country'] ?? null,
                    'payment_method'  => $v['payment_method'],
                    'amount_subtotal' => $subtotal,
                    'amount_gst'      => $gst,
                    'amount_pst'      => $pst,
                    'amount_total'    => $total,
                    'currency'        => 'CAD',
                    'invoice_number'  => 'INV-' . now('UTC')->format('Ymd') . '-' . Str::upper(Str::random(6)),
                ]);
            });

            if ($v['payment_method'] === 'card') {
                Stripe::setApiKey(config('services.stripe.secret'));
                $paymentIntent = PaymentIntent::create([
                    'amount'   => $total,
                    'currency' => 'cad',
                    'metadata' => [
                        'booking_id'  => $booking->id,
                        'forklift_id' => $forklift->id,
                        'user_id'     => auth()->id(),
                    ],
                ]);
                $booking->update([
                    'payment_intent_id' => $paymentIntent->id,
                    'payment_status'    => 'pending',
                ]);
            }

        } catch (\Throwable $e) {
            // FIX: Catch DB unique constraint violation (1062) and show a friendly message
            if (
                str_contains($e->getMessage(), '1062') ||
                str_contains($e->getMessage(), 'Duplicate entry') ||
                str_contains($e->getMessage(), 'bookings_unique_slot')
            ) {
                $message = 'Selected time slot is already booked. Please choose a different time.';
            } else {
                $message = $e->getMessage();
            }

            return $isAjax
                ? response()->json(['ok' => false, 'message' => $message], 422)
                : back()->withErrors($message);
        }

        $response = [
            'ok'         => true,
            'booking_id' => $booking->id,
            'redirect'   => route('bookings.thankyou', $booking->id),
        ];

        if ($booking->payment_method === 'card' && $paymentIntent) {
            $response['client_secret'] = $paymentIntent->client_secret;
        }

        return $isAjax
            ? response()->json($response)
            : redirect($response['redirect']);
    }

    /** Thank-you page */
    public function thankyou(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id() && (Auth::user()->role ?? 'customer') !== 'admin', 403);
        return view('bookings.thankyou', compact('booking'));
    }

    /** My bookings page */
    public function mine(Request $request)
    {
        $userId = Auth::id();
        $tz     = config('booking.display_tz', 'America/Regina');

        $accepted = Booking::with(['forklift', 'forklift.location'])
            ->where('user_id', $userId)
            ->where('status', Booking::STATUS_CONFIRMED)
            ->orderByDesc('start_time')
            ->get();

        $pending = Booking::with(['forklift', 'forklift.location'])
            ->where('user_id', $userId)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_AWAITING])
            ->orderByDesc('start_time')
            ->get();

        $denied = Booking::with(['forklift', 'forklift.location'])
            ->where('user_id', $userId)
            ->where('status', Booking::STATUS_CANCELLED)
            ->orderByDesc('start_time')
            ->get();

        return view('bookings.mine', compact('accepted', 'pending', 'denied', 'tz'));
    }

    /** Admin: update status */
    public function updateStatus(Request $request, Booking $booking)
    {
        abort_if((Auth::user()->role ?? 'customer') !== 'admin', 403, 'Unauthorized');

        $data = $request->validate([
            'status' => 'required|in:pending,awaiting_admin,confirmed,cancelled,rejected',
        ]);

        $old             = $booking->status;
        $booking->status = $data['status'];

        /*
        |--------------------------------------------------------------------------
        | NOTE: Cash "paid" logic on completion is intentionally removed here.
        | Use the dedicated complete() method to mark bookings as completed,
        | which handles payment status correctly for all payment methods.
        |--------------------------------------------------------------------------
        */

        $booking->save();

        if ($old !== Booking::STATUS_CONFIRMED && $booking->status === Booking::STATUS_CONFIRMED) {
            $booking->loadMissing(['user', 'forklift.location']);
            $pdfBinary = '';
            try {
                $pdfBinary = Pdf::loadView('pdf.invoice', ['booking' => $booking])->output();
            } catch (\Throwable $e) {
                Log::warning('Invoice PDF generation failed: ' . $e->getMessage());
            }

            try {
                Mail::to($booking->user->email)->queue(new BookingConfirmed($booking, $pdfBinary));
            } catch (\Throwable $e) {
                Log::warning('BookingConfirmed mail failed: ' . $e->getMessage());
            }
        }

        return $request->ajax()
            ? response()->json(['ok' => true, 'status' => $booking->status])
            : back()->with('success', 'Status updated.');
    }

    /** Forklift catalog */
    public function forklifts(Request $request)
    {
        $q = (string) $request->query('q', '');
        $forklifts = Forklift::with('location')
            ->when($q !== '', fn($qr) => $qr->where('name', 'like', "%{$q}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('bookings.forklifts', compact('forklifts', 'q'));
    }

    /** Admin: refund a booking */
    public function refund(Booking $booking)
    {
        abort_if((Auth::user()->role ?? 'customer') !== 'admin', 403, 'Unauthorized');

        if ($booking->payment_status !== 'paid' || $booking->refund_status === 'refunded') {
            return back()->with('error', 'Refund not eligible.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // amount_total is already stored in cents — do NOT multiply by 100 again
            $refund = Refund::create([
                'payment_intent' => $booking->payment_intent_id,
                'amount'         => $booking->amount_total,
            ]);

            if ($refund->status === 'succeeded') {
                $booking->refund_status  = 'refunded';
                $booking->refund_amount  = $booking->amount_total;
                $booking->refunded_at    = now();
                $booking->payment_status = 'refunded';
                $booking->save();
            }

        } catch (\Throwable $e) {
            Log::error('Refund failed for booking ' . $booking->id . ': ' . $e->getMessage());
            return back()->with('error', 'Refund failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Refund processed successfully.');
    }

    /** Admin: manually mark a cash booking as paid */
    public function markPaid(Booking $booking)
    {
        abort_if((Auth::user()->role ?? 'customer') !== 'admin', 403, 'Unauthorized');

        if ($booking->payment_method === 'cash') {
            $booking->payment_status = 'paid';
            $booking->paid_at        = now();
            $booking->save();
        }

        return back()->with('success', 'Payment marked as paid successfully.');
    }

    /** Admin: complete a confirmed booking */
    public function complete(Booking $booking)
    {
        abort_if((Auth::user()->role ?? 'customer') !== 'admin', 403);

        // reload fresh to ensure all fields are present
        $booking = Booking::findOrFail($booking->id);

        if ($booking->status !== Booking::STATUS_CONFIRMED) {
            return back()->with('error', 'Only confirmed bookings can be completed.');
        }

        if ($booking->payment_method === 'card' && $booking->payment_status !== 'paid') {
            return back()->with('error', 'Cannot complete a booking with an outstanding card payment.');
        }

        $booking->status       = Booking::STATUS_COMPLETED;
        $booking->completed_at = now();

        // auto-mark cash as paid
        if ($booking->payment_method === 'cash') {
            $booking->payment_status = 'paid';
            $booking->paid_at        = now();
        }

        $booking->save();

        return back()->with('success', 'Booking marked as completed.');
    }
}
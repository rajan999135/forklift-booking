<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    /**
     * Handle incoming Stripe webhook events.
     * Route: POST /stripe/webhook  (exclude from CSRF in VerifyCsrfToken)
     */
    public function handle(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret'); // STRIPE_WEBHOOK_SECRET in .env

        // Verify the webhook signature
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature mismatch', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Webhook error'], 400);
        }

        Log::info('Stripe webhook received', ['type' => $event->type]);

        match ($event->type) {
            'payment_intent.succeeded'       => $this->handlePaymentSucceeded($event->data->object),
            'payment_intent.payment_failed'  => $this->handlePaymentFailed($event->data->object),
            'charge.refunded'                => $this->handleChargeRefunded($event->data->object),
            default                          => null,
        };

        return response()->json(['status' => 'ok']);
    }

    /**
     * Payment succeeded → update booking as paid + confirmed
     */
    private function handlePaymentSucceeded(object $paymentIntent): void
{
    // Find by payment_intent_id first
    $booking = Booking::where('payment_intent_id', $paymentIntent->id)->first();

    // Fallback: find by booking_id in metadata
    if (!$booking && isset($paymentIntent->metadata->booking_id)) {
        $booking = Booking::find($paymentIntent->metadata->booking_id);
        if ($booking) {
            $booking->payment_intent_id = $paymentIntent->id;
        }
    }

    if (!$booking) {
        Log::warning('No booking found for PI: ' . $paymentIntent->id);
        return;
    }

    $booking->payment_status = 'paid';
    $booking->payment_method = 'card';
    $booking->amount_total   = $paymentIntent->amount;
    $booking->currency       = strtoupper($paymentIntent->currency);

    if (isset($paymentIntent->metadata->amount_subtotal)) {
        $booking->amount_subtotal = (int) $paymentIntent->metadata->amount_subtotal;
    }
    if (isset($paymentIntent->metadata->amount_gst)) {
        $booking->amount_gst = (int) $paymentIntent->metadata->amount_gst;
    }
    if (isset($paymentIntent->metadata->amount_pst)) {
        $booking->amount_pst = (int) $paymentIntent->metadata->amount_pst;
    }
    if (isset($paymentIntent->metadata->invoice_number)) {
        $booking->invoice_number = $paymentIntent->metadata->invoice_number;
    }

    if (in_array($booking->status, ['pending', 'awaiting_payment'])) {
        $booking->status = 'confirmed';
    }

    $booking->save();
    Log::info("Booking #{$booking->id} marked paid via PI {$paymentIntent->id}");
}
    /**
     * Payment failed → keep booking pending, log it
     */
    private function handlePaymentFailed(object $paymentIntent): void
    {
        $booking = Booking::where('payment_intent_id', $paymentIntent->id)->first();

        if (!$booking) return;

        $booking->payment_status = 'failed';
        $booking->save();

        Log::warning("Booking #{$booking->id} payment failed for PI {$paymentIntent->id}");
    }

    /**
     * Charge refunded → update refund columns automatically
     */
    private function handleChargeRefunded(object $charge): void
    {
        // Find booking by payment_intent_id (charge has payment_intent field)
        $booking = Booking::where('payment_intent_id', $charge->payment_intent)->first();

        if (!$booking) {
            Log::warning('Stripe webhook: no booking found for charge refund PI ' . ($charge->payment_intent ?? 'null'));
            return;
        }

        // Get the most recent refund from the charge
        $latestRefund = $charge->refunds->data[0] ?? null;

        $booking->refund_status   = 'refunded';
        $booking->refund_amount   = $charge->amount_refunded; // cents
        $booking->refunded_at     = now();
        $booking->payment_status  = 'refunded';

        if ($latestRefund) {
            $booking->stripe_refund_id = $latestRefund->id;
        }

        $booking->save();

        Log::info("Booking #{$booking->id} refund of {$charge->amount_refunded} cents recorded from Stripe.");
    }
}
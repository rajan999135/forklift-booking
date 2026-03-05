<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingConfirmed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking, public string $invoicePdf = '') {}

    public function build()
    {
        $mail = $this->markdown('emails.bookings.confirmed')
            ->subject('Your booking is confirmed');
        if ($this->invoicePdf !== '') {
            $mail->attachData($this->invoicePdf, "invoice-{$this->booking->id}.pdf");
        }
        return $mail;
    }
}

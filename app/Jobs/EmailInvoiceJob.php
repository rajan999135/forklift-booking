<?php

namespace App\Jobs;

use App\Models\Booking;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

class EmailInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // ✅ Worker controls (good for production)
    public int $tries = 3;
    public int $timeout = 120; // seconds
    public array $backoff = [10, 30, 90]; // retry delays

    public function __construct(public int $bookingId) {}

    public function handle(): void
    {
        $b = Booking::with(['forklift', 'user'])->find($this->bookingId);

        if (!$b) {
            Log::warning('EmailInvoiceJob: booking not found', ['booking_id' => $this->bookingId]);
            return;
        }

        if (!$b->user?->email) {
            Log::warning('EmailInvoiceJob: booking user email missing', ['booking_id' => $b->id]);
            return;
        }

        // Ensure invoice number exists (adjust field name if needed)
        $invoiceNo = $b->invoice_number ?? $b->invoice_no ?? null;
        if (!$invoiceNo) {
            $invoiceNo = 'INV-' . $b->id; // fallback
        }

        try {
            // Render Blade → HTML
            $html = view('pdf.invoice', ['b' => $b])->render();

            // Dompdf config
            $opts = new Options();
            $opts->set('isRemoteEnabled', true);
            $opts->set('defaultFont', 'DejaVu Sans');

            $dompdf = new Dompdf($opts);
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $pdf = $dompdf->output();

            // Save to private disk (make sure disk 'private' exists in config/filesystems.php)
            $path = "invoices/{$invoiceNo}.pdf";
            Storage::disk('private')->put($path, $pdf);

            // Attach using disk path safely
            $fullPath = Storage::disk('private')->path($path);

            Mail::raw('Your forklift booking invoice is attached.', function ($m) use ($b, $invoiceNo, $fullPath) {
                $m->to($b->user->email, $b->user->name ?? null)
                  ->subject('Your Forklift Booking Invoice')
                  ->attach($fullPath, [
                      'as'   => $invoiceNo . '.pdf',
                      'mime' => 'application/pdf',
                  ]);
            });

            Log::info('Invoice emailed successfully', [
                'booking_id' => $b->id,
                'email'      => $b->user->email,
                'path'       => $path,
            ]);

        } catch (Throwable $e) {
            Log::error('EmailInvoiceJob failed', [
                'booking_id' => $b->id,
                'e' => $e->getMessage(),
            ]);

            // rethrow so the queue marks it as failed and retries (per $tries/$backoff)
            throw $e;
        }
    }
}

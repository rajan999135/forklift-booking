<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Forklift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Show admin dashboard with statistics
     */
    public function dashboard()
    {
        if ((Auth::user()->role ?? 'customer') !== 'admin') {
            abort(403);
        }

        $stats = [
            'total_bookings'   => Booking::count(),
            'pending_bookings' => Booking::whereIn('status', [
                                      Booking::STATUS_PENDING,
                                      Booking::STATUS_AWAITING,
                                  ])->count(),
            'confirmed'        => Booking::where('status', 'confirmed')->count(),
            'total_users'      => User::count(),
            'forklifts'        => Forklift::count(),
        ];

        $bookings = Booking::with(['user', 'forklift'])
            ->latest()
            ->paginate(20);

        return view('admin.dashboard', compact('stats', 'bookings'));
    }

    /**
     * View specific booking
     */
    public function viewBooking(Booking $booking)
    {
        if ((Auth::user()->role ?? 'customer') !== 'admin') {
            abort(403);
        }

        $booking->load(['user', 'forklift']);

        $bookings = Booking::with(['user', 'forklift'])->latest()->paginate(20);

        $stats = [
            'total_bookings'   => Booking::count(),
            'pending_bookings' => Booking::whereIn('status', [
                                      Booking::STATUS_PENDING,
                                      Booking::STATUS_AWAITING,
                                  ])->count(),
            'confirmed'        => Booking::where('status', 'confirmed')->count(),
            'total_users'      => User::count(),
            'forklifts'        => Forklift::count(),
        ];

        return view('admin.booking-show', compact('booking', 'bookings', 'stats'));
    }

    /**
     * Approve a booking
     */
    public function approveBooking(Booking $booking)
    {
        if ((Auth::user()->role ?? 'customer') !== 'admin') {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'awaiting_admin'])) {
            return redirect()->back()->with('error', 'Only pending bookings can be approved.');
        }

        $booking->status = 'confirmed';
        $booking->save();

        return redirect()->back()->with('success', "Booking #{$booking->id} approved successfully!");
    }

    /**
     * Reject a booking — issues real Stripe refund if paid by card
     */
    public function rejectBooking(Request $request, Booking $booking)
    {
        if ((Auth::user()->role ?? 'customer') !== 'admin') {
            abort(403);
        }

        $booking->status = 'rejected';
        $booking->save();

        $refundMessage = null;

        // ── Real Stripe refund ──────────────────────────────────
        if ($booking->payment_method === 'card' && $booking->payment_intent_id && ($booking->amount_total ?? 0) > 0) {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                \Stripe\Refund::create([
                    'payment_intent' => $booking->payment_intent_id,
                    'amount'         => $booking->amount_total, // cents
                ]);

                $booking->refund_status = 'refunded';
                $booking->refund_amount = $booking->amount_total;
                $booking->refunded_at   = now();
                $booking->save();

                Log::info("Stripe refund issued for rejected Booking #{$booking->id}", [
                    'pi'     => $booking->payment_intent_id,
                    'amount' => $booking->amount_total,
                ]);

                $refundMessage = 'Card refund of $' . number_format($booking->amount_total / 100, 2)
                               . ' has been sent to Stripe for Booking #' . $booking->id . '.';

            } catch (\Stripe\Exception\ApiErrorException $e) {
                Log::error('Stripe refund failed on reject', [
                    'booking_id' => $booking->id,
                    'error'      => $e->getMessage(),
                ]);
                $booking->refund_status = 'failed';
                $booking->save();
                return redirect()->back()->with('error', 'Booking rejected but refund failed: ' . $e->getMessage());
            }
        }

        return redirect()->back()
            ->with('success', "Booking #{$booking->id} has been rejected.")
            ->with('refund', $refundMessage);
    }

    /**
     * Mark a booking as completed
     */
    public function completeBooking(Booking $booking)
    {
        if ((Auth::user()->role ?? 'customer') !== 'admin') {
            abort(403);
        }

        if (in_array($booking->status, ['completed', 'cancelled', 'rejected'])) {
            return redirect()->back()->with('error', 'This booking cannot be marked as completed.');
        }

        $booking->status       = 'completed';
        $booking->completed_at = now();
        $booking->save();

        return redirect()->back()->with('success', "Booking #{$booking->id} has been marked as completed.");
    }

    /**
     * Cancel a booking — issues real Stripe refund if paid by card
     */
    public function cancelBooking(Booking $booking)
    {
        if ((Auth::user()->role ?? 'customer') !== 'admin') {
            abort(403);
        }

        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'This booking cannot be cancelled.');
        }

        $booking->status = 'cancelled';
        $booking->save();

        $refundMessage = null;

        // ── Real Stripe refund ──────────────────────────────────
        if ($booking->payment_method === 'card' && $booking->payment_intent_id && ($booking->amount_total ?? 0) > 0) {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                \Stripe\Refund::create([
                    'payment_intent' => $booking->payment_intent_id,
                    'amount'         => $booking->amount_total, // cents
                ]);

                $booking->refund_status = 'refunded';
                $booking->refund_amount = $booking->amount_total;
                $booking->refunded_at   = now();
                $booking->save();

                Log::info("Stripe refund issued for cancelled Booking #{$booking->id}", [
                    'pi'     => $booking->payment_intent_id,
                    'amount' => $booking->amount_total,
                ]);

                $refundMessage = 'Card refund of $' . number_format($booking->amount_total / 100, 2)
                               . ' has been sent to Stripe for Booking #' . $booking->id . '.';

            } catch (\Stripe\Exception\ApiErrorException $e) {
                Log::error('Stripe refund failed on cancel', [
                    'booking_id' => $booking->id,
                    'error'      => $e->getMessage(),
                ]);
                $booking->refund_status = 'failed';
                $booking->save();
                return redirect()->back()->with('error', 'Booking cancelled but refund failed: ' . $e->getMessage());
            }
        }

        return redirect()->back()
            ->with('success', "Booking #{$booking->id} has been cancelled.")
            ->with('refund', $refundMessage);
    }

    /**
     * Update booking status (generic)
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        if ((Auth::user()->role ?? 'customer') !== 'admin') {
            abort(403);
        }

        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,cancelled,approved,rejected,completed'],
        ]);

        $booking->status = $data['status'];
        $booking->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status'  => $booking->status,
                'message' => 'Booking status updated.',
            ]);
        }

        return back()->with('success', 'Booking status updated.');
    }

    /**
     * Show all users
     */
    public function users()
    {
        if ((Auth::user()->role ?? 'customer') !== 'admin') {
            abort(403);
        }

        $users = User::paginate(20);
        return view('admin.users', compact('users'));
    }

    /**
     * Show specific user
     */
    public function viewUser(User $user)
    {
        if ((Auth::user()->role ?? 'customer') !== 'admin') {
            abort(403);
        }

        return view('admin.user-show', compact('user'));
    }

    /**
     * Toggle user admin role
     */
    public function toggleUserRole(User $user)
    {
        if ((Auth::user()->role ?? 'customer') !== 'admin') {
            abort(403);
        }

        $newRole = $user->role === 'admin' ? 'user' : 'admin';
        $user->role = $newRole;
        $user->save();

        return redirect()->back()->with('success', "User role changed to {$newRole}");
    }

    /**
     * Show reports / analytics page
     */
    public function reports()
    {
        if ((Auth::user()->role ?? 'customer') !== 'admin') {
            abort(403);
        }

        $stats = [
            'total_revenue'    => Booking::where('payment_method', 'card')->sum('amount_total'),
            'monthly_revenue'  => Booking::where('payment_method', 'card')
                                    ->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->sum('amount_total'),
            'total_refunds'    => Booking::whereNotNull('refund_amount')->sum('refund_amount'),
            'total_bookings'   => Booking::count(),
            'pending_bookings' => Booking::whereIn('status', [
                                      Booking::STATUS_PENDING,
                                      Booking::STATUS_AWAITING,
                                  ])->count(),
            'confirmed'        => Booking::where('status', 'confirmed')->count(),
            'completed'        => Booking::where('status', 'completed')->count(),
            'rejected'         => Booking::where('status', 'rejected')->count(),
            'cancelled'        => Booking::where('status', 'cancelled')->count(),
            'completion_rate'  => Booking::count() > 0
                                    ? round(Booking::where('status', 'completed')->count() / Booking::count() * 100)
                                    : 0,
            'monthly_bookings' => $this->getMonthlyBookings(),
        ];

        $recentBookings = Booking::with(['user', 'forklift'])->latest()->take(10)->get();
        $equipmentStats = $this->getEquipmentStats();

        return view('admin.reports.index', compact('stats', 'recentBookings', 'equipmentStats'));
    }

    /**
     * Get monthly booking counts for the last 6 months
     */
    private function getMonthlyBookings(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month  = now()->subMonths($i);
            $count  = Booking::whereMonth('created_at', $month->month)
                             ->whereYear('created_at', $month->year)
                             ->count();
            $data[] = ['label' => $month->format('M'), 'count' => $count];
        }
        return $data;
    }

    /**
     * Get per-equipment utilization stats
     */
    private function getEquipmentStats(): array
    {
        return Forklift::withCount('bookings')
            ->get()
            ->map(function ($forklift) {
                $revenue   = $forklift->bookings()->where('payment_method', 'card')->sum('amount_total');
                $completed = $forklift->bookings()->where('status', 'completed')->count();
                $total     = max($forklift->bookings_count, 1);
                return [
                    'name'           => $forklift->name,
                    'total_bookings' => $forklift->bookings_count,
                    'revenue'        => $revenue,
                    'utilization'    => round($completed / $total * 100),
                ];
            })
            ->toArray();
    }

    /**
     * Export bookings as CSV
     */
    public function exportReports()
    {
        if ((Auth::user()->role ?? 'customer') !== 'admin') {
            abort(403);
        }

        $bookings = Booking::with(['user', 'forklift'])->get();

        $csv = "ID,Customer,Email,Equipment,Start Date,End Date,Status,Payment Method,Total,Refund Status,Refund Amount\n";
        foreach ($bookings as $b) {
            $csv .= implode(',', [
                $b->id,
                '"' . ($b->user->name ?? 'N/A') . '"',
                '"' . ($b->user->email ?? 'N/A') . '"',
                '"' . ($b->forklift->name ?? 'N/A') . '"',
                $b->start_time?->format('Y-m-d') ?? 'N/A',
                $b->end_time?->format('Y-m-d') ?? 'N/A',
                $b->status ?? 'N/A',
                $b->payment_method ?? 'N/A',
                '$' . number_format(($b->amount_total ?? 0) / 100, 2),
                $b->refund_status ?? 'N/A',
                '$' . number_format(($b->refund_amount ?? 0) / 100, 2),
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings_report_' . now()->format('Y_m_d') . '.csv"',
        ]);
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        if ((Auth::user()->role ?? 'customer') !== 'admin') {
            abort(403);
        }

        $settings = $this->loadSettings();
        return view('admin.settings', compact('settings'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        if ((Auth::user()->role ?? 'customer') !== 'admin') {
            abort(403);
        }

        $data = $request->validate([
            'business_name'        => 'nullable|string|max:255',
            'business_email'       => 'nullable|email|max:255',
            'business_phone'       => 'nullable|string|max:50',
            'business_address'     => 'nullable|string',
            'currency'             => 'nullable|string|max:10',
            'min_booking_days'     => 'nullable|integer|min:1',
            'max_booking_days'     => 'nullable|integer|min:1',
            'advance_booking_days' => 'nullable|integer|min:0',
            'tax_rate'             => 'nullable|numeric|min:0|max:100',
            'refund_window_days'   => 'nullable|integer|min:0',
            'partial_refund_pct'   => 'nullable|integer|min:0|max:100',
            'refund_policy_text'   => 'nullable|string',
            'payment_provider'     => 'nullable|string',
            'payment_mode'         => 'nullable|string',
            'payment_key_public'   => 'nullable|string',
            'payment_key_secret'   => 'nullable|string',
        ]);

        $booleans = [
            'auto_approve', 'auto_refund',
            'notify_new_booking', 'notify_booking_confirmed',
            'notify_booking_rejected', 'notify_booking_cancelled',
            'notify_booking_completed', 'notify_refund_processed',
        ];
        foreach ($booleans as $key) {
            $data[$key] = $request->boolean($key);
        }

        $path     = storage_path('app/admin_settings.json');
        $existing = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
        $merged   = array_merge($existing, $data);
        file_put_contents($path, json_encode($merged, JSON_PRETTY_PRINT));

        return redirect()->back()->with('success', 'Settings saved successfully!');
    }

    /**
     * Load settings from JSON file with defaults
     */
    private function loadSettings(): array
    {
        $path = storage_path('app/admin_settings.json');
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true) ?? [];
        }

        return [
            'business_name'            => '',
            'business_email'           => '',
            'business_phone'           => '',
            'currency'                 => 'CAD',
            'min_booking_days'         => 1,
            'max_booking_days'         => 30,
            'advance_booking_days'     => 1,
            'tax_rate'                 => 0,
            'auto_approve'             => false,
            'refund_window_days'       => 7,
            'partial_refund_pct'       => 50,
            'auto_refund'              => true,
            'payment_provider'         => 'stripe',
            'payment_mode'             => 'sandbox',
            'notify_new_booking'       => true,
            'notify_booking_confirmed' => true,
            'notify_booking_rejected'  => true,
            'notify_booking_cancelled' => true,
            'notify_booking_completed' => true,
            'notify_refund_processed'  => true,
        ];
    }

    public function markPaid(Booking $booking)
{
    $booking->update([
        'payment_status' => 'paid',
        'status'         => 'confirmed',
    ]);

    return redirect()->route('admin.booking-show', $booking)
        ->with('success', "Booking #{$booking->id} marked as paid.");
}
}
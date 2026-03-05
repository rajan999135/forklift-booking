<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Forklift;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        // ── KPI Counts ────────────────────────────────────────────────
        $totalBookings   = Booking::count();
        $pendingBookings = Booking::whereIn('status', ['pending', 'awaiting_admin'])->count();
        $confirmed       = Booking::where('status', 'confirmed')->count();
        $completed       = Booking::where('status', 'completed')->count();
        $cancelled       = Booking::where('status', 'cancelled')->count();
        $rejected        = Booking::where('status', 'rejected')->count();

        $completionRate = $totalBookings > 0
            ? round(($completed / $totalBookings) * 100)
            : 0;

        // ── Revenue: CARD only (cents) ─────────────────────────────────
        $cardRevenue = Booking::where('payment_method', 'card')
            ->whereIn('status', ['confirmed', 'completed', 'awaiting_admin'])
            ->sum('amount_total');

        // ── Revenue: CASH only (cents) ─────────────────────────────────
        // Cash bookings that are confirmed, completed, or paid
        $cashRevenue = Booking::where('payment_method', 'cash')
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('amount_total');

        // ── Total Revenue (card + cash) ────────────────────────────────
        $totalRevenue = $cardRevenue + $cashRevenue;

        // ── Monthly Revenue (this month, both methods) ─────────────────
        $monthlyRevenue = Booking::whereIn('payment_method', ['card', 'cash'])
            ->whereIn('status', ['confirmed', 'completed', 'awaiting_admin'])
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount_total');

        // ── Refunds ────────────────────────────────────────────────────
        $totalRefunds = Booking::where('payment_method', 'card')
            ->whereIn('status', ['cancelled', 'rejected'])
            ->sum('amount_total');

        // ── Booking counts by payment method ───────────────────────────
        $cardBookingsCount = Booking::where('payment_method', 'card')->count();
        $cashBookingsCount = Booking::where('payment_method', 'cash')->count();

        // ── Monthly Bookings (last 6 months) ───────────────────────────
        $monthlyBookings = [];
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $count = Booking::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyBookings[] = [
                'label' => $date->format('M'),
                'count' => $count,
            ];
        }

        // ── Build $stats array ─────────────────────────────────────────
        $stats = [
            // Counts
            'total_bookings'       => $totalBookings,
            'pending_bookings'     => $pendingBookings,
            'confirmed'            => $confirmed,
            'completed'            => $completed,
            'cancelled'            => $cancelled,
            'rejected'             => $rejected,
            'completion_rate'      => $completionRate,

            // Revenue
            'total_revenue'        => $totalRevenue,
            'card_revenue'         => $cardRevenue,
            'cash_revenue'         => $cashRevenue,
            'monthly_revenue'      => $monthlyRevenue,
            'total_refunds'        => $totalRefunds,

            // Payment method booking counts
            'card_bookings_count'  => $cardBookingsCount,
            'cash_bookings_count'  => $cashBookingsCount,

            // Chart data
            'monthly_bookings'     => $monthlyBookings,
        ];

        // ── Equipment Stats ────────────────────────────────────────────
        $forklifts   = Forklift::withCount('bookings')->get();
        $maxBookings = $forklifts->max('bookings_count') ?: 1;

        $equipmentStats = $forklifts->map(function ($f) use ($maxBookings) {

            // Card revenue per forklift
            $cardRev = Booking::where('forklift_id', $f->id)
                ->where('payment_method', 'card')
                ->whereIn('status', ['confirmed', 'completed', 'awaiting_admin'])
                ->sum('amount_total');

            // Cash revenue per forklift
            $cashRev = Booking::where('forklift_id', $f->id)
                ->where('payment_method', 'cash')
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('amount_total');

            return [
                'id'             => $f->id,
                'name'           => $f->name,
                'total_bookings' => $f->bookings_count,
                'card_revenue'   => $cardRev,
                'cash_revenue'   => $cashRev,
                'revenue'        => $cardRev + $cashRev,   // total for display
                'utilization'    => $maxBookings > 0
                    ? round(($f->bookings_count / $maxBookings) * 100)
                    : 0,
            ];
        })->sortByDesc('total_bookings')->values()->toArray();

        // ── Recent Bookings ────────────────────────────────────────────
        $recentBookings = Booking::with(['user', 'forklift'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.reports', compact('stats', 'equipmentStats', 'recentBookings'));
    }

    // ── CSV Export ─────────────────────────────────────────────────────
    public function export(): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID', 'Customer', 'Email', 'Forklift',
                'Status', 'Payment Method', 'Amount (C$)',
                'Created At',
            ]);

            Booking::with(['user', 'forklift'])
                ->orderByDesc('id')
                ->chunk(200, function ($bookings) use ($handle) {
                    foreach ($bookings as $b) {
                        fputcsv($handle, [
                            $b->id,
                            $b->user->name  ?? 'N/A',
                            $b->user->email ?? 'N/A',
                            $b->forklift->name ?? 'N/A',
                            $b->status,
                            $b->payment_method,
                            $b->payment_method === 'card'
                                ? number_format(($b->amount_total ?? 0) / 100, 2)
                                : number_format(($b->amount_total ?? 0) / 100, 2), // show cash amount too
                            $b->created_at?->setTimezone('America/Regina')->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
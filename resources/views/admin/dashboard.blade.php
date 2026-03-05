@extends('layouts.admin')

@section('admin-content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">

    {{-- Sticky Header --}}
    <div class="sticky top-0 z-20 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Admin Dashboard</h1>
                        <p class="text-sm text-slate-500">Welcome back, {{ auth()->user()->name }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 px-4 py-2 rounded-full bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-green-700">System Active</span>
                    </div>
                    <a href="{{ route('admin.forklifts.index') }}"
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                        </svg>
                        View Forklifts
                    </a>
                    <a href="{{ route('admin.reports.index') }}" 
   class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
    </svg>
    Analytics & Reports
</a>
                    
                </div>
            </div>
        </div>
    </div>

   <div class="max-w-full mx-auto px-6 py-py-12"></div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl opacity-0 group-hover:opacity-100 transition duration-300 blur-xl"></div>
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 hover:border-blue-300 transition shadow-sm hover:shadow-lg">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-slate-600 text-sm font-semibold mb-2">Total Bookings</p>
                            <p class="text-4xl font-bold text-slate-900">{{ $stats['total_bookings'] ?? 0 }}</p>
                            <p class="text-xs text-slate-500 mt-2">All time records</p>
                        </div>
                        <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-100 to-amber-200 rounded-2xl opacity-0 group-hover:opacity-100 transition duration-300 blur-xl"></div>
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 hover:border-amber-300 transition shadow-sm hover:shadow-lg">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-slate-600 text-sm font-semibold mb-2">Pending Approval</p>
                            <p class="text-4xl font-bold text-amber-600">{{ $stats['pending_bookings'] ?? 0 }}</p>
                            <p class="text-xs text-slate-500 mt-2">Awaiting action</p>
                        </div>
                        <div class="p-3 bg-gradient-to-br from-amber-100 to-amber-50 rounded-xl">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-2xl opacity-0 group-hover:opacity-100 transition duration-300 blur-xl"></div>
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 hover:border-emerald-300 transition shadow-sm hover:shadow-lg">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-slate-600 text-sm font-semibold mb-2">Confirmed</p>
                            <p class="text-4xl font-bold text-emerald-600">{{ $stats['confirmed'] ?? 0 }}</p>
                            <p class="text-xs text-slate-500 mt-2">Active bookings</p>
                        </div>
                        <div class="p-3 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-xl">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-2xl opacity-0 group-hover:opacity-100 transition duration-300 blur-xl"></div>
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 hover:border-indigo-300 transition shadow-sm hover:shadow-lg">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-slate-600 text-sm font-semibold mb-2">Equipment</p>
                            <p class="text-4xl font-bold text-indigo-600">{{ $stats['forklifts'] ?? 0 }}</p>
                            <p class="text-xs text-slate-500 mt-2">Available units</p>
                        </div>
                        <div class="p-3 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-xl">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Messages — ONLY here in dashboard, not in layout --}}
        

        {{-- Bookings Table --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Recent Bookings</h2>
                        <p class="text-sm text-slate-600 mt-1">Manage and review customer requests</p>
                    </div>
                    <a href="{{ route('admin.dashboard') }}"
                       class="px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold hover:bg-blue-200 transition">
                        Refresh
                    </a>
                </div>
            </div>

            @if(count($bookings) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Equipment</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($bookings as $booking)
                                <tr class="hover:bg-blue-50/50 transition">
                                    <td class="px-6 py-5">
                                        <span class="font-bold text-slate-900">#{{ $booking->id }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <p class="font-semibold text-slate-900">{{ $booking->user->name ?? 'N/A' }}</p>
                                        <p class="text-sm text-slate-500">{{ $booking->user->email ?? 'N/A' }}</p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <p class="font-medium text-slate-700">{{ $booking->forklift->name ?? 'N/A' }}</p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <p class="text-slate-600">
                                            {{ $booking->start_time?->setTimezone('America/Regina')->format('M d, Y h:i A') ?? 'N/A' }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-5">
                                        @php
                                            $statusConfig = match($booking->status) {
                                                'confirmed'      => ['bg' => 'bg-emerald-100 text-emerald-700', 'label' => '✅ Confirmed'],
                                                'pending'        => ['bg' => 'bg-amber-100 text-amber-700',    'label' => '⏳ Pending'],
                                                'awaiting_admin' => ['bg' => 'bg-blue-100 text-blue-700',      'label' => '💳 Paid – Awaiting'],
                                                'completed'      => ['bg' => 'bg-indigo-100 text-indigo-700',  'label' => '✔ Completed'],
                                                'cancelled'      => ['bg' => 'bg-red-100 text-red-700',        'label' => '✖ Cancelled'],
                                                'rejected'       => ['bg' => 'bg-rose-100 text-rose-700',      'label' => '✖ Rejected'],
                                                default          => ['bg' => 'bg-slate-100 text-slate-700',    'label' => ucfirst($booking->status)],
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold {{ $statusConfig['bg'] }}">
                                            {{ $statusConfig['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <a href="{{ route('admin.booking-show', $booking) }}"
                                               class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-300 transition">
                                                Details
                                            </a>

                                            

        
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="p-16 text-center">
                    <p class="text-slate-900 font-bold text-lg">No bookings yet</p>
                    <p class="text-slate-600 text-sm mt-1">Bookings will appear here when customers submit requests</p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

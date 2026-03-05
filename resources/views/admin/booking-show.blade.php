@extends('layouts.admin')
@php $tz = 'America/Regina'; @endphp
@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Flash Messages — single source of truth, layout should NOT also render these --}}

        @if(session('refund'))
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                <p class="text-blue-700 font-medium">{{ session('refund') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        @php
            $statusStyles = [
                'confirmed'     => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                'approved'      => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                'pending'       => 'bg-amber-100  text-amber-700  border-amber-200',
                'awaiting_admin'=> 'bg-blue-100   text-blue-700   border-blue-200',
                'cancelled'     => 'bg-red-100    text-red-700    border-red-200',
                'rejected'      => 'bg-red-100    text-red-700    border-red-200',
                'completed'     => 'bg-slate-100  text-slate-700  border-slate-200',
            ];
            $style = $statusStyles[$booking->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';

            $amountTotal    = $booking->amount_total    ?? 0;
            $amountSubtotal = $booking->amount_subtotal ?? 0;
            $amountGst      = $booking->amount_gst      ?? 0;
            $amountPst      = $booking->amount_pst      ?? 0;
            $refundAmount   = $booking->refund_amount   ?? 0;
            // All amounts stored in cents
            $d = 100;
        @endphp

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.bookings.index') }}"
                   class="flex items-center justify-center w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Booking #{{ $booking->id }}</h1>
                    <p class="text-sm text-gray-500">Full booking details & management</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-medium border {{ $style }}">
                <span class="w-2 h-2 rounded-full bg-current opacity-70"></span>
                @if($booking->status === 'awaiting_admin')
                    💳 Paid – Awaiting Approval
                @else
                    {{ ucwords(str_replace('_', ' ', $booking->status)) }}
                @endif
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ═══ LEFT COLUMN ═══ --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Customer Information --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="flex items-center gap-2 text-base font-semibold text-gray-800 mb-5">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A10.97 10.97 0 0112 15c2.21 0 4.26.656 5.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Customer Information
                    </h2>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-5 text-sm">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Full Name</p>
                            <p class="font-medium text-gray-800">{{ $booking->user->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Email</p>
                            <p class="font-medium text-gray-800">{{ $booking->user->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Phone</p>
                            <p class="font-medium text-gray-800">{{ $booking->user->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Customer Since</p>
                            <p class="font-medium text-gray-800">{{ $booking->user?->created_at?->format('M Y') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Service Address</p>
                            <p class="font-medium text-gray-800">{{ $booking->service_address ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Postal Code</p>
                            <p class="font-medium text-gray-800">{{ $booking->postal_code ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Equipment Details --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="flex items-center gap-2 text-base font-semibold text-gray-800 mb-5">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Equipment Details
                    </h2>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-5 text-sm">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Equipment Name</p>
                            <p class="font-medium text-gray-800">{{ $booking->forklift->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Model</p>
                            <p class="font-medium text-gray-800">{{ $booking->forklift->model ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Capacity</p>
                            <p class="font-medium text-gray-800">{{ $booking->forklift->capacity_kg ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Hourly Rate</p>
                            <p class="font-medium text-gray-800">
                                C${{ number_format($booking->forklift->hourly_rate ?? 0, 2) }}/hr
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Notes</p>
                            <p class="font-medium text-gray-800">{{ $booking->notes ?? 'None' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Invoice #</p>
                            <p class="font-medium text-gray-800">{{ $booking->invoice_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Booking Schedule --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="flex items-center gap-2 text-base font-semibold text-gray-800 mb-5">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Booking Schedule
                        <span class="ml-auto text-xs font-normal text-gray-400">(Regina / CST)</span>
                    </h2>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-5 text-sm">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Start Date & Time</p>
                            <p class="font-medium text-gray-800">
                                {{ $booking->start_time?->setTimezone($tz)->format('M d, Y h:i A') ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">End Date & Time</p>
                            <p class="font-medium text-gray-800">
                                {{ $booking->end_time?->setTimezone($tz)->format('M d, Y h:i A') ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Duration</p>
                            <p class="font-medium text-gray-800">
                                @if($booking->start_time && $booking->end_time)
                                    @php $hours = $booking->start_time->diffInHours($booking->end_time); @endphp
                                    {{ $hours }}h
                                    @if($hours >= 24)
                                        ({{ round($hours / 24, 1) }} day{{ round($hours/24,1) != 1 ? 's' : '' }})
                                    @endif
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Submitted On</p>
                            <p class="font-medium text-gray-800">
                                {{ $booking->created_at?->setTimezone($tz)->format('M d, Y h:i A') ?? 'N/A' }}
                            </p>
                        </div>
                        @if($booking->completed_at)
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Completed At</p>
                            <p class="font-medium text-emerald-700">
                                {{ $booking->completed_at->setTimezone($tz)->format('M d, Y h:i A') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Refund Information (only shown after a refund) --}}
                @if($booking->refund_status === 'refunded' && $refundAmount > 0)
                <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6">
                    <h2 class="flex items-center gap-2 text-base font-semibold text-blue-800 mb-5">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Refund Information
                        <span class="ml-auto text-xs font-normal text-blue-400">(Regina / CST)</span>
                    </h2>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div>
                            <p class="text-xs text-blue-400 uppercase tracking-wide mb-1">Refund Status</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                ✓ {{ ucfirst($booking->refund_status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-blue-400 uppercase tracking-wide mb-1">Refund Amount</p>
                            <p class="font-semibold text-blue-800">C${{ number_format($refundAmount / $d, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-blue-400 uppercase tracking-wide mb-1">Refunded At</p>
                            {{-- FIX: convert UTC stored time to Regina local time --}}
                            <p class="font-medium text-blue-800">
                                {{ $booking->refunded_at?->setTimezone($tz)->format('M d, Y h:i A') ?? 'Pending' }}
                            </p>
                        </div>
                        @if($booking->payment_intent_id)
                        <div>
                            <p class="text-xs text-blue-400 uppercase tracking-wide mb-1">Stripe Payment Intent</p>
                            <p class="font-mono text-xs text-blue-700 break-all">{{ $booking->payment_intent_id }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Refund Failed Warning --}}
                @if($booking->refund_status === 'failed')
                <div class="bg-red-50 rounded-2xl border border-red-200 p-6">
                    <h2 class="flex items-center gap-2 text-base font-semibold text-red-800 mb-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Refund Failed
                    </h2>
                    <p class="text-sm text-red-700">The Stripe refund attempt failed. Please issue the refund manually from your <a href="https://dashboard.stripe.com/test/payments" target="_blank" class="underline font-medium">Stripe Dashboard</a>.</p>
                </div>
                @endif

            </div>

            {{-- ═══ RIGHT COLUMN ═══ --}}
            <div class="space-y-6">

                {{-- Payment Summary --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-base font-semibold text-gray-800 mb-5">Payment Summary</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>C${{ number_format($amountSubtotal / $d, 2) }}</span>
                        </div>
                        @if($amountGst > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>GST (5%)</span>
                            <span>C${{ number_format($amountGst / $d, 2) }}</span>
                        </div>
                        @endif
                        @if($amountPst > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>PST (6%)</span>
                            <span>C${{ number_format($amountPst / $d, 2) }}</span>
                        </div>
                        @endif
                        <div class="border-t border-gray-100 pt-3 flex justify-between font-semibold text-gray-900 text-base">
                            <span>Total</span>
                            <span>C${{ number_format($amountTotal / $d, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-1">
                            <span class="text-gray-500">Payment Method</span>
                            <span class="font-medium text-gray-800">
                                @if($booking->payment_method === 'card')
                                    <span class="inline-flex items-center gap-1 text-blue-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        Card (Stripe)
                                    </span>
                                @elseif($booking->payment_method === 'cash')
                                    <span class="inline-flex items-center gap-1 text-green-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                        </svg>
                                        Cash
                                    </span>
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Payment Status</span>
                            <span class="font-semibold
                                {{ $booking->payment_status === 'paid'     ? 'text-emerald-600' :
                                   ($booking->payment_status === 'refunded' ? 'text-blue-600'    : 'text-amber-600') }}">
                                {{ ucfirst($booking->payment_status ?? 'Unpaid') }}
                            </span>
                        </div>
                        @if($booking->currency)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Currency</span>
                            <span class="font-medium text-gray-800 uppercase">{{ $booking->currency }}</span>
                        </div>
                        @endif
                        @if($booking->payment_intent_id)
                        <div class="pt-2 border-t border-gray-100">
                            <p class="text-xs text-gray-400 mb-1">Stripe Payment Intent</p>
                            <p class="font-mono text-xs text-gray-500 break-all">{{ $booking->payment_intent_id }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Admin Actions --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-base font-semibold text-gray-800 mb-4">Admin Actions</h2>
                    <div class="space-y-3">

                        {{-- Approve (pending or awaiting_admin) --}}
                        @if(in_array($booking->status, ['pending', 'awaiting_admin']))
                        <form method="POST" action="{{ route('admin.booking.approve', $booking) }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Approve Booking
                            </button>
                        </form>
                        @endif

                        {{-- Reject (pending or awaiting_admin) --}}
                        @if(in_array($booking->status, ['pending', 'awaiting_admin']))
                        <form method="POST" action="{{ route('admin.booking.reject', $booking) }}"
                              onsubmit="return confirm('Reject this booking?{{ $booking->payment_method === 'card' && $amountTotal > 0 ? ' A Stripe refund of C$' . number_format($amountTotal / 100, 2) . ' will be issued.' : '' }}')">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Reject Booking
                            </button>
                        </form>
                        
                        @endif

                        {{-- Mark Completed (confirmed only) --}}
                        @if($booking->status === 'confirmed')
    <!-- Mark as Completed -->
    <form method="POST" action="{{ route('admin.booking.complete', $booking) }}">
        @csrf
        @method('PATCH')
        <button type="submit"
            class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Mark as Completed
        </button>
    </form>

    <!-- Mark as Paid -->
    <form action="{{ route('admin.bookings.mark-paid', $booking) }}" method="POST" class="mt-2">
        @csrf
        <button type="submit"
            class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
            </svg>
            Mark as Paid
        </button>
    </form>
@endif
                        {{-- Cancel + Refund (any non-terminal status) --}}
                        @if(!in_array($booking->status, ['cancelled', 'completed', 'rejected']))
                        @php
                            $confirmMsg = ($booking->payment_method === 'card' && $amountTotal > 0)
                                ? 'Cancel booking? A Stripe refund of C$' . number_format($amountTotal / $d, 2) . ' will be issued automatically.'
                                : 'Cancel this booking? No refund will be issued (cash or unpaid).';
                        @endphp
                        <form method="POST" action="{{ route('admin.booking.cancel', $booking) }}"
                              onsubmit="return confirm('{{ $confirmMsg }}')">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                </svg>
                                @if($booking->payment_method === 'card' && $amountTotal > 0)
                                    Cancel & Issue Refund
                                @else
                                    Cancel Booking
                                @endif
                            </button>
                        </form>
                        @endif

                        {{-- Terminal state badges --}}
                        @if($booking->status === 'completed')
                        <div class="w-full flex items-center justify-center gap-2 bg-slate-100 text-slate-600 text-sm font-semibold py-2.5 px-4 rounded-xl">
                            ✔ Booking Completed
                        </div>
                        @endif

                        @if(in_array($booking->status, ['cancelled', 'rejected']))
                        <div class="w-full flex items-center justify-center gap-2 bg-red-50 text-red-600 text-sm font-semibold py-2.5 px-4 rounded-xl border border-red-200">
                            ✖ {{ ucfirst($booking->status) }}
                            @if($booking->refund_status === 'refunded')
                                — Refunded
                            @elseif($booking->refund_status === 'failed')
                                — ⚠ Refund Failed
                            @endif
                        </div>
                        @endif

                    </div>
                    <p class="mt-4 text-xs text-gray-400 text-center">
                        {{ $booking->payment_method === 'card' ? 'Stripe refunds apply to card payments only.' : 'Cash payments: no automatic refund.' }}
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
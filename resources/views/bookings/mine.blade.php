@extends('layouts.app')

@section('content')
@php
    use App\Models\Booking;

    $badge = function (string $s): string {
        return match ($s) {
            Booking::STATUS_CONFIRMED  => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            Booking::STATUS_AWAITING,
            Booking::STATUS_PENDING    => 'bg-amber-100 text-amber-800 border-amber-200',
            Booking::STATUS_CANCELLED  => 'bg-red-100 text-red-700 border-red-200',
            default                    => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    };

    $label = fn (string $s) => match ($s) {
        Booking::STATUS_CONFIRMED  => 'Accepted',
        Booking::STATUS_AWAITING   => 'Awaiting Admin',
        Booking::STATUS_PENDING    => 'Pending',
        Booking::STATUS_CANCELLED  => 'Denied',
        default                    => ucfirst($s),
    };
@endphp

<div class="max-w-6xl mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-semibold text-slate-800">My Bookings</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Accepted --}}
        <section class="bg-white rounded-xl shadow p-4">
            <h2 class="text-lg font-semibold mb-3">Accepted</h2>
            @forelse ($accepted as $b)
                <div class="border rounded-md p-3 mb-3">
                    <div class="flex items-center justify-between">
                        <div class="font-medium">{{ $b->forklift->name ?? '—' }}</div>
                        <span class="inline-flex items-center rounded border px-2 py-0.5 text-xs {{ $badge($b->status) }}">
                            {{ $label($b->status) }}
                        </span>
                    </div>

                    <div class="text-sm text-slate-600 mt-1">
                        {{ $b->start_time?->clone()->timezone($tz)->format('Y-m-d H:i a') }}
                        →
                        {{ $b->end_time?->clone()->timezone($tz)->format('Y-m-d H:i a') }}
                        <span class="ml-1 text-xs text-slate-500">({{ $tz }})</span>
                    </div>

                    @if ($b->service_address)
                        <div class="text-xs text-slate-500 mt-1">{{ $b->service_address }}</div>
                    @endif
                    <div class="text-xs text-slate-500 mt-1">Invoice: {{ $b->invoice_number ?? '—' }}</div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No accepted bookings yet.</p>
            @endforelse
        </section>

        {{-- Pending --}}
        <section class="bg-white rounded-xl shadow p-4">
            <h2 class="text-lg font-semibold mb-3">Pending Review</h2>
            @forelse ($pending as $b)
                <div class="border rounded-md p-3 mb-3">
                    <div class="flex items-center justify-between">
                        <div class="font-medium">{{ $b->forklift->name ?? '—' }}</div>
                        <span class="inline-flex items-center rounded border px-2 py-0.5 text-xs {{ $badge($b->status) }}">
                            {{ $label($b->status) }}
                        </span>
                    </div>

                    <div class="text-sm text-slate-600 mt-1">
                        {{ $b->start_time?->clone()->timezone($tz)->format('Y-m-d H:i a') }}
                        →
                        {{ $b->end_time?->clone()->timezone($tz)->format('Y-m-d H:i a') }}
                        <span class="ml-1 text-xs text-slate-500">({{ $tz }})</span>
                    </div>

                    @if ($b->service_address)
                        <div class="text-xs text-slate-500 mt-1">{{ $b->service_address }}</div>
                    @endif

                    @if ($b->payment_method === 'card')
                        <div class="text-xs text-slate-500 mt-1">Paid online — awaiting admin approval.</div>
                    @else
                        <div class="text-xs text-slate-500 mt-1">Cash booking — awaiting admin approval.</div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-500">No bookings awaiting review.</p>
            @endforelse
        </section>

        {{-- Denied --}}
        <section class="bg-white rounded-xl shadow p-4">
            <h2 class="text-lg font-semibold mb-3">Denied</h2>
            @forelse ($denied as $b)
                <div class="border rounded-md p-3 mb-3">
                    <div class="flex items-center justify-between">
                        <div class="font-medium">{{ $b->forklift->name ?? '—' }}</div>
                        <span class="inline-flex items-center rounded border px-2 py-0.5 text-xs {{ $badge($b->status) }}">
                            {{ $label($b->status) }}
                        </span>
                    </div>

                    <div class="text-sm text-slate-600 mt-1">
                        {{ $b->start_time?->clone()->timezone($tz)->format('Y-m-d H:i a') }}
                        →
                        {{ $b->end_time?->clone()->timezone($tz)->format('Y-m-d H:i a') }}
                        <span class="ml-1 text-xs text-slate-500">({{ $tz }})</span>
                    </div>

                    @if ($b->service_address)
                        <div class="text-xs text-slate-500 mt-1">{{ $b->service_address }}</div>
                    @endif

                    <div class="text-xs text-slate-500 mt-1">If this was a mistake, please contact support.</div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No denied bookings.</p>
            @endforelse
        </section>
    </div>

    {{-- Action Buttons --}}
    <div class="mt-20 border-t border-zinc-200 bg-zinc-50 py-10">
        <div class="mx-auto max-w-5xl px-6">
            <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                <a href="{{ route('bookings.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl
                          bg-emerald-600 px-6 py-3
                          text-sm font-semibold text-white
                          shadow-md transition-all
                          hover:bg-emerald-700 hover:shadow-lg
                          focus:outline-none focus:ring-2
                          focus:ring-emerald-400 focus:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                    New Booking
                </a>

                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 rounded-xl
                          bg-sky-600 px-6 py-3
                          text-sm font-semibold text-white
                          shadow transition-all
                          hover:bg-sky-700 hover:shadow-md
                          focus:outline-none focus:ring-2
                          focus:ring-sky-400 focus:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4m4 0h4a1 1 0 001-1V10"/>
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- @component('mail::message')
# Thanks, {{ $booking->user->name ?? 'customer' }}!
Your booking has been **confirmed**.

@component('mail::panel')
**Booking ID:** #{{ $booking->id }}  
**Forklift:** {{ $booking->forklift->name }} ({{ $booking->forklift->capacity_kg }} kg)  
**Location:** {{ optional($booking->forklift->location)->name ?? '—' }}  
**Start:** {{ $booking->start_time->timezone('America/Regina')->format('D, M j, Y g:ia') }}  
**End:** {{ $booking->end_time->timezone('America/Regina')->format('D, M j, Y g:ia') }}  
**Total:** ${{ number_format(($booking->amount_total ?? 0)/100, 2) }} {{ $booking->currency ?? 'CAD' }}  
**Invoice #:** {{ $booking->invoice_number ?? '—' }}  
**Transaction:** {{ $booking->payment_intent_id ?? '—' }}  
@endcomponent

@component('mail::button', ['url' => route('bookings.thankyou', $booking->id)])
View Booking
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent -->
@php
    // Use per-booking timezone if present, otherwise fallback to Regina
    $tz = $booking->tz ?? 'America/Regina';
    $start = $booking->start_time->timezone($tz);
    $end   = $booking->end_time->timezone($tz);
@endphp

@component('mail::message')
# Thanks, {{ $booking->user->name ?? 'customer' }}!
Your booking has been **confirmed**.

@component('mail::panel')
**Booking ID:** #{{ $booking->id }}  
**Forklift:** {{ $booking->forklift->name }} ({{ $booking->forklift->capacity_kg }} kg)  
**Location:** {{ optional($booking->forklift->location)->name ?? '—' }}  
**Start:** {{ $start->format('D, M j, Y g:ia') }}  
**End:** {{ $end->format('D, M j, Y g:ia') }}  
**Total:** ${{ number_format(($booking->amount_total ?? 0)/100, 2) }} {{ $booking->currency ?? 'CAD' }}  
**Invoice #:** {{ $booking->invoice_number ?? '—' }}  
**Transaction:** {{ $booking->payment_intent_id ?? '—' }}  
@endcomponent

@component('mail::button', ['url' => route('bookings.thankyou', $booking->id)])
View Booking
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@endcomponent

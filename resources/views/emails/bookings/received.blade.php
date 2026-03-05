@php
  // Show times in your app’s timezone (falls back to SK/Regina)
  // $tz = config('app.timezone', 'America/Regina');
  $tz = $booking->tz
      ?? config('booking.display_tz', 'America/Regina');
@endphp

@component('mail::message')
# We’ve received your booking request ✅

Hi {{ $booking->user->name }},

Thanks! Your request has been submitted and is now **waiting for admin review**.  
You’ll get another email as soon as it’s **approved** (with the final invoice).  
If anything is unavailable, we’ll contact you to reschedule.

@component('mail::panel')
**Booking ID:** #{{ $booking->id }}  
**Forklift:** {{ $booking->forklift->name }} ({{ $booking->forklift->capacity_kg }} kg)  
**Location:** {{ optional($booking->forklift->location)->name ?? '—' }}  
**Start:** {{ $booking->start_time->timezone($tz)->format('D, M j, Y g:ia') }}  
**End:** {{ $booking->end_time->timezone($tz)->format('D, M j, Y g:ia') }}  
@if($booking->service_address)
**Service Address:** {{ $booking->service_address }} {{ $booking->postal_code ? '('.$booking->postal_code.')' : '' }}
@endif
@endcomponent

### Cost Summary (estimated)
- Subtotal: ${{ number_format(($booking->amount_subtotal ?? 0)/100, 2) }}  
- GST: ${{ number_format(($booking->amount_gst ?? 0)/100, 2) }}  
- PST: ${{ number_format(($booking->amount_pst ?? 0)/100, 2) }}  
- **Total:** **${{ number_format(($booking->amount_total ?? 0)/100, 2) }} {{ $booking->currency ?? 'CAD' }}**

> Final charges are confirmed on approval and will appear on your invoice.

### What happens next
- An admin reviews availability and details.  
- You’ll receive a **confirmation email** (and invoice PDF) once approved.  
- If adjustments are needed, we’ll reach out.

### Need to change something?
Reply to this email or update your booking from the link below.

@component('mail::button', ['url' => route('bookings.thankyou', $booking->id)])
View / Manage Booking
@endcomponent

Thanks,  
{{ config('app.name') }}

@if(config('app.name'))
<br><span style="font-size:12px;color:#94a3b8">Time zone: {{ $tz }}</span>
@endif
@endcomponent

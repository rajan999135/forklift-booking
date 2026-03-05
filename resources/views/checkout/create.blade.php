@extends('layouts.app')

@section('content')
<link rel="preconnect" href="https://js.stripe.com">
<script src="https://js.stripe.com/v3"></script>

<script>
  const DEFAULT_FORKLIFT_ID = {{ $forklift->id ?? request('forklift_id') ?? 'null' }};
</script>

@php
  $money      = fn(int $c) => number_format($c / 100, 2);
  $rateCents  = (int) round(((float) $forklift->hourly_rate) * 100);
  $mapsKey    = config('services.google.maps_key');
@endphp

<div class="max-w-6xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

  {{-- LEFT: form --}}
  <div class="bg-white rounded-xl shadow p-5">
    <h2 class="text-lg font-semibold mb-4">Checkout</h2>

    <form id="checkoutForm" method="POST" action="{{ route('bookings.store') }}" class="space-y-4">
      @csrf
      <input type="hidden" name="forklift_id"     value="{{ $forklift->id }}">
      <input type="hidden" name="payment_method"  id="payment_method" value="cash">
      <input type="hidden" name="amount_total"    id="amount_total_post">

      <input type="datetime-local" id="start_at" name="start_time"
             class="w-full border rounded-md p-2 text-sm"
             value="{{ $start->format('Y-m-d\TH:i') }}">

      <input type="datetime-local" id="end_at" name="end_time"
             class="w-full border rounded-md p-2 text-sm"
             value="{{ $end->format('Y-m-d\TH:i') }}">

      <p id="timeError" class="mt-2 text-sm text-red-600 hidden"></p>

      <div>
        <label class="block text-sm text-slate-600">Forklift</label>
        <div class="border rounded-md p-2 text-sm bg-slate-50">
          {{ $forklift->name }} — {{ $forklift->capacity_kg }}kg ({{ $forklift->location->name ?? '—' }})
        </div>
      </div>

      {{-- Address --}}
      <div>
        <label class="block text-sm text-slate-600 mb-1">Service Address</label>
        <input type="text" id="address_line" name="service_address"
               class="w-full border rounded-md p-2 text-sm"
               placeholder="Start typing your address…" autocomplete="off">
        <p id="gmapsMsg" class="text-xs text-amber-600 mt-1 hidden">
          Google Places couldn't load — check your API key or referrer settings.
        </p>
        <p class="text-xs text-slate-500 mt-2">Or pick from Google suggestions:</p>
        <div id="address_element" class="mt-1"></div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <div>
          <label class="block text-sm text-slate-600 mb-1">Postal Code</label>
          <input type="text" id="postal_code" name="postal_code"
                 class="w-full border rounded-md p-2 text-sm">
        </div>
        <div>
          <label class="block text-sm text-slate-600 mb-1">City</label>
          <input type="text" id="city" name="city"
                 class="w-full border rounded-md p-2 text-sm">
        </div>
        <div>
          <label class="block text-sm text-slate-600 mb-1">Province</label>
          <input type="text" id="province" name="province"
                 class="w-full border rounded-md p-2 text-sm">
        </div>
      </div>

      <div>
        <label class="block text-sm text-slate-600 mb-1">Country</label>
        <input type="text" id="country" name="country"
               class="w-full border rounded-md p-2 text-sm" style="max-width:210px">
      </div>

      <input type="hidden" id="lat" name="lat">
      <input type="hidden" id="lng" name="lng">

      {{-- Payment method --}}
      <div>
        <label class="block text-sm text-slate-600 mb-1">Payment Method</label>
        <div class="flex items-center gap-6">
          <label class="inline-flex items-center gap-2">
            <input type="radio" name="pm" value="cash" checked> Cash
          </label>
          <label class="inline-flex items-center gap-2">
            <input type="radio" name="pm" value="card" id="pmCard"> Online (Card)
          </label>
        </div>
      </div>

      {{-- Card UI --}}
      <div id="cardWrap" class="hidden">
        <div>
          <label class="block text-sm text-slate-600 mb-1">Cardholder name</label>
          <input type="text" id="cardholder" class="w-full border rounded-md p-2 text-sm">
        </div>
        <label class="block text-sm text-slate-600 mb-1 mt-2">Card</label>
        <div id="cardElement" class="border rounded-md p-3"></div>
        <p id="cardError" class="text-sm text-red-600 mt-2 hidden"></p>
      </div>

      {{-- Summary --}}
      <div class="bg-slate-50 rounded-md p-3">
        <div class="flex items-center justify-between text-sm">
          <div class="text-slate-600">Estimated Total</div>
          <div class="font-semibold"><span id="totalLabel">$0.00</span> CAD</div>
        </div>
        <p class="text-xs text-slate-500 mt-1">Final amount is confirmed on the server before payment.</p>
      </div>

      <button id="submitBtn" type="button"
              class="w-full px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700">
        Book (Cash)
      </button>
    </form>

    <div class="mt-10 pt-6 border-t border-blue-200 flex flex-wrap justify-center gap-3">
      <a href="{{ route('bookings.create') }}"
         class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-md hover:bg-blue-700">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Previous Page
      </a>
      <a href="{{ route('home') }}"
         class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-6 py-3 text-sm font-semibold text-white shadow-md hover:bg-sky-600">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h4m10-11v11a1 1 0 01-1 1h-4"/>
        </svg>
        Home Page
      </a>
    </div>
  </div>

  {{-- RIGHT: Calendar --}}
  <div class="bg-white rounded-xl shadow p-5">
    <h3 class="text-base font-semibold mb-3">Calendar</h3>
    <div id="calendar" class="border rounded-lg p-2"></div>

    <div class="flex items-center justify-between mt-4 mb-2">
      <div class="font-semibold text-sm">
        Availability for <span id="chipsDate">{{ now()->toDateString() }}</span>
      </div>
      <div class="text-xs text-slate-500 flex items-center gap-3">
        <span class="inline-flex items-center gap-1">
          <span class="w-2 h-2 bg-green-500 rounded-full"></span> Free
        </span>
        <span class="inline-flex items-center gap-1">
          <span class="w-2 h-2 bg-yellow-400 rounded-full"></span> Pending
        </span>
        <span class="inline-flex items-center gap-1">
          <span class="w-2 h-2 bg-red-500 rounded-full"></span> Booked
        </span>
      </div>
    </div>
    <div id="slots" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2"></div>
  </div>
</div>

<script>
(() => {
  /* ── Helpers ── */
  function getTotalMinutesFromDatetimeLocal(value) {
    if (!value || !value.includes('T')) return null;
    const [h, m] = value.split('T')[1].split(':');
    const hour = parseInt(h, 10), min = parseInt(m, 10);
    if (isNaN(hour) || isNaN(min)) return null;
    return hour * 60 + min;
  }

  function convertLocalToUTC(localStr) {
    if (!localStr) return null;
    return new Date(localStr).toISOString();
  }

  function getLocalNowString() {
    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    return `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
  }

  /* ── Totals ── */
  const rateCents  = {{ $rateCents }};
  const totalLabel = document.getElementById('totalLabel');
  const startEl    = document.getElementById('start_at');
  const endEl      = document.getElementById('end_at');

  const nowStr = getLocalNowString();
  startEl.min = nowStr;
  endEl.min   = nowStr;

  function calcTotal() {
    const start   = new Date(startEl.value);
    const end     = new Date(endEl.value);
    const minutes = Math.max(0, (end - start) / 60000);
    const hours   = Math.max(1, Math.ceil(minutes / 60));
    const subtotal = hours * rateCents;
    const gst      = Math.round(subtotal * 0.05);
    const pst      = Math.round(subtotal * 0.06);
    const total    = subtotal + gst + pst;
    totalLabel.textContent = '$' + (total / 100).toFixed(2);
    return { hours, totalCents: total };
  }

  startEl.addEventListener('change', () => {
    endEl.min = startEl.value;
    calcTotal();
  });
  endEl.addEventListener('change', calcTotal);
  calcTotal();

  /* ── Availability chips ── */
  const chipsDate = document.getElementById('chipsDate');
  const slotsWrap = document.getElementById('slots');

  async function loadChips(dateStr) {
    chipsDate.textContent = dateStr;
    slotsWrap.innerHTML   = '<div class="text-slate-500 text-sm">Loading…</div>';
    try {
      const forkId = DEFAULT_FORKLIFT_ID;
      if (!forkId) throw new Error('No forklift selected.');

      const url = new URL("{{ route('bookings.availability') }}", window.location.origin);
      url.searchParams.set('date',        dateStr);
      url.searchParams.set('forklift_id', forkId);
      url.searchParams.set('tz',          'America/Regina');

      const res = await fetch(url);
      if (!res.ok) throw new Error(`Server error: HTTP ${res.status}`);

      const data = await res.json();
      if (!Array.isArray(data.slots)) throw new Error('Invalid response from server.');

      slotsWrap.innerHTML = '';
      data.slots.forEach(slot => {
        const chip = document.createElement('button');
        chip.type = 'button';

        let chipClass = '';
        if (slot.status === 'free') {
          chipClass = 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100';
        } else if (slot.status === 'awaiting') {
          chipClass = 'bg-yellow-50 text-yellow-700 border-yellow-300 cursor-not-allowed';
        } else {
          chipClass = 'bg-red-50 text-red-700 border-red-200 cursor-not-allowed';
        }
        chip.className = 'px-2 py-1 rounded-md text-sm border ' + chipClass;
        chip.textContent = slot.label + (slot.status === 'awaiting' ? ' ⏳' : '');

        if (slot.status === 'free') {
          chip.addEventListener('click', () => {
            slotsWrap.querySelectorAll('button').forEach(b => b.style.outline = '');
            chip.style.outline = '2px solid #10b981';
            const startLocal = new Date(slot.start);
            const endLocal   = new Date(slot.end);
            const pad = n => String(n).padStart(2, '0');
            const fmt = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
            startEl.value = fmt(startLocal);
            endEl.value   = fmt(endLocal);
            calcTotal();
          });
        } else {
          chip.disabled = true;
        }
        slotsWrap.appendChild(chip);
      });
    } catch (e) {
      console.error('Availability error:', e);
      slotsWrap.innerHTML = `<div class="text-red-600 text-sm"><strong>Error:</strong> ${e.message}</div>`;
    }
  }

  /* ── FullCalendar ── */
  const calendarEl = document.getElementById('calendar');
  const calendar   = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height:      350,
    selectable:  true,
    events: (info, success, failure) => {
      const url = new URL(@json(route('bookings.calendar')), window.location.origin);
      url.searchParams.set('forklift_id', DEFAULT_FORKLIFT_ID);
      fetch(url)
        .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
        .then(success)
        .catch(err => { console.error('Calendar error:', err); failure(err); });
    },
    dateClick: arg => {
      loadChips(arg.dateStr);

      const currentStartTime = startEl.value ? startEl.value.split('T')[1] : '09:00';
      const currentEndTime   = endEl.value   ? endEl.value.split('T')[1]   : '17:00';

      const newStart = arg.dateStr + 'T' + currentStartTime;
      const newEnd   = arg.dateStr + 'T' + currentEndTime;

      const nowStr = getLocalNowString();
      if (newStart >= nowStr) {
        startEl.value = newStart;
        endEl.value   = newEnd;
        calcTotal();
      }
    },
  });
  calendar.render();
  loadChips(new Date().toISOString().slice(0, 10));

  /* ── Payment mode toggle ── */
  const pmRadios  = document.querySelectorAll('input[name="pm"]');
  const pmCard    = document.getElementById('pmCard');
  const cardWrap  = document.getElementById('cardWrap');
  const submitBtn = document.getElementById('submitBtn');
  const payMethod = document.getElementById('payment_method');

  pmRadios.forEach(r => r.addEventListener('change', () => {
    const isCard = pmCard.checked;
    cardWrap.classList.toggle('hidden', !isCard);
    payMethod.value       = isCard ? 'card' : 'cash';
    submitBtn.textContent = isCard ? 'Pay & Book' : 'Book (Cash)';
    if (isCard) ensureStripeMounted();
  }));

  /* ── Stripe ── */
  let stripe, elements, cardEl;
  const cardError = document.getElementById('cardError');
  function showErr(msg) { cardError.textContent = msg; cardError.classList.remove('hidden'); }

  function ensureStripeMounted() {
    if (cardEl) return true;
    const pk = @json(config('services.stripe.key'));
    if (!pk) { showErr('Card payments unavailable.'); return false; }
    try {
      stripe   = Stripe(pk);
      elements = stripe.elements();
      cardEl   = elements.create('card', { hidePostalCode: true });
      cardEl.mount('#cardElement');
      return true;
    } catch (e) {
      showErr('Could not load card form.'); return false;
    }
  }

  /* ── Submit guard ── */
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                 || document.querySelector('input[name="_token"]')?.value || '';

  // FIX: track in-flight state to prevent double-submit
  let isSubmitting = false;

  function setBusy(busy) {
    isSubmitting = busy;
    submitBtn.disabled = busy;
    submitBtn.classList.toggle('opacity-60', busy);
  }

  function redirectToThanks(data) {
    if (data?.redirect)      window.location.href = data.redirect;
    else if (data?.id)       window.location.href = `/bookings/${data.id}/thank-you`;
    else alert('Booking created but no redirect URL returned.');
  }

  /* ── Submit ── */
  submitBtn.addEventListener('click', async () => {
    // FIX: prevent double-click / re-entry
    if (isSubmitting) return;

    if (new Date(endEl.value) <= new Date(startEl.value)) {
      alert('End time must be after start time.');
      return;
    }

    if (new Date(startEl.value) < new Date()) {
      alert('You cannot book a time slot in the past.');
      return;
    }

    const startMins = getTotalMinutesFromDatetimeLocal(startEl.value);
    const endMins   = getTotalMinutesFromDatetimeLocal(endEl.value);
    if (startMins === null || endMins === null ||
        startMins < 300 || startMins > 1380 ||
        endMins   < 300 || endMins   > 1380) {
      alert('Please select a time between 5:00 AM and 11:00 PM.');
      return;
    }

    const { totalCents } = calcTotal();
    document.getElementById('amount_total_post').value = totalCents;

    const basePayload = {
      forklift_id:     @json($forklift->id),
      payment_method:  payMethod.value,
      start_time:      convertLocalToUTC(startEl.value),
      end_time:        convertLocalToUTC(endEl.value),
      service_address: document.getElementById('address_line').value,
      postal_code:     document.getElementById('postal_code').value,
      city:            document.getElementById('city').value,
      province:        document.getElementById('province').value,
      country:         document.getElementById('country').value,
      lat:             document.getElementById('lat').value  || null,
      lng:             document.getElementById('lng').value  || null,
      tz:              'America/Regina',
    };

    setBusy(true);
    try {
      /* ══════════════════════════════
         CASH FLOW
         Simple: one POST → redirect
         ══════════════════════════════ */
      if (payMethod.value === 'cash') {
        const res = await fetch(@json(route('bookings.store')), {
          method:  'POST',
          headers: {
            'Content-Type':     'application/json',
            'X-CSRF-TOKEN':     csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept':           'application/json',
          },
          body: JSON.stringify(basePayload),
        });
        let data = {};
        try { data = await res.json(); } catch {}
        if (!res.ok) {
          const msg = data?.errors
            ? Object.values(data.errors).flat().join(' | ')
            : (data?.message || `HTTP ${res.status}`);
          throw new Error(msg);
        }
        redirectToThanks(data);
        return;
      }

      /* ══════════════════════════════════════════════════════════════
         CARD FLOW  — THREE-STEP, NO DOUBLE-INSERT
         ──────────────────────────────────────────────────────────────
         FIX: The old code called checkout.intent (which created a
         pending booking) AND THEN called bookings.store again with the
         same slot, triggering the unique-slot constraint and leaving
         the user stuck with an alert and no redirect.

         Correct flow:
           1. POST checkout.intent  → server creates ONE pending booking
                                       + PaymentIntent, returns
                                       { booking_id, clientSecret }
           2. stripe.confirmCardPayment → Stripe charges the card
           3. POST bookings.confirm  → server marks the existing booking
                                       confirmed (UPDATE, not INSERT)
                                       and returns redirect URL
         ══════════════════════════════════════════════════════════════ */
      if (!ensureStripeMounted()) { setBusy(false); return; }
      cardError.classList.add('hidden');

      const cardholder = document.getElementById('cardholder').value.trim();
      if (!cardholder) { showErr('Please enter the cardholder name.'); setBusy(false); return; }

      // ── Step 1: Create pending booking + PaymentIntent on server ──
      const intentRes = await fetch(@json(route('checkout.intent')), {
        method:  'POST',
        headers: {
          'Content-Type':     'application/json',
          'X-CSRF-TOKEN':     csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept':           'application/json',
        },
        body: JSON.stringify({
          forklift_id:     basePayload.forklift_id,
          start_time:      basePayload.start_time,
          end_time:        basePayload.end_time,
          name:            cardholder,
          service_address: basePayload.service_address,
          postal_code:     basePayload.postal_code,
          city:            basePayload.city,
          province:        basePayload.province,
          country:         basePayload.country,
          lat:             basePayload.lat,
          lng:             basePayload.lng,
        }),
      });

      const intentData   = await intentRes.json();
      const clientSecret = intentData.clientSecret || intentData.client_secret;
      const bookingId    = intentData.booking_id;

      if (!intentRes.ok || !clientSecret || !bookingId) {
        showErr(intentData.message || 'Unable to start payment. Please try again.');
        setBusy(false);
        return;
      }

      // ── Step 2: Confirm card payment in the browser via Stripe ──
      const result = await stripe.confirmCardPayment(clientSecret, {
        payment_method: { card: cardEl, billing_details: { name: cardholder } },
      });

      if (result.error) {
        // Payment was declined / cancelled — tell user; don't redirect.
        // The server-side pending booking will be cleaned up by a
        // scheduled job or webhook (recommend adding one).
        showErr(result.error.message || 'Payment failed. Please try a different card.');
        setBusy(false);
        return;
      }

      // ── Step 3: Mark the existing booking as confirmed on server ──
      // FIX: POST to a dedicated confirm endpoint (UPDATE, not INSERT)
      //      so we never attempt to insert a duplicate row.
      const confirmRes = await fetch(@json(route('checkout.confirm')), {
        method:  'POST',
        headers: {
          'Content-Type':     'application/json',
          'X-CSRF-TOKEN':     csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept':           'application/json',
        },
        body: JSON.stringify({
          booking_id:        bookingId,
          payment_intent_id: result.paymentIntent?.id || null,
        }),
      });

      let confirmData = {};
      try { confirmData = await confirmRes.json(); } catch {}

      if (!confirmRes.ok) {
        // Payment went through but confirmation call failed.
        // Money is already captured — show a clear message instead of a
        // generic error so the user knows their booking IS recorded.
        const msg = confirmData?.message || 'Payment received but we hit a server error confirming your booking. Please contact support — your booking ID is ' + bookingId + '.';
        alert(msg);
        // Still try to redirect so they reach the thank-you page
        window.location.href = `/bookings/${bookingId}/thank-you`;
        return;
      }

      redirectToThanks(confirmData);

    } catch (err) {
      console.error('Submit error:', err);
      alert(err.message || 'Something went wrong. Please try again.');
    } finally {
      // Only release the busy lock if we haven't redirected.
      // (After redirect the page unloads anyway, so this is safe.)
      setBusy(false);
    }
  });

  /* ── Google Places helpers ── */
  function fillFromPlace(place) {
    const comps     = place.addressComponents || place.address_components || [];
    const mapByType = {};
    comps.forEach(c => (c.types || []).forEach(t => (mapByType[t] = c)));
    const getLong  = t => mapByType[t]?.longText   || mapByType[t]?.long_name  || '';
    const getShort = t => mapByType[t]?.shortText  || mapByType[t]?.short_name || '';

    const input = document.getElementById('address_line');
    if (place.formattedAddress || place.formatted_address)
      input.value = place.formattedAddress || place.formatted_address;

    document.getElementById('postal_code').value =
      getLong('postal_code') || getShort('postal_code');
    document.getElementById('city').value =
      getLong('locality') || getLong('postal_town') ||
      getLong('sublocality') || getLong('administrative_area_level_2');
    document.getElementById('province').value =
      getShort('administrative_area_level_1') || getLong('administrative_area_level_1');
    document.getElementById('country').value =
      getShort('country') || getLong('country');

    const loc = place.location || place.geometry?.location;
    if (loc) {
      document.getElementById('lat').value = typeof loc.lat === 'function' ? loc.lat() : loc.lat;
      document.getElementById('lng').value = typeof loc.lng === 'function' ? loc.lng() : loc.lng;
    }
  }

  window.initPlaces = function () {
    try {
      const input = document.getElementById('address_line');
      if (!input || !window.google?.maps?.places) throw new Error('no-google');

      const ac = new google.maps.places.Autocomplete(input, {
        fields: ['address_components', 'geometry', 'formatted_address'],
        types:  ['address'],
      });
      ac.addListener('place_changed', () => fillFromPlace(ac.getPlace() || {}));

      if (google.maps.places?.AutocompleteElement) {
        const mount = document.getElementById('address_element');
        if (mount) {
          const elem = new google.maps.places.AutocompleteElement({
            componentRestrictions: { country: ['ca'] },
            placeholder: 'Search your address…',
          });
          elem.addEventListener('gmp-placeselect',        e => e?.detail?.place && fillFromPlace(e.detail.place));
          elem.addEventListener('gmp-suggestview-select', e => e?.detail?.place && fillFromPlace(e.detail.place));
          elem.addEventListener('input', e => {
            if (typeof e.target?.value === 'string')
              document.getElementById('address_line').value = e.target.value;
          });
          mount.appendChild(elem);
        }
      }
    } catch {
      document.getElementById('gmapsMsg')?.classList.remove('hidden');
    }
  };

})();
</script>

@if($mapsKey)
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ $mapsKey }}&libraries=places&v=weekly&language=en&callback=initPlaces">
  </script>
@else
  <script>document.getElementById('gmapsMsg')?.classList.remove('hidden');</script>
@endif

@endsection
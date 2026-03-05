{{-- resources/views/bookings/create.blade.php --}}
@extends('layouts.app')
@section('hideDefaultNav', true)

@section('content')
  @php
    // SVG placeholder to avoid broken images (no network requests)
    $PLACEHOLDER = 'data:image/svg+xml;utf8,' . rawurlencode(
      '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="675">
         <defs><linearGradient id="g" x1="0" x2="1" y1="0" y2="1">
           <stop stop-color="#eef2f7" offset="0"/><stop stop-color="#f4f6fa" offset="1"/></linearGradient></defs>
         <rect width="100%" height="100%" fill="url(#g)"/>
         <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
               font-family="system-ui, -apple-system, Segoe UI, Roboto" font-size="20" fill="#94a3b8">
           No image
         </text>
       </svg>'
    );
  @endphp

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- STRIP: horizontally scrollable model tiles --}}
    <section class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm">
      <div class="mb-3 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Select a Forklift</h2>

        <div class="hidden md:flex items-center gap-2 text-xs text-zinc-500">
          <span>Tip: scroll the strip</span>
          <div class="flex items-center gap-1 rounded-full border border-zinc-200 px-2 py-0.5">
            <span>◀︎</span><span>▶︎</span>
          </div>
        </div>
      </div>
  

      <div class="relative group">
        {{-- Left/Right controls (only on ≥sm screens) --}}
        <button id="modelsPrev" type="button" aria-label="Previous"
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 hidden sm:flex h-9 w-9 items-center justify-center
                       rounded-full border border-zinc-200 bg-white shadow hover:bg-emerald-50">
          ‹
        </button>
        <button id="modelsNext" type="button" aria-label="Next"
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 hidden sm:flex h-9 w-9 items-center justify-center
                       rounded-full border border-zinc-200 bg-white shadow hover:bg-emerald-50">
          ›
        </button>

        <div id="modelsStrip"
             class="flex gap-4 overflow-x-auto scroll-smooth py-1 px-1 sm:px-8
                    [scrollbar-width:none] [-ms-overflow-style:none]"
             style="scrollbar-width:none">
          <style>#modelsStrip::-webkit-scrollbar{display:none}</style>

          @forelse($forklifts as $i => $f)
            @php
              $images = $f->images_urls ?: ($f->image_url ? [$f->image_url] : [$PLACEHOLDER]);
            @endphp

            <div
              class="model-tile shrink-0 w-[260px] p-3 rounded-2xl bg-white
                     border border-zinc-200/80 shadow-sm hover:shadow-md transition
                     cursor-pointer data-[active=true]:ring-2 data-[active=true]:ring-sky-400/70">
              <div class="relative">
                <img src="{{ $images[0] }}" alt="{{ $f->name }}" class="h-40 w-full rounded-lg object-cover">
                <div class="absolute right-2 top-2 rounded-full bg-emerald-600/95 px-2.5 py-0.5 text-[11px] font-semibold text-white shadow">
                  ${{ number_format($f->hourly_rate ?? 0, 2) }}/hr
                </div>
              </div>

              <div class="mt-3 flex items-start justify-between gap-2">
                <div class="min-w-0">
                  <div class="truncate text-sm font-medium text-zinc-900">{{ $f->name }}</div>
                  <div class="text-xs text-zinc-500">
                    Capacity: {{ (int)($f->capacity_kg ?? 0) }} kg
                    @if(optional($f->location)->name)
                      <span class="mx-1">•</span>{{ $f->location->name }}
                    @endif
                  </div>
                </div>
              </div>

              {{-- Inline data for JS --}}
              <template class="js-payload"
                        data-index="{{ $i }}"
                        data-id="{{ $f->id }}"
                        data-name="{{ e($f->name) }}"
                        data-capacity="{{ (int)($f->capacity_kg ?? 0) }}"
                        data-rate="{{ number_format((float)($f->hourly_rate ?? 0), 2, '.', '') }}"
                        data-location="{{ $f->location_id }}"
                        data-images='@json($images)'></template>
            </div>
          @empty
            <div class="text-sm text-red-600">No forklifts available.</div>
          @endforelse
        </div>
      </div>
    </section>

    {{-- PREVIEW: large image + details + continue --}}
    <section class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm">
      <h3 class="text-base font-semibold mb-3">Model preview</h3>

      <div class="grid gap-6 md:grid-cols-3">
        {{-- Main media --}}
        <div class="md:col-span-2">
          <div class="relative overflow-hidden rounded-2xl border border-zinc-200">
            <img id="mainModelImage" src="{{ $PLACEHOLDER }}" alt="Model image"
                 class="h-64 w-full object-cover bg-zinc-100">

            {{-- Carousel arrows --}}
            <button id="imgPrev" type="button" aria-label="Prev"
                    class="absolute left-2 top-1/2 -translate-y-1/2 z-10 h-8 w-8 rounded-full
                           border border-zinc-200 bg-white/95 shadow hover:bg-emerald-50">‹</button>
            <button id="imgNext" type="button" aria-label="Next"
                    class="absolute right-2 top-1/2 -translate-y-1/2 z-10 h-8 w-8 rounded-full
                           border border-zinc-200 bg-white/95 shadow hover:bg-emerald-50">›</button>
          </div>

          {{-- Thumbs --}}
          <div class="mt-3 flex gap-2 overflow-x-auto">
            <div id="thumbs" class="flex gap-2"></div>
          </div>
        </div>

        {{-- Details + CTA --}}
        <div class="relative z-20 space-y-4">

          {{-- Selected model card --}}
          <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm">
            <p class="text-[11px] font-semibold tracking-[0.15em] text-zinc-500 uppercase mb-2">
              step 1 · Selected Model
            </p>

            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="text-sm font-semibold text-zinc-900 truncate" id="modelName">—</div>
                <div class="mt-1 text-xs text-zinc-500" id="modelCapacity">—</div>
              </div>

              <div class="text-right">
                <div class="text-sm font-semibold text-emerald-700" id="modelRate">
                  $0.00 / hr
                </div>
                <p class="mt-1 text-[11px] text-zinc-400">
                  Fuel &amp; operator not included
                </p>
              </div>
            </div>
          </div>

          {{-- Step 2: Availability & Payment --}}
          <div class="rounded-2xl border border-zinc-200 bg-zinc-50/80 p-4 shadow-sm space-y-3">
            <p class="text-[11px] font-semibold tracking-[0.15em] text-zinc-500 uppercase">
              Step 2 · Availability &amp; Payment
            </p>
            <p class="text-xs text-zinc-600">
              First select your model, then continue to choose your date, time, and payment method.
            </p>

            <div class="grid grid-cols-1 gap-2">
              {{-- Select model button --}}
              <button id="selectModelBtn" type="button"
                      class="w-full rounded-xl bg-sky-600 px-4 py-2.5 font-semibold text-white shadow
                             hover:bg-sky-700 hover:shadow-lg hover:shadow-sky-300/40
                             transition-all flex items-center justify-center gap-2">
                <span>Select this model</span>
                <span class="text-base">➜</span>
              </button>

              {{-- Shown after Select --}}
              <a id="checkoutBtn" href="#"
                 class="hidden w-full rounded-xl bg-emerald-600 px-4 py-2.5 font-semibold text-white shadow
                        hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-300/40
                        transition-all">
                <span class="inline-flex items-center justify-center gap-2">
                  <span>Continue to Availability &amp; Payment</span>
                  <span class="text-base">➡️</span>
                </span>
              </a>
            </div>

            <p class="text-[11px] text-zinc-500 text-center">
              You’ll complete your booking on the next screen.
            </p>
          </div>
        </div>
      </div>
      
      
      {{-- Action Buttons --}}
<div class="mt-20 border-t border-zinc-200 bg-zinc-50 py-10">
    <div class="mx-auto max-w-5xl px-6">
        <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-center">

            {{-- Back to Home (Secondary) --}}
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 rounded-xl
                      bg-sky-600 px-6 py-3
                      text-sm font-semibold text-white
                      shadow transition-all
                      hover:bg-sky-700 hover:shadow-md
                      focus:outline-none focus:ring-2
                      focus:ring-sky-400 focus:ring-offset-2">
                {{-- Home Icon --}}
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

    </section>
  </main>

  {{-- Page JS --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const strip       = document.getElementById('modelsStrip');
      const cards       = strip ? Array.from(strip.querySelectorAll('.model-tile')) : [];
      const prevBtn     = document.getElementById('modelsPrev');
      const nextBtn     = document.getElementById('modelsNext');

      const mainImg     = document.getElementById('mainModelImage');
      const thumbsWrap  = document.getElementById('thumbs');
      const nameEl      = document.getElementById('modelName');
      const capEl       = document.getElementById('modelCapacity');
      const rateEl      = document.getElementById('modelRate');

      const selectBtn   = document.getElementById('selectModelBtn');
      const checkoutBtn = document.getElementById('checkoutBtn');
      const checkoutBase= @json(route('checkout.create'));
      const PLACEHOLDER = @json($PLACEHOLDER);
      const BROWSER_TZ  = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';

      if (!strip || !cards.length) {
        // nothing to wire up
        return;
      }

      const payloads = cards.map(card => {
        const t = card.querySelector('template.js-payload');
        return t ? t.dataset : {};
      });

      let selectedIndex   = -1;
      let currentImages   = [];
      let currentImageIdx = 0;

      function renderMain() {
        mainImg.src = currentImages[currentImageIdx] || PLACEHOLDER;
      }

      function renderThumbs() {
        thumbsWrap.innerHTML = '';
        currentImages.forEach((src, i) => {
          const im = document.createElement('img');
          im.src = src || PLACEHOLDER;
          im.alt = 'Thumbnail';
          im.className =
            'h-14 w-20 rounded-lg object-cover border ' +
            (i === currentImageIdx
              ? 'ring-2 ring-sky-400 border-transparent'
              : 'border-zinc-200');
          im.addEventListener('click', () => {
            currentImageIdx = i;
            renderMain();
            renderThumbs();
          });
          thumbsWrap.appendChild(im);
        });
      }

      function setActive(idx) {
        cards.forEach(c => c.dataset.active = 'false');
        const card = cards[idx];
        const data = payloads[idx];
        if (!card || !data) return;

        card.dataset.active = 'true';
        selectedIndex = idx;

        const name = data.name || 'Model';
        const cap  = parseInt(data.capacity || '0', 10);
        const rate = parseFloat(data.rate || '0');

        let imgs;
        try { imgs = JSON.parse(data.images || '[]'); } catch { imgs = []; }
        if (!Array.isArray(imgs) || !imgs.length) imgs = [PLACEHOLDER];
        currentImages   = imgs;
        currentImageIdx = 0;

        nameEl.textContent = name;
        capEl.textContent  = cap ? `Capacity: ${cap} kg` : '—';
        rateEl.textContent = `$${rate.toFixed(2)} / hr`;

        renderMain();
        renderThumbs();

        // Reset checkout button when changing model
        checkoutBtn.classList.add('hidden');
        checkoutBtn.classList.remove('flex', 'items-center', 'justify-center', 'gap-2');
      }

      // clicking a tile selects it
      strip.addEventListener('click', (e) => {
        const tile = e.target.closest('.model-tile');
        if (!tile) return;
        const idx = cards.indexOf(tile);
        if (idx !== -1) setActive(idx);
      });

      // horizontal scroll controls
      nextBtn?.addEventListener('click', () =>
        strip.scrollBy({ left: 320, behavior: 'smooth' })
      );
      prevBtn?.addEventListener('click', () =>
        strip.scrollBy({ left: -320, behavior: 'smooth' })
      );

      // image carousel
      document.getElementById('imgNext').addEventListener('click', () => {
        if (!currentImages.length) return;
        currentImageIdx = (currentImageIdx + 1) % currentImages.length;
        renderMain();
        renderThumbs();
      });

      document.getElementById('imgPrev').addEventListener('click', () => {
        if (!currentImages.length) return;
        currentImageIdx = (currentImageIdx - 1 + currentImages.length) % currentImages.length;
        renderMain();
        renderThumbs();
      });

      // Select model → show Continue button
      selectBtn.addEventListener('click', () => {
        if (selectedIndex === -1) {
          alert('Please select a model from the strip first.');
          return;
        }
        const data = payloads[selectedIndex];
        const u = new URL(checkoutBase, window.location.origin);
        u.searchParams.set('forklift_id', data.id);
        u.searchParams.set('tz', BROWSER_TZ);

        checkoutBtn.href = u.toString();
        checkoutBtn.classList.remove('hidden');
        checkoutBtn.classList.add('flex', 'items-center', 'justify-center', 'gap-2');

        checkoutBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
      });

      // preselect from query string if present
      const params = new URLSearchParams(window.location.search);
      const preId  = params.get('forklift_id');

      let initial = 0;
      if (preId) {
        const found = payloads.findIndex(p => p.id === preId);
        if (found !== -1) initial = found;
      }
      if (cards.length && selectedIndex === -1) setActive(initial);
    });
  </script>
@endsection

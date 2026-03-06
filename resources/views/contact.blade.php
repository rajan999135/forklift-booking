{{-- resources/views/support.blade.php --}}
@php
  // Admin details (replace with real info, or pass $admin from a controller)
  $admin = $admin ?? [
      'name'  => 'Rajan Nanda',
      'phone' => '+1 (306)-351-4149',
      'email' => 'rajankumarnanada3@gmail.com',
  ];

  // Build safe tel/mail/WhatsApp links
  $telHref   = 'tel:' . preg_replace('/[^0-9+]/', '', $admin['phone']);
  $mailHref  = 'mailto:' . $admin['email'];
  $waDigits  = preg_replace('/[^0-9]/', '', $admin['phone']);   // digits only for WhatsApp
  $waLink    = 'https://wa.me/' . $waDigits;

  // Safe route helpers for navbar (will fall back to # if route not defined)
  $homeUrl        = \Illuminate\Support\Facades\Route::has('home')                   ? route('home')                   : url('/');
  $fleetUrl       = \Illuminate\Support\Facades\Route::has('forklifts.index')        ? route('forklifts.index')        : '#';
  $availUrl       = \Illuminate\Support\Facades\Route::has('bookings.availability')  ? route('bookings.availability')  : '#';
  $contactUrl     = \Illuminate\Support\Facades\Route::has('contact')                ? route('contact')                : '#';
  $supportUrl     = \Illuminate\Support\Facades\Route::has('support')                ? route('support')                : '#';
  $myBookingsUrl  = \Illuminate\Support\Facades\Route::has('bookings.mine')          ? route('bookings.mine')          : '#';
  $adminUrl       = \Illuminate\Support\Facades\Route::has('admin.dashboard')        ? route('admin.dashboard')        : '#';

  $isActive = fn(string $pattern) => request()->routeIs($pattern) ? 'text-emerald-700' : 'text-zinc-700';
@endphp

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite(["resources/css/app.css","resources/js/app.js"])
  <title>Contact Support • Forklift Booking</title>
  <!-- Remove this CDN if your app already loads Tailwind via Vite -->
 
</head>
<body class="bg-zinc-50 text-zinc-900">

  {{-- Navbar --}}
  <nav class="w-full bg-white/80 backdrop-blur border-b border-zinc-200 sticky top-0 z-40">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="h-16 flex items-center justify-between">
        <!-- Brand -->
        <a href="{{ $homeUrl }}" class="flex items-center gap-2">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold">FB</span>
          <span class="hidden sm:block text-lg font-semibold text-zinc-900">Forklift Booking</span>
        </a>

        
      <div class="hidden md:flex items-center gap-6">
          <a href="{{ route('bookings.forklifts') }}"
   class="text-sm font-medium
   {{ request()->routeIs('bookings.forklifts')
        ? 'text-emerald-700 font-semibold'
        : 'text-zinc-700 hover:text-emerald-700' }}">
  Models
</a>

        
          <!-- <a href="{{ $availUrl }}"  class="text-sm font-medium hover:text-emerald-700 {{ $isActive('bookings.calendar') }}">Home</a> -->
           <a href="{{ route('home') }}"
   class="text-sm font-medium hover:text-emerald-700 {{ $isActive('home') }}">
    Home
</a>

          @if ($supportUrl !== '#')
            <a href="{{ $supportUrl }}" class="text-sm font-medium hover:text-emerald-700 {{ $isActive('support') }}">Support</a>
          @endif
          @if ($contactUrl !== '#')
            <a href="{{ $contactUrl }}" class="text-sm font-medium hover:text-emerald-700 {{ $isActive('contact') }}">Contact</a>
          @endif
        </div>

        <!-- Auth / User (desktop) -->
        <div class="hidden sm:flex items-center gap-3">
          @auth
            <details class="relative group">
              <summary
                class="flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-3 py-1.5 shadow-sm cursor-pointer select-none hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-semibold">
                  {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                </div>
                <span class="text-zinc-800 font-medium max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                <svg class="h-4 w-4 text-zinc-500 group-open:rotate-180 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </summary>

              <div class="absolute right-0 mt-2 w-56 rounded-xl border border-zinc-200 bg-white p-2 shadow-lg">
                <div class="px-3 py-2 text-xs text-zinc-500">Signed in as</div>
                <div class="px-3 pb-2 text-sm font-medium text-zinc-800 truncate">{{ auth()->user()->email }}</div>
                <div class="my-2 h-px bg-zinc-100"></div>

                <a href="{{ $myBookingsUrl }}" class="flex items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-zinc-50">
                  <svg class="h-4 w-4 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 002-2v-8H3v8a2 2 0 002 2z"/>
                  </svg>
                  My Bookings
                </a>

                @if (optional(auth()->user())->role === 'admin')
                  <a href="{{ $adminUrl }}" class="flex items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-zinc-50">
                    <svg class="h-4 w-4 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317a1 1 0 011.35-.436l.947.474a1 1 0 00.894 0l.947-.474a1 1 0 011.35.436l.474.947a1 1 0 000 .894l-.474.947a1 1 0 000 .894l.474.947a1 1 0 01-.436 1.35l-.947.474a1 1 0 00-.894 0l-.947.474a1 1 0 01-1.35-.436l-.474-.947a1 1 0 000-.894l.474-.947a1 1 0 000-.894l-.474-.947z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Admin
                  </a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                  @csrf
                  <button type="submit" class="w-full text-left rounded-md px-3 py-2 text-sm hover:bg-zinc-50">
                    Log out
                  </button>
                </form>
              </div>
            </details>
          @else
            @if (Route::has('login'))
              <a href="{{ route('login') }}" class="text-sm font-medium text-zinc-700 hover:text-emerald-700">Sign in</a>
            @endif
            @if (Route::has('register'))
              <a href="{{ route('register') }}"
                 class="inline-flex items-center rounded-xl px-3 py-1.5 text-sm bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                Create account
              </a>
            @endif
          @endauth
        </div>

        <!-- Mobile toggle -->
        <button id="mobileMenuBtn" class="md:hidden inline-flex items-center justify-center p-2 rounded-md border border-zinc-200">
          <svg class="h-5 w-5 text-zinc-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>

      <!-- Mobile menu -->
      <div id="mobileMenu" class="md:hidden hidden pb-4">
        <div class="flex flex-col gap-2">
          <a href="{{ $fleetUrl }}"  class="px-2 py-2 rounded-md hover:bg-zinc-50 {{ $isActive('forklifts.*') }}">Models</a>
          <a href="{{ $availUrl }}"  class="px-2 py-2 rounded-md hover:bg-zinc-50 {{ $isActive('bookings.availability') }}">Availability</a>
          @if ($supportUrl !== '#')
            <a href="{{ $supportUrl }}" class="px-2 py-2 rounded-md hover:bg-zinc-50 {{ $isActive('support') }}">Support</a>
          @endif
          @if ($contactUrl !== '#')
            <a href="{{ $contactUrl }}" class="px-2 py-2 rounded-md hover:bg-zinc-50 {{ $isActive('contact') }}">Contact</a>
          @endif

          @auth
            <a href="{{ $myBookingsUrl }}" class="px-2 py-2 rounded-md hover:bg-zinc-50">My Bookings</a>
            @if (optional(auth()->user())->role === 'admin')
              <a href="{{ $adminUrl }}" class="px-2 py-2 rounded-md hover:bg-zinc-50">Admin</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="px-2 py-2 rounded-md hover:bg-zinc-50 text-left">Log out</button>
            </form>
          @else
            @if (Route::has('login'))
              <a href="{{ route('login') }}" class="px-2 py-2 rounded-md hover:bg-zinc-50">Sign in</a>
            @endif
            @if (Route::has('register'))
              <a href="{{ route('register') }}" class="px-2 py-2 rounded-md hover:bg-zinc-50">Create account</a>
            @endif
          @endauth
        </div>
      </div>
    </div>
  </nav>

  <!-- Header -->
  <header class="bg-white border-b border-zinc-200">
    <div class="max-w-xl mx-auto px-4 py-10 text-center">
      <h1 class="text-3xl font-extrabold tracking-tight">Contact Support</h1>
      
      <p class="mt-2 text-zinc-600 text-sm">We’re here to help you with any questions.</p>
    </div>
  </header>

  <!-- Card -->
  <main class="max-w-xl mx-auto px-4 py-10">
    <div class="bg-white shadow-sm border border-zinc-200 rounded-2xl p-6 text-center">

      <!-- Avatar initial -->
      <div class="mx-auto h-20 w-20 flex items-center justify-center rounded-full bg-emerald-100 text-emerald-700 text-2xl font-bold">
        {{ strtoupper(substr($admin['name'], 0, 1)) }}
      </div>

      <h2 class="mt-4 text-xl font-bold text-zinc-900">{{ $admin['name'] }}</h2>
      <p class="text-sm text-zinc-500">Support Admin</p>

      <!-- Actions -->
      <div class="mt-6 space-y-3">
        <!-- Call -->
        <a href="{{ $telHref }}"
           class="flex items-center justify-center gap-2 w-full rounded-lg bg-emerald-600 text-white font-medium py-2.5 hover:bg-emerald-700 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.9.31 1.77.57 2.6a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.48-1.09a2 2 0 0 1 2.11-.45 12.36 12.36 0 0 0 2.6.57A2 2 0 0 1 22 16.92Z"/>
          </svg>
          Call: {{ $admin['phone'] }}
        </a>

        <!-- Email -->
        <a href="{{ $mailHref }}"
           class="flex items-center justify-center gap-2 w-full rounded-lg bg-white border text-zinc-800 font-medium py-2.5 hover:bg-zinc-50 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z"/>
          </svg>
          Email: {{ $admin['email'] }}
        </a>

        <!-- WhatsApp -->
        <a href="{{ $waLink }}" target="_blank" rel="noopener"
           class="flex items-center justify-center gap-2 w-full rounded-lg bg-[#25D366] text-white font-medium py-2.5 hover:brightness-95 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20.52 3.48A11.94 11.94 0 0 0 12.04 0C5.46 0 .11 5.35.11 11.95c0 2.1.55 4.15 1.6 5.96L0 24l6.26-1.67a11.8 11.8 0 0 0 5.79 1.49h.01c6.6 0 11.95-5.35 11.95-11.95 0-3.2-1.25-6.2-3.49-8.39Zm-8.48 18.3h-.01a9.87 9.87 0 0 1-5.02-1.37l-.36-.21-3.72.99.99-3.63-.23-.37a9.9 9.9 0 1 1 8.35 4.59Zm5.67-7.43c-.31-.16-1.83-.9-2.11-1-.28-.1-.48-.16-.68.16-.2.31-.78 1-.95 1.2-.17.2-.35.22-.66.06-.31-.16-1.31-.48-2.5-1.53-.92-.82-1.54-1.83-1.72-2.14-.18-.31-.02-.48.14-.64.15-.15.31-.4.47-.6.16-.2.21-.34.31-.57.1-.23.05-.43-.02-.6-.07-.16-.68-1.64-.93-2.25-.24-.58-.49-.5-.68-.51h-.58c-.2 0-.52.07-.79.37-.27.31-1.04 1.02-1.04 2.47 0 1.45 1.07 2.85 1.22 3.05.15.2 2.11 3.23 5.1 4.53.71.31 1.26.49 1.69.62.71.23 1.35.2 1.86.12.57-.08 1.83-.75 2.09-1.48.26-.73.26-1.36.18-1.48-.08-.12-.28-.2-.59-.36Z"/>
          </svg>
          WhatsApp: Chat now
        </a>
      </div>
    </div>

    <p class="text-center text-xs text-zinc-500 mt-6">
      We usually respond within 1 hour during business hours.
    </p>
  </main>

  <!-- Footer -->
  <footer class="max-w-xl mx-auto px-4 py-10 text-xs text-zinc-500 text-center">
    &copy; <span id="year"></span> Forklift Booking
  </footer>

  <script>
    // Year + mobile menu
    document.getElementById('year').textContent = new Date().getFullYear();
    document.addEventListener('DOMContentLoaded', () => {
      const btn = document.getElementById('mobileMenuBtn');
      const menu = document.getElementById('mobileMenu');
      if (btn && menu) btn.addEventListener('click', () => menu.classList.toggle('hidden'));
    });
  </script>
</body>
</html>

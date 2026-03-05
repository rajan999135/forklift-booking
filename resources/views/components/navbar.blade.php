{{-- resources/views/components/navbar.blade.php --}}
@php
  $homeUrl    = url('/');
  $fleetUrl   = Route::has('home')   ? route('home')   : url('/');
  $contactUrl = Route::has('contact')           ? route('contact')           : url('/contact');
  $mineUrl    = Route::has('bookings.mine')     ? route('bookings.mine')     : url('/bookings/mine');
  $adminUrl   = Route::has('admin.dashboard')   ? route('admin.dashboard')   : url('/admin');
  $loginUrl   = Route::has('login')             ? route('login')             : url('/login');
  $registerUrl= Route::has('register')          ? route('register')          : url('/register');
  $logoutUrl  = Route::has('logout')            ? route('logout')            : '#';
@endphp

<nav class="bg-white border-b border-zinc-200/80 sticky top-0 z-40">
  <div class="max-w-7xl mx-auto px-4">
    <div class="h-14 flex items-center justify-between gap-3">

      {{-- Brand --}}
      <a href="{{ $homeUrl }}" class="flex items-center gap-2 font-semibold tracking-tight">
        <span class="text-xl">🚜</span>
        <span class="text-lg">Forklift Booking</span>
      </a>

      {{-- Desktop menu --}}
      <div class="hidden sm:flex items-center gap-3 text-sm">
        <a href="{{ $fleetUrl }}" class="px-3 py-1.5 rounded-full hover:bg-zinc-50">Home Page </a>
        <a href="{{ $contactUrl }}" class="px-3 py-1.5 rounded-full hover:bg-zinc-50">Contact</a>

        @auth
          <details class="relative group">
            <summary
              class="flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-3 py-1.5 shadow-sm cursor-pointer select-none hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-emerald-400">
              <div class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-semibold">
                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
              </div>
              <span class="text-zinc-800 font-medium truncate max-w-[140px]">{{ auth()->user()->name }}</span>
              <svg class="h-4 w-4 text-zinc-500 group-open:rotate-180 transition" xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                   d="M19 9l-7 7-7-7"/></svg>
            </summary>

            <div class="absolute right-0 mt-2 w-56 rounded-xl border border-zinc-200 bg-white p-2 shadow-lg">
              <div class="px-3 py-2 text-xs text-zinc-500">Signed in as</div>
              <div class="px-3 pb-2 text-sm font-medium text-zinc-800 truncate">{{ auth()->user()->email }}</div>
              <div class="my-2 h-px bg-zinc-100"></div>

              <a href="{{ $mineUrl }}"
                 class="flex items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-zinc-50 {{ request()->url() === $mineUrl ? 'bg-zinc-50' : '' }}">
                <svg class="h-4 w-4 text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round"
                     stroke-linejoin="round" stroke-width="2"
                     d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 002-2v-8H3v8a2 2 0 002 2z"/></svg>
                My Bookings
              </a>

              @if(optional(auth()->user())->role === 'admin')
                <a href="{{ $adminUrl }}" class="flex items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-zinc-50">
                  <svg class="h-4 w-4 text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                       viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                       d="M10.325 4.317a1 1 0 011.35-.436l.947.474a1 1 0 00.894 0l.947-.474a1 1 0 011.35.436l.474.947a1 1 0 000 .894l-.474.947a1 1 0 000 .894l.474.947a1 1 0 01-.436 1.35l-.947.474a1 1 0 00-.894 0l-.947.474a1 1 0 01-1.35-.436l-.474-.947a1 1 0 000-.894l.474-.947a1 1 0 000-.894l-.474-.947z"/>
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                       d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                  Admin
                </a>
              @endif

              <form method="POST" action="{{ $logoutUrl }}" class="mt-1">
                @csrf
                <button class="w-full rounded-md px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Log out</button>
              </form>
            </div>
          </details>
        @else
          <a href="{{ $loginUrl }}" class="px-3 py-1.5 rounded-full hover:bg-zinc-50">Log in</a>
          <a href="{{ $registerUrl }}" class="rounded-full bg-emerald-600 px-4 py-1.5 font-semibold text-white hover:bg-emerald-700">Sign up</a>
        @endauth
      </div>

      {{-- Mobile menu --}}
      <details class="sm:hidden">
        <summary class="inline-flex items-center gap-2 rounded-full border border-zinc-200 px-3 py-1.5 text-sm">
          Menu
          <svg class="h-4 w-4 text-zinc-500 transition group-open:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none"
               viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
               d="M19 9l-7 7-7-7"/></svg>
        </summary>
        <div class="mt-2 space-y-1 rounded-xl border border-zinc-200 bg-white p-2 text-sm">
          <a href="{{ $fleetUrl }}" class="block rounded-md px-3 py-2 hover:bg-zinc-50">Models</a>
          <a href="{{ $contactUrl }}" class="block rounded-md px-3 py-2 hover:bg-zinc-50">Contact</a>
          @auth
            <a href="{{ $mineUrl }}" class="block rounded-md px-3 py-2 hover:bg-zinc-50">My Bookings</a>
            @if(optional(auth()->user())->role === 'admin')
              <a href="{{ $adminUrl }}" class="block rounded-md px-3 py-2 hover:bg-zinc-50">Admin</a>
            @endif
            <form method="POST" action="{{ $logoutUrl }}">
              @csrf
              <button class="w-full rounded-md px-3 py-2 text-left text-red-600 hover:bg-red-50">Log out</button>
            </form>
          @else
            <a href="{{ $loginUrl }}" class="block rounded-md px-3 py-2 hover:bg-zinc-50">Log in</a>
            <a href="{{ $registerUrl }}" class="block rounded-md px-3 py-2 bg-emerald-600 text-white hover:bg-emerald-700">Sign up</a>
          @endauth
        </div>
      </details>

    </div>
  </div>
</nav>

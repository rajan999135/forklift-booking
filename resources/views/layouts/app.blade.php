<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Forklift Booking</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- CSRF for AJAX --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Your Vite assets --}}
  @vite(['resources/css/app.css','resources/js/app.js'])

  {{-- Tailwind CSS via CDN (optional if you already compile Tailwind via Vite) --}}
 

  {{-- FullCalendar via CDN (keep one version app-wide) --}}
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900">

  <!-- NAVBAR -->
  <nav class="sticky top-0 z-40 bg-gradient-to-r from-green-500 via-green-600 to-green-700 text-white shadow">
    <div class="mx-auto max-w-6xl px-4">
      <div class="flex h-14 items-center justify-between">
        <!-- Brand -->
        <a href="{{ url('/') }}" class="flex items-center gap-2 font-semibold tracking-tight">
          <span class="text-lg">🚜</span>
          <span class="text-lg">Forklift Booking</span>
        </a>

        <!-- Desktop actions -->
        <div class="hidden sm:flex items-center gap-3">
          @auth
            <span class="text-white/90">Hi, {{ auth()->user()->name }}</span>

            @if(auth()->user()->role === 'admin')
              <a href="{{ route('admin.dashboard') }}"
                 class="inline-flex items-center px-3 py-1.5 rounded-md bg-white/10 text-white
                        hover:bg-white/20 transition focus:outline-none focus:ring-2 focus:ring-white/60">
                Admin
              </a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit"
                      class="inline-flex items-center px-3 py-1.5 rounded-md bg-white text-green-700
                             font-semibold shadow-sm hover:bg-green-50 transition
                             focus:outline-none focus:ring-2 focus:ring-white/60">
                Logout
              </button>
            </form>
          @else
            <a href="{{ route('login') }}"
               class="inline-flex items-center px-3 py-1.5 rounded-md bg-white text-green-700
                      font-semibold shadow-sm hover:bg-green-50 transition
                      focus:outline-none focus:ring-2 focus:ring-white/60">
              Login
            </a>
            <a href="{{ route('register') }}"
               class="inline-flex items-center px-3 py-1.5 rounded-md bg-white/10 text-white
                      hover:bg-white/20 transition
                      focus:outline-none focus:ring-2 focus:ring-white/60">
              Register
            </a>
          @endauth
        </div>

        <!-- Mobile menu -->
        <details class="sm:hidden relative">
          <summary class="list-none cursor-pointer p-2 rounded-md hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/60">
            <!-- Hamburger -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </summary>

          <div class="absolute right-0 mt-2 w-56 rounded-lg bg-white text-gray-800 shadow-lg p-3">
            @auth
              <div class="px-2 py-1.5 text-sm font-medium">Hi, {{ auth()->user()->name }}</div>

              @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}"
                   class="block px-2 py-2 rounded-md text-sm hover:bg-gray-100">Admin</a>
              @endif

              <form method="POST" action="{{ route('logout') }}" class="px-2 pt-1.5">
                @csrf
                <button type="submit"
                        class="w-full inline-flex items-center justify-center px-3 py-2 rounded-md
                               bg-green-600 text-white font-semibold hover:bg-green-700
                               focus:outline-none focus:ring-2 focus:ring-green-400 transition">
                  Logout
                </button>
              </form>
            @else
              <a href="{{ route('login') }}"
                 class="block px-2 py-2 rounded-md text-sm hover:bg-gray-100">Login</a>
              <a href="{{ route('register') }}"
                 class="block px-2 py-2 rounded-md text-sm hover:bg-gray-100">Register</a>
            @endauth
          </div>
        </details>
      </div>
    </div>
  </nav>

  <!-- MAIN -->
  <main class="mx-auto max-w-6xl p-4 sm:pt-6">
    @if (session('success'))
      <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-800">
        {{ session('success') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-800">
        <ul class="list-disc pl-5 space-y-0.5">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @yield('content')
  </main>

  <!-- FOOTER -->
  <footer class="mx-auto max-w-6xl px-4 py-10 text-xs text-gray-500">
    &copy; {{ date('Y') }} Forklift Booking
  </footer>
</body>
</html>

@extends('layouts.app')

@section('content')
<main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

  {{-- Header --}}
  <section class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <p class="text-xs font-semibold tracking-[0.2em] text-emerald-600 uppercase">Customer Reviews</p>
      <h1 class="mt-1 text-2xl font-semibold text-zinc-900">
        How was your experience with our forklift booking?
      </h1>
      <p class="text-sm text-zinc-500">
        Share quick feedback so new customers know about our service quality.
      </p>
    </div>

    <div class="mt-2 sm:mt-0 flex items-center gap-2 rounded-2xl bg-emerald-50 px-4 py-2 border border-emerald-100">
      @php
        $avg   = round(\App\Models\Review::avg('rating') ?? 0, 1);
        $count = \App\Models\Review::count();
      @endphp
      <span class="text-2xl">⭐</span>
      <div class="leading-tight">
        <p class="text-sm font-semibold text-emerald-800">
          {{ $count ? $avg . ' / 5.0' : 'No rating yet' }}
        </p>
        <p class="text-xs text-emerald-700">
          {{ $count }} review{{ $count === 1 ? '' : 's' }} from customers
        </p>
      </div>
      
    </div>
    

  </section>

  {{-- Form + list --}}
  <section class="grid gap-6 md:grid-cols-[minmax(0,1.1fr)_minmax(0,1.2fr)]">

    {{-- Left: form --}}
    <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4">
      <h2 class="text-base font-semibold text-zinc-900">Leave a review</h2>
      <p class="text-xs text-zinc-500">
        
      @auth
    You are reviewing as <span class="font-medium text-zinc-800">{{ auth()->user()->name }}</span>.
@else
    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-600 rounded-2xl p-8 shadow-2xl">
        <!-- Glow effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-400 to-teal-400 opacity-50 blur-xl animate-pulse"></div>
        
        <div class="relative text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full mb-4 shadow-lg">
                <span class="text-3xl">🔐</span>
            </div>
            
            <h3 class="text-3xl font-extrabold text-white mb-2 drop-shadow-lg">
                Please Sign In First
            </h3>
            
            <p class="text-emerald-50 text-lg mb-6">
                Login to share your valuable feedback and help others!
            </p>
            
            <a href="{{ route('login') }}" 
               class="inline-flex items-center gap-2 bg-white text-emerald-700 font-extrabold text-xl px-10 py-5 rounded-xl shadow-2xl hover:bg-yellow-400 hover:text-emerald-900 hover:scale-110 transform transition-all duration-300 border-4 border-white animate-bounce">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Login to Write Review
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            
            <p class="text-white text-sm mt-4 opacity-90">
                Don't have an account? 
                <a href="{{ route('register') }}" class="underline font-bold hover:text-yellow-300">
                    Sign up here
                </a>
            </p>
        </div>
    </div>
@endauth
      </p>

      @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-800">
          {{ session('success') }}
        </div>
      @endif

      @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700">
          <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('reviews.store') }}" class="space-y-4">
        @csrf

        {{-- Stars --}}
        <div>
          <label class="block text-xs font-semibold text-zinc-700 mb-2">
            Your rating
          </label>
          <div class="flex flex-row-reverse justify-end gap-1">
            @for($i = 5; $i >= 1; $i--)
              <input type="radio" name="rating" id="star-{{ $i }}" value="{{ $i }}"
                     class="peer sr-only" {{ old('rating', 5) == $i ? 'checked' : '' }}>
              <label for="star-{{ $i }}"
                     class="cursor-pointer text-2xl transition-transform peer-checked:scale-110 peer-checked:text-amber-400
                            hover:-translate-y-0.5 hover:text-amber-300">
                ★
              </label>
            @endfor
          </div>
          <p class="mt-1 text-[11px] text-zinc-500">
            5 = excellent, 1 = poor.
          </p>
        </div>

        {{-- Comment --}}
        <div>
          <label for="comment" class="block text-xs font-semibold text-zinc-700 mb-1">
            Short comment <span class="text-zinc-400">(max 50 characters)</span>
          </label>
          <textarea id="comment" name="comment" rows="2" maxlength="50" required
                    class="mt-1 block w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm
                           text-zinc-900 placeholder:text-zinc-400 focus:border-emerald-500 focus:ring-2
                           focus:ring-emerald-500/30">{{ old('comment') }}</textarea>
          <p class="mt-1 text-[11px] text-zinc-500">
            Example: “Friendly staff, smooth pickup, forklift was in great condition.”
          </p>
        </div>

        <button type="submit"
                class="inline-flex w-full items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5
                       text-sm font-semibold text-white shadow hover:bg-emerald-700 hover:shadow-lg
                       hover:shadow-emerald-300/40 transition-all">
          Save review
        </button>
      </form>
    </div>

    {{-- Right: reviews list --}}
    <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4">
      <h2 class="text-base font-semibold text-zinc-900">What customers say</h2>

      @if($reviews->isEmpty())
        <p class="text-sm text-zinc-500">
          No reviews yet. Be the first to share your experience!
        </p>
        
      @else
        <div class="space-y-3 max-h-[480px] overflow-y-auto pr-1">
          @foreach($reviews as $review)
            <article class="rounded-2xl border border-zinc-100 bg-zinc-50 px-3 py-3 flex gap-3">
              {{-- Avatar --}}
              <div class="mt-1 flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 text-sm font-semibold">
                {{ strtoupper(mb_substr($review->user?->name ?? 'U', 0, 1)) }}
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <div class="min-w-0">
                    <p class="text-sm font-semibold text-zinc-900 truncate">
                      {{ $review->user?->name ?? 'Deleted User' }}
                    </p>
                    <p class="text-[11px] text-zinc-500">
                      {{ $review->created_at->format('M d, Y') }}
                    </p>
                  </div>
                  {{-- Stars display --}}
                  <div class="flex items-center gap-0.5 text-amber-400 text-sm">
                    @for($s = 1; $s <= 5; $s++)
                      <span>{{ $s <= $review->rating ? '★' : '☆' }}</span>
                    @endfor
                  </div>
                </div>

                <p class="mt-2 text-sm text-zinc-700">
                  {{ $review->comment }}
                  
                </p>
              </div>
            </article>
          @endforeach
        </div>

        <div class="mt-2">
          {{ $reviews->links() }}
        </div>

        
      @endif
      
    </div>

    

  </section>

  {{-- Page CTA – Back to Home (Full Width Edge-to-Edge) --}}
<div class="mt-12 border-t-2 border-zinc-200 bg-gradient-to-br from-zinc-50 via-white to-emerald-50/30">
    <div class="mx-auto max-w-full px-6 py-16 text-center">
        <div class="max-w-4xl mx-auto">
            <p class="text-xs font-semibold tracking-[0.18em] text-emerald-700 uppercase">
                Navigation
            </p>

            <h3 class="mt-4 text-3xl font-bold text-zinc-900">
                All done with your review?
            </h3>

            <p class="mt-3 text-lg text-zinc-600">
                Head back to the homepage to browse forklifts, manage your bookings,
                or continue exploring our services.
            </p>

            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 rounded-xl
                          bg-emerald-600 px-10 py-5
                          text-lg font-bold text-white
                          shadow-xl shadow-emerald-500/40
                          transition-all duration-300
                          hover:bg-emerald-700 hover:shadow-2xl hover:scale-105
                          focus:outline-none focus:ring-4 focus:ring-emerald-400 focus:ring-offset-2">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4m4 0h4a1 1 0 001-1V10" />
                    </svg>
                    <span>Back to Homepage</span>
                </a>

                <a href="{{ route('bookings.mine') }}"
                   class="inline-flex items-center gap-2 rounded-xl
                          bg-white border-2 border-emerald-600 px-10 py-5
                          text-lg font-bold text-emerald-700
                          shadow-lg
                          transition-all duration-300
                          hover:bg-emerald-50 hover:shadow-xl hover:scale-105
                          focus:outline-none focus:ring-4 focus:ring-emerald-400 focus:ring-offset-2">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 002-2v-8H3v8a2 2 0 002 2z"/>
                    </svg>
                    <span>My Bookings</span>
                </a>
            </div>
        </div>
    </div>
</div>
</main>
@endsection

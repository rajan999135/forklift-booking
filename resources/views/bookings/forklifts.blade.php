<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Fleet - Forklift Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

    {{-- Navigation Bar --}}
    <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="text-3xl">🚜</span>
                    <div>
                        <div class="text-xl font-bold text-gray-900">Forklift Booking</div>
                        <div class="text-xs text-gray-500">Equipment Rental Platform</div>
                    </div>
                </a>

                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">Home</a>
                    <a href="{{ route('bookings.forklifts') }}" class="text-emerald-600 font-semibold">Browse Fleet</a>
                    <a href="{{ route('how') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">How It Works</a>
                    <a href="{{ route('reviews.index') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">Reviews</a>
                    <a href="{{ route('contact') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">Contact</a>
                    
                    @auth
                        <a href="{{ route('bookings.mine') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">My Bookings</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">Login</a>
                        <a href="{{ route('register') }}" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-emerald-700 transition shadow-sm">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Page Header --}}
    <header class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8">
                <div>
                    <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Our Fleet</h1>
                    <p class="text-xl text-emerald-100 max-w-2xl">
                        Browse our complete range of forklifts and equipment. Filter by capacity, location, or features to find the perfect match.
                    </p>
                </div>

                {{-- Search Box --}}
                <div class="w-full md:w-96">
                    <form method="GET" class="relative">
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ request('q') }}"
                            placeholder="Search by model, capacity, location..."
                            class="w-full px-5 py-4 pr-12 rounded-xl border-2 border-white/20 bg-white/10 backdrop-blur text-white placeholder-emerald-200 focus:bg-white focus:text-gray-900 focus:placeholder-gray-400 focus:border-white transition outline-none">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white text-emerald-600 p-2.5 rounded-lg hover:bg-emerald-50 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Stats Bar --}}
            <div class="grid grid-cols-3 gap-6 mt-12 pt-8 border-t border-emerald-400/30">
                <div>
                    <div class="text-3xl font-bold">{{ $forklifts->total() }}+</div>
                    <div class="text-sm text-emerald-100 mt-1">Available Units</div>
                </div>
                <div>
                    <div class="text-3xl font-bold">5+</div>
                    <div class="text-sm text-emerald-100 mt-1">Locations</div>
                </div>
                <div>
                    <div class="text-3xl font-bold">24/7</div>
                    <div class="text-sm text-emerald-100 mt-1">Availability</div>
                </div>
            </div>
        </div>
    </header>

    {{-- Gallery Grid --}}
    <main class="max-w-7xl mx-auto px-6 py-16">
        
        @if(request('q'))
            <div class="mb-8 flex items-center justify-between">
                <p class="text-lg text-gray-600">
                    Showing results for <span class="font-semibold text-gray-900">"{{ request('q') }}"</span>
                </p>
                <a href="{{ route('bookings.forklifts') }}" class="text-emerald-600 hover:text-emerald-700 font-medium">
                    Clear search
                </a>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($forklifts as $f)
                @php
                    $main = $f->image_url ?: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&auto=format&fit=crop';
                    $thumbs = is_array($f->images_urls) ? array_slice($f->images_urls, 1, 3) : [];
                    $chips = [];
                    if (is_string($f->features) && trim($f->features) !== '') {
                        $maybeJson = json_decode($f->features, true);
                        $chips = is_array($maybeJson) ? $maybeJson : array_filter(array_map('trim', explode(',', $f->features)));
                    }
                @endphp

                <article class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100">
                    {{-- Image --}}
                    <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
                        <img 
                            src="{{ $main }}" 
                            alt="{{ $f->name }}"
                            class="w-full h-full object-cover transition duration-500 group-hover:scale-110"
                            loading="lazy">
                        
                        {{-- Price Badge --}}
                        <div class="absolute top-4 right-4 bg-emerald-600 text-white px-4 py-2 rounded-full font-bold text-sm shadow-lg">
                            {{ $f->formatted_hourly_rate }}/hr
                        </div>

                        {{-- Availability Badge --}}
                        <div class="absolute top-4 left-4 bg-white/95 backdrop-blur px-3 py-1.5 rounded-full text-xs font-semibold text-emerald-600 shadow-lg flex items-center gap-1.5">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            Available Now
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-6">
                        {{-- Title & Location --}}
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-emerald-600 transition">
                                {{ $f->name }}
                            </h3>
                            <div class="flex items-center gap-4 text-sm text-gray-600">
                                @if($f->capacity_kg)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                        </svg>
                                        <span class="font-medium">{{ number_format($f->capacity_kg) }} kg</span>
                                    </div>
                                @endif
                                @if(optional($f->location)->name)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $f->location->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Features --}}
                        @if(count($chips))
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach(array_slice($chips, 0, 4) as $chip)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                                        {{ $chip }}
                                    </span>
                                @endforeach
                                @if(count($chips) > 4)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-medium">
                                        +{{ count($chips) - 4 }} more
                                    </span>
                                @endif
                            </div>
                        @endif

                        {{-- Thumbnail Gallery --}}
                        @if(count($thumbs))
                            <div class="flex gap-2 mb-4">
                                @foreach($thumbs as $thumb)
                                    <div class="w-20 h-16 rounded-lg overflow-hidden border-2 border-gray-200 hover:border-emerald-500 transition cursor-pointer">
                                        <img src="{{ $thumb }}" alt="Gallery" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Action Button --}}
                        <a href="{{ route('bookings.create', ['forklift' => $f->id]) }}" 
                           class="block w-full text-center bg-emerald-600 text-white font-bold py-3 rounded-xl hover:bg-emerald-700 transition shadow-md hover:shadow-lg">
                            Book Now
                        </a>
                    </div>
                </article>

            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-2xl border-2 border-dashed border-gray-300 p-16 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">No Forklifts Found</h3>
                        <p class="text-gray-600 mb-6">Try adjusting your search or browse all available equipment</p>
                        @if(request('q'))
                            <a href="{{ route('bookings.forklifts') }}" class="inline-flex items-center bg-emerald-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-emerald-700 transition">
                                View All Equipment
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($forklifts->hasPages())
            <div class="mt-12">
                {{ $forklifts->onEachSide(1)->links() }}
            </div>
        @endif
    </main>

    {{-- CTA Section --}}
    <section class="bg-gradient-to-br from-emerald-600 to-teal-700 py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="bg-white/10 backdrop-blur rounded-2xl p-8 md:p-12 border border-white/20">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="text-white">
                        <h3 class="text-3xl font-bold mb-2">Ready to Book Your Equipment?</h3>
                        <p class="text-emerald-100 text-lg">Create your booking now and get instant confirmation</p>
                    </div>
                    <a href="{{ route('bookings.create') }}" 
                       class="inline-flex items-center gap-2 bg-white text-emerald-700 px-8 py-4 rounded-xl font-bold text-lg hover:bg-emerald-50 transition shadow-xl hover:shadow-2xl whitespace-nowrap">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Booking
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-2xl">🚜</span>
                        <span class="text-xl font-bold">Forklift Booking</span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Professional equipment rental platform for warehouses and industrial operations.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('bookings.forklifts') }}" class="hover:text-white transition">Browse Fleet</a></li>
                        <li><a href="{{ route('how') }}" class="hover:text-white transition">How It Works</a></li>
                        <li><a href="{{ route('reviews.index') }}" class="hover:text-white transition">Reviews</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">Contact Us</a></li>
                        <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition">FAQs</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Contact</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li>📧 support@forkliftbooking.com</li>
                        <li>📞 1-800-FORKLIFT</li>
                        <li>📍 Industrial District</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-400">
                <p>© <span id="year"></span> Forklift Booking. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>

</body>
</html>
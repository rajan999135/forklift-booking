<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forklift Booking - Professional Equipment Rental Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

    {{-- Navigation Bar --}}
    <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-20">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="text-3xl">🚜</span>
                    <div>
                        <div class="text-xl font-bold text-gray-900">Forklift Booking</div>
                        <div class="text-xs text-gray-500">Equipment Rental Platform</div>
                    </div>
                </a>

                {{-- Desktop Navigation --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">Home</a>
                    <a href="{{ route('bookings.forklifts') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">Browse Fleet</a>
                    <a href="{{ route('how') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">How It Works</a>
                    <a href="{{ route('reviews.index') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">Reviews</a>
                    <a href="{{ route('contact') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">Contact</a>
                    
                    @auth
                        {{-- User Dropdown --}}
                        <div class="relative group">
                            <button class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                                <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-semibold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-900">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            {{-- Dropdown Menu --}}
                            <div class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <div class="p-4 border-b border-gray-100">
                                    <p class="text-xs text-gray-500">Signed in as</p>
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="p-2">
                                    <a href="{{ route('bookings.mine') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-50 transition">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 002-2v-8H3v8a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900">My Bookings</span>
                                    </a>
                                    @if(auth()->user()->role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-50 transition">
                                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-900">Admin Panel</span>
                                        </a>
                                    @endif
                                </div>
                                <div class="p-2 border-t border-gray-100">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            <span class="text-sm font-semibold">Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">Login</a>
                        <a href="{{ route('register') }}" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-emerald-700 transition shadow-sm">
                            Get Started
                        </a>
                    @endauth
                </div>

                {{-- Mobile Menu Button --}}
                <button class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    {{-- Hero Section - FIXED --}}
    <section class="bg-gradient-to-br from-white via-emerald-50/30 to-teal-50/30 py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-block px-4 py-2 bg-emerald-100 text-emerald-700 rounded-full text-sm font-semibold mb-6">
                        🚀 Professional Equipment Rental Platform
                    </div>
                    <h1 class="text-5xl md:text-6xl font-extrabold text-gray-900 mb-6 leading-tight">
                        Book Forklifts Fast, Stay On Schedule
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Streamline your warehouse operations with our easy-to-use forklift booking system. Real-time availability, instant confirmation, and 24/7 access.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('bookings.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-emerald-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Booking
                        </a>
                        <a href="{{ route('how') }}" class="inline-flex items-center gap-2 bg-white border-2 border-gray-300 text-gray-900 px-8 py-4 rounded-xl font-bold text-lg hover:border-emerald-600 hover:text-emerald-600 transition shadow-sm hover:shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            How It Works
                        </a>
                    </div>

                    {{-- Stats - Below buttons --}}
                    <div class="grid grid-cols-3 gap-8 mt-12 pt-12 border-t border-gray-200">
                        <div>
                            <div class="text-4xl font-bold text-emerald-600">500+</div>
                            <div class="text-sm text-gray-600 mt-1">Active Users</div>
                        </div>
                        <div>
                            <div class="text-4xl font-bold text-emerald-600">1000+</div>
                            <div class="text-sm text-gray-600 mt-1">Bookings/Month</div>
                        </div>
                        <div>
                            <div class="text-4xl font-bold text-emerald-600">99%</div>
                            <div class="text-sm text-gray-600 mt-1">Satisfaction</div>
                        </div>
                    </div>
                </div>

                {{-- Hero Image - CLEAN, NO OVERLAPPING --}}
                <div class="relative">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl border-4 border-white">
                        <img src="{{ asset('images/forklift.avif') }}" 
                             alt="Warehouse with forklifts" 
                             class="w-full h-full object-cover"
                             onerror="this.src='https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&auto=format&fit=crop'">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section - CLEAN CARDS --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">What Do You Need?</h2>
                <p class="text-xl text-gray-600">Jump straight to the most common actions</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                {{-- Card 1: Create Booking --}}
                <a href="{{ route('bookings.create') }}" class="group bg-white rounded-2xl border-2 border-gray-200 overflow-hidden hover:border-emerald-500 hover:shadow-xl transition-all duration-300">
                    <div class="h-56 bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-emerald-600 transition">Create Booking</h3>
                        <p class="text-gray-600">Pick date/time, assign equipment, and add notes in minutes</p>
                    </div>
                </a>

                {{-- Card 2: How It Works --}}
                <a href="{{ route('how') }}" class="group bg-white rounded-2xl border-2 border-gray-200 overflow-hidden hover:border-emerald-500 hover:shadow-xl transition-all duration-300">
                    <div class="h-56 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-emerald-600 transition">⭐ How It Works</h3>
                        <p class="text-gray-600">Learn the simple steps to get started with our platform</p>
                    </div>
                </a>

                {{-- Card 3: My Bookings --}}
                @auth
                    <a href="{{ route('bookings.mine') }}" class="group bg-white rounded-2xl border-2 border-gray-200 overflow-hidden hover:border-emerald-500 hover:shadow-xl transition-all duration-300">
                @else
                    <a href="{{ route('login') }}" class="group bg-white rounded-2xl border-2 border-gray-200 overflow-hidden hover:border-emerald-500 hover:shadow-xl transition-all duration-300">
                @endauth
                    <div class="h-56 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-12 h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 002-2v-8H3v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-emerald-600 transition">My Bookings</h3>
                        @auth
                            <p class="text-gray-600">Manage, edit, or cancel your equipment requests</p>
                        @else
                            <p class="text-gray-600">Sign in to view and manage your bookings</p>
                        @endauth
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-20 bg-gradient-to-br from-emerald-600 to-teal-700">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-white">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <h3 class="text-3xl font-bold mb-2">Admin Controls</h3>
                            <p class="text-emerald-100 text-lg">Manage bookings, maintenance windows, users, and system logs</p>
                        @else
                            <h3 class="text-3xl font-bold mb-2">Need Help or Support?</h3>
                            <p class="text-emerald-100 text-lg">Contact our support team for assistance with bookings or questions</p>
                        @endif
                    @else
                        <h3 class="text-3xl font-bold mb-2">Ready to Get Started?</h3>
                        <p class="text-emerald-100 text-lg">Sign in to access your bookings and account features</p>
                    @endauth
                </div>

                <div>
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center bg-white text-emerald-700 px-8 py-4 rounded-xl font-bold text-lg hover:bg-emerald-50 transition shadow-lg">
                                Go to Admin Panel →
                            </a>
                        @else
                            <a href="{{ route('contact') }}" class="inline-flex items-center bg-white text-emerald-700 px-8 py-4 rounded-xl font-bold text-lg hover:bg-emerald-50 transition shadow-lg">
                                Contact Support →
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center bg-white text-emerald-700 px-8 py-4 rounded-xl font-bold text-lg hover:bg-emerald-50 transition shadow-lg">
                            Sign In Now →
                        </a>
                    @endauth
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
                        <li>📧 rajannanda123456@gmail.com</li>
                        <li>📞 306-351-4149</li>
                        <li>📍 Regina, Saskatchewan, CA, S4N 5C1</li>
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
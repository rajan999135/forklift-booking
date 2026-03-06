<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(["resources/css/app.css","resources/js/app.js"])
    <title>Login - Forklift Booking</title>
   
</head>
<body class="bg-gray-50">

    {{-- Navigation Bar --}}
    <nav class="bg-white border-b border-gray-200 shadow-sm">
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

                {{-- Navigation Links --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-emerald-600 font-medium transition">Home</a>
                    <a href="{{ route('bookings.forklifts') }}" class="text-gray-600 hover:text-emerald-600 font-medium transition">Browse Fleet</a>
                    <a href="{{ route('how') }}" class="text-gray-600 hover:text-emerald-600 font-medium transition">How It Works</a>
                    <a href="{{ route('contact') }}" class="text-gray-600 hover:text-emerald-600 font-medium transition">Contact</a>
                    <a href="{{ route('register') }}" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-emerald-700 transition shadow-sm">
                        Sign Up
                    </a>
                </div>

                {{-- Mobile Menu Button --}}
                <button class="md:hidden p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="py-16 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                
                {{-- Left Side - Welcome Message & Image --}}
                <div class="hidden lg:block">
                    <div class="mb-8">
                        <h2 class="text-4xl font-bold text-gray-900 mb-4">
                            Welcome Back!
                        </h2>
                        <p class="text-xl text-gray-600 mb-6">
                            Sign in to manage your forklift bookings and equipment rentals.
                        </p>
                    </div>

                    {{-- Illustration --}}
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-12 flex items-center justify-center">
                        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.svg" 
                             alt="Login illustration" 
                             class="w-full max-w-md">
                    </div>

                    {{-- Benefits --}}
                    <div class="mt-8 grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 text-sm">Quick Booking</div>
                                <div class="text-xs text-gray-500">Reserve in minutes</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 text-sm">24/7 Access</div>
                                <div class="text-xs text-gray-500">Manage anytime</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side - Login Form --}}
                <div>
                    {{-- Session Status --}}
                    @if (session('status'))
                        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8 md:p-12">
                        <div class="mb-8">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">Sign in to your account</h1>
                            <p class="text-gray-600">Access your dashboard and manage bookings</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}" class="space-y-6">
                            @csrf

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-bold text-gray-900 mb-2">
                                    Email address
                                </label>
                                <input id="email" 
                                       type="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       required 
                                       autofocus
                                       autocomplete="email"
                                       class="w-full px-4 py-3.5 text-base rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition outline-none"
                                       placeholder="your.email@company.com">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="password" class="block text-sm font-bold text-gray-900 mb-2">
                                    Password
                                </label>
                                <input id="password" 
                                       type="password" 
                                       name="password" 
                                       required
                                       autocomplete="current-password"
                                       class="w-full px-4 py-3.5 text-base rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition outline-none"
                                       placeholder="Enter your password">
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Remember Me & Forgot Password --}}
                            <div class="flex items-center justify-between">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="remember" 
                                           id="remember"
                                           {{ old('remember') ? 'checked' : '' }}
                                           class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                    <span class="ml-2 text-sm text-gray-700">Remember me</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">
                                        Forgot password?
                                    </a>
                                @endif
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit"
                                    class="w-full bg-emerald-600 text-white font-bold text-base py-4 rounded-lg hover:bg-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                Sign in
                            </button>

                            {{-- Don't have account --}}
                            <p class="text-center text-sm text-gray-600">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="text-emerald-600 font-semibold hover:text-emerald-700">
                                    Sign up
                                </a>
                            </p>

                            {{-- Divider --}}
                            <div class="relative my-8">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-200"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-4 bg-white text-gray-500">or continue with</span>
                                </div>
                            </div>

                            {{-- Google OAuth --}}
                            <a href="{{ route('oauth.redirect', 'google') }}"
                               class="flex items-center justify-center gap-3 w-full py-4 px-6 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                                <svg class="w-5 h-5" viewBox="0 0 48 48">
                                    <path fill="#FFC107" d="M44.5 20H24v8.5h11.8C34.9 33.9 30.1 37 24 37c-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.2-6.2C34.6 4.2 29.6 2 24 2 11.8 2 2 11.8 2 24s9.8 22 22 22c12.7 0 21.5-8.9 21.5-21.5 0-1.4-.1-2.5-.3-3.5z"/>
                                    <path fill="#FF3D00" d="M6.3 14.7l7 5.1C15.4 16.2 19.3 13 24 13c3.1 0 5.9 1.1 8.1 2.9l6.2-6.2C34.6 4.2 29.6 2 24 2 16 2 9.1 6.1 6.3 14.7z"/>
                                    <path fill="#4CAF50" d="M24 46c6 0 11-2 14.7-5.5l-6.8-5.6C29.8 36.9 27.1 38 24 38c-6.1 0-10.9-4.1-12.7-9.6l-7 5.4C6.9 41.6 14.7 46 24 46z"/>
                                    <path fill="#1976D2" d="M44.5 20H24v8.5h11.8C35.5 31.9 30.8 34.9 24 34.9"/>
                                </svg>
                                Continue with Google
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-white mt-20">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="grid md:grid-cols-3 gap-8">
                
                {{-- Company Info --}}
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-2xl">🚜</span>
                        <span class="text-xl font-bold">Forklift Booking</span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Professional forklift rental and equipment management platform for warehouses and industrial operations.
                    </p>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h3 class="font-bold text-lg mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('bookings.forklifts') }}" class="text-gray-400 hover:text-white transition">Browse Fleet</a></li>
                        <li><a href="{{ route('how') }}" class="text-gray-400 hover:text-white transition">How It Works</a></li>
                        <li><a href="{{ route('reviews.index') }}" class="text-gray-400 hover:text-white transition">Customer Reviews</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition">Contact Us</a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h3 class="font-bold text-lg mb-4">Get in Touch</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li>📧 support@forkliftbooking.com</li>
                        <li>📞 1-800-FORKLIFT</li>
                        <li>📍 123 Warehouse District, Industrial City</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>© {{ date('Y') }} Forklift Booking. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
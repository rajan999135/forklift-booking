<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password – Forklift Booking</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f4f3;
            min-height: 100vh;
            color: #1a2e1a;
        }

        /* ── Navbar ── */
        .navbar {
            background: #fff;
            border-bottom: 1px solid #e4ebe4;
            padding: 0 40px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }

        .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }

        .brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }

        .brand-text strong { display: block; font-size: 0.95rem; font-weight: 800; color: #111827; line-height: 1.1; }
        .brand-text span { font-size: 0.7rem; color: #6b7280; font-weight: 400; }

        .nav-links { display: flex; align-items: center; gap: 32px; list-style: none; }

        .nav-links a {
            text-decoration: none; font-size: 0.875rem; font-weight: 500;
            color: #374151; transition: color 0.15s;
        }
        .nav-links a:hover { color: #16a34a; }

        .nav-cta {
            background: #16a34a; color: #fff !important;
            padding: 9px 22px; border-radius: 8px;
            font-weight: 600 !important;
            transition: background 0.15s, transform 0.15s !important;
        }
        .nav-cta:hover { background: #15803d !important; transform: translateY(-1px); }

        /* ── Page layout ── */
        .page-body {
            min-height: calc(100vh - 70px);
            display: flex; align-items: center; justify-content: center;
            padding: 48px 20px;
        }

        .split-layout {
            display: grid;
            grid-template-columns: 1fr 460px;
            gap: 56px;
            max-width: 960px;
            width: 100%;
            align-items: center;
        }

        /* ── Left ── */
        .left-panel { animation: slideIn 0.55s cubic-bezier(0.16,1,0.3,1) both; }
        @keyframes slideIn { from { opacity:0; transform:translateX(-20px); } to { opacity:1; transform:translateX(0); } }

        .left-panel h2 { font-size: 2.25rem; font-weight: 800; color: #111827; line-height: 1.2; margin-bottom: 12px; }
        .left-panel > p { font-size: 0.93rem; color: #6b7280; line-height: 1.6; margin-bottom: 32px; max-width: 340px; }

        .steps-list { display: flex; flex-direction: column; gap: 18px; }
        .step-item { display: flex; align-items: flex-start; gap: 14px; }

        .step-num {
            width: 32px; height: 32px; background: #16a34a;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 700; color: #fff;
            flex-shrink: 0; margin-top: 1px;
        }

        .step-text strong { display: block; font-size: 0.85rem; font-weight: 700; color: #111827; margin-bottom: 2px; }
        .step-text span { font-size: 0.78rem; color: #9ca3af; line-height: 1.4; }

        /* ── Auth Card ── */
        .auth-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 40px 36px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            animation: riseUp 0.55s cubic-bezier(0.16,1,0.3,1) 0.1s both;
        }
        @keyframes riseUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }

        .card-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #f0fdf4; border: 1px solid #bbf7d0;
            border-radius: 99px; padding: 4px 12px;
            font-size: 0.72rem; font-weight: 600; color: #16a34a;
            text-transform: uppercase; letter-spacing: 0.06em;
            margin-bottom: 20px;
        }
        .card-badge svg { width: 12px; height: 12px; }

        .auth-card h3 { font-size: 1.5rem; font-weight: 800; color: #111827; margin-bottom: 6px; }
        .card-sub { font-size: 0.83rem; color: #9ca3af; margin-bottom: 28px; line-height: 1.5; }

        .status-banner {
            background: #f0fdf4; border: 1px solid #bbf7d0;
            border-radius: 10px; padding: 12px 16px;
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 20px; font-size: 0.82rem; font-weight: 500; color: #166534;
        }
        .status-banner svg { width: 16px; height: 16px; color: #16a34a; flex-shrink: 0; }

        .field-group { margin-bottom: 18px; }
        .field-group label { display: block; font-size: 0.78rem; font-weight: 600; color: #374151; margin-bottom: 7px; }

        .field-group input {
            width: 100%; border: 1.5px solid #d1d5db; border-radius: 10px;
            padding: 11px 14px; font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.875rem; color: #111827; background: #fafafa;
            outline: none; transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
        }
        .field-group input::placeholder { color: #c4c9d1; }
        .field-group input:focus { border-color: #16a34a; background: #fff; box-shadow: 0 0 0 3px rgba(22,163,74,0.1); }

        .error-text { font-size: 0.72rem; color: #dc2626; margin-top: 5px; }

        .btn-submit {
            width: 100%; padding: 13px;
            background: #16a34a; border: none; border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.875rem; font-weight: 700; color: #fff;
            cursor: pointer; transition: background 0.15s, transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 2px 8px rgba(22,163,74,0.3); margin-top: 4px;
        }
        .btn-submit:hover { background: #15803d; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(22,163,74,0.35); }
        .btn-submit:active { transform: translateY(0); }

        .card-divider { height: 1px; background: #f3f4f6; margin: 24px 0; }

        .back-link {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            text-decoration: none; font-size: 0.82rem; font-weight: 500;
            color: #6b7280; transition: color 0.15s;
        }
        .back-link:hover { color: #16a34a; }
        .back-link svg { width: 14px; height: 14px; }

        @media (max-width: 768px) {
            .split-layout { grid-template-columns: 1fr; }
            .left-panel { display: none; }
            .navbar { padding: 0 20px; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <a href="{{ url('/') }}" class="nav-brand">
        <div class="brand-icon">🚜</div>
        <div class="brand-text">
            <strong>Forklift Booking</strong>
            <span>Equipment Rental Platform</span>
        </div>
    </a>
    <ul class="nav-links">
       <a href="{{ route('home') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">Home</a>
                    <a href="{{ route('bookings.forklifts') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">Browse Fleet</a>
                    <a href="{{ route('how') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">How It Works</a>
                    <a href="{{ route('reviews.index') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">Reviews</a>
                    <a href="{{ route('contact') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">Contact</a>
        <li><a href="{{ route('register') }}" class="nav-cta">Sign Up</a></li>
    </ul>
</nav>

<!-- Page Body -->
<div class="page-body">
    <div class="split-layout">

        <!-- Left Panel -->
        <div class="left-panel">
            <h2>Forgot your<br>password?</h2>
            <p>No worries — it happens. Follow these simple steps to recover access to your account.</p>
            <div class="steps-list">
                <div class="step-item">
                    <div class="step-num">1</div>
                    <div class="step-text">
                        <strong>Enter your email</strong>
                        <span>Provide the address linked to your Forklift Booking account.</span>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div class="step-text">
                        <strong>Check your inbox</strong>
                        <span>We'll send a secure password reset link directly to your email.</span>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <div class="step-text">
                        <strong>Create a new password</strong>
                        <span>Click the link and choose a strong new password to regain access.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auth Card -->
        <div class="auth-card">
            <div class="card-badge">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                </svg>
                Account Recovery
            </div>
            <h3>Reset your password</h3>
            <p class="card-sub">Enter your registered email and we'll send you a secure reset link.</p>

            @if (session('status'))
                <div class="status-banner">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="field-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                        value="{{ old('email') }}"
                        placeholder="your.email@company.com"
                        required autofocus autocomplete="email" />
                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn-submit">Send Reset Link</button>
            </form>

            <div class="card-divider"></div>

            <a href="{{ route('login') }}" class="back-link">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Back to Sign In
            </a>
        </div>

    </div>
</div>

</body>
</html>

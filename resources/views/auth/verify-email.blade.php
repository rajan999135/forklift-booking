<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Verified – Forklift Booking</title>
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

        .navbar {
            background: #fff;
            border-bottom: 1px solid #e4ebe4;
            padding: 0 40px; height: 70px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px;
        }
        .brand-text strong { display: block; font-size: 0.95rem; font-weight: 800; color: #111827; line-height: 1.1; }
        .brand-text span { font-size: 0.7rem; color: #6b7280; }

        .page-body {
            min-height: calc(100vh - 70px);
            display: flex; align-items: center; justify-content: center;
            padding: 48px 20px;
        }

        .success-wrapper {
            width: 100%; max-width: 480px;
            text-align: center;
        }

        /* ── Animated checkmark ── */
        .check-circle {
            width: 100px; height: 100px;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
            animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
            box-shadow: 0 12px 40px rgba(22,163,74,0.4);
        }

        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        .check-circle svg {
            width: 48px; height: 48px; color: #fff;
            animation: drawCheck 0.4s ease 0.3s both;
        }

        @keyframes drawCheck {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }

        /* ── Confetti dots ── */
        .confetti { position: relative; margin-bottom: 8px; }
        .dot {
            position: absolute;
            border-radius: 50%;
            animation: flyOut 0.8s ease-out both;
        }
        .dot-1 { width:10px; height:10px; background:#22c55e; top:-20px; left:50%; margin-left:-60px; animation-delay:0.2s; }
        .dot-2 { width:8px; height:8px; background:#f59e0b; top:-30px; left:50%; margin-left:40px; animation-delay:0.25s; }
        .dot-3 { width:6px; height:6px; background:#3b82f6; top:-10px; left:50%; margin-left:70px; animation-delay:0.3s; }
        .dot-4 { width:8px; height:8px; background:#ec4899; top:-25px; left:50%; margin-left:-80px; animation-delay:0.35s; }
        .dot-5 { width:6px; height:6px; background:#8b5cf6; top:-35px; left:50%; animation-delay:0.28s; }

        @keyframes flyOut {
            from { transform: translate(0,0) scale(0); opacity: 1; }
            to { transform: translate(var(--tx,0), -40px) scale(1); opacity: 0; }
        }

        /* ── Card ── */
        .success-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 40px 36px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            animation: riseUp 0.55s cubic-bezier(0.16,1,0.3,1) 0.15s both;
        }

        @keyframes riseUp {
            from { opacity:0; transform:translateY(20px); }
            to { opacity:1; transform:translateY(0); }
        }

        .verified-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #f0fdf4; border: 1px solid #bbf7d0;
            border-radius: 99px; padding: 4px 14px;
            font-size: 0.72rem; font-weight: 700; color: #16a34a;
            text-transform: uppercase; letter-spacing: 0.08em;
            margin-bottom: 16px;
        }
        .verified-badge svg { width: 12px; height: 12px; }

        .success-card h2 {
            font-size: 1.75rem; font-weight: 800; color: #111827;
            margin-bottom: 10px; line-height: 1.2;
        }

        .success-card p {
            font-size: 0.875rem; color: #6b7280; line-height: 1.6;
            margin-bottom: 28px;
        }

        /* ── Steps all done ── */
        .steps-bar {
            display: flex; align-items: center; justify-content: center;
            gap: 0; margin-bottom: 28px;
            background: #f9fafb; border-radius: 12px; padding: 16px;
        }

        .step-dot { display: flex; flex-direction: column; align-items: center; gap: 6px; flex: 1; }

        .dot-circle {
            width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.72rem; font-weight: 700;
        }
        .dot-circle.done { background: #16a34a; color: #fff; }

        .dot-label { font-size: 0.68rem; font-weight: 600; color: #16a34a; text-align: center; line-height: 1.2; }
        .step-line { flex: 1; max-width: 40px; height: 2px; background: #16a34a; margin-bottom: 20px; }

        /* ── CTA button ── */
        .btn-dashboard {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 14px;
            background: #16a34a; border: none; border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.9rem; font-weight: 700; color: #fff;
            text-decoration: none;
            transition: background 0.15s, transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 2px 8px rgba(22,163,74,0.3);
        }
        .btn-dashboard svg { width: 18px; height: 18px; }
        .btn-dashboard:hover { background: #15803d; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(22,163,74,0.35); }

        /* ── Auto redirect countdown ── */
        .redirect-note {
            margin-top: 16px;
            font-size: 0.78rem; color: #9ca3af;
        }

        .redirect-note span {
            font-weight: 700; color: #16a34a;
        }

        @media (max-width: 600px) {
            .navbar { padding: 0 20px; }
            .success-card { padding: 32px 24px; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="{{ url('/') }}" class="nav-brand">
        <div class="brand-icon">🚜</div>
        <div class="brand-text">
            <strong>Forklift Booking</strong>
            <span>Equipment Rental Platform</span>
        </div>
    </a>
</nav>

<div class="page-body">
    <div class="success-wrapper">

        <!-- Animated check -->
        <div class="confetti">
            <div class="dot dot-1"></div>
            <div class="dot dot-2"></div>
            <div class="dot dot-3"></div>
            <div class="dot dot-4"></div>
            <div class="dot dot-5"></div>
            <div class="check-circle">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
            </div>
        </div>

        <!-- Card -->
        <div class="success-card">
            <div class="verified-badge">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Email Verified
            </div>

            <h2>Your account is<br>now activated! 🎉</h2>
            <p>Your email has been successfully verified. You can now access your dashboard and start booking forklift equipment.</p>

            <!-- All steps done -->
            <div class="steps-bar">
                <div class="step-dot">
                    <div class="dot-circle done">
                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    </div>
                    <span class="dot-label">Account<br>Created</span>
                </div>
                <div class="step-line"></div>
                <div class="step-dot">
                    <div class="dot-circle done">
                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    </div>
                    <span class="dot-label">Email<br>Verified</span>
                </div>
                <div class="step-line"></div>
                <div class="step-dot">
                    <div class="dot-circle done">
                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    </div>
                    <span class="dot-label">Start<br>Booking</span>
                </div>
            </div>

            <a href="{{ route('dashboard') }}" class="btn-dashboard">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                </svg>
                Go to Dashboard
            </a>

            <p class="redirect-note">
                Redirecting automatically in <span id="countdown">5</span> seconds...
            </p>
        </div>

    </div>
</div>

<script>
    // Auto redirect countdown
    let seconds = 5;
    const el = document.getElementById('countdown');
    const timer = setInterval(() => {
        seconds--;
        el.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(timer);
            window.location.href = "{{ route('dashboard') }}";
        }
    }, 1000);
</script>

</body>
</html>

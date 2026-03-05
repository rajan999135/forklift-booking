<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify OTP – Forklift Booking</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f0f4f3; min-height: 100vh; }

        .navbar {
            background: #fff; border-bottom: 1px solid #e4ebe4;
            padding: 0 40px; height: 70px;
            display: flex; align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .brand-icon { width: 38px; height: 38px; background: linear-gradient(135deg, #16a34a, #22c55e); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .brand-text strong { display: block; font-size: 0.95rem; font-weight: 800; color: #111827; }
        .brand-text span { font-size: 0.7rem; color: #6b7280; }

        .page-body { min-height: calc(100vh - 70px); display: flex; align-items: center; justify-content: center; padding: 40px 20px; }

        .card {
            background: #fff; border: 1px solid #e5e7eb;
            border-radius: 20px; padding: 40px 36px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            width: 100%; max-width: 420px;
            animation: riseUp 0.5s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes riseUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

        .icon-wrap {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            border-radius: 18px; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 8px 24px rgba(22,163,74,0.3);
        }
        .icon-wrap svg { width: 30px; height: 30px; color: #fff; }

        h2 { font-size: 1.5rem; font-weight: 800; color: #111827; text-align: center; margin-bottom: 8px; }
        .subtitle { font-size: 0.83rem; color: #6b7280; text-align: center; line-height: 1.5; margin-bottom: 8px; }
        .email-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #f0fdf4; border: 1px solid #bbf7d0;
            border-radius: 99px; padding: 4px 12px;
            font-size: 0.75rem; font-weight: 600; color: #16a34a;
            margin: 0 auto 28px; display: block; text-align: center;
        }

        /* Success banner */
        .success-banner {
            background: #f0fdf4; border: 1px solid #bbf7d0;
            border-radius: 10px; padding: 11px 14px;
            display: flex; align-items: center; gap: 8px;
            font-size: 0.8rem; font-weight: 500; color: #166534;
            margin-bottom: 20px;
        }
        .success-banner svg { width: 16px; height: 16px; flex-shrink: 0; }

        /* Error */
        .error-banner {
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 10px; padding: 11px 14px;
            display: flex; align-items: center; gap: 8px;
            font-size: 0.8rem; font-weight: 500; color: #dc2626;
            margin-bottom: 20px;
        }
        .error-banner svg { width: 16px; height: 16px; flex-shrink: 0; }

        /* OTP inputs */
        .otp-label { font-size: 0.78rem; font-weight: 600; color: #374151; margin-bottom: 12px; display: block; }

        .otp-inputs {
            display: flex; gap: 10px; justify-content: center;
            margin-bottom: 24px;
        }

        .otp-inputs input {
            width: 52px; height: 58px;
            border: 2px solid #d1d5db; border-radius: 12px;
            text-align: center; font-size: 1.4rem; font-weight: 800;
            color: #111827; background: #fafafa;
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
        }

        .otp-inputs input:focus {
            border-color: #16a34a; background: #fff;
            box-shadow: 0 0 0 3px rgba(22,163,74,0.1);
        }

        .otp-inputs input.filled {
            border-color: #16a34a; background: #f0fdf4; color: #16a34a;
        }

        .otp-inputs input.error-input {
            border-color: #dc2626; background: #fef2f2;
        }

        /* Timer */
        .timer {
            text-align: center; font-size: 0.78rem; color: #9ca3af;
            margin-bottom: 20px;
        }
        .timer span { font-weight: 700; color: #16a34a; }
        .timer.expired span { color: #dc2626; }

        /* Submit button */
        .btn-verify {
            width: 100%; padding: 13px;
            background: #16a34a; border: none; border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.875rem; font-weight: 700; color: #fff;
            cursor: pointer; transition: background 0.15s, transform 0.15s;
            box-shadow: 0 2px 8px rgba(22,163,74,0.3);
            margin-bottom: 16px;
        }
        .btn-verify:hover { background: #15803d; transform: translateY(-1px); }
        .btn-verify:disabled { background: #d1d5db; cursor: not-allowed; transform: none; box-shadow: none; }

        .divider { height: 1px; background: #f3f4f6; margin: 4px 0 16px; }

        /* Resend button */
        .btn-resend {
            width: 100%; padding: 12px;
            background: transparent; border: 1.5px solid #e5e7eb; border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.83rem; font-weight: 600; color: #6b7280;
            cursor: pointer; transition: all 0.15s;
            display: flex; align-items: center; justify-content: center; gap: 7px;
        }
        .btn-resend svg { width: 14px; height: 14px; }
        .btn-resend:hover { border-color: #16a34a; color: #16a34a; }
        .btn-resend:disabled { opacity: 0.4; cursor: not-allowed; }
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
    <div class="card">

        <div class="icon-wrap">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3m-3 3.75h3M9 21h.008v.008H9V21z"/>
            </svg>
        </div>

        <h2>Enter your OTP</h2>
        <p class="subtitle">We sent a 6-digit verification code to</p>
        <div class="email-badge">{{ auth()->user()->email }}</div>

        {{-- Success message --}}
        @if (session('status') === 'otp-sent')
            <div class="success-banner">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                A new OTP has been sent to your email!
            </div>
        @endif

        {{-- Error message --}}
        @if ($errors->any())
            <div class="error-banner">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                {{ $errors->first() }}
            </div>
        @endif

        {{-- OTP Form --}}
        <form method="POST" action="{{ route('otp.verify') }}" id="otp-form">
            @csrf

            <label class="otp-label">Verification Code</label>

            {{-- 6 individual OTP input boxes --}}
            <div class="otp-inputs">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-box" autofocus />
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-box" />
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-box" />
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-box" />
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-box" />
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-box" />
            </div>

            {{-- Hidden field that holds the combined OTP --}}
            <input type="hidden" name="otp" id="otp-hidden" />

            {{-- Countdown timer --}}
            <div class="timer" id="timer">
                Code expires in <span id="countdown">10:00</span>
            </div>

            <button type="submit" class="btn-verify" id="btn-verify">
                Verify & Activate Account
            </button>
        </form>

        <div class="divider"></div>

        {{-- Resend OTP --}}
        <form method="POST" action="{{ route('otp.send') }}">
            @csrf
            <button type="submit" class="btn-resend" id="btn-resend">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
                Resend OTP
            </button>
        </form>

    </div>
</div>

<script>
    // ── OTP input auto-focus & navigation ──
    const boxes = document.querySelectorAll('.otp-box');
    const hidden = document.getElementById('otp-hidden');
    const form   = document.getElementById('otp-form');

    boxes.forEach((box, i) => {
        box.addEventListener('input', (e) => {
            const val = e.target.value.replace(/\D/g, '');
            box.value = val;
            if (val) {
                box.classList.add('filled');
                if (i < boxes.length - 1) boxes[i + 1].focus();
            } else {
                box.classList.remove('filled');
            }
            updateHidden();
        });

        box.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !box.value && i > 0) {
                boxes[i - 1].focus();
                boxes[i - 1].value = '';
                boxes[i - 1].classList.remove('filled');
                updateHidden();
            }
        });

        // Allow paste of full OTP
        box.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasted = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            pasted.split('').forEach((char, idx) => {
                if (boxes[idx]) {
                    boxes[idx].value = char;
                    boxes[idx].classList.add('filled');
                }
            });
            if (boxes[pasted.length - 1]) boxes[pasted.length - 1].focus();
            updateHidden();
        });
    });

    function updateHidden() {
        hidden.value = Array.from(boxes).map(b => b.value).join('');
    }

    // Auto-submit when all 6 digits entered
    form.addEventListener('input', () => {
        const otp = Array.from(boxes).map(b => b.value).join('');
        if (otp.length === 6) {
            setTimeout(() => form.submit(), 300);
        }
    });

    // ── Countdown timer (10 minutes) ──
    let totalSeconds = 10 * 60;
    const countdownEl = document.getElementById('countdown');
    const timerEl     = document.getElementById('timer');
    const resendBtn   = document.getElementById('btn-resend');

    resendBtn.disabled = true;

    const interval = setInterval(() => {
        totalSeconds--;
        const m = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
        const s = (totalSeconds % 60).toString().padStart(2, '0');
        countdownEl.textContent = `${m}:${s}`;

        if (totalSeconds <= 0) {
            clearInterval(interval);
            timerEl.innerHTML = 'Code has <span>expired</span>. Please resend.';
            timerEl.classList.add('expired');
            resendBtn.disabled = false;
        }

        // Enable resend after 60 seconds
        if (totalSeconds <= (9 * 60)) {
            resendBtn.disabled = false;
        }
    }, 1000);
</script>

</body>
</html>

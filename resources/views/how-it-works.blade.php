<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How It Works - Forklift Booking</title>
   
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        body { background: #f8fafc; margin: 0; }

        /* ── NAV ── */
        nav { background: #fff; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 50; }
        .nav-inner { max-width: 1280px; margin: 0 auto; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; height: 56px; }
        .nav-brand { display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .nav-brand-name { font-size: 1rem; font-weight: 800; color: #111; }
        .nav-brand-sub { font-size: 0.62rem; color: #94a3b8; }
        .nav-links { display: flex; align-items: center; gap: 20px; }
        .nav-links a { font-size: 0.8rem; font-weight: 600; color: #64748b; text-decoration: none; transition: color 0.15s; }
        .nav-links a:hover { color: #059669; }
        .nav-links a.active { color: #059669; }
        .nav-btn { background: #059669; color: #fff !important; padding: 6px 16px; border-radius: 8px; font-size: 0.78rem !important; }
        .nav-btn:hover { background: #047857; color: #fff !important; }

        /* ── HERO (compact) ── */
        .hero {
            background: linear-gradient(135deg, #059669, #0d9488);
            padding: 28px 24px 24px;
            text-align: center;
        }
        .hero-badge {
            display: inline-block;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 99px;
            margin-bottom: 10px;
        }
        .hero h1 { font-size: 1.8rem; font-weight: 800; color: #fff; margin: 0 0 6px; }
        .hero p { font-size: 0.82rem; color: rgba(255,255,255,0.85); margin: 0; }

        /* ── MAIN LAYOUT ── */
        .page-wrap { max-width: 1100px; margin: 0 auto; padding: 20px 20px 24px; }

        /* ── STEPS GRID ── */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }

        .step-card {
            border-radius: 14px;
            padding: 16px 14px;
            position: relative;
            overflow: hidden;
            transition: transform 0.18s, box-shadow 0.18s;
        }
        .step-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,0.12); }

        .step-card.s1 { background: linear-gradient(145deg, #059669, #047857); }
        .step-card.s2 { background: linear-gradient(145deg, #2563eb, #1d4ed8); }
        .step-card.s3 { background: linear-gradient(145deg, #7c3aed, #6d28d9); }
        .step-card.s4 { background: linear-gradient(145deg, #0d9488, #0f766e); }
        .step-card.s5 { background: linear-gradient(145deg, #ea580c, #c2410c); }

        /* subtle pattern */
        .step-card::before {
            content: '';
            position: absolute;
            top: -20px; right: -20px;
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
        }

        .step-num {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.22);
            border: 2px solid rgba(255,255,255,0.35);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; font-weight: 800; color: #fff;
            margin-bottom: 10px;
        }

        .step-title { font-size: 0.88rem; font-weight: 800; color: #fff; margin-bottom: 5px; line-height: 1.3; }
        .step-desc  { font-size: 0.72rem; color: rgba(255,255,255,0.8); line-height: 1.5; margin-bottom: 10px; }

        .step-tags { display: flex; flex-direction: column; gap: 4px; }
        .step-tag {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(255,255,255,0.15);
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 0.64rem; font-weight: 600; color: #fff;
        }

        /* connector arrows between cards */
        .steps-connector {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 16px;
            pointer-events: none;
        }
        .conn-cell { display: flex; align-items: center; justify-content: center; height: 0; position: relative; }

        /* ── TIMELINE BAR ── */
        .timeline {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px 24px;
            margin-bottom: 16px;
        }

        .timeline-title { font-size: 0.65rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #94a3b8; margin-bottom: 14px; }

        .timeline-track { position: relative; display: flex; align-items: center; justify-content: space-between; }

        .timeline-line {
            position: absolute;
            top: 50%; left: 5%; right: 5%;
            height: 3px;
            background: linear-gradient(90deg, #059669, #2563eb, #7c3aed, #0d9488, #ea580c);
            transform: translateY(-50%);
            border-radius: 3px;
            z-index: 0;
        }

        .tl-step { display: flex; flex-direction: column; align-items: center; gap: 7px; position: relative; z-index: 1; }

        .tl-dot {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.82rem; font-weight: 800; color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.18);
            border: 3px solid #fff;
        }
        .tl-dot.d1 { background: #059669; }
        .tl-dot.d2 { background: #2563eb; }
        .tl-dot.d3 { background: #7c3aed; }
        .tl-dot.d4 { background: #0d9488; }
        .tl-dot.d5 { background: #ea580c; }

        .tl-label { font-size: 0.65rem; font-weight: 700; color: #374151; text-align: center; max-width: 80px; line-height: 1.3; }

        /* ── PAYMENT + CTA ROW ── */
        .bottom-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        .pay-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px 20px;
        }

        .pay-title { font-size: 0.65rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #94a3b8; margin-bottom: 12px; }

        .pay-options { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }

        .pay-opt {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .pay-opt-icon {
            width: 34px; height: 34px;
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
        }
        .pay-opt-icon.card { background: rgba(37,99,235,0.1); }
        .pay-opt-icon.cash { background: rgba(5,150,105,0.1); }
        .pay-opt-name { font-size: 0.78rem; font-weight: 700; color: #111; }
        .pay-opt-sub  { font-size: 0.62rem; color: #94a3b8; }

        .cta-card {
            background: linear-gradient(135deg, #059669, #0d9488);
            border-radius: 14px;
            padding: 16px 20px;
            display: flex; flex-direction: column; justify-content: space-between;
        }

        .cta-title { font-size: 1.1rem; font-weight: 800; color: #fff; margin-bottom: 4px; }
        .cta-sub   { font-size: 0.75rem; color: rgba(255,255,255,0.8); margin-bottom: 14px; }

        .cta-btns { display: flex; gap: 8px; flex-wrap: wrap; }

        .cta-btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fff; color: #059669;
            padding: 9px 18px; border-radius: 9px;
            font-size: 0.78rem; font-weight: 800;
            text-decoration: none;
            transition: all 0.15s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }
        .cta-btn-primary:hover { background: #ecfdf5; transform: translateY(-1px); }

        .cta-btn-secondary {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff;
            padding: 9px 18px; border-radius: 9px;
            font-size: 0.78rem; font-weight: 700;
            text-decoration: none;
            transition: all 0.15s;
        }
        .cta-btn-secondary:hover { background: rgba(255,255,255,0.25); }

        /* ── FOOTER ── */
        footer { background: #111827; color: #fff; padding: 28px 24px 20px; }
        .footer-inner { max-width: 1100px; margin: 0 auto; }
        .footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 24px; margin-bottom: 20px; }
        .footer-brand-name { font-size: 1rem; font-weight: 800; }
        .footer-brand-desc { font-size: 0.72rem; color: #9ca3af; margin-top: 6px; line-height: 1.6; }
        .footer-col h4 { font-size: 0.72rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #6b7280; margin-bottom: 10px; }
        .footer-col ul { list-style: none; padding: 0; display: flex; flex-direction: column; gap: 6px; }
        .footer-col a { font-size: 0.73rem; color: #9ca3af; text-decoration: none; transition: color 0.15s; }
        .footer-col a:hover { color: #fff; }
        .footer-col li { font-size: 0.73rem; color: #9ca3af; }
        .footer-bottom { border-top: 1px solid #1f2937; padding-top: 16px; text-align: center; font-size: 0.7rem; color: #6b7280; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .steps-grid { grid-template-columns: 1fr 1fr; }
            .timeline { display: none; }
            .bottom-row { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 600px) {
            .steps-grid { grid-template-columns: 1fr; }
            .nav-links { display: none; }
            .hero h1 { font-size: 1.4rem; }
            .pay-options { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

{{-- NAV --}}
<nav>
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="nav-brand">
            <span style="font-size:1.4rem">🚜</span>
            <div>
                <div class="nav-brand-name">Forklift Booking</div>
                <div class="nav-brand-sub">Equipment Rental Platform</div>
            </div>
        </a>
        <div class="nav-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('bookings.forklifts') }}">Browse Fleet</a>
            <a href="{{ route('how') }}" class="active">How It Works</a>
            <a href="{{ route('reviews.index') }}">Reviews</a>
            <a href="{{ route('contact') }}">Contact</a>
            @auth
                <a href="{{ route('bookings.mine') }}">My Bookings</a>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}" class="nav-btn">Get Started</a>
            @endauth
        </div>
    </div>
</nav>

{{-- HERO --}}
<div class="hero">
    <div class="hero-badge">📚 Simple 5-Step Process</div>
    <h1>How It Works</h1>
    <p>Book your forklift in under 5 minutes — follow these easy steps</p>
</div>

{{-- MAIN --}}
<div class="page-wrap">

    {{-- 5 STEP CARDS --}}
    <div class="steps-grid">

        {{-- Step 1 --}}
        <div class="step-card s1">
            <div class="step-num">1</div>
            <div class="step-title">Create Booking</div>
            <div class="step-desc">Start from our dashboard or homepage in one click.</div>
            <div class="step-tags">
                <span class="step-tag">✓ Quick access</span>
                <span class="step-tag">✓ Mobile friendly</span>
            </div>
        </div>

        {{-- Step 2 --}}
        <div class="step-card s2">
            <div class="step-num">2</div>
            <div class="step-title">Login or Sign Up</div>
            <div class="step-desc">Secure account in under 1 minute. Google sign-in available.</div>
            <div class="step-tags">
                <span class="step-tag">✓ Google sign-in</span>
                <span class="step-tag">✓ Encrypted</span>
            </div>
        </div>

        {{-- Step 3 --}}
        <div class="step-card s3">
            <div class="step-num">3</div>
            <div class="step-title">Select Forklift</div>
            <div class="step-desc">Browse our fleet and pick the right equipment for your job.</div>
            <div class="step-tags">
                <span class="step-tag">✓ Filter by capacity</span>
                <span class="step-tag">✓ View specs</span>
            </div>
        </div>

        {{-- Step 4 --}}
        <div class="step-card s4">
            <div class="step-num">4</div>
            <div class="step-title">Pick Date & Time</div>
            <div class="step-desc"><span style="font-weight:700;color:#fff">Green</span> = Available &nbsp; <span style="font-weight:700;color:#fca5a5">Red</span> = Booked</div>
            <div class="step-tags">
                <span class="step-tag">✓ Real-time slots</span>
                <span class="step-tag">✓ Flexible duration</span>
            </div>
            {{-- mini slot preview --}}
            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:3px;margin-top:10px;">
                @for($i=1;$i<=7;$i++)
                    <div style="height:6px;border-radius:3px;background:{{ $i%3===0 ? 'rgba(252,165,165,0.7)' : 'rgba(255,255,255,0.4)' }}"></div>
                @endfor
            </div>
        </div>

        {{-- Step 5 --}}
        <div class="step-card s5">
            <div class="step-num">5</div>
            <div class="step-title">Complete Payment</div>
            <div class="step-desc">Pay by card or choose cash on pickup. Instant email confirmation.</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-top:10px;">
                <div style="background:rgba(255,255,255,0.15);border-radius:8px;padding:7px;text-align:center;font-size:0.62rem;font-weight:700;color:#fff;">
                    💳<br>Card
                </div>
                <div style="background:rgba(255,255,255,0.15);border-radius:8px;padding:7px;text-align:center;font-size:0.62rem;font-weight:700;color:#fff;">
                    💵<br>Cash
                </div>
            </div>
        </div>

    </div>

    {{-- TIMELINE --}}
    <div class="timeline">
        <div class="timeline-title">Quick Reference — All Steps at a Glance</div>
        <div class="timeline-track">
            <div class="timeline-line"></div>
            <div class="tl-step">
                <div class="tl-dot d1">1</div>
                <div class="tl-label">Create Booking</div>
            </div>
            <div class="tl-step">
                <div class="tl-dot d2">2</div>
                <div class="tl-label">Login / Sign Up</div>
            </div>
            <div class="tl-step">
                <div class="tl-dot d3">3</div>
                <div class="tl-label">Select Forklift</div>
            </div>
            <div class="tl-step">
                <div class="tl-dot d4">4</div>
                <div class="tl-label">Pick Date & Time</div>
            </div>
            <div class="tl-step">
                <div class="tl-dot d5">5</div>
                <div class="tl-label">Complete Payment</div>
            </div>
        </div>
    </div>

    {{-- PAYMENT + CTA --}}
    <div class="bottom-row">

        {{-- Payment methods --}}
        <div class="pay-card">
            <div class="pay-title">Payment Options</div>
            <div class="pay-options">
                <div class="pay-opt">
                    <div class="pay-opt-icon card">💳</div>
                    <div>
                        <div class="pay-opt-name">Pay by Card</div>
                        <div class="pay-opt-sub">Visa, Mastercard, Amex</div>
                    </div>
                </div>
                <div class="pay-opt">
                    <div class="pay-opt-icon cash">💵</div>
                    <div>
                        <div class="pay-opt-name">Cash on Pickup</div>
                        <div class="pay-opt-sub">Pay when you collect</div>
                    </div>
                </div>
            </div>
            {{-- success badge --}}
            <div style="display:flex;align-items:center;gap:8px;margin-top:14px;padding:10px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;">
                <span style="font-size:1rem;">✅</span>
                <span style="font-size:0.75rem;font-weight:700;color:#059669;">Instant email confirmation after payment</span>
            </div>
        </div>

        {{-- CTA --}}
        <div class="cta-card">
            <div>
                <div class="cta-title">Ready to get started?</div>
                <div class="cta-sub">Start your booking now and experience our streamlined 5-step process</div>
            </div>
            <div class="cta-btns">
                <a href="{{ route('bookings.create') }}" class="cta-btn-primary">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Create Booking
                </a>
                <a href="{{ route('bookings.forklifts') }}" class="cta-btn-secondary">
                    Browse Fleet →
                </a>
            </div>
        </div>

    </div>

</div>

{{-- FOOTER --}}
<footer>
    <div class="footer-inner">
        <div class="footer-grid">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <span style="font-size:1.3rem">🚜</span>
                    <span class="footer-brand-name">Forklift Booking</span>
                </div>
                <div class="footer-brand-desc">Professional equipment rental platform for warehouses and industrial operations.</div>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="{{ route('bookings.forklifts') }}">Browse Fleet</a></li>
                    <li><a href="{{ route('how') }}">How It Works</a></li>
                    <li><a href="{{ route('reviews.index') }}">Reviews</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Support</h4>
                <ul>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">FAQs</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <ul>
                    <li>📧 support@forkliftbooking.com</li>
                    <li>📞 1-800-FORKLIFT</li>
                    <li>📍 Industrial District</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">© <span id="yr"></span> Forklift Booking. All rights reserved.</div>
    </div>
</footer>

<script>document.getElementById('yr').textContent = new Date().getFullYear();</script>
</body>
</html>
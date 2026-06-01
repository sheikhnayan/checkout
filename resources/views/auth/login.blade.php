<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CartVIP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --gold: #f4b400;
            --gold-light: #ffd866;
            --dark-base: #06090f;
            --dark-panel: #0c1120;
            --dark-card: #111827;
            --dark-input: #161e2e;
            --border: rgba(255,255,255,0.08);
            --text: #e8edf8;
            --muted: #8892a4;
            --radius: 14px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-base);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        /* ── LEFT PANEL ─────────────────────────────── */
        .auth-left {
            flex: 0 0 48%;
            background: linear-gradient(145deg, #0c1529 0%, #07101e 55%, #0a0d18 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 50px 56px;
            position: relative;
            overflow: hidden;
            border-right: 1px solid var(--border);
        }

        .auth-left::before {
            content: '';
            position: absolute;
            top: -120px; left: -120px;
            width: 520px; height: 520px;
            background: radial-gradient(circle, rgba(244,180,0,0.07) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -80px; right: -80px;
            width: 380px; height: 380px;
            background: radial-gradient(circle, rgba(80,130,255,0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            z-index: 2;
        }
        .brand-logo img {
            height: 55px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(244,180,0,0.3));
        }
        .brand-logo-text {
            font-size: 1.4rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
        }
        .brand-logo-text span {
            color: var(--gold);
        }

        .left-hero {
            position: relative;
            z-index: 2;
        }
        .left-hero h1 {
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1.18;
            color: #fff;
            margin-bottom: 18px;
            letter-spacing: -0.03em;
        }
        .left-hero h1 em {
            font-style: normal;
            color: var(--gold);
        }
        .left-hero p {
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 38px;
            max-width: 380px;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }
        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(244,180,0,0.1);
            border: 1px solid rgba(244,180,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold);
            font-size: 0.9rem;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .feature-text strong {
            display: block;
            color: #dde4f0;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .feature-text span {
            color: var(--muted);
            font-size: 0.82rem;
            line-height: 1.5;
        }

        .left-footer {
            position: relative;
            z-index: 2;
            color: var(--muted);
            font-size: 0.8rem;
        }

        /* ── RIGHT PANEL ─────────────────────────────── */
        .auth-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 40px;
            background: var(--dark-panel);
        }

        .auth-form-wrap {
            width: 100%;
            max-width: 420px;
        }

        .auth-form-wrap .form-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.03em;
            margin-bottom: 6px;
        }
        .auth-form-wrap .form-subtitle {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 32px;
        }

        .input-group-auth {
            position: relative;
            margin-bottom: 18px;
        }
        .input-group-auth label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #c5cedf;
            margin-bottom: 7px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }
        .input-group-auth .input-icon {
            position: relative;
        }
        .input-group-auth .input-icon > i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 0.85rem;
            z-index: 2;
            pointer-events: none;
        }
        .input-group-auth .input-icon input {
            width: 100%;
            background: var(--dark-input);
            border: 1px solid rgba(255,255,255,0.09);
            color: #fff;
            padding: 13px 44px 13px 38px;
            border-radius: var(--radius);
            font-size: 0.92rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }
        .input-group-auth .input-icon input::placeholder { color: #4a5568; }
        .input-group-auth .input-icon input:focus {
            border-color: rgba(244,180,0,0.5);
            background: #1a2133;
            box-shadow: 0 0 0 3px rgba(244,180,0,0.08);
        }
        .input-group-auth .input-icon input.is-invalid {
            border-color: rgba(239,68,68,0.6);
        }
        .invalid-feedback {
            font-size: 0.8rem;
            color: #f87171;
            margin-top: 5px;
        }

        .pass-toggle {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            padding: 2px 4px;
            z-index: 3;
            font-size: 0.85rem;
            transition: color 0.2s;
        }
        .pass-toggle:hover { color: var(--gold); }

        .row-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 26px;
            margin-top: 4px;
        }
        .check-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .check-wrap input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--gold);
            cursor: pointer;
        }
        .check-wrap label {
            font-size: 0.85rem;
            color: var(--muted);
            cursor: pointer;
        }
        .forgot-link {
            font-size: 0.85rem;
            color: var(--gold-light);
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: var(--gold); text-decoration: underline; }

        .btn-auth {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #f4b400, #ffd866);
            color: #111;
            border: none;
            border-radius: var(--radius);
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.01em;
            cursor: pointer;
            transition: transform 0.18s, box-shadow 0.18s;
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow: hidden;
        }
        .btn-auth::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0);
            transition: background 0.2s;
        }
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(244,180,0,0.35);
        }
        .btn-auth:active { transform: translateY(0); }

        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 28px 0;
            color: var(--muted);
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .alt-links {
            display: flex;
            gap: 10px;
        }
        .alt-link {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 10px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--muted);
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 500;
            background: rgba(255,255,255,0.02);
            transition: border-color 0.2s, color 0.2s, background 0.2s;
        }
        .alt-link i { font-size: 0.85rem; }
        .alt-link:hover {
            border-color: rgba(244,180,0,0.35);
            color: var(--gold-light);
            background: rgba(244,180,0,0.05);
        }

        .alert-auth {
            border-radius: var(--radius);
            padding: 12px 16px;
            font-size: 0.86rem;
            margin-bottom: 22px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border: 1px solid transparent;
        }
        .alert-auth i { margin-top: 1px; flex-shrink: 0; }
        .alert-auth.success {
            background: rgba(52,211,153,0.1);
            border-color: rgba(52,211,153,0.25);
            color: #6ee7b7;
        }
        .alert-auth.danger {
            background: rgba(239,68,68,0.1);
            border-color: rgba(239,68,68,0.25);
            color: #fca5a5;
        }

        /* ── MOBILE ─────────────────────────────────── */
        @media (max-width: 900px) {
            body { flex-direction: column; align-items: stretch; }
            .auth-left {
                flex: 0 0 auto;
                padding: 36px 28px 32px;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }
            .left-hero h1 { font-size: 1.6rem; }
            .feature-list { flex-direction: row; flex-wrap: wrap; gap: 12px; }
            .feature-item { flex: 0 0 calc(50% - 6px); }
            .left-footer { display: none; }
            .auth-right { padding: 36px 24px 50px; }
        }
        @media (max-width: 560px) {
            .auth-left { padding: 28px 20px; }
            .brand-logo img { height: 45px; }
            .feature-list { display: none; }
            .auth-right { padding: 28px 20px 44px; }
            .left-hero p { display: none; }
            .left-hero h1 { font-size: 1.35rem; margin-bottom: 0; }
        }
    </style>
</head>
<body>

    <!-- LEFT: Brand Panel -->
    <div class="auth-left">
        <div class="brand-logo">
            <img src="{{ asset('images/logo.png') }}" alt="CartVIP">
            {{-- <div class="brand-logo-text">Cart<span>VIP</span></div> --}}
        </div>

        <div class="left-hero">
            <h1>Your <em>Command Center</em><br>for Sales, Bookings, <br>and Growth.</h1>
            <p>Manage packages, affiliates, and transactions in one streamlined dashboard — built to maximize revenue and simplify operations.</p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-ticket-alt"></i></div>
                    <div class="feature-text">
                        <strong>Smart Packages</strong>
                        <span>Create high-converting packages with custom pricing, perks, and availability — built for real-world bookings.</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="feature-text">
                        <strong>Live Performance</strong>
                        <span>Monitor revenue, conversions, and customer activity in real time — no guesswork, just clarity.</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-link"></i></div>
                    <div class="feature-text">
                        <strong>Affiliate Engine</strong>
                        <span>Turn partners into a sales force. Track referrals, commissions, and performance automatically.</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="feature-text">
                        <strong>Secure Checkout</strong>
                        <span>Payments are securely processed through trusted providers, including Authorize.Net and Stripe. We do not store or handle sensitive cardholder data.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="left-footer">
            &copy; {{ date('Y') }} CartVIP. All rights reserved.
        </div>
    </div>

    <!-- RIGHT: Form Panel -->
    <div class="auth-right">
        <div class="auth-form-wrap">
            <div class="form-title">Welcome back</div>
            <div class="form-subtitle">Sign in to your CartVIP account to continue.</div>

            @if(session('success'))
                <div class="alert-auth success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="alert-auth danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>@foreach($errors->all() as $err){{ $err }} @endforeach</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="input-group-auth">
                    <label for="email">Email Address</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="you@example.com"
                               autocomplete="email"
                               class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                               required autofocus>
                    </div>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="input-group-auth">
                    <label for="password">Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password"
                               placeholder="Enter your password"
                               autocomplete="current-password"
                               class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                               required>
                        <button type="button" class="pass-toggle" onclick="togglePass('password',this)" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row-meta">
                    <div class="check-wrap">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Keep me signed in</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-auth">
                    <i class="fas fa-arrow-right-to-bracket me-2"></i> Sign In
                </button>
            </form>

            <div class="divider">or join as</div>

            <div class="alt-links">
                <a href="{{ route('affiliate.apply') }}" class="alt-link">
                    <i class="fas fa-bullhorn"></i> Affiliate
                </a>
                <a href="{{ route('entertainer.apply') }}" class="alt-link">
                    <i class="fas fa-star"></i> Entertainer
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePass(id, btn) {
            var input = document.getElementById(id);
            var icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>

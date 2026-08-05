<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — CartVIP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --gold: #f4b400; --gold-light: #ffd866;
            --dark-base: #06090f; --dark-panel: #0c1120;
            --dark-input: #161e2e; --border: rgba(255,255,255,0.08);
            --text: #e8edf8; --muted: #8892a4; --radius: 14px;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-base);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }
        .auth-left {
            flex: 0 0 48%;
            background: linear-gradient(145deg, #0c1529 0%, #07101e 55%, #0a0d18 100%);
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 50px 56px; position: relative; overflow: hidden;
            border-right: 1px solid var(--border);
        }
        .auth-left::before {
            content: ''; position: absolute; top: -120px; left: -120px;
            width: 520px; height: 520px;
            background: radial-gradient(circle, rgba(244,180,0,0.07) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-left::after {
            content: ''; position: absolute; bottom: -80px; right: -80px;
            width: 380px; height: 380px;
            background: radial-gradient(circle, rgba(80,130,255,0.06) 0%, transparent 70%);
            pointer-events: none;
        }
        .brand-logo { display: flex; align-items: center; gap: 14px; position: relative; z-index: 2; }
        .brand-logo img { height: 44px; width: auto; object-fit: contain; filter: drop-shadow(0 2px 8px rgba(244,180,0,0.3)); }
        .brand-logo-text { font-size: 1.4rem; font-weight: 800; color: #fff; letter-spacing: -0.02em; }
        .brand-logo-text span { color: var(--gold); }
        .left-hero { position: relative; z-index: 2; }
        .left-hero h1 { font-size: 2.1rem; font-weight: 800; line-height: 1.2; color: #fff; margin-bottom: 16px; letter-spacing: -0.03em; }
        .left-hero h1 em { font-style: normal; color: var(--gold); }
        .left-hero p { color: var(--muted); font-size: 0.97rem; line-height: 1.7; margin-bottom: 36px; max-width: 380px; }
        .tip-box {
            background: rgba(244,180,0,0.06);
            border: 1px solid rgba(244,180,0,0.18);
            border-radius: 12px;
            padding: 20px 22px;
        }
        .tip-box h6 { color: var(--gold-light); font-size: 0.82rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px; }
        .tip-item { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 10px; }
        .tip-item:last-child { margin-bottom: 0; }
        .tip-item i { color: var(--gold); font-size: 0.78rem; margin-top: 3px; flex-shrink: 0; }
        .tip-item span { color: var(--muted); font-size: 0.83rem; line-height: 1.5; }
        .left-footer { position: relative; z-index: 2; color: var(--muted); font-size: 0.8rem; }
        .auth-right { flex: 1; display: flex; align-items: center; justify-content: center; padding: 50px 40px; background: var(--dark-panel); }
        .auth-form-wrap { width: 100%; max-width: 420px; }
        .auth-form-wrap .form-title { font-size: 1.6rem; font-weight: 800; color: #fff; letter-spacing: -0.03em; margin-bottom: 6px; }
        .auth-form-wrap .form-subtitle { color: var(--muted); font-size: 0.9rem; margin-bottom: 32px; }
        .input-group-auth { position: relative; margin-bottom: 18px; }
        .input-group-auth label { display: block; font-size: 0.82rem; font-weight: 600; color: #c5cedf; margin-bottom: 7px; letter-spacing: 0.02em; text-transform: uppercase; }
        .input-group-auth .input-icon { position: relative; }
        .input-group-auth .input-icon i.fi { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 0.85rem; z-index: 2; pointer-events: none; }
        .input-group-auth .input-icon input {
            width: 100%; background: var(--dark-input); border: 1px solid rgba(255,255,255,0.09);
            color: #fff; padding: 13px 14px 13px 38px; border-radius: var(--radius);
            font-size: 0.92rem; font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s; outline: none;
        }
        .input-group-auth .input-icon input::placeholder { color: #4a5568; }
        .input-group-auth .input-icon input:focus { border-color: rgba(244,180,0,0.5); background: #1a2133; box-shadow: 0 0 0 3px rgba(244,180,0,0.08); }
        .input-group-auth .input-icon input.is-invalid { border-color: rgba(239,68,68,0.6); }
        .pass-toggle { position: absolute; right: 13px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--muted); cursor: pointer; padding: 2px 4px; z-index: 3; font-size: 0.85rem; transition: color 0.2s; }
        .pass-toggle:hover { color: var(--gold); }
        .invalid-feedback { font-size: 0.8rem; color: #f87171; margin-top: 5px; }
        .strength-bar { height: 3px; border-radius: 2px; background: rgba(255,255,255,0.06); margin-top: 8px; overflow: hidden; }
        .strength-fill { height: 100%; width: 0; border-radius: 2px; transition: width 0.3s, background 0.3s; }
        .btn-auth {
            width: 100%; padding: 14px;
            background: linear-gradient(90deg, #f4b400, #ffd866);
            color: #111; border: none; border-radius: var(--radius);
            font-weight: 700; font-size: 0.95rem; cursor: pointer;
            transition: transform 0.18s, box-shadow 0.18s;
            font-family: 'Inter', sans-serif; margin-top: 8px;
        }
        .btn-auth:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(244,180,0,0.35); }
        .back-link { display: inline-flex; align-items: center; gap: 7px; color: var(--muted); font-size: 0.85rem; text-decoration: none; margin-top: 24px; transition: color 0.2s; }
        .back-link:hover { color: var(--gold-light); }
        .alert-auth { border-radius: var(--radius); padding: 12px 16px; font-size: 0.86rem; margin-bottom: 22px; display: flex; align-items: flex-start; gap: 10px; border: 1px solid transparent; }
        .alert-auth i { margin-top: 1px; flex-shrink: 0; }
        .alert-auth.danger { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.25); color: #fca5a5; }
        @media (max-width: 900px) {
            body { flex-direction: column; }
            .auth-left { flex: 0 0 auto; padding: 36px 28px 32px; border-right: none; border-bottom: 1px solid var(--border); }
            .left-hero h1 { font-size: 1.6rem; }
            .left-footer { display: none; }
            .auth-right { padding: 36px 24px 50px; }
        }
        @media (max-width: 560px) {
            .auth-left { padding: 28px 20px; }
            .tip-box { display: none; }
            .auth-right { padding: 28px 20px 44px; }
            .left-hero p { display: none; }
            .left-hero h1 { font-size: 1.35rem; margin-bottom: 0; }
        }
    </style>
</head>
<body>
    <div class="auth-left">
        <div class="brand-logo">
            <img src="{{ asset('images/logo.png') }}" alt="CartVIP">
            <div class="brand-logo-text">Cart<span>VIP</span></div>
        </div>
        <div class="left-hero">
            <h1>Choose a<br><em>strong</em><br>new password.</h1>
            <p>Your new password will protect your CartVIP account and all your event data.</p>
            <div class="tip-box">
                <h6><i class="fas fa-shield-alt me-1"></i> Password Tips</h6>
                <div class="tip-item"><i class="fas fa-check"></i><span>At least 8 characters long.</span></div>
                <div class="tip-item"><i class="fas fa-check"></i><span>Mix uppercase, lowercase, numbers and symbols.</span></div>
                <div class="tip-item"><i class="fas fa-check"></i><span>Avoid using your name, email or common words.</span></div>
                <div class="tip-item"><i class="fas fa-check"></i><span>Never reuse passwords from other sites.</span></div>
            </div>
        </div>
        <div class="left-footer">&copy; {{ date('Y') }} CartVIP. All rights reserved.</div>
    </div>

    <div class="auth-right">
        <div class="auth-form-wrap">
            <div class="form-title">Set new password</div>
            <div class="form-subtitle">Enter your email and choose a new password for your account.</div>

            @if($errors->any())
                <div class="alert-auth danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>@foreach($errors->all() as $err){{ $err }} @endforeach</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="input-group-auth">
                    <label for="email">Email Address</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope fi"></i>
                        <input type="email" id="email" name="email"
                               value="{{ old('email', $email) }}"
                               placeholder="you@example.com"
                               autocomplete="email"
                               class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                               required>
                    </div>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="input-group-auth">
                    <label for="password">New Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock fi"></i>
                        <input type="password" id="password" name="password"
                               placeholder="Create a strong password"
                               autocomplete="new-password"
                               class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                               required oninput="checkStrength(this.value)">
                        <button type="button" class="pass-toggle" onclick="togglePass('password',this)" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="input-group-auth">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock fi"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               placeholder="Re-enter new password"
                               autocomplete="new-password"
                               required>
                        <button type="button" class="pass-toggle" onclick="togglePass('password_confirmation',this)" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-auth">
                    <i class="fas fa-check me-2"></i> Reset Password
                </button>
            </form>

            <a href="{{ route('login') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Sign In
            </a>
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
        function checkStrength(val) {
            var fill = document.getElementById('strength-fill');
            if (!fill) return;
            var score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            var pct = (score / 4) * 100;
            var colors = ['#ef4444','#f97316','#eab308','#22c55e'];
            fill.style.width = pct + '%';
            fill.style.background = colors[score - 1] || 'transparent';
        }
    </script>
</body>
</html>

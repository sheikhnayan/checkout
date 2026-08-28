<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">
    <title>Set Up Your Access | The Nightly Reports</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('user/assets/img/favicon/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --nr-bg: #07111f;
            --nr-surface: #0d1a2e;
            --nr-surface-2: #142238;
            --nr-border: rgba(255, 255, 255, 0.09);
            --nr-border-gold: rgba(201, 168, 76, 0.4);
            --nr-gold: #c9a84c;
            --nr-gold-bright: #e8be6a;
            --nr-gold-glow: rgba(201, 168, 76, 0.18);
            --nr-text: #e2e8f0;
            --nr-muted: #94a3b8;
            --nr-danger: #fb7185;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                linear-gradient(rgba(7, 17, 31, 0.9), rgba(7, 17, 31, 0.96)),
                linear-gradient(90deg, rgba(201, 168, 76, 0.05) 1px, transparent 1px),
                linear-gradient(rgba(201, 168, 76, 0.05) 1px, transparent 1px),
                var(--nr-bg);
            background-size: auto, 44px 44px, 44px 44px, auto;
            color: var(--nr-text);
            font-family: 'DM Sans', sans-serif;
        }

        .page-shell {
            min-height: 100vh;
            padding: 28px clamp(20px, 5vw, 72px);
            display: flex;
            flex-direction: column;
        }

        .brand-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: min(1120px, 100%);
            margin: 0 auto;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--nr-border);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            font-family: 'Playfair Display', serif;
            font-size: 1.18rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .brand i { color: var(--nr-gold); filter: drop-shadow(0 0 7px var(--nr-gold-glow)); }

        .secure-label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--nr-muted);
            font-size: 0.73rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .secure-label i { color: var(--nr-gold-bright); font-size: 0.7rem; }

        .content {
            width: min(1120px, 100%);
            margin: auto;
            padding: clamp(42px, 8vh, 92px) 0;
            display: grid;
            grid-template-columns: minmax(0, 0.9fr) minmax(360px, 1.1fr);
            gap: clamp(42px, 8vw, 120px);
            align-items: center;
        }

        .intro { max-width: 470px; }

        .eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 20px;
            color: var(--nr-gold-bright);
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .eyebrow::before {
            width: 30px;
            height: 1px;
            background: var(--nr-gold);
            content: '';
        }

        h1 {
            margin: 0;
            color: #fff;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 5vw, 4.25rem);
            font-weight: 600;
            line-height: 1.05;
        }

        .intro-copy {
            max-width: 390px;
            margin: 24px 0 0;
            color: var(--nr-muted);
            font-size: 1rem;
            line-height: 1.7;
        }

        .profile-mark {
            display: inline-grid;
            width: 58px;
            height: 58px;
            margin-top: 42px;
            place-items: center;
            border: 1px solid var(--nr-border-gold);
            border-radius: 50%;
            background: var(--nr-gold-glow);
            color: var(--nr-gold-bright);
            font-size: 1.25rem;
        }

        .setup-panel {
            padding: clamp(28px, 5vw, 46px);
            border: 1px solid var(--nr-border);
            border-top: 2px solid var(--nr-gold);
            border-radius: 12px;
            background: linear-gradient(145deg, rgba(20, 34, 56, 0.96), rgba(13, 26, 46, 0.98));
            box-shadow: 0 18px 55px rgba(0, 0, 0, 0.35);
        }

        .panel-heading { margin-bottom: 28px; }

        .panel-heading h2 {
            margin: 0 0 8px;
            color: #fff;
            font-size: 1.35rem;
            font-weight: 600;
        }

        .panel-heading p {
            margin: 0;
            color: var(--nr-muted);
            font-size: 0.86rem;
            line-height: 1.55;
        }

        .alert {
            margin-bottom: 22px;
            padding: 12px 14px;
            border: 1px solid rgba(244, 63, 94, 0.45);
            border-radius: 8px;
            background: rgba(244, 63, 94, 0.13);
            color: var(--nr-danger);
            font-size: 0.82rem;
        }

        .alert ul { margin: 0; padding-left: 18px; }
        .alert li + li { margin-top: 4px; }

        .field { margin-bottom: 20px; }

        label {
            display: block;
            margin-bottom: 8px;
            color: #fff;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        input {
            width: 100%;
            min-height: 50px;
            padding: 0 15px;
            border: 1px solid var(--nr-border);
            border-radius: 8px;
            outline: none;
            background: rgba(7, 17, 31, 0.55);
            color: #fff;
            font: inherit;
            font-size: 0.92rem;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        input::placeholder { color: #64748b; }

        input:focus {
            border-color: var(--nr-gold);
            background: rgba(7, 17, 31, 0.8);
            box-shadow: 0 0 0 3px var(--nr-gold-glow);
        }

        .submit-button {
            display: inline-flex;
            width: 100%;
            min-height: 52px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            margin-top: 5px;
            border: 1px solid var(--nr-gold);
            border-radius: 8px;
            background: linear-gradient(135deg, var(--nr-gold), #b3923d);
            color: #0b0e1a;
            cursor: pointer;
            font: inherit;
            font-size: 0.88rem;
            font-weight: 700;
            transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .submit-button:hover {
            background: linear-gradient(135deg, #dfbc5e, var(--nr-gold));
            box-shadow: 0 7px 20px var(--nr-gold-glow);
            transform: translateY(-1px);
        }

        .form-note {
            margin: 18px 0 0;
            color: #64748b;
            font-size: 0.72rem;
            line-height: 1.5;
            text-align: center;
        }

        .page-footer {
            width: min(1120px, 100%);
            margin: auto auto 0;
            padding-top: 22px;
            border-top: 1px solid var(--nr-border);
            color: #64748b;
            font-size: 0.72rem;
            text-align: center;
        }

        @media (max-width: 760px) {
            .page-shell { padding: 20px; }
            .brand-bar { padding-bottom: 18px; }
            .secure-label { font-size: 0.65rem; }
            .content { grid-template-columns: 1fr; gap: 34px; padding: 48px 0 42px; }
            .intro { max-width: none; text-align: center; }
            .eyebrow { justify-content: center; }
            .intro-copy { margin-right: auto; margin-left: auto; }
            .profile-mark { margin-top: 26px; }
            .setup-panel { padding: 28px 22px; }
        }

        @media (max-width: 420px) {
            .brand { font-size: 1rem; }
            .secure-label { display: none; }
            h1 { font-size: 2.55rem; }
        }
    </style>
</head>
<body>
    <main class="page-shell">
        <header class="brand-bar">
            <div class="brand"><i class="fas fa-moon"></i><span>The Nightly Reports</span></div>
            <div class="secure-label"><i class="fas fa-lock"></i> Secure portal access</div>
        </header>

        <section class="content">
            <div class="intro">
                <p class="eyebrow">Ambassador access</p>
                <h1>Your portal begins here.</h1>
                <p class="intro-copy">Welcome, {{ $ambassador->name }}. Create your password to access the Nightly Reports workspace and the locations assigned to you.</p>
                <span class="profile-mark" aria-hidden="true"><i class="fas fa-key"></i></span>
            </div>

            <div class="setup-panel">
                <div class="panel-heading">
                    <h2>Set your password</h2>
                    <p>Choose a secure password for your ambassador account.</p>
                </div>

                @if ($errors->any())
                    <div class="alert" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('ambassador.setup', $token) }}">
                    @csrf
                    <div class="field">
                        <label for="password">New password</label>
                        <input type="password" id="password" name="password" autocomplete="new-password" required autofocus>
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
                    </div>

                    <button type="submit" class="submit-button"><i class="fas fa-arrow-right"></i> Set password and enter</button>
                    <p class="form-note"><i class="fas fa-shield-halved"></i> Your password is encrypted and never visible to administrators.</p>
                </form>
            </div>
        </section>

        <footer class="page-footer">The Nightly Reports &nbsp;&middot;&nbsp; Executive Operations &amp; Analytics</footer>
    </main>
</body>
</html>

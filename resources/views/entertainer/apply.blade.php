<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entertainer Registration - CartVIP</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('user/assets/img/favicon/favicon.svg') }}" />
    <link rel="mask-icon" href="{{ asset('user/assets/img/favicon/safari-mask.svg') }}" color="#ffcc00" />
    <link rel="shortcut icon" href="{{ asset('user/assets/img/favicon/favicon.ico') }}" />
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
            top: -120px;
            left: -120px;
            width: 520px;
            height: 520px;
            background: radial-gradient(circle, rgba(244,180,0,0.07) 0%, transparent 70%);
            pointer-events: none;
        }

        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 380px;
            height: 380px;
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
            height: 60px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(244,180,0,0.3));
        }

        .left-hero {
            position: relative;
            z-index: 2;
        }

        .left-hero h1 {
            font-size: 2.1rem;
            font-weight: 800;
            line-height: 1.2;
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
            font-size: 0.98rem;
            line-height: 1.7;
            margin-bottom: 34px;
            max-width: 390px;
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
            max-width: 640px;
        }

        .form-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.03em;
            margin-bottom: 6px;
        }

        .form-subtitle {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .alert-auth {
            border-radius: var(--radius);
            padding: 12px 16px;
            font-size: 0.86rem;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border: 1px solid rgba(239,68,68,0.25);
            background: rgba(239,68,68,0.1);
            color: #fca5a5;
        }

        .social-signup-wrap {
            margin-bottom: 18px;
            padding: 14px;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: var(--radius);
            background: rgba(255,255,255,0.03);
        }

        .social-signup-wrap .muted {
            color: var(--muted);
            font-size: 0.82rem;
        }

        .btn-social {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 10px;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            text-decoration: none;
            padding: 10px 14px;
            background: rgba(255,255,255,0.06);
            transition: border-color 0.2s, background 0.2s, color 0.2s;
        }

        .btn-social:hover {
            border-color: rgba(244,180,0,0.35);
            color: var(--gold-light);
            background: rgba(244,180,0,0.05);
        }

        .btn-social.disabled {
            opacity: 0.45;
            pointer-events: none;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .form-grid .full {
            grid-column: 1 / -1;
        }

        .input-group-auth {
            position: relative;
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

        .input-icon {
            position: relative;
        }

        .input-icon > i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 0.85rem;
            z-index: 2;
            pointer-events: none;
        }

        .input-icon input,
        .input-group-auth textarea,
        .input-group-auth select,
        .readonly-input {
            width: 100%;
            background: var(--dark-input);
            border: 1px solid rgba(255,255,255,0.09);
            color: #fff;
            border-radius: var(--radius);
            font-size: 0.92rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }

        .input-icon input {
            padding: 13px 44px 13px 38px;
        }

        .input-group-auth select,
        .readonly-input {
            padding: 13px 14px;
        }

        .input-group-auth select option {
            background: #ffffff;
            color: #0f172a;
        }

        .readonly-input {
            background: rgba(255,255,255,0.04);
        }

        .input-group-auth textarea {
            padding: 13px 14px;
            min-height: 120px;
            resize: vertical;
        }

        .input-icon input::placeholder,
        .input-group-auth textarea::placeholder {
            color: #4a5568;
        }

        .input-icon input:focus,
        .input-group-auth textarea:focus,
        .input-group-auth select:focus {
            border-color: rgba(244,180,0,0.5);
            background: #1a2133;
            box-shadow: 0 0 0 3px rgba(244,180,0,0.08);
        }

        .club-help {
            color: var(--muted);
            font-size: 0.8rem;
            margin-top: 6px;
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

        .pass-toggle:hover {
            color: var(--gold);
        }

        .actions-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-top: 20px;
        }

        .back-link {
            color: var(--muted);
            text-decoration: none;
            font-size: 0.86rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--gold-light);
        }

        .btn-auth {
            padding: 13px 18px;
            background: linear-gradient(90deg, #f4b400, #ffd866);
            color: #111;
            border: none;
            border-radius: var(--radius);
            font-weight: 700;
            font-size: 0.94rem;
            cursor: pointer;
            transition: transform 0.18s, box-shadow 0.18s;
            font-family: 'Inter', sans-serif;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(244,180,0,0.35);
        }

        @media (max-width: 900px) {
            body {
                flex-direction: column;
            }

            .auth-left {
                flex: 0 0 auto;
                padding: 36px 28px 32px;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .left-hero h1 {
                font-size: 1.6rem;
            }

            .feature-list {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 12px;
            }

            .feature-item {
                flex: 0 0 calc(50% - 6px);
            }

            .left-footer {
                display: none;
            }

            .auth-right {
                padding: 36px 24px 50px;
            }
        }

        @media (max-width: 700px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .social-signup-wrap {
            display: none !important;
        }

        @media (max-width: 560px) {
            .auth-left {
                padding: 28px 20px;
            }

            .feature-list {
                display: none;
            }

            .auth-right {
                padding: 28px 20px 44px;
            }

            .left-hero p {
                display: none;
            }

            .left-hero h1 {
                font-size: 1.35rem;
                margin-bottom: 0;
            }

            .actions-row {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-auth {
                width: 100%;
            }
        }
    </style>
    <!-- reCAPTCHA v3 Script -->
    @if(config('services.recaptcha.site_key') && config('services.recaptcha.site_key') !== 'YOUR_RECAPTCHA_SITE_KEY_HERE')
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    <script>
        window.executeRecaptcha = function(action = 'submit') {
            return new Promise((resolve) => {
                if (!window.grecaptcha) {
                    resolve(null);
                    return;
                }
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: action})
                        .then(function(token) {
                            resolve(token);
                        })
                        .catch(function() {
                            resolve(null);
                        });
                });
            });
        };
    </script>
    @endif
</head>
<body>
    <div class="auth-left">
        <div class="brand-logo">
            <img src="{{ asset('images/logo.png') }}" alt="CartVIP">
        </div>

        <div class="left-hero">
            <h1>Register as an <em>Entertainer</em><br>and build your own<br>audience funnel.</h1>
            <p>Apply to your selected club, manage your profile, and drive more direct bookings through your entertainer page.</p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-star"></i></div>
                    <div class="feature-text">
                        <strong>Dedicated Profile</strong>
                        <span>Showcase your brand, content, and availability in one place.</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <div class="feature-text">
                        <strong>Audience Growth</strong>
                        <span>Turn social traffic into trackable package bookings.</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-sliders"></i></div>
                    <div class="feature-text">
                        <strong>Flexible Content</strong>
                        <span>Update promotions, highlights, and offers from your dashboard.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="left-footer">&copy; {{ date('Y') }} CartVIP. All rights reserved.</div>
    </div>

    <div class="auth-right">
        <div class="auth-form-wrap">
            <div class="form-title">Entertainer registration</div>
            <div class="form-subtitle">Submit your details to apply as an entertainer.</div>

            @if($errors->any() || session('error'))
                <div class="alert-auth">
                    <i class="fas fa-exclamation-circle mt-1"></i>
                    <span>
                        @if(session('error'))
                            <div>{{ session('error') }}</div>
                        @endif
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </span>
                </div>
            @endif

            <form method="POST" action="{{ route('entertainer.apply.submit') }}">
                @csrf

                @php
                    $googleBase = route('social.signup.redirect', ['role' => 'entertainer', 'provider' => 'google']);
                    $selectedClubQuery = $selectedClub ? ('?club=' . urlencode($selectedClub->slug)) : '';
                @endphp

                <div class="social-signup-wrap">
                    <div class="fw-semibold">Quick Sign Up</div>
                    <div class="muted">Use Google to continue with entertainer registration.</div>
                    <a class="btn-social {{ $selectedClub ? '' : 'disabled' }}" id="entertainer-social-google" href="{{ $googleBase . $selectedClubQuery }}">
                        <i class="fab fa-google"></i>
                        Sign up with Google
                    </a>
                    @if(!$selectedClub)
                        <div class="club-help">Select a club first to enable Google sign up.</div>
                    @endif
                </div>

                <div class="form-grid">
                    @if($selectedClub)
                        <input type="hidden" name="club_slug" value="{{ $selectedClub->slug }}">
                        <div class="input-group-auth full">
                            <label for="club_name">Selected Club</label>
                            <input id="club_name" type="text" class="readonly-input" value="{{ $selectedClub->name }}" readonly>
                            <div class="club-help">This registration link is tied to this club.</div>
                        </div>
                    @else
                        <div class="input-group-auth full">
                            <label for="website_id">Select Club</label>
                            <select id="website_id" name="website_id" required>
                                <option value="">Choose a club</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}" @selected((string) old('website_id') === (string) $club->id)>{{ $club->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="input-group-auth">
                        <label for="name">Full Name</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" required>
                        </div>
                    </div>

                    <div class="input-group-auth">
                        <label for="email">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                        </div>
                    </div>

                    <div class="input-group-auth">
                        <label for="password">Password</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input id="password" type="password" name="password" placeholder="Create password" required>
                            <button type="button" class="pass-toggle" onclick="togglePass('password', this)" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="input-group-auth">
                        <label for="password_confirmation">Confirm Password</label>
                        <div class="input-icon">
                            <i class="fas fa-shield-alt"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm password" required>
                            <button type="button" class="pass-toggle" onclick="togglePass('password_confirmation', this)" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="input-group-auth">
                        <label for="phone">Phone (optional)</label>
                        <div class="input-icon">
                            <i class="fas fa-phone"></i>
                            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="+1 555 123 4567">
                        </div>
                    </div>

                    <div class="input-group-auth full">
                        <label for="experience">Experience (optional)</label>
                        <textarea id="experience" name="experience" placeholder="Tell us about your profile and audience">{{ old('experience') }}</textarea>
                    </div>
                </div>

                <div class="actions-row">
                    <a href="{{ route('login') }}" class="back-link">Already approved? Login</a>
                    <button type="submit" class="btn-auth">
                        <i class="fas fa-paper-plane me-2"></i> Submit Application
                    </button>
                </div>
                <input type="hidden" name="recaptcha_token" id="recaptcha_token" value="">
                <input type="hidden" name="form_load_time" id="form_load_time" value="">
            </form>
        </div>
    </div>

    <script>
        // Set form load time
        document.addEventListener('DOMContentLoaded', function() {
            const formLoadTimeField = document.getElementById('form_load_time');
            if (formLoadTimeField) {
                formLoadTimeField.value = Math.floor(Date.now() / 1000);
            }
        });

        // Get reCAPTCHA token on form submit
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', async function(e) {
                if (typeof window.executeRecaptcha === 'function') {
                    e.preventDefault();
                    const token = await window.executeRecaptcha('entertainer_apply');
                    if (token) {
                        document.getElementById('recaptcha_token').value = token;
                    }
                    form.submit();
                }
            });
        }
    </script>

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

        (function () {
            const clubSelect = document.querySelector('select[name="website_id"]');
            const googleBtn = document.getElementById('entertainer-social-google');
            const googleBase = @json($googleBase ?? null);
            const clubs = @json($clubs ?? []);

            if (!clubSelect || !googleBtn || !googleBase) {
                return;
            }

            function updateSocialLink() {
                const websiteId = clubSelect.value;
                if (!websiteId) {
                    googleBtn.classList.add('disabled');
                    googleBtn.setAttribute('href', '#');
                    return;
                }

                const club = clubs.find(function (item) {
                    return String(item.id) === String(websiteId);
                });

                if (!club || !club.slug) {
                    return;
                }

                googleBtn.classList.remove('disabled');
                googleBtn.setAttribute('href', googleBase + '?club=' + encodeURIComponent(club.slug));
            }

            clubSelect.addEventListener('change', updateSocialLink);
            updateSocialLink();
        })();
    </script>
</body>
</html>

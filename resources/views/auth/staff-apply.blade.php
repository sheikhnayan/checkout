<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Staff Registration - CartVIP</title>
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
            font-size: 2.15rem;
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
            font-size: 0.75rem;
            color: var(--muted);
            position: relative;
            z-index: 2;
        }

        .auth-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px 56px;
            overflow-y: auto;
        }

        .auth-form-wrap {
            max-width: 400px;
        }

        .form-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .form-subtitle {
            font-size: 0.85rem;
            color: var(--muted);
            margin-bottom: 24px;
        }

        .alert-auth {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.25);
            border-radius: 10px;
            margin-bottom: 20px;
            color: #fca5a5;
            font-size: 0.85rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            opacity: 0.68;
            margin-bottom: 8px;
            display: block;
        }

        .form-control, select {
            background: var(--dark-input);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus, select:focus {
            background: var(--dark-input);
            border-color: var(--gold);
            color: var(--text);
            box-shadow: 0 0 0 3px rgba(244,180,0,0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: var(--muted);
        }

        .form-control.is-invalid {
            border-color: #ef4444 !important;
        }

        .invalid-feedback {
            display: block;
            color: #fca5a5;
            font-size: 0.75rem;
            margin-top: 4px;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap .toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 0.9rem;
            padding: 0;
            margin-top: 8px;
        }

        .password-wrap .toggle-btn:hover {
            color: var(--text);
        }

        .btn-auth {
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(135deg, var(--gold), #ffc107);
            border: none;
            color: #000;
            font-weight: 700;
            font-size: 0.94rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: transform 0.18s, box-shadow 0.18s;
            font-family: 'Inter', sans-serif;
            margin-top: 8px;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(244,180,0,0.35);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.85rem;
        }

        .back-link:hover {
            color: var(--text);
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
            <h1>Join as <em>current staff</em> and get started quickly.</h1>
            <p>Already working at a club? Register with your existing club information and skip the W-9 form process. Get approved and start earning today.</p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-clock"></i></div>
                    <div class="feature-text">
                        <strong>Quick Setup</strong>
                        <span>No W-9 form needed—register and you're ready to go.</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-id-card"></i></div>
                    <div class="feature-text">
                        <strong>Choose Your Role</strong>
                        <span>Register as a Promoter or Entertainer based on your position.</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="feature-text">
                        <strong>Instant Approval</strong>
                        <span>Get fast-tracked approval with existing club staff verification.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="left-footer">&copy; {{ date('Y') }} CartVIP. All rights reserved.</div>
    </div>

    <div class="auth-right">
        <div class="auth-form-wrap">
            <div class="form-title">Staff registration</div>
            <div class="form-subtitle">Complete your details to submit your staff application.</div>

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

            <form method="POST" action="{{ route('staff.apply.submit') }}">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label for="staff_type">Staff Type <span style="color: #ef4444;">*</span></label>
                        <select id="staff_type" name="staff_type" class="form-control @error('staff_type') is-invalid @enderror" required>
                            <option value="">Select role...</option>
                            <option value="affiliate" {{ old('staff_type') === 'affiliate' ? 'selected' : '' }}>Promoter</option>
                            <option value="entertainer" {{ old('staff_type') === 'entertainer' ? 'selected' : '' }}>Entertainer</option>
                        </select>
                        @error('staff_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="website_id">Club <span style="color: #ef4444;">*</span></label>
                        <select id="website_id" name="website_id" class="form-control @error('website_id') is-invalid @enderror" required>
                            <option value="">Select club...</option>
                            @foreach($websites as $website)
                                <option value="{{ $website->id }}" {{ old('website_id') == $website->id ? 'selected' : '' }}>{{ $website->name }}</option>
                            @endforeach
                        </select>
                        @error('website_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group full">
                        <label for="name">Full Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Your full name" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group full">
                        <label for="email">Email <span style="color: #ef4444;">*</span></label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="your@email.com" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group full">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="+1 (555) 123-4567" value="{{ old('phone') }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password <span style="color: #ef4444;">*</span></label>
                        <div class="password-wrap">
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required>
                            <button type="button" class="toggle-btn" onclick="togglePass('password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password <span style="color: #ef4444;">*</span></label>
                        <div class="password-wrap">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm password" required>
                            <button type="button" class="toggle-btn" onclick="togglePass('password_confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="actions-row">
                    <a href="{{ route('login') }}" class="back-link">Already approved? Login</a>
                    <button type="submit" class="btn-auth">
                        <i class="fas fa-paper-plane me-2"></i> Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>

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

        // Set form load time
        document.addEventListener('DOMContentLoaded', function() {
            const formLoadTimeField = document.createElement('input');
            formLoadTimeField.type = 'hidden';
            formLoadTimeField.name = 'form_load_time';
            formLoadTimeField.id = 'form_load_time';
            formLoadTimeField.value = Math.floor(Date.now() / 1000);
            document.querySelector('form').appendChild(formLoadTimeField);
        });

        // Get reCAPTCHA token on form submit
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', async function(e) {
                if (typeof window.executeRecaptcha === 'function') {
                    e.preventDefault();
                    const token = await window.executeRecaptcha('staff_registration');
                    if (token) {
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = 'recaptcha_token';
                        tokenInput.value = token;
                        form.appendChild(tokenInput);
                    }
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>

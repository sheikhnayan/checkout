<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Staff Registration — CartVIP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #f4b400;
            --dark-base: #06090f;
            --dark-panel: #0c1120;
            --dark-card: #111827;
            --dark-input: #161e2e;
            --border: rgba(255,255,255,0.08);
            --text: #e8edf8;
            --muted: #8892a4;
            --radius: 14px;
        }
        body { font-family: 'Inter', sans-serif; background: var(--dark-base); color: var(--text); min-height: 100vh; padding: 20px; }
        .auth-container { max-width: 500px; margin: 0 auto; }
        .auth-card { background: var(--dark-panel); border: 1px solid var(--border); border-radius: var(--radius); padding: 40px; }
        .auth-header { text-align: center; margin-bottom: 30px; }
        .auth-header h1 { font-size: 1.8rem; font-weight: 800; margin-bottom: 8px; }
        .auth-header p { color: var(--muted); font-size: 0.9rem; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; }
        .form-control, select { background: var(--dark-input); border: 1px solid var(--border); color: var(--text); padding: 10px 14px; border-radius: 8px; font-size: 0.95rem; }
        .form-control:focus, select:focus { background: var(--dark-input); border-color: var(--gold); color: var(--text); box-shadow: 0 0 0 3px rgba(244,180,0,0.1); }
        .form-control::placeholder { color: var(--muted); }
        .password-toggle { position: relative; }
        .password-toggle button { position: absolute; right: 12px; top: 32px; background: none; border: none; color: var(--muted); cursor: pointer; }
        .password-toggle button:hover { color: var(--text); }
        .btn-auth { width: 100%; padding: 12px 16px; background: linear-gradient(135deg, var(--gold), #ffc107); border: none; border-radius: 8px; color: #000; font-weight: 700; font-size: 0.95rem; cursor: pointer; margin-top: 12px; transition: all 0.3s ease; }
        .btn-auth:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(244,180,0,0.3); }
        .btn-auth:active { transform: translateY(0); }
        .btn-auth:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .divider { text-align: center; margin: 24px 0; font-size: 0.85rem; color: var(--muted); }
        .divider::before, .divider::after { content: ''; display: inline-block; width: 35%; height: 1px; background: var(--border); vertical-align: middle; margin: 0 12px; }
        .back-link { display: inline-block; margin-top: 20px; color: var(--muted); text-decoration: none; font-size: 0.9rem; }
        .back-link:hover { color: var(--text); }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-danger { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; }
        .alert-success { background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #86efac; }
        .invalid-feedback { display: block; color: #fca5a5; font-size: 0.8rem; margin-top: 4px; }
        .form-control.is-invalid { border-color: #ef4444; }
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
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1><i class="fas fa-id-badge me-2"></i>Current Staff</h1>
                <p>Register as existing club staff</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Registration failed:</strong> {{ $errors->first() }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('staff.apply.submit') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="staff_type">Staff Type <span style="color: #ef4444;">*</span></label>
                    <select id="staff_type" name="staff_type" class="form-control @error('staff_type') is-invalid @enderror" required>
                        <option value="">Select Staff Type...</option>
                        <option value="affiliate" {{ old('staff_type') === 'affiliate' ? 'selected' : '' }}>Promoter</option>
                        <option value="entertainer" {{ old('staff_type') === 'entertainer' ? 'selected' : '' }}>Entertainer</option>
                    </select>
                    @error('staff_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="website_id">Club <span style="color: #ef4444;">*</span></label>
                    <select id="website_id" name="website_id" class="form-control @error('website_id') is-invalid @enderror" required>
                        <option value="">Select Club...</option>
                        @foreach($websites as $website)
                            <option value="{{ $website->id }}" {{ old('website_id') == $website->id ? 'selected' : '' }}>{{ $website->name }}</option>
                        @endforeach
                    </select>
                    @error('website_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="name">Full Name <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Your full name" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Email <span style="color: #ef4444;">*</span></label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="your@email.com" value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="phone">Phone <span style="opacity: 0.5;">(Optional)</span></label>
                    <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="+1 (555) 123-4567" value="{{ old('phone') }}">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group password-toggle">
                    <label for="password">Password <span style="color: #ef4444;">*</span></label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required>
                    <button type="button" onclick="togglePass('password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group password-toggle">
                    <label for="password_confirmation">Confirm Password <span style="color: #ef4444;">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm password" required>
                    <button type="button" onclick="togglePass('password_confirmation', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                    @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn-auth">
                    <i class="fas fa-paper-plane me-2"></i> Submit Registration
                </button>
            </form>

            <a href="{{ route('login') }}" class="back-link">← Back to Login</a>
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

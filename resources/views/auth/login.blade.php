<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CartVIP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: radial-gradient(circle at 10% 10%, #1f2b44, #0a1020 55%);
            color: #f5f7ff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }
        .login-card {
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 18px;
            background: rgba(11, 17, 32, 0.9);
            padding: 0;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(140deg, rgba(33,44,68,0.82), rgba(12,18,35,0.92));
            border-radius: 18px 18px 0 0;
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }
        .login-header h3 {
            color: #f5f7ff;
            font-weight: 700;
        }
        .login-header p {
            color: #b8c0d9;
            margin-bottom: 0;
        }
        .form-control {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            padding: 12px 14px;
            border-radius: 10px;
        }
        .form-control::placeholder {
            color: #b8c0d9;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.12);
            border-color: rgba(255,212,102,0.6);
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(244,180,0,0.15);
        }
        .form-label {
            color: #f5f7ff;
            font-weight: 500;
        }
        .btn-login {
            background: linear-gradient(90deg, #f4b400, #ffd866);
            color: #1a1a1a;
            border: none;
            font-weight: 700;
            padding: 12px 28px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(244,180,0,0.3);
            background: linear-gradient(90deg, #ffd866, #f4b400);
        }
        .form-check-label {
            color: #b8c0d9;
        }
        .alert {
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.05);
        }
        .alert-success {
            background: rgba(76, 175, 125, 0.15);
            border-color: rgba(76, 175, 125, 0.5);
            color: #7fe5d0;
        }
        .alert-danger {
            background: rgba(244, 67, 54, 0.15);
            border-color: rgba(244, 67, 54, 0.5);
            color: #ff8a7f;
        }
        .muted {
            color: #b8c0d9;
        }
        a {
            color: #ffd866;
            text-decoration: none;
        }
        a:hover {
            color: #f4b400;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12" style="max-width: 500px;">
                <div class="card login-card">
                    <div class="login-header">
                        <h3 class="mb-2">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Login
                        </h3>
                        <p class="mb-0 muted">Secure access to your account</p>
                    </div>
                    
                    <div style="padding: 30px;">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                @foreach($errors->all() as $error)
                                    {{ $error }}
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>
                                    Email Address
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" 
                                       placeholder="Enter your email"
                                       required 
                                       autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>
                                    Password
                                </label>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       placeholder="Enter your password"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <div class="mb-3 text-end">
                                <a href="{{ route('password.request') }}" class="muted text-decoration-none">
                                    Forgot password?
                                </a>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Login
                                </button>
                            </div>
                        </form>

                        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                            <a href="{{ route('affiliate.apply') }}" class="muted text-decoration-none">
                                <i class="fas fa-bullhorn me-1"></i> Apply as Affiliate
                            </a>
                            <a href="{{ route('entertainer.apply') }}" class="muted text-decoration-none">
                                <i class="fas fa-user-star me-1"></i> Apply as Entertainer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
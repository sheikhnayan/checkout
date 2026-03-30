<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CartVIP</title>
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
        .auth-card {
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 18px;
            background: rgba(11, 17, 32, 0.9);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            max-width: 520px;
            width: 100%;
            overflow: hidden;
        }
        .auth-header {
            background: linear-gradient(140deg, rgba(33,44,68,0.82), rgba(12,18,35,0.92));
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }
        .auth-header h3 {
            color: #f5f7ff;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .auth-header p {
            color: #b8c0d9;
            margin-bottom: 0;
        }
        .auth-body {
            padding: 30px;
        }
        .form-label {
            color: #f5f7ff;
            font-weight: 500;
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
        .btn-primary-custom {
            background: linear-gradient(90deg, #f4b400, #ffd866);
            color: #1a1a1a;
            border: none;
            font-weight: 700;
            padding: 12px 28px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(244,180,0,0.3);
            background: linear-gradient(90deg, #ffd866, #f4b400);
        }
        .alert {
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.05);
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
    <div class="auth-card">
        <div class="auth-header">
            <h3><i class="fas fa-lock me-2"></i>Reset Password</h3>
            <p>Choose a new password for your account.</p>
        </div>

        <div class="auth-body">
            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $email) }}"
                        placeholder="Enter your email"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Enter new password"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-control"
                        placeholder="Re-enter new password"
                        required
                    >
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="fas fa-check me-2"></i>Reset Password
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="muted">
                    <i class="fas fa-arrow-left me-1"></i>Back to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>

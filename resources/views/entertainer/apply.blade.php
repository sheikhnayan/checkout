<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply as Entertainer - CartVIP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: radial-gradient(circle at 10% 10%, #1f2b44, #0a1020 55%);
            color: #f5f7ff;
            min-height: 100vh;
            padding: 30px 15px;
        }
        .apply-wrap {
            max-width: 880px;
            margin: 0 auto;
        }
        .hero {
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 18px;
            background: linear-gradient(140deg, rgba(33,44,68,0.82), rgba(12,18,35,0.92));
            padding: 30px;
            margin-bottom: 20px;
        }
        .card-panel {
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 18px;
            background: rgba(11, 17, 32, 0.9);
            padding: 24px;
        }
        .form-control, .form-control:focus, .form-select, .form-select:focus {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            box-shadow: none;
        }
        .form-select option {
            background: #ffffff;
            color: #0f172a;
        }
        .form-control::placeholder { color: #b8c0d9; }
        .btn-apply {
            background: linear-gradient(90deg, #f4b400, #ffd866);
            color: #1a1a1a;
            border: none;
            font-weight: 700;
            padding: 12px 18px;
            border-radius: 12px;
        }
        .muted { color: #b8c0d9; }
    </style>
</head>
<body>
    <div class="apply-wrap">
        <div class="hero">
            <h2 class="mb-2">Sign up as an Entertainer</h2>
            <p class="mb-0 muted">Once approved, manage your own page&mdash;share updates, promote packages, and connect directly with guests.</p>
        </div>

        <div class="card-panel">
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('entertainer.apply.submit') }}">
                @csrf

                @if($selectedClub)
                    <input type="hidden" name="club_slug" value="{{ $selectedClub->slug }}">
                    <div class="mb-3">
                        <label class="form-label">Selected Club</label>
                        <input type="text" class="form-control" value="{{ $selectedClub->name }}" readonly>
                        <small class="muted">This registration link is tied to this club.</small>
                    </div>
                @else
                    <div class="mb-3">
                        <label class="form-label">Select Club</label>
                        <select name="website_id" class="form-select" required>
                            <option value="">Choose a club</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" @selected((string) old('website_id') === (string) $club->id)>{{ $club->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone (optional)</label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Experience / Introduction (optional)</label>
                        <textarea class="form-control" rows="5" name="experience" placeholder="Tell us about your profile and audience">{{ old('experience') }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('login') }}" class="text-decoration-none muted">Already approved? Login</a>
                    <button type="submit" class="btn btn-apply">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

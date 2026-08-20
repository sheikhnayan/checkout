<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Submission Successful — The Nightly Reports</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('user/assets/vendor/css/core.css') }}" />
  <style>
    :root { --bg: #07111f; --surface: #0d1a2e; --surface-2: #142238; --border: rgba(255, 255, 255, 0.08); --gold: #c9a84c; --gold-glow: rgba(201, 168, 76, 0.2); }
    body { background-color: var(--bg); color: #e2e8f0; font-family: 'DM Sans', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; }
    .card-success-luxury { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 3rem 2rem; max-width: 540px; text-align: center; box-shadow: 0 15px 40px rgba(0,0,0,0.4); }
    .icon-circle { width: 80px; height: 80px; border-radius: 50%; background: rgba(16, 185, 129, 0.15); border: 2px solid #10b981; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: #34d399; font-size: 2.2rem; }
    .btn-gold { background: linear-gradient(135deg, #c9a84c 0%, #b3923d 100%); border: none; color: #07111f; font-weight: 700; border-radius: 8px; padding: 0.65rem 1.5rem; }
  </style>
</head>
<body>

<div class="card-success-luxury">
  <div class="icon-circle">
    <i class="fas fa-check"></i>
  </div>
  <h3 class="text-white fw-bold mb-2" style="font-family: 'Playfair Display', serif;">Submission Received</h3>
  <p class="text-muted mb-4">
    {{ session('success') ?? 'Your shift operations record has been securely encrypted, saved, and dispatched to executive reporting.' }}
  </p>
  <div class="d-flex flex-wrap gap-2 justify-content-center">
    <a href="{{ route('nightly.submit.nightly') }}" class="btn btn-gold">
      <i class="fas fa-plus me-1"></i> Submit Another Report
    </a>
    @if(auth()->check())
      <a href="{{ route('admin.nightly-reports.dashboard') }}" class="btn btn-outline-secondary">
        Return to Portal Dashboard
      </a>
    @endif
  </div>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Website — CartVIP</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('user/assets/img/favicon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('user/assets/img/favicon/favicon.ico') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --gold: #f4b400; --gold-light: #ffd866; --dark-base: #06090f; --dark-panel: #0c1120; --dark-input: #161e2e; --border: rgba(255,255,255,0.08); --text: #e8edf8; --muted: #8892a4; --radius: 14px; }
        body { font-family: 'Inter', sans-serif; background: var(--dark-base); color: var(--text); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .select-card { width: 100%; max-width: 460px; background: var(--dark-panel); border: 1px solid var(--border); border-radius: 18px; padding: 36px 32px; }
        .select-card img { height: 48px; margin-bottom: 22px; }
        .select-card h1 { font-size: 1.5rem; font-weight: 800; color: #fff; letter-spacing: -0.02em; margin-bottom: 6px; }
        .select-card p { color: var(--muted); font-size: 0.9rem; margin-bottom: 24px; }
        .alert-auth { border-radius: var(--radius); padding: 12px 16px; font-size: 0.86rem; margin-bottom: 18px; display: flex; align-items: flex-start; gap: 10px; border: 1px solid rgba(239,68,68,0.25); background: rgba(239,68,68,0.1); color: #fca5a5; }
        .site-option { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 15px 18px; margin-bottom: 12px; background: var(--dark-input); border: 1px solid rgba(255,255,255,0.09); border-radius: var(--radius); color: #fff; font-size: 0.98rem; font-weight: 600; font-family: 'Inter', sans-serif; cursor: pointer; transition: border-color 0.2s, background 0.2s, transform 0.15s; text-align: left; }
        .site-option:hover { border-color: rgba(244,180,0,0.5); background: #1a2133; transform: translateY(-1px); }
        .site-option i { color: var(--gold); font-size: 0.9rem; }
        .site-name { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .back-link { display: inline-block; margin-top: 14px; color: var(--muted); font-size: 0.85rem; text-decoration: none; }
        .back-link:hover { color: var(--gold-light); }
    </style>
</head>
<body>
    <div class="select-card">
        <img src="{{ asset('images/logo.png') }}" alt="CartVIP">
        <h1>Select a website</h1>
        <p>Your account manages more than one website. Choose which one you want to sign in to.</p>

        @if($errors->any())
            <div class="alert-auth"><i class="fas fa-exclamation-circle"></i><span>@foreach($errors->all() as $err){{ $err }} @endforeach</span></div>
        @endif

        <form method="POST" action="{{ route('login.select-website.submit') }}">
            @csrf
            @foreach($accounts as $account)
                <button type="submit" name="user_id" value="{{ $account->id }}" class="site-option">
                    <span class="site-name">{{ optional($account->website)->name ?? ('Website #' . $account->website_id) }}</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            @endforeach
        </form>

        <a href="{{ route('login') }}" class="back-link"><i class="fas fa-arrow-left me-1"></i>Back to login</a>
    </div>
</body>
</html>

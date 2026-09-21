<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 | Page Not Found</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-1: #090e1a;
            --bg-2: #0f172a;
            --card: rgba(15, 23, 42, 0.85);
            --border: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #f59e0b;
            --accent-glow: rgba(245, 158, 11, 0.25);
            --primary: #6366f1;
            --primary-glow: rgba(99, 102, 241, 0.25);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-main);
            background:
                radial-gradient(circle at 20% 20%, rgba(99, 102, 241, 0.15), transparent 35%),
                radial-gradient(circle at 80% 80%, rgba(245, 158, 11, 0.12), transparent 35%),
                linear-gradient(135deg, var(--bg-1), var(--bg-2));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .error-card {
            width: min(580px, 100%);
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 44px 36px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(245, 158, 11, 0.5), transparent);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fbbf24;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .status-badge svg {
            width: 14px;
            height: 14px;
        }

        .status-code {
            font-size: clamp(3.5rem, 10vw, 5.5rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.04em;
            background: linear-gradient(135deg, #ffffff 40%, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
        }

        h1 {
            font-size: clamp(1.4rem, 4vw, 1.85rem);
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
            color: #ffffff;
        }

        p {
            font-size: 0.98rem;
            line-height: 1.65;
            color: var(--text-muted);
            margin-bottom: 32px;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 22px;
            border-radius: 12px;
            font-size: 0.92rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.18s ease;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #ffffff;
            box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.4);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px -5px rgba(245, 158, 11, 0.5);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.14);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .footer-brand {
            margin-top: 36px;
            font-size: 0.82rem;
            color: #64748b;
            letter-spacing: 0.02em;
        }

        @media (max-width: 480px) {
            .error-card {
                padding: 32px 20px;
                border-radius: 20px;
            }

            .actions {
                flex-direction: column;
                width: 100%;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <main class="error-card" role="main">
        <div class="status-badge">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            Error 404
        </div>

        <div class="status-code">404</div>

        <h1>Page Or Form Not Found</h1>

        <p>
            {{ !empty($exception) && $exception->getMessage() ? $exception->getMessage() : 'The link you followed may be expired, deactivated, or the page does not exist.' }}
        </p>

        <div class="actions">
            <button type="button" class="btn btn-secondary" onclick="window.history.length > 1 ? window.history.back() : window.location.href='/'">
                Go Back
            </button>
            <a href="/" class="btn btn-primary">
                Return to Home
            </a>
        </div>

        <div class="footer-brand">
            CartVIP Platform
        </div>
    </main>
</body>
</html>

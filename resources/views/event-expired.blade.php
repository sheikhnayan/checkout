<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $data->name }} | Event Expired</title>
    <style>
        :root {
            --bg-1: #060b1a;
            --bg-2: #0f1c3a;
            --card: rgba(12, 20, 40, 0.9);
            --border: rgba(255, 255, 255, 0.14);
            --text: #f5f8ff;
            --muted: #a7b2cc;
            --accent: #f5be56;
            --accent-dark: #2a1e06;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Poppins", "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 15% 10%, rgba(245, 190, 86, 0.22), transparent 24%),
                radial-gradient(circle at 80% 88%, rgba(130, 170, 255, 0.2), transparent 30%),
                linear-gradient(135deg, var(--bg-1), var(--bg-2));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .expired-card {
            width: min(780px, 100%);
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(245, 190, 86, 0.5);
            background: rgba(245, 190, 86, 0.18);
            color: #ffe1a6;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        h1 {
            margin: 14px 0 10px;
            font-size: clamp(1.7rem, 4vw, 2.5rem);
            line-height: 1.12;
            letter-spacing: -0.02em;
        }

        p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
            font-size: 1rem;
        }

        .event-name {
            margin-top: 18px;
            color: #dbe7ff;
            font-size: 1.02rem;
            font-weight: 700;
        }

        .event-date {
            margin-top: 8px;
            color: #b8c7e6;
            font-size: 0.92rem;
        }

        .actions {
            margin-top: 24px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn {
            appearance: none;
            border: 0;
            text-decoration: none;
            cursor: pointer;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            transition: transform .16s ease, box-shadow .16s ease, opacity .16s ease;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn-primary {
            background: linear-gradient(135deg, #ffd38a, var(--accent));
            color: var(--accent-dark);
            box-shadow: 0 14px 28px rgba(245, 190, 86, 0.28);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #e8efff;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .club {
            margin-top: 16px;
            color: #d7deef;
            font-size: 0.9rem;
        }

        @media (max-width: 640px) {
            .expired-card {
                padding: 22px;
                border-radius: 16px;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                text-align: center;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @php
        $eventEnd = $event->end_date_value ?: $event->start_date_value ?: $event->date_value;
        $formattedEventDate = null;

        if ($eventEnd) {
            try {
                $formattedEventDate = \Carbon\Carbon::createFromFormat('Y-m-d', $eventEnd, $websiteTimezone)->format('l, M d, Y');
            } catch (\Throwable $exception) {
                $formattedEventDate = null;
            }
        }
    @endphp

    <main class="expired-card" role="main" aria-labelledby="expired-title">
        <span class="badge">Event Expired</span>
        <h1 id="expired-title">This event is no longer available.</h1>
        <p>The event link is valid, but this event has already ended and cannot be booked anymore.</p>

        <div class="event-name">{{ $event->name }}</div>
        @if($formattedEventDate)
            <div class="event-date">Ended on {{ $formattedEventDate }} ({{ \App\Support\WebsiteTimezone::label($websiteTimezone) }})</div>
        @endif

        <div class="actions">
            <a class="btn btn-primary" href="{{ route('index', $data->slug) }}">Go To {{ $data->name }} Checkout</a>
            <a class="btn btn-secondary" href="{{ route('index', $data->slug) }}#packages">Browse Packages</a>
        </div>

        <div class="club">{{ $data->name }} · Powered by CartVIP</div>
    </main>
</body>
</html>

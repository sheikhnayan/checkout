<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $club->name }} Roll Call</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        :root {
            --roll-bg: #070d1a;
            --roll-stage: rgba(10, 17, 33, 0.86);
            --roll-stage-border: rgba(159, 196, 255, 0.18);
            --roll-card: rgba(12, 20, 36, 0.92);
            --roll-border: rgba(255, 255, 255, 0.1);
            --roll-text: #edf3ff;
            --roll-muted: #91a2c1;
            --roll-accent: #efbe6f;
            --roll-shadow: 0 28px 86px rgba(0, 0, 0, 0.48);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Poppins", "Segoe UI", sans-serif;
            color: var(--roll-text);
            background:
                radial-gradient(circle at 14% 6%, rgba(239, 190, 111, 0.2), transparent 24%),
                radial-gradient(circle at 85% 88%, rgba(122, 169, 255, 0.22), transparent 30%),
                linear-gradient(90deg, #02050d 0%, #081123 19%, #15345f 50%, #081123 81%, #02050d 100%),
                linear-gradient(180deg, #071121 0%, var(--roll-bg) 100%);
            background-attachment: fixed;
        }

        .roll-shell {
            width: min(1120px, calc(100% - 24px));
            margin: 18px auto 0;
            padding: 22px 20px 48px;
            border: 1px solid var(--roll-stage-border);
            border-radius: 28px;
            background:
                linear-gradient(180deg, rgba(18, 34, 59, 0.62) 0%, rgba(8, 14, 28, 0.76) 24%, rgba(8, 13, 25, 0.86) 100%),
                var(--roll-stage);
            backdrop-filter: blur(8px);
            box-shadow: var(--roll-shadow);
        }

        .roll-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: var(--roll-muted);
            margin-bottom: 18px;
        }

        .roll-topbar-nav {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .roll-topbar-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid rgba(239, 190, 111, 0.35);
            background: linear-gradient(145deg, rgba(239, 190, 111, 0.2), rgba(239, 190, 111, 0.1));
            color: #f6ddb2;
            font-size: .82rem;
            font-weight: 700;
            letter-spacing: .03em;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, color .2s ease;
        }

        .roll-topbar-link:hover {
            color: #fff;
            border-color: rgba(239, 190, 111, 0.72);
            box-shadow: 0 10px 24px rgba(239, 190, 111, 0.22);
            transform: translateY(-1px);
        }

        .roll-topbar-context {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(145, 162, 193, 0.28);
            background: rgba(145, 162, 193, 0.08);
            color: var(--roll-muted);
            font-size: .75rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .roll-hero {
            border: 1px solid var(--roll-border);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 16px;
            background:
                radial-gradient(circle at 7% 14%, rgba(239, 190, 111, 0.16), transparent 28%),
                linear-gradient(145deg, rgba(16, 25, 44, 0.98), rgba(8, 14, 27, 0.9));
            box-shadow: var(--roll-shadow);
        }

        .roll-kicker {
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: .16em;
            font-size: .72rem;
            color: var(--roll-accent);
            margin-bottom: 8px;
        }

        .roll-title {
            margin: 0;
            font-size: clamp(2rem, 4vw, 3.3rem);
            letter-spacing: -0.04em;
            line-height: .95;
            font-weight: 800;
        }

        .roll-copy {
            margin: 10px 0 0;
            color: var(--roll-muted);
            line-height: 1.75;
            max-width: 70ch;
        }

        .roll-grid {
            display: grid;
            grid-template-columns: 340px minmax(0, 1fr);
            gap: 16px;
        }

        .roll-calendar-panel,
        .roll-models-panel {
            border: 1px solid var(--roll-border);
            border-radius: 16px;
            background: var(--roll-card);
            box-shadow: var(--roll-shadow);
        }

        .roll-calendar-panel {
            padding: 16px;
            position: sticky;
            top: 14px;
            height: fit-content;
        }

        .roll-calendar-head {
            margin-bottom: 12px;
        }

        .roll-calendar-head h2 {
            margin: 0;
            font-size: 1.2rem;
        }

        .roll-calendar-head p {
            margin: 6px 0 0;
            color: var(--roll-muted);
            font-size: .9rem;
            line-height: 1.6;
        }

        .roll-selected-date {
            margin-top: 12px;
            border-radius: 12px;
            border: 1px solid rgba(239, 190, 111, 0.28);
            background: rgba(239, 190, 111, 0.12);
            padding: 11px 12px;
            font-size: .88rem;
        }

        .roll-selected-date strong {
            color: #fff;
        }

        .flatpickr-calendar.inline {
            width: 100%;
            max-width: 100%;
            background: transparent;
            box-shadow: none;
            border: 0;
            margin: 0;
        }

        .flatpickr-months .flatpickr-month,
        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-weekday,
        .flatpickr-day {
            color: var(--roll-text);
        }

        .flatpickr-months {
            margin-bottom: 14px;
            padding-bottom: 4px;
        }

        .flatpickr-current-month {
            padding-top: 4px;
            line-height: 1.35;
        }

        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            color: #f3d8a5;
            fill: #f3d8a5;
            top: 4px;
            border-radius: 10px;
            width: 34px;
            height: 34px;
            padding: 6px;
        }

        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            color: #fff;
            fill: #fff;
            background: rgba(255,255,255,0.08);
        }

        .flatpickr-months .flatpickr-prev-month svg,
        .flatpickr-months .flatpickr-next-month svg {
            width: 14px;
            height: 14px;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-current-month input.cur-year {
            color: var(--roll-text);
            font-weight: 700;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            background: rgba(20, 31, 54, 0.95);
            border: 1px solid rgba(255,255,255,0.16);
            border-radius: 10px;
            padding: 2px 8px;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months option {
            background: #172643;
            color: #edf3ff;
        }

        .flatpickr-current-month .numInputWrapper span {
            border-color: rgba(148, 163, 184, 0.5);
        }

        .flatpickr-current-month .numInputWrapper span.arrowUp:after {
            border-bottom-color: #edf3ff;
        }

        .flatpickr-current-month .numInputWrapper span.arrowDown:after {
            border-top-color: #edf3ff;
        }

        .flatpickr-weekdays {
            background: rgba(255,255,255,0.03);
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .flatpickr-weekdays .flatpickr-weekday {
            color: #91a2c1 !important;
            font-weight: 600;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .flatpickr-day {
            border-radius: 10px;
            border: 1px solid transparent;
            max-width: 40px;
            line-height: 36px;
            height: 38px;
        }

        .flatpickr-day:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.12);
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange {
            background: linear-gradient(135deg, #f4d696, #e2af5c);
            color: #241707;
            border-color: rgba(242, 188, 103, 0.9);
            box-shadow: 0 10px 22px rgba(239, 190, 111, 0.28);
        }

        .flatpickr-day.today:not(.selected) {
            border-color: rgba(125, 174, 255, 0.68);
            color: #dce9ff;
        }

        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.flatpickr-disabled:hover {
            color: rgba(145, 162, 193, 0.35);
            cursor: not-allowed;
            border-color: transparent;
            background: transparent;
        }

        .roll-models-panel {
            padding: 16px;
        }

        .roll-models-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .roll-models-head h2 {
            margin: 0;
            font-size: 1.26rem;
        }

        .roll-count-chip {
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.05);
            color: var(--roll-muted);
            padding: 8px 12px;
            font-size: .8rem;
            font-weight: 700;
        }

        .roll-model-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1.12fr));
            gap: 12px;
        }

        .roll-model-card {
            position: relative;
            border-radius: 14px;
            padding: 2px;
            border: none;
            background: linear-gradient(128deg, #45e2ff, #7a6cff, #ff4fd8, #ffd257, #45e2ff);
            background-size: 280% 280%;
            overflow: hidden;
            transition: transform .24s ease, border-color .24s ease, box-shadow .24s ease, filter .24s ease;
            display: block;
            color: inherit;
            text-decoration: none;
            box-shadow:
                0 0 24px rgba(69, 226, 255, 0.34),
                0 0 44px rgba(255, 79, 216, 0.28),
                0 0 58px rgba(255, 210, 87, 0.22);
            animation: roll-neon-flow 8s linear infinite;
        }

        .roll-model-card::before {
            content: "";
            position: absolute;
            inset: 2px;
            pointer-events: none;
            border-radius: 12px;
            background: rgba(8, 14, 26, 0.9);
        }

        .roll-model-card:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow:
                0 0 18px rgba(69, 226, 255, 0.28),
                0 0 32px rgba(122, 108, 255, 0.24),
                0 0 44px rgba(255, 79, 216, 0.22),
                0 0 52px rgba(255, 210, 87, 0.18);
            filter: saturate(1.12);
        }

        .roll-model-card > * {
            position: relative;
            z-index: 1;
        }

        @keyframes roll-neon-flow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .roll-model-media {
            aspect-ratio: 1 / 1;
            background: rgba(255,255,255,0.06);
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            border: none;
            box-shadow: none;
        }

        .roll-model-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .roll-model-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.2rem;
            color: #dce8ff;
            background: linear-gradient(135deg, rgba(115, 161, 246, 0.35), rgba(242, 190, 109, 0.35));
        }

        .roll-model-body {
            padding: 11px 12px 13px;
        }

        .roll-model-name {
            margin: 0;
            font-size: .95rem;
            font-weight: 700;
        }

        .roll-model-link {
            margin-top: 5px;
            color: var(--roll-muted);
            font-size: .78rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .roll-empty {
            border: 1px dashed rgba(255,255,255,0.14);
            border-radius: 14px;
            background: rgba(255,255,255,0.02);
            text-align: center;
            padding: 36px 18px;
            color: var(--roll-muted);
        }

        .roll-events-wrap {
            margin-bottom: 16px;
        }

        .roll-event-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .roll-event-card {
            width: 100%;
            border: 1px solid var(--roll-border);
            border-radius: 14px;
            overflow: hidden;
            background: rgba(255,255,255,0.03);
            color: inherit;
            text-align: left;
            padding: 0;
            cursor: pointer;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        .roll-event-card:hover {
            transform: translateY(-3px);
            border-color: rgba(239, 190, 111, 0.46);
            box-shadow: 0 18px 34px rgba(0,0,0,0.28);
        }

        .roll-event-media {
            position: relative;
            aspect-ratio: 16 / 10;
            background: #000;
            overflow: hidden;
        }

        .roll-event-media img,
        .roll-event-media video,
        .roll-event-media iframe {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border: 0;
        }

        .roll-event-badges {
            position: absolute;
            top: 10px;
            right: 10px;
            display: inline-flex;
            gap: 8px;
            align-items: center;
            z-index: 1;
        }

        .roll-event-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.18);
            background: rgba(7, 14, 26, 0.8);
            color: #fff;
            font-size: .72rem;
            padding: 5px 9px;
            font-weight: 700;
        }

        .roll-event-body {
            padding: 12px;
        }

        .roll-event-title {
            margin: 0;
            font-size: .98rem;
            font-weight: 700;
        }

        .roll-event-caption {
            margin-top: 6px;
            color: var(--roll-muted);
            line-height: 1.55;
            font-size: .86rem;
        }

        .roll-event-meta {
            margin-top: 8px;
            color: var(--roll-muted);
            font-size: .8rem;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
        }

        .roll-lightbox {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: none;
            background: rgba(5, 8, 14, 0.88);
            backdrop-filter: blur(16px);
        }

        .roll-lightbox.is-open {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .roll-lightbox-dialog {
            width: min(1080px, 100%);
            max-height: calc(100vh - 40px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            overflow: hidden;
            background: rgba(9, 15, 28, 0.96);
            box-shadow: var(--roll-shadow);
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) 340px;
        }

        .roll-lightbox-media {
            position: relative;
            min-height: 420px;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .roll-lightbox-media img,
        .roll-lightbox-media video,
        .roll-lightbox-media iframe {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #000;
            border: 0;
        }

        .roll-lightbox-side {
            display: flex;
            flex-direction: column;
            min-height: 0;
            border-left: 1px solid rgba(255,255,255,0.08);
        }

        .roll-lightbox-head,
        .roll-lightbox-body,
        .roll-lightbox-foot {
            padding: 18px 20px;
        }

        .roll-lightbox-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: start;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .roll-lightbox-body {
            overflow-y: auto;
            color: var(--roll-muted);
            line-height: 1.75;
        }

        .roll-lightbox-foot {
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            color: var(--roll-muted);
            font-size: .86rem;
        }

        .roll-lightbox-close,
        .roll-lightbox-nav {
            appearance: none;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(9, 15, 28, 0.78);
            color: #fff;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .roll-lightbox-close {
            width: 42px;
            height: 42px;
            font-size: 1.2rem;
        }

        .roll-lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 48px;
            font-size: 1.35rem;
            box-shadow: 0 12px 26px rgba(0,0,0,0.28);
        }

        .roll-lightbox-nav.prev { left: 16px; }
        .roll-lightbox-nav.next { right: 16px; }
        .roll-lightbox-nav[hidden] { display: none; }

        .roll-lightbox-comment-list {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        .roll-lightbox-comment-card {
            border-radius: 14px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(170, 205, 255, 0.14);
        }

        .roll-footer {
            margin-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.08);
            background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
        }

        .roll-footer-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            padding: 16px 0;
            font-size: 12.5px;
            color: rgba(232,234,246,0.78);
            text-align: center;
        }

        .roll-footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(232,234,246,0.94);
            font-weight: 700;
            letter-spacing: .02em;
            text-decoration: none;
        }

        .roll-footer-brand .brand-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--roll-accent);
            box-shadow: 0 0 0 5px rgba(239,190,111,0.16);
        }

        @media (max-width: 991.98px) {
            .roll-grid {
                grid-template-columns: 1fr;
            }

            .roll-calendar-panel {
                position: static;
            }

            .roll-model-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .roll-event-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .roll-shell {
                width: calc(100% - 12px);
                padding: 12px 10px 28px;
                border-radius: 18px;
            }

            .roll-topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .roll-hero {
                border-radius: 14px;
                padding: 16px;
            }

            .roll-model-grid {
                grid-template-columns: 1fr;
            }

            .roll-lightbox.is-open {
                padding: 10px;
            }

            .roll-lightbox-dialog {
                max-height: calc(100vh - 20px);
                border-radius: 14px;
                grid-template-columns: 1fr;
            }

            .roll-lightbox-side {
                border-left: 0;
                border-top: 1px solid rgba(255,255,255,0.08);
            }

            .roll-lightbox-nav {
                width: 42px;
                height: 42px;
            }
        }
    </style>
</head>
<body>
    @php
        $mediaUrl = function ($item) {
            return ($item['source'] ?? 'upload') === 'upload' ? asset('uploads/' . $item['url']) : ($item['url'] ?? '');
        };

        $embedUrl = function ($url) {
            if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $matches)) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }

            if (preg_match('~vimeo\.com/(\d+)~', $url, $matches)) {
                return 'https://player.vimeo.com/video/' . $matches[1];
            }

            return null;
        };
    @endphp

    <div class="roll-shell">
        <div class="roll-topbar">
            <div class="roll-topbar-nav">
                <a href="{{ route('club.feed.profile', $club->slug) }}" class="roll-topbar-link"><i class="fas fa-chevron-left"></i> Back To Club Profile</a>
                <a href="{{ route('club.feed', $club->slug) }}" class="roll-topbar-link"><i class="fas fa-rss"></i> Back To Feed</a>
            </div>
            <span class="roll-topbar-context">Roll Call</span>
        </div>

        <section class="roll-hero">
            <span class="roll-kicker">Club Schedule</span>
            <h1 class="roll-title">Roll Call</h1>
            <p class="roll-copy">Choose a date to see which entertainers are working at {{ $club->name }}. Select any profile card to jump straight into that entertainer's profile page.</p>
        </section>

        <section class="roll-grid">
            <aside class="roll-calendar-panel">
                <div class="roll-calendar-head">
                    <h2>Date Selector</h2>
                    <p>This calendar only enables dates where at least one entertainer is scheduled.</p>
                </div>
                <div id="rollcall-calendar"></div>
                <div class="roll-selected-date">
                    Selected Date:
                    <strong>{{ \Illuminate\Support\Carbon::parse($selectedDate)->format('l, F j, Y') }}</strong>
                </div>
            </aside>

            <div class="roll-models-panel">
                @if(isset($eventPosts) && $eventPosts->isNotEmpty())
                    <div class="roll-events-wrap">
                        <div class="roll-models-head">
                            <h2>Events</h2>
                            <span class="roll-count-chip">{{ $eventPosts->count() }} On This Date</span>
                        </div>

                        <div class="roll-event-grid">
                            @foreach($eventPosts as $eventPost)
                                @php
                                    $eventMediaItems = array_values(array_filter((array) $eventPost->resolved_media_items));
                                    $eventItem = $eventMediaItems[0] ?? null;
                                    $eventUrl = $eventItem ? $mediaUrl($eventItem) : null;
                                    $eventEmbed = $eventItem && ($eventItem['type'] ?? 'image') === 'video' ? $embedUrl($eventUrl) : null;
                                    $eventLightboxItems = collect($eventMediaItems)->map(function ($item) use ($mediaUrl, $embedUrl) {
                                        $url = $mediaUrl($item);

                                        return [
                                            'type' => $item['type'] ?? 'image',
                                            'url' => $url,
                                            'embed' => ($item['type'] ?? 'image') === 'video' ? $embedUrl($url) : null,
                                        ];
                                    })->values();
                                    $eventHasVideo = collect($eventMediaItems)->contains(function ($item) {
                                        return ($item['type'] ?? 'image') === 'video';
                                    });
                                    $eventComments = $eventPost->visibleComments->map(function ($comment) {
                                        return [
                                            'name' => $comment->commenter_name,
                                            'body' => $comment->body,
                                            'time' => $comment->created_at ? $comment->created_at->diffForHumans() : '',
                                        ];
                                    })->values();
                                @endphp
                                <button
                                    type="button"
                                    class="roll-event-card"
                                    data-lightbox-items='@json($eventLightboxItems)'
                                    data-lightbox-caption="{{ e($eventPost->caption ?? '') }}"
                                    data-lightbox-date="{{ optional($eventPost->posted_at)->format('M d, Y') }}"
                                    data-lightbox-comments="{{ $eventPost->visible_comments_count }}"
                                    data-lightbox-comment-items='@json($eventComments)'
                                    data-lightbox-author="{{ e($eventPost->author_name) }}"
                                >
                                    @if($eventItem)
                                        <div class="roll-event-media">
                                            <div class="roll-event-badges">
                                                @if(count($eventMediaItems) > 1)
                                                    <span class="roll-event-pill"><i class="fas fa-clone"></i> {{ count($eventMediaItems) }}</span>
                                                @endif
                                                @if($eventHasVideo)
                                                    <span class="roll-event-pill"><i class="fas fa-circle-play"></i> Video</span>
                                                @endif
                                            </div>
                                            @if(($eventItem['type'] ?? 'image') === 'video')
                                                @if($eventEmbed)
                                                    <iframe src="{{ $eventEmbed }}" frameborder="0" allowfullscreen allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"></iframe>
                                                @else
                                                    <video src="{{ $eventUrl }}" playsinline webkit-playsinline preload="metadata"></video>
                                                @endif
                                            @else
                                                <img src="{{ $eventUrl }}" alt="Event media">
                                            @endif
                                        </div>
                                    @endif

                                    <div class="roll-event-body">
                                        <h3 class="roll-event-title">Event Post</h3>
                                        @if($eventPost->caption)
                                            <div class="roll-event-caption">{{ \Illuminate\Support\Str::limit($eventPost->caption, 110) }}</div>
                                        @endif
                                        <div class="roll-event-meta">
                                            <span>{{ optional($eventPost->posted_at)->format('M d, Y') }}</span>
                                            <span>{{ $eventPost->visible_comments_count }} comments</span>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="roll-models-head">
                    <h2>Working Entertainers</h2>
                    <span class="roll-count-chip">{{ $workingModels->count() }} On Shift</span>
                </div>

                @if($workingModels->isNotEmpty())
                    <div class="roll-model-grid">
                        @foreach($workingModels as $model)
                            <a class="roll-model-card" href="{{ route('club.feed.model.profile', ['slug' => $club->slug, 'feedModel' => $model->id, 'from' => 'roll-call', 'date' => $selectedDate]) }}">
                                <div class="roll-model-media">
                                    @if($model->profile_image)
                                        <img src="{{ asset('uploads/' . $model->profile_image) }}" alt="{{ $model->name }}">
                                    @else
                                        <div class="roll-model-fallback">{{ strtoupper(substr($model->name, 0, 2)) }}</div>
                                    @endif
                                </div>
                                <div class="roll-model-body">
                                    <h3 class="roll-model-name">{{ $model->name }}</h3>
                                    <span class="roll-model-link">
                                        <i class="fas fa-arrow-right"></i>
                                        Open profile
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="roll-empty">
                        <h3 class="mb-2">No entertainers are scheduled</h3>
                        <p class="mb-0">Try another date on the calendar to see who is working.</p>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <footer class="roll-footer">
        <div class="roll-footer-inner">
            <a href="https://cartvip.com" target="_blank" rel="noopener" class="roll-footer-brand">
                <span class="brand-dot"></span>
                <span>Mr.RollCall.com powered by CartVIP</span>
            </a>
        </div>
    </footer>

    <div class="roll-lightbox" id="roll-lightbox" aria-hidden="true">
        <div class="roll-lightbox-dialog" role="dialog" aria-modal="true" aria-label="Event media viewer">
            <div class="roll-lightbox-media">
                <button type="button" class="roll-lightbox-nav prev" id="roll-lightbox-prev" aria-label="Previous media">&#8249;</button>
                <div id="roll-lightbox-stage" style="width:100%;height:100%;"></div>
                <button type="button" class="roll-lightbox-nav next" id="roll-lightbox-next" aria-label="Next media">&#8250;</button>
            </div>
            <div class="roll-lightbox-side">
                <div class="roll-lightbox-head">
                    <div>
                        <div style="font-weight:700;">{{ $club->name }} Roll Call</div>
                        <div id="roll-lightbox-author" style="color:var(--roll-muted);font-size:.86rem;"></div>
                    </div>
                    <button type="button" class="roll-lightbox-close" id="roll-lightbox-close" aria-label="Close viewer">&times;</button>
                </div>
                <div class="roll-lightbox-body">
                    <div id="roll-lightbox-caption"></div>
                    <div class="roll-lightbox-comment-list" id="roll-lightbox-comment-list"></div>
                </div>
                <div class="roll-lightbox-foot">
                    <span id="roll-lightbox-date"></span>
                    <span id="roll-lightbox-counter"></span>
                    <span id="roll-lightbox-comments"></span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        (function () {
            const selectedDate = @json($selectedDate);
            const availableDates = @json($availableDates);
            const lightbox = document.getElementById('roll-lightbox');
            const stage = document.getElementById('roll-lightbox-stage');
            const closeButton = document.getElementById('roll-lightbox-close');
            const prevButton = document.getElementById('roll-lightbox-prev');
            const nextButton = document.getElementById('roll-lightbox-next');
            const captionNode = document.getElementById('roll-lightbox-caption');
            const dateNode = document.getElementById('roll-lightbox-date');
            const counterNode = document.getElementById('roll-lightbox-counter');
            const commentsNode = document.getElementById('roll-lightbox-comments');
            const authorNode = document.getElementById('roll-lightbox-author');
            const commentsListNode = document.getElementById('roll-lightbox-comment-list');
            const eventCards = document.querySelectorAll('.roll-event-card');

            let currentItems = [];
            let currentIndex = 0;

            function renderMediaItem(item) {
                if (!item) {
                    stage.innerHTML = '';
                    return;
                }

                if (item.type === 'video') {
                    if (item.embed) {
                        stage.innerHTML = '<iframe src="' + item.embed + '" allowfullscreen allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"></iframe>';
                    } else {
                        stage.innerHTML = '<video src="' + item.url + '" controls autoplay playsinline webkit-playsinline preload="metadata"></video>';
                    }
                } else {
                    stage.innerHTML = '<img src="' + item.url + '" alt="Event media">';
                }

                counterNode.textContent = currentItems.length ? (currentIndex + 1) + ' / ' + currentItems.length : '';
                prevButton.hidden = currentItems.length <= 1;
                nextButton.hidden = currentItems.length <= 1;
            }

            function openLightbox(card) {
                currentItems = JSON.parse(card.getAttribute('data-lightbox-items') || '[]');
                currentIndex = 0;
                captionNode.textContent = card.getAttribute('data-lightbox-caption') || '';
                dateNode.textContent = card.getAttribute('data-lightbox-date') || '';
                commentsNode.textContent = (card.getAttribute('data-lightbox-comments') || '0') + ' comments';
                authorNode.textContent = card.getAttribute('data-lightbox-author') || '';

                const commentItems = JSON.parse(card.getAttribute('data-lightbox-comment-items') || '[]');
                if (commentItems.length) {
                    commentsListNode.innerHTML = commentItems.map(function (comment) {
                        const name = (comment.name || '').replace(/[&<>"']/g, function (char) {
                            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char];
                        });
                        const body = (comment.body || '').replace(/[&<>"']/g, function (char) {
                            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char];
                        });
                        const time = (comment.time || '').replace(/[&<>"']/g, function (char) {
                            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char];
                        });
                        return '<div class="roll-lightbox-comment-card"><div class="d-flex justify-content-between gap-3 mb-2" style="color:var(--roll-muted);font-size:.84rem;"><strong style="color:var(--roll-text);">' + name + '</strong><span>' + time + '</span></div><div style="white-space:pre-wrap;line-height:1.6;">' + body + '</div></div>';
                    }).join('');
                } else {
                    commentsListNode.innerHTML = '<div class="roll-empty py-3">No comments yet.</div>';
                }

                renderMediaItem(currentItems[currentIndex]);
                lightbox.classList.add('is-open');
                lightbox.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function closeLightbox() {
                lightbox.classList.remove('is-open');
                lightbox.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                stage.innerHTML = '';
                currentItems = [];
                currentIndex = 0;
            }

            function moveLightbox(direction) {
                if (currentItems.length <= 1) {
                    return;
                }

                currentIndex = (currentIndex + direction + currentItems.length) % currentItems.length;
                renderMediaItem(currentItems[currentIndex]);
            }

            flatpickr('#rollcall-calendar', {
                inline: true,
                defaultDate: selectedDate,
                dateFormat: 'Y-m-d',
                enable: availableDates.length ? availableDates : undefined,
                onChange: function (selectedDates, dateStr) {
                    if (!dateStr) {
                        return;
                    }

                    const url = new URL(window.location.href);
                    url.searchParams.set('date', dateStr);
                    window.location.href = url.toString();
                }
            });

            eventCards.forEach(function (card) {
                card.addEventListener('click', function () {
                    openLightbox(card);
                });
            });

            if (prevButton && nextButton && closeButton && lightbox) {
                prevButton.addEventListener('click', function () { moveLightbox(-1); });
                nextButton.addEventListener('click', function () { moveLightbox(1); });
                closeButton.addEventListener('click', closeLightbox);

                lightbox.addEventListener('click', function (event) {
                    if (event.target === lightbox) {
                        closeLightbox();
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (!lightbox.classList.contains('is-open')) {
                        return;
                    }

                    if (event.key === 'Escape') {
                        closeLightbox();
                    }

                    if (event.key === 'ArrowLeft') {
                        moveLightbox(-1);
                    }

                    if (event.key === 'ArrowRight') {
                        moveLightbox(1);
                    }
                });
            }
        })();
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $club ? $club->name . ' Feed' : 'Club Feed Directory' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --feed-bg: #080d18;
            --feed-bg-edge: #050914;
            --feed-bg-center: #132646;
            --feed-bg-soft: rgba(255,255,255,0.05);
            --feed-stage: rgba(9, 15, 30, 0.78);
            --feed-stage-border: rgba(170, 205, 255, 0.18);
            --feed-card: rgba(10, 16, 30, 0.94);
            --feed-border: rgba(255,255,255,0.08);
            --feed-text: #ecf2ff;
            --feed-muted: #92a2c1;
            --feed-accent: #d7ae64;
            --feed-accent-soft: #f2d7a4;
            --feed-shadow: 0 26px 70px rgba(0, 0, 0, 0.42);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Poppins", "Segoe UI", sans-serif;
            color: var(--feed-text);
            background:
                radial-gradient(circle at 18% -12%, rgba(216, 174, 100, 0.18), transparent 26%),
                radial-gradient(circle at 82% 112%, rgba(129, 170, 255, 0.2), transparent 30%),
                linear-gradient(90deg, #03070f 0%, #091122 18%, #183b67 50%, #091122 82%, #03070f 100%),
                linear-gradient(180deg, #081121 0%, var(--feed-bg) 100%);
            background-attachment: fixed;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(90deg, rgba(3, 6, 14, 0.88) 0%, rgba(4, 10, 21, 0.58) 18%, rgba(18, 39, 72, 0.08) 34%, rgba(39, 83, 151, 0.12) 50%, rgba(18, 39, 72, 0.08) 66%, rgba(4, 10, 21, 0.58) 82%, rgba(3, 6, 14, 0.88) 100%);
            z-index: 0;
        }

        body::after {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(70% 52% at 50% 14%, rgba(122, 168, 255, 0.16), transparent 58%),
                radial-gradient(64% 58% at 50% 64%, rgba(62, 120, 220, 0.14), transparent 66%);
            z-index: 0;
        }

        a { color: inherit; text-decoration: none; }

        .feed-shell {
            width: min(860px, calc(100% - 24px));
            margin: 0 auto;
            padding: 18px 18px 48px;
            position: relative;
            z-index: 1;
            border: 1px solid var(--feed-stage-border);
            border-radius: 28px;
            background:
                linear-gradient(180deg, rgba(16, 33, 59, 0.62) 0%, rgba(8, 14, 28, 0.72) 24%, rgba(7, 13, 26, 0.84) 100%),
                var(--feed-stage);
            backdrop-filter: blur(8px);
            box-shadow: 0 26px 84px rgba(0, 0, 0, 0.44);
            overflow: hidden;
        }

        .feed-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
            color: var(--feed-muted);
        }

        .feed-topbar a:hover { color: var(--feed-accent-soft); }

        .feed-search-shell {
            margin: 0 0 18px;
            padding: 12px;
            border: 1px solid var(--feed-border);
            border-radius: 20px;
            background: rgba(8, 14, 28, 0.68);
            box-shadow: var(--feed-shadow);
        }

        .club-hero {
            border: 1px solid var(--feed-border);
            border-radius: 20px;
            padding: 24px;
            background:
                linear-gradient(145deg, rgba(14, 23, 42, 0.96), rgba(8, 13, 24, 0.9)),
                radial-gradient(circle at top right, rgba(215,174,100,0.18), transparent 30%);
            box-shadow: var(--feed-shadow);
            margin-bottom: 18px;
        }

        .club-hero-head {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .club-avatar,
        .club-avatar-fallback {
            width: 78px;
            height: 78px;
            border-radius: 22px;
            object-fit: cover;
            background: rgba(255,255,255,0.06);
            flex-shrink: 0;
        }

        .club-avatar-fallback {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: var(--feed-accent-soft);
            font-size: 1.35rem;
        }

        .club-name {
            font-size: clamp(2rem, 5vw, 3.4rem);
            line-height: .95;
            letter-spacing: -0.04em;
            margin: 0;
            font-weight: 800;
        }

        .club-copy {
            color: var(--feed-muted);
            margin: 8px 0 0;
            max-width: 58ch;
            line-height: 1.7;
        }

        .feed-search {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 10px;
            align-items: center;
        }

        .feed-hero-actions {
            margin-top: 14px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .feed-input,
        .feed-textarea {
            width: 100%;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.05);
            color: var(--feed-text);
            border-radius: 18px;
            padding: 14px 16px;
            outline: none;
        }

        .feed-textarea {
            resize: vertical;
            min-height: 120px;
        }

        .feed-btn,
        .feed-btn-secondary {
            border: 0;
            border-radius: 999px;
            padding: 10px 16px;
            font-weight: 700;
            transition: transform .18s ease, opacity .18s ease;
        }

        .feed-btn {
            background: linear-gradient(135deg, #f2ddb8 0%, #d7ae64 64%, #b68334 100%);
            color: #1d1506;
            box-shadow: 0 16px 30px rgba(215,174,100,0.22);
        }

        .feed-btn-secondary {
            background: rgba(255,255,255,0.06);
            color: var(--feed-text);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .feed-btn:hover,
        .feed-btn-secondary:hover {
            transform: translateY(-1px);
            opacity: .94;
        }

        .feed-reset-btn {
            white-space: nowrap;
        }

        .feed-search-btn {
            min-width: 112px;
            justify-self: end;
            box-shadow: 0 10px 22px rgba(215,174,100,0.18);
        }

        .feed-card {
            border: 1px solid var(--feed-border);
            border-radius: 16px;
            overflow: hidden;
            background: var(--feed-card);
            box-shadow: var(--feed-shadow);
            margin-bottom: 22px;
        }

        .feed-card-head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
        }

        .feed-avatar,
        .feed-avatar-fallback {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .feed-avatar-fallback {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.08);
            color: var(--feed-accent-soft);
            font-weight: 700;
        }

        .feed-author-name {
            font-weight: 700;
            font-size: .98rem;
        }

        .feed-author-meta {
            color: var(--feed-muted);
            font-size: .82rem;
        }

        .feed-author-link,
        .feed-club-link {
            transition: color .18s ease;
        }

        .feed-author-link:hover,
        .feed-club-link:hover {
            color: #fff;
        }

        .feed-header-spacer {
            margin-left: auto;
        }

        .feed-club-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            color: var(--feed-muted);
            font-size: .78rem;
            font-weight: 700;
        }

        .feed-card-body {
            padding: 0 0 18px;
        }

        .feed-caption {
            padding: 14px 18px 0;
            white-space: pre-wrap;
            line-height: 1.7;
        }

        .feed-media,
        .feed-media img,
        .feed-media video,
        .feed-carousel .carousel-item img,
        .feed-carousel .carousel-item video,
        .feed-embed {
            width: 100%;
            display: block;
        }

        .feed-media img,
        .feed-media video,
        .feed-carousel .carousel-item img,
        .feed-carousel .carousel-item video,
        .feed-embed {
            height: min(74vh, 820px);
            object-fit: cover;
            background: #000;
        }

        .feed-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 16px 18px 0;
            color: var(--feed-muted);
            font-size: .86rem;
        }

        .feed-carousel .carousel-control-prev,
        .feed-carousel .carousel-control-next {
            width: 56px;
            opacity: 1;
        }

        .feed-carousel .carousel-control-prev-icon,
        .feed-carousel .carousel-control-next-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: rgba(9, 15, 28, 0.82);
            background-size: 48% 48%;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.24);
            border: 1px solid rgba(255,255,255,0.14);
            backdrop-filter: blur(10px);
        }

        .feed-comments-preview {
            padding: 14px 18px 0;
            display: grid;
            gap: 8px;
        }

        .feed-comment-inline {
            color: var(--feed-muted);
            line-height: 1.55;
        }

        .feed-comment-inline strong {
            color: var(--feed-text);
        }

        .club-directory {
            display: grid;
            gap: 14px;
        }

        .club-card {
            display: flex;
            gap: 14px;
            align-items: center;
            border: 1px solid var(--feed-border);
            border-radius: 24px;
            background: rgba(255,255,255,0.04);
            padding: 18px;
            box-shadow: var(--feed-shadow);
        }

        .feed-empty {
            border: 1px dashed rgba(255,255,255,0.12);
            border-radius: 16px;
            padding: 40px 24px;
            text-align: center;
            color: var(--feed-muted);
            background: rgba(255,255,255,0.03);
        }

        .feed-media-indicators {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .feed-indicator-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.14);
            background: rgba(9, 15, 28, 0.6);
            color: #fff;
            font-size: .82rem;
        }

        .feed-media-launch {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 38px;
            height: 38px;
            border: 1px solid rgba(255,255,255,0.16);
            background: rgba(9, 15, 28, 0.74);
            color: #fff;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 5;
        }

        .feed-media-wrap {
            position: relative;
            cursor: zoom-in;
        }

        .feed-lightbox {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: none;
            background: rgba(5, 8, 14, 0.88);
            backdrop-filter: blur(16px);
        }

        .feed-lightbox.is-open {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .feed-lightbox-dialog {
            width: min(1080px, 100%);
            max-height: calc(100vh - 40px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            overflow: hidden;
            background: rgba(9, 15, 28, 0.96);
            box-shadow: var(--feed-shadow);
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) 340px;
        }

        .feed-lightbox-media {
            position: relative;
            min-height: 420px;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .feed-lightbox-media img,
        .feed-lightbox-media video,
        .feed-lightbox-media iframe {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #000;
            border: 0;
        }

        .feed-lightbox-side {
            display: flex;
            flex-direction: column;
            min-height: 0;
            border-left: 1px solid rgba(255,255,255,0.08);
        }

        .feed-lightbox-head,
        .feed-lightbox-body,
        .feed-lightbox-foot {
            padding: 18px 20px;
        }

        .feed-lightbox-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: start;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .feed-lightbox-body {
            overflow-y: auto;
            color: var(--feed-muted);
            line-height: 1.75;
        }

        .feed-lightbox-foot {
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            color: var(--feed-muted);
        }

        .feed-lightbox-close,
        .feed-lightbox-nav {
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

        .feed-lightbox-close {
            width: 42px;
            height: 42px;
            font-size: 1.2rem;
        }

        .feed-lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 48px;
            font-size: 1.35rem;
            box-shadow: 0 12px 26px rgba(0,0,0,0.28);
        }

        .feed-lightbox-nav.prev { left: 16px; }
        .feed-lightbox-nav.next { right: 16px; }
        .feed-lightbox-nav[hidden] { display: none; }

        .feed-modal .modal-content {
            background:
                linear-gradient(180deg, rgba(24, 38, 64, 0.96) 0%, rgba(14, 24, 44, 0.96) 100%),
                radial-gradient(circle at top right, rgba(215, 174, 100, 0.16), transparent 36%);
            color: var(--feed-text);
            border: 1px solid rgba(170, 205, 255, 0.2);
            border-radius: 24px;
            box-shadow: 0 22px 50px rgba(8, 15, 28, 0.32);
        }

        .feed-modal .modal-header,
        .feed-modal .modal-footer {
            border-color: rgba(170, 205, 255, 0.16);
        }

        .feed-modal .modal-title,
        .feed-modal .text-white,
        .feed-modal label,
        .feed-modal .form-label {
            color: var(--feed-text) !important;
        }

        .feed-modal .btn-close {
            filter: invert(1) brightness(1.05);
        }

        .feed-modal small {
            color: var(--feed-muted) !important;
        }

        .feed-modal .feed-input,
        .feed-modal .feed-textarea {
            background: rgba(255, 255, 255, 0.06);
            color: var(--feed-text);
            border: 1px solid rgba(170, 205, 255, 0.18);
        }

        .feed-modal .feed-input::placeholder,
        .feed-modal .feed-textarea::placeholder {
            color: rgba(211, 223, 243, 0.72);
        }

        .feed-comment-list {
            display: grid;
            gap: 12px;
            max-height: 320px;
            overflow-y: auto;
            padding-right: 6px;
            margin-bottom: 16px;
        }

        .feed-comment-card {
            border-radius: 18px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(170, 205, 255, 0.14);
        }

        .feed-pagination .pagination {
            justify-content: center;
            gap: 8px;
            margin-top: 28px;
        }

        .feed-pagination .page-link {
            background: rgba(255,255,255,0.05);
            color: var(--feed-text);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
        }

        .feed-footer {
            margin-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.18);
            background: rgba(7, 12, 24, 0.94);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.06), 0 -16px 30px rgba(0,0,0,0.25);
        }

        .feed-footer-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            padding: 17px 0;
            font-size: 12.5px;
            color: rgba(240,244,255,0.95);
            text-align: center;
        }

        .feed-footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #f6f8ff;
            font-weight: 700;
            letter-spacing: .02em;
            text-decoration: none;
        }

        .feed-footer-brand .brand-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--feed-accent);
            box-shadow: 0 0 0 5px rgba(215,174,100,0.24);
        }

        @media (max-width: 767.98px) {
            .feed-shell {
                width: calc(100% - 12px);
                padding: 12px 10px 28px;
                border-radius: 18px;
                border-color: rgba(161, 196, 248, 0.2);
            }

            .feed-topbar,
            .club-hero,
            .feed-empty {
                width: 100%;
                margin-left: auto;
                margin-right: auto;
            }

            .club-hero {
                margin-top: 10px;
                border-radius: 16px;
                padding: 18px;
            }

            .feed-search {
                grid-template-columns: 1fr;
            }

            .feed-search-btn {
                width: 100%;
                justify-self: stretch;
            }

            .feed-card {
                border-radius: 10px;
                margin-bottom: 14px;
            }

            .feed-lightbox.is-open {
                padding: 10px;
            }

            .feed-lightbox-dialog {
                max-height: calc(100vh - 20px);
                border-radius: 14px;
                grid-template-columns: 1fr;
            }

            .feed-lightbox-side {
                border-left: 0;
                border-top: 1px solid rgba(255,255,255,0.08);
            }

            .feed-lightbox-nav {
                width: 42px;
                height: 42px;
            }

            .feed-media img,
            .feed-media video,
            .feed-carousel .carousel-item img,
            .feed-carousel .carousel-item video,
            .feed-embed {
                height: min(78vh, 560px);
            }

            .feed-card-head {
                padding: 14px 14px 12px;
            }

            .feed-caption,
            .feed-meta-row,
            .feed-comments-preview {
                padding-left: 14px;
                padding-right: 14px;
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

    <div class="feed-shell">
        <div class="feed-topbar">
            <div>
                @if($club)
                    <span style="letter-spacing:.14em;text-transform:uppercase;font-size:.75rem;">{{ $club->name }} Feed</span>
                @else
                    <span style="letter-spacing:.14em;text-transform:uppercase;font-size:.75rem;">CartVIP Feed Directory</span>
                @endif
            </div>
            <a href="{{ $club ? url($club->slug) : url('/') }}">Back to Checkout</a>
        </div>

        @if($club)
            <section class="feed-search-shell" aria-label="Feed search">
                <form method="GET" action="{{ route('club.feed', $club->slug) }}" class="feed-search" id="feed-search-form">
                    <input class="feed-input" type="text" name="q" value="{{ $query }}" placeholder="Search posts or entertainer names">
                    <button type="submit" class="feed-btn feed-search-btn">Search</button>
                </form>
            </section>
        @endif

        @if(session('success'))
            <div class="alert alert-success border-0" style="width:calc(100% - 16px);margin:0 auto 16px;background:rgba(34,197,94,0.15);color:#d9ffe7;border-radius:18px;">{{ session('success') }}</div>
        @endif

        @if(!$club)
            <section class="club-hero">
                <h1 class="club-name">Choose a club feed</h1>
                <p class="club-copy">Each club now has its own dedicated feed page at /club-slug/feed. Open one of the clubs below to browse its posts.</p>
            </section>

            <section class="club-directory">
                @forelse($websites as $website)
                    <a href="{{ route('club.feed', $website->slug) }}" class="club-card">
                        @if($website->logo)
                            <img src="{{ asset('uploads/' . $website->logo) }}" alt="{{ $website->name }}" class="club-avatar">
                        @else
                            <div class="club-avatar-fallback">{{ strtoupper(substr($website->name, 0, 2)) }}</div>
                        @endif
                        <div>
                            <div class="fw-semibold" style="font-size:1.1rem;">{{ $website->name }}</div>
                            <div class="text-muted">Open {{ $website->name }}'s feed</div>
                        </div>
                    </a>
                @empty
                    <div class="feed-empty">No club feeds are live yet.</div>
                @endforelse
            </section>
        @else
            <section class="club-hero">
                <div class="club-hero-head">
                    @if($club->logo)
                        <img src="{{ asset('uploads/' . $club->logo) }}" alt="{{ $club->name }}" class="club-avatar">
                    @else
                        <div class="club-avatar-fallback">{{ strtoupper(substr($club->name, 0, 2)) }}</div>
                    @endif
                    <div>
                        <h1 class="club-name">{{ $club->name }}</h1>
                        <p class="club-copy">{{ $club->hero_subtitle ?: $club->description ?: 'A focused stream of club media updates, spotlight posts, and community conversation.' }}</p>
                    </div>
                </div>

                <div class="feed-hero-actions">
                    <a href="{{ route('club.feed.profile', $club->slug) }}" class="feed-btn-secondary">Open Club Profile</a>
                </div>
            </section>

            @if($posts->count())
                <div id="feed-post-list">
                @foreach($posts as $post)
                    @php
                        $mediaItems = array_values(array_filter((array) $post->resolved_media_items));
                        $commentModalId = 'commentModal-' . $post->id;
                        $lightboxItems = collect($mediaItems)->map(function ($item) use ($mediaUrl, $embedUrl) {
                            $url = $mediaUrl($item);

                            return [
                                'type' => $item['type'] ?? 'image',
                                'url' => $url,
                                'embed' => ($item['type'] ?? 'image') === 'video' ? $embedUrl($url) : null,
                            ];
                        })->values();
                        $hasVideo = collect($mediaItems)->contains(function ($item) {
                            return ($item['type'] ?? 'image') === 'video';
                        });
                        $lightboxComments = $post->visibleComments->map(function ($comment) {
                            return [
                                'name' => $comment->commenter_name,
                                'body' => $comment->body,
                                'time' => $comment->created_at ? $comment->created_at->diffForHumans() : '',
                            ];
                        })->values();
                    @endphp
                    <article
                        class="feed-card feed-post-media"
                        id="post-{{ $post->id }}"
                        data-lightbox-items='@json($lightboxItems)'
                        data-lightbox-caption="{{ $post->caption ?? '' }}"
                        data-lightbox-date="{{ optional($post->posted_at)->format('M d, Y') }}"
                        data-lightbox-comments="{{ $post->visible_comments_count }}"
                        data-lightbox-comment-items='@json($lightboxComments)'
                        data-lightbox-author="{{ $post->author_name }}"
                    >
                        <div class="feed-card-head">
                            @if($post->author_avatar)
                                <img class="feed-avatar" src="{{ asset('uploads/' . $post->author_avatar) }}" alt="{{ $post->author_name }}">
                            @else
                                <div class="feed-avatar-fallback">{{ strtoupper(substr($post->author_name ?? 'FM', 0, 2)) }}</div>
                            @endif
                            <div>
                                <div class="feed-author-name">
                                    @if($post->author_mode === 'model' && $post->feedModel)
                                        <a class="feed-author-link" href="{{ route('club.feed.model.profile', ['slug' => $club->slug, 'feedModel' => $post->feedModel]) }}">{{ $post->author_name }}</a>
                                    @else
                                        <a class="feed-author-link" href="{{ route('club.feed.profile', $club->slug) }}">{{ $post->author_name }}</a>
                                    @endif
                                </div>
                                <div class="feed-author-meta">{{ optional($post->posted_at)->diffForHumans() }}</div>
                            </div>
                            <div class="feed-header-spacer"></div>
                            <a class="feed-club-chip feed-club-link" href="{{ route('club.feed.profile', $club->slug) }}">{{ $club->name }}</a>
                        </div>

                        <div class="feed-card-body">
                            @if(count($mediaItems) > 1)
                                <div class="feed-media-wrap" data-feed-open-media>
                                    @if(count($mediaItems) > 1 || $hasVideo)
                                        <div class="feed-media-launch feed-open-media-trigger" aria-hidden="true">
                                            @if(count($mediaItems) > 1)
                                                <span class="feed-indicator-icon" title="Multiple media"><i class="fas fa-clone"></i></span>
                                            @endif
                                            @if($hasVideo)
                                                <span class="feed-indicator-icon" title="Contains video"><i class="fas fa-circle-play"></i></span>
                                            @endif
                                        </div>
                                    @endif
                                <div id="feed-carousel-{{ $post->id }}" class="carousel slide feed-carousel" data-bs-ride="false">
                                    <div class="carousel-inner">
                                        @foreach($mediaItems as $index => $item)
                                            @php
                                                $currentUrl = $mediaUrl($item);
                                                $currentEmbed = ($item['type'] ?? 'image') === 'video' ? $embedUrl($currentUrl) : null;
                                            @endphp
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                @if(($item['type'] ?? 'image') === 'video')
                                                    @if($currentEmbed)
                                                        <iframe class="feed-embed" src="{{ $currentEmbed }}" frameborder="0" allowfullscreen allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"></iframe>
                                                    @else
                                                        <video class="feed-open-media-trigger" src="{{ $currentUrl }}" muted autoplay loop playsinline webkit-playsinline preload="auto" controls></video>
                                                    @endif
                                                @else
                                                    <img class="feed-open-media-trigger" src="{{ $currentUrl }}" alt="{{ $post->author_name }} media">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#feed-carousel-{{ $post->id }}" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#feed-carousel-{{ $post->id }}" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                </div>
                                </div>
                            @elseif(!empty($mediaItems))
                                @php
                                    $item = $mediaItems[0];
                                    $currentUrl = $mediaUrl($item);
                                    $currentEmbed = ($item['type'] ?? 'image') === 'video' ? $embedUrl($currentUrl) : null;
                                @endphp
                                <div class="feed-media-wrap" data-feed-open-media>
                                    @if(count($mediaItems) > 1 || $hasVideo)
                                        <div class="feed-media-launch feed-open-media-trigger" aria-hidden="true">
                                            @if(count($mediaItems) > 1)
                                                <span class="feed-indicator-icon" title="Multiple media"><i class="fas fa-clone"></i></span>
                                            @endif
                                            @if($hasVideo)
                                                <span class="feed-indicator-icon" title="Contains video"><i class="fas fa-circle-play"></i></span>
                                            @endif
                                        </div>
                                    @endif
                                <div class="feed-media">
                                    @if(($item['type'] ?? 'image') === 'video')
                                        @if($currentEmbed)
                                            <iframe class="feed-embed" src="{{ $currentEmbed }}" frameborder="0" allowfullscreen allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"></iframe>
                                        @else
                                            <video class="feed-open-media-trigger" src="{{ $currentUrl }}" muted autoplay loop playsinline webkit-playsinline preload="auto" controls></video>
                                        @endif
                                    @else
                                        <img class="feed-open-media-trigger" src="{{ $currentUrl }}" alt="{{ $post->author_name }} media">
                                    @endif
                                </div>
                                </div>
                            @endif

                            @if($post->caption)
                                <div class="feed-caption">{!! nl2br(e($post->caption)) !!}</div>
                            @endif

                            <div class="feed-meta-row">
                                <span class="feed-media-indicators">
                                    @if(count($mediaItems) > 1)
                                        <span class="feed-indicator-icon" title="Multiple media"><i class="fas fa-clone"></i></span>
                                    @endif
                                    @if($hasVideo)
                                        <span class="feed-indicator-icon" title="Contains video"><i class="fas fa-circle-play"></i></span>
                                    @endif
                                </span>
                                <button type="button" class="feed-btn-secondary" data-bs-toggle="modal" data-bs-target="#{{ $commentModalId }}">
                                    <i class="fas fa-comment-dots me-2"></i>{{ $post->visible_comments_count }} Comments
                                </button>
                            </div>

                            @if($post->visibleComments->count())
                                <div class="feed-comments-preview">
                                    @foreach($post->visibleComments->take(2) as $comment)
                                        <div class="feed-comment-inline"><strong>{{ $comment->commenter_name }}</strong> {{ $comment->body }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
                </div>

                <div class="feed-pagination" id="feed-pagination" data-next-url="{{ method_exists($posts, 'nextPageUrl') ? $posts->nextPageUrl() : '' }}">
                    {{ $posts->links() }}
                </div>
                <div id="feed-infinite-sentinel" style="height:1px;"></div>
            @else
                <div class="feed-empty">
                    <h3 class="mb-2">No posts match your search</h3>
                    <p class="mb-0">Try another keyword or clear the search to browse the full club feed.</p>
                </div>
            @endif
        @endif
    </div>

    <footer class="feed-footer">
        <div class="feed-footer-inner">
            <a href="https://cartvip.com" target="_blank" rel="noopener" class="feed-footer-brand">
                <span class="brand-dot"></span>
                <span>Mr.RollCall.com powered by CartVIP</span>
            </a>
        </div>
    </footer>

    @if(isset($posts) && $posts->count() > 0)
        @foreach($posts as $post)
            @php $commentModalId = 'commentModal-' . $post->id; @endphp
            <div class="modal fade feed-modal" id="{{ $commentModalId }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title mb-1">Comments for {{ $post->author_name }}</h5>
                                <small style="color:var(--feed-muted);">Join the conversation for this post.</small>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="feed-comment-list">
                                @forelse($post->visibleComments as $comment)
                                    <div class="feed-comment-card">
                                        <div class="d-flex justify-content-between gap-3 mb-2" style="color:var(--feed-muted);font-size:.84rem;">
                                            <strong style="color:var(--feed-text);">{{ $comment->commenter_name }}</strong>
                                            <span>{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div style="white-space:pre-wrap;line-height:1.6;">{{ $comment->body }}</div>
                                    </div>
                                @empty
                                    <div class="feed-empty py-4">No comments yet. Be the first to comment.</div>
                                @endforelse
                            </div>

                            <form method="POST" action="{{ route('feed.comments.store', $post) }}">
                                @csrf
                                <input type="hidden" name="q" value="{{ $query }}">
                                <input type="text" name="comment_hp" value="" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;opacity:0;pointer-events:none;">
                                <input type="hidden" name="comment_form_ts" value="{{ now()->timestamp }}">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-white">Name</label>
                                        <input class="feed-input" type="text" name="commenter_name" placeholder="Your name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-white">Email (optional)</label>
                                        <input class="feed-input" type="email" name="commenter_email" placeholder="name@example.com">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-white">Comment</label>
                                    <textarea class="feed-textarea" name="body" placeholder="Share your thoughts on this post" required></textarea>
                                </div>
                                <button type="submit" class="feed-btn">Post Comment</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="feed-lightbox" id="feed-lightbox" aria-hidden="true">
        <div class="feed-lightbox-dialog" role="dialog" aria-modal="true" aria-label="Media viewer">
            <div class="feed-lightbox-media">
                <button type="button" class="feed-lightbox-nav prev" id="feed-lightbox-prev" aria-label="Previous media">&#8249;</button>
                <div id="feed-lightbox-stage" style="width:100%;height:100%;"></div>
                <button type="button" class="feed-lightbox-nav next" id="feed-lightbox-next" aria-label="Next media">&#8250;</button>
            </div>
            <div class="feed-lightbox-side">
                <div class="feed-lightbox-head">
                    <div>
                        <div style="font-weight:700;">{{ $club->name ?? 'Club Feed' }}</div>
                        <div id="feed-lightbox-author" style="color:var(--feed-muted);font-size:.86rem;"></div>
                    </div>
                    <button type="button" class="feed-lightbox-close" id="feed-lightbox-close" aria-label="Close viewer">&times;</button>
                </div>
                <div class="feed-lightbox-body">
                    <div id="feed-lightbox-caption"></div>
                    <div class="feed-comment-list mt-3" id="feed-lightbox-comment-list"></div>
                </div>
                <div class="feed-lightbox-foot">
                    <span id="feed-lightbox-date"></span>
                    <span id="feed-lightbox-counter"></span>
                    <span id="feed-lightbox-comments"></span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const isIOSDevice = /iPad|iPhone|iPod/.test(navigator.userAgent)
            || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

        function restartLoopingVideos(scope) {
            if (!isIOSDevice) {
                return;
            }

            const root = scope || document;
            const videos = root.querySelectorAll('video');

            videos.forEach(function (video) {
                if (video.closest('.feed-lightbox')) {
                    return;
                }

                video.muted = true;
                video.defaultMuted = true;
                video.autoplay = true;
                video.loop = true;
                video.playsInline = true;
                video.preload = 'auto';
                video.setAttribute('muted', 'muted');
                video.setAttribute('autoplay', 'autoplay');
                video.setAttribute('loop', 'loop');
                video.setAttribute('playsinline', 'playsinline');
                video.setAttribute('webkit-playsinline', 'webkit-playsinline');

                try {
                    video.load();
                } catch (error) {
                    // Ignore reload errors and attempt playback anyway.
                }

                const playPromise = video.play();
                if (playPromise && typeof playPromise.catch === 'function') {
                    playPromise.catch(function () {});
                }
            });
        }

        const lightbox = document.getElementById('feed-lightbox');
        const stage = document.getElementById('feed-lightbox-stage');
        const closeButton = document.getElementById('feed-lightbox-close');
        const prevButton = document.getElementById('feed-lightbox-prev');
        const nextButton = document.getElementById('feed-lightbox-next');
        const captionNode = document.getElementById('feed-lightbox-caption');
        const dateNode = document.getElementById('feed-lightbox-date');
        const counterNode = document.getElementById('feed-lightbox-counter');
        const commentsNode = document.getElementById('feed-lightbox-comments');
        const authorNode = document.getElementById('feed-lightbox-author');
        const commentsListNode = document.getElementById('feed-lightbox-comment-list');
        const postList = document.getElementById('feed-post-list');
        const pagination = document.getElementById('feed-pagination');
        const sentinel = document.getElementById('feed-infinite-sentinel');

        if (!lightbox) {
            return;
        }

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
                stage.innerHTML = '<img src="' + item.url + '" alt="Feed media">';
            }

            counterNode.textContent = currentItems.length ? (currentIndex + 1) + ' / ' + currentItems.length : '';
            prevButton.hidden = currentItems.length <= 1;
            nextButton.hidden = currentItems.length <= 1;
        }

        function openLightbox(postElement) {
            currentItems = JSON.parse(postElement.getAttribute('data-lightbox-items') || '[]');
            currentIndex = 0;
            captionNode.textContent = postElement.getAttribute('data-lightbox-caption') || '';
            dateNode.textContent = postElement.getAttribute('data-lightbox-date') || '';
            commentsNode.textContent = (postElement.getAttribute('data-lightbox-comments') || '0') + ' comments';
            authorNode.textContent = postElement.getAttribute('data-lightbox-author') || '';
            const commentItems = JSON.parse(postElement.getAttribute('data-lightbox-comment-items') || '[]');
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
                    return '<div class="feed-comment-card"><div class="d-flex justify-content-between gap-3 mb-2" style="color:var(--feed-muted);font-size:.84rem;"><strong style="color:var(--feed-text);">' + name + '</strong><span>' + time + '</span></div><div style="white-space:pre-wrap;line-height:1.6;">' + body + '</div></div>';
                }).join('');
            } else {
                commentsListNode.innerHTML = '<div class="feed-empty py-3">No comments yet.</div>';
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

        document.addEventListener('click', function (event) {
            const wrap = event.target.closest('[data-feed-open-media]');
            if (!wrap) {
                return;
            }
            if (event.target.closest('.carousel-control-prev, .carousel-control-next')) {
                return;
            }
            event.preventDefault();
            event.stopPropagation();
            const post = wrap.closest('.feed-post-media');
            if (post) {
                openLightbox(post);
            }
        });

        async function loadMorePosts() {
            if (!postList || !pagination || !sentinel) {
                return;
            }

            const nextUrl = pagination.dataset.nextUrl;
            if (!nextUrl || pagination.dataset.loading === '1') {
                return;
            }

            pagination.dataset.loading = '1';

            try {
                const response = await fetch(nextUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load more posts');
                }

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const incomingList = doc.getElementById('feed-post-list');
                const incomingPagination = doc.getElementById('feed-pagination');

                if (incomingList) {
                    Array.from(incomingList.children).forEach(function (child) {
                        postList.appendChild(child);
                    });
                    restartLoopingVideos(postList);
                }

                if (incomingPagination) {
                    pagination.innerHTML = incomingPagination.innerHTML;
                    pagination.dataset.nextUrl = incomingPagination.dataset.nextUrl || '';
                } else {
                    pagination.dataset.nextUrl = '';
                }

                if (!pagination.dataset.nextUrl && sentinel.parentNode) {
                    sentinel.parentNode.removeChild(sentinel);
                }
            } catch (error) {
                console.error(error);
            } finally {
                pagination.dataset.loading = '0';
            }
        }

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

        if (postList && pagination && sentinel && pagination.dataset.nextUrl) {
            pagination.style.display = 'none';
            const observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        loadMorePosts();
                    }
                });
            }, {
                rootMargin: '450px 0px'
            });
            observer.observe(sentinel);
        }

        restartLoopingVideos(document);

        window.addEventListener('pageshow', function () {
            restartLoopingVideos(document);
        });

        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') {
                restartLoopingVideos(document);
            }
        });
    });
    </script>
</body>
</html>
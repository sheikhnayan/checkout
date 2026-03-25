<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $profileTitle }} Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css">
    <style>
        :root {
            --profile-bg: #08101d;
            --profile-panel: rgba(11, 18, 32, 0.94);
            --profile-border: rgba(255, 255, 255, 0.09);
            --profile-text: #ecf3ff;
            --profile-muted: #91a2c1;
            --profile-accent: #d8b067;
            --profile-shadow: 0 30px 80px rgba(0, 0, 0, 0.42);
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Poppins", "Segoe UI", sans-serif;
            color: var(--profile-text);
            background:
                radial-gradient(circle at top left, rgba(216,176,103,0.12), transparent 22%),
                radial-gradient(circle at top right, rgba(91,126,255,0.10), transparent 24%),
                linear-gradient(180deg, #09111f 0%, var(--profile-bg) 100%);
        }

        button,
        a {
            color: inherit;
            text-decoration: none;
        }

        .profile-shell {
            width: min(1080px, calc(100% - 24px));
            margin: 0 auto;
            padding: 28px 0 52px;
        }

        .profile-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            color: var(--profile-muted);
            margin-bottom: 18px;
        }

        .profile-topbar a:hover { color: #fff; }

        .profile-hero {
            border: 1px solid var(--profile-border);
            border-radius: 30px;
            background: linear-gradient(145deg, rgba(15, 24, 42, 0.98), rgba(8, 14, 25, 0.9));
            box-shadow: var(--profile-shadow);
            padding: 28px;
            margin-bottom: 18px;
        }

        .profile-hero-grid {
            display: grid;
            grid-template-columns: 146px minmax(0, 1fr);
            gap: 22px;
            align-items: center;
        }

        .profile-avatar,
        .profile-avatar-fallback {
            width: 146px;
            height: 146px;
            border-radius: 36px;
            object-fit: cover;
            background: rgba(255,255,255,0.06);
        }

        .profile-avatar-fallback {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            font-weight: 800;
            color: #fff;
        }

        .profile-kicker {
            font-size: .76rem;
            text-transform: uppercase;
            letter-spacing: .16em;
            color: var(--profile-accent);
            margin-bottom: 10px;
        }

        .profile-title {
            font-size: clamp(2rem, 4vw, 3.6rem);
            line-height: .95;
            margin: 0;
            letter-spacing: -0.04em;
            font-weight: 800;
        }

        .profile-copy {
            margin: 10px 0 0;
            max-width: 68ch;
            line-height: 1.75;
            color: var(--profile-muted);
            white-space: pre-wrap;
        }

        .profile-sticky-bar {
            position: sticky;
            top: 12px;
            z-index: 12;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 18px;
            padding: 14px 16px;
            border: 1px solid var(--profile-border);
            border-radius: 22px;
            background: rgba(8, 13, 24, 0.78);
            box-shadow: var(--profile-shadow);
            backdrop-filter: blur(16px);
        }

        .profile-counters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .profile-counter {
            min-width: 120px;
            padding: 10px 14px;
            border-radius: 18px;
            border: 1px solid var(--profile-border);
            background: rgba(255,255,255,0.04);
        }

        .profile-counter strong {
            display: block;
            font-size: 1.1rem;
            color: #fff;
        }

        .profile-counter span {
            color: var(--profile-muted);
            font-size: .76rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .profile-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .profile-tab {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 10px 16px;
            font-weight: 700;
            border: 1px solid var(--profile-border);
            background: rgba(255,255,255,0.04);
            color: var(--profile-text);
        }

        .profile-tab:hover,
        .profile-tab.active {
            background: rgba(216,176,103,0.18);
            border-color: rgba(216,176,103,0.34);
            color: #fff;
        }

        .profile-section {
            scroll-margin-top: 100px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .profile-tile {
            position: relative;
            display: block;
            width: 100%;
            padding: 0;
            border: 1px solid var(--profile-border);
            border-radius: 26px;
            overflow: hidden;
            background: var(--profile-panel);
            box-shadow: var(--profile-shadow);
            aspect-ratio: 1 / 1.12;
            cursor: pointer;
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, filter .22s ease;
            transform: translateY(0) scale(1);
        }

        .profile-tile:hover {
            transform: translateY(-6px) scale(1.01);
            border-color: rgba(216,176,103,0.28);
            box-shadow: 0 36px 90px rgba(0, 0, 0, 0.5);
            filter: saturate(1.04);
        }

        .profile-tile:active {
            transform: translateY(-2px) scale(0.985);
        }

        .profile-tile img,
        .profile-tile video,
        .profile-tile-embed {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            background: #000;
            transition: transform .35s ease;
        }

        .profile-tile:hover img,
        .profile-tile:hover video,
        .profile-tile:hover .profile-tile-embed {
            transform: scale(1.04);
        }

        .profile-tile::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.05), transparent 34%, rgba(0,0,0,0.08));
            opacity: 0;
            transition: opacity .22s ease;
            pointer-events: none;
        }

        .profile-tile:hover::after {
            opacity: 1;
        }

        .profile-tile-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.02), rgba(0,0,0,0.74));
            opacity: 0;
            transition: opacity .18s ease;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 18px;
            pointer-events: none;
        }

        .profile-tile:hover .profile-tile-overlay {
            opacity: 1;
        }

        .profile-tile-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            font-size: .82rem;
            color: #dce6f8;
        }

        .profile-tile-caption {
            margin-top: 8px;
            color: #fff;
            font-weight: 600;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .profile-tile-badges {
            position: absolute;
            top: 14px;
            left: 14px;
            right: 14px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            pointer-events: none;
        }

        .profile-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            background: rgba(9, 15, 28, 0.82);
            color: #fff;
            padding: 7px 11px;
            font-size: .72rem;
            font-weight: 700;
        }

        .profile-empty,
        .profile-about {
            border: 1px solid var(--profile-border);
            border-radius: 24px;
            background: rgba(255,255,255,0.03);
            padding: 30px 24px;
        }

        .profile-empty {
            text-align: center;
            color: var(--profile-muted);
        }

        .profile-about-copy {
            color: var(--profile-muted);
            line-height: 1.75;
            white-space: pre-wrap;
            margin: 0;
        }

        .profile-lightbox {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: none;
            background: rgba(5, 8, 14, 0.88);
            backdrop-filter: blur(16px);
        }

        .profile-lightbox.is-open {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .profile-lightbox-dialog {
            width: min(1080px, 100%);
            max-height: calc(100vh - 40px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 28px;
            overflow: hidden;
            background: rgba(9, 15, 28, 0.96);
            box-shadow: var(--profile-shadow);
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) 340px;
        }

        .profile-lightbox-media {
            position: relative;
            min-height: 420px;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-lightbox-media img,
        .profile-lightbox-media video,
        .profile-lightbox-media iframe {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #000;
            border: 0;
        }

        .profile-lightbox-side {
            display: flex;
            flex-direction: column;
            min-height: 0;
            border-left: 1px solid rgba(255,255,255,0.08);
        }

        .profile-lightbox-head,
        .profile-lightbox-body,
        .profile-lightbox-foot {
            padding: 18px 20px;
        }

        .profile-lightbox-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: start;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .profile-lightbox-body {
            overflow-y: auto;
            color: var(--profile-muted);
            line-height: 1.75;
        }

        .profile-lightbox-foot {
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            color: var(--profile-muted);
        }

        .profile-close,
        .profile-nav {
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

        .profile-close {
            width: 42px;
            height: 42px;
            font-size: 1.2rem;
        }

        .profile-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 48px;
            font-size: 1.35rem;
            box-shadow: 0 12px 26px rgba(0,0,0,0.28);
        }

        .profile-nav.prev { left: 16px; }
        .profile-nav.next { right: 16px; }

        .profile-nav[hidden] { display: none; }

        @media (max-width: 991.98px) {
            .profile-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .profile-lightbox-dialog {
                grid-template-columns: 1fr;
            }

            .profile-lightbox-side {
                border-left: 0;
                border-top: 1px solid rgba(255,255,255,0.08);
            }
        }

        @media (max-width: 767.98px) {
            .profile-shell {
                width: calc(100% - 16px);
                padding-top: 14px;
            }

            .profile-hero {
                border-radius: 22px;
                padding: 18px;
            }

            .profile-hero-grid {
                grid-template-columns: 1fr;
            }

            .profile-avatar,
            .profile-avatar-fallback {
                width: 108px;
                height: 108px;
                border-radius: 28px;
            }

            .profile-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .profile-tile {
                border-radius: 18px;
            }

            .profile-sticky-bar {
                top: 8px;
                border-radius: 18px;
                padding: 12px;
            }

            .profile-counters,
            .profile-tabs {
                width: 100%;
            }

            .profile-counter {
                flex: 1 1 calc(50% - 10px);
                min-width: 0;
            }

            .profile-lightbox.is-open {
                padding: 10px;
            }

            .profile-lightbox-dialog {
                max-height: calc(100vh - 20px);
                border-radius: 20px;
            }

            .profile-nav {
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

        $mediaCount = $posts->reduce(function ($carry, $post) {
            return $carry + count((array) $post->resolved_media_items);
        }, 0);

        $commentCount = $posts->sum('visible_comments_count');
    @endphp

    <div class="profile-shell">
        <div class="profile-topbar">
            <a href="{{ route('club.feed', $club->slug) }}">Back To Feed</a>
            <span>{{ $profileType === 'club' ? 'Club-only posts' : 'Model-only posts' }}</span>
        </div>

        <section class="profile-hero profile-section" id="top">
            <div class="profile-hero-grid">
                @if($profileImage)
                    <img src="{{ asset('uploads/' . $profileImage) }}" alt="{{ $profileTitle }}" class="profile-avatar">
                @else
                    <div class="profile-avatar-fallback">{{ strtoupper(substr($profileTitle, 0, 2)) }}</div>
                @endif

                <div>
                    <div class="profile-kicker">{{ $profileType === 'club' ? 'Club Profile' : 'Model Profile' }}</div>
                    <h1 class="profile-title">{{ $profileTitle }}</h1>
                    @if($profileSubtitle)
                        <p class="profile-copy">{{ $profileSubtitle }}</p>
                    @endif
                </div>
            </div>
        </section>

        <div class="profile-sticky-bar">
            <div class="profile-counters">
                <div class="profile-counter">
                    <strong>{{ $posts->count() }}</strong>
                    <span>Posts</span>
                </div>
                <div class="profile-counter">
                    <strong>{{ $mediaCount }}</strong>
                    <span>Media</span>
                </div>
                <div class="profile-counter">
                    <strong>{{ $commentCount }}</strong>
                    <span>Comments</span>
                </div>
            </div>

            <div class="profile-tabs">
                <a href="#posts" class="profile-tab active">Posts</a>
                <a href="#media" class="profile-tab">Media</a>
                <a href="#about" class="profile-tab">About</a>
            </div>
        </div>

        @if($posts->count())
            <section class="profile-grid profile-section" id="posts">
                @foreach($posts as $post)
                    @php
                        $mediaItems = array_values(array_filter((array) $post->resolved_media_items));
                        $tileItem = $mediaItems[0] ?? null;
                        $tileUrl = $tileItem ? $mediaUrl($tileItem) : null;
                        $tileEmbed = $tileItem && ($tileItem['type'] ?? 'image') === 'video' ? $embedUrl($tileUrl) : null;
                        $lightboxItems = collect($mediaItems)->map(function ($item) use ($mediaUrl, $embedUrl) {
                            $url = $mediaUrl($item);

                            return [
                                'type' => $item['type'] ?? 'image',
                                'url' => $url,
                                'embed' => ($item['type'] ?? 'image') === 'video' ? $embedUrl($url) : null,
                            ];
                        })->values();
                    @endphp
                    <button
                        type="button"
                        class="profile-tile"
                        data-lightbox-items='@json($lightboxItems)'
                        data-lightbox-caption="{{ e($post->caption ?? '') }}"
                        data-lightbox-date="{{ optional($post->posted_at)->format('M d, Y') }}"
                        data-lightbox-comments="{{ $post->visible_comments_count }}"
                        data-lightbox-author="{{ e($post->author_name) }}"
                    >
                        @if($tileItem)
                            @if(($tileItem['type'] ?? 'image') === 'video')
                                @if($tileEmbed)
                                    <iframe src="{{ $tileEmbed }}" class="profile-tile-embed" frameborder="0" allowfullscreen></iframe>
                                @else
                                    <video src="{{ $tileUrl }}" muted playsinline></video>
                                @endif
                            @else
                                <img src="{{ $tileUrl }}" alt="{{ $profileTitle }} post media">
                            @endif
                        @endif

                        <div class="profile-tile-badges">
                            <span class="profile-badge">{{ strtoupper($post->author_mode === 'club' ? 'CLUB' : 'MODEL') }}</span>
                            <span class="profile-badge">{{ count($mediaItems) }} media</span>
                        </div>

                        <div class="profile-tile-overlay">
                            <div class="profile-tile-meta">
                                <span>{{ optional($post->posted_at)->format('M d, Y') }}</span>
                                <span>{{ $post->visible_comments_count }} comments</span>
                            </div>
                            @if($post->caption)
                                <div class="profile-tile-caption">{{ \Illuminate\Support\Str::limit($post->caption, 110) }}</div>
                            @endif
                        </div>
                    </button>
                @endforeach
            </section>
        @else
            <div class="profile-empty profile-section" id="posts">
                <h3 class="mb-2">No profile posts yet</h3>
                <p class="mb-0">This profile does not have any live feed posts right now.</p>
            </div>
        @endif

        <section class="profile-about profile-section mt-4" id="about">
            <h3 class="mb-3">About</h3>
            <p class="profile-about-copy">{{ $profileSubtitle ?: 'No additional profile information is available yet.' }}</p>
        </section>
    </div>

    <div class="profile-lightbox" id="profile-lightbox" aria-hidden="true">
        <div class="profile-lightbox-dialog" role="dialog" aria-modal="true" aria-label="Media viewer">
            <div class="profile-lightbox-media">
                <button type="button" class="profile-nav prev" id="profile-lightbox-prev" aria-label="Previous media">&#8249;</button>
                <div id="profile-lightbox-stage" style="width:100%;height:100%;"></div>
                <button type="button" class="profile-nav next" id="profile-lightbox-next" aria-label="Next media">&#8250;</button>
            </div>
            <div class="profile-lightbox-side">
                <div class="profile-lightbox-head">
                    <div>
                        <div style="font-weight:700;">{{ $profileTitle }}</div>
                        <div id="profile-lightbox-author" style="color:var(--profile-muted);font-size:.86rem;"></div>
                    </div>
                    <button type="button" class="profile-close" id="profile-lightbox-close" aria-label="Close viewer">&times;</button>
                </div>
                <div class="profile-lightbox-body">
                    <div id="profile-lightbox-caption"></div>
                </div>
                <div class="profile-lightbox-foot">
                    <span id="profile-lightbox-date"></span>
                    <span id="profile-lightbox-counter"></span>
                    <span id="profile-lightbox-comments"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.profile-tab');
        const sections = Array.from(document.querySelectorAll('.profile-section'));
        const lightbox = document.getElementById('profile-lightbox');
        const stage = document.getElementById('profile-lightbox-stage');
        const closeButton = document.getElementById('profile-lightbox-close');
        const prevButton = document.getElementById('profile-lightbox-prev');
        const nextButton = document.getElementById('profile-lightbox-next');
        const captionNode = document.getElementById('profile-lightbox-caption');
        const dateNode = document.getElementById('profile-lightbox-date');
        const counterNode = document.getElementById('profile-lightbox-counter');
        const commentsNode = document.getElementById('profile-lightbox-comments');
        const authorNode = document.getElementById('profile-lightbox-author');
        const tileButtons = document.querySelectorAll('.profile-tile');

        let currentItems = [];
        let currentIndex = 0;

        function setActiveTab() {
            const offset = window.scrollY + 140;
            let activeId = sections[0] ? sections[0].id : null;

            sections.forEach(function (section) {
                if (section.offsetTop <= offset) {
                    activeId = section.id;
                }
            });

            tabs.forEach(function (tab) {
                tab.classList.toggle('active', tab.getAttribute('href') === '#' + activeId);
            });
        }

        function renderMediaItem(item) {
            if (!item) {
                stage.innerHTML = '';
                return;
            }

            if (item.type === 'video') {
                if (item.embed) {
                    stage.innerHTML = '<iframe src="' + item.embed + '" allowfullscreen allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"></iframe>';
                } else {
                    stage.innerHTML = '<video src="' + item.url + '" controls autoplay playsinline></video>';
                }
            } else {
                stage.innerHTML = '<img src="' + item.url + '" alt="Profile media">';
            }

            counterNode.textContent = currentItems.length ? (currentIndex + 1) + ' / ' + currentItems.length : '';
            prevButton.hidden = currentItems.length <= 1;
            nextButton.hidden = currentItems.length <= 1;
        }

        function openLightbox(button) {
            currentItems = JSON.parse(button.getAttribute('data-lightbox-items') || '[]');
            currentIndex = 0;
            captionNode.textContent = button.getAttribute('data-lightbox-caption') || '';
            dateNode.textContent = button.getAttribute('data-lightbox-date') || '';
            commentsNode.textContent = (button.getAttribute('data-lightbox-comments') || '0') + ' comments';
            authorNode.textContent = button.getAttribute('data-lightbox-author') || '';
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

        tileButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                openLightbox(button);
            });
        });

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

        window.addEventListener('scroll', setActiveTab, { passive: true });
        setActiveTab();
    });
    </script>
</body>
</html>
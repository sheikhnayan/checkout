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
            --feed-bg-soft: rgba(255,255,255,0.05);
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
                radial-gradient(circle at top left, rgba(215,174,100,0.12), transparent 24%),
                radial-gradient(circle at top right, rgba(83, 127, 255, 0.12), transparent 26%),
                linear-gradient(180deg, #0a1120 0%, var(--feed-bg) 100%);
        }

        a { color: inherit; text-decoration: none; }

        .feed-shell {
            width: min(780px, calc(100% - 24px));
            margin: 0 auto;
            padding: 20px 0 48px;
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

        .club-hero {
            border: 1px solid var(--feed-border);
            border-radius: 30px;
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
            margin-top: 20px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 10px;
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
            padding: 12px 18px;
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

        .feed-card {
            border: 1px solid var(--feed-border);
            border-radius: 28px;
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
            border-radius: 24px;
            padding: 40px 24px;
            text-align: center;
            color: var(--feed-muted);
            background: rgba(255,255,255,0.03);
        }

        .feed-modal .modal-content {
            background: #0c1527;
            color: var(--feed-text);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
        }

        .feed-modal .modal-header,
        .feed-modal .modal-footer {
            border-color: rgba(255,255,255,0.08);
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
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
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

        @media (max-width: 767.98px) {
            .feed-shell {
                width: 100%;
                padding-top: 0;
            }

            .feed-topbar,
            .club-hero,
            .feed-empty {
                width: calc(100% - 16px);
                margin-left: auto;
                margin-right: auto;
            }

            .club-hero {
                margin-top: 10px;
                border-radius: 22px;
                padding: 18px;
            }

            .feed-search {
                grid-template-columns: 1fr;
            }

            .feed-card {
                border-radius: 0;
                border-left: 0;
                border-right: 0;
                margin-bottom: 14px;
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
            <a href="/">Back to Checkout</a>
        </div>

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

                <form method="GET" action="{{ route('club.feed', $club->slug) }}" class="feed-search">
                    <input class="feed-input" type="text" name="q" value="{{ $query }}" placeholder="Search posts by caption, model name, bio, or keywords">
                    <button type="submit" class="feed-btn">Search Feed</button>
                </form>

                <div class="feed-hero-actions">
                    <a href="{{ route('club.feed.profile', $club->slug) }}" class="feed-btn-secondary">Open Club Profile</a>
                </div>
            </section>

            @if($posts->count())
                @foreach($posts as $post)
                    @php
                        $mediaItems = array_values(array_filter((array) $post->resolved_media_items));
                        $commentModalId = 'commentModal-' . $post->id;
                    @endphp
                    <article class="feed-card" id="post-{{ $post->id }}">
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
                                                        <video src="{{ $currentUrl }}" controls playsinline></video>
                                                    @endif
                                                @else
                                                    <img src="{{ $currentUrl }}" alt="{{ $post->author_name }} media">
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
                            @elseif(!empty($mediaItems))
                                @php
                                    $item = $mediaItems[0];
                                    $currentUrl = $mediaUrl($item);
                                    $currentEmbed = ($item['type'] ?? 'image') === 'video' ? $embedUrl($currentUrl) : null;
                                @endphp
                                <div class="feed-media">
                                    @if(($item['type'] ?? 'image') === 'video')
                                        @if($currentEmbed)
                                            <iframe class="feed-embed" src="{{ $currentEmbed }}" frameborder="0" allowfullscreen allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"></iframe>
                                        @else
                                            <video src="{{ $currentUrl }}" controls playsinline></video>
                                        @endif
                                    @else
                                        <img src="{{ $currentUrl }}" alt="{{ $post->author_name }} media">
                                    @endif
                                </div>
                            @endif

                            @if($post->caption)
                                <div class="feed-caption">{!! nl2br(e($post->caption)) !!}</div>
                            @endif

                            <div class="feed-meta-row">
                                <span>{{ count($mediaItems) }} media item{{ count($mediaItems) === 1 ? '' : 's' }}</span>
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

                <div class="feed-pagination">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="feed-empty">
                    <h3 class="mb-2">No posts match your search</h3>
                    <p class="mb-0">Try another keyword or clear the search to browse the full club feed.</p>
                </div>
            @endif
        @endif
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/js/bootstrap.bundle.min.js"></script>
</body>
</html>
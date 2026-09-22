@extends('admin.main')

@section('content')
<style>
    .template-selection-header {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.98));
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 28px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .venue-logo-box {
        width: 120px;
        height: 68px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        flex-shrink: 0;
        overflow: hidden;
    }

    .venue-logo-img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    .venue-logo-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .template-card {
        background: rgba(30, 41, 59, 0.7);
        border: 2px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        padding: 22px;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .template-card:hover {
        transform: translateY(-4px);
        border-color: rgba(99, 102, 241, 0.4);
        box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.5);
    }

    .template-card.is-active {
        border-color: #22c55e;
        background: linear-gradient(180deg, rgba(34, 197, 94, 0.07) 0%, rgba(30, 41, 59, 0.85) 100%);
        box-shadow: 0 0 25px rgba(34, 197, 94, 0.2);
    }

    .active-ribbon {
        position: absolute;
        top: 16px;
        right: 16px;
        background: #22c55e;
        color: #052e16;
        font-weight: 800;
        font-size: 0.72rem;
        padding: 5px 12px;
        border-radius: 999px;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.35);
        z-index: 5;
    }

    /* Tall Realtime Viewframe Styles */
    .viewframe-card-container {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: #0b1120;
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
    }

    .viewframe-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 14px;
        background: rgba(15, 23, 42, 0.95);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 0.76rem;
        z-index: 4;
    }

    .viewframe-status-dot {
        width: 8px;
        height: 8px;
        background: #22c55e;
        border-radius: 50%;
        box-shadow: 0 0 8px #22c55e;
        display: inline-block;
        animation: pulseDot 2s infinite ease-in-out;
    }

    @keyframes pulseDot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.85); }
    }

    .viewframe-toolbar-title {
        color: #94a3b8;
        font-weight: 600;
        letter-spacing: 0.03em;
    }

    .viewframe-toolbar-link {
        color: #cbd5e1;
        text-decoration: none;
        font-size: 0.74rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.08);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .viewframe-toolbar-link:hover {
        color: #fff;
        background: rgba(99, 102, 241, 0.5);
    }

    .viewframe-viewport {
        position: relative;
        width: 100%;
        height: 540px; /* Tall viewframe so the full layout can be viewed! */
        overflow: hidden;
        background: #0b1120;
    }

    .viewframe-scroll-container {
        width: 100%;
        height: 100%;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        position: relative;
        scroll-behavior: smooth;
    }

    .viewframe-scroll-container::-webkit-scrollbar {
        width: 6px;
    }

    .viewframe-scroll-container::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.6);
    }

    .viewframe-scroll-container::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
    }

    .viewframe-scroll-container::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .viewframe-full-img {
        width: 100%;
        height: auto;
        display: block;
    }

    .viewframe-scroll-badge {
        position: absolute;
        bottom: 12px;
        left: 12px;
        background: rgba(15, 23, 42, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #e2e8f0;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.72rem;
        font-weight: 600;
        pointer-events: none;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(6px);
        z-index: 3;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }

    .viewframe-loading-placeholder {
        position: absolute;
        inset: 0;
        background: #0b1120;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        z-index: 2;
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .viewframe-scaler {
        position: absolute;
        top: 0;
        left: 0;
        transform-origin: top left;
    }

    .viewframe-iframe {
        width: 100%;
        height: 100%;
        border: none;
        display: block;
        background: #0b1120;
    }

    .viewframe-hover-expand-btn {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: rgba(15, 23, 42, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        opacity: 0.85;
        transition: all 0.2s ease;
        text-decoration: none;
        z-index: 3;
        backdrop-filter: blur(4px);
    }

    .viewframe-hover-expand-btn:hover {
        opacity: 1;
        background: #6366f1;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 0.84rem;
        color: #cbd5e1;
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .feature-item i {
        color: #22c55e;
        margin-top: 2px;
        font-size: 0.8rem;
    }

    .template-desc {
        font-size: 0.88rem;
        line-height: 1.55;
        color: #94a3b8;
        margin-bottom: 18px;
        min-height: 54px;
    }

    .card-actions {
        margin-top: auto;
        padding-top: 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb Header -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <div class="text-muted small mb-1">
                <a href="{{ route('admin.website.index') }}" class="text-muted text-decoration-none">
                    <i class="fas fa-globe me-1"></i>Websites
                </a>
                <span class="mx-1">/</span>
                <span class="text-white">{{ $data->name }}</span>
                <span class="mx-1">/</span>
                <span>Select Template</span>
            </div>
            <h4 class="fw-bold mb-0 text-white">Choose Primary Checkout Template</h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.website.edit', $data->id) }}" class="btn btn-sm btn-outline-light">
                <i class="fas fa-cog me-1"></i>Website Settings
            </a>
            <a href="{{ route('admin.website.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Websites
            </a>
        </div>
    </div>

    @php
        $logoUrl = null;
        if (!empty($data->logo)) {
            $rawLogo = trim((string) $data->logo);
            if (str_starts_with($rawLogo, 'http://') || str_starts_with($rawLogo, 'https://')) {
                $logoUrl = $rawLogo;
            } elseif (str_starts_with($rawLogo, 'uploads/') || str_starts_with($rawLogo, '/uploads/')) {
                $logoUrl = asset(ltrim($rawLogo, '/'));
            } else {
                $logoUrl = asset('uploads/' . $rawLogo);
            }
        }
    @endphp

    <!-- Club Overview Banner -->
    <div class="template-selection-header">
        <div class="row align-items-center g-3">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-3">
                    <div class="venue-logo-box">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $data->name }}" class="venue-logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="venue-logo-fallback" style="display: none;">
                                <i class="fas fa-building fa-2x text-warning"></i>
                            </div>
                        @else
                            <div class="venue-logo-fallback">
                                <i class="fas fa-building fa-2x text-warning"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h5 class="text-white mb-1 fw-bold">{{ $data->name }}</h5>
                        <div class="d-flex align-items-center gap-2 flex-wrap text-muted small">
                            <span>Slug: <code class="text-info">{{ $data->slug }}</code></span>
                            @if($data->domain)
                                <span>•</span>
                                <span>Domain: <strong>{{ $data->domain }}</strong></span>
                            @endif
                            <span>•</span>
                            <span>Live Checkout URL: 
                                <a href="{{ url('/' . $data->slug) }}" target="_blank" class="text-warning text-decoration-none">
                                    {{ url('/' . $data->slug) }} <i class="fas fa-external-link-alt ms-1" style="font-size: 0.7rem;"></i>
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-md-end">
                <div class="d-inline-flex align-items-center gap-2 bg-dark px-3 py-2 rounded-3 border border-secondary border-opacity-25">
                    <span class="text-muted small">Active Primary Template:</span>
                    <span class="badge bg-success text-white px-2 py-1" style="font-size: 0.85rem;">
                        <i class="fas fa-check-circle me-1"></i>{{ $templates[$currentTemplate]['name'] ?? ucfirst($currentTemplate) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Feedback Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert" style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.4); color: #86efac;">
            <i class="fas fa-check-circle me-2 fa-lg"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Template Selection Grid -->
    <div class="row g-4">
        @foreach($templates as $key => $template)
            @php
                $isActive = ($currentTemplate === $key);
            @endphp
            <div class="col-lg-4 col-md-6">
                <div class="template-card {{ $isActive ? 'is-active' : '' }}">
                    @if($isActive)
                        <div class="active-ribbon">
                            <i class="fas fa-check"></i> Active Primary
                        </div>
                    @endif

                    <!-- Tall Realtime Viewframe -->
                    <div class="viewframe-card-container" id="vf-card-{{ $key }}">
                        <div class="viewframe-toolbar">
                            <div class="d-flex align-items-center gap-2">
                                <span class="viewframe-status-dot"></span>
                                <span class="viewframe-toolbar-title">Preview</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-xs btn-primary active btn-vf-screenshot px-2 py-0" style="font-size: 0.7rem;" onclick="setViewframeView('{{ $key }}', 'screenshot')">
                                        <i class="fas fa-image me-1"></i>Full View
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary text-white btn-vf-live px-2 py-0" style="font-size: 0.7rem;" onclick="setViewframeView('{{ $key }}', 'live')">
                                        <i class="fas fa-desktop me-1"></i>Interactive
                                    </button>
                                </div>
                                <a href="{{ $template['preview_url'] }}" target="_blank" class="viewframe-toolbar-link" title="Open Full Screen in New Tab">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                        <div class="viewframe-viewport">
                            <!-- Scrollable Full-Length Screenshot View -->
                            <div class="viewframe-screenshot-view viewframe-scroll-container" onscroll="var b = this.querySelector('.viewframe-scroll-badge'); if(b) b.style.opacity = '0';">
                                <img src="{{ $template['image'] }}" alt="{{ $template['name'] }} Preview" class="viewframe-full-img" loading="lazy">
                                <div class="viewframe-scroll-badge">
                                    <i class="fas fa-arrows-alt-v text-warning"></i>
                                    <span>Scroll to explore full layout</span>
                                </div>
                            </div>

                            <!-- Live Interactive Scaled Iframe View -->
                            <div class="viewframe-live-view" style="display: none; width: 100%; height: 100%;">
                                <div class="viewframe-loading-placeholder">
                                    <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                                    <span>Loading interactive {{ $template['name'] }}...</span>
                                </div>
                                <div class="viewframe-scaler">
                                    <iframe 
                                        data-src="{{ $template['preview_url'] }}" 
                                        class="viewframe-iframe" 
                                        loading="lazy"
                                        onload="var p = this.closest('.viewframe-live-view').querySelector('.viewframe-loading-placeholder'); if(p) p.style.display='none';"
                                        title="Interactive preview of {{ $template['name'] }} for {{ $data->name }}">
                                    </iframe>
                                </div>
                            </div>

                            <a href="{{ $template['preview_url'] }}" target="_blank" class="viewframe-hover-expand-btn" title="Open live checkout in full window">
                                <i class="fas fa-expand me-1"></i>
                                <span>Fullscreen</span>
                            </a>
                        </div>
                    </div>

                    <!-- Template Info -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge {{ $template['badge_class'] }}" style="font-size: 0.72rem; padding: 4px 8px;">
                            {{ $template['badge'] }}
                        </span>
                        <span class="text-muted small">ID: <code>{{ $key }}</code></span>
                    </div>

                    <h5 class="text-white fw-bold mb-1">{{ $template['name'] }}</h5>
                    <div class="text-warning small mb-2 fw-semibold">{{ $template['subtitle'] }}</div>
                    
                    <p class="template-desc">{{ $template['description'] }}</p>

                    <!-- Feature Highlights -->
                    <div class="mb-3">
                        <div class="text-white-50 small fw-bold text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">Highlights</div>
                        @foreach($template['features'] as $feature)
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Actions -->
                    <div class="card-actions">
                        <div class="d-flex gap-2">
                            <a href="{{ $template['preview_url'] }}" target="_blank" class="btn btn-sm btn-outline-info flex-grow-1" style="font-weight: 600; padding: 8px 12px;">
                                <i class="fas fa-eye me-1"></i>Live Preview
                            </a>
                            
                            @if($isActive)
                                <button type="button" class="btn btn-sm btn-success flex-grow-1" disabled style="font-weight: 600; padding: 8px 12px;">
                                    <i class="fas fa-check-circle me-1"></i>Current
                                </button>
                            @else
                                <form action="{{ route('admin.website.select-template.update', $data->id) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <input type="hidden" name="checkout_template" value="{{ $key }}">
                                    <button type="submit" class="btn btn-sm btn-primary w-100" style="font-weight: 600; padding: 8px 12px;" onclick="return confirm('Switch primary checkout template for {{ $data->name }} to {{ $template['name'] }}?');">
                                        <i class="fas fa-check me-1"></i>Set as Primary
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Informational Card -->
    <div class="card bg-dark border-secondary border-opacity-25 mt-4 p-3 rounded-3">
        <div class="d-flex align-items-start gap-3">
            <div class="text-info mt-1">
                <i class="fas fa-info-circle fa-lg"></i>
            </div>
            <div class="small text-muted">
                <strong class="text-white">How Template Selection Works:</strong>
                <p class="mb-0 mt-1">
                    When you switch the primary checkout template, customers visiting <code>{{ url('/' . $data->slug) }}</code> or using your embed script will automatically be served the selected design. All transactions, pricing rules, promo codes, ClubLifter schedules, and reporting remain fully synchronized across all templates.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var TARGET_DESKTOP_WIDTH = 1080;

    function resizeLiveViewframes() {
        document.querySelectorAll('.viewframe-viewport').forEach(function(viewport) {
            var containerWidth = viewport.clientWidth;
            if (!containerWidth) return;

            var scale = containerWidth / TARGET_DESKTOP_WIDTH;
            var scaler = viewport.querySelector('.viewframe-scaler');
            if (scaler) {
                scaler.style.width = TARGET_DESKTOP_WIDTH + 'px';
                scaler.style.height = (viewport.clientHeight / scale) + 'px';
                scaler.style.transform = 'scale(' + scale + ')';
            }
        });
    }

    window.setViewframeView = function(key, mode) {
        var card = document.getElementById('vf-card-' + key);
        if (!card) return;
        var screenshotBox = card.querySelector('.viewframe-screenshot-view');
        var liveBox = card.querySelector('.viewframe-live-view');
        var btnScreenshot = card.querySelector('.btn-vf-screenshot');
        var btnLive = card.querySelector('.btn-vf-live');

        if (mode === 'live') {
            if (screenshotBox) screenshotBox.style.display = 'none';
            if (liveBox) {
                liveBox.style.display = 'block';
                var iframe = liveBox.querySelector('iframe');
                if (iframe && !iframe.getAttribute('src') && iframe.getAttribute('data-src')) {
                    iframe.setAttribute('src', iframe.getAttribute('data-src'));
                }
            }
            if (btnScreenshot) {
                btnScreenshot.classList.remove('active', 'btn-primary');
                btnScreenshot.classList.add('btn-outline-secondary');
            }
            if (btnLive) {
                btnLive.classList.add('active', 'btn-primary');
                btnLive.classList.remove('btn-outline-secondary');
            }
            resizeLiveViewframes();
        } else {
            if (liveBox) liveBox.style.display = 'none';
            if (screenshotBox) screenshotBox.style.display = 'block';
            if (btnScreenshot) {
                btnScreenshot.classList.add('active', 'btn-primary');
                btnScreenshot.classList.remove('btn-outline-secondary');
            }
            if (btnLive) {
                btnLive.classList.remove('active', 'btn-primary');
                btnLive.classList.add('btn-outline-secondary');
            }
        }
    };

    window.addEventListener('resize', resizeLiveViewframes);
    window.addEventListener('load', resizeLiveViewframes);
    document.addEventListener('DOMContentLoaded', function() {
        resizeLiveViewframes();
        setTimeout(resizeLiveViewframes, 100);
        setTimeout(resizeLiveViewframes, 400);
    });
})();
</script>
@endsection

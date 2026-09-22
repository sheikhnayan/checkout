@extends('admin.main')

@section('content')
<style>
    .template-selection-header {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.95));
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 28px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .template-card {
        background: rgba(30, 41, 59, 0.7);
        border: 2px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        padding: 24px;
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
        top: 18px;
        right: 18px;
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
        z-index: 2;
    }

    .recommended-badge {
        position: absolute;
        top: 18px;
        left: 18px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.7rem;
        padding: 4px 10px;
        border-radius: 999px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
        z-index: 2;
    }

    .preview-mockup-wrapper {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: #0f172a;
        margin-bottom: 20px;
        position: relative;
        aspect-ratio: 16 / 10;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Template Mockup Styles */
    .mockup-ui {
        width: 100%;
        height: 100%;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        background: #0b1120;
        font-size: 10px;
        user-select: none;
    }

    .mockup-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 6px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .mockup-logo-pill {
        width: 45px;
        height: 10px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 4px;
    }

    .mockup-nav-dots {
        display: flex;
        gap: 4px;
    }

    .mockup-nav-dot {
        width: 16px;
        height: 6px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 3px;
    }

    .mockup-body {
        flex: 1;
        display: flex;
        gap: 8px;
    }

    .mockup-card-item {
        flex: 1;
        border-radius: 6px;
        padding: 8px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Template 1 Mockup Specifics */
    .mockup-t1 .mockup-card-item {
        background: rgba(99, 102, 241, 0.12);
        border: 1px solid rgba(99, 102, 241, 0.3);
    }
    .mockup-t1 .mockup-accent-btn {
        height: 12px;
        background: #6366f1;
        border-radius: 4px;
    }

    /* Template 4 Mockup Specifics */
    .mockup-t4 .mockup-card-item {
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.35);
    }
    .mockup-t4 .mockup-accent-btn {
        height: 12px;
        background: linear-gradient(90deg, #f59e0b, #ec4899);
        border-radius: 4px;
    }

    /* Default Mockup Specifics */
    .mockup-default .mockup-card-item {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .mockup-default .mockup-accent-btn {
        height: 12px;
        background: #2563eb;
        border-radius: 4px;
    }

    .mockup-overlay-btn {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.75);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        opacity: 0;
        transition: opacity 0.2s ease;
        backdrop-filter: blur(3px);
        text-decoration: none;
        color: #fff;
        z-index: 3;
    }

    .preview-mockup-wrapper:hover .mockup-overlay-btn {
        opacity: 1;
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
        min-height: 58px;
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

    <!-- Club Overview Banner -->
    <div class="template-selection-header">
        <div class="row align-items-center g-3">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-3">
                    @if($data->logo)
                        <img src="{{ asset('storage/' . $data->logo) }}" alt="{{ $data->name }}" style="max-height: 44px; max-width: 140px; object-fit: contain;">
                    @else
                        <div class="bg-primary text-white rounded p-2 d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="fas fa-building fa-lg"></i>
                        </div>
                    @endif
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

                    @if(!empty($template['recommended']))
                        <div class="recommended-badge">
                            <i class="fas fa-crown me-1"></i> Recommended
                        </div>
                    @endif

                    <!-- Visual Mini-Mockup Preview -->
                    <div class="preview-mockup-wrapper">
                        <!-- Visual representation of the layout -->
                        <div class="mockup-ui mockup-{{ $key }}">
                            <div class="mockup-header">
                                <div class="mockup-logo-pill"></div>
                                <div class="mockup-nav-dots">
                                    <div class="mockup-nav-dot"></div>
                                    <div class="mockup-nav-dot"></div>
                                </div>
                            </div>
                            <div class="mockup-body">
                                <div class="mockup-card-item">
                                    <div style="height: 6px; width: 60%; background: rgba(255,255,255,0.3); border-radius: 2px;"></div>
                                    <div style="height: 4px; width: 40%; background: rgba(255,255,255,0.15); border-radius: 2px;"></div>
                                    <div class="mockup-accent-btn"></div>
                                </div>
                                <div class="mockup-card-item">
                                    <div style="height: 6px; width: 70%; background: rgba(255,255,255,0.3); border-radius: 2px;"></div>
                                    <div style="height: 4px; width: 50%; background: rgba(255,255,255,0.15); border-radius: 2px;"></div>
                                    <div class="mockup-accent-btn"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Hover Overlay to Preview Live -->
                        <a href="{{ $template['preview_url'] }}" target="_blank" class="mockup-overlay-btn" title="Click to preview with {{ $data->name }}'s data">
                            <i class="fas fa-external-link-alt fa-2x text-warning"></i>
                            <span class="fw-bold small">Live Preview with Venue Data</span>
                        </a>
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
@endsection

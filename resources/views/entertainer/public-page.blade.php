@php
    $isEntertainerProfile = true;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $entertainer->display_name ?: $entertainer->user->name }} - CartVIP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --accent: #a774ff;
            --brand-gradient: linear-gradient(135deg, #a774ff 0%, #7c3aed 52%, #5b21b6 100%);
            --bg-dark: #0b0e1a;
            --text-main: #e8eaf6;
            --text-muted: rgba(232, 234, 246, 0.72);
            --border-light: rgba(255, 255, 255, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            background: linear-gradient(180deg, #0b0e1a 0%, #0f1526 52%, #0b0e1a 100%);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        /* Hero Section */
        .cv-hero {
            padding: 32px 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.01) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .cv-hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .cv-hero-head {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }

        .cv-hero-venue {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
        }

        .cv-hero-venue-avatar, .cv-hero-venue-initial {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent);
            flex-shrink: 0;
        }

        .cv-hero-venue-initial {
            background: rgba(167,116,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
        }

        .cv-hero-venue-title {
            font-size: 1.3rem;
            font-weight: 800;
            margin: 0;
        }

        .cv-hero-venue-verified {
            color: var(--accent);
            margin-left: 4px;
        }

        .cv-hero-venue-meta {
            font-size: 13px;
            color: var(--text-muted);
            margin: 4px 0 0;
        }

        .cv-hero-bottom {
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 24px;
            align-items: stretch;
        }

        .cv-hero-content {
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            padding: 28px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .aff-kicker {
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            opacity: .7;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .cv-hero-title {
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            line-height: 1.1;
            font-weight: 800;
            margin: 0 0 12px;
        }

        .cv-hero-subtitle {
            font-size: 15px;
            color: var(--text-muted);
            margin: 0;
        }

        .cv-hero-location {
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            padding: 20px;
        }

        /* Gallery */
        .hero-gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin: 24px auto;
            max-width: 1200px;
            padding: 0 20px;
        }

        .hero-gallery-item {
            width: 100%;
            aspect-ratio: 4/3;
            border: 1px solid var(--border-light);
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            background: rgba(255,255,255,0.02);
            transition: all 0.2s ease;
        }

        .hero-gallery-item:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .hero-gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Main Checkout */
        .cv-checkout-body {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 420px;
            gap: 28px;
            align-items: start;
            margin-top: 24px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 20px;
        }

        .section-kicker-lg {
            font-size: 14px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 700;
            opacity: 0.8;
            margin: 0 !important;
        }

        /* Package Categories */
        .package-category-tiles {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            width: 100%;
        }

        .package-category-tile {
            flex: 0 1 auto;
            padding: 10px 16px;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            background: rgba(255,255,255,0.04);
            color: var(--text-main);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .package-category-tile:hover {
            border-color: rgba(255,255,255,0.28);
            background: rgba(255,255,255,0.08);
        }

        .package-category-tile.active {
            background: rgba(167,116,255,0.15);
            border-color: var(--accent);
            color: #fff;
        }

        .package-category-indicator {
            font-size: 14px;
            font-weight: 800;
            opacity: 0.6;
        }

        .package-category-tile.active .package-category-indicator {
            opacity: 1;
            transform: rotate(45deg);
        }

        /* Rectangle background behind category icon (dynamic by --cat-rgb) */
        .package-category-tile-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 32px;
            padding: 4px 8px;
            border-radius: 8px;
            background: rgba(255,255,255,0.03);
            box-shadow: none;
            transition: all .18s;
            margin-right: 7px;
        }
        .package-category-tile.has-cat-color .package-category-tile-icon {
            background: rgba(var(--cat-rgb), 0.12) !important;
            border: 1px solid rgba(var(--cat-rgb), 0.18) !important;
            color: rgba(var(--cat-rgb), 1) !important;
        }
        .package-category-tile.has-cat-color.active .package-category-tile-icon {
            background: linear-gradient(135deg, rgba(var(--cat-rgb), 0.95) 0%, rgba(var(--cat-rgb), 0.75) 100%) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(var(--cat-rgb), 0.18) !important;
        }
        .package-category-tile[style*="255,204,0"] .package-category-tile-icon,
        .package-category-tile.has-cat-color[style*="255,204,0"] .package-category-tile-icon {
            color: #000 !important;
        }

        /* Package Cards */
        .vip-card {
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            gap: 16px;
            align-items: stretch;
            transition: all 0.2s;
        }

        .vip-card:hover {
            border-color: rgba(255,255,255,0.28);
            background: rgba(255,255,255,0.04);
        }

        .cv-pkg-media-wrap {
            position: relative;
            width: 140px;
            min-width: 140px;
            height: 140px;
            border-radius: 10px;
            overflow: hidden;
            background: rgba(255,255,255,0.02);
        }

        .cv-pkg-media {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cv-popular-pill {
            position: absolute;
            top: 8px;
            left: 8px;
            background: linear-gradient(135deg, #a774ff 0%, #7c3aed 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.05em;
        }

        .vip-card-main {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .cv-pkg-title-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .cv-pkg-title-icon {
            font-size: 16px;
            color: var(--accent);
            flex-shrink: 0;
        }

        .cv-pkg-title {
            font-size: 15px;
            font-weight: 700;
        }

        .cv-pkg-sub {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 8px;
        }

        .vip-card-side {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            gap: 8px;
            min-width: 120px;
        }

        .vip-price-tag {
            font-size: 20px;
            font-weight: 800;
            color: var(--accent);
        }

        .cv-price-meta {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Order Sidebar */
        .cv-sidebar {
            position: sticky;
            top: 24px;
            background: rgba(16,18,34,0.92);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            padding: 20px;
            height: fit-content;
        }

        .cv-sidebar-header {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 16px;
            color: rgba(255,255,255,0.9);
        }

        .cv-sidebar-venue-image {
            width: 100%;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 12px;
        }

        .cv-sidebar-venue-row {
            padding-bottom: 12px;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .cv-sidebar-venue-name {
            font-size: 14px;
            font-weight: 700;
            color: rgba(255,255,255,0.9);
        }

        .cv-sidebar-venue-date {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .cv-trust-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin: 16px 0;
            padding-top: 12px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .cv-trust-item {
            display: flex;
            gap: 10px;
            font-size: 11px;
        }

        .cv-trust-item i {
            color: var(--accent);
            font-size: 12px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .cv-trust-item strong {
            display: block;
            margin-bottom: 2px;
            color: rgba(255,255,255,0.8);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-gallery-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
            .cv-hero-bottom {
                grid-template-columns: 1fr;
            }
            .cv-checkout-body {
                grid-template-columns: 1fr;
            }
            .cv-sidebar {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .hero-gallery-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .vip-card {
                flex-direction: column;
            }
            .cv-pkg-media-wrap {
                width: 100%;
                height: 200px;
            }
            .vip-card-side {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        @media (max-width: 480px) {
            .hero-gallery-grid {
                grid-template-columns: 1fr;
            }
            .cv-hero-head {
                flex-direction: column;
                text-align: center;
            }
            .package-category-tiles {
                gap: 8px;
            }
            .package-category-tile {
                flex: 1 1 48%;
                min-width: 120px;
            }
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<header class="cv-hero">
    <div class="cv-hero-inner">
        <div class="cv-hero-head">
            <div class="cv-hero-venue">
                @if($entertainer->profile_image)
                    <img src="{{ asset('uploads/' . $entertainer->profile_image) }}" alt="Profile" class="cv-hero-venue-avatar">
                @else
                    <div class="cv-hero-venue-initial">{{ strtoupper(substr($entertainer->display_name ?: $entertainer->user->name, 0, 1)) }}</div>
                @endif
                <div>
                    <p class="cv-hero-venue-title">{{ $entertainer->display_name ?: $entertainer->user->name }}<span class="cv-hero-venue-verified">✓</span></p>
                    @if($entertainer->website)
                        <p class="cv-hero-venue-meta">{{ $entertainer->website->name }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="cv-hero-bottom">
            <div class="cv-hero-content">
                <div class="aff-kicker">Entertainer Package</div>
                <h1 class="cv-hero-title">{{ $entertainer->display_name ?: $entertainer->user->name }}</h1>
                <p class="cv-hero-subtitle">{{ $entertainer->description ?: 'Premium entertainment packages available now.' }}</p>
            </div>

            <div class="cv-hero-location">
                <div style="margin-bottom: 12px;">
                    <div style="font-size: 11px; text-transform: uppercase; opacity: 0.7; font-weight: 700; margin-bottom: 8px;">Venue</div>
                    <div style="font-size: 13px; font-weight: 600;">{{ $entertainer->website->name ?? 'Premium Club' }}</div>
                </div>
            </div>
        </div>
    </div>
</header>

@if(!empty($entertainer->gallery_images))
<div class="hero-gallery-grid">
    @foreach($entertainer->gallery_images as $galleryImage)
        <button type="button" class="hero-gallery-item">
            <img src="{{ asset('uploads/' . $galleryImage) }}" alt="Gallery">
        </button>
    @endforeach
</div>
@endif

<!-- Main Section -->
<main>
    <div class="cv-checkout-body">
        <div class="cv-main-col">
            <!-- Package Selection -->
            <div style="margin-bottom: 32px;">
                <h2 class="section-kicker-lg" style="margin-bottom: 16px;">Your Available Packages</h2>

                <!-- Category Tabs -->
                @php
                    $categories = collect();
                    foreach($clubGroups as $clubId => $mappings) {
                        foreach($mappings->groupBy('package.package_category_id') as $categoryId => $categoryMappings) {
                            $category = $categoryMappings->first()->package->category;
                            $categoryName = $category?->name ?? 'Uncategorized';
                            $categoryKey = $categoryId ?? 'uncategorized';

                            if (!$categories->has($categoryKey)) {
                                $categories->put($categoryKey, [
                                    'id' => $categoryKey,
                                    'name' => $categoryName,
                                    'packages' => collect()
                                ]);
                            }

                            foreach($categoryMappings as $mapping) {
                                $categories[$categoryKey]['packages']->push($mapping);
                            }
                        }
                    }
                @endphp

                <div class="package-category-tiles">
                    @foreach($categories as $categoryKey => $categoryData)
                        <button type="button" class="package-category-tile {{ $loop->first ? 'active' : '' }}" data-target="#category-{{ $categoryKey }}">
                            <span class="package-category-name">{{ $categoryData['name'] }}</span>
                            <span class="package-category-indicator">+</span>
                        </button>
                    @endforeach
                </div>

                <!-- Package Cards by Category -->
                @foreach($categories as $categoryKey => $categoryData)
                    <div id="category-{{ $categoryKey }}" class="package-category-group" style="display: {{ $loop->first ? 'block' : 'none' }};">
                        @foreach($categoryData['packages'] as $mapping)
                            @php
                                $package = $mapping->package;
                                $club = $package->website;
                            @endphp
                            <div class="vip-card" data-package-id="{{ $package->id }}">
                                <div class="cv-pkg-media-wrap">
                                    @if($package->image)
                                        <img src="{{ asset('uploads/' . $package->image) }}" alt="{{ $package->name }}" class="cv-pkg-media">
                                    @else
                                        <div style="width: 100%; height: 100%; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.3); font-size: 12px;">No Image</div>
                                    @endif
                                </div>

                                <div class="vip-card-main">
                                    <div class="cv-pkg-title-row">
                                        <i class="fas fa-star cv-pkg-title-icon"></i>
                                        <div class="cv-pkg-title">{{ $package->name }}</div>
                                    </div>
                                    <div class="cv-pkg-sub"><i class="fas fa-building"></i>{{ $club->name }}</div>
                                    @if($package->description)
                                        <p style="font-size: 12px; color: rgba(255,255,255,0.6); margin: 8px 0 0; line-height: 1.4;">{{ strip_tags($package->description) }}</p>
                                    @endif
                                </div>

                                <div class="vip-card-side">
                                    <div class="vip-price-tag">${{ number_format($package->price, 2) }}</div>
                                    <div class="cv-price-meta">Available</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Info Sidebar -->
        <aside class="cv-sidebar">
            <div class="cv-sidebar-header">About</div>

            @if($entertainer->profile_image)
                <img src="{{ asset('uploads/' . $entertainer->profile_image) }}" alt="{{ $entertainer->display_name }}" class="cv-sidebar-venue-image">
            @endif

            <div class="cv-sidebar-venue-row">
                <div>
                    <div class="cv-sidebar-venue-name">{{ $entertainer->display_name ?: $entertainer->user->name }}</div>
                    <div class="cv-sidebar-venue-date"><i class="fas fa-star"></i>Premium Performer</div>
                </div>
            </div>

            <div style="font-size: 12px; color: var(--text-muted); line-height: 1.6; margin-bottom: 16px;">
                {{ $entertainer->description ?: 'Premium entertainment services available. Contact for booking details.' }}
            </div>

            <div class="cv-trust-list">
                <div class="cv-trust-item"><i class="fas fa-award"></i><div><strong>Professional</strong><span>Verified performer</span></div></div>
                <div class="cv-trust-item"><i class="fas fa-check-circle"></i><div><strong>Confirmed</strong><span>All details verified</span></div></div>
                <div class="cv-trust-item"><i class="fas fa-headset"></i><div><strong>Support</strong><span>Always available</span></div></div>
            </div>
        </aside>
    </div>
</main>

<script>
// Category tab switching
document.querySelectorAll('.package-category-tile').forEach(btn => {
    btn.addEventListener('click', function() {
        const target = this.dataset.target;

        // Hide all categories
        document.querySelectorAll('.package-category-group').forEach(group => {
            group.style.display = 'none';
        });

        // Deactivate all tabs
        document.querySelectorAll('.package-category-tile').forEach(tab => {
            tab.classList.remove('active');
        });

        // Show selected category and activate tab
        document.querySelector(target).style.display = 'block';
        this.classList.add('active');
    });
});
</script>

</body>
</html>

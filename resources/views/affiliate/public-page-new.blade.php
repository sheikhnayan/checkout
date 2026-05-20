@php
    $isEntertainerProfile = $affiliate instanceof \App\Models\Entertainer;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $affiliate->display_name ?: $affiliate->user->name }} - CartVIP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --accent: #a774ff;
            --accent-dark: #7c3aed;
            --accent-darker: #5b21b6;
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

        a { color: var(--accent); text-decoration: none; }

        /* Hero Section */
        .aff-hero {
            background: linear-gradient(135deg, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0.02) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding: 20px 0 18px;
        }

        .aff-profile-head {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .aff-avatar-wrap {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            padding: 3px;
            background: linear-gradient(145deg, rgba(167, 116, 255, 0.95), rgba(167, 116, 255, 0.35));
            box-shadow: 0 12px 26px rgba(167, 116, 255, 0.2);
            flex-shrink: 0;
        }

        .aff-avatar { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 2px solid rgba(11, 14, 26, 0.85); display: block; }
        .aff-initials { width: 100%; height: 100%; border-radius: 50%; border: 2px solid rgba(11, 14, 26, 0.85); background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 800; }

        .aff-profile-content { min-width: 0; max-width: min(760px, 100%); }
        .aff-profile-name { margin: 0; font-size: clamp(1.1rem, 1.2vw, 1.35rem); font-weight: 800; line-height: 1.2; letter-spacing: .01em; color: #f8f9ff; }
        .aff-profile-desc { margin: 6px 0 0; color: rgba(232, 234, 246, 0.78); font-size: 13px; line-height: 1.5; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }

        .aff-socials { display: flex; align-items: center; gap: 10px; font-size: 16px; flex-wrap: wrap; }
        .aff-socials a { width: 34px; height: 34px; border-radius: 999px; border: 1px solid rgba(255,255,255,0.16); background: rgba(255,255,255,0.04); color: var(--text-main); display: inline-flex; align-items: center; justify-content: center; text-decoration: none; opacity: .92; transition: all .2s ease; }
        .aff-socials a:hover { color: var(--accent); border-color: var(--accent); transform: translateY(-1px); }

        .aff-share-btn { width: auto; height: 34px; padding: 0 12px; gap: 7px; font-weight: 700; font-size: 12px; border: 1px solid rgba(255,255,255,0.16); border-radius: 999px; background: rgba(255,255,255,0.04); color: var(--text-main); display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all .2s ease; }
        .aff-share-btn:hover { color: var(--accent); border-color: var(--accent); transform: translateY(-1px); }

        /* Banner Section */
        .aff-banner {
            position: relative;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            overflow: hidden;
            min-height: 220px;
            margin: 20px 0 24px;
            background: linear-gradient(125deg, rgba(8,11,22,0.82), rgba(8,11,22,0.52)), radial-gradient(circle at top right, rgba(255,255,255,0.08), transparent 35%), var(--accent-darker);
        }

        .aff-banner.has-image {
            background: linear-gradient(125deg, rgba(8,11,22,0.84), rgba(8,11,22,0.48)), url('{{ $affiliate->banner_image ? asset('uploads/' . $affiliate->banner_image) : '' }}') center/contain no-repeat, #101522;
        }

        .aff-banner-content { position: relative; z-index: 1; padding: 28px; }
        .aff-kicker { font-size: 11px; letter-spacing: 1px; text-transform: uppercase; opacity: .64; font-weight: 700; }
        .aff-display-title { font-size: clamp(2rem, 5vw, 3.8rem); line-height: 1; font-weight: 800; margin: 10px 0 12px; }
        .aff-display-copy { max-width: 620px; font-size: 15px; opacity: .82; }

        /* Gallery */
        .aff-gallery { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; margin-top: 18px; }
        .aff-gallery-item { position: relative; width: 100%; min-height: 125px; padding: 0; border: 1px solid rgba(167, 116, 255, 0.28); border-radius: 14px; overflow: hidden; background: rgba(255,255,255,0.04); cursor: pointer; transition: transform .24s ease, border-color .24s ease, box-shadow .24s ease; }
        .aff-gallery-item:hover { transform: translateY(-3px); border-color: rgba(167, 116, 255, 0.46); box-shadow: 0 18px 34px rgba(167, 116, 255, 0.25); }
        .aff-gallery-item img { width: 100%; height: 100%; object-fit: cover; display: block; }

        /* Package Search */
        .package-search-wrap {
            display: grid;
            grid-template-columns: 1.4fr minmax(220px, 0.9fr) auto;
            gap: 10px;
            align-items: end;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .package-search-wrap input, .package-search-wrap select {
            width: 100%;
            margin: 0 !important;
            background: rgba(255,255,255,0.08) !important;
            border: 1px solid rgba(255,255,255,0.2) !important;
            border-radius: 10px !important;
            color: var(--text-main) !important;
            padding: 9px 11px !important;
            min-height: 40px;
        }

        .package-search-wrap input::placeholder { color: rgba(255,255,255,0.5) !important; }

        .package-search-clear {
            appearance: none;
            border: 1px solid rgba(255,255,255,0.22);
            background: rgba(255,255,255,0.08);
            color: var(--text-main);
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            min-height: 40px;
            padding: 0 14px;
            cursor: pointer;
            transition: all .2s;
        }

        .package-search-clear:hover {
            border-color: var(--accent);
            background: rgba(167, 116, 255, 0.14);
            color: #fff;
        }

        /* Package Cards */
        .vip-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 14px;
            padding: 16px 18px;
            margin-bottom: 12px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            transition: border-color .2s;
        }

        .vip-card:hover { border-color: rgba(255,255,255,0.28); }
        .vip-card.selected { border-color: var(--accent) !important; background: rgba(255,255,255,0.06); }

        .vip-title { font-size: 15px; font-weight: 700; margin-bottom: 2px; }
        .vip-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .vip-card-main { flex: 1; min-width: 220px; }
        .vip-card-side { width: 170px; min-width: 170px; display: flex; align-items: flex-start; justify-content: flex-end; gap: 18px; }

        .vip-price-tag { font-size: 18px; font-weight: 800; color: var(--accent); text-align: right; flex-shrink: 0; }

        .vip-btn {
            background: linear-gradient(135deg, #a774ff 0%, #7c3aed 100%);
            color: #fff;
            font-weight: 700;
            border: none;
            padding: 8px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: all .2s;
            white-space: nowrap;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(167, 116, 255, 0.3);
        }

        .vip-btn:hover {
            box-shadow: 0 6px 16px rgba(167, 116, 255, 0.45);
            transform: translateY(-2px);
        }

        /* Step Indicator */
        .checkout-steps {
            display: flex !important;
            justify-content: center;
            align-items: flex-start;
            margin: 2rem 0;
            padding: 0;
            list-style: none;
            gap: 0;
            flex-wrap: nowrap !important;
            width: 100%;
        }

        .step {
            flex: 1 1 0;
            min-width: 0;
            text-align: center;
            position: relative;
            padding: 0 0.5rem;
        }

        .step::after {
            content: '';
            position: absolute;
            top: 20px;
            left: calc(50% + 22px);
            width: calc(100% - 44px);
            height: 2px;
            background: rgba(255,255,255,0.14);
            z-index: 0;
        }

        .step:last-child::after { display: none; }
        .step.completed::after, .step.active::after { background: linear-gradient(90deg, #a774ff, #7c3aed); }

        .step-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.7);
            line-height: 1;
            font-weight: bold;
            margin: 0 auto 0.5rem;
            border: 2px solid rgba(255,255,255,0.18);
            position: relative;
            z-index: 1;
            transition: all .2s;
        }

        .step.active .step-number {
            background: linear-gradient(135deg, #a774ff 0%, #7c3aed 100%);
            border-color: #7c3aed;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(167,116,255,0.18), 0 4px 12px rgba(124,58,237,0.4);
        }

        .step.completed .step-number {
            background: linear-gradient(135deg, #a774ff 0%, #5b21b6 100%);
            border-color: #7c3aed;
            color: #fff;
        }

        .step-title {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.55);
            margin: 0;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .step.active .step-title, .step.completed .step-title {
            color: #fff;
            font-weight: bold;
        }

        /* Forms */
        .form-row { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: .9rem; }
        .form-group { flex: 1; min-width: 130px; }
        label { font-size: 13px; margin-bottom: 4px; display: block; opacity: .85; }

        input[type="text"], input[type="email"], input[type="tel"], input[type="number"], textarea, select.form-select {
            background: rgba(255,255,255,0.07) !important;
            border: 1px solid #9797a0 !important;
            border-radius: 10px !important;
            color: #fff !important;
            padding: 10px 14px;
            width: 100%;
            margin-bottom: 4px;
            font-size: 15px;
        }

        textarea { min-height: 80px; resize: vertical; }
        input::placeholder, textarea::placeholder { color: rgba(255,255,255,0.35) !important; }
        select.form-select { -webkit-appearance: none !important; appearance: none !important; }
        select option { background: #1a1d2e !important; color: #fff; }

        /* Buttons */
        .btn-next, .submit-btn {
            background: linear-gradient(135deg, #a774ff 0%, #7c3aed 50%, #5b21b6 100%) !important;
            color: #fff !important;
            border: none;
            padding: 11px 28px;
            border-radius: 25px;
            font-weight: 700;
            cursor: pointer;
            font-size: 15px;
            min-width: 180px;
            transition: all .2s;
            box-shadow: 0 6px 20px rgba(167, 116, 255, 0.4);
        }

        .btn-next:hover, .submit-btn:hover {
            box-shadow: 0 8px 28px rgba(167, 116, 255, 0.55);
            transform: translateY(-2px);
        }

        .btn-prev {
            background: #555 !important;
            color: #fff !important;
            border: none;
            padding: 11px 28px;
            border-radius: 25px;
            font-weight: 700;
            cursor: pointer;
            font-size: 15px;
            min-width: 140px;
            transition: all .2s;
        }

        .btn-prev:hover { opacity: 0.9; transform: translateY(-2px); }

        /* Cart Section */
        #cart-section {
            background: linear-gradient(145deg, rgba(255,255,255,0.12), rgba(255,255,255,0.03)), radial-gradient(circle at top right, color-mix(in srgb, var(--accent) 22%, transparent), transparent 48%), linear-gradient(135deg, rgba(10, 16, 32, 0.96), rgba(7, 11, 22, 0.94));
            border: 1px solid color-mix(in srgb, var(--accent) 44%, rgba(255,255,255,0.12));
            border-radius: 16px;
            padding: 18px 20px;
            margin-bottom: 1.2rem;
            display: none;
            box-shadow: 0 20px 42px rgba(0,0,0,0.24);
            backdrop-filter: blur(10px);
        }

        /* Footer */
        .aff-footer {
            border-top: 1px solid rgba(255,255,255,0.08);
            background: transparent;
            padding: 28px 0 8px;
        }

        .cv-footer-inner {
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            gap: 28px;
            align-items: flex-start;
        }

        .cv-footer-brand { display: flex; flex-direction: column; align-items: flex-start; gap: 7px; flex-shrink: 0; min-width: 128px; }
        .cv-footer-logo { height: 34px; width: auto; display: block; opacity: 0.82; }

        .cv-footer-legal {
            color: rgba(255,255,255,0.32);
            font-size: 11px;
            line-height: 1.8;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1 1 auto;
        }

        .cv-footer-legal a {
            color: rgba(255,255,255,0.5);
            text-decoration: underline;
        }

        .cv-footer-legal a:hover { color: rgba(255,255,255,0.85); }

        .cv-footer-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            padding: 14px 0 16px;
            font-size: 11px;
            color: rgba(255,255,255,0.35);
        }

        .cv-footer-bar-socials { display: inline-flex; gap: 8px; }
        .cv-footer-bar-social {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(167, 116, 255, 0.1);
            border: 1px solid rgba(167, 116, 255, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: rgba(167, 116, 255, 0.7) !important;
            text-decoration: none !important;
            transition: all .15s;
            font-size: 11px;
        }

        .cv-footer-bar-social:hover {
            background: rgba(167, 116, 255, 0.25);
            border-color: rgba(167, 116, 255, 0.6);
            color: #a774ff !important;
            transform: translateY(-2px);
        }

        /* Modals */
        .modal-content {
            background: rgba(9, 13, 24, 0.96);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px;
            overflow: hidden;
        }

        .modal-header {
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding: 12px 16px;
        }

        .btn-close { filter: invert(1) grayscale(1); opacity: .9; }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .aff-gallery { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .vip-card-side { width: 100%; min-width: 100%; justify-content: space-between; margin-top: 8px; }
            .package-search-wrap { grid-template-columns: 1fr; }
            .step-number { width: 32px; height: 32px; font-size: 13px; }
            .step-title { font-size: 0.72rem; }
            .checkout-steps { margin: 1.25rem 0; }
        }

        @media (max-width: 600px) {
            .aff-gallery { grid-template-columns: 1fr; }
            .cv-footer-inner { flex-direction: column; gap: 16px; }
            .cv-footer-bar { justify-content: center; text-align: center; flex-direction: column; align-items: center; }
            .step-number { width: 28px; height: 28px; font-size: 12px; }
            .step-title { font-size: 0.65rem; }
        }
    </style>
</head>
<body>

<section class="aff-hero">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="aff-profile-head">
                <div class="aff-avatar-wrap">
                    @if($affiliate->profile_image)
                        <img src="{{ asset('uploads/' . $affiliate->profile_image) }}" alt="Profile" class="aff-avatar">
                    @else
                        <div class="aff-initials">{{ strtoupper(substr($affiliate->display_name ?: $affiliate->user->name, 0, 2)) }}</div>
                    @endif
                </div>
                <div class="aff-profile-content">
                    <h2 class="aff-profile-name">{{ $affiliate->display_name ?: $affiliate->user->name }}</h2>
                    @if($affiliate->description)
                        <p class="aff-profile-desc">{{ $affiliate->description }}</p>
                    @endif
                </div>
            </div>
            <div class="aff-socials">
                @if($affiliate->facebook_url)<a href="{{ $affiliate->facebook_url }}" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>@endif
                @if($affiliate->instagram_url)<a href="{{ $affiliate->instagram_url }}" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>@endif
                @if($affiliate->tiktok_url)<a href="{{ $affiliate->tiktok_url }}" target="_blank" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>@endif
                @if($affiliate->youtube_url)<a href="{{ $affiliate->youtube_url }}" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>@endif
                <button type="button" class="aff-share-btn" id="aff-share-page-btn" aria-label="Share this page">
                    <i class="fas fa-share-nodes"></i> Share
                </button>
            </div>
        </div>
    </div>
</section>

<main>
<div class="container py-4">
    <section class="aff-banner {{ $affiliate->banner_image ? 'has-image' : '' }}">
        <div class="aff-banner-content">
            <div class="aff-kicker">{{ $isEntertainerProfile ? 'Entertainer' : 'Affiliate' }} Booking</div>
            <div class="aff-display-title">{{ $affiliate->hero_title ?: ($affiliate->display_name ?: $affiliate->user->name) }}</div>
            <div class="aff-display-copy">
                {{ $affiliate->hero_subtitle ?: ($affiliate->description ?: 'Book premium experiences from our exclusive partners.') }}
            </div>
            @if(!empty($affiliate->gallery_images))
                <div class="aff-gallery">
                    @foreach($affiliate->gallery_images as $galleryImage)
                        <div class="aff-gallery-item">
                            <img src="{{ asset('uploads/' . $galleryImage) }}" alt="Gallery" data-bs-toggle="modal" data-bs-target="#checkoutGalleryModal" data-image="{{ asset('uploads/' . $galleryImage) }}">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Package Search -->
    <div class="package-search-wrap">
        <div class="package-search-field">
            <label>Search Package</label>
            <input type="text" id="package-search-text" placeholder="Search package, club, or location">
        </div>
        <div class="package-search-field">
            <label>Location</label>
            <select id="package-location-filter">
                <option value="">All Locations</option>
                @foreach($uniqueClubsForFilter as $club)
                    <option value="{{ $club->slug }}">{{ $club->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="button" id="package-search-clear" class="package-search-clear">Clear</button>
    </div>

    <!-- Packages -->
    <div class="package">
        @foreach($clubGroups as $clubId => $mappings)
            @php $club = $mappings->first()->package->website; @endphp
            <div class="package-group" data-club-slug="{{ $club->slug }}">
                @foreach($mappings->groupBy('package.package_category_id') as $categoryId => $categoryMappings)
                    <div class="package-category-group">
                        @php $category = $categoryMappings->first()->package->packageCategory; @endphp
                        <h3 class="package-category-title">{{ $category->name }}</h3>
                        @foreach($categoryMappings as $index => $mapping)
                            @php $package = $mapping->package; @endphp
                            <div class="vip-card" data-package-id="{{ $package->id }}" data-club-id="{{ $club->id }}">
                                <div class="vip-card-main">
                                    <div class="vip-title">{{ $package->name }}</div>
                                    <div class="vip-meta">
                                        <span>{{ $club->name }}</span>
                                        <span>•</span>
                                        <span>{{ $club->location }}</span>
                                    </div>
                                </div>
                                <div class="vip-card-side">
                                    <div class="vip-price-tag">${{ number_format($package->price) }}</div>
                                    <button type="button" class="vip-btn" data-package-id="{{ $package->id }}" data-club-id="{{ $club->id }}">Add to Cart</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <!-- Cart Display -->
    <div id="cart-section" style="display: none;">
        <h4>Your Selections</h4>
        <div id="cart-list"></div>
        <div id="cart-total"></div>
    </div>

    <!-- Step Indicator -->
    <ul class="checkout-steps" id="checkout-steps">
        <li class="step active" id="step-1">
            <div class="step-number">1</div>
            <p class="step-title">Details</p>
        </li>
        <li class="step" id="step-2">
            <div class="step-number">2</div>
            <p class="step-title">Transportation</p>
        </li>
        <li class="step" id="step-3">
            <div class="step-number">3</div>
            <p class="step-title">Payment</p>
        </li>
    </ul>

    <!-- Checkout Sections -->
    <form id="payment-form" method="POST" action="{{ route('checkout.store') }}">
        @csrf

        <!-- Step 1: Personal Details -->
        <section class="checkout-section active" id="section-1" style="display: block;">
            <h2>Personal Details</h2>
            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" required>
                </div>
            </div>
            <div class="form-row" style="margin-top: 20px;">
                <button type="button" class="btn-next" onclick="nextStep(1)">Next</button>
            </div>
        </section>

        <!-- Step 2: Transportation -->
        <section class="checkout-section" id="section-2" style="display: none;">
            <h2>Transportation</h2>
            <div class="form-row">
                <div class="form-group">
                    <label>Pickup Time</label>
                    <input type="time" name="pickup_time">
                </div>
            </div>
            <div class="form-row" style="margin-top: 20px; gap: 10px; justify-content: center;">
                <button type="button" class="btn-prev" onclick="prevStep(2)">← Previous</button>
                <button type="button" class="btn-next" onclick="nextStep(2)">Next</button>
            </div>
        </section>

        <!-- Step 3: Payment -->
        <section class="checkout-section" id="section-3" style="display: none;">
            <h2>Payment</h2>
            <div class="form-row">
                <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" name="card_number" placeholder="•••• •••• •••• ••••">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Expiry</label>
                    <input type="text" name="card_expiry" placeholder="MM/YY">
                </div>
                <div class="form-group">
                    <label>CVV</label>
                    <input type="text" name="card_cvv" placeholder="•••">
                </div>
            </div>
            <div class="form-row" style="margin-top: 20px; gap: 10px; justify-content: center;">
                <button type="button" class="btn-prev" onclick="prevStep(3)">← Previous</button>
                <button type="submit" class="submit-btn">Complete Purchase</button>
            </div>
        </section>
    </form>
</div>
</main>

<!-- Footer -->
<footer class="aff-footer">
    <div class="container">
        <div class="cv-footer-inner">
            <div class="cv-footer-brand">
                <img src="{{ asset('images/logo.png') }}" alt="CartVIP" class="cv-footer-logo">
                <div class="cv-footer-powered">Powered by CartVIP</div>
            </div>
            <div class="cv-footer-legal">
                <p>&copy; {{ date('Y') }} CartVIP. All rights reserved.</p>
                <p><a href="#">Privacy Policy</a> • <a href="#">Terms of Service</a></p>
            </div>
        </div>
        <div class="cv-footer-bar">
            <div class="cv-footer-bar-copy">
                <span>Follow us:</span>
            </div>
            <div class="cv-footer-bar-socials">
                <a href="#" class="cv-footer-bar-social"><i class="fab fa-facebook"></i></a>
                <a href="#" class="cv-footer-bar-social"><i class="fab fa-instagram"></i></a>
                <a href="#" class="cv-footer-bar-social"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </div>
</footer>

<script>
function nextStep(step) {
    document.getElementById(`section-${step}`).style.display = 'none';
    document.getElementById(`section-${step + 1}`).style.display = 'block';
    document.getElementById(`step-${step}`).classList.remove('active');
    document.getElementById(`step-${step}`).classList.add('completed');
    document.getElementById(`step-${step + 1}`).classList.add('active');
}

function prevStep(step) {
    document.getElementById(`section-${step}`).style.display = 'none';
    document.getElementById(`section-${step - 1}`).style.display = 'block';
    document.getElementById(`step-${step}`).classList.remove('active');
    document.getElementById(`step-${step - 1}`).classList.add('active');
}

// Package search
document.getElementById('package-search-text').addEventListener('input', filterPackages);
document.getElementById('package-location-filter').addEventListener('change', filterPackages);
document.getElementById('package-search-clear').addEventListener('click', () => {
    document.getElementById('package-search-text').value = '';
    document.getElementById('package-location-filter').value = '';
    filterPackages();
});

function filterPackages() {
    const text = document.getElementById('package-search-text').value.toLowerCase();
    const location = document.getElementById('package-location-filter').value;
    document.querySelectorAll('.vip-card').forEach(card => {
        const title = card.querySelector('.vip-title').textContent.toLowerCase();
        const club = card.querySelector('.vip-card-main').textContent.toLowerCase();
        const cardLocation = card.getAttribute('data-club-slug');
        const match = (text === '' || title.includes(text) || club.includes(text)) &&
                      (location === '' || cardLocation === location);
        card.style.display = match ? 'flex' : 'none';
    });
}

// Cart functionality
document.querySelectorAll('.vip-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        alert('Added to cart!');
    });
});
</script>

</body>
</html>

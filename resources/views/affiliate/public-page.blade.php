<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $affiliateTitle = $affiliate->display_name ?: $affiliate->user->name;
        $affiliateDescription = trim((string) ($affiliate->description ?: $affiliate->hero_subtitle ?: 'Book premium experiences from multiple clubs in one curated flow.'));
        $affiliateDescription = \Illuminate\Support\Str::limit($affiliateDescription, 160);
        $rawAffiliateMetaImage = $affiliate->profile_image
            ? asset('uploads/' . $affiliate->profile_image)
            : (optional($affiliate->website)->logo
                ? asset('uploads/' . optional($affiliate->website)->logo)
                : 'https://ui-avatars.com/api/?name=' . urlencode($affiliateTitle) . '&background=1a75ff&color=ffffff&size=1200');
        $affiliateMetaImage = str_contains($rawAffiliateMetaImage, '?')
            ? $rawAffiliateMetaImage . '&w=1200&h=630&fit=crop'
            : $rawAffiliateMetaImage . '?w=1200&h=630&fit=crop';
        $affiliateMetaUrl = request()->url();
    @endphp
    <meta name="description" content="{{ $affiliateDescription }}">
    <link rel="canonical" href="{{ $affiliateMetaUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ optional($affiliate->website)->name ?: 'CartVIP' }}">
    <meta property="og:title" content="{{ $affiliateTitle }}">
    <meta property="og:description" content="{{ $affiliateDescription }}">
    <meta property="og:url" content="{{ $affiliateMetaUrl }}">
    <meta property="og:image" content="{{ $affiliateMetaImage }}">
    <meta property="og:image:secure_url" content="{{ $affiliateMetaImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $affiliateTitle }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $affiliateTitle }}">
    <meta name="twitter:description" content="{{ $affiliateDescription }}">
    <meta name="twitter:image" content="{{ $affiliateMetaImage }}">
    <title>{{ $affiliate->display_name ?: $affiliate->user->name }} — Book Now</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --aff-accent: #ffcc00;
            --aff-bg: #0b0e1a;
            --aff-text: #e8eaf6;
            --aff-theme: #1a75ff;
        }
        * { box-sizing: border-box; }
        body {
            background:
                radial-gradient(circle at top right, rgba(26, 117, 255, 0.12), transparent 34%),
                linear-gradient(180deg, #0b0e1a 0%, #0f1526 52%, #0b0e1a 100%);
            color: var(--aff-text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        a { color: var(--aff-accent); }

        .package-category-tile {
            background: var(--aff-accent) !important;
            color: #000 !important;
            border: 1px solid var(--aff-accent) !important;
            box-shadow: 0 12px 24px rgba(255, 204, 0, 0.12);
        }
        .package-category-tile:hover {
            filter: brightness(1.03);
        }
        .package-category-tile.active {
            background: #101725 !important;
            color: var(--aff-accent) !important;
            border-color: var(--aff-accent) !important;
            box-shadow: none;
        }

        /* Hero */
        .aff-hero {
            background: linear-gradient(135deg, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0.02) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.1);
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
            background: linear-gradient(145deg, rgba(255, 204, 0, 0.95), rgba(255, 204, 0, 0.35));
            box-shadow: 0 12px 26px rgba(0, 0, 0, 0.35);
            flex-shrink: 0;
        }
        .aff-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(11, 14, 26, 0.85);
            display: block;
        }
        .aff-initials {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid rgba(11, 14, 26, 0.85);
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 800;
        }
        .aff-profile-content {
            min-width: 0;
            max-width: min(760px, 100%);
        }
        .aff-profile-name {
            margin: 0;
            font-size: clamp(1.1rem, 1.2vw, 1.35rem);
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: .01em;
            color: #f8f9ff;
        }
        .aff-profile-desc {
            margin: 6px 0 0;
            max-width: 760px;
            color: rgba(232, 234, 246, 0.78);
            font-size: 13px;
            line-height: 1.5;
            max-width: 100%;
            overflow-wrap: anywhere;
            word-break: break-word;
            white-space: normal;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .aff-feed-cta {
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 11px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.06);
            color: var(--aff-text);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .01em;
            text-decoration: none;
            transition: all .2s ease;
        }
        .aff-feed-cta:hover {
            color: var(--aff-accent);
            border-color: var(--aff-accent);
            transform: translateY(-1px);
        }
        .aff-socials {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            flex-wrap: wrap;
        }
        .aff-socials a {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.16);
            background: rgba(255,255,255,0.04);
            color: var(--aff-text);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            opacity: .92;
            transition: all .2s ease;
        }
        .aff-socials a:hover {
            color: var(--aff-accent);
            border-color: var(--aff-accent);
            transform: translateY(-1px);
        }

        .aff-share-btn {
            width: auto;
            height: 34px;
            padding: 0 12px;
            gap: 7px;
            font-weight: 700;
            font-size: 12px;
            border: 1px solid rgba(255,255,255,0.16);
            border-radius: 999px;
            background: rgba(255,255,255,0.04);
            color: var(--aff-text);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .2s ease;
        }

        .aff-share-btn:hover {
            color: var(--aff-accent);
            border-color: var(--aff-accent);
            transform: translateY(-1px);
        }

        .aff-share-menu {
            position: fixed;
            z-index: 1600;
            min-width: 190px;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            background: rgba(8, 14, 26, 0.98);
            box-shadow: 0 22px 40px rgba(0,0,0,0.35);
            padding: 8px;
            display: none;
            gap: 6px;
        }

        .aff-share-menu.is-open {
            display: grid;
        }

        .aff-share-option {
            appearance: none;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.04);
            color: var(--aff-text);
            border-radius: 9px;
            padding: 9px 10px;
            font-size: .84rem;
            text-align: left;
            cursor: pointer;
        }

        .aff-share-option:hover {
            border-color: rgba(255, 204, 0, 0.5);
            background: rgba(255, 204, 0, 0.16);
        }

        /* Package location badge */
        .club-badge { display:inline-block; font-size:10px; font-weight:700; letter-spacing:.6px; text-transform:uppercase; padding:2px 8px; border-radius:4px; background:rgba(255,255,255,0.1); margin-bottom:6px; }

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
        .package-search-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .package-search-field label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .6px;
            opacity: .68;
            font-weight: 700;
            margin: 0;
        }
        .package-search-wrap input,
        .package-search-wrap select {
            width: 100%;
            margin: 0 !important;
            background: rgba(255,255,255,0.08) !important;
            border: 1px solid rgba(255,255,255,0.2) !important;
            border-radius: 10px !important;
            color: var(--aff-text) !important;
            padding: 9px 11px !important;
            min-height: 40px;
        }
        .package-search-wrap input::placeholder {
            color: rgba(255,255,255,0.5) !important;
        }
        .package-search-clear {
            appearance: none;
            border: 1px solid rgba(255,255,255,0.22);
            background: rgba(255,255,255,0.08);
            color: var(--aff-text);
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .4px;
            min-height: 40px;
            padding: 0 14px;
            cursor: pointer;
            transition: border-color .2s ease, background .2s ease;
        }
        .package-search-clear:hover {
            border-color: var(--aff-accent);
            background: rgba(255, 204, 0, 0.14);
            color: #fff;
        }
        .package-search-empty {
            display: none;
            margin: 6px 2px 12px;
            font-size: 13px;
            opacity: .75;
        }

        /* Package cards */
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
        .vip-card.selected { border-color: var(--aff-accent) !important; background: rgba(255,255,255,0.06); }
        .vip-title { font-size:15px; font-weight:700; margin-bottom:2px; }
        .vip-meta { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
        .vip-title-row { display:flex; align-items:center; gap:8px; }
        .vip-card-main { flex:1; min-width:220px; }
        .vip-card-side {
            width: 170px;
            min-width: 170px;
            display: flex;
            align-items: flex-start;
            justify-content: flex-end;
            gap: 18px;
        }
        .vip-guest-control {
            width: 76px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            text-align: center;
            flex-shrink: 0;
        }
        .vip-guest-label {
            font-size: 11px;
            opacity: .6;
            margin-bottom: 4px;
            min-height: 16px;
        }
        .package_number_of_guestss {
            width: 80px !important;
            min-width: 80px;
            padding: 5px 8px !important;
            margin-bottom: 0 !important;
            text-align: center;
        }
        .vip-price-tag {
            width: 76px;
            min-width: 76px;
            font-size: 18px;
            font-weight: 800;
            color: var(--aff-accent);
            text-align: right;
            line-height: 36px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .vip-btn {
            background: var(--aff-accent);
            color: #000;
            font-weight: 700;
            border: none;
            padding: 8px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: opacity .2s, transform .15s;
            white-space: nowrap;
            font-size: 14px;
        }
        .vip-btn:hover { opacity:.85; transform:translateY(-1px); }
        .club-detail-trigger {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:24px;
            height:24px;
            border-radius:50%;
            border:1px solid rgba(255,255,255,0.18);
            background:rgba(255,255,255,0.07);
            color:var(--aff-text);
            cursor:pointer;
            font-size:12px;
        }
        .club-detail-trigger:hover { border-color:var(--aff-accent); color:var(--aff-accent); }
        .club-popover { max-width: 340px; }
        .club-popover .popover-header { background:#141a2d; color:#fff; border-bottom:1px solid rgba(255,255,255,0.08); }
        .club-popover .popover-body { background:#0e1324; color:#d8def0; }
        .club-popover-logo { width:100%; max-height:120px; object-fit:contain; margin-bottom:10px; border-radius:8px; background:rgba(255,255,255,0.03); }
        .club-tooltip-row { margin-bottom:6px; font-size:13px; line-height:1.45; }
        .club-tooltip-row i { width:16px; color:var(--aff-accent); }
        .club-tooltip-section-title {
            margin-top: 10px;
            margin-bottom: 6px;
            padding-top: 8px;
            border-top: 1px solid rgba(255,255,255,0.12);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: var(--aff-accent);
        }

        .aff-banner {
            position:relative;
            border:1px solid rgba(255,255,255,0.08);
            border-radius:18px;
            overflow:hidden;
            min-height:220px;
            margin:20px 0 24px;
            background:
                linear-gradient(125deg, rgba(8,11,22,0.82), rgba(8,11,22,0.52)),
                radial-gradient(circle at top right, rgba(255,255,255,0.08), transparent 35%),
                var(--aff-theme);
        }
        .aff-banner.has-image {
            background:
                linear-gradient(125deg, rgba(8,11,22,0.84), rgba(8,11,22,0.48)),
                url('{{ $affiliate->banner_image ? asset('uploads/' . $affiliate->banner_image) : '' }}') center/contain no-repeat,
                #101522;
        }
        .aff-banner-content { position:relative; z-index:1; padding:28px; }
        .aff-kicker { font-size:11px; letter-spacing:1px; text-transform:uppercase; opacity:.64; font-weight:700; }
        .aff-display-title { font-size:clamp(2rem, 5vw, 3.8rem); line-height:1; font-weight:800; max-width:unset; margin:10px 0 12px; }
        .aff-display-copy {
            max-width: 620px;
            font-size: 15px;
            opacity: .82;
            max-width: 100%;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .aff-gallery { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:10px; margin-top:18px; }
        .aff-gallery-item {
            position: relative;
            width: 100%;
            min-height: 125px;
            padding: 0;
            border: 1px solid rgba(239, 190, 111, 0.28);
            border-radius: 14px;
            overflow: hidden;
            background: rgba(255,255,255,0.04);
            cursor: pointer;
            transition: transform .24s ease, border-color .24s ease, box-shadow .24s ease, filter .24s ease;
        }
        .aff-gallery-item::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.14), inset 0 0 28px rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .aff-gallery-item:hover,
        .aff-gallery-item:focus-visible {
            transform: translateY(-3px);
            border-color: rgba(239, 190, 111, 0.46);
            box-shadow: 0 18px 34px rgba(0,0,0,0.28);
            filter: brightness(1.02);
            outline: none;
        }
        .aff-gallery-item img { width:100%; height:100%; object-fit:cover; display:block; }
        .checkout-gallery-modal .modal-content {
            background: rgba(9, 13, 24, 0.96);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px;
            overflow: hidden;
        }
        .checkout-gallery-modal .modal-header {
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding: 12px 16px;
        }
        .checkout-gallery-modal .btn-close {
            filter: invert(1) grayscale(1);
            opacity: .9;
        }
        .checkout-gallery-modal .modal-body {
            padding: 0;
            background: #030712;
        }
        .checkout-gallery-modal-image {
            width: 100%;
            max-height: min(82vh, 980px);
            object-fit: contain;
            display: block;
            background: #030712;
        }
        .aff-story {
            margin: 0 0 24px;
            padding: 18px 20px;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            background: rgba(255,255,255,0.03);
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .aff-story,
        .aff-story * {
            max-width: 100%;
        }
        .aff-story img,
        .aff-story iframe,
        .aff-story video,
        .aff-story table,
        .aff-story pre,
        .aff-story code,
        .aff-display-copy img,
        .aff-display-copy iframe,
        .aff-display-copy video,
        .aff-display-copy table,
        .aff-display-copy pre,
        .aff-display-copy code {
            max-width: 100%;
        }

        .aff-location-card {
            margin: 0 0 24px;
            padding: 18px;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            background: rgba(255,255,255,0.03);
        }
        .aff-location-shell {
            display: grid;
            grid-template-columns: minmax(230px, 0.95fr) minmax(0, 1.25fr);
            gap: 16px;
            align-items: stretch;
        }
        .aff-location-copy {
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 12px;
            background: rgba(255,255,255,0.04);
            padding: 16px;
        }
        .aff-location-kicker {
            display: inline-block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .85px;
            opacity: .62;
            margin-bottom: 8px;
        }
        .aff-location-title {
            margin: 0 0 8px;
            font-size: 1.4rem;
            font-weight: 700;
        }
        .aff-location-address {
            margin-bottom: 14px;
            opacity: .88;
            line-height: 1.6;
        }
        .aff-location-contact {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .aff-location-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255,255,255,0.16);
            border-radius: 10px;
            background: rgba(255,255,255,0.04);
            padding: 8px 11px;
            color: var(--aff-text);
            text-decoration: none;
            font-size: 14px;
        }
        .aff-location-chip:hover {
            border-color: var(--aff-accent);
            color: var(--aff-accent);
        }
        .aff-location-map {
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            overflow: hidden;
            min-height: 300px;
            background: rgba(255,255,255,0.02);
        }
        .aff-location-map iframe {
            width: 100%;
            min-height: 300px;
            height: 100%;
            border: 0;
            display: block;
        }

        /* Steps */
        .checkout-steps { display:flex; justify-content:center; align-items:center; margin:1.5rem 0; padding:0; list-style:none; }
        .step { flex:1; text-align:center; position:relative; padding:0 .5rem; }
        .step-number { display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:50%; background:#444; color:#fff; font-weight:bold; margin-bottom:.4rem; border:2px solid #444; font-size:15px; }
        .step.active .step-number { background:var(--aff-accent); border-color:var(--aff-accent); color:#000; }
        .step.completed .step-number { background:#28a745; border-color:#28a745; color:#fff; }
        .step-title { font-size:.8rem; color:#888; margin:0; }
        .step.active .step-title, .step.completed .step-title { color:#ddd; font-weight:600; }
        .step::after { content:''; position:absolute; top:19px; right:-50%; width:100%; height:2px; background:#444; z-index:-1; }
        .step:last-child::after { display:none; }
        .step.completed::after { background:#28a745; }
        .checkout-section { display:none; }
        .checkout-section.active { display:block; }

        /* Forms */
        .form-row { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:.9rem; }
        .form-group { flex:1; min-width:130px; }
        label { font-size:13px; margin-bottom:4px; display:block; opacity:.85; }
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
        textarea { min-height:80px; resize:vertical; }
        input::placeholder, textarea::placeholder { color:rgba(255,255,255,0.35) !important; }
        select.form-select { -webkit-appearance:none !important; appearance:none !important; }
        select option { background:#1a1d2e !important; color:#fff; }

        #Pick-up-time,
        input[name="transportation_pickup_time"].flatpickr-time {
            background: #ffffff !important;
            color: #111111 !important;
            -webkit-text-fill-color: #111111 !important;
            border-color: #d2d7e3 !important;
        }

        #Pick-up-time::placeholder,
        input[name="transportation_pickup_time"].flatpickr-time::placeholder {
            color: #555555 !important;
        }

        .date-input-wrapper {
            position: relative;
            width: 100%;
        }
        #package_use_date {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            padding: 10px 45px 10px 14px !important;
            margin-bottom: 0 !important;
        }
        #package_use_date::-webkit-calendar-picker-indicator {
            opacity: 0;
            display: none;
        }
        .custom-calendar-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            cursor: pointer;
            background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>') no-repeat center;
            background-size: contain;
            opacity: .8;
        }

        #package_use_date{
            width: 33%;
            border-radius: 10px;
            background: #1c1f29;
            color: #fff;
            -webkit-text-fill-color: #fff !important;
            opacity: 1 !important;
            text-shadow: 0 0 0 #fff;
        }

        #package_use_date[readonly],
        #package_use_date.flatpickr-input[readonly] {
            color: #fff !important;
            -webkit-text-fill-color: #fff !important;
            opacity: 1 !important;
            text-shadow: 0 0 0 #fff;
        }

        @media (max-width: 767.98px) {
            .vip-card-side {
                width: 100%;
                min-width: 100%;
                justify-content: space-between;
                margin-top: 8px;
            }

            #package_use_date {
                width: 100%;
            }
        }

        /* Buttons */
        .btn-next, .submit-btn {
            background: var(--aff-accent) !important;
            color: #000 !important;
            border: none;
            padding: 11px 28px;
            border-radius: 25px;
            font-weight: 700;
            cursor: pointer;
            font-size: 15px;
            min-width: 180px;
            transition: opacity .2s, transform .15s;
        }
        .btn-prev { background:#555 !important; color:#fff !important; border:none; padding:11px 28px; border-radius:25px; font-weight:700; cursor:pointer; font-size:15px; min-width:140px; }
        .btn-next:hover, .submit-btn:hover { opacity:.85; transform:translateY(-1px); }
        .step-navigation { display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin:1.5rem 0; }
        .same-as-info, .same-as-info-transport { background:var(--aff-accent) !important; color:#000 !important; border:none; padding:9px 20px; border-radius:25px; font-weight:700; margin-bottom:16px; cursor:pointer; font-size:13px; display:inline-block; }

        /* Cart */
        #cart-section {
            background:
                linear-gradient(145deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02)),
                linear-gradient(135deg, color-mix(in srgb, var(--aff-accent) 16%, transparent), rgba(11, 16, 29, 0.94) 58%);
            border:1px solid color-mix(in srgb, var(--aff-accent) 34%, rgba(255,255,255,0.12));
            border-radius:12px;
            padding:16px 18px;
            margin-bottom:1.2rem;
            display:none;
            box-shadow:0 16px 34px rgba(0,0,0,0.18), inset 0 1px 0 rgba(255,255,255,0.08);
            backdrop-filter:blur(10px);
        }

        /* Addons */
        .addons-wrap { display:none; margin:12px 0 4px; }
        .addon-item { display:flex; justify-content:space-between; align-items:center; padding:7px 0; border-bottom:1px solid rgba(255,255,255,0.07); }
        .addon-item:last-child { border-bottom:none; }
        .addon-item input[type="checkbox"] {
            -webkit-appearance: none;
            appearance: none;
            width: 46px !important;
            height: 26px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.28);
            background: rgba(255,255,255,0.16);
            position: relative;
            margin: 0 !important;
            padding: 0 !important;
            cursor: pointer;
            transition: background .2s ease, border-color .2s ease;
        }
        .addon-item input[type="checkbox"]::before {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fff;
            transition: transform .2s ease;
        }
        .addon-item input[type="checkbox"]:checked {
            background: var(--aff-accent);
            border-color: var(--aff-accent);
        }
        .addon-item input[type="checkbox"]:checked::before {
            transform: translateX(20px);
        }

        #addonSelectionModal .addon-modal-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }
        #addonSelectionModal .addon-modal-label {
            display: inline-block;
            color: #e8eaf6 !important;
            font-size: 14px;
        }
        #addonSelectionModal .addon-modal-desc {
            display: block;
            margin-top: 3px;
            color: rgba(232, 234, 246, 0.7);
            font-size: 12px;
            line-height: 1.45;
        }
        #addonSelectionModal .addon-switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 26px;
            flex-shrink: 0;
        }
        #addonSelectionModal .addon-modal-switch-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        #addonSelectionModal .addon-switch-slider {
            position: absolute;
            inset: 0;
            cursor: pointer;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 999px;
            transition: all .2s ease;
        }
        #addonSelectionModal .addon-switch-slider::before {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            left: 2px;
            top: 2px;
            border-radius: 50%;
            background: #fff;
            transition: transform .2s ease;
        }
        #addonSelectionModal .addon-modal-switch-input:checked + .addon-switch-slider {
            background: linear-gradient(135deg, #f7e2b4 0%, #ddb774 52%, #ffcc00 100%);
            border-color: rgba(247,226,180,0.65);
        }
        #addonSelectionModal .addon-modal-switch-input:checked + .addon-switch-slider::before {
            transform: translateX(20px);
        }

        /* Price summary */
        .price-summary { display:none; background:rgba(255,255,255,0.04); border-radius:10px; padding:14px 16px; margin-bottom:1rem; font-size:14px; }

        /* Promo */
        #promo-section { display:none; margin-bottom:1rem; }
        .promo-row { display:flex; }
        #promo_code { border-top-right-radius:0 !important; border-bottom-right-radius:0 !important; margin-bottom:0; }
        #applyPromoBtn { background:var(--aff-accent); color:#000; font-weight:700; border:none; border-top-right-radius:10px; border-bottom-right-radius:10px; padding:0 18px; cursor:pointer; white-space:nowrap; font-size:14px; }

        /* StripeElement */
        .StripeElement { padding:10px 14px; border:1px solid #9797a0; border-radius:10px; margin-bottom:8px; background:rgba(255,255,255,0.07); }

        /* Consent checkboxes */
        .consent-label { display:flex; gap:10px; align-items:flex-start; cursor:pointer; margin-bottom:10px; font-size:13px; }
        .consent-label span { flex:1; line-height:1.4; }
        .consent-label input {
            -webkit-appearance: none;
            appearance: none;
            width: 46px !important;
            height: 26px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.28);
            background: rgba(255,255,255,0.16);
            position: relative;
            margin-top: 0 !important;
            padding: 0 !important;
            flex-shrink: 0;
            cursor: pointer;
            transition: background .2s ease, border-color .2s ease;
        }
        .consent-label input::before {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fff;
            transition: transform .2s ease;
        }
        .consent-label input:checked {
            background: var(--aff-accent);
            border-color: var(--aff-accent);
        }
        .consent-label input:checked::before {
            transform: translateX(20px);
        }
        .consent-label input:focus-visible {
            outline: 2px solid rgba(255,204,0,0.7);
            outline-offset: 2px;
        }

        /* Required field highlight */
        .required-field { border-color:#ff6b6b !important; }

        /* Footer */
        .aff-footer {
            margin-top: 26px;
            border-top: 1px solid rgba(255,255,255,0.08);
            background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
        }
        .aff-footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            padding: 16px 0;
            font-size: 12.5px;
            color: rgba(232,234,246,0.72);
        }
        .aff-footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(232,234,246,0.9);
            font-weight: 700;
            letter-spacing: .02em;
            text-decoration: none;
        }
        .aff-footer-brand .brand-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--aff-accent);
            box-shadow: 0 0 0 5px rgba(255,204,0,0.16);
        }
        .aff-footer-note {
            opacity: .72;
        }

        /* Mobile */
        @media(max-width:768px) {
            .step-title { font-size:.55rem !important; }
            input, select, textarea { font-size:16px !important; }
            .vip-card { flex-direction:column; align-items:flex-start; }
            .package-search-wrap { grid-template-columns: 1fr; }
            .aff-banner-content { padding:22px 18px; }
            .aff-gallery { grid-template-columns:repeat(2, minmax(0, 1fr)); }
            .aff-profile-head {
                width: 100%;
            }
            .aff-profile-desc {
                -webkit-line-clamp: 3;
            }
            .aff-location-shell { grid-template-columns: 1fr; }
            .aff-location-map,
            .aff-location-map iframe { min-height: 250px; }
            .aff-footer-inner {
                justify-content: center;
                text-align: center;
            }
        }
    </style>
</head>
<body>

{{-- Per-club config for JS --}}
<script>
const clubConfigs = {
    @foreach($clubGroups as $clubId => $mappings)
    @php
        $club = $mappings->first()->package->website;
        $sk = $club->stripe_app_key ?? ($setting ? $setting->stripe_key : '');
        $transportConfirmTextJson = json_encode((string) ($club->transportation_confirmation_text ?? 'I confirm I am not arriving via Uber, Lyft, limo, taxi, ride-sharing or any other paid service. I am arriving in a personal vehicle.'));
    @endphp
    @json((string) $club->slug): {
        id: {{ (int) $club->id }},
        slug: @json((string) $club->slug),
        name: @json((string) $club->name),
        color: @json((string) ($club->color ?? '#ffcc00')),
        paymentMethod: @json((string) ($club->payment_method ?? 'authorize')),
        stripeKey: @json((string) ($sk ?? '')),
        gratuityFee: {{ (float)($club->gratuity_fee ?? 0) }},
        gratuityName: @json((string) ($club->gratuity_name ?? 'Gratuity')),
        refundableFee: {{ (float)($club->refundable_fee ?? 0) }},
        refundableName: @json((string) ($club->refundable_name ?? 'Non Refundable Processing Fees')),
        salesTaxFee: {{ (float)($club->sales_tax_fee ?? 0) }},
        salesTaxName: @json((string) ($club->sales_tax_name ?? '0')),
        serviceChargeFee: {{ (float)($club->service_charge_fee ?? 0) }},
        serviceChargeName: @json((string) ($club->service_charge_name ?? '0')),
        processingFee: {{ (float)($club->processing_fee ?? 0) }},
        processingFeeType: @json((string) ($club->processing_fee_type ?? 'percentage')),
        transportConfirmText: {!! $transportConfirmTextJson !!},
        terms: @json((string) ($club->terms ?? '#')),
        privacy: @json((string) ($club->privacy ?? $club->policy ?? '#')),
        promoCodeName: @json((string) ($club->promo_code_name ?? 'Promo Code')),
        location: @json((string) ($club->location ?? '')),
        phone: @json((string) ($club->phone ?? '')),
        email: @json((string) ($club->email ?? '')),
    },
    @endforeach
};
</script>

{{-- Affiliate Hero --}}
@php
    $isEntertainerProfile = $affiliate instanceof \App\Models\Entertainer;
    $entertainerFeedUrl = null;

    if ($isEntertainerProfile && optional($affiliate->website)->slug) {
        $entertainerFeedUrl = $affiliate->feed_model_id
            ? route('club.feed.model.profile', ['slug' => $affiliate->website->slug, 'feedModel' => $affiliate->feedModel])
            : route('club.feed', ['slug' => $affiliate->website->slug]);
    }
@endphp
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
                    @if($entertainerFeedUrl)
                        <a href="{{ $entertainerFeedUrl }}" class="aff-feed-cta">
                            <i class="fas fa-bolt"></i>
                            <span>Click here to see my social feed</span>
                        </a>
                    @endif
                </div>
            </div>
            <div class="aff-socials">
                @if($affiliate->facebook_url)<a href="{{ $affiliate->facebook_url }}" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>@endif
                @if($affiliate->instagram_url)<a href="{{ $affiliate->instagram_url }}" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>@endif
                @if($affiliate->tiktok_url)<a href="{{ $affiliate->tiktok_url }}" target="_blank" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>@endif
                @if($affiliate->youtube_url)<a href="{{ $affiliate->youtube_url }}" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>@endif
                <button
                    type="button"
                    class="aff-share-btn"
                    id="aff-share-page-btn"
                    data-share-url="{{ request()->url() }}"
                    data-share-title="{{ $affiliate->display_name ?: $affiliate->user->name }}"
                    data-share-text="{{ \Illuminate\Support\Str::limit($affiliate->description ?: 'Check out this affiliate page', 100) }}"
                    aria-label="Share this page"
                >
                    <i class="fas fa-share-nodes"></i>
                    Share
                </button>
            </div>
        </div>
    </div>
</section>

<div class="aff-share-menu" id="aff-share-menu" aria-hidden="true">
    <button type="button" class="aff-share-option" data-share-option="whatsapp">WhatsApp</button>
    <button type="button" class="aff-share-option" data-share-option="facebook">Facebook</button>
    <button type="button" class="aff-share-option" data-share-option="instagram">Instagram</button>
    <button type="button" class="aff-share-option" data-share-option="x">X</button>
    <button type="button" class="aff-share-option" data-share-option="linkedin">LinkedIn</button>
    <button type="button" class="aff-share-option" data-share-option="copy">Copy Link</button>
</div>

<main>
<div class="container py-4">

    <section class="aff-banner {{ $affiliate->banner_image ? 'has-image' : '' }}">
        <div class="aff-banner-content">
            <div class="aff-kicker">Affiliate Booking Page</div>
            <div class="aff-display-title">{{ $affiliate->hero_title ?: ($affiliate->display_name ?: $affiliate->user->name) }}</div>
            <div class="aff-display-copy">
                {{ $affiliate->hero_subtitle ?: ($affiliate->description ?: 'Book premium experiences from multiple clubs in one curated flow.') }}
            </div>

            @if(!empty($affiliate->gallery_images))
                <div class="aff-gallery">
                    @foreach($affiliate->gallery_images as $galleryImage)
                        <button type="button" class="aff-gallery-item js-checkout-gallery-trigger" data-gallery-src="{{ asset('uploads/' . $galleryImage) }}" data-gallery-alt="Gallery image {{ $loop->iteration }}">
                            <img src="{{ asset('uploads/' . $galleryImage) }}" alt="Gallery image {{ $loop->iteration }}">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if($affiliate->secondary_description)
        <section class="aff-story">
            {{ $affiliate->secondary_description }}
        </section>
    @endif

    @session('success')
        <div class="alert alert-success">Purchase Successful! Check your email for confirmation.</div>
    @endsession
    @session('error')
        <div class="alert alert-danger">{{ $value }}</div>
    @endsession

    @php
        $featuredClub = optional(optional(optional($clubGroups->first())->first())->package)->website;
    @endphp

    {{-- ===== PACKAGES ===== --}}
    <h5 class="mb-3" style="opacity:.6;font-size:.85rem;text-transform:uppercase;letter-spacing:.8px;font-weight:700;">Select a Package to Book</h5>

    @if($packageCategoryGroups->count())
        @php
            $sortedPackageCategoryGroups = collect($packageCategoryGroups)
                ->sortBy(function ($categoryGroup) {
                    $clubName = strtolower((string) (($categoryGroup['club']->name ?? '')));
                    $categoryName = strtolower((string) (($categoryGroup['name'] ?? '')));
                    return $clubName . ' - ' . $categoryName;
                })
                ->values();
            $uniqueClubsForFilter = $sortedPackageCategoryGroups
                ->pluck('club')
                ->filter()
                ->unique('id')
                ->values();
        @endphp

        <div class="package-search-wrap" id="package-search-wrap">
            <div class="package-search-field">
                <label for="package-search-text">Search</label>
                <input type="text" id="package-search-text" placeholder="Search package, club, or city/location">
            </div>
            <div class="package-search-field">
                <label for="package-location-filter">Filter by Location</label>
                <select id="package-location-filter">
                    <option value="">All Locations</option>
                    @foreach($uniqueClubsForFilter as $clubOption)
                        <option value="{{ $clubOption->id }}">{{ $clubOption->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" id="package-search-clear" class="package-search-clear">Clear</button>
        </div>
        <div id="package-search-empty" class="package-search-empty">No packages matched your filters.</div>

        <div style="margin: 8px 0 14px; font-size: 12px; line-height: 1.5; color: rgba(255,255,255,0.82);">
            This experience is fulfilled by the venue. Entry is subject to venue rules including minimum age requirements (18+ or 21+ depending on venue), valid ID, and dress code.
        </div>

        @foreach($sortedPackageCategoryGroups as $categoryGroup)
            <button
                type="button"
                class="btn btn-outline-light package-category-tile mb-2 w-100"
                data-target="#{{ $categoryGroup['id'] }}"
                style="background:var(--aff-accent); border-color:var(--aff-accent); color:#000; display:flex; justify-content:space-between; align-items:center; text-align:left; padding:14px 16px; border-radius:12px; font-size:15px; font-weight:600;"
            >
                {{ $categoryGroup['club']->name }} - {{ $categoryGroup['name'] }}
                <span class="package-category-indicator" style="opacity:.7; font-size:12px;">+</span>
            </button>
            <div id="{{ $categoryGroup['id'] }}" class="package-category-group" style="display: none;">
                @foreach($categoryGroup['mappings'] as $mapping)
                    @php $package = $mapping->package; $club = $package->website; @endphp
                    <div
                        class="vip-card"
                        id="pkg-card-{{ $package->id }}"
                        data-package-name="{{ strtolower(trim((string) $package->name)) }}"
                        data-club-name="{{ strtolower(trim((string) $club->name)) }}"
                        data-location="{{ strtolower(trim((string) ($club->location ?? ''))) }}"
                        data-club-id="{{ $club->id }}"
                    >
                        <div class="vip-card-main">
                            <div class="vip-meta">
                                <span class="club-badge">Location: {{ $club->location ?: $club->name }}</span>
                            </div>
                            <div class="vip-title-row">
                                <div class="vip-title">{{ $package->name }}</div>
                                <button
                                    type="button"
                                    class="club-detail-trigger"
                                    data-bs-toggle="popover"
                                    data-bs-placement="top"
                                    data-bs-custom-class="club-popover"
                                    data-bs-html="true"
                                    data-bs-title="{{ e($club->name) }}"
                                    data-bs-content="
                                        @if($club->logo)
                                            &lt;img src='{{ asset('uploads/' . $club->logo) }}' alt='{{ e($club->name) }}' class='club-popover-logo'&gt;
                                        @endif
                                        @if($club->location)
                                            &lt;div class='club-tooltip-row'&gt;&lt;i class='fas fa-location-dot'&gt;&lt;/i&gt; {{ e($club->location) }}&lt;/div&gt;
                                        @endif
                                        @if($club->phone)
                                            &lt;div class='club-tooltip-row'&gt;&lt;i class='fas fa-phone'&gt;&lt;/i&gt; {{ e($club->phone) }}&lt;/div&gt;
                                        @endif
                                        @if($club->email)
                                            &lt;div class='club-tooltip-row'&gt;&lt;i class='fas fa-envelope'&gt;&lt;/i&gt; {{ e($club->email) }}&lt;/div&gt;
                                        @endif
                                        @if($club->text_description)
                                            &lt;div class='club-tooltip-row' style='margin-top:10px; opacity:.82;'&gt;{{ e(\Illuminate\Support\Str::limit(strip_tags($club->text_description), 220)) }}&lt;/div&gt;
                                        @endif
                                        &lt;div class='club-tooltip-section-title'&gt;Package Details&lt;/div&gt;
                                        @if($package->description)
                                            &lt;div class='club-tooltip-row'&gt;{{ e(\Illuminate\Support\Str::limit(strip_tags($package->description), 320)) }}&lt;/div&gt;
                                        @else
                                            &lt;div class='club-tooltip-row' style='opacity:.72;'&gt;No package description added yet.&lt;/div&gt;
                                        @endif
                                    "
                                >
                                    <i class="fas fa-info"></i>
                                </button>
                            </div>
                            <button
                                type="button"
                                class="vip-btn mt-2"
                                data-id="{{ $package->id }}"
                                data-name="{{ e($package->name) }}"
                                data-price="{{ $package->price }}"
                                data-transportation="{{ $package->transportation }}"
                                data-club-id="{{ $club->id }}"
                                data-club-slug="{{ $club->slug }}"
                            >Add to Cart</button>
                        </div>
                        <div class="vip-card-side">
                            <div class="vip-guest-control">
                                <div class="vip-guest-label">Guests</div>
                                <select class="form-select package_number_of_guestss" data-id="{{ $package->id }}" data-multiple="{{ $package->multiple }}">
                                    @php
                                        $maxGuests = 1;
                                        if ($package->package_type === 'ticket' && $package->daily_ticket_limit) {
                                            $maxGuests = $package->daily_ticket_limit;
                                        } elseif ($package->package_type === 'table' && $package->daily_table_limit) {
                                            $maxGuests = $package->daily_table_limit;
                                        } elseif ($package->number_of_guest) {
                                            $maxGuests = $package->number_of_guest;
                                        }
                                    @endphp
                                    @for($i = 1; $i <= $maxGuests; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="vip-price-tag">${{ number_format((float) $package->price, 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        <p style="opacity:.5;">No packages are available on this page yet.</p>
    @endif

    {{-- ===== ADD-ONS ===== --}}
    <div class="addons-wrap" id="addons-section">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0" style="font-weight:700;">Add-ons <span style="opacity:.5;font-size:12px;">(optional)</span></h6>
            <span style="font-size:12px;opacity:.5;">Toggle to include</span>
        </div>
        <div class="addons-list"></div>
    </div>

    {{-- ===== PROMO CODE ===== --}}
    <div id="promo-section">
        <label id="promo-lbl" style="font-size:12px;opacity:.6;margin-bottom:4px;display:block;">Promo Code</label>
        <div class="promo-row">
            <input type="text" id="promo_code" placeholder="Enter promo or referral code">
            <button type="button" id="applyPromoBtn">Apply</button>
        </div>
    </div>

    {{-- ===== CART ===== --}}
    <div id="cart-section">
        <div style="font-weight:700;font-size:15px;margin-bottom:10px;">Your Cart</div>
        <div id="cart-list"></div>
        <div id="cart-total" style="font-size:15px;margin-top:8px;font-weight:600;"></div>
        <div id="cart-coupon" style="font-size:13px;color:#4caf7d;margin-top:4px;"></div>
    </div>

    @if(($affiliate->show_location_section ?? true) && $featuredClub && ($featuredClub->location || $featuredClub->phone || $featuredClub->email))
        <section class="aff-location-card mt-3">
            <div class="aff-location-shell">
                <div class="aff-location-copy">
                    <span class="aff-location-kicker">Club Contact & Location</span>
                    <h3 class="aff-location-title">{{ $featuredClub->name }}</h3>
                    @if($featuredClub->location)
                        <p class="aff-location-address">{{ $featuredClub->location }}</p>
                    @endif
                    <div class="aff-location-contact">
                        @if($featuredClub->phone)
                            <a class="aff-location-chip" href="tel:{{ $featuredClub->phone }}">
                                <i class="fas fa-phone"></i>
                                <span>{{ $featuredClub->phone }}</span>
                            </a>
                        @endif
                        @if($featuredClub->email)
                            <a class="aff-location-chip" href="mailto:{{ $featuredClub->email }}">
                                <i class="fas fa-envelope"></i>
                                <span>{{ $featuredClub->email }}</span>
                            </a>
                        @endif
                    </div>
                </div>
                @if($featuredClub->location)
                    <div class="aff-location-map">
                        <iframe
                            src="https://www.google.com/maps?q={{ urlencode($featuredClub->location) }}&output=embed"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <div id="shareLinkContainer" style="margin-bottom:1rem;">
        <button type="button" id="generateShareLink" style="background:var(--aff-accent);color:#000;font-weight:700;border:none;padding:8px 20px;border-radius:25px;cursor:pointer;font-size:14px;">&#128279; Share Cart Link</button>
        <div style="position:relative;margin-top:8px;">
            <input type="text" id="shareableLink" readonly style="width:100%;display:none;padding-right:40px;">
            <div id="copyTooltip" style="display:none;position:absolute;top:-35px;right:0;background:#28a745;color:white;padding:8px 12px;border-radius:4px;font-size:12px;white-space:nowrap;z-index:1000;">URL Copied!</div>
        </div>
    </div>

    {{-- ===== PRICE SUMMARY ===== --}}
    <div class="price-summary" id="price-summary">
        <div style="font-weight:600;margin-bottom:6px;">Price Summary</div>
        <div class="default-package-price"><span class="summary-label">Package</span>: <span class="summary-value">$0.00</span></div>
        <div id="sc-row" style="display:none;" class="default-service-charge"><span id="sc-lbl" class="summary-label">Service Charge</span>: <span class="summary-value">$0.00</span></div>
        <div id="st-row" style="display:none;" class="default-sales-tax"><span id="st-lbl" class="summary-label">Sales Tax</span>: <span class="summary-value">$0.00</span></div>
        <div id="gr-row" style="display:none;" class="default-gratuity"><span id="gr-lbl" class="summary-label">Gratuity</span>: <span class="summary-value">$0.00</span></div>
        <hr style="border-color:rgba(255,255,255,0.15);margin:8px 0;">
        <div style="font-size:16px;font-weight:800;" class="default-deposit"><span class="summary-label">Total</span>: <span class="summary-value">$0.00</span></div>
        <div id="rf-row" style="display:none;font-weight:700;" class="default-refundable"><span id="rf-lbl" class="summary-label">Processing Fees</span>: <span class="summary-value">$0.00</span> (Pay Now)</div>
        <div id="due-row" style="display:none;font-weight:700;" class="default-due"><span class="summary-label">DUE ON ARRIVAL</span>: <span class="summary-value">$0.00</span></div>
    </div>

    {{-- ===== CHECKOUT STEPS ===== --}}
    <ul class="checkout-steps" id="checkout-steps" style="display:none;">
        <li class="step active" id="step-1"><div class="step-number">1</div><p class="step-title">Personal Details</p></li>
        <li class="step" id="step-2"><div class="step-number">2</div><p class="step-title">Transportation</p></li>
        <li class="step" id="step-3"><div class="step-number">3</div><p class="step-title">Payment</p></li>
    </ul>

    {{-- ===== CHECKOUT FORM ===== --}}
    <form id="payment-form" action="#" method="post">
        @csrf
        <input type="hidden" name="package_id"              id="package_id">
        <input type="hidden" name="website_id"              id="website_id">
        <input type="hidden" name="addons"                  id="addons">
        <input type="hidden" name="cart_items"              id="cart_items">
        <input type="hidden" name="total"                   id="subtotal">
        <input type="hidden" name="payment_total"           class="payment_total">
        <input type="hidden" name="commission_base_amount"  id="commission_base_amount">
        <input type="hidden" name="affiliate_slug"          value="{{ $affiliate->slug }}">
        <input type="hidden" name="package_number_of_guest" class="package_number_of_guest" value="1">
        <input type="hidden" name="package_use_date"        class="package_use_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
        <input type="hidden" name="promo_code"              class="promo_code">
        <input type="hidden" name="discounted_amount"       class="discounted_amount">

        {{-- Step 1: Personal Details --}}
        <section class="checkout-section" id="section-1">
            <h5 class="mb-3" style="font-weight:700;">Personal Details</h5>
            <div class="form-row">
                <div class="form-group"><label>First Name</label><input type="text" name="package_first_name" placeholder="First Name" required></div>
                <div class="form-group"><label>Last Name</label><input type="text" name="package_last_name" placeholder="Last Name" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Phone Number</label><input type="tel" name="package_phone" placeholder="Phone Number" required></div>
                <div class="form-group"><label>Email <small style="font-size:10px;">(confirmation sent here)</small></label><input type="email" name="package_email" placeholder="email@example.com" required></div>
            </div>
            <div class="form-group mb-3">
                <label>Date of Birth</label>
                <div class="d-flex gap-2">
                    <select id="package-dob-month" name="package_month" class="form-select" style="flex:1;padding:10px 8px !important;" required></select>
                    <select id="package-dob-day"   name="package_day"   class="form-select" style="flex:1;padding:10px 8px !important;" required></select>
                    <select id="package-dob-year"  name="package_year"  class="form-select" style="flex:1;padding:10px 8px !important;" required></select>
                </div>
            </div>
            <div class="form-group mb-3">
                <label>Reservation Date</label>
                <div class="date-input-wrapper">
                    <input id="package_use_date" type="text" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" readonly>
                    <span class="custom-calendar-icon"></span>
                </div>
            </div>
            <div class="form-group mb-3"><label>Booking Note</label><textarea name="package_note" placeholder="Your occasion or special request?"></textarea></div>
            <div class="step-navigation"><button type="button" class="btn-next" id="next-to-transport">Next: Transportation &rarr;</button></div>
        </section>

        {{-- Step 2: Transportation --}}
        <section class="checkout-section" id="section-2">
            {{-- No transport needed --}}
            <div id="transport-confirmation" style="display:none;">
                <h5 class="mb-3" style="font-weight:700;">Transportation</h5>
                <label class="consent-label">
                    <input type="checkbox" id="transportation_part">
                    <span id="transport-confirm-text">I confirm I am not arriving via a paid transportation service.</span>
                </label>
                <div class="step-navigation">
                    <button type="button" class="btn-prev" id="prev-to-pkg">← Personal Details</button>
                    <button type="button" class="btn-next" id="next-to-pay-confirm">Next: Payment →</button>
                </div>
            </div>
            {{-- Transport form --}}
            <div id="transport-form" style="display:none;">
                <h5 class="mb-3" style="font-weight:700;">Transportation Details</h5>
                <button type="button" class="same-as-info-transport">Same as personal details</button>
                <div class="form-row">
                    <div class="form-group">
                        <label>Pick-up Time</label>
                        <input name="transportation_pickup_time" type="text" id="Pick-up-time" class="flatpickr-time" placeholder="Select Time" value="{{ \Carbon\Carbon::now()->format('h:i A') }}">
                    </div>
                    <div class="form-group"><label>Contact Phone (for driver)</label><input type="tel" name="transportation_phone" placeholder=""></div>
                </div>
                <div class="form-group mb-2"><label>Pick-up Address</label><input type="text" name="transportation_address" placeholder=""></div>
                <div class="form-row">
                    <div class="form-group"><label>Number of Guests</label><input type="number" name="transportation_guest" value="0" min="1" required style="width:120px;max-width:120px;"></div>
                </div>
                <div class="form-group mb-2"><label>Pickup Note</label><textarea name="transportation_note" placeholder="Any special instructions?"></textarea></div>
                <div class="step-navigation">
                    <button type="button" class="btn-prev" id="prev-to-pkg-from-form">← Personal Details</button>
                    <button type="button" class="btn-next" id="next-to-pay">Next: Payment →</button>
                </div>
            </div>
        </section>

        {{-- Step 3: Payment --}}
        <section class="checkout-section" id="section-3">
            <h5 class="mb-3" style="font-weight:700;">Payment Information</h5>
            <button type="button" class="same-as-info">Same as personal details</button>
            <div class="form-row">
                <div class="form-group"><label>First Name</label><input name="payment_first_name" type="text" required></div>
                <div class="form-group"><label>Last Name</label><input name="payment_last_name" type="text" required></div>
            </div>
            <input type="hidden" name="payment_phone"  id="hidden_payment_phone">
            <input type="hidden" name="payment_email"  id="hidden_payment_email">
            <input type="hidden" name="payment_month"  id="hidden_payment_month">
            <input type="hidden" name="payment_day"    id="hidden_payment_day">
            <input type="hidden" name="payment_year"   id="hidden_payment_year">
            <div class="form-group mb-2"><label>Billing Address</label><input name="payment_address" type="text" required></div>
            <div class="form-row">
                <div class="form-group"><label>Country</label><select id="country" name="payment_country" class="form-select" required></select></div>
                <div class="form-group"><label>State / Province</label><select name="payment_state" id="st-pv" class="form-select" required><option value="null" disabled selected>Select State/Province</option></select></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>City</label><input type="text" name="payment_city" required></div>
                <div class="form-group"><label>Zip / Postal Code</label><input type="text" name="payment_zip_code" required></div>
            </div>

            <div id="payment-logos" class="mb-3">
                <img src="https://img.icons8.com/color/48/visa.png" alt="Visa" style="height:30px;margin-right:4px;">
                <img src="https://img.icons8.com/color/48/mastercard-logo.png" alt="MC" style="height:30px;margin-right:4px;">
                <img src="https://img.icons8.com/color/48/amex.png" alt="Amex" style="height:30px;margin-right:4px;">
            </div>

            {{-- Authorize.net raw card fields --}}
            <div id="authorize-section" style="display:none;">
                <div class="form-group mb-2"><label>Card Number</label><input type="tel" name="card_number" placeholder="" inputmode="numeric" autocomplete="cc-number"></div>
                <div class="form-row">
                    <div class="form-group"><label>Month (MM)</label><input type="tel" maxlength="2" name="card_month" placeholder="MM" required></div>
                    <div class="form-group"><label>Year (YY)</label><input type="tel" maxlength="2" name="card_year" placeholder="YY" required></div>
                    <div class="form-group"><label>CVV</label><input type="tel" name="card_cvv" placeholder="CVV" required></div>
                </div>
            </div>

            {{-- Stripe element fields --}}
            <div id="stripe-section" style="display:none;">
                <div class="form-group mb-1"><label>Card Number</label><div id="card_number" class="StripeElement"></div></div>
                <div class="form-row">
                    <div class="form-group"><label>Expiry Date</label><div id="expiration_date" class="StripeElement"></div></div>
                    <div class="form-group"><label>CVV</label><div id="cvv" class="StripeElement"></div></div>
                </div>
            </div>

            {{-- Business expense --}}
            <label class="consent-label mt-2" style="margin-bottom:6px;">
                <input type="checkbox" id="businessExpenseCheckbox">
                <span>This purchase is for business purposes</span>
            </label>
            <div id="businessFields" style="display:none;margin-bottom:10px;">
                <div class="form-row">
                    <div class="form-group"><label>Company Name</label><input type="text" name="business_company" placeholder="Company Name"></div>
                    <div class="form-group"><label>VAT / Tax ID</label><input type="text" name="business_vat" placeholder="VAT or Tax ID"></div>
                </div>
                <div class="form-group mb-2"><label>Business Address</label><input type="text" name="business_address" placeholder="Business Address"></div>
                <div class="form-group mb-2"><label>Purpose of Purchase</label><input type="text" name="business_purpose" placeholder="e.g. team event, client entertainment"></div>
            </div>

            <label class="consent-label">
                <input type="checkbox" id="smsConsent" required>
                <span id="sms-consent-text">I agree to receive SMS communications regarding my upcoming reservation. Message and data rates may apply. Reply STOP to opt out at any time.</span>
            </label>
            <label class="consent-label" id="driverNotificationConsentWrap" style="display:none;">
                <input type="checkbox" id="driverNotificationConsent">
                <span>I agree to receive notifications from the driver regarding my transportation pickup.</span>
            </label>
            <label class="consent-label">
                <input type="checkbox" id="termsConsent" required>
                <span>I understand that all sales are final. I agree to the <a id="terms-link" href="#" target="_blank">Terms of Service</a>, and acknowledge that CartVIP is the merchant of record for this purchase.</span>
            </label>

            <p style="margin: 12px 0 0; font-size: 12px; line-height: 1.5; color: rgba(255,255,255,0.82);">
                All bookings are processed by CartVIP. By completing this purchase, you agree to our no-refund policy and venue entry requirements.
            </p>
            <p style="margin: 8px 0 0; font-size: 12px; line-height: 1.5; color: rgba(255,255,255,0.72);">
                By completing this purchase, you confirm you are authorized to use this payment method and agree not to initiate a chargeback without contacting CartVIP first.
            </p>

            <div class="step-navigation">
                <button type="button" class="btn-prev" id="prev-to-transport">← Transportation</button>
                <button class="submit-btn" id="submitBtn" type="submit">Complete Purchase</button>
            </div>
        </section>
    </form>

</div>
</main>

<style>
    #checkout-processing-overlay {
        position: fixed;
        inset: 0;
        background: rgba(8, 12, 22, 0.78);
        backdrop-filter: blur(5px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    #checkout-processing-overlay.is-visible {
        display: flex;
    }

    .checkout-processing-card {
        width: min(92vw, 420px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: linear-gradient(150deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02));
        box-shadow: 0 22px 55px rgba(0, 0, 0, 0.35);
        padding: 22px 20px;
        text-align: center;
    }

    .checkout-processing-spinner {
        width: 56px;
        height: 56px;
        margin: 0 auto 12px;
        border-radius: 50%;
        border: 3px solid rgba(255,255,255,0.18);
        border-top-color: var(--aff-accent);
        animation: checkoutSpin .9s linear infinite;
    }

    .checkout-processing-title {
        margin: 0;
        color: #f8fbff;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: .01em;
    }

    .checkout-processing-copy {
        margin: 7px 0 0;
        color: rgba(226, 234, 248, 0.88);
        font-size: 13px;
        line-height: 1.45;
    }

    @keyframes checkoutSpin {
        to { transform: rotate(360deg); }
    }
</style>
<div id="checkout-processing-overlay" aria-hidden="true" role="status" aria-live="polite">
    <div class="checkout-processing-card">
        <div class="checkout-processing-spinner" aria-hidden="true"></div>
        <p class="checkout-processing-title">Processing Your Purchase</p>
        <p class="checkout-processing-copy">Please wait while we securely complete your transaction.</p>
    </div>
</div>

<footer class="aff-footer">
    <div class="container aff-footer-inner">
        <a href="https://cartvip.com" target="_blank" rel="noopener" class="aff-footer-brand">
            <span class="brand-dot" aria-hidden="true"></span>
            <span>Powered by CartVIP.com</span>
        </a>
        <div class="aff-footer-note">All bookings and payments are processed securely by CartVIP, the merchant of record for all transactions.</div>
        <a id="footer-terms-link" href="#" target="_blank" rel="noopener" class="aff-footer-brand" style="font-size:12px; opacity:.9;">
            <span>Terms of Service</span>
        </a>
    </div>
</footer>

{{-- Modal for package / addon description --}}
<div class="modal fade" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="background:#1a1d2e;color:#ddd;">
        <div class="modal-header"><h5 class="modal-title" style="color:#fff;"></h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
    </div></div>
</div>

<div class="modal fade" id="addonSelectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:#1a1d2e;color:#ddd;">
            <div class="modal-header">
                <h5 class="modal-title" id="addonSelectionModalTitle" style="color:#fff;">Select Add-ons</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="addonSelectionModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn" id="addonModalConfirmBtn" style="background:var(--aff-accent);color:#000;font-weight:700;">Confirm & Add to Cart</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://js.stripe.com/v3/"></script>

<script>
// ============================================================
//  CART SYSTEM
// ============================================================
window.cart = [];
window.cartCoupon = null;
window.activeClub = null;
window.activeClubSlug = null;
let stripeObj = null, cardNum_el = null, cardExp_el = null, cardCvc_el = null, loadedStripeKey = '';

function ensureCart() { if (!Array.isArray(window.cart)) window.cart = []; }

function formatCurrency(value) {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(Number(value || 0));
}

function cartRequiresTransport() {
    ensureCart();
    return window.cart.some(function(item) {
        return item.transport == 1 || item.transport === '1' || item.transport === true;
    });
}

function syncTransportStateFromCart() {
    window.requiresTransport = cartRequiresTransport();
    const transportationPhoneField = $('[name="transportation_phone"]');
    const transportationAddressField = $('[name="transportation_address"]');
    const transportationPickupTimeField = $('[name="transportation_pickup_time"]');
    const pickupDateField = $('[name="package_use_date"]');
    const driverNotificationConsentWrap = $('#driverNotificationConsentWrap');
    const driverNotificationConsent = $('#driverNotificationConsent');
    if (window.requiresTransport) {
        transportationPhoneField.prop('required', true).attr('aria-required', 'true');
        transportationAddressField.prop('required', true).attr('aria-required', 'true');
        transportationPickupTimeField.prop('required', true).attr('aria-required', 'true');
        pickupDateField.prop('required', true).attr('aria-required', 'true');
        driverNotificationConsentWrap.css('display', 'flex');
        driverNotificationConsent.prop('required', true).attr('aria-required', 'true');
    } else {
        transportationPhoneField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
        transportationAddressField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
        transportationPickupTimeField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
        pickupDateField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
        driverNotificationConsentWrap.hide();
        driverNotificationConsent.prop('checked', false).prop('required', false).removeAttr('aria-required');
    }
}

function parseMultipleFlag(value) {
    return value === true || value === 1 || value === '1' || value === 'true';
}

function getPackageMultipleFromDom(pkgId) {
    const multipleValue = $('.package_number_of_guestss[data-id="' + pkgId + '"]').first().data('multiple');
    return parseMultipleFlag(multipleValue);
}

function getBillableGuests(item) {
    return parseMultipleFlag(item.isMultiple) ? (parseInt(item.guests) || 1) : 1;
}

window.addToCart = function(pkgId, pkgName, pkgPrice, guests, addons, transport, isMultiple) {
    ensureCart();
    const normalizedGuests = parseInt(guests) || 1;
    
    // Check daily limits for this package
    const activeClub = window.activeClub;
    if (!activeClub || !activeClub.slug) {
        alert('Unable to verify package availability. Please refresh and try again.');
        return false;
    }
    
    $.get('/' + activeClub.slug + '/package/' + pkgId + '/capacity', function(response) {
        if (!response.available) {
            alert('This package is no longer available: ' + response.message);
            return false;
        }

        const existing = window.cart.find(function(item) { return item.pkgId == pkgId; });

        if (existing) {
            existing.pkgName = pkgName;
            existing.pkgPrice = pkgPrice;
            existing.guests = normalizedGuests;
            existing.addons = addons || [];
            existing.transport = transport;
            existing.isMultiple = parseMultipleFlag(isMultiple);
        } else {
            window.cart.push({
                pkgId: pkgId,
                pkgName: pkgName,
                pkgPrice: pkgPrice,
                guests: normalizedGuests,
                addons: addons || [],
                transport: transport,
                isMultiple: parseMultipleFlag(isMultiple)
            });
        }

        syncTransportStateFromCart();
        renderCart(); calcTotal();
        return true;
    }).fail(function() {
        alert('Error checking package availability. Please try again.');
        return false;
    });
};

window.removeFromCart = function(pkgId) {
    ensureCart();
    window.cart = window.cart.filter(p => p.pkgId != pkgId);
    syncTransportStateFromCart();
    renderCart(); calcTotal();
};

function renderCart() {
    ensureCart();
    if (!window.cart.length) { $('#cart-section').hide(); return; }
    $('#cart-section').show();
    $('#shareLinkContainer').show();
    let html = '';
    window.cart.forEach(p => {
        const billableGuests = getBillableGuests(p);
        const unitPrice = parseFloat(p.pkgPrice) || 0;
        const lineTotal = unitPrice * billableGuests;
        const priceLine = parseMultipleFlag(p.isMultiple)
            ? ('$' + formatCurrency(unitPrice) + ' &times; ' + (parseInt(p.guests, 10) || 1) + ' = $' + formatCurrency(lineTotal))
            : ('$' + formatCurrency(lineTotal));
        html += `<div style="border-bottom:1px solid rgba(255,255,255,0.08);padding:8px 0;">`
            + `<strong>${p.pkgName}</strong> &mdash; <span style="color:var(--aff-accent)">${priceLine}</span>`
            + `<button onclick="window.removeFromCart('${p.pkgId}')" style="float:right;background:#c00;color:#fff;border:none;border-radius:5px;padding:3px 9px;cursor:pointer;font-size:12px;">Remove</button>`
            + (p.addons.length ? `<div style="margin-left:18px;font-size:12px;opacity:.6;">Add-ons: ${p.addons.map(a => a.name + ' ($' + formatCurrency(a.price) + ')').join(', ')}</div>` : '')
            + '</div>';
    });
    $('#cart-list').html(html);
    syncCheckoutCartFields();
}

function syncCheckoutCartFields() {
    const firstCartItem = window.cart[0] || null;
    const totalGuests = window.cart.reduce((sum, item) => sum + (parseInt(item.guests, 10) || 1), 0);
    const addonSummary = window.cart.flatMap(item => Array.isArray(item.addons) ? item.addons : []).map(addon => addon.name + ' ($' + addon.price + ')').join(', ');

    $('#cart_items').val(window.cart.length ? JSON.stringify(window.cart) : '');
    $('#addons').val(addonSummary);
    $('.package_number_of_guest').val(totalGuests || 1);

    if (firstCartItem) {
        $('#package_id').val(firstCartItem.pkgId || firstCartItem.packageId || '');
    }
}

function calcTotal() {
    ensureCart();
    if (!window.activeClub) return;
    const c = window.activeClub;
    let sub = 0;
    window.cart.forEach(p => { sub += (p.pkgPrice * getBillableGuests(p)) + p.addons.reduce((s, a) => s + parseFloat(a.price), 0); });

    let scAmt = (c.serviceChargeName !== '0' && c.serviceChargeName !== 0) ? sub * c.serviceChargeFee / 100 : 0;
    let grAmt = (c.gratuityName !== '0' && c.gratuityName !== 0) ? sub * c.gratuityFee / 100 : 0;
    let stAmt = (c.salesTaxName !== '0' && c.salesTaxName !== 0) ? (sub + scAmt + grAmt) * c.salesTaxFee / 100 : 0;
    let totalBeforeCoupon = sub + scAmt + stAmt + grAmt;

    let promoDisc = 0;
    if (window.cartCoupon) {
        promoDisc = window.cartCoupon.type === 'percentage' ? totalBeforeCoupon * window.cartCoupon.discount / 100 : window.cartCoupon.discount;
    }
    let amountAfterCoupon = totalBeforeCoupon - promoDisc;
    let processingFeeRate = parseFloat(c.processingFee || 0) || 0;
    let processingFeeType = String(c.processingFeeType || 'percentage').toLowerCase();
    let processingFeeAmt = processingFeeType === 'flat'
        ? processingFeeRate
        : amountAfterCoupon * processingFeeRate / 100;
    let grand = amountAfterCoupon + processingFeeAmt;
    let rfAmt = grand * c.refundableFee / 100;

    $('.default-package-price .summary-value').text('$' + formatCurrency(sub));
    scAmt > 0 ? ($('#sc-row').show(), $('#sc-row .summary-value').text('$' + formatCurrency(scAmt))) : $('#sc-row').hide();
    stAmt > 0 ? ($('#st-row').show(), $('#st-row .summary-value').text('$' + formatCurrency(stAmt))) : $('#st-row').hide();
    grAmt > 0 ? ($('#gr-row').show(), $('#gr-row .summary-value').text('$' + formatCurrency(grAmt))) : $('#gr-row').hide();

    if ($('#gr-row').length) {
        if ($('#sc-row').length) {
            $('#gr-row').insertBefore('#sc-row');
        } else if ($('#st-row').length) {
            $('#gr-row').insertBefore('#st-row');
        }
    }

    if (promoDisc > 0) {
        if (!$('.promo-disc-row').length) $('.default-gratuity').after('<div class="promo-disc-row" style="font-size:13px;">Promo Discount: <span>$0.00</span></div>');
        $('.promo-disc-row span').text('-$' + formatCurrency(promoDisc));
    } else { $('.promo-disc-row').remove(); }

    if (processingFeeAmt > 0) {
        if (!$('.processing-fee-row').length) $('.default-gratuity').after('<div class="processing-fee-row" style="font-size:13px;">Processing Fee: <span>$0.00</span></div>');
        $('.processing-fee-row span').text('$' + formatCurrency(processingFeeAmt));
    } else { $('.processing-fee-row').remove(); }

    c.refundableFee > 0 ? ($('#rf-row').show(), $('#due-row').show(), $('#rf-row .summary-value').text('$' + formatCurrency(rfAmt)), $('#due-row .summary-value').text('$' + formatCurrency(grand - rfAmt))) : ($('#rf-row').hide(), $('#due-row').hide());
    $('.default-deposit .summary-value').text('$' + formatCurrency(grand));
    $('.payment_total').val(grand.toFixed(2));
    $('#commission_base_amount').val(Math.max(sub - promoDisc, 0).toFixed(2));
    $('#subtotal').val(c.refundableFee > 0 ? rfAmt.toFixed(2) : grand.toFixed(2));
    $('#cart-total').text('Packages Subtotal: $' + formatCurrency(sub));
    window.cartCoupon && promoDisc > 0 ? $('#cart-coupon').text('Coupon: ' + window.cartCoupon.code + ' (-$' + formatCurrency(promoDisc) + ')') : $('#cart-coupon').text('');
}

function getCurrentSelections() {
    return {
        club: window.activeClubSlug || '',
        cart: window.cart,
        coupon: window.cartCoupon ? window.cartCoupon.code : ''
    };
}

function getUrlWithSelections() {
    const data = getCurrentSelections();
    const params = new URLSearchParams();
    if (data.club) params.set('club', data.club);
    if (data.cart && data.cart.length) params.set('cart', encodeURIComponent(JSON.stringify(data.cart)));
    if (data.coupon) params.set('coupon', data.coupon);
    return window.location.origin + window.location.pathname + '?' + params.toString();
}

function setSelectionsFromParams() {
    const params = new URLSearchParams(window.location.search);
    const club = params.get('club');
    const cartParam = params.get('cart');
    const couponParam = params.get('coupon');

    if (club && clubConfigs[club]) {
        activateClub(club);
    }

    if (cartParam) {
        try {
            const decoded = JSON.parse(decodeURIComponent(cartParam));
            if (Array.isArray(decoded) && decoded.length) {
                window.cart = decoded.map(function(item) {
                    if (typeof item.isMultiple === 'undefined') {
                        item.isMultiple = getPackageMultipleFromDom(item.pkgId);
                    }
                    return item;
                });
                const firstPackage = window.cart[0];
                if (firstPackage) {
                    $('#package_id').val(firstPackage.pkgId || '');
                    $('.package_number_of_guest').val(firstPackage.guests || 1);
                }

                $('.vip-card').removeClass('selected');
                window.cart.forEach(function(item) {
                    $('.package_number_of_guestss[data-id="' + item.pkgId + '"]').val(item.guests || 1);
                    $('#pkg-card-' + item.pkgId).addClass('selected');
                });

                syncTransportStateFromCart();
                renderCart();
                calcTotal();
                $('.price-summary').show();
                $('#promo-section').show();
                $('#checkout-steps').show();
                showStep(1);
            }
        } catch (e) {
            console.error('Failed to parse shared cart', e);
        }
    }

    if (couponParam) {
        $('#promo_code').val(couponParam);
        $('#applyPromoBtn').trigger('click');
    }
}
</script>
<div class="modal fade checkout-gallery-modal" id="checkoutGalleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gallery Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="" alt="" id="checkoutGalleryModalImage" class="checkout-gallery-modal-image">
            </div>
        </div>
    </div>
</div>

<script>
// ============================================================
//  ACTIVATE CLUB (switches form action, payment, labels)
// ============================================================
function activateClub(slug) {
    const c = clubConfigs[slug];
    if (!c) return;
    window.activeClub = c;
    window.activeClubSlug = slug;
    $('#payment-form').attr('action', '/' + slug + '/checkout/store');
    $('#website_id').val(c.id);
    $('#sc-lbl').text(c.serviceChargeName !== '0' ? c.serviceChargeName : 'Service Charge');
    $('#st-lbl').text(c.salesTaxName !== '0' ? c.salesTaxName : 'Sales Tax');
    $('#gr-lbl').text(c.gratuityName !== '0' ? c.gratuityName : 'Gratuity');
    $('#rf-lbl').text(c.refundableName || 'Processing Fees');
    $('#promo-lbl').text(c.promoCodeName || 'Promo Code');
    $('#terms-link').attr('href', c.terms);
    $('#footer-terms-link').attr('href', c.terms);
    $('#privacy-link').attr('href', c.privacy);
    $('#sms-consent-text').text('I agree to receive SMS communications from ' + c.name + ' regarding my upcoming reservation. Message and data rates may apply. Reply STOP to opt out.');
    $('#transport-confirm-text').text(c.transportConfirmText);

    if (c.paymentMethod === 'stripe') {
        $('#stripe-section').show(); $('#authorize-section').hide();
        $('#authorize-section').find('input').prop('required', false).prop('disabled', true);
        if (c.stripeKey && c.stripeKey !== loadedStripeKey) initStripe(c.stripeKey);
    } else {
        $('#authorize-section').show(); $('#stripe-section').hide();
        $('#authorize-section').find('input').prop('disabled', false);
        $('#authorize-section').find('input[name="card_number"]').prop('required', true);
        $('#authorize-section').find('input[name="card_month"]').prop('required', true);
        $('#authorize-section').find('input[name="card_year"]').prop('required', true);
        $('#authorize-section').find('input[name="card_cvv"]').prop('required', true);
    }

}

function initStripe(key) {
    loadedStripeKey = key;
    stripeObj = Stripe(key);
    const els = stripeObj.elements();
    const style = { base: { color: '#fff', fontSize: '15px', '::placeholder': { color: '#aab7cc' } }, invalid: { color: '#f87' } };
    if (cardNum_el) { try { cardNum_el.unmount(); cardExp_el.unmount(); cardCvc_el.unmount(); } catch(e){} }
    $('#card_number').empty(); $('#expiration_date').empty(); $('#cvv').empty();
    cardNum_el = els.create('cardNumber', { style }); cardNum_el.mount('#card_number');
    cardExp_el = els.create('cardExpiry', { style }); cardExp_el.mount('#expiration_date');
    cardCvc_el = els.create('cardCvc', { style });    cardCvc_el.mount('#cvv');
}
</script>

<script>
// ============================================================
//  VIP-BTN CLICK → LOAD ADDONS, SHOW CHECKOUT
// ============================================================
window.pendingPackageSelection = null;

function escapeAddonHtml(value) {
    return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function openAddonSelectionModal(selection) {
    const addons = selection.addons || [];
    let html = '';

    if (!addons.length) {
        html = '<p style="margin:0;opacity:.8;">No add-ons available for this package. Click confirm to continue.</p>';
    } else {
        addons.forEach(function(a) {
            const description = String(a.description || '').trim();
            const descriptionHtml = description ? ('<small class="addon-modal-desc">' + escapeAddonHtml(description) + '</small>') : '';
            html += '<label class="addon-modal-row">'
                + '<span class="addon-modal-label">' + escapeAddonHtml(a.name) + ' <span style="opacity:.6;">($' + formatCurrency(a.price || 0) + ')</span>' + descriptionHtml + '</span>'
                + '<span class="addon-switch">'
                + '<input type="checkbox" class="addon-modal-switch-input" data-id="' + a.id + '" data-name="' + escapeAddonHtml(a.name) + '" data-price="' + parseFloat(a.price || 0) + '">'
                + '<span class="addon-switch-slider"></span>'
                + '</span>'
                + '</label>';
        });
    }

    $('#addonSelectionModalTitle').text('Select Add-ons for ' + selection.pkgName);
    $('#addonSelectionModalBody').html(html);
    bootstrap.Modal.getOrCreateInstance(document.getElementById('addonSelectionModal')).show();
}

$(document).ready(function() {
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (element) {
        new bootstrap.Popover(element, {
            trigger: 'focus hover',
            html: true,
            sanitize: false,
        });
    });

    function setCategoryTileDefaultState($tile) {
        $tile.removeClass('active').css({ background: 'var(--aff-accent)', color: '#000' });
        $tile.find('.package-category-indicator').text('+');
    }

    function applyPackageFilters() {
        const searchQuery = String($('#package-search-text').val() || '').trim().toLowerCase();
        const selectedClubId = String($('#package-location-filter').val() || '').trim();

        let visibleCategories = 0;

        $('.package-category-group').each(function() {
            const $group = $(this);
            const groupId = $group.attr('id');
            const $tile = $('.package-category-tile[data-target="#' + groupId + '"]');
            let visibleCardsInGroup = 0;

            $group.find('.vip-card').each(function() {
                const $card = $(this);
                const packageName = String($card.data('package-name') || '').toLowerCase();
                const clubName = String($card.data('club-name') || '').toLowerCase();
                const location = String($card.data('location') || '').toLowerCase();
                const clubId = String($card.data('club-id') || '');

                const matchesSearch = !searchQuery
                    || packageName.includes(searchQuery)
                    || clubName.includes(searchQuery)
                    || location.includes(searchQuery);
                const matchesClub = !selectedClubId || clubId === selectedClubId;
                const isVisible = matchesSearch && matchesClub;

                $card.toggle(isVisible);
                if (isVisible) {
                    visibleCardsInGroup += 1;
                }
            });

            const showCategory = visibleCardsInGroup > 0;
            $tile.toggle(showCategory);

            if (!showCategory) {
                setCategoryTileDefaultState($tile);
                $group.stop(true, true).hide();
            } else {
                visibleCategories += 1;
            }
        });

        $('#package-search-empty').toggle(visibleCategories === 0);
    }

    $('#package-search-text').on('input', applyPackageFilters);
    $('#package-location-filter').on('change', applyPackageFilters);
    $('#package-search-clear').on('click', function() {
        $('#package-search-text').val('');
        $('#package-location-filter').val('');
        applyPackageFilters();
    });

    applyPackageFilters();

    $(document).on('click', '.package-category-tile', function() {
        const targetSelector = String($(this).data('target') || '');
        const targetId = targetSelector.replace(/^#/, '');
        const $target = targetId ? $('#' + targetId) : $();
        const isOpen = $(this).hasClass('active');

        $('.package-category-tile').each(function() {
            setCategoryTileDefaultState($(this));
        });
        $('.package-category-group').stop(true, true).slideUp(180);

        if (!isOpen && $target.length) {
            $(this)
                .addClass('active')
                .css({ background: '#101725', color: 'var(--aff-accent)' })
                .find('.package-category-indicator').text('−');
            $target.stop(true, true).slideDown(180);
        }
    });

    $(document).on('click', '.vip-btn', function() {
        if (this.id === 'generateShareLink') {
            return;
        }

        const pkgId     = $(this).data('id');
        const pkgName   = $(this).data('name');
        const pkgPrice  = parseFloat($(this).data('price'));
        const $guestSel = $('.package_number_of_guestss[data-id="' + pkgId + '"]');
        const guests    = parseInt($guestSel.val()) || 1;
        const isMultiple = parseMultipleFlag($guestSel.data('multiple'));
        const transport = $(this).data('transportation');
        const clubSlug  = $(this).data('club-slug');

        // Highlight selected card
        $('.vip-card').removeClass('selected');
        $('#pkg-card-' + pkgId).addClass('selected');

        // Activate club config
        activateClub(clubSlug);
        $('#package_id').val(pkgId);
        $('.package_number_of_guest').val(guests);
        window.requiresTransport = (transport == 1 || transport === '1' || transport === true);

        $.ajax({
            url: '/' + clubSlug + '/addons/' + pkgId,
            type: 'GET', dataType: 'json',
            success: function(res) {
                window.pendingPackageSelection = {
                    pkgId: pkgId,
                    pkgName: pkgName,
                    pkgPrice: pkgPrice,
                    guests: guests,
                    isMultiple: isMultiple,
                    transport: transport,
                    addons: Array.isArray(res) ? res : []
                };

                openAddonSelectionModal(window.pendingPackageSelection);
            }
        });
    });

    $('#addonModalConfirmBtn').on('click', function() {
        if (!window.pendingPackageSelection) {
            return;
        }

        const selection = window.pendingPackageSelection;
        const addons = [];

        $('#addonSelectionModalBody .addon-modal-switch-input:checked').each(function() {
            addons.push({
                id: $(this).data('id'),
                name: $(this).data('name'),
                price: parseFloat($(this).data('price'))
            });
        });

        window.addToCart(selection.pkgId, selection.pkgName, selection.pkgPrice, selection.guests, addons, selection.transport, selection.isMultiple);
        $('#package_id').val(selection.pkgId);
        $('.package_number_of_guest').val(selection.guests);

        $('.price-summary').show();
        $('#promo-section').show();
        $('#checkout-steps').show();
        syncTransportStateFromCart();
        showStep(1);

        bootstrap.Modal.getOrCreateInstance(document.getElementById('addonSelectionModal')).hide();
        window.pendingPackageSelection = null;
    });

    $('#generateShareLink').on('click', function() {
        if (!window.cart.length) {
            alert('Please add at least one package to cart');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('Generating…');

        $.ajax({
            url: '/cart/share',
            type: 'POST',
            data: {
                cart: JSON.stringify(window.cart),
                affiliate_slug: '{{ $affiliate->slug }}',
                club_slug: window.activeClubSlug || '',
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success) {
                    $('#shareableLink').val(res.short_url).show();
                } else {
                    alert('Error: ' + res.message);
                }
            },
            error: function() {
                alert('Error generating share link. Please try again.');
            },
            complete: function() {
                $btn.prop('disabled', false).html('&#128279; Share Cart Link');
            }
        });
    });

    $('#shareableLink').on('click', function() {
        const url = $(this).val();
        navigator.clipboard.writeText(url).then(function() {
            const tooltip = $('#copyTooltip');
            tooltip.show();
            setTimeout(function() { tooltip.hide(); }, 2000);
        }).catch(function() {
            $('#shareableLink').select();
        });
    });

    function buildAffiliateSharePayload(data) {
        const payload = data || {};
        return {
            url: payload.url || window.location.href.split('#')[0],
            title: payload.title || document.title,
            text: payload.text || ''
        };
    }

    const affShareButton = document.getElementById('aff-share-page-btn');
    const affShareMenu = document.getElementById('aff-share-menu');
    let affCurrentSharePayload = null;

    function closeAffiliateShareMenu() {
        if (!affShareMenu) {
            return;
        }

        affShareMenu.classList.remove('is-open');
        affShareMenu.setAttribute('aria-hidden', 'true');
    }

    function openAffiliateShareMenu(triggerButton) {
        if (!affShareMenu || !triggerButton) {
            return;
        }

        const rect = triggerButton.getBoundingClientRect();
        const top = rect.bottom + window.scrollY + 8;
        const left = Math.max(12, Math.min(rect.left + window.scrollX, window.scrollX + window.innerWidth - 214));

        affShareMenu.style.top = top + 'px';
        affShareMenu.style.left = left + 'px';
        affShareMenu.classList.add('is-open');
        affShareMenu.setAttribute('aria-hidden', 'false');
    }

    function openAffiliateFallbackShare(option, payload) {
        const cleanPayload = buildAffiliateSharePayload(payload);
        const encodedUrl = encodeURIComponent(cleanPayload.url);
        const shareLine = cleanPayload.text ? cleanPayload.text + ' ' + cleanPayload.url : cleanPayload.url;

        if (option === 'whatsapp') {
            window.open('https://wa.me/?text=' + encodeURIComponent(shareLine), '_blank', 'noopener');
            return;
        }

        if (option === 'facebook') {
            window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodedUrl, '_blank', 'noopener');
            return;
        }

        if (option === 'x') {
            window.open('https://twitter.com/intent/tweet?url=' + encodedUrl + '&text=' + encodeURIComponent(cleanPayload.text || cleanPayload.title || ''), '_blank', 'noopener');
            return;
        }

        if (option === 'linkedin') {
            window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + encodedUrl, '_blank', 'noopener');
            return;
        }

        if (option === 'instagram') {
            if (navigator.share) {
                navigator.share({
                    title: cleanPayload.title,
                    text: cleanPayload.text,
                    url: cleanPayload.url
                }).catch(function () {});
                return;
            }

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(shareLine).catch(function () {});
            }

            window.open('https://www.instagram.com/', '_blank', 'noopener');
            return;
        }

        if (option === 'copy' && navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(cleanPayload.url).catch(function () {
                window.prompt('Copy this link', cleanPayload.url);
            });
            return;
        }

        window.prompt('Copy this link', cleanPayload.url);
    }

    function shareAffiliatePage(button) {
        const payload = buildAffiliateSharePayload({
            url: button.getAttribute('data-share-url') || window.location.href.split('#')[0],
            title: button.getAttribute('data-share-title') || document.title,
            text: button.getAttribute('data-share-text') || ''
        });

        affCurrentSharePayload = payload;

        openAffiliateShareMenu(button);
    }

    if (affShareButton) {
        affShareButton.addEventListener('click', function () {
            shareAffiliatePage(affShareButton);
        });
    }

    document.addEventListener('click', function (event) {
        if (affShareMenu && affShareMenu.classList.contains('is-open') && !event.target.closest('#aff-share-menu') && !event.target.closest('#aff-share-page-btn')) {
            closeAffiliateShareMenu();
        }

        const affShareOptionButton = event.target.closest('#aff-share-menu [data-share-option]');
        if (!affShareOptionButton) {
            return;
        }

        event.preventDefault();
        openAffiliateFallbackShare(affShareOptionButton.getAttribute('data-share-option'), affCurrentSharePayload || {
            url: window.location.href.split('#')[0],
            title: document.title,
            text: ''
        });
        closeAffiliateShareMenu();
    });

    // Guest select change → update cart
    $(document).on('change', '.package_number_of_guestss', function() {
        $('.package_number_of_guest').val($(this).val());
        const pkgId = $(this).data('id');
        let p = window.cart.find(x => x.pkgId == pkgId);
        if (p) {
            p.guests = parseInt($(this).val());
            p.isMultiple = parseMultipleFlag($(this).data('multiple'));
            syncTransportStateFromCart();
            renderCart();
            calcTotal();
        }
    });

    // Reservation date change → sync hidden field
    $('#package_use_date').on('change', function() {
        $('.package_use_date').val($(this).val());
    });

    // Business expense toggle
    $('#businessExpenseCheckbox').on('change', function() {
        $(this).is(':checked') ? $('#businessFields').slideDown() : $('#businessFields').slideUp();
    });

    setSelectionsFromParams();
});
</script>

<script>
// ============================================================
//  STEP MANAGEMENT
// ============================================================
let currentStep = 1;
window.requiresTransport = false;

function showStep(n) {
    $('.checkout-section').removeClass('active').hide();
    $('#section-' + n).addClass('active').show();
    $('.step').removeClass('active completed');
    for (let i = 1; i < n; i++) $('#step-' + i).addClass('completed');
    $('#step-' + n).addClass('active');
    currentStep = n;
    syncTransportStateFromCart();
    if (n === 2) {
        window.requiresTransport ? ($('#transport-form').show(), $('#transport-confirmation').hide())
                                 : ($('#transport-confirmation').show(), $('#transport-form').hide());
    }
    window.scrollTo({ top: ($('#section-' + n).offset().top - 90), behavior: 'smooth' });
}

function validateStep(n) {
    if (n === 1) {
        const req = ['[name="package_first_name"]','[name="package_last_name"]','[name="package_phone"]','[name="package_email"]','[name="package_month"]','[name="package_day"]','[name="package_year"]'];
        let ok = true;
        req.forEach(s => { const f=$(s); if (!f.val()?.trim()) { f.addClass('required-field'); ok=false; } else f.removeClass('required-field'); });
        if (!ok) { alert('Please fill in all required fields.'); return false; }
    }
    if (n === 2) {
        if (window.requiresTransport) {
            const req = ['[name="package_use_date"]','[name="transportation_pickup_time"]','[name="transportation_address"]','[name="transportation_phone"]'];
            let ok = true;
            req.forEach(s => { const f=$(s); if (!f.val()?.trim()) { f.addClass('required-field'); ok=false; } else f.removeClass('required-field'); });
            const transportationGuestField = $('[name="transportation_guest"]');
            const transportationGuestValue = parseInt(transportationGuestField.val(), 10);
            if (!Number.isFinite(transportationGuestValue) || transportationGuestValue < 1) {
                transportationGuestField.addClass('required-field');
                ok = false;
            } else {
                transportationGuestField.removeClass('required-field');
            }
            if (!ok) { alert('Please fill in transportation details, including Number of Guests (minimum 1).'); return false; }
        } else {
            if (!$('#transportation_part').is(':checked')) { alert('Please confirm your transportation arrangement.'); return false; }
        }
    }
    return true;
}

function populatePaymentFields() {
    $('#hidden_payment_phone').val($('[name="package_phone"]').val());
    $('#hidden_payment_email').val($('[name="package_email"]').val());
    $('#hidden_payment_month').val($('[name="package_month"]').val());
    $('#hidden_payment_day').val($('[name="package_day"]').val());
    $('#hidden_payment_year').val($('[name="package_year"]').val());
}

$(document).ready(function() {
    $('#next-to-transport').click(() => { if (validateStep(1)) showStep(2); });
    $('#prev-to-pkg').click(() => showStep(1));
    $('#prev-to-pkg-from-form').click(() => showStep(1));
    $('#next-to-pay-confirm').click(() => { if (validateStep(2)) { populatePaymentFields(); showStep(3); } });
    $('#next-to-pay').click(() => { if (validateStep(2)) { populatePaymentFields(); showStep(3); } });
    $('#prev-to-transport').click(() => showStep(2));

    $(document).on('click', '.same-as-info', function() {
        $('[name="payment_first_name"]').val($('[name="package_first_name"]').val());
        $('[name="payment_last_name"]').val($('[name="package_last_name"]').val());
        populatePaymentFields();
    });
    $(document).on('click', '.same-as-info-transport', function() {
        $('[name="transportation_phone"]').val($('[name="package_phone"]').val());
    });
    $(document).on('input change', 'input, select, textarea', function() { $(this).removeClass('required-field'); });
});
</script>

<script>
// ============================================================
//  RAW CARD INPUT FORMATTER (Authorize.net fields)
// ============================================================
(function initRawCardNumberFormatting() {
    function detectCardMeta(digits) {
        const number = String(digits || '');

        if (/^3[47]/.test(number)) {
            return { maxLen: 15, validLens: [15], grouping: [4, 6, 5] };
        }
        if (/^3(?:0[0-5]|[68])/.test(number)) {
            return { maxLen: 14, validLens: [14], grouping: [4, 6, 4] };
        }
        if (/^(5[1-5]|2[2-7])/.test(number)) {
            return { maxLen: 16, validLens: [16], grouping: [4, 4, 4, 4] };
        }
        if (/^(6011|65|64[4-9])/.test(number)) {
            return { maxLen: 19, validLens: [16, 19], grouping: [4, 4, 4, 4, 3] };
        }
        if (/^4/.test(number)) {
            return { maxLen: 19, validLens: [13, 16, 19], grouping: [4, 4, 4, 4, 3] };
        }
        if (/^35/.test(number)) {
            return { maxLen: 19, validLens: [16, 17, 18, 19], grouping: [4, 4, 4, 4, 3] };
        }

        return { maxLen: 19, validLens: [13, 14, 15, 16, 17, 18, 19], grouping: [4, 4, 4, 4, 3] };
    }

    function formatWithGrouping(digits, grouping) {
        let cursor = 0;
        const parts = [];

        for (let i = 0; i < grouping.length && cursor < digits.length; i++) {
            const size = grouping[i];
            const chunk = digits.slice(cursor, cursor + size);
            if (!chunk) {
                break;
            }
            parts.push(chunk);
            cursor += size;
        }

        if (cursor < digits.length) {
            parts.push(digits.slice(cursor));
        }

        return parts.join(' ');
    }

    function applyMask(input) {
        if (!input) {
            return;
        }

        let digits = String(input.value || '').replace(/\D/g, '');
        const meta = detectCardMeta(digits);

        if (digits.length > meta.maxLen) {
            digits = digits.slice(0, meta.maxLen);
        }

        input.value = formatWithGrouping(digits, meta.grouping);
        input.maxLength = formatWithGrouping('9'.repeat(meta.maxLen), meta.grouping).length;
        input.setAttribute('inputmode', 'numeric');
        input.setAttribute('autocomplete', 'cc-number');
        input.setCustomValidity('');

        if (digits.length > 0 && !meta.validLens.includes(digits.length)) {
            input.setCustomValidity('Please enter a valid card number.');
        }
    }

    function bindField(input) {
        if (!input || input.dataset.cardFormatBound === '1') {
            return;
        }

        input.dataset.cardFormatBound = '1';
        applyMask(input);
        input.addEventListener('input', function() { applyMask(input); });
        input.addEventListener('blur', function() { applyMask(input); });
    }

    const cardFields = document.querySelectorAll('input[name="card_number"]');
    cardFields.forEach(function(field) { bindField(field); });

    const form = document.getElementById('payment-form');
    if (form) {
        form.addEventListener('submit', function(event) {
            const inputs = form.querySelectorAll('input[name="card_number"]');
            let hasInvalid = false;

            inputs.forEach(function(input) {
                applyMask(input);
                if (!input.checkValidity()) {
                    hasInvalid = true;
                }
                input.value = String(input.value || '').replace(/\D/g, '');
            });

            if (hasInvalid) {
                event.preventDefault();
                const first = inputs[0];
                if (first) {
                    first.reportValidity();
                }
            }
        });
    }
})();

// ============================================================
//  FORM SUBMIT (Stripe token or direct Authorize.net POST)
// ============================================================
function showCheckoutProcessingOverlay() {
    const overlay = document.getElementById('checkout-processing-overlay');
    if (!overlay) {
        return;
    }

    overlay.classList.add('is-visible');
    overlay.setAttribute('aria-hidden', 'false');

    const submitButton = document.getElementById('submitBtn');
    if (submitButton) {
        if (!submitButton.dataset.defaultText) {
            submitButton.dataset.defaultText = submitButton.textContent;
        }
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
    }
}

function hideCheckoutProcessingOverlay() {
    const overlay = document.getElementById('checkout-processing-overlay');
    if (!overlay) {
        return;
    }

    overlay.classList.remove('is-visible');
    overlay.setAttribute('aria-hidden', 'true');

    const submitButton = document.getElementById('submitBtn');
    if (submitButton) {
        submitButton.disabled = false;
        submitButton.textContent = submitButton.dataset.defaultText || 'Complete Purchase';
    }
}

$('#payment-form').on('submit', async function(e) {
    e.preventDefault();
    const c = window.activeClub;
    if (!c) { alert('Please select a package first.'); return; }
    if (!window.cart.length) { alert('Your cart is empty.'); return; }
    if (!this.reportValidity()) { return; }

    syncCheckoutCartFields();

    // Populate addons field from cart
    const pkgId = $('#package_id').val();
    const pkg = window.cart.find(p => p.pkgId == pkgId);
    if (pkg) {
        $('#addons').val(pkg.addons.map(a => a.name + ' ($' + a.price + ')').join(', '));
    }

    if (c.paymentMethod === 'stripe') {
        if (!stripeObj || !cardNum_el) { alert('Payment not ready. Please try again.'); return; }
        showCheckoutProcessingOverlay();
        const { token, error } = await stripeObj.createToken(cardNum_el);
        if (error) { hideCheckoutProcessingOverlay(); alert(error.message); return; }
        const h = document.createElement('input');
        h.type = 'hidden'; h.name = 'stripeToken'; h.value = token.id;
        this.appendChild(h);
    } else {
        showCheckoutProcessingOverlay();
    }

    this.submit();
});
</script>

<script>
// ============================================================
//  PROMO CODE
// ============================================================
$('#applyPromoBtn').on('click', function() {
    const c = window.activeClub;
    if (!c) return;
    const code = $('#promo_code').val().trim();
    if (!code) return;
    $.get('/' + c.slug + '/check/' + encodeURIComponent(code), { source: '{{ $isEntertainerProfile ? 'entertainer' : 'affiliate' }}', owner_slug: '{{ $affiliate->slug }}' }, function(res) {
        if (!res.valid || res.valid === 'false') {
            window.cartCoupon = null; alert('Invalid promo code.'); calcTotal();
        } else {
            window.cartCoupon = { code, id: res.id, discount: parseFloat(res.discount), type: res.type || 'percentage' };
            $('#applyPromoBtn').prop('disabled', true);
            $('.promo_code').val(res.id);
            calcTotal();
        }
    });
});
</script>

<script>
// ============================================================
//  COUNTRY / STATE / DOB SELECTS
// ============================================================
function fillCountry(id) {
    const CC = ['United States','Canada','United Kingdom','Australia','Germany','France','Italy','Spain','Netherlands','Brazil','India','China','Japan','South Korea','Mexico','Russia','South Africa','New Zealand','Sweden','Norway','Denmark','Finland','Ireland','Switzerland','Austria','Belgium','Portugal','Poland','Turkey','Argentina','Chile','Colombia','Czech Republic','Greece','Hungary','Iceland','Indonesia','Israel','Malaysia','Philippines','Saudi Arabia','Singapore','Slovakia','Thailand','Ukraine','United Arab Emirates','Vietnam','Egypt','Morocco','Nigeria','Pakistan','Romania','Serbia','Croatia','Slovenia','Bulgaria','Estonia','Latvia','Lithuania','Luxembourg','Malta','Monaco','Montenegro','Qatar','Kuwait','Oman','Bahrain','Jordan','Lebanon','Cyprus','Georgia','Kazakhstan','Uzbekistan','Bangladesh','Sri Lanka','Nepal','Cambodia','Laos','Myanmar','Mongolia','Afghanistan','Albania','Armenia','Azerbaijan','Belarus','Bosnia and Herzegovina','Botswana','Brunei','Burkina Faso','Burundi','Cameroon','Cape Verde','Central African Republic','Chad','Comoros','Congo','Costa Rica','Cuba','Djibouti','Dominica','Dominican Republic','Ecuador','El Salvador','Equatorial Guinea','Eritrea','Eswatini','Ethiopia','Fiji','Gabon','Gambia','Ghana','Grenada','Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Jamaica','Kenya','Kiribati','Lesotho','Liberia','Libya','Liechtenstein','Madagascar','Malawi','Maldives','Mali','Marshall Islands','Mauritania','Mauritius','Micronesia','Moldova','Mozambique','Namibia','Nauru','Nicaragua','Niger','North Korea','North Macedonia','Palau','Palestine','Panama','Papua New Guinea','Paraguay','Peru','Rwanda','Saint Kitts and Nevis','Saint Lucia','Saint Vincent and the Grenadines','Samoa','San Marino','Sao Tome and Principe','Senegal','Seychelles','Sierra Leone','Solomon Islands','Somalia','South Sudan','Sudan','Suriname','Syria','Tajikistan','Tanzania','Timor-Leste','Togo','Tonga','Trinidad and Tobago','Tunisia','Turkmenistan','Tuvalu','Uganda','Uruguay','Vanuatu','Vatican City','Venezuela','Yemen','Zambia','Zimbabwe'];
    const el = document.getElementById(id); if (!el) return;
    el.innerHTML = '<option value="">Select Country</option>';
    CC.forEach(c => el.innerHTML += `<option value="${c}">${c}</option>`);
}

function fillDOB(mId, dId, yId) {
    const m = document.getElementById(mId), d = document.getElementById(dId), y = document.getElementById(yId);
    if (!m || !d || !y) return;
    m.innerHTML = d.innerHTML = y.innerHTML = '';
    for (let i=1; i<=12; i++) m.innerHTML += `<option value="${String(i).padStart(2,'0')}">${String(i).padStart(2,'0')}</option>`;
    for (let i=1; i<=31; i++) d.innerHTML += `<option value="${String(i).padStart(2,'0')}">${String(i).padStart(2,'0')}</option>`;
    const yr = new Date().getFullYear();
    for (let i=yr; i>=yr-100; i--) y.innerHTML += `<option value="${i}">${i}</option>`;
}

$(function() {
    fillCountry('country');
    fillDOB('package-dob-month','package-dob-day','package-dob-year');
});

$(document).on('change', '#country', function() {
    const country = $(this).val(), $st = $('#st-pv');
    $st.html('<option>Loading…</option>');
    if (!country) { $st.html('<option disabled selected>Select State/Province</option>'); return; }
    $.ajax({
        url: 'https://countriesnow.space/api/v0.1/countries/states',
        type: 'POST', contentType: 'application/json',
        data: JSON.stringify({ country }),
        success: function(res) {
            if (res?.data?.states?.length) {
                let o = '<option value="null" disabled selected>Select State/Province</option>';
                res.data.states.forEach(s => o += `<option value="${s.name}">${s.name}</option>`);
                $st.html(o);
            } else { $st.html('<option disabled>No states found</option>'); }
        },
        error: function() { $st.html('<option disabled>Error loading states</option>'); }
    });
});

flatpickr('.flatpickr-time', { enableTime:true, noCalendar:true, dateFormat:'h:i K', time_24hr:false });
flatpickr('#package_use_date', {
    dateFormat: 'Y-m-d',
    defaultDate: '{{ \Carbon\Carbon::now()->format('Y-m-d') }}',
    minDate: 'today',
    allowInput: false,
    clickOpens: true
});
$('.custom-calendar-icon').on('click', function() {
    const picker = document.getElementById('package_use_date')._flatpickr;
    if (picker) {
        picker.open();
    }
});

document.addEventListener('click', function(event) {
    const trigger = event.target.closest('.js-checkout-gallery-trigger');
    if (!trigger) {
        return;
    }

    const modalElement = document.getElementById('checkoutGalleryModal');
    const modalImage = document.getElementById('checkoutGalleryModalImage');
    if (!modalElement || !modalImage) {
        return;
    }

    modalImage.src = trigger.getAttribute('data-gallery-src') || '';
    modalImage.alt = trigger.getAttribute('data-gallery-alt') || 'Gallery image';
    bootstrap.Modal.getOrCreateInstance(modalElement).show();
});

document.getElementById('checkoutGalleryModal')?.addEventListener('hidden.bs.modal', function() {
    const modalImage = document.getElementById('checkoutGalleryModalImage');
    if (modalImage) {
        modalImage.src = '';
        modalImage.alt = '';
    }
});
</script>

</body>
</html>

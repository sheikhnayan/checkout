@php
    $brandPrimary = '#2563eb';
    $brandSecondary = '#1d4ed8';
    $brandGradient = 'linear-gradient(135deg, #3b82f6 0%, #2563eb 52%, #1d4ed8 100%)';
    $data->color = $brandPrimary;
    $data->secondary_color = $brandSecondary;
    $data->background_color = '#ffffff';
    $data->font_color = '#0f172a';
@endphp
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Checkout</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('user/assets/img/favicon/favicon.svg') }}?v={{ time() }}" />
        <link rel="mask-icon" href="{{ asset('user/assets/img/favicon/safari-mask.svg') }}?v={{ time() }}" color="#ffcc00" />
        <link rel="shortcut icon" href="{{ asset('user/assets/img/favicon/favicon.ico') }}?v={{ time() }}" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css"
            integrity="sha512-fw7f+TcMjTb7bpbLJZlP8g2Y4XcCyFZW8uy8HsRZsH/SwbMw0plKHFHr99DN3l04VsYNwvzicUX/6qurvIxbxw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('styles/main.css') }}">
        
        @php
            $gaMeasurementId = preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($data->google_analytics_id ?? ''));
        @endphp
        @if(!empty($gaMeasurementId))
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaMeasurementId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ $gaMeasurementId }}');
            </script>
        @endif
        <!-- reCAPTCHA v3 Script -->
        @if(config('services.recaptcha.site_key') && config('services.recaptcha.site_key') !== 'YOUR_RECAPTCHA_SITE_KEY_HERE')
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
        <script>
            window.executeRecaptcha = function(action = 'submit') {
                return new Promise((resolve) => {
                    if (!window.grecaptcha) {
                        resolve(null);
                        return;
                    }
                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: action})
                            .then(function(token) {
                                resolve(token);
                            })
                            .catch(function() {
                                resolve(null);
                            });
                    });
                });
            };
        </script>
        @endif
        <link rel="stylesheet" href="{{ asset('styles/checkout-template-1.css') }}?v={{ file_exists(public_path('styles/checkout-template-1.css')) ? filemtime(public_path('styles/checkout-template-1.css')) : time() }}">
    </head>

    <body class="{{ !empty($isIframeCheckout) ? 'embed-checkout-mode' : '' }} {{ !empty($isSinglePackageCheckout) ? 'single-package-checkout-mode' : '' }}">
        @php
            $isSharedLink = request()->hasAny([
                'package',
                'addons',
                'guests',
                'use_date',
                'coupon'
            ]);
        @endphp
        <div class="background-glow"></div>

        {{-- New CartVIP Navbar --}}
        <nav class="cv-top-nav">
            <a href="https://cartvip.com" target="_blank" class="cv-nav-brand">
                <img src="{{ asset('images/logo.png') }}" alt="CartVIP" class="cv-nav-logo-img">
            </a>
            @php
                $allPackagesCheckoutUrl = url('/' . $data->slug);
            @endphp
            <div class="cv-nav-actions">
                @if ($data->back_link)
                <a href="{{ $data->back_link }}" class="cv-nav-back">+ {{ $data->back_text ?: 'Back to Home' }}</a>
                @else
                <a href="https://cartvip.com" class="cv-nav-back">+ Back to Home</a>+ {{ $data->back_text ?: 'Back to Home' }}</a>
                @endif
                @if (!empty($isSinglePackageCheckout))
                <a href="{{ $allPackagesCheckoutUrl }}" class="cv-nav-back cv-nav-view-all">
                    <i class="fas fa-th-large"></i> View All Packages
                </a>
                @endif
            </div>
            <button class="cv-hamburger" id="cv-hamburger" aria-label="Open menu">
                <span></span><span></span><span></span>
            </button>
        </nav>

        {{-- Duplicate venue header removed - club details are shown in the hero section --}}

                <header>
            <div class="container py-1">
                @session('success')
                    <div class="alert alert-success" role="alert">Purchase Successful!</div>
                @endsession

                @session('error')
                    <div class="alert alert-danger" role="alert">{{ $value }}</div>
                @endsession

                @php
                    $heroVenueAvatar = null;
                    if (!empty($data->logo)) {
                        $heroVenueAvatar = asset('uploads/' . $data->logo);
                    } elseif (!empty($data->gallery_images) && is_array($data->gallery_images) && !empty($data->gallery_images[0])) {
                        $heroVenueAvatar = asset('uploads/' . $data->gallery_images[0]);
                    } else {
                        $heroVenueAvatar = asset('images/logo.png');
                    }
                @endphp

                @if (empty($isSinglePackageCheckout))
                <section class="cv-hero-card">
                    <div class="cv-hero-card-header">
                        <div class="cv-hero-venue-info">
                            @if ($data->logo)
                                <img src="{{ asset('uploads/' . $data->logo) }}" alt="{{ $data->name }}" class="cv-hero-venue-thumb" @if(!empty($data->logo_style)) style="{{ $data->logo_style }}" @endif>
                            @else
                                <span class="cv-hero-venue-initial">{{ strtoupper(substr($data->name, 0, 1)) }}</span>
                            @endif
                            <div class="cv-hero-venue-meta-wrap">
                                <div class="cv-hero-venue-name-row">
                                    <h2 class="cv-hero-venue-name">{{ $data->name }}</h2>
                                    <span class="cv-verified-badge" title="Verified Venue"><i class="fas fa-check-circle"></i></span>
                                </div>
                                <div class="cv-hero-venue-address">{{ $data->location ?: '123 Ocean Drive, Miami, FL 33139' }}</div>
                                <div class="cv-hero-venue-rating">
                                    <i class="fas fa-star"></i> <span>4.8 (1.2k+ reviews)</span>
                                </div>
                            </div>
                        </div>
                        <div class="cv-hero-badges-row">
                            <div class="cv-hero-badge-pill">
                                <i class="far fa-clock"></i>
                                <div>
                                    <div class="cv-badge-label">{{ $data->hero_badge_1_label ?: 'Open Daily' }}</div>
                                    <div class="cv-badge-value">{{ $data->hero_badge_1_sub ?: '5:00 PM - 2:00 AM' }}</div>
                                </div>
                            </div>
                            <div class="cv-hero-badge-pill">
                                <i class="fas fa-trophy"></i>
                                <div>
                                    <div class="cv-badge-label">{{ $data->hero_badge_2_label ?: 'Top Rated Venue' }}</div>
                                    <div class="cv-badge-value">{{ $data->hero_badge_2_sub ?: '#1 Lounge in Miami' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cv-hero-card-body">
                        <div class="cv-hero-body-left">
                            <div class="cv-kicker">VENUE CHECKOUT</div>
                            <h1 class="cv-hero-main-title">{{ $data->hero_title ?: 'Reserve Your VIP Experience' }}</h1>
                            <p class="cv-hero-desc">{!! $data->hero_subtitle ?: ($data->description ?: "Enjoy exclusive access, premium seating, and curated amenities at Miami's most talked-about lounge. Book your experience now and make it unforgettable.") !!}</p>

                            <div class="cv-hero-date-box">
                                <label class="cv-date-box-label">CHOOSE YOUR RESERVATION DATE</label>
                                <div class="cv-date-input-group">
                                    <i class="far fa-calendar-alt cv-calendar-icon-left"></i>
                                    <input id="package_use_date" type="text"
                                        value="" placeholder="{{ \Carbon\Carbon::now($data->resolved_timezone)->format('Y-m-d') }}" readonly aria-describedby="package_use_date_error">
                                    <i class="far fa-calendar cv-calendar-icon-right"></i>
                                </div>
                                <small id="package_use_date_error" class="reservation-date-error" style="display:none; color:#ef4444; font-size:12px; margin-top:4px;">Please select a reservation date.</small>
                            </div>
                        </div>

                        <div class="cv-hero-body-right">
                            <div class="cv-find-us-card">
                                <div class="cv-find-us-header">
                                    <div class="cv-find-us-tag"><span class="cv-tag-dot">◆</span> FIND US</div>
                                    <div class="cv-find-us-venue">{{ $data->name }}</div>
                                    <div class="cv-find-us-addr">{{ $data->location ?: '123 Ocean Drive, Miami, FL 33139' }}</div>
                                </div>
                                <div class="cv-map-container">
                                    <iframe 
                                        src="https://maps.google.com/maps?q={{ urlencode($data->location ?: ($data->name . ' Miami')) }}&output=embed" 
                                        class="cv-map-iframe" 
                                        loading="lazy"
                                        frameborder="0" 
                                        scrolling="no">
                                    </iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                @endif
            </div>
        </header>
        <main>
            <div class="container mt-4">
                {{-- Mobile: toggle to show/hide order summary --}}
                <button type="button" class="cv-mobile-cart-toggle" id="cv-mobile-cart-toggle" style="display:none;">
                    <span><i class="fas fa-shopping-cart" style="margin-right:6px;"></i>View Order Summary</span>
                    <span class="cv-mobile-cart-count" id="cv-mobile-cart-count">0 items</span>
                </button>

                <div class="cv-checkout-body" id="cv-checkout-layout">
                    <div class="cv-main-col" id="cv-checkout-main" style="background: #fff; padding: 24px; border-radius: 18px; border: 1px solid var(--cv-border) !important;">
                    <div class="cv-desktop-shell">
                        <div class="cv-desktop-steps" id="cv-checkout-steps" @if(!empty($isSinglePackageCheckout) || $data->reservation != 1) style="grid-template-columns: repeat(3, minmax(0, 1fr)) !important;" @endif>
                            <div class="cv-dstep is-active" id="cv-dstep-1" data-step="1"><span class="cv-dstep-num">1</span><span>Choose Date</span></div>
                            <div class="cv-dstep" id="cv-dstep-2" data-step="2"><span class="cv-dstep-num">2</span><span>{{ !empty($isSinglePackageCheckout) ? 'Select Guest' : (($data->reservation == 1) ? 'Choose Access' : 'Select Package') }}</span></div>
                            <div class="cv-dstep" id="cv-dstep-3" data-step="3"><span class="cv-dstep-num">3</span><span>{{ !empty($isSinglePackageCheckout) ? 'Review & Pay' : (($data->reservation == 1) ? 'Select Package' : 'Review & Pay') }}</span></div>
                            @if (empty($isSinglePackageCheckout) && $data->reservation == 1)
                                <div class="cv-dstep" id="cv-dstep-4" data-step="4"><span class="cv-dstep-num">4</span><span>Review &amp; Pay</span></div>
                            @endif
                        </div>
                        @if (!empty($isSinglePackageCheckout))
                            
                            <div class="hero-date-card single-package-date-card">
                                <label>Choose Your Reservation Date</label>
                                <div class="date-input-wrapper">
                                    <input id="package_use_date" type="text"
                                        value="" placeholder="{{ \Carbon\Carbon::now($data->resolved_timezone)->format('M d, Y') }}" style="width: 100%;" readonly aria-describedby="package_use_date_error">
                                    <span class="custom-calendar-icon"></span>
                                </div>
                                <small id="package_use_date_error" class="reservation-date-error">Please select a reservation date.</small>
                            </div>
                        @endif
                        @if ($data->reservation == 1)
                        
                        <div class="cv-desktop-steps cv-desktop-steps-res" id="cv-checkout-steps-res">
                            <div class="cv-dstep is-active" id="cv-rstep-1" data-step="1"><span class="cv-dstep-num">1</span><span>Choose Date</span></div>
                            <div class="cv-dstep" id="cv-rstep-2" data-step="2"><span class="cv-dstep-num">2</span><span>Your Details</span></div>
                            <div class="cv-dstep" id="cv-rstep-3" data-step="3"><span class="cv-dstep-num">3</span><span>Submit</span></div>
                        </div>
                        @endif

                        @if ($data->reservation == 1 && empty($isSinglePackageCheckout))
                            <div class="cv-access-hint">Choose one to continue<span class="cv-access-hint-dot"></span></div>
                        @endif
                        @php
                            $cvGuestHex = ltrim($data->guest_tab_color ?? '#34d399', '#');
                            $cvPkgHex   = ltrim($data->package_tab_color ?? '#e8be6a', '#');
                            [$cvGr, $cvGg, $cvGb] = sscanf($cvGuestHex, '%02x%02x%02x');
                            [$cvPr, $cvPg, $cvPb] = sscanf($cvPkgHex, '%02x%02x%02x');
                            $cvGRgb = "$cvGr,$cvGg,$cvGb";
                            $cvPRgb = "$cvPr,$cvPg,$cvPb";
                        @endphp
                        
                        @if ($data->reservation != 1)
                        
                        @endif
                        @if (empty($isSinglePackageCheckout))
                        @if ($data->reservation == 1)<div class="cv-access-grid">
                                <button type="button" class="cv-access-card cv-access-tab is-active" data-name="package">
                                    @if(!empty($data->package_tab_ribbon))
                                        <span class="cv-ac-ribbon">{{ $data->package_tab_ribbon }}</span>
                                    @endif
                                    <span class="cv-ac-shimmer" aria-hidden="true"></span>
                                    <span class="cv-ac-icon-wrap"><i class="fas {{ $data->package_tab_icon ?? 'fa-star' }}"></i></span>
                                    <span class="cv-ac-body">
                                        <strong>{{ $data->package_button_text ?? 'VIP Packages' }}</strong>
                                        <span style="color: #fff !important;">{{ $data->package_tab_subtitle ?? 'VIP table packages &amp; experiences' }}</span>
                                    </span>
                                </button>
                                <button type="button" class="cv-access-card cv-access-tab" data-name="guest">
                                    <span class="cv-ac-icon-wrap"><i class="fas {{ $data->guest_tab_icon ?? 'fa-car-side' }}"></i></span>
                                    <span class="cv-ac-body">
                                        <strong>{{ $data->guest_list_button_text ?? 'Free Ride & Entry' }}</strong>
                                        <span style="color: #fff !important;">{{ $data->guest_tab_subtitle ?? 'Complimentary ride and general entry' }}</span>
                                    </span>
                                </button>
                            </div>@endif
                        @endif
                    </div>

                @if ($data->reservation == 1)
                    <div class="guest">
                        <form action="{{ route('reservations.store', ['slug' => $data->slug]) }}" method="post">
                            @csrf
                            <input type="hidden" name="website_id" value="{{ $data->id }}">
                            <input type="hidden" name="affiliate_slug" value="{{ $affiliateReferral->slug ?? '' }}">
                            <!-- Reservation date - synced from header dropdown -->
                            <input type="hidden" name="package_use_date" value="">
                            <section style="width: 100%">
                                <h5 class="section-kicker-lg">Guest List Reservation</h5>
                                <div class="">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- Left: Form Fields -->
                                            <div class="">

                                                <div class="form-row" style="margin-bottom: 1rem;">
                                                    <div class="form-group" style="width: 50%;">
                                                        <label for="firstName">First Name</label>
                                                        <input type="text" name="reservation_first_name"
                                                            id="firstName" placeholder="First Name" required />
                                                    </div>
                                                    <div class="form-group" style="width: 50%;">
                                                        <label for="lastName">Last Name</label>
                                                        <input type="text" name="reservation_last_name"
                                                            id="lastName" placeholder="Last Name" required />
                                                    </div>
                                                </div>

                                                <div class="form-row" style="margin-bottom: 1rem;">
                                                    <div class="form-group" style="width: 50%;">
                                                        <label for="phone">Phone Number</label>
                                                        <input type="tel" name="reservation_phone" id="reservation_phone"
                                                            placeholder="(555) 123-4567" required />
                                                        <div class="phone-note" style="font-size: 0.75rem; color: rgba(255,255,255,0.6); margin-top: 4px;">Phone formatting may vary by country. International SMS delivery is not guaranteed.</div>
                                                    </div>
                                                    <div class="form-group" style="width: 50%;">
                                                        <label for="email">Email</label>
                                                        <input type="email" name="reservation_email" id="email"
                                                            placeholder="For Confirmation" required />
                                                    </div>
                                                </div>

                                                <div class="form-row" style="margin-bottom: 1rem;">
                                                    <div class="form-group ddoobb" style="width: 100%;">
                                                        <label for="dob-month">Date of Birth <span class="text-danger">*</span></label>
                                                        <div class="form-row" style="display: flex; gap: 10px; width: 100%;">
                                                            <select id="dob-month" name="reservation_day"
                                                                class="form-select cv-dob-select"
                                                                required></select>
                                                            <select id="dob-day" name="reservation_month"
                                                                class="form-select cv-dob-select"
                                                                required></select>
                                                            <select id="dob-year" name="reservation_year"
                                                                class="form-select cv-dob-select"
                                                                required></select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group" style="margin-bottom: 1rem;">
                                                    <label for="note">Booking Note</label>
                                                    <textarea id="note" name="reservation_description" placeholder="Your occasion or special request?"></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="host">Host / Promoter Referral</label>
                                                    <input id="host" name="host_name"
                                                        placeholder="Enter host/promoter name or referral code (optional)">
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </section>


                            <section class="guest-count">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-12 guest-list">
                                            <h2>Total Guests</h2>
                                            <div class="guest-gender-row">
                                                <div class="guest-section guest-section--men"
                                                    style="border-color: {{ $brandPrimary }} !important;">
                                                    <span class="label">Men</span>
                                                    <div class="counter">
                                                        <span class="addon-qty-stepper guest-qty-stepper">
                                                            <button class="addon-qty-btn guest-qty-btn" type="button"
                                                                data-type="men" data-action="dec"
                                                                onclick="decrements('men')">−</button>
                                                            <span class="count addon-qty-val guest-qty-val" id="menCount">0</span>
                                                            <button class="addon-qty-btn guest-qty-btn" type="button"
                                                                data-type="men" data-action="inc"
                                                                onclick="increments('men')">+</button>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="guest-section guest-section--women"
                                                    style="border-color: {{ $brandPrimary }} !important;">
                                                    <span class="label">Women</span>
                                                    <div class="counter">
                                                        <span class="addon-qty-stepper guest-qty-stepper">
                                                            <button class="addon-qty-btn guest-qty-btn" type="button"
                                                                data-type="women" data-action="dec"
                                                                onclick="decrements('women')">−</button>
                                                            <span class="count addon-qty-val guest-qty-val" id="womenCount">0</span>
                                                            <button class="addon-qty-btn guest-qty-btn" type="button"
                                                                data-type="women" data-action="inc"
                                                                onclick="increments('women')">+</button>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="guest-section guest-section--total"
                                                    style="border-color: {{ $brandPrimary }} !important;">
                                                    <span class="label">Total Guests</span>
                                                    <div class="counter">
                                                        <span class="addon-qty-stepper guest-qty-stepper">
                                                            <span class="count addon-qty-val guest-qty-val" id="totalCount" style="margin-right: 0px !important">0</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="men_count" id="men_count" value="0">
                                            <input type="hidden" name="women_count" id="women_count"
                                                value="0">
                                        </div>
                                        <div class="col-md-12 mt-4">
                                        </div>
                                        <div class="col-md-12">
                                            <div class="checkbox-container">
                                                @if($data->show_sms_consent ?? true)
                                                <label class="consent-label">
                                                    <input type="checkbox" id="smsConsent_two" required />
                                                    <span>{{ $data->sms_consent_text }}</span>
                                                </label>
                                                @endif
                                                @if($data->show_terms_consent ?? true)
                                                <label class="consent-label">
                                                    <input type="checkbox" id="termsConsent_two" required />
                                                    <span>{!! $data->terms_consent_text_formatted !!}</span>
                                                </label>
                                                @endif
                                            </div>
                                            <button class="submit-btn" type="submit" id="submitBtn_two">Create
                                                Reservation</button>

                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                </div>

                            </section>

                            {{-- Location card removed (now lives in the hero .cv-hero-location panel) --}}


                            <input type="hidden" name="type" value="guest">
                            <input type="hidden" name="recaptcha_token" id="recaptcha_token" value="">
                            <input type="hidden" name="form_load_time" id="form_load_time" value="">

                        </form>
                    </div>
                @endif


                <div class="package">
                    <section class="vip-pack">
                        <div class="">

                            <div class="row">
                                <div class="col-md-12">

                                    @php
                                        $mostPopularPackageName = '';
                                        $mostPopularPackageId = null;
                                        $mostPopularPackageCatId = null;
                                        if (isset($packageCategories) && $packageCategories->count()) {
                                            foreach ($packageCategories as $category) {
                                                $catId = is_array($category) ? ($category['id'] ?? null) : ($category->id ?? null);
                                                $pkgs = is_array($category) ? ($category['packages'] ?? []) : ($category->packages ?? []);
                                                foreach ($pkgs as $pkg) {
                                                    if ((int) ($pkg->is_most_popular ?? 0) === 1) {
                                                        $mostPopularPackage = $pkg;
                                                        $mostPopularPackageName = $pkg->name ?? '';
                                                        $mostPopularPackageId = $pkg->id ?? null;
                                                        $mostPopularPackageCatId = $catId;
                                                        break 2;
                                                    }
                                                }
                                            }
                                            if (!$mostPopularPackageId) {
                                                $firstCategory = $packageCategories->first();
                                                if ($firstCategory) {
                                                    $mostPopularPackageCatId = is_array($firstCategory) ? ($firstCategory['id'] ?? null) : ($firstCategory->id ?? null);
                                                    $pkgs = is_array($firstCategory) ? ($firstCategory['packages'] ?? []) : ($firstCategory->packages ?? []);
                                                    $firstPkg = collect($pkgs)->first();
                                                    if ($firstPkg) {
                                                        $mostPopularPackageName = $firstPkg->name ?? '';
                                                        $mostPopularPackageId = $firstPkg->id ?? null;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    @if(!empty($isSinglePackageCheckout))
                                    <style>
                                        .cv-package-section-header p { display: none !important; }
                                    </style>
                                    @endif
                                    @if(empty($isSinglePackageCheckout))
                                    <div class="cv-experience-banner">
                                        <div class="cv-experience-banner-left">
                                            <div class="cv-experience-icon-box">
                                                <i class="fas fa-shopping-cart"></i>
                                            </div>
                                            <div>
                                                <div class="cv-experience-title">Choose Your Experience</div>
                                                <div class="cv-experience-sub">Browse tickets and VIP packages below. Select your reservation date to book.</div>
                                            </div>
                                        </div>
                                        <i class="fas fa-star" style="color: var(--cv-t1-primary);"></i>
                                    </div>
                                    @endif

                                    <div class="cv-package-section-header" style="display:flex; justify-content:space-between; align-items:center; margin: 18px 0 14px; flex-wrap:wrap; gap:10px;">
                                        <div>
                                            @if(!empty($isSinglePackageCheckout))
                                            <style>
                                                .cv-package-section-header { margin: 0px 0 12px !important; }
                                            </style>
                                                <h5 class="section-kicker-lg" style="margin:0 !important; text-transform: unset; font-size: 0.8rem">Select your package to checkout, or <a href="{{ $allPackagesCheckoutUrl }}" style="text-transform: uppercase; color:inherit;text-decoration:underline;">View all packages.</a></h5>
                                            @else
                                                <h5 class="section-kicker-lg" style="margin:0 !important; font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-t1-ink);">{{ $data->package_section_title ?: 'Select Your Package' }}</h5>
                                                <p style="margin: 4px 0 0; font-size: 12.5px; color: var(--cv-t1-ink-muted);">{{ $data->package_section_subtext ?: 'All packages include free ride, club entry, and priority access.' }}</p>
                                            @endif
                                        </div>
                                        <div class="cv-filter-pills">
                                            <button type="button" class="cv-pill-btn is-active" id="btnFilterMostPopular">Most Popular</button>
                                            @if(!empty($mostPopularPackageName))
                                                <button type="button" class="cv-pill-btn btn-outline" id="btnFilterPopularPkg"
                                                    data-target-cat="#category-group-{{ $mostPopularPackageCatId }}"
                                                    data-target-pkg="#pkg-card-{{ $mostPopularPackageId }}"
                                                    title="View {{ $mostPopularPackageName }}">
                                                    {{ $mostPopularPackageName }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    @if(!empty($isIframeCheckout))
                                        <div class="hero-date-card iframe-date-card">
                                            <label>Choose Your Reservation Date</label>
                                            <div class="date-input-wrapper">
                                                <input id="package_use_date_iframe" type="text"
                                                    value="" placeholder="{{ \Carbon\Carbon::now($data->resolved_timezone)->format('M d, Y') }}" style="width: 100%;" readonly aria-describedby="package_use_date_iframe_error">
                                                <span class="custom-calendar-icon custom-calendar-icon-iframe"></span>
                                            </div>
                                            <small id="package_use_date_iframe_error" class="reservation-date-error" style="display:none;">Please select a reservation date.</small>
                                        </div>
                                    @endif

                                    @if(isset($packageCategories) && $packageCategories->count())
                                        @if(empty($isSinglePackageCheckout))
                                        <div class="mb-3 package-category-tiles" style="width:100%;">
                                            @foreach ($packageCategories as $category)
                                                @php
                                                    $catRgbStr = null;
                                                    if (!empty($category['color'])) {
                                                        $ch = ltrim($category['color'], '#');
                                                        [$cr, $cg, $cb] = sscanf($ch, '%02x%02x%02x');
                                                        $catRgbStr = "$cr,$cg,$cb";
                                                    }
                                                @endphp
                                                <button
                                                    type="button"
                                                    class="package-category-tile{{ $loop->first ? ' active' : '' }}{{ $catRgbStr ? ' has-cat-color' : '' }}"
                                                    data-target="#category-group-{{ $category['id'] }}"
                                                    @if($catRgbStr) style="--cat-rgb: {{ $catRgbStr }}" @endif
                                                >
                                                    @if(!empty($category['icon']))
                                                        <i class="fas {{ $category['icon'] }} package-category-tile-icon"></i>
                                                    @endif
                                                    <span class="package-category-name">{{ $category['name'] }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                        @endif

                                        @foreach ($packageCategories as $category)
                                            <div id="category-group-{{ $category['id'] }}" class="package-category-group" style="display: {{ ($loop->first || !empty($isSinglePackageCheckout)) ? 'block' : 'none' }};">
                                                @foreach ($category['packages'] as $item)
                                                    @php
                                                        $pkgTierIdx = ($loop->index % 5) + 1;
                                                        $pkgTierIcons = ['fas fa-crown','fas fa-star','fas fa-gem','fas fa-fire','fas fa-bolt'];
                                                        $pkgTierIcon = $pkgTierIcons[$pkgTierIdx - 1];
                                                        $pkgGuestCap = max(1, (int) ($item->guests_per_table ?: $item->number_of_guest ?: 1));
                                                        $pkgTableCap = max(2, (int) ($item->guests_per_table ?: $item->number_of_guest ?: 2));
                                                        $pkgIsTicket = ($item->package_type ?? 'table') === 'ticket';
                                                        $pkgTicketMax = max(1, (int) ($item->number_of_guest ?: 1));
                                                        $pkgTableMax  = max(2, (int) ($item->guests_per_table ?: $item->number_of_guest ?: 2));
                                                        $fallbackVisual = $data->logo ? asset('uploads/' . $data->logo) : asset('images/logo.png');
                                                        $packageVisual = !empty($item->image) ? asset('uploads/' . $item->image) : $fallbackVisual;
                                                        $packageMobileVisual = !empty($item->mobile_image) ? asset('uploads/' . $item->mobile_image) : $packageVisual;
                                                    @endphp
                                                    <div class="vip-card cv-tier-{{ $pkgTierIdx }} cv-exact-card" id="pkg-card-{{ $item->id }}">
                                                        <div class="cv-pkg-media-wrap">
                                                            <picture>
                                                                <source media="(max-width: 767px)" srcset="{{ $packageMobileVisual }}">
                                                                <img src="{{ $packageVisual }}" alt="{{ $item->name }}" class="cv-pkg-media">
                                                            </picture>
                                                            @if ((int) ($item->is_most_popular ?? 0) === 1)
                                                                <span class="cv-popular-pill">MOST POPULAR</span>
                                                            @endif
                                                        </div>

                                                        <div class="vip-card-main">
                                                            <div class="cv-pkg-title-row">
                                                                <i class="{{ $pkgTierIcon }} cv-pkg-title-icon"></i>
                                                                <div class="cv-pkg-title">{{ $item->name }}</div>
                                                                @if(trim((string) ($item->tooltip ?? '')) !== '')
                                                                    <button type="button" class="cv-pkg-tooltip-trigger" aria-label="View package details" data-title="{{ $item->name }}" data-tooltip="{{ trim((string) ($item->tooltip ?? '')) }}">i</button>
                                                                @endif
                                                            </div>
                                                            @if($pkgIsTicket)
                                                                <span class="cv-pkg-sub"><i class="fas fa-ticket-alt"></i>1 ticket per person</span>
                                                            @else
                                                                <span class="cv-pkg-sub"><i class="fas fa-user-friends"></i>Up to {{ $pkgTableMax }} guests</span>
                                                            @endif
                                                            @if($item->description)
                                                                <p class="cv-pkg-desc">{{ strip_tags($item->description) }}</p>
                                                            @endif
                                                            @php
                                                                $defaultPackageFeatures = [
                                                                    ['icon' => 'fa-chair', 'text' => 'VIP Table'],
                                                                    ['icon' => 'fa-wine-bottle', 'text' => '1 Premium Bottle'],
                                                                    ['icon' => 'fa-user-shield', 'text' => 'VIP Hosts'],
                                                                    ['icon' => 'fa-shield-alt', 'text' => $item->package_type === 'ticket' ? 'Free Entry' : 'Skip the Line'],
                                                                ];

                                                                $packageFeatures = collect(is_array($item->package_features) ? $item->package_features : [])
                                                                    ->map(function ($feature) {
                                                                        $icon = trim((string) ($feature['icon'] ?? ''));
                                                                        $text = trim((string) ($feature['text'] ?? ''));

                                                                        if ($text === '') {
                                                                            return null;
                                                                        }

                                                                        if (!preg_match('/^fa-[a-z0-9-]+$/i', $icon)) {
                                                                            $icon = 'fa-chair';
                                                                        }

                                                                        return [
                                                                            'icon' => strtolower($icon),
                                                                            'text' => $text,
                                                                        ];
                                                                    })
                                                                    ->filter()
                                                                    ->values();

                                                                if ($packageFeatures->isEmpty()) {
                                                                    $packageFeatures = collect($defaultPackageFeatures);
                                                                }
                                                            @endphp
                                                            <div class="cv-pkg-features">
                                                                @foreach($packageFeatures as $feature)
                                                                    <span class="cv-pkg-feature"><i class="fas {{ $feature['icon'] }}"></i>{{ $feature['text'] }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <div class="vip-card-side">
                                                            <div class="vip-price-tag price-{{ $item->id }}"
                                                                data-price="{{ $item->price }}">${{ number_format((float) $item->price, 2) }}</div>
                                                            @if(!$pkgIsTicket)
                                                                <div class="cv-price-meta">Per Package</div>
                                                            @endif

                                                            <div class="package-guest-input-wrap">
                                                                    @if ($item->package_type === 'ticket')
                                                                        @php $ticketInitMax = min(15, max(1, (int) ($item->number_of_guest ?? 1))); @endphp
                                                                        <select
                                                                            data-package-type="{{ $item->package_type }}"
                                                                            data-guests-per-table="{{ (int) ($item->guests_per_table ?? 0) }}"
                                                                            data-package-guest-limit="{{ (int) ($item->number_of_guest ?? 1) }}"
                                                                            data-ticket-max="{{ (int) ($item->number_of_guest ?? 1) }}"
                                                                            data-multiple="{{ $item->multiple }}"
                                                                            data-id="{{ $item->id }}"
                                                                            class="form-select package_number_of_guestss ticket-select-lazy"
                                                                            required
                                                                        >
                                                                            <option value=""># of Tickets</option>
                                                                            @for ($i = 1; $i <= $ticketInitMax; $i++)
                                                                                <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'ticket' : 'tickets' }}</option>
                                                                            @endfor
                                                                        </select>
                                                                    @else
                                                                        <select
                                                                            data-package-type="{{ $item->package_type }}"
                                                                            data-guests-per-table="{{ (int) ($item->guests_per_table ?? 0) }}"
                                                                            data-package-guest-limit="{{ $pkgTableCap }}"
                                                                            data-multiple="{{ $item->multiple }}"
                                                                            data-id="{{ $item->id }}"
                                                                            class="form-select package_number_of_guestss"
                                                                            required
                                                                        >
                                                                            <option value="">Select Guests ▼</option>
                                                                            @for ($i = 1; $i <= $pkgTableCap; $i++)
                                                                                <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'guest' : 'guests' }}</option>
                                                                            @endfor
                                                                        </select>
                                                                    @endif
                                                            </div>
                                                            <button class="vip-btn btn-{{ $item->id }} mt-2"
                                                                style="background-color: {{ $brandPrimary }} !important;"
                                                                data-id="{{ $item->id }}"
                                                                data-name="{{ $item->name }}"
                                                                data-price="{{ $item->price }}"
                                                                data-gratuity="{{ $data->gratuity_fee }}"
                                                                data-refundable="{{ $data->refundable_fee }}"
                                                                data-sales_tax="{{ $data->sales_tax_fee ?? 10 }}"
                                                                data-transportation="{{ $item->transportation }}"
                                                                data-service_charge="{{ $data->service_charge_fee ?? 10 }}"
                                                                data-default-label="Add to Cart">Add to Cart <i class="fas fa-chevron-right" style="font-size: 10px; margin-left: 4px;"></i></button>

                                                            <small class="package-guest-error" style="display:none;color:#ff6b6b;font-size:11px;line-height:1.35;margin-top:4px;"></small>
                                                            <div class="package-soldout" style="display:none;color:#ff2b2b;font-size:12px;font-weight:700;line-height:1.35;margin-top:4px;">Sold Out!</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    @else
                                        <p style="opacity:.6;">No packages are available yet.</p>
                                    @endif

                                    {{-- <div class="cv-freeride-callout">
                                        <div class="cv-freeride-icon"><i class="fas fa-car-side"></i></div>
                                        <div>
                                            <strong>Free Ride Included</strong>
                                            <span>Complimentary pickup &amp; return for you and your guests. We'll contact you after booking to confirm details.</span>
                                        </div>
                                    </div> --}}

                                    <section id="cart-section" class="container py-4" style="display:none; margin-bottom:2rem;">
                                        <div class="cart-heading">Your Cart</div>
                                        <div id="cart-list"></div>
                                        <div id="cart-total" style="font-size:15px;margin-top:8px;font-weight:600;"></div>
                                        <div id="cart-coupon" style="font-size:13px;color:#4caf7d;margin-top:4px;"></div>
                                    </section>

                                    <div class="row pricing-shell g-3">
                                        <div class="text-start mt-3 col-md-6">
                                            <div style="font-size: 16px;" class="default-price">Package:
                                                <span>$0.00</span>
                                            </div>
                                            <div class="dynamic-price" style="display: none;">
                                                <input type="hidden" id="old_price">
                                                <div style="font-size: 16px;" class="default-package-price"><span>Subtotal</span>
                                                    <span>$0.00</span>
                                                </div>
                                                <div class="addonns"></div>

                                                @if ($data->service_charge_name != 0)
                                                    <div style="font-size: 16px;" class="default-service-charge" data-tip="Covers reservation coordination, operational support, and service-related costs.">
                                                        <span>{{ $data->service_charge_name ?? 'Service Fee' }}</span>
                                                        <span>$0.00</span>
                                                    </div>
                                                @endif
                                                <div class="sales_tax"></div>
                                                @if ($data->sales_tax_name != 0)
                                                    <div style="font-size: 16px;" class="default-sales-tax" data-tip="Government-required sales tax based on local and state regulations.">
                                                        <span>{{ $data->sales_tax_name ?? 'Tax' }}</span> <span>$0.00</span>
                                                    </div>
                                                @endif

                                                @if ($data->gratuity_name != 0)
                                                    <div style="font-size: 16px;" class="default-gratuity" data-tip="Supports venue staff and hospitality service. Calculated based on subtotal.">
                                                        <span>{{ $data->gratuity_name ?? 'Gratuity Fee' }}</span>
                                                        <span>$0.00</span></div>
                                                @else
                                                    <div class="default-gratuity"></div>
                                                @endif

                                                <div style="font-size: 16px; font-weight: bold; display: none"
                                                    class="default-total"><span>Total</span> <span>$0.00</span></div>
                                            </div>

                                            <!-- Shareable Link Button -->
                                            <div class="mt-3" id="shareLinkContainer" style="display:none;">
                                                <button type="button" id="generateShareLink" style="background:#fff;color:#111;border:2px solid #0b0b0b;padding:8px 12px;border-radius:2px;font-size:12px;font-weight:700;">Generate
                                                    Shareable Link</button>
                                                <div style="position: relative;">
                                                    <input type="text" id="shareableLink" readonly
                                                        style="width:100%;margin-top:8px;display:none;padding-right:40px;background:#fff;color:#111;border:2px solid #0b0b0b;border-radius:2px;"
  required />
                                                    <div id="copyTooltip" style="position: absolute; top: -35px; right: 0; background: #ffffff; color: #111111; padding: 8px 12px; border-radius: 2px; border: 2px solid #0b0b0b; font-size: 12px; display: none; white-space: nowrap; z-index: 1000;">
                                                        Link copied!
                                                    </div>
                                                </div>
                                                <div id="shareActions" style="display:none;gap:8px;flex-wrap:wrap;margin-top:8px;">
                                                    <button type="button" class="checkout-share-btn" data-share="email" style="background:#ffffff;color:#111111;border:2px solid #0b0b0b;padding:6px 10px;border-radius:2px;font-size:12px;">Email</button>
                                                    <button type="button" class="checkout-share-btn" data-share="whatsapp" style="background:#ffffff;color:#111111;border:2px solid #0b0b0b;padding:6px 10px;border-radius:2px;font-size:12px;">WhatsApp</button>
                                                    <button type="button" class="checkout-share-btn" data-share="facebook" style="background:#ffffff;color:#111111;border:2px solid #0b0b0b;padding:6px 10px;border-radius:2px;font-size:12px;">Facebook</button>
                                                    <button type="button" class="checkout-share-btn" data-share="copy" style="background:#ffffff;color:#111111;border:2px solid #0b0b0b;padding:6px 10px;border-radius:2px;font-size:12px;">Copy</button>
                                                </div>
                                            </div>

                                            <div class="default-deposit" class="default-deposit-clean"><span>Total</span><span>$0.00</span></div>
                                            @if ($data->refundable_fee > 0)
                                                <div style="font-size: 16px; font-weight: 700; color: {{ $brandSecondary }} !important;"
                                                    class="vip-price default-refundable">
                                                    {{ $data->refundable_name ?? 'Non Refundable Processing Fees' }}:
                                                    <span class="refundable-amount">$0.00</span><span class="pay-now-tag">(Pay Now)</span>
                                                </div>
                                                <div style="font-size: 16px; font-weight: 700; color: {{ $brandSecondary }} !important;"
                                                    class="vip-price default-due">DUE ON ARRIVAL: <span class="due-amount">$0.00</span>
                                                </div>
                                            @endif
                                            {{-- @if ($data->sales_tax_name == 0)
                                                <div style="font-size: 10px; font-weight: 700; color: {{ $brandSecondary }} !important;"
                                                    class="vip-price">
                                                    <span>*No sales tax applied. Services sold are
                                                        not subject to sales tax under Nevada law. Please consult a tax
                                                        advisor for your local region if applicable.</span>
                                                    </div>
                                            @endif --}}
                                        </div>
                                        <div class="cv-promo-box dynamic-price" style="display: none;">
                                            <label class="cv-promo-label">{{ $data->promo_code_name ?: 'Have a promo code?' }}</label>
                                            <div class="cv-promo-input-group">
                                                <input type="text" id="promo_code" class="cv-promo-input" placeholder="Enter promo code" />
                                                <button type="button" class="cv-promo-btn" id="applyPromoBtn">Apply</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step Progress Indicator -->
                                    <ul class="checkout-steps" id="checkout-steps">
                                        <li class="step active" id="step-1">
                                            <div class="step-number">1</div>
                                            <p class="step-title">Package Details</p>
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

                                    <div style="margin: 14px 0;" class="cv-info-notice">
                                        <i class="fas fa-info-circle"></i>
                                        <span>This experience is fulfilled by the venue. Entry is subject to venue rules including minimum age requirements (21+), ID verification and dress code.</span>
                                    </div>

                                    <form action="{{ route('checkout.store', ['slug' => $data->slug]) }}"
                                        id="payment-form" method="post">
                                        @csrf
                                        

                                        
                                        <!-- Step 1: Package Holder Info -->
                                        <section class="checkout-section holder-info mt-4"
                                            id="section-1" style="width: 100%;">
                                            <div class="">
                                                <div class="row">

                                                    <div class="col-md-12">

                                                        <h2 style="margin-bottom: 35px;">Personal details <span
                                                                style="font-size: 1rem;"> Is this package being purchased for someone else? If so enter their legal name here (must present ID upon entry): </span></h2>

                                                        <!-- Left: Form Fields -->
                                                        <div class="form-left">

                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="firstName">First Name</label>
                                                                    <input type="text" id="firstName"
                                                                        name="package_first_name"
                                                                        placeholder="First Name" required />
                                                                </div>
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="lastName">Last Name</label>
                                                                    <input type="text" id="lastName"
                                                                        name="package_last_name"
                                                                        placeholder="Last Name" required />
                                                                </div>
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="phone">Phone Number</label>
                                                                    <input type="tel" id="package_phone"
                                                                        name="package_phone"
                                                                        placeholder="(555) 123-4567" required />
                                                                    <div class="phone-note" style="font-size: 0.75rem; color: rgba(255,255,255,0.6); margin-top: 4px;">Phone formatting may vary by country. International SMS delivery is not guaranteed.</div>
                                                                </div>
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="email">Email</label>
                                                                    <input type="email" id="email"
                                                                        name="package_email"
                                                                        placeholder="sample@sample.com" required />
                                                                    <div class="email-note" style="font-size: 0.75rem; color: #64748b; margin-top: 4px;">Your booking confirmation will be sent to this email. Please make sure it’s correct.</div>
                                                                </div>
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 100%;">
                                                                    <label for="dob-month">Date of Birth <span class="text-danger">*</span></label>
                                                                    <div class="form-row" style="display: flex; gap: 10px; width: 100%;">
                                                                        <select id="package-dob-month"
                                                                            name="package_month" class="form-select cv-dob-select"
                                                                            required></select>
                                                                        <select id="package-dob-day"
                                                                            name="package_day" class="form-select cv-dob-select"
                                                                            required></select>
                                                                        <select id="package-dob-year"
                                                                            name="package_year" class="form-select cv-dob-select"
                                                                            required></select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="note">Booking Note</label>
                                                                <textarea id="note" name="package_note" placeholder="Your occasion or special request?"></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="host">Host / Promoter Referral</label>
                                                                <input id="host" name="host_name"
                                                                    placeholder="Enter host/promoter name or referral code (optional)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Step Navigation -->
                                            <div class="step-navigation">
                                                <button type="button" class="btn-next" id="next-to-transport">Next:
                                                    Transportation Details <i class="fas fa-arrow-right" style="margin-left: 6px;"></i></button>
                                            </div>
                                        </section>

                                        <!-- Step 2: Transportation -->
                                        <section class="checkout-section transport mt-4" id="section-2"
                                            style="display: none; width: 100%;">

                                            <!-- Transportation confirmation checkbox -->
                                            <div class="checkbox-container transportaiton" id="transport-confirmation"
                                                style="display:none">
                                                <label>
                                                    <input type="checkbox" id="transportation_part"  required />
                                                    {{ $data->transportation_confirmation_text ?? 'I confirm I am arriving in a personal vehicle or approved venue transportation. I am not arriving via Uber, Lyft, taxi, limousine, ride-share, or any other third-party transportation service.' }}
                                                </label>
                                                <div class="step-navigation" style="margin-top: 20px;">
                                                    <button type="button" class="btn-prev"
                                                        id="prev-to-package">Previous: Package Details</button>
                                                    <button type="button" class="btn-next"
                                                        id="next-to-payment-from-confirm">Next: Payment
                                                        Details</button>
                                                </div>
                                            </div>

                                            <!-- Transportation form -->
                                            <div class="non-transportaiton" id="transport-form"
                                                style="display: none;">
                                                <div class="">
                                                    <div class="row">

                                                        <div class="col-md-12">

                                                            <h2 id="transport-section-title" style="margin-bottom: 8px;">Arrival Time</h2>
                                                            <div id="transportation-hours-range" style="display: none; margin-bottom: 24px; font-size: 16px; font-weight: 700; color: rgba(255,255,255,0.92);"></div>

                                                            <!-- Left: Form Fields -->
                                                            <div class="form-left">
                                                                <div id="transportation-details-fields">

                                                                <div class="form-row">
                                                                    <div class="form-group" style="width: 100%;">
                                                                        <label for="Pick-up-time">Pick-up Time</label>
                                                                        <small style="display:block;margin-top:4px;margin-bottom:8px;font-size:12px;line-height:1.4;color:#4b5563;font-weight:600;">Reservations must be made at least 15 minutes in advance. Reservation times are available in 5-minute intervals.</small>
                                                                        <div class="pickup-time-wrap">
                                                                            <i class="fas fa-clock pickup-time-icon"></i>
                                                                            <input name="transportation_pickup_time" type="text" readonly
                                                                                id="Pick-up-time"
                                                                                class="form-control"
                                                                                placeholder="Select pick-up time" required />
                                                                        </div>
                                                                        <div id="pickup-hours-badge" class="schedule-hours-badge" style="display: none; margin-top: 12px;"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-row" style="margin-top: 14px;">
                                                                    <div class="form-group" style="width: 100%;">
                                                                        <label for="address">Pick-up Location</label>
                                                                        <input type="text"
                                                                            name="transportation_address"
                                                                            id="address" placeholder="Enter pick-up address" required />
                                                                    </div>
                                                                </div>

                                                                <div class="form-row" style="display:none !important;" aria-hidden="true">
                                                                    <div class="form-group" style="width: 100%;">
                                                                        <label for="phone">Contact Phone Number or
                                                                            WhatsApp</label>
                                                                        <input type="tel"
                                                                            name="transportation_phone" id="phone"
                                                                            placeholder="For driver/dispatch to coordinate pickup"  required />
                                                                    </div>

                                                                </div>

                                                                <div class="form-row" style="display:none !important;" aria-hidden="true">
                                                                    <div class="num-guest"
                                                                        style="width: 100%; display: flex;">
                                                                        <label for="">Number of
                                                                            Guest(s)</label>

                                                                        <input type="number" class="form-control"
                                                                            name="transportation_guest" value="0" min="0"
                                                                            style="width: 120px; max-width: 120px; color: #fff;" required />



                                                                    </div>
                                                                </div>

                                                                <div class="form-group" style="display:none !important;" aria-hidden="true">
                                                                    <label for="note">Pickup Note</label>
                                                                    <textarea name="transportation_note" id="note" placeholder="If any"></textarea>
                                                                </div>
                                                                </div>

                                                                <div class="form-row" id="transportation-arrival-time-field" style="display:none !important; margin-top: 14px;">
                                                                    <div class="form-group" style="width: 100%;">
                                                                        <label for="Arrival-time">Time of Arrival</label>
                                                                        <div class="pickup-time-wrap">
                                                                            <i class="fas fa-clock pickup-time-icon"></i>
                                                                            <input name="transportation_arrival_time" type="text" readonly
                                                                                id="Arrival-time"
                                                                                class="form-control"
                                                                                placeholder="Select time of arrival" />
                                                                        </div>
                                                                        @if(($data->show_arrival_time_verbiage ?? 1) == 1)
                                                                            <small style="display:block;margin-top:6px;font-size:12px;line-height:1.4;color:#4b5563;font-weight:600;">Required when self-driving or when package transportation is not included.</small>
                                                                        @endif
                                                                        <div id="arrival-hours-badge" class="schedule-hours-badge" style="display: none; margin-top: 12px;"></div>
                                                                    </div>
                                                                </div>

                                                                <div class="checkbox-container transportaiton" id="transportation-self-drive-wrap" style="margin-top: 20px;">
                                                                    <label>
                                                                        <input type="checkbox" id="transportation_self_drive_ack" name="transportation_self_drive_ack" value="1" />
                                                                        I do not need the complimentary transportation included with my package and will self-drive. My party will arrive by private vehicle. Uber, Lyft, taxis, limousines, and ride-sharing services are not permitted.
                                                                    </label>
                                                                </div>

                                                                <div id="transportation-notice-wrap" class="checkbox-container transportaiton" style="margin-top: 14px;">
                                                                    <div style="display:flex; align-items:flex-start; gap:10px; color:#0b0b0b; font-size:14px; line-height:1.55;">
                                                                        <i class="fas fa-triangle-exclamation" style="color:#d97706; font-size:16px; margin-top:2px; flex-shrink:0;"></i>
                                                                        <span><strong style="color:#0b0b0b; font-weight:800;">Transportation Notice:</strong> Transportation is subject to availability. Requests made shortly before your desired pickup time may not be able to be accommodated. Please allow a reasonable amount of advance notice so we have time to coordinate a driver. While we will always do our best to assist, last-minute transportation cannot be guaranteed.</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Step Navigation -->
                                                <div class="step-navigation">
                                                    <button type="button" class="btn-prev"
                                                        id="prev-to-package-from-form">Previous: Package
                                                        Details</button>
                                                    <button type="button" class="btn-next"
                                                        id="next-to-payment">Next: Payment Details</button>
                                                </div>
                                            </div>
                                        </section>

                                        <input type="hidden" name="addons" id="addons">

                                        <input type="hidden" name="cart_items" id="cart_items">

                                        <input type="hidden" name="package_id" id="package_id">

                                        <input type="hidden" name="total" id="subtotal">

                                        <input type="hidden" name="payment_total" class="payment_total">

                                        <input type="hidden" name="commission_base_amount" id="commission_base_amount">

                                        <input type="hidden" name="website_id" value="{{ $data->id }}">

                                        <input type="hidden" name="affiliate_slug" value="{{ $affiliateReferral->slug ?? '' }}">

                                        <input type="hidden" name="package_number_of_guest"
                                            class="package_number_of_guest" value="2">

                                        <!-- Step 3: Payment Information -->
                                        <section class="checkout-section payment-info dynamic-price mt-4"
                                            id="section-3" style="display: none;">
                                            <div class="">
                                                <div class="row">

                                                    <div class="col-md-12">
                                                        <h2 style="margin-bottom: 35px;">Payment</h2>

                                                        <!-- Left: Form Fields -->
                                                        <div class="form-left">

                                                            <button type="button" class="same-as-info">Same as package holder
                                                                information</button>

                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="firstName">First Name</label>
                                                                    <input name="payment_first_name" type="text"
                                                                        id="firstName" placeholder="" required />
                                                                </div>
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="lastName">Last Name</label>
                                                                    <input name="payment_last_name" type="text"
                                                                        id="lastName" placeholder="" required />
                                                                </div>
                                                            </div>

                                                            <!-- Hidden fields for phone, email, and DOB - will be auto-populated from package holder info -->
                                                            <input type="hidden" name="payment_phone"
                                                                id="hidden_payment_phone"  required />
                                                            <input type="hidden" name="payment_email"
                                                                id="hidden_payment_email"  required />
                                                            <input type="hidden" name="payment_month"
                                                                id="hidden_payment_month"  required />
                                                            <input type="hidden" name="payment_day"
                                                                id="hidden_payment_day"  required />
                                                            <input type="hidden" name="payment_year"
                                                                id="hidden_payment_year"  required />

                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 100%;">
                                                                    <label for="bill-add">Address</label>
                                                                    <input name="payment_address" type="text"
                                                                        id="bill-add" placeholder="" required />
                                                                </div>
                                                            </div>
                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="country">Country</label>
                                                                    <select id="country" name="payment_country"
                                                                        class="form-select" required></select>
                                                                </div>
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="st-pv">State/ Province</label>
                                                                    <select name="payment_state" id="st-pv"
                                                                        class="form-select" required>
                                                                        <option value="null" selected disabled>Select
                                                                            State/Province</option>
                                                                        <!-- Options will be loaded dynamically -->
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="city">City</label>
                                                                    <input type="text" name="payment_city"
                                                                        id="city" placeholder="" required />
                                                                </div>
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="zip">Zip/Postal Code</label>
                                                                    <input type="text" name="payment_zip_code"
                                                                        id="zip" placeholder="" required />
                                                                </div>
                                                            </div>


                                                            @php
                                                                $stockPaymentLogoMap = [
                                                                    'visa' => ['name' => 'Visa', 'logo' => 'https://img.icons8.com/color/48/000000/visa.png'],
                                                                    'mastercard' => ['name' => 'Mastercard', 'logo' => 'https://img.icons8.com/color/48/000000/mastercard-logo.png'],
                                                                    'amex' => ['name' => 'Amex', 'logo' => 'https://img.icons8.com/color/48/000000/amex.png'],
                                                                    'google_pay' => ['name' => 'Google Pay', 'logo' => 'https://img.icons8.com/color/48/000000/google-pay-india.png'],
                                                                    'apple_pay' => ['name' => 'Apple Pay', 'logo' => 'https://img.icons8.com/color/48/000000/apple-pay.png'],
                                                                ];

                                                                $paymentLogosToRender = $data->paymentLogos->map(function ($logo) use ($stockPaymentLogoMap) {
                                                                    $logoKey = strtolower(trim((string) $logo->logo));

                                                                    if (isset($stockPaymentLogoMap[$logoKey])) {
                                                                        return [
                                                                            'src' => $stockPaymentLogoMap[$logoKey]['logo'],
                                                                            'name' => $stockPaymentLogoMap[$logoKey]['name'],
                                                                        ];
                                                                    }

                                                                    if ($logoKey === '') {
                                                                        return null;
                                                                    }

                                                                    if (str_starts_with($logoKey, 'http://') || str_starts_with($logoKey, 'https://')) {
                                                                        return [
                                                                            'src' => $logoKey,
                                                                            'name' => $logo->name,
                                                                        ];
                                                                    }

                                                                    return [
                                                                        'src' => asset('uploads/' . $logo->logo),
                                                                        'name' => $logo->name,
                                                                    ];
                                                                })->filter()->values();

                                                                if ($paymentLogosToRender->isEmpty()) {
                                                                    $paymentLogosToRender = collect($stockPaymentLogoMap)->map(fn ($method) => [
                                                                        'src' => $method['logo'],
                                                                        'name' => $method['name'],
                                                                    ])->values();
                                                                }
                                                            @endphp
                                                            <div id="checkout-card-fields">
                                                            @if ($data->payment_method == 'authorize')
                                                                <div class="form-row">
                                                                    <div class="form-group" style="width: 100%;">
                                                                        <!-- Payment method logos start -->
                                                                        <div style="margin-bottom: 10px;">
                                                                            @foreach($paymentLogosToRender as $logo)
                                                                                <img src="{{ $logo['src'] }}"
                                                                                    alt="{{ $logo['name'] }}"
                                                                                    style="height:32px; margin-right:4px;">
                                                                            @endforeach
                                                                        </div>
                                                                        <label for="card_number">Card Number</label>
                                                                        <input type="tel" name="card_number"
                                                                            id="card_number" placeholder="" inputmode="numeric" autocomplete="cc-number"
                                                                            maxlength="19" required data-card-required="1" />
                                                                    </div>

                                                                </div>
                                                                <div class="form-row">
                                                                    <div class="form-group" style="width: 25%;">
                                                                        <label>Month</label>
                                                                        <input type="tel" maxlength="2"
                                                                            name="card_month" id="city"
                                                                            placeholder="(MM)" required data-card-required="1" />
                                                                    </div>
                                                                    <div class="form-group" style="width: 25%;">
                                                                        <label>Year</label>
                                                                        <input type="tel" maxlength="2"
                                                                            name="card_year" placeholder="(YY)"
                                                                            required data-card-required="1" />
                                                                    </div>
                                                                    <div class="form-group" style="width: 25%;">
                                                                        <label>CVV</label>
                                                                        <input type="tel" name="card_cvv"
                                                                            id="cvv" placeholder="CVV"
                                                                            required data-card-required="1" />
                                                                    </div>
                                                                </div>
                                                                @else
                                                                    <div class="form-row">
                                                                        @foreach($paymentLogosToRender as $logo)
                                                                            <img src="{{ $logo['src'] }}"
                                                                                alt="{{ $logo['name'] }}"
                                                                                style="height:32px; margin-right:4px;">
                                                                        @endforeach
                                                                    </div>
                                                                    <div style="margin-bottom: 10px;">
                                                                        <div class="form-group" style="width: 100%;"
                                                                            id="card_number">
                                                                            <label for="card_number">Card
                                                                                Number</label>
                                                                            {{-- <input type="tel" name="card_number" 
                                                                            placeholder="" required /> --}}
                                                                        </div>

                                                                    </div>
                                                                    <div class="form-row">
                                                                        <div class="form-group" style="width: 50%;"
                                                                            id="expiration_date">
                                                                            <label>Expiry Date</label>
                                                                            {{-- <input type="text"  name="expiration_date"
                                                                                 placeholder="MM/YY" required /> --}}
                                                                        </div>
                                                                        <div class="form-group" style="width: 50%;"
                                                                            id="cvv">
                                                                            <label>CVV</label>
                                                                            {{-- <input type="tel" name="card_cvv" 
                                                                            placeholder="CVV" required /> --}}
                                                                        </div>
                                                                    </div>
                                                            @endif
                                                        </div>
                                                        <div id="zero-total-payment-note" style="display: none; margin: 0 0 18px; padding: 14px 16px; border: 1px solid rgba(34, 197, 94, 0.35); border-radius: 12px; background: rgba(34, 197, 94, 0.08); color: #d1fae5; font-size: 13px; line-height: 1.5;">
                                                            This order total is $0.00. No card information is required to complete checkout.
                                                        </div>
                                                        <div id="card-errors" style="margin-bottom: 14px; color: #ff9b9b; font-size: 13px;"></div>
                                                        <div class="checkbox-container payment-consent-group" style="margin-top: 1.5rem; display: none;">
                                                            <label class="consent-label">
                                                                <input type="checkbox" id="businessExpenseCheckbox" />
                                                                <span>This purchase is for business purposes</span>
                                                            </label>
                                                        </div>
                                                        <div id="businessFields"
                                                            style="display: none; margin-top: 1rem;">
                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="business_company">Company Name</label>
                                                                    <input type="text" name="business_company"
                                                                        id="business_company"
                                                                        placeholder="Company Name"  required />
                                                                </div>
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="business_vat">VAT or Tax ID</label>
                                                                    <input type="text" name="business_vat"
                                                                        id="business_vat"
                                                                        placeholder="VAT or Tax ID"  required />
                                                                </div>
                                                            </div>
                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 100%;">
                                                                    <label for="business_address">Business
                                                                        Address</label>
                                                                    <input type="text" name="business_address"
                                                                        id="business_address"
                                                                        placeholder="Business Address"  required />
                                                                </div>
                                                            </div>
                                                        </div>

                                                            <div class="checkbox-container payment-consent-group" id="payment-consent-group">
                                                            @if($data->show_sms_consent ?? true)
                                                            <label class="consent-label">
                                                                <input type="checkbox" id="smsConsent" required />
                                                                <span>{{ $data->sms_consent_text }}</span>
                                                            </label>
                                                            @endif

                                                            @if($data->show_terms_consent ?? true)
                                                            <label class="consent-label" style="margin-top: 1.4rem;">
                                                                <input type="checkbox" id="termsConsent" required />
                                                                <span>{!! $data->terms_consent_text_formatted !!}</span>
                                                            </label>
                                                            @endif
                                                        </div>

                                                        <input type="hidden" class="package_use_date"
                                                            name="package_use_date"
                                                            value="{{ \Carbon\Carbon::now($data->resolved_timezone)->format('Y-m-d') }}">
                                                        <input type="hidden" class="promo_code" name="promo_code">
                                                        <input type="hidden" class="discounted_amount"
                                                            name="discounted_amount">

                                                        <!-- Step Navigation -->
                                                        <div class="step-navigation">
                                                            <button type="button" class="btn-prev"
                                                                id="prev-to-transport">Previous:
                                                                Transportation</button>
                                                            <button style="margin-top: 0px !important;" class="submit-btn" id="submitBtn"
                                                                type="submit">Complete Purchase</button>
                                                        </div>

                                                    </div>

                                                </div>






                                            </div>
                                </div>
                    </section>
                    </form>

                </div>
            </div>
            </div>


            </section>

            <input type="hidden" name="type" value="package">

            </div>{{-- end .package --}}
            </div>{{-- end cv-main-col --}}

            {{-- RIGHT: Order Summary Sidebar --}}
            <aside class="cv-sidebar" id="cv-order-sidebar">
                <div class="cv-sidebar-header">
                    <span>ORDER SUMMARY</span>
                    {{-- <button type="button" class="cv-sidebar-edit-btn" id="cv-edit-cart" style="display:none;"><i class="fas fa-pen"></i> Edit Cart</button> --}}
                </div>

                                {{-- Empty cart state placeholder --}}
                <div id="cv-sidebar-empty-placeholder" class="cv-sidebar-empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <p>No package selected yet</p>
                    <span>Select a package on the left to proceed</span>
                </div>

                {{-- Cart, pricing, promo will be moved here by JS --}}
                <div id="cv-sidebar-body">
                    {{-- JS will insert #cart-section, .pricing-shell, and #shareLinkContainer here --}}
                </div>

                {{-- Deposit box (always present, shown when selection active) --}}
                @php
                    $refundablePctTwo = (int) ($data->refundable_fee ?? 0);
                @endphp
                <div class="cv-deposit-box dynamic-price" id="cv-deposit-box" style="display:none;">
                    <div class="cv-deposit-content">
                        <div class="cv-deposit-top">
                            <div class="cv-deposit-label" data-tip="@if($refundablePctTwo > 0){{ $refundablePctTwo }}% of the total is collected today to secure your reservation. The balance is paid on arrival at the venue.@else You're paying the full amount today.@endif">@if($refundablePctTwo > 0)Due Today ({{ $refundablePctTwo }}% Deposit)@else{{ 'Due Today' }}@endif <span class="cv-info-icon">i</span></div>
                            <div class="cv-deposit-shield" data-tip="Secure checkout — your payment is protected by bank-level SSL encryption and never stored on this site." data-tip-right><i class="fas fa-shield-alt"></i></div>
                        </div>
                        <div class="cv-deposit-main" id="cv-deposit-display">$0.00</div>
                        <div class="cv-deposit-sub">Secure your reservation</div>
                        @if($refundablePctTwo > 0)
                            <div class="cv-deposit-due-row">
                                <span>Due on Arrival</span>
                                <span id="cv-due-on-arrival">$0.00</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- CTA buttons --}}
                <div id="cv-sidebar-cta-wrap" style="margin-top:14px;">
                    <button type="button" class="cv-continue-shopping-btn" id="cv-continue-shopping" onclick="var el = document.getElementById('cv-checkout-steps'); if(el) { el.scrollIntoView({behavior:'smooth'}); }">
                        Continue Shopping
                    </button>
                </div>

                {{-- Trust badges --}}
                <div class="cv-trust-list">
                    <div class="cv-trust-item">
                        <i class="fas fa-calendar-check"></i>
                        <div><strong>Free Cancellation</strong><span>Up to 24 hours before your booking</span></div>
                    </div>
                    <div class="cv-trust-item">
                        <i class="fas fa-headset"></i>
                        <div><strong>Instant Support</strong><span>Get help anytime, anywhere</span></div>
                    </div>
                    <div class="cv-trust-item">
                        <i class="fas fa-lock"></i>
                        <div><strong>Secure Payment</strong><span>100% safe &amp; encrypted</span></div>
                    </div>
                </div>

                <p class="cv-cta-terms">
                    By continuing, you agree to our
                    <a href="{{ $data->terms }}" target="_blank">Terms of Service</a> and
                    <a href="{{ $data->privacy_policy ?? $data->terms }}" target="_blank">Privacy Policy</a>
                </p>
            </aside>

            </div>{{-- end cv-checkout-body --}}

            {{-- Location info now lives in the hero (.cv-hero-location). --}}

            @if (empty($isSinglePackageCheckout))
            <section class="cv-events-shell">
                <div class="container py-5 events-section-container">
                    <div class="event-header">
                        <h2>Upcoming Events</h2>
                        <div class="event-filters">
                            <button type="button" class="event-filter" data-filter="week">This Week</button>
                            <button type="button" class="event-filter" data-filter="month">This Month</button>
                            <button type="button" class="event-filter" data-filter="year">This Year</button>
                        </div>
                    </div>
                    <div class="row g-4" id="events-list">
                        @php
                            $todayPacific = \Carbon\Carbon::now($data->resolved_timezone)->toDateString();
                        @endphp
                        @forelse ($data->events as $item)
                            @php
                                $eventStartDate = $item->start_date ?? $item->date;
                                $eventEndDate = $item->end_date ?? $eventStartDate;
                            @endphp
                            @if (!$item->is_archieved && $eventEndDate && \Carbon\Carbon::parse($eventEndDate)->toDateString() >= $todayPacific)
                                <div class="col-md-4 event-card-item"
                                    data-date="{{ \Carbon\Carbon::parse($eventStartDate)->format('Y-m-d') }}">
                                    <a href="/{{ $data->slug }}?event_name={{ $item->name }}" class="event-card">
                                        <div class="card">
                                            <img src="{{ asset('uploads/' . $item->image) }}" alt="{{ $item->name }}">
                                            <div class="d-flex">
                                                <div class="event-day">{{ \Carbon\Carbon::parse($eventStartDate)->format('l') }}</div>
                                                <div class="event-dates">{{ \Carbon\Carbon::parse($eventStartDate)->format('M') }}<span>{{ \Carbon\Carbon::parse($eventStartDate)->format('d') }}</span></div>
                                            </div>
                                            <div class="event-location">{{ $item->name }}</div>
                                            @if($eventEndDate && $eventStartDate !== $eventEndDate)
                                                <div class="event-location">
                                                    {{ \Carbon\Carbon::parse($eventStartDate)->format('M d') }} - {{ \Carbon\Carbon::parse($eventEndDate)->format('M d') }}
                                                </div>
                                            @endif
                                                @if(($item->show_time_range ?? true) && !empty($item->time))
                                                <div class="event-location"><i class="fas fa-clock"></i>{{ $item->time }}</div>
                                            @endif
                                            <div class="event-location"><i class="fas fa-map-marker-alt"></i>{{ $data->location }}</div>
                                            @if (!is_null($item->remaining_attendee_capacity))
                                                <div class="event-capacity-chip{{ !empty($item->is_sold_out) ? ' sold-out' : '' }}">
                                                    {{ !empty($item->is_sold_out) ? 'Sold Out' : $item->remaining_attendee_capacity . ' Spots Left' }}
                                                </div>
                                            @else
                                                <div class="event-location">Reserve</div>
                                            @endif
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @empty
                            <div class="col-12" style="opacity:.75;">No upcoming events available.</div>
                        @endforelse
                    </div>
                </div>


            </section>
            @endif

            <div class="modal fade" id="infoTooltipModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Modal title</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Modal body text goes here.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="addonSelectionModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable addon-modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addonSelectionModalTitle">Select Add-ons</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="addonSelectionModalBody"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="addonModalNoAddonsBtn">No Add-ons</button>
                            <button type="button" class="btn" id="addonModalConfirmBtn">Confirm & Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($checkoutPopup) && $checkoutPopup)
                <div class="modal fade" id="checkoutPopupModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header {{ empty($checkoutPopup->title) ? 'justify-content-end' : '' }}">
                                @if(!empty($checkoutPopup->title))
                                    <h5 class="modal-title">{{ $checkoutPopup->title }}</h5>
                                @endif
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if($checkoutPopup->image_path)
                                    <img src="{{ asset('uploads/' . $checkoutPopup->image_path) }}" alt="Popup" style="display:block;max-width:100%;width:auto;max-height:70vh;height:auto;object-fit:contain;border-radius:10px;margin:0 auto 14px;background:#0b1222;">
                                @endif
                                @if(!empty($checkoutPopup->message))
                                    <div style="line-height:1.6;white-space:normal;">{!! nl2br(e($checkoutPopup->message)) !!}</div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                @if(!empty($checkoutPopup->button_text) && !empty($checkoutPopup->button_url))
                                    <a href="{{ $checkoutPopup->button_url }}" target="_blank" rel="noopener" class="btn popup-cta">{{ $checkoutPopup->button_text }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            </div>


        </main>
        
            
        <div id="cv-cart-toast" role="status" aria-live="polite" aria-atomic="true">
            <span class="cv-toast-icon"><i class="fas fa-check"></i></span>
            <span class="cv-toast-body">
                <span class="cv-toast-title">Added to cart!</span>
                <span class="cv-toast-sub" id="cv-cart-toast-sub"></span>
            </span>
            <button type="button" class="cv-toast-close" aria-label="Close" onclick="window.hideCartToast && window.hideCartToast();">&times;</button>
        </div>
        <div id="checkout-processing-overlay" aria-hidden="true" role="status" aria-live="polite">
            <div class="checkout-processing-card">
                <div class="checkout-processing-spinner" aria-hidden="true"></div>
                <p class="checkout-processing-title">Processing Your Purchase</p>
                <p class="checkout-processing-copy">Please wait while we securely complete your transaction.</p>
            </div>
        </div>
                <footer class="aff-footer">
            <div class="container">
                <div class="cv-footer-inner">
                    <div class="cv-footer-brand">
                        <img src="{{ asset('images/logo.png') }}" alt="CartVIP" class="cv-footer-logo" onerror="this.style.display='none';">
                        <p class="cv-footer-tagline">Your premium experiences, simplified.</p>
                    </div>
                    <div class="cv-footer-links">
                        <a href="https://cartvip.com">Home</a>
                        <a href="https://cartvip.com">Shop</a>
                        <a href="https://cartvip.com">Packages</a>
                        <a href="https://cartvip.com">Events</a>
                        <a href="https://cartvip.com">Contact</a>
                    </div>
                    <div class="cv-footer-socials">
                        <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://youtube.com" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="cv-footer-bar">
                    <span class="cv-footer-bar-copy">&copy; {{ date('Y') }} CartVIP.com &middot; All rights reserved</span>
                    <div class="cv-footer-bar-links">
                        <a href="https://cartvip.com/page/privacy-policy" target="_blank">Privacy Policy</a>
                        <a href="https://cartvip.com/page/terms-of-service" target="_blank">Terms of Service</a>
                        <a href="https://cartvip.com/page/contact" target="_blank">Help</a>
                    </div>
                </div>
            </div>
        </footer>
        <script src="scripts/main.js"></script>
        <script>
            // Guest counter - robust override to fix double-fire / missed-click bug.
            // main.js's updateDisplay() calls checkEligibility() which is undefined and
            // throws mid-function, leaving state inconsistent. This replaces the global
            // increments/decrements with safe versions and uses a single delegated
            // click handler with a click guard to prevent double firing.
            (function () {
                var guestCounts = { men: 0, women: 0 };
                var lastClickAt = 0;

                function readDom() {
                    var menEl = document.getElementById('menCount');
                    var womenEl = document.getElementById('womenCount');
                    if (menEl) guestCounts.men = parseInt(menEl.textContent, 10) || 0;
                    if (womenEl) guestCounts.women = parseInt(womenEl.textContent, 10) || 0;
                }
                function writeDom() {
                    var menEl = document.getElementById('menCount');
                    var womenEl = document.getElementById('womenCount');
                    var totalEl = document.getElementById('totalCount');
                    var menHidden = document.getElementById('men_count');
                    var womenHidden = document.getElementById('women_count');
                    if (menEl) menEl.textContent = guestCounts.men;
                    if (womenEl) womenEl.textContent = guestCounts.women;
                    if (totalEl) totalEl.textContent = guestCounts.men + guestCounts.women;
                    if (menHidden) menHidden.value = guestCounts.men;
                    if (womenHidden) womenHidden.value = guestCounts.women;
                }
                window.increments = function (type) {
                    if (type !== 'men' && type !== 'women') return;
                    readDom();
                    guestCounts[type] += 1;
                    writeDom();
                };
                window.decrements = function (type) {
                    if (type !== 'men' && type !== 'women') return;
                    readDom();
                    if (guestCounts[type] > 0) guestCounts[type] -= 1;
                    writeDom();
                };

                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('.guest-qty-btn').forEach(function (btn) {
                        btn.removeAttribute('onclick');
                    });
                    readDom();
                    writeDom();
                });

                document.addEventListener('click', function (e) {
                    var btn = e.target.closest('.guest-qty-btn');
                    if (!btn) return;
                    e.preventDefault();
                    e.stopPropagation();
                    var now = Date.now();
                    if (now - lastClickAt < 200) return;
                    lastClickAt = now;
                    var type = btn.getAttribute('data-type');
                    var action = btn.getAttribute('data-action');
                    if (!type || !action) return;
                    if (action === 'inc') window.increments(type);
                    else if (action === 'dec') window.decrements(type);
                });
            })();

            // Reservation form validation: prevent submission without date and guests
            (function () {
                const submitBtn = document.getElementById('submitBtn_two');
                if (!submitBtn) return;

                const form = submitBtn.closest('form');
                if (!form) return;

                // Set form load time
                const formLoadTimeField = document.getElementById('form_load_time');
                if (formLoadTimeField) {
                    formLoadTimeField.value = Math.floor(Date.now() / 1000);
                }

                submitBtn.addEventListener('click', function (e) {
                    const reservationDate = document.getElementById('package_use_date');
                    const menCount = parseInt(document.getElementById('menCount')?.textContent || '0', 10);
                    const womenCount = parseInt(document.getElementById('womenCount')?.textContent || '0', 10);
                    const totalGuests = menCount + womenCount;

                    // Sync reservation date to hidden field BEFORE validation
                    if (reservationDate && reservationDate.value) {
                        const hiddenDateField = document.querySelector('input[name="package_use_date"]');
                        if (hiddenDateField) {
                            hiddenDateField.value = reservationDate.value;
                        }
                    }

                    let hasError = false;
                    let errorMessage = '';

                    // Check if reservation date is selected
                    if (!reservationDate || !reservationDate.value || reservationDate.value.trim() === '') {
                        hasError = true;
                        errorMessage = 'Please select a reservation date.';
                        if (reservationDate) {
                            reservationDate.classList.add('required-field');
                            reservationDate.setAttribute('aria-invalid', 'true');
                        }
                        const dateError = document.getElementById('package_use_date_error');
                        if (dateError) {
                            dateError.textContent = errorMessage;
                            dateError.style.display = 'block';
                        }
                    } else {
                        if (reservationDate) {
                            reservationDate.classList.remove('required-field');
                            reservationDate.removeAttribute('aria-invalid');
                        }
                        const dateError = document.getElementById('package_use_date_error');
                        if (dateError) {
                            dateError.style.display = 'none';
                        }
                    }

                    // Check if total guests is greater than 0
                    if (totalGuests === 0) {
                        hasError = true;
                        errorMessage = errorMessage ? 'Please select a reservation date and add at least one guest.' : 'Please add at least one guest (men or women).';
                    }

                    // Check SMS consent checkbox
                    const smsConsent = document.getElementById('smsConsent_two');
                    if (!smsConsent || !smsConsent.checked) {
                        hasError = true;
                        errorMessage = 'Please agree to receive SMS communications regarding your reservation, transportation updates, VIP services, and related notifications.';
                    }

                    // Check terms consent checkbox
                    const termsConsent = document.getElementById('termsConsent_two');
                    if (!termsConsent || !termsConsent.checked) {
                        hasError = true;
                        errorMessage = 'Please accept the Terms of Service.';
                    }

                    // Require a valid country code selection on the reservation phone picker.
                    // The picker's code box is a searchable text input; block submit if the user typed
                    // search text without picking a country (or typed an invalid code).
                    var __ccFields = form.querySelectorAll('.country-code-field');
                    for (var __ci = 0; __ci < __ccFields.length; __ci++) {
                        var __cc = __ccFields[__ci];
                        if (__cc.offsetParent === null) continue;
                        var __ccWrap = __cc.closest('.country-code-input');
                        if (!__ccWrap) continue;
                        var __opts = __ccWrap.querySelectorAll('.country-option');
                        if (!__opts.length) continue; // fail-safe: nothing to validate against
                        var __ccVal = (__cc.value || '').trim();
                        var __ccOk = false;
                        for (var __oi = 0; __oi < __opts.length; __oi++) {
                            if ((__opts[__oi].getAttribute('data-flag') + ' ' + __opts[__oi].getAttribute('data-code')) === __ccVal) { __ccOk = true; break; }
                        }
                        if (!__ccOk) {
                            __cc.style.borderColor = '#ff6b6b';
                            hasError = true;
                            errorMessage = 'Please select a valid country code from the list (search and click your country, or type the full +code in the phone box).';
                            break;
                        }
                        __cc.style.borderColor = '';
                    }

                    if (hasError) {
                        e.preventDefault();
                        e.stopPropagation();
                        // Show error message instead of alert
                        const errorMsg = document.getElementById('validation-error-msg-reservation') || document.createElement('div');
                        if (!errorMsg.id) {
                            errorMsg.id = 'validation-error-msg-reservation';
                            errorMsg.style.cssText = 'color: #ff6b6b; padding: 12px; margin: 10px 0; font-weight: 600; text-align: center; background: rgba(255, 107, 107, 0.1); border-radius: 6px; border-left: 4px solid #ff6b6b;';
                            form.parentElement.insertBefore(errorMsg, form);
                        }
                        errorMsg.textContent = errorMessage;
                        errorMsg.style.display = 'block';
                        errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }

                    // Prevent default and handle submission with reCAPTCHA
                    e.preventDefault();

                    // Replace visible phone fields with E.164 values before submission
                    const phoneFieldsToSync = [
                        { visible: 'reservation_phone', e164: 'reservation_phone_e164' }
                    ];

                    phoneFieldsToSync.forEach(pair => {
                        const e164Field = form.querySelector(`input[name="${pair.e164}"]`);
                        const visibleField = form.querySelector(`input[name="${pair.visible}"]`);
                        if (e164Field && visibleField && e164Field.value) {
                            // Use E.164 format for submission
                            visibleField.value = e164Field.value;
                        }
                    });

                    // Get reCAPTCHA token before submitting
                    if (typeof window.executeRecaptcha === 'function') {
                        window.executeRecaptcha('reservation_submit').then(function(token) {
                            if (token) {
                                const tokenField = document.getElementById('recaptcha_token');
                                if (tokenField) {
                                    tokenField.value = token;
                                }
                            }
                            // Submit form after token is set
                            form.submit();
                        }).catch(function(error) {
                            console.warn('reCAPTCHA error:', error);
                            // Still submit if reCAPTCHA fails - server-side validation will handle
                            form.submit();
                        });
                    } else {
                        // reCAPTCHA not available - submit directly
                        console.warn('reCAPTCHA not loaded');
                        form.submit();
                    }
                });
            })();

            // Cart toast: show a notification when an item is added (helpful on mobile
            // where the cart sidebar is below the fold).
            (function () {
                var hideTimer = null;
                window.showToast = function (title, sub, iconClass) {
                    var toast = document.getElementById('cv-cart-toast');
                    if (!toast) return;
                    try {
                        document.querySelectorAll('.flatpickr-calendar.open').forEach(function(el) {
                            el.classList.remove('open');
                        });
                        if (document.activeElement && typeof document.activeElement.blur === 'function') {
                            document.activeElement.blur();
                        }
                    } catch(e){}
                    var titleEl = toast.querySelector('.cv-toast-title');
                    var subEl = document.getElementById('cv-cart-toast-sub');
                    var iconEl = toast.querySelector('.cv-toast-icon i');
                    if (titleEl) titleEl.textContent = title || 'Notice';
                    if (subEl) subEl.textContent = sub || '';
                    if (iconEl) iconEl.className = iconClass || 'fas fa-check';
                    toast.classList.add('is-visible');
                    if (hideTimer) clearTimeout(hideTimer);
                    hideTimer = setTimeout(function () { window.hideCartToast(); }, 4000);
                };
                window.showCartToast = function (packageName, guests) {
                    var qty = parseInt(guests, 10) || 1;
                    var label = qty + (qty === 1 ? ' guest' : ' guests');
                    window.showToast('Added to cart!', packageName ? (packageName + ' · ' + label) : label, 'fas fa-check');
                };
                window.hideCartToast = function () {
                    var toast = document.getElementById('cv-cart-toast');
                    if (!toast) return;
                    toast.classList.remove('is-visible');
                    if (hideTimer) { clearTimeout(hideTimer); hideTimer = null; }
                };
            })();

            // Inject inline info icons into Service Fee / Tax / Gratuity rows so the
            // row's ::after stays free for the custom hover tooltip.
            (function () {
                function inject() {
                    var rows = document.querySelectorAll(
                        '#cv-order-sidebar .pricing-shell .default-service-charge, ' +
                        '#cv-order-sidebar .pricing-shell .default-sales-tax, ' +
                        '#cv-order-sidebar .pricing-shell .default-gratuity'
                    );
                    rows.forEach(function (row) {
                        if (!row.hasAttribute('data-tip')) return;
                        if (row.querySelector('.cv-row-info-icon')) return;
                        var labelSpan = row.querySelector('span');
                        if (!labelSpan) return;
                        var icon = document.createElement('span');
                        icon.className = 'cv-row-info-icon';
                        icon.textContent = 'i';
                        icon.setAttribute('aria-hidden', 'true');
                        labelSpan.appendChild(icon);
                    });
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', inject);
                } else {
                    inject();
                }
                setTimeout(inject, 50);
                setTimeout(inject, 500);
            })();

            (function () {
                function initAddonModalScrollArrow() {
                    var modal = document.getElementById('addonSelectionModal');
                    if (!modal || modal.dataset.scrollArrowBound === '1') return;

                    var modalBody = modal.querySelector('#addonSelectionModalBody') || modal.querySelector('.modal-body');
                    var scrollButton = modal.querySelector('.addon-scroll-down-fab');
                    if (!modalBody || !scrollButton) return;

                    modal.dataset.scrollArrowBound = '1';

                    function updateArrowVisibility() {
                        var canScroll = (modalBody.scrollHeight - modalBody.clientHeight) > 8;
                        var atBottom = (modalBody.scrollTop + modalBody.clientHeight) >= (modalBody.scrollHeight - 6);
                        scrollButton.style.display = (!canScroll || atBottom) ? 'none' : 'flex';
                    }

                    scrollButton.addEventListener('click', function () {
                        var step = Math.max(modalBody.clientHeight * 0.8, 220);
                        modalBody.scrollBy({ top: step, behavior: 'smooth' });
                    });

                    modalBody.addEventListener('scroll', updateArrowVisibility, { passive: true });
                    modal.addEventListener('shown.bs.modal', function () {
                        window.requestAnimationFrame(updateArrowVisibility);
                        setTimeout(updateArrowVisibility, 120);
                    });
                    modal.addEventListener('hidden.bs.modal', function () {
                        scrollButton.style.display = 'none';
                    });

                    if (window.MutationObserver) {
                        var observer = new MutationObserver(updateArrowVisibility);
                        observer.observe(modalBody, { childList: true, subtree: true, characterData: true });
                    }

                    window.addEventListener('resize', updateArrowVisibility);
                    updateArrowVisibility();
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initAddonModalScrollArrow);
                } else {
                    initAddonModalScrollArrow();
                }
            })();

            // Open order-summary tips in the info modal on click (no hover tooltip).
            (function () {
                function escapeHtml(text) {
                    return String(text || '').replace(/[&<>"']/g, function (char) {
                        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char];
                    });
                }

                function resolveTitle(trigger) {
                    if (trigger.classList.contains('cv-deposit-label')) return 'Due Today';
                    if (trigger.classList.contains('cv-deposit-shield')) return 'Secure Checkout';

                    var directText = '';
                    Array.prototype.forEach.call(trigger.childNodes, function (node) {
                        if (node && node.nodeType === Node.TEXT_NODE) {
                            directText += node.textContent || '';
                        }
                    });
                    directText = String(directText || '').replace(/\s+/g, ' ').trim().replace(/:\s*$/, '').trim();
                    if (directText) return directText;

                    var spans = trigger.querySelectorAll('span');
                    for (var i = 0; i < spans.length; i++) {
                        var clone = spans[i].cloneNode(true);
                        if (clone.querySelectorAll) {
                            clone.querySelectorAll('.cv-row-info-icon').forEach(function (icon) {
                                icon.remove();
                            });
                        }

                        var text = String(clone.textContent || '').replace(/\s+/g, ' ').trim().replace(/:\s*$/, '').trim();
                        if (text && !/^\$/.test(text)) return text.replace(/\bi\s*$/i, '').trim();
                    }
                    return 'Details';
                }

                function openTipModal(trigger) {
                    var tip = String(trigger.getAttribute('data-tip') || '').trim();
                    if (!tip) return;

                    var modal = document.getElementById('infoTooltipModal');
                    if (!modal) return;

                    var modalTitle = modal.querySelector('.modal-title');
                    var modalBody = modal.querySelector('.modal-body');
                    if (modalTitle) modalTitle.textContent = resolveTitle(trigger);
                    if (modalBody) modalBody.innerHTML = '<p style="margin:0;">' + escapeHtml(tip) + '</p>';

                    if (window.bootstrap && window.bootstrap.Modal) {
                        window.bootstrap.Modal.getOrCreateInstance(modal).show();
                        return;
                    }
                    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                        window.jQuery(modal).modal('show');
                    }
                }

                document.addEventListener('click', function (event) {
                    var trigger = event.target.closest('#cv-order-sidebar [data-tip], #cv-deposit-box [data-tip]');
                    if (!trigger) return;
                    event.preventDefault();
                    event.stopPropagation();
                    openTipModal(trigger);
                });
            })();
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

        <script>
            function showCheckoutProcessingOverlay() {
                var overlay = document.getElementById('checkout-processing-overlay');
                if (!overlay) {
                    return;
                }

                overlay.classList.add('is-visible');
                overlay.setAttribute('aria-hidden', 'false');

                var submitButton = document.getElementById('submitBtn');
                if (submitButton) {
                    if (!submitButton.dataset.defaultText) {
                        submitButton.dataset.defaultText = submitButton.textContent;
                    }
                    submitButton.disabled = true;
                    submitButton.textContent = 'Processing...';
                }
            }

            function hideCheckoutProcessingOverlay() {
                var overlay = document.getElementById('checkout-processing-overlay');
                if (!overlay) {
                    return;
                }

                overlay.classList.remove('is-visible');
                overlay.setAttribute('aria-hidden', 'true');

                var submitButton = document.getElementById('submitBtn');
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = submitButton.dataset.defaultText || 'Complete Purchase';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                var form = document.getElementById('payment-form');
                if (!form) {
                    return;
                }

                form.addEventListener('submit', function(event) {
                    window.setTimeout(function() {
                        if (!event.defaultPrevented) {
                            showCheckoutProcessingOverlay();
                        }
                    }, 0);
                });
            });
        </script>

        <script>
            // --- Cart System --- Define immediately at top level
            // Initialize cart variables
            window.cart = [];
            window.cartCoupon = window.cartCoupon || null;
            
            // Ensure cart is always an array
            function ensureCartArray() {
                if (!Array.isArray(window.cart)) {
                    console.warn('window.cart was not an array, resetting');
                    window.cart = [];
                }
            }
            
            function formatCurrency(value) {
                return '$' + new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(Number(value) || 0);
            }

            window.isZeroTotalCheckout = function() {
                var totalField = document.querySelector('.payment_total');
                var total = parseFloat(totalField ? totalField.value : '0');
                return Number.isFinite(total) && Math.abs(total) < 0.00001;
            };

            window.updateCheckoutPaymentRequirement = function() {
                var form = document.getElementById('payment-form');
                var cardFieldsWrapper = document.getElementById('checkout-card-fields');
                var zeroTotalNote = document.getElementById('zero-total-payment-note');
                var cardErrors = document.getElementById('card-errors');
                var isFreeCheckout = window.isZeroTotalCheckout();

                if (form) {
                    form.dataset.zeroTotalCheckout = isFreeCheckout ? '1' : '0';
                }

                if (cardFieldsWrapper) {
                    cardFieldsWrapper.style.display = isFreeCheckout ? 'none' : '';
                }

                if (zeroTotalNote) {
                    zeroTotalNote.style.display = isFreeCheckout ? 'block' : 'none';
                }

                if (cardErrors && isFreeCheckout) {
                    cardErrors.textContent = '';
                }

                document.querySelectorAll('[data-card-required="1"]').forEach(function(field) {
                    field.required = !isFreeCheckout;
                    field.disabled = isFreeCheckout;

                    if (isFreeCheckout) {
                        field.value = '';
                        field.setCustomValidity('');
                    }
                });
            };

            function syncCheckoutCartFields() {
                let form = document.getElementById('payment-form');
                if (!form || !Array.isArray(window.cart) || !window.cart.length) {
                    return;
                }

                let cartField = form.querySelector('#cart_items');
                let packageField = form.querySelector('#package_id');
                let guestField = form.querySelector('.package_number_of_guest');
                let addonsField = form.querySelector('#addons');
                let firstItem = window.cart[0];
                let totalGuests = window.cart.reduce(function(sum, item) {
                    return sum + (parseInt(item.guests, 10) || 1);
                }, 0);
                let addonNames = window.cart.reduce(function(all, item) {
                    return all.concat(Array.isArray(item.addons) ? item.addons : []);
                }, []).map(function(addon) {
                    return addon.name + ' ($' + addon.price + ')';
                });

                if (cartField) {
                    cartField.value = JSON.stringify(window.cart);
                }
                if (packageField && firstItem) {
                    packageField.value = firstItem.packageId || packageField.value;
                }
                // guestField is now for Host Name (text field), not guest count
                // if (guestField) {
                //     guestField.value = totalGuests || 1;
                // }
                if (addonsField) {
                    addonsField.value = addonNames.join(', ');
                }
            }

            function cartRequiresTransportation() {
                ensureCartArray();
                return window.cart.some(pkg => pkg.transportation === true || pkg.transportation === 1 || pkg.transportation === '1');
            }

                        function syncDerivedTransportationFields() {
                const transportationPhoneField = $('input[name="transportation_phone"]');
                const transportationGuestField = $('input[name="transportation_guest"]');
                const packagePhone = $('input[name="package_phone"]').first().val() || $('input[name="payment_phone"]').first().val() || '';
                let totalGuests = 0;

                ensureCartArray();
                window.cart.forEach(function(pkg) {
                    if (typeof getBillableGuests === 'function') {
                        totalGuests += getBillableGuests(pkg);
                    } else {
                        const guests = parseInt(pkg && pkg.guests, 10);
                        totalGuests += Number.isFinite(guests) && guests > 0 ? guests : 1;
                    }
                });

                totalGuests = Math.max(1, totalGuests);

                transportationPhoneField.val(packagePhone).prop('required', false).removeAttr('aria-required');
                transportationGuestField.val(String(totalGuests)).prop('required', false).removeAttr('aria-required');

                transportationPhoneField.closest('.form-row').hide();
                transportationGuestField.closest('.form-row').hide();
            }
            function syncTransportationStateFromCart() {
                window.requiresTransportation = cartRequiresTransportation();
                const transportationFields = $('#transport-form').find('input, select, textarea');
                const transportationPhoneField = $('input[name="transportation_phone"]');
                const transportationAddressField = $('input[name="transportation_address"]');
                const transportationPickupTimeField = $('input[name="transportation_pickup_time"]');
                const transportationArrivalTimeField = $('input[name="transportation_arrival_time"]');
                const transportationGuestField = $('input[name="transportation_guest"]');
                const transportSectionTitle = $('#transport-section-title');
                const transportNoticeWrap = $('#transportation-notice-wrap');
                const pickupDateField = $('input[name="package_use_date"]');
                const driverNotificationConsentWrap = $('.driver-notification-consent-wrap');
                const driverNotificationConsentInputs = $('.driver-notification-consent-input');
                if (window.requiresTransportation) {
                    $('#step-2 .step-title').text('Transportation');
                    $('#next-to-transport').text('Next: Transportation Details');
                    $('#prev-to-transport').text('Previous: Transportation');
                    transportSectionTitle.text('Transportation');
                    transportNoticeWrap.show();
                    transportationFields.prop('disabled', false);
                    transportationPickupTimeField.prop('disabled', false).prop('readonly', false);
                    transportationArrivalTimeField.prop('required', false).prop('disabled', false).removeClass('required-field').removeAttr('aria-required');
                    transportationPhoneField.prop('required', true).attr('aria-required', 'true');
                    transportationAddressField.prop('required', true).attr('aria-required', 'true');
                    transportationPickupTimeField.prop('required', true).attr('aria-required', 'true');
                    transportationGuestField.prop('required', true).attr('aria-required', 'true');
                    if (!Number.isFinite(parseInt(transportationGuestField.val(), 10)) || parseInt(transportationGuestField.val(), 10) < 0) {
                        transportationGuestField.val('0');
                    }
                    pickupDateField.prop('required', true).attr('aria-required', 'true');
                    driverNotificationConsentWrap.css('display', 'flex');
                    driverNotificationConsentInputs.prop('required', true).attr('aria-required', 'true');
                } else {
                    $('#step-2 .step-title').text('Arrival');
                    $('#next-to-transport').text('Next: Arrival Time Details');
                    $('#prev-to-transport').text('Previous: Arrival Time');
                    transportSectionTitle.text('Arrival Time');
                    transportNoticeWrap.hide();
                    transportationFields.prop('disabled', false);
                    transportationPhoneField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
                    transportationAddressField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
                    transportationPickupTimeField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
                    transportationGuestField.prop('required', false).removeClass('required-field').removeAttr('aria-required').val('0');
                    transportationArrivalTimeField.prop('required', true).prop('disabled', false).attr('aria-required', 'true');
                    pickupDateField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
                    driverNotificationConsentWrap.hide();
                    driverNotificationConsentInputs.prop('checked', false).prop('required', false).removeAttr('aria-required');
                }

                syncDerivedTransportationFields();

                updateTransportationSelfDriveState();
            }

            function updateTransportationSelfDriveState() {
                const selfDriveAck = $('#transportation_self_drive_ack');
                const selfDriveWrap = $('#transportation-self-drive-wrap');
                const transportationDetailsFields = $('#transportation-details-fields');
                const transportationArrivalTimeRow = $('#transportation-arrival-time-field');
                const setArrivalTimeVisibility = function (isVisible) {
                    transportationArrivalTimeRow.each(function () {
                        this.style.setProperty('display', isVisible ? 'flex' : 'none', 'important');
                    });
                };
                const transportationPhoneField = $('input[name="transportation_phone"]');
                const transportationAddressField = $('input[name="transportation_address"]');
                const transportationPickupTimeField = $('input[name="transportation_pickup_time"]');
                const transportationArrivalTimeField = $('input[name="transportation_arrival_time"]');
                const transportationGuestField = $('input[name="transportation_guest"]');
                const transportNoticeWrap = $('#transportation-notice-wrap');
                const isSelfDrive = selfDriveAck.is(':checked');

                if (!window.requiresTransportation) {
                    transportNoticeWrap.hide();
                    selfDriveWrap.hide();
                    selfDriveAck.prop('checked', false).prop('disabled', true);
                    transportationDetailsFields.hide();
                    setArrivalTimeVisibility(true);
                    transportationPhoneField.prop('required', false).prop('disabled', true).removeClass('required-field').removeAttr('aria-required');
                    transportationAddressField.prop('required', false).prop('disabled', true).removeClass('required-field').removeAttr('aria-required');
                    transportationPickupTimeField.prop('required', false).prop('disabled', true).removeClass('required-field').removeAttr('aria-required');
                    transportationGuestField.prop('required', false).prop('disabled', true).removeClass('required-field').removeAttr('aria-required');
                    transportationArrivalTimeField.prop('required', true).prop('disabled', false).attr('aria-required', 'true');
                    return;
                }

                selfDriveWrap.show();
                selfDriveAck.prop('disabled', false);

                if (isSelfDrive) {
                    transportNoticeWrap.hide();
                    transportationDetailsFields.hide();
                    setArrivalTimeVisibility(true);
                    transportationPhoneField.prop('required', false).prop('disabled', true).removeClass('required-field').removeAttr('aria-required');
                    transportationAddressField.prop('required', false).prop('disabled', true).removeClass('required-field').removeAttr('aria-required');
                    transportationPickupTimeField.prop('required', false).prop('disabled', true).removeClass('required-field').removeAttr('aria-required');
                    transportationGuestField.prop('required', false).prop('disabled', true).removeClass('required-field').removeAttr('aria-required');
                    transportationArrivalTimeField.prop('required', true).prop('disabled', false).attr('aria-required', 'true');
                } else {
                    transportNoticeWrap.show();
                    transportationDetailsFields.show();
                    setArrivalTimeVisibility(false);
                    transportationPhoneField.prop('required', true).prop('disabled', false).attr('aria-required', 'true');
                    transportationAddressField.prop('required', true).prop('disabled', false).attr('aria-required', 'true');
                    transportationPickupTimeField.prop('required', true).prop('disabled', false).attr('aria-required', 'true');
                    transportationGuestField.prop('required', true).prop('disabled', false).attr('aria-required', 'true');
                    transportationArrivalTimeField.prop('required', false).prop('disabled', true).removeClass('required-field').removeAttr('aria-required');
                }
            }

            function parseMultipleFlag(value) {
                return value === true || value === 1 || value === '1' || value === 'true';
            }

            function getPackageMultipleFromDom(packageId) {
                let multipleValue = $('.package_number_of_guestss[data-id="' + packageId + '"]').first().data('multiple');
                return parseMultipleFlag(multipleValue);
            }

            function getBillableGuests(pkg) {
                return parseMultipleFlag(pkg.isMultiple) ? (parseInt(pkg.guests) || 1) : 1;
            }

            function getSelectedUseDate() {
                return String($('#package_use_date_iframe').val() || $('#package_use_date').val() || $('.package_use_date').val() || '').trim();
            }

            function showReservationDateError(message) {
                const text = String(message || 'Please select a reservation date.').trim();
                $('#package_use_date, #package_use_date_iframe').addClass('required-field').attr('aria-invalid', 'true');
                $('#package_use_date_error').text(text).show();
                $('#package_use_date_iframe_error').text(text).show();
            }

            function clearReservationDateError() {
                $('#package_use_date, #package_use_date_iframe').removeClass('required-field').removeAttr('aria-invalid');
                $('#package_use_date_error').hide();
                $('#package_use_date_iframe_error').hide();
            }

            function ensureReservationDateSelected() {
                const selectedDate = getSelectedUseDate();
                if (selectedDate) {
                    clearReservationDateError();
                    return true;
                }

                showReservationDateError('Please select a reservation date above before continuing.');
                if (typeof window.showToast === 'function') {
                    window.showToast('Must Choose Date', 'Please select a reservation date to continue.', 'fas fa-calendar-alt');
                }
                const dateCard = document.querySelector('.hero-date-card');
                if (dateCard && typeof dateCard.scrollIntoView === 'function') {
                    dateCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                if (document.body.classList.contains('embed-checkout-mode') && document.getElementById('package_use_date_iframe')) {
                    $('#package_use_date_iframe').trigger('focus');
                } else {
                    $('#package_use_date').trigger('focus');
                }
                return false;
            }

            function clearGuestFieldError($field) {
                const $control = $field.closest('.vip-guest-control');
                $control.find('.package-guest-error').hide().text('');
                $field.removeClass('required-field').removeAttr('aria-invalid');
            }

            function showGuestFieldError($field, message) {
                const $control = $field.closest('.vip-guest-control');
                $control.find('.package-guest-error').text(message || 'The quantity you entered is unavailable for the selected date. Please choose a lower number.').show();
                $field.addClass('required-field').attr('aria-invalid', 'true');
            }

            function updateGuestControlAvailability($field, maxSelectable, soldOutMessage) {
                const packageId = $field.data('id');
                const existingCartPackage = (typeof window.cart !== 'undefined' && Array.isArray(window.cart))
                    ? window.cart.find(function(pkg) { return String(pkg.packageId) === String(packageId); })
                    : null;
                const currentVal = $field.val();
                let current = parseInt(currentVal, 10);
                if ((!current || isNaN(current)) && existingCartPackage) {
                    current = parseInt(existingCartPackage.guests, 10);
                }
                if (!current || isNaN(current)) {
                    current = 1;
                }
                const hasPlaceholder = (!currentVal || currentVal === '') && !existingCartPackage;
                const safeMax = Math.max(0, parseInt(maxSelectable, 10) || 0);
                const isTicketInput = $field.is('input[type="number"]');
                const isTicketSelect = $field.hasClass('ticket-select-lazy');
                const $control = $field.closest('.vip-guest-control');
                const $inputWrap = $control.find('.package-guest-input-wrap');
                const $soldOut = $control.find('.package-soldout');
                let html = '';

                clearGuestFieldError($field);

                if (safeMax <= 0) {
                    $inputWrap.hide();
                    $soldOut.text(soldOutMessage || 'Sold Out for Selected Date').show();
                    $field.val('1').prop('disabled', true);
                    return;
                }

                $soldOut.hide();
                $inputWrap.show();

                if (isTicketInput) {
                    const safeValue = Math.min(Math.max(current, 1), safeMax);
                    $field.prop('disabled', false);
                    $field.attr('min', '1');
                    $field.attr('step', '1');
                    $field.val(String(safeValue));
                    return;
                }

                if (isTicketSelect) {
                    const showMax = safeMax;
                    $field.data('ticket-max', safeMax).attr('data-ticket-max', safeMax);
                    let ticketHtml = '<option value=""># of Tickets</option>';
                    for (let i = 1; i <= showMax; i++) {
                        ticketHtml += '<option value="' + i + '">' + i + ' ' + (i === 1 ? 'ticket' : 'tickets') + '</option>';
                    }
                    $field.html(ticketHtml);
                    if (hasPlaceholder) {
                        $field.val('');
                    } else {
                        const safeValue = Math.min(Math.max(current, 1), safeMax);
                        $field.val(String(safeValue));
                    }
                    $field.prop('disabled', false);
                    return;
                }

                html += '<option value="">Select Guests ▼</option>';
                for (let i = 1; i <= safeMax; i++) {
                    html += '<option value="' + i + '">' + i + ' ' + (i === 1 ? 'guest' : 'guests') + '</option>';
                }

                $field.html(html);
                if (hasPlaceholder) {
                    $field.val('');
                } else {
                    const safeValue = Math.min(Math.max(current, 1), safeMax);
                    $field.val(String(safeValue));
                }
                $field.prop('disabled', false);
            }

            // Ticket select lazy-load: append next 15 options when scrolled to bottom
            $(document).on('scroll', '.ticket-select-lazy', function () {
                var $sel = $(this);
                var shownMax = $sel.find('option').length;
                var totalMax = parseInt($sel.data('ticket-max'), 10) || shownMax;
                if (shownMax >= totalMax) { return; }
                var el = this;
                if (el.scrollHeight - el.scrollTop - el.clientHeight < 40) {
                    var nextMax = Math.min(shownMax + 15, totalMax);
                    for (var i = shownMax + 1; i <= nextMax; i++) {
                        $sel.append('<option value="' + i + '">' + i + ' ' + (i === 1 ? 'ticket' : 'tickets') + '</option>');
                    }
                }
            });
            $(document).on('keydown', '.ticket-select-lazy', function (e) {
                if (e.key !== 'ArrowDown') { return; }
                var $sel = $(this);
                var shownMax = $sel.find('option').length;
                var totalMax = parseInt($sel.data('ticket-max'), 10) || shownMax;
                if (shownMax >= totalMax) { return; }
                if (parseInt($sel.val(), 10) >= shownMax) {
                    var nextMax = Math.min(shownMax + 15, totalMax);
                    for (var i = shownMax + 1; i <= nextMax; i++) {
                        $sel.append('<option value="' + i + '">' + i + ' ' + (i === 1 ? 'ticket' : 'tickets') + '</option>');
                    }
                }
            });

            function refreshPackageAvailabilityForSelectedDate(showAlertWhenReduced) {
                const useDate = getSelectedUseDate();
                $('.package_number_of_guestss').each(function() {
                    const $field = $(this);
                    const packageId = $field.data('id');
                    const previous = parseInt($field.val(), 10) || 1;

                    $.get('/{{ $data->slug }}/package/' + packageId + '/capacity', { use_date: useDate })
                        .done(function(response) {
                            let maxSelectable = parseInt(response.max_select, 10);
                            if (!Number.isFinite(maxSelectable)) {
                                maxSelectable = parseInt(response.capacity, 10) || 0;
                            }

                            const existingCartPackage = (typeof window.cart !== 'undefined' && Array.isArray(window.cart))
                                ? window.cart.find(function(pkg) { return String(pkg.packageId) === String(packageId); })
                                : null;
                            const cartGuestsBefore = existingCartPackage ? (parseInt(existingCartPackage.guests, 10) || 1) : null;

                            updateGuestControlAvailability($field, maxSelectable, response.message || 'Sold Out for Selected Date');

                            if (existingCartPackage && cartGuestsBefore !== null) {
                                const safeMax = Math.max(0, maxSelectable);
                                if (safeMax <= 0) {
                                    window.removePackageFromCart(packageId);
                                    if (showAlertWhenReduced) {
                                        alert('A package in your cart is sold out for the selected date and was removed.');
                                    }
                                } else if (cartGuestsBefore > safeMax) {
                                    existingCartPackage.guests = safeMax;
                                    $field.val(String(safeMax));
                                    syncCheckoutCartFields();
                                    window.renderCart();
                                    window.calculateCartTotal();
                                    if (showAlertWhenReduced) {
                                        alert('Your guest count was adjusted to match current availability for the selected date.');
                                    }
                                } else {
                                    $field.val(String(cartGuestsBefore));
                                }
                            }

                            if (showAlertWhenReduced && previous > reducedTo) {
                                alert('Your guest count was adjusted to match current availability for the selected date.');
                            }

                            const $button = $('.vip-btn[data-id="' + packageId + '"]');
                            if ($button.length) {
                                if (!$button.data('default-label')) {
                                    $button.data('default-label', ($button.attr('data-default-label') || $button.text() || 'Add to Cart').trim());
                                }
                                const isSoldOut = maxSelectable <= 0;
                                $button.prop('disabled', isSoldOut);
                                $button.text(isSoldOut ? 'Sold Out' : ($button.data('default-label') || 'Add to Cart'));
                            }
                        });
                });
            }

            // Define cart functions directly on window
            window.addPackageToCart = function(packageId, packageName, packagePrice, guests, addons, transportation, isMultiple) {
                console.log('addPackageToCart called', packageId, packageName);
                ensureCartArray();
                let normalizedGuests = parseInt(guests, 10) || 1;
                let useDate = getSelectedUseDate();
                
                // Check daily limits for this package
                $.get('/{{ $data->slug }}/package/' + packageId + '/capacity', {
                    use_date: useDate,
                    requested_quantity: normalizedGuests
                }, function(response) {
                    if (!response.available) {
                        alert(response.message || 'This package is not available for the selected date.');
                        refreshPackageAvailabilityForSelectedDate(true);
                        return false;
                    }

                    let maxSelectable = parseInt(response.max_select, 10);
                    if (!Number.isFinite(maxSelectable)) {
                        maxSelectable = parseInt(response.capacity, 10) || 0;
                    }

                    if (normalizedGuests > maxSelectable) {
                        const $field = $('.package_number_of_guestss[data-id="' + packageId + '"]');
                        updateGuestControlAvailability($field, maxSelectable, response.message || 'Sold Out for Selected Date');
                        showGuestFieldError($field, response.message || 'The quantity you entered is unavailable for the selected date. Please choose a lower number.');
                        return false;
                    }

                    const packageType = ($('.package_number_of_guestss[data-id="' + packageId + '"]').data('package-type') || 'table');
                    let existing = window.cart.find(p => p.packageId === packageId);
                    if (existing) {
                        existing.guests = normalizedGuests;
                        existing.addons = addons;
                        existing.transportation = transportation;
                        existing.isMultiple = parseMultipleFlag(isMultiple);
                        existing.packageType = packageType;
                    } else {
                        window.cart.push({ packageId, packageName, packagePrice, guests: normalizedGuests, addons, transportation, isMultiple: parseMultipleFlag(isMultiple), packageType });
                    }
                    window.renderCart();
                    syncCheckoutCartFields();
                    window.calculateCartTotal();
                    syncTransportationStateFromCart();
                    refreshPackageAvailabilityForSelectedDate(false);
                    if (typeof window.showCartToast === 'function') {
                        window.showCartToast(packageName, normalizedGuests);
                    }
                    return true;
                }).fail(function() {
                    alert('We could not verify availability right now. Please try again.');
                    return false;
                });
            };

            window.removePackageFromCart = function(packageId) {
                ensureCartArray();
                window.cart = window.cart.filter(p => p.packageId != packageId);
                window.renderCart();
                syncCheckoutCartFields();
                window.calculateCartTotal();
                syncTransportationStateFromCart();
            };

            window.renderCart = function() {
                ensureCartArray();
                if (window.cart.length === 0) {
                    $('#cart-section').hide();
                    return;
                }
                $('#cart-section').show();
                    let html = '';
                window.cart.forEach(pkg => {
                    let billableGuests = getBillableGuests(pkg);
                    let unitPrice = parseFloat(pkg.packagePrice) || 0;
                    let lineTotal = unitPrice * billableGuests;
                    let priceLine = parseMultipleFlag(pkg.isMultiple)
                        ? (formatCurrency(unitPrice) + ' &times; ' + (parseInt(pkg.guests, 10) || 1) + ' = ' + formatCurrency(lineTotal))
                        : formatCurrency(lineTotal);
                    let guestQty = parseInt(pkg.guests, 10) || 1;
                    const isTicketPkg = pkg.packageType === 'ticket';
                    let guestLabel = guestQty + (isTicketPkg ? (guestQty === 1 ? ' Ticket' : ' Tickets') : (guestQty === 1 ? ' Guest' : ' Guests'));
                    let pkgThumb = pkg.packageVisual ? `<img src="${pkg.packageVisual}" class="cart-item-thumb" alt="${pkg.packageName}">` : `<div class="cart-item-thumb-placeholder"><i class="fas fa-cocktail"></i></div>`;
                    html += `<div class="cart-line">`
                        + pkgThumb
                        + `<div class="cart-line-main"><div class="cart-item-name">${pkg.packageName}</div><div class="cart-line-guests">Qty: ${guestQty}</div></div>`
                        + `<div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;"><div class="cart-item-price">${priceLine}</div><button onclick='window.removePackageFromCart("${pkg.packageId}")' class="cart-remove-btn">Remove</button></div>`
                        + (pkg.addons.length ? `<div class="cart-addons" style="color: var(--cv-primary) !important;">Add-ons: ${pkg.addons.map(a => a.name + ((parseInt(a.qty, 10) || 1) > 1 ? (' x' + (parseInt(a.qty, 10) || 1)) : '') + ' (' + formatCurrency(a.price) + ')').join(', ')}</div>` : '')
                        + `</div>`;
                });
                $('#cart-list').html(html);
                var emptyPlaceholder = document.getElementById('cv-sidebar-empty-placeholder');
                if (emptyPlaceholder) {
                    if (window.cart && window.cart.length > 0) {
                        emptyPlaceholder.style.setProperty('display', 'none', 'important');
                        emptyPlaceholder.classList.add('is-hidden');
                    } else {
                        emptyPlaceholder.style.setProperty('display', 'flex', 'important');
                        emptyPlaceholder.classList.remove('is-hidden');
                    }
                }
                syncCheckoutCartFields();
            };
            
            window.calculateCartTotal = function() {
                ensureCartArray();
                let subtotal = 0;
                window.cart.forEach(pkg => {
                    subtotal += (pkg.packagePrice * getBillableGuests(pkg)) + pkg.addons.reduce((sum, a) => sum + parseFloat(a.price), 0);
                });
                
                let gratuity = parseFloat($('#gratuity').val()) || 0;
                let refundable = parseFloat($('#refundable').val()) || 0;
                let sales_tax = parseFloat($('#sales_tax').val()) || 0;
                let service_charge = parseFloat($('#service_charge').val()) || 0;
                
                // Apply coupon discount
                let promoDiscount = 0;
                if (window.cartCoupon) {
                    if (window.cartCoupon.type == 'percentage') {
                        promoDiscount = (subtotal / 100) * window.cartCoupon.discount;
                    } else {
                        promoDiscount = window.cartCoupon.discount;
                    }
                }

                promoDiscount = Math.min(Math.max(promoDiscount, 0), subtotal);

                let discountedSubtotal = subtotal - promoDiscount;
                let service_charge_price = ("{{ $data->service_charge_name }}" != "0") ? (discountedSubtotal / 100) * service_charge : 0;
                let gratuited_price = ("{{ $data->gratuity_name }}" != "0") ? (discountedSubtotal / 100) * gratuity : 0;
                let sales_tax_price = ("{{ $data->sales_tax_name }}" != "0") ? (discountedSubtotal / 100) * sales_tax : 0;

                let processingFeeBase = discountedSubtotal;
                let amountAfterCoupon = discountedSubtotal + service_charge_price + sales_tax_price + gratuited_price;
                let processingFee = parseFloat($('#processing_fee').val()) || 0;
                let processingFeeType = ($('#processing_fee_type').val() || 'percentage').toLowerCase();
                let processingFeeAmount = processingFeeType === 'flat'
                    ? processingFee
                    : (processingFeeBase / 100) * processingFee;
                let grandTotal = amountAfterCoupon + processingFeeAmount;
                
                let refundable_price = (grandTotal / 100) * refundable;
                
                // Update displays
                $('.default-package-price > span:last-child').text(formatCurrency(subtotal));
                $('.default-service-charge > span:last-child').text(formatCurrency(service_charge_price));
                $('.default-sales-tax > span:last-child').text(formatCurrency(sales_tax_price));
                $('.default-gratuity > span:last-child').text(formatCurrency(gratuited_price));

                if (window.cartCoupon && promoDiscount > 0) {
                    if ($('.default-promo-discount').length === 0) {
                        $('.default-package-price').after('<div style="font-size: inherit !important; color: #22c55e !important; font-weight: 700 !important;" class="default-promo-discount">Promo Code Discount: <span style="font-size: inherit !important; color: #22c55e !important; font-weight: 700 !important;">$0.00</span></div>');
                    }
                    $('.default-promo-discount span').text('-' + formatCurrency(promoDiscount));
                    $('.default-package-price').after($('.default-promo-discount'));
                } else {
                    $('.default-promo-discount').remove();
                }

                if (processingFeeAmount > 0) {
                    if ($('.default-processing-fee').length === 0) {
                        $('.default-gratuity').after('<div style="font-size: 12px;" class="default-processing-fee" data-tip="Covers secure payment and transaction processing costs.">Processing Fee: <span>$0.00</span></div>');
                    }
                    $('.default-processing-fee span').text(formatCurrency(processingFeeAmount));
                } else {
                    $('.default-processing-fee').remove();
                }
                
                $('.default-refundable .refundable-amount').text(formatCurrency(refundable_price));
                $('.default-total > span:last-child').text(formatCurrency(grandTotal));
                $('.default-deposit > span:last-child').text(formatCurrency(grandTotal));
                $('.default-due .due-amount').text(formatCurrency(grandTotal - refundable_price));
                $('.payment_total').val(grandTotal.toFixed(2));
                $('#subtotal').val(refundable_price > 0 ? refundable_price.toFixed(2) : grandTotal.toFixed(2));
                $('#commission_base_amount').val(Math.max(subtotal - promoDiscount, 0).toFixed(2));
                if (typeof window.updateCheckoutPaymentRequirement === 'function') {
                    window.updateCheckoutPaymentRequirement();
                }

                $('#cart-total').text('');
                if (window.cartCoupon) {
                    $('#cart-coupon').text('Coupon: ' + window.cartCoupon.code + ' (-' + formatCurrency(promoDiscount) + ')');
                } else {
                    $('#cart-coupon').text('');
                }

                // Update Due Today (Deposit) box: show deposit amount + Due on Arrival
                if (refundable > 0) {
                    $('#cv-deposit-display').text(formatCurrency(refundable_price));
                    $('#cv-due-on-arrival').text(formatCurrency(Math.max(grandTotal - refundable_price, 0)));
                } else {
                    $('#cv-deposit-display').text(formatCurrency(grandTotal));
                }
            };
            
            console.log('Cart functions initialized:', typeof window.addPackageToCart);
            
            // Update addon checkboxes to refresh cart when changed
            $(document).on('change', '.termsConsent', function() {
                ensureCartArray();
                let packageId = $('#package_id').val();
                if (packageId) {
                    let pkg = window.cart.find(p => p.packageId == packageId);
                    if (pkg) {
                        let addons = [];
                        $('.termsConsent:checked').each(function() {
                            addons.push({ 
                                id: $(this).attr('id'), 
                                name: $(this).data('name'), 
                                price: parseFloat($(this).data('price')) 
                            });
                        });
                        pkg.addons = addons;
                        window.renderCart();
                        window.calculateCartTotal();
                    }
                }
            });
            
            // --- Shareable Link Logic for Cart ---
            function openPackageTab() {
                var packageTab = $("nav .tab[data-name='package']");
                if (packageTab.length) {
                    packageTab.trigger('click');
                } else {
                    $('.guest').hide();
                    $('.package').show();
                }
            }
            
            function getCapturedFormFields() {
                var fields = {};

                // 1. Reservation Date
                var useDate = $('#package_use_date').val() || $('input[name="package_use_date"]').val() || $('.package_use_date').val() || '';
                if (useDate) fields.package_use_date = useDate;

                // 2. Transportation & Arrival Details
                var pickupTime = $('#Pick-up-time').val() || $('input[name="transportation_pickup_time"]').val() || '';
                if (pickupTime) fields.transportation_pickup_time = pickupTime;

                var pickupAddress = $('#address').val() || $('input[name="transportation_address"]').val() || '';
                if (pickupAddress) fields.transportation_address = pickupAddress;

                var arrivalTime = $('#Arrival-time').val() || $('input[name="transportation_arrival_time"]').val() || '';
                if (arrivalTime) fields.transportation_arrival_time = arrivalTime;

                var destination = $('#destination').val() || $('input[name="transportation_destination"]').val() || '';
                if (destination) fields.transportation_destination = destination;

                // 3. Host Name & Booking / Pickup Notes
                var hostName = $('#host').val() || $('[name="host_name"]').val() || $('[name="package_host_name"]').val() || $('[name="reservation_host_name"]').val() || $('[name="host"]').val() || '';
                if (hostName) fields.host_name = hostName;

                var bookingNote = $('#note').val() || $('[name="reservation_description"]').val() || $('[name="package_note"]').val() || $('[name="transportation_note"]').val() || $('[name="notes"]').val() || $('[name="special_requests"]').val() || '';
                if (bookingNote) fields.booking_note = bookingNote;

                // 4. DOB Fields (Month, Day, Year)
                var dobMonth = $('#dob-month').val() || $('#package-dob-month').val() || $('#payment-dob-month').val() || $('[name="dob_month"]').val() || $('[name="package_dob_month"]').val() || $('[name="reservation_dob_month"]').val() || $('[name="reservation_month"]').val() || '';
                if (dobMonth) fields.dob_month = dobMonth;

                var dobDay = $('#dob-day').val() || $('#package-dob-day').val() || $('#payment-dob-day').val() || $('[name="dob_day"]').val() || $('[name="package_dob_day"]').val() || $('[name="reservation_dob_day"]').val() || $('[name="reservation_day"]').val() || '';
                if (dobDay) fields.dob_day = dobDay;

                var dobYear = $('#dob-year').val() || $('#package-dob-year').val() || $('#payment-dob-year').val() || $('[name="dob_year"]').val() || $('[name="package_dob_year"]').val() || $('[name="reservation_dob_year"]').val() || $('[name="reservation_year"]').val() || '';
                if (dobYear) fields.dob_year = dobYear;

                // 5. Customer Contact & Personal Information (including prefixed field names)
                var fieldNames = [
                    'first_name', 'package_first_name', 'reservation_first_name', 'payment_first_name',
                    'last_name', 'package_last_name', 'reservation_last_name', 'payment_last_name',
                    'name',
                    'email', 'package_email', 'reservation_email', 'payment_email',
                    'phone', 'package_phone', 'reservation_phone', 'payment_phone',
                    'gender', 'package_gender', 'reservation_gender',
                    'country', 'package_country', 'reservation_country',
                    'state', 'package_state', 'reservation_state', 'st-pv', 'state_province',
                    'city', 'package_city', 'reservation_city',
                    'zip', 'package_zip', 'reservation_zip', 'postal_code',
                    'hotel_staying', 'package_hotel_staying', 'reservation_hotel_staying', 'hotel',
                    'business_company', 'business_vat', 'business_address'
                ];

                fieldNames.forEach(function(n) {
                    var el = $('[name="' + n + '"], #' + n);
                    if (el.length && el.val()) {
                        fields[n] = el.val();
                    }
                });

                if ($('#businessExpenseCheckbox').length) {
                    fields.businessExpenseCheckbox = $('#businessExpenseCheckbox').is(':checked');
                }

                // Custom Form Fields
                $('[name^="custom_fields"]').each(function() {
                    var name = $(this).attr('name');
                    var val = $(this).val();
                    if (name && val) {
                        fields[name] = val;
                    }
                });

                return fields;
            }

            function getCurrentSelections() {
                var cartData = window.cart || (typeof cart !== 'undefined' ? cart : []);
                var couponCode = window.cartCoupon ? window.cartCoupon.code : (typeof cartCoupon !== 'undefined' && cartCoupon ? cartCoupon.code : '');
                var fields = getCapturedFormFields();

                var payload = {
                    cart: cartData,
                    coupon: couponCode,
                    fields: fields
                };

                return {
                    cart: JSON.stringify(payload),
                    coupon: couponCode,
                    fields: fields
                };
            }

            function setSelectionsFromParams(params) {
                if (!params || !params.cart) return;

                if (typeof openPackageTab === 'function') openPackageTab();

                try {
                    var cartStr = params.cart;
                    if (typeof cartStr === 'string') {
                        try {
                            cartStr = cartStr.replace(/\+/g, '%20');
                            cartStr = decodeURIComponent(cartStr);
                        } catch(e) {}
                    }

                    var parsed = (typeof cartStr === 'string') ? JSON.parse(cartStr) : cartStr;

                    var cartItems = [];
                    var couponCode = params.coupon || '';
                    var formFields = {};

                    if (parsed && typeof parsed === 'object' && !Array.isArray(parsed) && parsed.cart) {
                        cartItems = parsed.cart;
                        if (parsed.coupon) couponCode = parsed.coupon;
                        if (parsed.fields) formFields = parsed.fields;
                    } else if (Array.isArray(parsed)) {
                        cartItems = parsed;
                    }

                    window.cart = cartItems.map(function(pkg) {
                        if (typeof pkg.isMultiple === 'undefined' && typeof getPackageMultipleFromDom === 'function') {
                            pkg.isMultiple = getPackageMultipleFromDom(pkg.packageId);
                        }
                        return pkg;
                    });

                    if (typeof cart !== 'undefined') {
                        cart = window.cart;
                    }

                    if (typeof window.renderCart === 'function') window.renderCart();
                    if (typeof window.calculateCartTotal === 'function') window.calculateCartTotal();
                    if (typeof syncTransportationStateFromCart === 'function') syncTransportationStateFromCart();

                    if (window.cart.length > 0) {
                        $('#package_id').val(window.cart[0].packageId);
                        $('.package_number_of_guest').val(window.cart[0].guests);
                        window.cart.forEach(function(pkg) {
                            $('.package_number_of_guestss[data-id="' + pkg.packageId + '"]').val(pkg.guests || 1);
                            $('#pkg-card-' + pkg.packageId).addClass('selected');
                        });
                        $('#cart-section').show();
                        $('#shareLinkContainer').show();
                        $('.dynamic-price').show();
                        $('.default-price').hide();
                    }

                    // Restore Promo Code
                    if (couponCode) {
                        $('#promo_code').val(couponCode);
                        setTimeout(function() {
                            $('#applyPromoBtn').trigger('click');
                        }, 500);
                    }

                    // Restore Form Fields
                    if (formFields && Object.keys(formFields).length > 0) {
                        if (typeof populateDobSelects === 'function') {
                            try { populateDobSelects(); } catch(e) {}
                        }

                        setTimeout(function() {
                            Object.keys(formFields).forEach(function(key) {
                                var val = formFields[key];
                                if (typeof val === 'string' && val.indexOf('+') > 0) {
                                    val = val.replace(/\+/g, ' ');
                                }
                                var target = $('[name="' + key + '"], #' + key);

                                if (target.length) {
                                    if (target.is(':checkbox')) {
                                        target.prop('checked', !!val).trigger('change');
                                    } else if (target.is(':radio')) {
                                        target.filter('[value="' + val + '"]').prop('checked', true).trigger('change');
                                    } else {
                                        target.val(val).trigger('change').trigger('input');
                                        if (target[0] && target[0]._flatpickr) {
                                            try {
                                                target[0]._flatpickr.setDate(val, true);
                                            } catch(err) {}
                                        }
                                    }
                                }
                            });

                            // Email Aliases Sync
                            var emailVal = formFields.email || formFields.package_email || formFields.reservation_email || formFields.payment_email || '';
                            if (emailVal) {
                                if (emailVal.indexOf('+') > 0) emailVal = emailVal.replace(/\+/g, ' ');
                                $('[name="email"], [name="package_email"], [name="reservation_email"], [name="payment_email"], #email, #hidden_payment_email')
                                    .val(emailVal).trigger('change').trigger('input');
                            }

                            // First Name Aliases Sync
                            var firstNameVal = formFields.first_name || formFields.package_first_name || formFields.reservation_first_name || formFields.payment_first_name || formFields.name || '';
                            if (firstNameVal) {
                                if (firstNameVal.indexOf('+') > 0) firstNameVal = firstNameVal.replace(/\+/g, ' ');
                                $('[name="first_name"], [name="package_first_name"], [name="reservation_first_name"], [name="payment_first_name"], [name="name"], #first_name')
                                    .val(firstNameVal).trigger('change').trigger('input');
                            }

                            // Last Name Aliases Sync
                            var lastNameVal = formFields.last_name || formFields.package_last_name || formFields.reservation_last_name || formFields.payment_last_name || '';
                            if (lastNameVal) {
                                if (lastNameVal.indexOf('+') > 0) lastNameVal = lastNameVal.replace(/\+/g, ' ');
                                $('[name="last_name"], [name="package_last_name"], [name="reservation_last_name"], [name="payment_last_name"], #last_name')
                                    .val(lastNameVal).trigger('change').trigger('input');
                            }

                            // Phone Aliases Sync
                            var phoneVal = formFields.phone || formFields.package_phone || formFields.reservation_phone || formFields.payment_phone || '';
                            if (phoneVal) {
                                if (phoneVal.indexOf('+') > 0) phoneVal = phoneVal.replace(/\+/g, ' ');
                                $('[name="phone"], [name="package_phone"], [name="reservation_phone"], [name="payment_phone"], #phone, #hidden_payment_phone')
                                    .val(phoneVal).trigger('change').trigger('input');
                            }

                            // Host Name Aliases Sync
                            var hostVal = formFields.host_name || formFields.package_host_name || formFields.reservation_host_name || formFields.host || '';
                            if (hostVal) {
                                if (hostVal.indexOf('+') > 0) hostVal = hostVal.replace(/\+/g, ' ');
                                $('#host, [name="host_name"], [name="package_host_name"], [name="reservation_host_name"], [name="host"]')
                                    .val(hostVal).trigger('change').trigger('input');
                            }

                            // Booking Note / Pickup Note Aliases Sync
                            var noteVal = formFields.booking_note || formFields.reservation_description || formFields.package_note || formFields.transportation_note || formFields.notes || formFields.special_requests || '';
                            if (noteVal) {
                                if (noteVal.indexOf('+') > 0) noteVal = noteVal.replace(/\+/g, ' ');
                                $('#note, [name="reservation_description"], [name="package_note"], [name="transportation_note"], [name="notes"], [name="special_requests"]')
                                    .val(noteVal).trigger('change').trigger('input');
                            }

                            // DOB Month Sync
                            var dobMonthVal = formFields.dob_month || formFields.package_dob_month || formFields.reservation_dob_month || formFields.reservation_month || formFields.payment_dob_month || '';
                            if (dobMonthVal) {
                                var mStr = String(dobMonthVal).padStart(2, '0');
                                $('#dob-month, #package-dob-month, #payment-dob-month, #payment-dob-month2, [name="dob_month"], [name="package_dob_month"], [name="reservation_dob_month"], [name="reservation_month"], [name="payment_dob_month"]')
                                    .val(mStr).trigger('change');
                            }

                            // DOB Day Sync
                            var dobDayVal = formFields.dob_day || formFields.package_dob_day || formFields.reservation_dob_day || formFields.reservation_day || formFields.payment_dob_day || '';
                            if (dobDayVal) {
                                var dStr = String(dobDayVal).padStart(2, '0');
                                $('#dob-day, #package-dob-day, #payment-dob-day, #payment-dob-day2, [name="dob_day"], [name="package_dob_day"], [name="reservation_dob_day"], [name="reservation_day"], [name="payment_dob_day"]')
                                    .val(dStr).trigger('change');
                            }

                            // DOB Year Sync
                            var dobYearVal = formFields.dob_year || formFields.package_dob_year || formFields.reservation_dob_year || formFields.reservation_year || formFields.payment_dob_year || '';
                            if (dobYearVal) {
                                var yStr = String(dobYearVal);
                                $('#dob-year, #package-dob-year, #payment-dob-year, #payment-dob-year2, [name="dob_year"], [name="package_dob_year"], [name="reservation_dob_year"], [name="reservation_year"], [name="payment_dob_year"]')
                                    .val(yStr).trigger('change');
                            }

                            // Special handling for package_use_date
                            if (formFields.package_use_date) {
                                var dateEl = $('#package_use_date, input[name="package_use_date"], .package_use_date');
                                if (dateEl.length) {
                                    dateEl.val(formFields.package_use_date).trigger('change');
                                    if (dateEl[0] && dateEl[0]._flatpickr) {
                                        try {
                                            dateEl[0]._flatpickr.setDate(formFields.package_use_date, true);
                                        } catch(e) {}
                                    }
                                }
                            }

                            // Special handling for businessExpenseCheckbox
                            if (formFields.businessExpenseCheckbox) {
                                $('#businessExpenseCheckbox').prop('checked', true).trigger('change');
                                $('#businessFields').show();
                                if (typeof setBusinessFieldsRequired === 'function') {
                                    setBusinessFieldsRequired(true);
                                }
                            }
                        }, 300);
                    }

                    if (window.cart.length > 0) {
                        $('#checkout-steps').show();
                        if (typeof showStep === 'function') {
                            showStep(1);
                        }
                    }
                } catch(e) {
                    console.error('Error in setSelectionsFromParams:', e);
                }
            }

            

            

            

            function getUrlWithSelections() {
                var sel = getCurrentSelections();
                var url = window.location.origin + window.location.pathname + '?cart=' + encodeURIComponent(sel.cart);
                if (sel.coupon) {
                    url += '&coupon=' + encodeURIComponent(sel.coupon);
                }
                return url;
            }

            $(document).ready(function() {
                function showCopyTooltip() {
                    const tooltip = $('#copyTooltip');
                    tooltip.text('Link copied!').show();
                    setTimeout(function() {
                        tooltip.hide();
                    }, 2000);
                }

                function getShareableUrl() {
                    var existing = String($('#shareableLink').val() || '').trim();
                    return existing || getUrlWithSelections();
                }

                function revealShareActions() {
                    $('#shareActions').css('display', 'flex');
                }

                function copyShareUrl(url) {
                    navigator.clipboard.writeText(url).then(function() {
                        showCopyTooltip();
                        alert('Link copied!');
                    }).catch(function() {
                        $('#shareableLink').val(url).show().trigger('focus').select();
                        revealShareActions();
                        alert('Link ready. Press Ctrl+C to copy.');
                    });
                }

                // Generate link button
                $('#generateShareLink').on('click', function() {
                    if (window.cart.length === 0) {
                        alert('Please add at least one package to cart');
                        return;
                    }
                    
                    var selections = getCurrentSelections();
                    
                    $.ajax({
                        url: '/cart/share',
                        type: 'POST',
                        data: {
                            cart: selections.cart,
                            website_slug: '{{ $data->slug }}',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.success) {
                                $('#shareableLink').val(res.short_url).show();
                                revealShareActions();
                                navigator.clipboard.writeText(res.short_url).then(function() {
                                    showCopyTooltip();
                                }).catch(function() {
                                    $('#shareableLink').select();
                                });
                            } else {
                                const fallbackUrl = getUrlWithSelections();
                                $('#shareableLink').val(fallbackUrl).show();
                                revealShareActions();
                                $('#shareableLink').select();
                            }
                        },
                        error: function(err) {
                            const fallbackUrl = getUrlWithSelections();
                            $('#shareableLink').val(fallbackUrl).show();
                            revealShareActions();
                            $('#shareableLink').select();
                            console.error(err);
                        }
                    });
                });

                $(document).on('click', '#shareActions .checkout-share-btn', function() {
                    var mode = String($(this).data('share') || '').toLowerCase();
                    var url = getShareableUrl();

                    if (!url) {
                        alert('Please generate a shareable link first.');
                        return;
                    }

                    if (mode === 'email') {
                        window.location.href = 'mailto:?subject=' + encodeURIComponent('Checkout Link') + '&body=' + encodeURIComponent(url);
                        return;
                    }

                    if (mode === 'whatsapp') {
                        window.open('https://wa.me/?text=' + encodeURIComponent(url), '_blank', 'noopener');
                        return;
                    }

                    if (mode === 'facebook') {
                        window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url), '_blank', 'noopener');
                        return;
                    }

                    if (mode === 'copy') {
                        copyShareUrl(url);
                    }
                });

                // Copy to clipboard when clicking the shareable link field
                $('#shareableLink').on('click', function() {
                    const url = $(this).val();
                    navigator.clipboard.writeText(url).then(function() {
                        showCopyTooltip();
                    }).catch(function(err) {
                        console.error('Failed to copy:', err);
                        $('#shareableLink').select();
                    });
                });

                if (String($('#shareableLink').val() || '').trim()) {
                    revealShareActions();
                }

                // On page load, check for params
                var urlParams = new URLSearchParams(window.location.search);
                var cartParam = urlParams.get('cart');
                var couponParam = urlParams.get('coupon');

                // Always keep shareable link button visible
                $('#generateShareLink').show();

                // Preselect items from params
                if (cartParam || couponParam) {
                    setSelectionsFromParams({
                        cart: cartParam,
                        coupon: couponParam
                    });
                    setTimeout(function() {
                        if (window.cart.length > 0) {
                            $('#checkout-steps').show();
                            showStep(1);
                        }
                    }, 1500);
                }
                
                // Business expense checkbox handler
                function setBusinessFieldsRequired(on) {
                    ['business_company', 'business_vat', 'business_address'].forEach(function (n) {
                        var el = document.querySelector('[name="' + n + '"]');
                        if (el) { if (on) { el.setAttribute('required', 'required'); } else { el.removeAttribute('required'); } }
                    });
                }
                // Business fields start hidden, so they must not be required until the box is checked
                // (a required field inside a display:none container blocks form submission).
                setBusinessFieldsRequired($('#businessExpenseCheckbox').is(':checked'));
                $('#businessExpenseCheckbox').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#businessFields').slideDown();
                        setBusinessFieldsRequired(true);
                    } else {
                        $('#businessFields').slideUp();
                        setBusinessFieldsRequired(false);
                    }
                });

                // Multi-step form safety: the instant "Complete Purchase" is clicked, drop `required`
                // from any field that is currently hidden so native validation can never block
                // submission with "An invalid form control is not focusable".
                (function () {
                    var purchaseBtn = document.getElementById('submitBtn');
                    if (purchaseBtn) {
                        purchaseBtn.addEventListener('click', function () {
                            document.querySelectorAll('#payment-form [required]').forEach(function (el) {
                                if (el.offsetParent === null) { el.removeAttribute('required'); }
                            });
                        }, true);
                    }
                })();
            });
            // --- End Shareable Link Logic ---
        </script>

        <script>
            $(function() {
                function isThisWeek(date) {
                    const now = new Date();
                    const input = new Date(date);
                    const first = now.getDate() - now.getDay();
                    const last = first + 6;
                    const weekStart = new Date(now.setDate(first));
                    weekStart.setHours(0, 0, 0, 0);
                    const weekEnd = new Date(now.setDate(last));
                    weekEnd.setHours(23, 59, 59, 999);
                    return input >= weekStart && input <= weekEnd;
                }

                function isNextWeek(date) {
                    const now = new Date();
                    const input = new Date(date);
                    const first = (now.getDate() - now.getDay()) + 7;
                    const last = first + 6;
                    const weekStart = new Date(now.setDate(first));
                    weekStart.setHours(0, 0, 0, 0);
                    const weekEnd = new Date(now.setDate(last));
                    weekEnd.setHours(23, 59, 59, 999);
                    return input >= weekStart && input <= weekEnd;
                }

                function getTodaysDate() {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }

                $('.package_use_date').attr('min', getTodaysDate());
            });
        </script>

        <script>
            $(function() {
                function isThisWeek(date) {
                    const now = new Date();
                    const input = new Date(date);
                    const first = now.getDate() - now.getDay();
                    const last = first + 6;
                    const weekStart = new Date(now.setDate(first));
                    weekStart.setHours(0, 0, 0, 0);
                    const weekEnd = new Date(now.setDate(last));
                    weekEnd.setHours(23, 59, 59, 999);
                    return input >= weekStart && input <= weekEnd;
                }

                function isNextWeek(date) {
                    const now = new Date();
                    const input = new Date(date);
                    const first = (now.getDate() - now.getDay()) + 7;
                    const last = first + 6;
                    const weekStart = new Date(now.setDate(first));
                    weekStart.setHours(0, 0, 0, 0);
                    const weekEnd = new Date(now.setDate(last));
                    weekEnd.setHours(23, 59, 59, 999);
                    return input >= weekStart && input <= weekEnd;
                }

                function getTodaysDate() {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }

                $('.package_use_date').attr('min', getTodaysDate());
            });
        </script>

        <script>
            $(function() {
                function isThisWeek(date) {
                    const now = new Date();
                    const input = new Date(date);
                    const first = now.getDate() - now.getDay();
                    const last = first + 6;
                    const weekStart = new Date(now.setDate(first));
                    weekStart.setHours(0, 0, 0, 0);
                    const weekEnd = new Date(now.setDate(last));
                    weekEnd.setHours(23, 59, 59, 999);
                    return input >= weekStart && input <= weekEnd;
                }

                function isThisMonth(date) {
                    const now = new Date();
                    const input = new Date(date);
                    return input.getMonth() === now.getMonth() && input.getFullYear() === now.getFullYear();
                }

                function isThisYear(date) {
                    const now = new Date();
                    const input = new Date(date);
                    return input.getFullYear() === now.getFullYear();
                }
                $('.event-filter').on('click', function() {
                    const filter = $(this).data('filter');
                    $('.event-filter').removeClass('active');
                    $(this).addClass('active');
                    $('#events-list .event-card-item').each(function() {
                        const date = $(this).data('date');
                        let show = false;
                        if (filter === 'week') show = isThisWeek(date);
                        if (filter === 'month') show = isThisMonth(date);
                        if (filter === 'year') show = isThisYear(date);
                        $(this).toggle(show);
                    });
                });
                // Optionally, trigger default filter (e.g., show all or this week)
                $('.event-filter[data-filter="year"]').trigger('click');
            });
        </script>

        <script>
            // Auto-populate hidden payment fields when moving to payment step
            function populatePaymentFields() {
                // Use E.164 format from hidden field for SMS
                const e164Phone = $('input[name="package_phone_e164"]').val() || $('input[name="package_phone"]').val();
                $('#hidden_payment_phone').val(e164Phone);
                $('#hidden_payment_email').val($('input[name="package_email"]').val());
                $('#hidden_payment_month').val($('select[name="package_month"]').val());
                $('#hidden_payment_day').val($('select[name="package_day"]').val());
                $('#hidden_payment_year').val($('select[name="package_year"]').val());
            }

            // Copy package holder info to payment info (for visible fields only)
            $(document).on('click', '.same-as-info', function() {
                // Text fields - only copy visible fields now
                $("input[name='payment_first_name']").val($("input[name='package_first_name']").val());
                $("input[name='payment_last_name']").val($("input[name='package_last_name']").val());
                // Hidden fields are auto-populated when moving to payment step
                populatePaymentFields();
            });

            // Copy package holder info to transportation info
            $(document).on('click', '.same-as-info-transport', function() {
                // Get references
                const transportPhoneInput = $('input[name="transportation_phone"]')[0];
                const transportCountryCode = $('input[name="transportation_phone_country"]')[0];
                const packagePhoneInput = $('input[name="package_phone"]')[0];
                const packageCountryCode = $('input[name="package_phone_country"]')[0];

                if (!transportPhoneInput || !transportCountryCode || !packageCountryCode) {
                    console.warn('Missing fields for copy operation');
                    return;
                }

                // Copy phone number
                const packagePhoneValue = packagePhoneInput ? packagePhoneInput.value : '';
                transportPhoneInput.value = packagePhoneValue;

                // Copy country code - both the display value and the dataset code
                transportCountryCode.value = packageCountryCode.value;
                transportCountryCode.dataset.code = packageCountryCode.dataset.code;

                // Copy E.164 field
                const packageE164Field = $('input[name="package_phone_e164"]')[0];
                let transportE164Field = $('input[name="transportation_phone_e164"]')[0];

                if (packageE164Field && packageE164Field.value) {
                    if (!transportE164Field) {
                        transportE164Field = document.createElement('input');
                        transportE164Field.type = 'hidden';
                        transportE164Field.name = 'transportation_phone_e164';
                        transportPhoneInput.parentElement.appendChild(transportE164Field);
                    }
                    transportE164Field.value = packageE164Field.value;
                }

                // Trigger change event and validation
                $(transportPhoneInput).trigger('input').trigger('change');
                $(transportCountryCode).trigger('change');
            });
            // Populate country select
            function populateCountrySelect(selectId) {
                const countries = [
                    'United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France', 'Italy', 'Spain',
                    'Netherlands', 'Brazil', 'India', 'China', 'Japan', 'South Korea', 'Mexico', 'Russia', 'South Africa',
                    'New Zealand', 'Sweden', 'Norway', 'Denmark', 'Finland', 'Ireland', 'Switzerland', 'Austria', 'Belgium',
                    'Portugal', 'Poland', 'Turkey', 'Argentina', 'Chile', 'Colombia', 'Czech Republic', 'Greece', 'Hungary',
                    'Iceland', 'Indonesia', 'Israel', 'Malaysia', 'Philippines', 'Saudi Arabia', 'Singapore', 'Slovakia',
                    'Thailand', 'Ukraine', 'United Arab Emirates', 'Vietnam', 'Egypt', 'Morocco', 'Nigeria', 'Pakistan',
                    'Romania', 'Serbia', 'Croatia', 'Slovenia', 'Bulgaria', 'Estonia', 'Latvia', 'Lithuania', 'Luxembourg',
                    'Malta', 'Monaco', 'Montenegro', 'Qatar', 'Kuwait', 'Oman', 'Bahrain', 'Jordan', 'Lebanon', 'Cyprus',
                    'Georgia', 'Kazakhstan', 'Uzbekistan', 'Bangladesh', 'Sri Lanka', 'Nepal', 'Cambodia', 'Laos',
                    'Myanmar', 'Mongolia', 'Afghanistan', 'Albania', 'Armenia', 'Azerbaijan', 'Belarus',
                    'Bosnia and Herzegovina', 'Botswana', 'Brunei', 'Burkina Faso', 'Burundi', 'Cameroon', 'Cape Verde',
                    'Central African Republic', 'Chad', 'Comoros', 'Congo', 'Costa Rica', 'Cuba', 'Djibouti', 'Dominica',
                    'Dominican Republic', 'Ecuador', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Eswatini', 'Ethiopia',
                    'Fiji', 'Gabon', 'Gambia', 'Ghana', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana',
                    'Haiti', 'Honduras', 'Jamaica', 'Kenya', 'Kiribati', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein',
                    'Madagascar', 'Malawi', 'Maldives', 'Mali', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Micronesia',
                    'Moldova', 'Mozambique', 'Namibia', 'Nauru', 'Nicaragua', 'Niger', 'North Korea', 'North Macedonia',
                    'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Rwanda',
                    'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino',
                    'Sao Tome and Principe', 'Senegal', 'Seychelles', 'Sierra Leone', 'Solomon Islands', 'Somalia',
                    'South Sudan', 'Sudan', 'Suriname', 'Syria', 'Tajikistan', 'Tanzania', 'Timor-Leste', 'Togo', 'Tonga',
                    'Trinidad and Tobago', 'Tunisia', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Uruguay', 'Vanuatu',
                    'Vatican City', 'Venezuela', 'Yemen', 'Zambia', 'Zimbabwe'
                ];
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">Select Country</option>';
                countries.forEach(function(country) {
                    select.innerHTML += `<option value="${country}">${country}</option>`;
                });
            }

            function populateCountrySelect2(selectId) {
                const countries = [
                    'United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France', 'Italy', 'Spain',
                    'Netherlands', 'Brazil', 'India', 'China', 'Japan', 'South Korea', 'Mexico', 'Russia', 'South Africa',
                    'New Zealand', 'Sweden', 'Norway', 'Denmark', 'Finland', 'Ireland', 'Switzerland', 'Austria', 'Belgium',
                    'Portugal', 'Poland', 'Turkey', 'Argentina', 'Chile', 'Colombia', 'Czech Republic', 'Greece', 'Hungary',
                    'Iceland', 'Indonesia', 'Israel', 'Malaysia', 'Philippines', 'Saudi Arabia', 'Singapore', 'Slovakia',
                    'Thailand', 'Ukraine', 'United Arab Emirates', 'Vietnam', 'Egypt', 'Morocco', 'Nigeria', 'Pakistan',
                    'Romania', 'Serbia', 'Croatia', 'Slovenia', 'Bulgaria', 'Estonia', 'Latvia', 'Lithuania', 'Luxembourg',
                    'Malta', 'Monaco', 'Montenegro', 'Qatar', 'Kuwait', 'Oman', 'Bahrain', 'Jordan', 'Lebanon', 'Cyprus',
                    'Georgia', 'Kazakhstan', 'Uzbekistan', 'Bangladesh', 'Sri Lanka', 'Nepal', 'Cambodia', 'Laos',
                    'Myanmar', 'Mongolia', 'Afghanistan', 'Albania', 'Armenia', 'Azerbaijan', 'Belarus',
                    'Bosnia and Herzegovina', 'Botswana', 'Brunei', 'Burkina Faso', 'Burundi', 'Cameroon', 'Cape Verde',
                    'Central African Republic', 'Chad', 'Comoros', 'Congo', 'Costa Rica', 'Cuba', 'Djibouti', 'Dominica',
                    'Dominican Republic', 'Ecuador', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Eswatini', 'Ethiopia',
                    'Fiji', 'Gabon', 'Gambia', 'Ghana', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana',
                    'Haiti', 'Honduras', 'Jamaica', 'Kenya', 'Kiribati', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein',
                    'Madagascar', 'Malawi', 'Maldives', 'Mali', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Micronesia',
                    'Moldova', 'Mozambique', 'Namibia', 'Nauru', 'Nicaragua', 'Niger', 'North Korea', 'North Macedonia',
                    'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Rwanda',
                    'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino',
                    'Sao Tome and Principe', 'Senegal', 'Seychelles', 'Sierra Leone', 'Solomon Islands', 'Somalia',
                    'South Sudan', 'Sudan', 'Suriname', 'Syria', 'Tajikistan', 'Tanzania', 'Timor-Leste', 'Togo', 'Tonga',
                    'Trinidad and Tobago', 'Tunisia', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Uruguay', 'Vanuatu',
                    'Vatican City', 'Venezuela', 'Yemen', 'Zambia', 'Zimbabwe'
                ];
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">Select Country</option>';
                countries.forEach(function(country) {
                    select.innerHTML += `<option value="${country}">${country}</option>`;
                });
            }

            // Function to force Safari/iOS select styling after JavaScript population
            function forceSafariSelectStyling() {
                // Target all select fields that are JavaScript-generated
                const selectIds = ['country', 'country2', 'st-pv', 'dob-month', 'dob-day', 'dob-year',
                    'package-dob-month', 'package-dob-day', 'package-dob-year',
                    'payment-dob-month', 'payment-dob-day', 'payment-dob-year',
                    'payment-dob-month2', 'payment-dob-day2', 'payment-dob-year2'
                ];

                selectIds.forEach(function(id) {
                    const element = document.getElementById(id);
                    if (element) {
                        element.style.setProperty('-webkit-appearance', 'none', 'important');
                        // Force re-apply CSS styles for Safari/iOS
                        element.style.setProperty('-moz-appearance', 'none', 'important');
                        element.style.setProperty('background-color', '#ffffff', 'important');
                        element.style.setProperty('appearance', 'none', 'important');
                        element.style.setProperty('background-image', 'url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 20 20\' fill=\'%2364748b\'%3E%3Cpath fill-rule=\'evenodd\' d=\'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z\' clip-rule=\'evenodd\'/%3E%3C/svg%3E")', 'important');
                        element.style.setProperty('background-repeat', 'no-repeat', 'important');
                        element.style.setProperty('background-position', 'right 12px center', 'important');
                        element.style.setProperty('background-size', '16px 16px', 'important');
                        element.style.setProperty('padding', '10px 32px 10px 14px', 'important');
                        element.style.setProperty('border', '1px solid #cbd5e1', 'important');
                        element.style.setProperty('border-radius', '10px', 'important');
                        element.style.setProperty('color', '#0f172a', 'important');
                        element.style.setProperty('-webkit-text-fill-color', '#0f172a', 'important');
                        element.style.setProperty('font-size', '13.5px', 'important');
                        element.style.setProperty('min-height', '46px', 'important');
                        element.style.setProperty('line-height', '1.5', 'important');
                        element.style.setProperty('text-align', 'left', 'important');

                        // Special handling for DOB fields (smaller arrows)
                        if (id.includes('dob')) {
                            element.style.setProperty('padding', '10px 28px 10px 12px', 'important');
                            element.style.setProperty('background-size', '14px 14px', 'important');
                            element.style.setProperty('background-position', 'right 8px center', 'important');
                            element.style.setProperty('text-align', 'left', 'important');
                        }
                    }
                });
            }

            // On DOM ready, also populate country select
            $(function() {
                populateCountrySelect('country');
                populateCountrySelect2('country2');

                // Apply styling after population with a slight delay for Safari
                setTimeout(function() {
                    forceSafariSelectStyling();
                }, 100);
            });
            // Populate DOB selects for all three sections
            function populateDOBSelects(monthId, dayId, yearId) {
                const monthSelect = document.getElementById(monthId);
                const daySelect = document.getElementById(dayId);
                const yearSelect = document.getElementById(yearId);

                // Check if elements exist before trying to populate them
                if (!monthSelect || !daySelect || !yearSelect) {
                    return; // Elements don't exist, skip population
                }

                // Months 1-12 (with "Month" placeholder)
                monthSelect.innerHTML = '<option value="" disabled selected hidden>Month</option>';
                for (let m = 1; m <= 12; m++) {
                    monthSelect.innerHTML +=
                        `<option value="${m.toString().padStart(2, '0')}">${m.toString().padStart(2, '0')}</option>`;
                }
                // Days 1-31 (with "Day" placeholder)
                daySelect.innerHTML = '<option value="" disabled selected hidden>Day</option>';
                for (let d = 1; d <= 31; d++) {
                    daySelect.innerHTML +=
                        `<option value="${d.toString().padStart(2, '0')}">${d.toString().padStart(2, '0')}</option>`;
                }
                // Years: current year to (current year - 100) (with "Year" placeholder)
                const currentYear = new Date().getFullYear();
                yearSelect.innerHTML = '<option value="" disabled selected hidden>Year</option>';
                for (let y = currentYear; y >= currentYear - 100; y--) {
                    yearSelect.innerHTML += `<option value="${y}">${y}</option>`;
                }
            }
            // On DOM ready
            $(function() {
                populateDOBSelects('dob-month', 'dob-day', 'dob-year');
                populateDOBSelects('package-dob-month', 'package-dob-day', 'package-dob-year');
                populateDOBSelects('payment-dob-month', 'payment-dob-day', 'payment-dob-year');
                populateDOBSelects('payment-dob-month2', 'payment-dob-day2', 'payment-dob-year2');

                // Apply styling after population with a slight delay for Safari
                setTimeout(function() {
                    forceSafariSelectStyling();
                }, 100);
            });


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
                let addons = selection.addons || [];
                let html = '';

                if (!addons.length) {
                    html = '<p style="margin:0;opacity:.8;">No add-ons available for this package. Click confirm to continue.</p>';
                } else {
                    let existingCartPkg = Array.isArray(window.cart) ? window.cart.find(p => p.packageId == selection.packageId) : null;
                    let existingAddons = existingCartPkg ? (existingCartPkg.addons || []) : [];
                    addons.forEach(function(addon) {
                        let unitPrice = parseFloat(addon.price || 0);
                        let existingAddon = existingAddons.find(a => String(a.id) === String(addon.id));
                        let currentQty = existingAddon ? (parseInt(existingAddon.qty, 10) || (existingAddon.price > 0 ? Math.round(existingAddon.price / unitPrice) : 1)) : 0;
                        if (!Number.isFinite(currentQty) || currentQty < 0) {
                            currentQty = 0;
                        }
                        let description = String(addon.description || '').trim();
                        let descriptionHtml = description ? ('<small class="addon-modal-desc">' + escapeAddonHtml(description) + '</small>') : '';
                        let lineTotal = unitPrice * currentQty;
                        html += '<div class="addon-modal-row">'
                            + '<span class="addon-modal-label">' + escapeAddonHtml(addon.name) + '<span class="addon-modal-unit">' + formatCurrency(unitPrice) + '/ea</span>' + descriptionHtml + '<small class="addon-line-total">Line total: <span class="addon-line-total-value" data-id="' + addon.id + '">' + formatCurrency(lineTotal) + '</span></small></span>'
                            + '<span class="addon-qty-stepper">'
                            + '<button type="button" class="addon-qty-btn addon-qty-dec" data-id="' + addon.id + '">&#8722;</button>'
                            + '<span class="addon-qty-val" data-id="' + addon.id + '" data-name="' + escapeAddonHtml(addon.name) + '" data-price="' + unitPrice + '">' + currentQty + '</span>'
                            + '<button type="button" class="addon-qty-btn addon-qty-inc" data-id="' + addon.id + '">+</button>'
                            + '</span>'
                            + '</div>';
                    });
                }

                $('#addonSelectionModalTitle').text('Select Add-ons for ' + (selection.pkgName || selection.packageName));
                $('#addonSelectionModalBody').html(html);
                var addonSelectionModalEl = document.getElementById('addonSelectionModal');
                if (document.body.classList.contains('embed-checkout-mode')) {
                    addonSelectionModalEl.dataset.returnScrollY = String(window.pageYOffset || window.scrollY || 0);
                    addonSelectionModalEl.dataset.restoreScrollOnHide = '1';
                    addonSelectionModalEl.dataset.scrollToCheckoutOnHide = '0';
                }
                bootstrap.Modal.getOrCreateInstance(addonSelectionModalEl).show();
                if (document.body.classList.contains('embed-checkout-mode')) {
                    window.parent.postMessage({ type: 'checkoutScrollToIframe' }, '*');
                    window.scrollTo({ top: 0, behavior: 'auto' });
                    addonSelectionModalEl.scrollTop = 0;
                    window.dispatchEvent(new CustomEvent('embed:category-toggle'));
                    window.setTimeout(function () {
                        adjustAddonModalScrollArea();
                    }, 0);
                }
            }

            $(document).ready(function() {
                const singlePackageHeroMode = @json(!empty($isSinglePackageCheckout));
                let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                popoverTriggerList.forEach(function (popoverTriggerEl) {
                    bootstrap.Popover.getOrCreateInstance(popoverTriggerEl, {
                        trigger: 'focus hover',
                        html: true,
                        sanitize: true,
                        container: 'body'
                    });
                });

                $(document).on('click', '.package-category-tile', function(e) {
                    e.preventDefault();
                    let targetSelector = String($(this).data('target') || '');
                    let targetId = targetSelector.replace(/^#/, '');
                    let $target = targetId ? $('#' + targetId) : $();
                    let isAlreadyActive = $(this).hasClass('active');

                    if (!isAlreadyActive && $target.length) {
                        $('.package-category-tile').removeClass('active');
                        $(this).addClass('active');
                        $('.package-category-group').hide();
                        $target.stop(true, true).fadeIn(150);
                    }

                    if (document.body.classList.contains('embed-checkout-mode')) {
                        $('.package-category-group').promise().done(function() {
                            window.dispatchEvent(new CustomEvent('embed:category-toggle'));
                        });
                    }
                });

                if ($('.package-category-tile').length) {
                    if (!$('.package-category-tile.active').length) {
                        $('.package-category-tile').first().addClass('active');
                    }
                    let activeTarget = $('.package-category-tile.active').data('target');
                    if (activeTarget && $(activeTarget).length) {
                        $('.package-category-group').hide();
                        $(activeTarget).show();
                    } else {
                        $('.package-category-group').first().show();
                    }
                } else {
                    $('.package-category-group').show();
                }

                if ($('#package_use_date_iframe').length) {
                    $('#package_use_date_iframe').val($('#package_use_date').val() || '');
                }

                $(document).on('click', '#btnFilterPopularPkg', function(e) {
                    e.preventDefault();
                    $('.cv-filter-pills .cv-pill-btn').removeClass('is-active').addClass('btn-outline');
                    $(this).addClass('is-active').removeClass('btn-outline');

                    let targetCat = String($(this).data('target-cat') || '');
                    let targetPkg = String($(this).data('target-pkg') || '');

                    if (targetCat) {
                        let $catTile = $('.package-category-tile[data-target="' + targetCat + '"]');
                        if ($catTile.length && !$catTile.hasClass('active')) {
                            $catTile.trigger('click');
                        } else {
                            $('.package-category-group').hide();
                            $(targetCat).show();
                        }
                    }

                    if (targetPkg && $(targetPkg).length) {
                        let $pkgCard = $(targetPkg);
                        $pkgCard.show();
                        $('html, body').stop().animate({
                            scrollTop: $pkgCard.offset().top - 120
                        }, 300);
                        $pkgCard.addClass('selected-package highlight-pulse');
                        setTimeout(function() {
                            $pkgCard.removeClass('highlight-pulse');
                        }, 2000);
                    }
                });

                $(document).on('click', '#btnFilterMostPopular', function(e) {
                    e.preventDefault();
                    $('.cv-filter-pills .cv-pill-btn').removeClass('is-active').addClass('btn-outline');
                    $(this).addClass('is-active').removeClass('btn-outline');

                    let $activeTile = $('.package-category-tile.active');
                    if (!$activeTile.length) {
                        $activeTile = $('.package-category-tile').first();
                        $activeTile.addClass('active');
                    }
                    let targetCat = $activeTile.data('target');
                    if (targetCat && $(targetCat).length) {
                        $('.package-category-group').hide();
                        $(targetCat).show();
                        $(targetCat).find('.vip-card').show();
                    }
                });

                $(document).on('click', '.vip-btn', function() {
                    let $btn = $(this);
                    let packageId = $btn.data('id');
                    let packageName = $btn.data('name');
                    let packagePrice = parseFloat($btn.data('price'));
                    let $guestSelect = $('.package_number_of_guestss[data-id="' + packageId + '"]');
                    let guestValue = $guestSelect.val();
                    let isMultiple = parseMultipleFlag($guestSelect.data('multiple'));
                    let transportation = $btn.data('transportation');

                    if (!ensureReservationDateSelected()) {
                        return;
                    }

                    if (!guestValue) {
                        let fieldLabel = $guestSelect.find('option:first').text();
                        alert('Please select ' + fieldLabel);
                        return;
                    }

                    let guests = parseInt(guestValue) || 1;

                    $('.vip-card').removeClass('selected');
                    $btn.closest('.vip-card').addClass('selected');

                    $.ajax({
                        url: "/{{ $data->slug }}/addons/" + packageId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(res) {
                            window.pendingPackageSelection = {
                                packageId: packageId,
                                packageName: packageName,
                    packageVisual: $(this).closest('.vip-card').find('.cv-pkg-media').attr('src') || '',
                                packagePrice: packagePrice,
                                guests: guests,
                                isMultiple: isMultiple,
                                transportation: transportation,
                                addons: Array.isArray(res) ? res : []
                            };

                            // No add-ons to offer: add the package straight to the cart (skip the modal).
                            if ((window.pendingPackageSelection.addons || []).length === 0) {
                                $('#addonModalNoAddonsBtn').trigger('click');
                            } else {
                                openAddonSelectionModal(window.pendingPackageSelection);
                            }
                        }
                    });
                });

                $('#addonModalConfirmBtn').on('click', function() {
                    if (!window.pendingPackageSelection) {
                        return;
                    }

                    let selection = window.pendingPackageSelection;
                    let selectedAddons = [];

                    $('#addonSelectionModalBody .addon-qty-val').each(function() {
                        let qty = parseInt($(this).text(), 10) || 0;
                        if (qty > 0) {
                            let unitPrice = parseFloat($(this).data('price'));
                            selectedAddons.push({
                                id: $(this).data('id'),
                                name: $(this).data('name'),
                                unit_price: unitPrice,
                                price: unitPrice * qty,
                                qty: qty
                            });
                        }
                    });

                    window.addPackageToCart(selection.packageId, selection.packageName, selection.packagePrice, selection.guests, selectedAddons, selection.transportation, selection.isMultiple);

                    let addonModalEl = document.getElementById('addonSelectionModal');
                    if (document.body.classList.contains('embed-checkout-mode') && addonModalEl) {
                        addonModalEl.dataset.restoreScrollOnHide = '0';
                        addonModalEl.dataset.scrollToCheckoutOnHide = '1';
                    }
                    $('#package_id').val(selection.packageId);

                    $('.dynamic-price').show();
                    $('.default-price').hide();
                    $('#checkout-steps').show();
                    syncTransportationStateFromCart();
                    showStep(1);

                    bootstrap.Modal.getOrCreateInstance(document.getElementById('addonSelectionModal')).hide();
                    window.pendingPackageSelection = null;
                });

                // No Add-ons button - adds package without any selected add-ons
                $('#addonModalNoAddonsBtn').on('click', function() {
                    if (!window.pendingPackageSelection) {
                        return;
                    }

                    let selection = window.pendingPackageSelection;
                    let selectedAddons = []; // Empty array - no add-ons selected

                    window.addPackageToCart(selection.packageId, selection.packageName, selection.packagePrice, selection.guests, selectedAddons, selection.transportation, selection.isMultiple);

                    let addonModalEl = document.getElementById('addonSelectionModal');
                    if (document.body.classList.contains('embed-checkout-mode') && addonModalEl) {
                        addonModalEl.dataset.restoreScrollOnHide = '0';
                        addonModalEl.dataset.scrollToCheckoutOnHide = '1';
                    }
                    $('#package_id').val(selection.packageId);

                    $('.dynamic-price').show();
                    $('.default-price').hide();
                    $('#checkout-steps').show();
                    syncTransportationStateFromCart();
                    showStep(1);

                    bootstrap.Modal.getOrCreateInstance(document.getElementById('addonSelectionModal')).hide();
                    window.pendingPackageSelection = null;
                });

                $(document).on('click', '#addonSelectionModalBody .addon-qty-dec', function() {
                    let id = $(this).data('id');
                    let valEl = $('#addonSelectionModalBody .addon-qty-val[data-id="' + id + '"]');
                    let current = parseInt(valEl.text(), 10) || 0;
                    let next = current > 0 ? current - 1 : 0;
                    valEl.text(next);
                    let unitPrice = parseFloat(valEl.data('price')) || 0;
                    $('#addonSelectionModalBody .addon-line-total-value[data-id="' + id + '"]').text(formatCurrency(unitPrice * next));
                });

                $(document).on('click', '#addonSelectionModalBody .addon-qty-inc', function() {
                    let id = $(this).data('id');
                    let valEl = $('#addonSelectionModalBody .addon-qty-val[data-id="' + id + '"]');
                    let current = parseInt(valEl.text(), 10) || 0;
                    let next = current + 1;
                    valEl.text(next);
                    let unitPrice = parseFloat(valEl.data('price')) || 0;
                    $('#addonSelectionModalBody .addon-line-total-value[data-id="' + id + '"]').text(formatCurrency(unitPrice * next));
                });

                function triggerEmbedCheckoutScrollFallback(delayMs) {
                    if (!document.body.classList.contains('embed-checkout-mode')) {
                        return;
                    }

                    setTimeout(function() {
                        let modalEl = document.getElementById('addonSelectionModal');
                        if (modalEl && modalEl.classList.contains('show')) {
                            return;
                        }

                        let toast = document.getElementById('cv-cart-toast');
                        let start = Date.now();
                        let maxWait = 2600;

                        let scrollToCheckout = function () {
                            let targetSection = document.getElementById('section-1') || document.querySelector('.checkout-section.active');
                            if (targetSection && targetSection.scrollIntoView) {
                                targetSection.scrollIntoView({ behavior: 'auto', block: 'start' });
                            } else {
                                window.scrollTo({ top: 0, behavior: 'auto' });
                            }
                        };

                        let runAfterToast = function () {
                            window.dispatchEvent(new CustomEvent('embed:category-toggle'));
                            setTimeout(function () {
                                window.parent.postMessage({ type: 'checkoutScrollToIframe' }, '*');
                            }, 220);
                            setTimeout(scrollToCheckout, 420);
                        };

                        if (!toast) {
                            runAfterToast();
                            return;
                        }

                        (function waitForToast() {
                            if (toast.classList.contains('is-visible')) {
                                runAfterToast();
                                return;
                            }
                            if ((Date.now() - start) >= maxWait) {
                                runAfterToast();
                                return;
                            }
                            setTimeout(waitForToast, 80);
                        })();
                    }, typeof delayMs === 'number' ? delayMs : 900);
                }

                function adjustAddonModalScrollArea(resetScroll) {
                    if (!document.body.classList.contains('embed-checkout-mode') || window.innerWidth > 991) {
                        return;
                    }

                    let modal = document.getElementById('addonSelectionModal');
                    if (!modal || !modal.classList.contains('show')) {
                        return;
                    }

                    let dialog = modal.querySelector('.addon-modal-dialog');
                    let content = modal.querySelector('.modal-content');
                    let body = modal.querySelector('.modal-body');
                    let header = modal.querySelector('.modal-header');
                    let footer = modal.querySelector('.modal-footer');
                    if (!dialog || !content || !body) {
                        return;
                    }

                    let viewportHeight = window.visualViewport && window.visualViewport.height ? window.visualViewport.height : window.innerHeight;
                    let verticalInset = 16;
                    let contentHeight = Math.max(220, Math.floor(viewportHeight - verticalInset));
                    let chromeHeight = (header ? header.offsetHeight : 0) + (footer ? footer.offsetHeight : 0);
                    let nextHeight = Math.max(160, contentHeight - chromeHeight);

                    dialog.style.alignItems = 'flex-start';
                    dialog.style.maxWidth = 'calc(100vw - 32px)';
                    dialog.style.width = 'calc(100vw - 32px)';
                    dialog.style.marginLeft = 'auto';
                    dialog.style.marginRight = 'auto';
                    dialog.style.marginTop = '8px';
                    dialog.style.marginBottom = '8px';

                    content.style.maxHeight = contentHeight + 'px';
                    content.style.display = 'flex';
                    content.style.flexDirection = 'column';

                    body.style.maxHeight = nextHeight + 'px';
                    body.style.minHeight = '0';
                    body.style.flex = '1 1 auto';
                    body.style.overflowY = 'auto';
                    body.style.webkitOverflowScrolling = 'touch';

                    if (resetScroll) {
                        body.scrollTop = 0;
                    }
                }

                document.getElementById('addonSelectionModal')?.addEventListener('shown.bs.modal', function() {
                    adjustAddonModalScrollArea(true);
                });
                document.getElementById('addonSelectionModal')?.addEventListener('hidden.bs.modal', function() {
                    let content = this.querySelector('.modal-content');
                    let body = this.querySelector('.modal-body');
                    let returnScrollY = parseInt(this.dataset.returnScrollY || '0', 10);
                    let shouldRestoreScrollOnHide = this.dataset.restoreScrollOnHide !== '0';
                    let shouldScrollToCheckoutOnHide = this.dataset.scrollToCheckoutOnHide === '1';
                    if (content) {
                        content.style.maxHeight = '';
                        content.style.display = '';
                        content.style.flexDirection = '';
                    }
                    if (body) {
                        body.style.maxHeight = '';
                        body.style.minHeight = '';
                        body.style.flex = '';
                        body.style.overflowY = '';
                        body.style.webkitOverflowScrolling = '';
                    }

                    if (document.body.classList.contains('embed-checkout-mode')) {
                        if (shouldRestoreScrollOnHide) {
                            window.scrollTo({ top: isNaN(returnScrollY) ? 0 : returnScrollY, behavior: 'auto' });
                        } else if (shouldScrollToCheckoutOnHide) {
                            // Success flow auto-scroll is handled by triggerEmbedCheckoutScrollFallback()
                            // so we avoid duplicate competing scrolls here.
                        }
                    }
                    delete this.dataset.returnScrollY;
                    delete this.dataset.restoreScrollOnHide;
                    delete this.dataset.scrollToCheckoutOnHide;

                    if (!document.querySelector('.modal.show')) {
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.overflowY = '';
                        document.body.style.paddingRight = '';
                        document.documentElement.style.overflow = '';
                        document.documentElement.style.overflowY = '';
                        document.querySelectorAll('.modal-backdrop').forEach((backdrop) => {
                            backdrop.remove();
                        });
                    }
                });
                window.addEventListener('resize', function() {
                    adjustAddonModalScrollArea(false);
                });
                if (window.visualViewport) {
                    window.visualViewport.addEventListener('resize', function() {
                        adjustAddonModalScrollArea(false);
                    });
                }
                setTimeout(function() {
                    refreshPackageAvailabilityForSelectedDate(false);
                }, 180);
            });

            // Step Management Functions
            let currentStep = 1;

            function showStep(stepNumber) {
                $('.checkout-section').removeClass('active').hide();

                $('#section-' + stepNumber).addClass('active').show();

                // Update step indicators
                $('.step').removeClass('active completed');
                for (let i = 1; i < stepNumber; i++) {
                    $('#step-' + i).addClass('completed');
                }
                $('#step-' + stepNumber).addClass('active');

                currentStep = stepNumber;
                syncTransportationStateFromCart();

                // Handle transportation logic for step 2
                if (stepNumber === 2) {
                    $('#transport-form').show();
                    $('#transport-confirmation').hide();
                }

                // Scroll to the top of the new step on all devices
                setTimeout(function() {
                    var el = document.getElementById('section-' + stepNumber);
                    if (el) {
                        var isEmbedMode = document.body.classList.contains('embed-checkout-mode');
                        var isIosDevice = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
                        var scrollBlock = (isEmbedMode && stepNumber === 2 && window.innerWidth <= 991)
                            ? 'center'
                            : 'start';
                        var scrollBehavior = (isEmbedMode && isIosDevice) ? 'auto' : 'smooth';
                        el.scrollIntoView({ behavior: scrollBehavior, block: scrollBlock });
                    }
                }, 50);
            }

            function validateStep(stepNumber) {
                let isValid = true;
                const requiredFields = [];
                let firstInvalidField = null;
                let alertMessage = 'Please fill in all required fields.';

                if (stepNumber === 1) {
                    // Validate package holder info
                    requiredFields.push(
                        '[name="package_first_name"]',
                        '[name="package_last_name"]',
                        '[name="package_phone"]',
                        '[name="package_email"]',
                        '[name="package_month"]',
                        '[name="package_day"]',
                        '[name="package_year"]'
                    );
                } else if (stepNumber === 2 && window.requiresTransportation) {
                    // Transportation form validation disabled - allow form to proceed
                    // requiredFields.push(
                    //     '[name="package_use_date"]',
                    //     '[name="transportation_pickup_time"]',
                    //     '[name="transportation_address"]',
                    //     '[name="transportation_phone"]',
                    //     '[name="transportation_guest"]'
                    // );
                }

                // Keep transportation guest default at 0 until user explicitly sets a value > 0.
                if (stepNumber === 2 && window.requiresTransportation) {
                    const guestField = $('[name="transportation_guest"]');
                    if (!guestField.val() || !Number.isFinite(parseInt(guestField.val(), 10))) {
                        guestField.val('0');
                    }
                }

                // Check required fields
                requiredFields.forEach(function(selector) {
                    const field = $(selector);
                    if (!field.val() || field.val().trim() === '') {
                        field.addClass('required-field');
                        isValid = false;
                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                    } else {
                        field.removeClass('required-field');
                    }
                });

                const skipTransportationInputs = stepNumber === 2 && window.requiresTransportation && $('#transportation_self_drive_ack').is(':checked');
                const requiresArrivalTime = stepNumber === 2 && (!window.requiresTransportation || skipTransportationInputs);

                if (isValid && stepNumber === 2 && window.requiresTransportation && !skipTransportationInputs && typeof validateTransportationScheduleClient === 'function') {
                    const scheduleValidation = validateTransportationScheduleClient();
                    if (!scheduleValidation.valid) {
                        isValid = false;
                        firstInvalidField = scheduleValidation.field || firstInvalidField;
                        alertMessage = scheduleValidation.message;
                    }
                }

                if (stepNumber === 2 && window.requiresTransportation && !skipTransportationInputs) {
                    const transportationGuestField = $('[name="transportation_guest"]');
                    const transportationGuestValue = parseInt(transportationGuestField.val(), 10);
                    if (!Number.isFinite(transportationGuestValue) || transportationGuestValue < 1) {
                        transportationGuestField.addClass('required-field');
                        isValid = false;
                        firstInvalidField = firstInvalidField || transportationGuestField;
                        alertMessage = 'Please enter Number of Guest(s) in Transportation (minimum 1).';
                    }
                }

                if (requiresArrivalTime) {
                    const transportationArrivalTimeField = $('[name="transportation_arrival_time"]');
                    const transportationArrivalTimeValue = transportationArrivalTimeField.val().trim();
                    if (!transportationArrivalTimeValue) {
                        transportationArrivalTimeField.addClass('required-field');
                        isValid = false;
                        firstInvalidField = firstInvalidField || transportationArrivalTimeField;
                        alertMessage = 'Please enter time of arrival.';
                    } else {
                        const arrivalSchedule = (typeof arrivalTransportationSchedule !== 'undefined')
                            ? arrivalTransportationSchedule
                            : transportationSchedule;
                        if (!isTimeWithinOperatingHours(transportationArrivalTimeValue, arrivalSchedule)) {
                            transportationArrivalTimeField.addClass('required-field');
                            isValid = false;
                            firstInvalidField = firstInvalidField || transportationArrivalTimeField;
                            alertMessage = 'Please Enter Valid Arrival Time.';
                        } else if (isTimeInPastForSelectedDate(transportationArrivalTimeValue, arrivalSchedule, false)) {
                            transportationArrivalTimeField.addClass('required-field');
                            isValid = false;
                            firstInvalidField = firstInvalidField || transportationArrivalTimeField;
                            alertMessage = 'Arrival time must be at least in the future for today\'s reservation date.';
                        }
                    }
                }

                if (!isValid && stepNumber === 2 && window.requiresTransportation && alertMessage === 'Please fill in all required fields.') {
                    alertMessage = 'Please complete the required transportation details before proceeding.';
                }

                // Require a valid country code selection on any visible phone country-code picker.
                // The picker's code box is a searchable text input; if the user typed search text and
                // did not pick a country (or typed an invalid code), block until a valid code is chosen.
                if (isValid) {
                    var __ccFields = document.querySelectorAll('.country-code-field');
                    for (var __ci = 0; __ci < __ccFields.length; __ci++) {
                        var __cc = __ccFields[__ci];
                        if (__cc.offsetParent === null) continue; // not visible in the current step
                        var __ccWrap = __cc.closest('.country-code-input');
                        if (!__ccWrap) continue;
                        var __opts = __ccWrap.querySelectorAll('.country-option');
                        if (!__opts.length) continue; // fail-safe: nothing to validate against
                        var __ccVal = (__cc.value || '').trim();
                        var __ccOk = false;
                        for (var __oi = 0; __oi < __opts.length; __oi++) {
                            if ((__opts[__oi].getAttribute('data-flag') + ' ' + __opts[__oi].getAttribute('data-code')) === __ccVal) { __ccOk = true; break; }
                        }
                        if (!__ccOk) {
                            __cc.style.borderColor = '#ff6b6b';
                            isValid = false;
                            firstInvalidField = $(__cc);
                            alertMessage = 'Please select a valid country code from the list (search and click your country, or type the full +code in the phone box).';
                            break;
                        }
                        __cc.style.borderColor = '';
                    }
                }

                if (!isValid) {
                    if (typeof window.showToast === 'function') {
                        window.showToast('Action Required', alertMessage, 'fas fa-exclamation-circle');
                    } else {
                        alert(alertMessage);
                    }
                    if (firstInvalidField && firstInvalidField.length) {
                        var isMobile = window.matchMedia('(max-width: 767px)').matches;
                        var isTimeInput = firstInvalidField.is('[name="transportation_pickup_time"], [name="transportation_arrival_time"], #Pick-up-time, #Arrival-time');

                        if (firstInvalidField.is('[name="transportation_pickup_time"], #Pick-up-time')) {
                            firstInvalidField.prop('disabled', false).prop('readonly', false)
                                .removeAttr('disabled').removeAttr('readonly');
                        }

                        if (isMobile && isTimeInput) {
                            var timeEl = firstInvalidField[0];
                            var rect = timeEl.getBoundingClientRect();
                            var desiredTop = 100;
                            var targetScrollY = window.scrollY + rect.top - desiredTop;
                            if (targetScrollY < 0) targetScrollY = 0;

                            window.scrollTo({ top: targetScrollY, behavior: 'smooth' });

                            setTimeout(function() {
                                if (timeEl && timeEl._flatpickr && typeof timeEl._flatpickr.open === 'function') {
                                    try { timeEl._flatpickr.open(); } catch(e) {}
                                } else if (typeof timeEl.focus === 'function') {
                                    try { timeEl.focus(); } catch(e) {}
                                }
                            }, 350);
                        } else {
                            firstInvalidField[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                            if (firstInvalidField.is('[name="transportation_pickup_time"]')) {
                                var timeInput = firstInvalidField[0];
                                if (timeInput && timeInput._flatpickr) {
                                    try { timeInput._flatpickr.close(); } catch (e) {}
                                }
                            }
                        }
                    }
                }
                    return isValid;
            }

            // Navigation Event Handlers
            $(document).ready(function() {

                // Next to Transportation
                $('#next-to-transport').click(function() {
                    if (validateStep(1)) {
                        showStep(2);
                    }
                });

                $(document).on('change', '#transportation_self_drive_ack', function() {
                    syncDerivedTransportationFields();

                updateTransportationSelfDriveState();
                });

                // Previous to Package from Transportation confirmation
                $('#prev-to-package').click(function() {
                    showStep(1);
                });

                // Previous to Package from Transportation form  
                $('#prev-to-package-from-form').click(function() {
                    showStep(1);
                });

                // Next to Payment from Transportation confirmation
                $('#next-to-payment-from-confirm').click(function() {
                    if (validateStep(2)) {
                        populatePaymentFields();
                        showStep(3);
                    }
                });

                // Next to Payment from Transportation form
                $('#next-to-payment').click(function() {
                    if (validateStep(2)) {
                        populatePaymentFields();
                        showStep(3);
                    }
                });

                // Previous to Transportation from Payment
                $('#prev-to-transport').click(function() {
                    showStep(2);
                });

                // Remove required field styling on input
                $(document).on('input change', 'input, select, textarea', function() {
                    $(this).removeClass('required-field');
                });
            });
        </script>

        <input type="hidden" id="gratuity" value="{{ $data->gratuity_fee }}">

        <input type="hidden" id="refundable" value="{{ $data->refundable_fee }}">

        <input type="hidden" id="sales_tax" value="{{ $data->sales_tax_fee ?? 10 }}">

        <input type="hidden" id="service_charge" value="{{ $data->service_charge_fee ?? 10 }}">

        <input type="hidden" id="processing_fee" value="{{ (float) ($data->processing_fee ?? 0) }}">

        <input type="hidden" id="processing_fee_type" value="{{ $data->processing_fee_type ?? 'percentage' }}">

        <script>
            function showInfoTooltipModal(title, description) {
                const modalElement = document.getElementById('infoTooltipModal');
                if (!modalElement) {
                    return;
                }

                const titleElement = modalElement.querySelector('.modal-title');
                const bodyElement = modalElement.querySelector('.modal-body');

                if (titleElement) {
                    titleElement.textContent = title || 'Details';
                }

                if (bodyElement) {
                    bodyElement.innerHTML = `<p style="margin:0;">${description || ''}</p>`;
                }

                if (window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
                    return;
                }

                if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                    window.jQuery(modalElement).modal('show');
                }
            }

            function openModal() {
                // Get the description from the clicked addon
                const description = event.target.closest('.addon-item').querySelector('label').getAttribute('data-description');
                const title = event.target.closest('.addon-item').querySelector('label').getAttribute('data-title');

                showInfoTooltipModal(title, description);

            }

            function openPackageModal(triggerElement) {
                const trigger = triggerElement || (window.event ? window.event.target : null);
                if (!trigger || !trigger.closest) {
                    return;
                }

                const card = trigger.closest('.vip-card');
                if (!card) {
                    return;
                }

                const legacyMeta = card.querySelector('.items');
                const triggerTitle = String(trigger.getAttribute('data-title') || '').trim();
                const triggerTooltip = String(trigger.getAttribute('data-tooltip') || '').trim();
                const title = (
                    triggerTitle ||
                    (legacyMeta && legacyMeta.getAttribute('data-title')) ||
                    (card.querySelector('.cv-pkg-title') && card.querySelector('.cv-pkg-title').textContent) ||
                    'Package Details'
                ).trim();
                const description = (
                    triggerTooltip ||
                    (legacyMeta && legacyMeta.getAttribute('data-description')) ||
                    (card.querySelector('.cv-pkg-desc') && card.querySelector('.cv-pkg-desc').textContent) ||
                    'No additional details available for this package.'
                ).trim();

                showInfoTooltipModal(title, description);
            }

            document.addEventListener('click', function(evt) {
                const tooltipTrigger = evt.target.closest('.cv-pkg-tooltip-trigger');
                if (!tooltipTrigger) {
                    return;
                }

                evt.preventDefault();
                evt.stopPropagation();
                openPackageModal(tooltipTrigger);
            });

            function addToTotal(price, name, id) {
                // This function is now handled by cart system
                // Keeping for backward compatibility
            }

            function transportation() {
                console.log('sss');
                if (event.target.checked) {
                    $('.transport').show();
                } else {
                    $('.transport').hide();
                }
            }
        </script>

        <script>
            $('.package_number_of_guestss').on('change', function() {
                ensureCartArray();
                var $field = $(this);
                var selectedValue = parseInt($field.val(), 10) || 1;
                var packageId = $field.data('id');
                var useDate = getSelectedUseDate();

                $.get('/{{ $data->slug }}/package/' + packageId + '/capacity', {
                    use_date: useDate,
                    requested_quantity: selectedValue
                }).done(function(response) {
                    var maxSelectable = parseInt(response.max_select, 10);
                    if (!Number.isFinite(maxSelectable)) {
                        maxSelectable = parseInt(response.capacity, 10) || 1;
                    }

                    if (selectedValue > maxSelectable) {
                        updateGuestControlAvailability($field, maxSelectable, response.message || 'Sold Out for Selected Date');
                        showGuestFieldError($field, response.message || 'The quantity you entered is unavailable for the selected date. Please choose a lower number.');
                        return;
                    }

                    clearGuestFieldError($field);
                    $('.package_number_of_guest').val(String(selectedValue));
                    var pkg = window.cart.find(function(p) { return String(p.packageId) === String(packageId); });
                    if (pkg) {
                        pkg.guests = selectedValue;
                        pkg.isMultiple = parseMultipleFlag($field.data('multiple'));
                        window.renderCart();
                        window.calculateCartTotal();
                    }
                }).fail(function() {
                    showGuestFieldError($field, 'We could not verify availability right now. Please try again.');
                });
            });
        </script>



        <script>
            // Removed old recalculateTotals - now using calculateCartTotal

            $('#applyPromoBtn').on('click', function() {
                let code = $('#promo_code').val().trim();
                if (!code) return;

                var promoSource = '{{ !empty($affiliateReferral) ? 'affiliate' : 'club' }}';
                var ownerSlug = '{{ !empty($affiliateReferral) ? $affiliateReferral->slug : '' }}';
                var cartItems = Array.isArray(window.cart) ? window.cart : [];
                var packageIds = [];
                var subtotal = 0;
                var totalQty = 0;

                cartItems.forEach(function(pkg) {
                    var pkgId = parseInt(pkg.packageId, 10) || 0;
                    if (pkgId > 0 && packageIds.indexOf(pkgId) === -1) {
                        packageIds.push(pkgId);
                    }

                    var guests = parseInt(pkg.guests, 10) || 1;
                    var billableGuests = (pkg.isMultiple === true || pkg.isMultiple === 1 || pkg.isMultiple === '1') ? guests : 1;
                    subtotal += (parseFloat(pkg.packagePrice) || 0) * billableGuests;
                    subtotal += (pkg.addons || []).reduce(function(sum, addon) { return sum + (parseFloat(addon.price) || 0); }, 0);
                    totalQty += guests;
                });

                $.get('/{{ $data->slug }}/check/' + encodeURIComponent(code), {
                    source: promoSource,
                    owner_slug: ownerSlug,
                    package_ids: packageIds.join(','),
                    subtotal: subtotal.toFixed(2),
                    total_qty: totalQty
                }, function(res) {
                    if (res.valid === false || res.valid === "false") {
                        window.cartCoupon = null;
                        alert(res.message || 'Invalid promo code');
                        window.calculateCartTotal();
                    } else {
                        window.cartCoupon = {
                            code: code,
                            id: res.id,
                            discount: parseFloat(res.discount),
                            type: res.type || 'percentage'
                        };
                        $('#applyPromoBtn').prop('disabled', true);
                        $('.promo_code').val(res.id);
                        window.calculateCartTotal();
                    }
                });
            });

            // Recalculate totals on page load and after any price change
            // $(document).on('change', '.vip-btn', function () {
            //     setTimeout(recalculateTotals, 100); // Wait for DOM updates
            // });
        </script>

        <script>
            // Replace this with your country select's ID
            const countrySelectId = 'country';
            const stateSelectId = 'st-pv';

            // Listen for country change
            $(document).on('change', `#${countrySelectId}`, function() {
                const country = $(this).val();
                const $state = $(`#${stateSelectId}`);
                $state.html('<option value="">Loading...</option>');
                if (!country) {
                    $state.html('<option value="">Select State/Province</option>');
                    return;
                }
                // Example API for US states: https://countriesnow.space/api/v0.1/countries/states
                // You can use another API if you prefer
                $.ajax({
                    url: 'https://countriesnow.space/api/v0.1/countries/states',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        country: country
                    }),
                    success: function(res) {
                        if (res && res.data && res.data.states && res.data.states.length > 0) {
                            let options =
                                '<option value="null" selected disabled>Select State/Province</option>';
                            res.data.states.forEach(function(state) {
                                options += `<option value="${state.name}">${state.name}</option>`;
                            });
                            $state.html(options);
                        } else {
                            $state.html('<option value="null" selected disabled>No states found</option>');
                        }
                    },
                    error: function() {
                        $state.html('<option value="null" selected disabled>Error loading states</option>');
                    }
                });
            });
        </script>

        <script>
            // Auto-discount logic: wrap calculateCartTotal to fetch and apply automatic discounts
            (function () {
                var _origCalcCartTotal = window.calculateCartTotal;
                var _autoDiscountTimer = null;
                var promoSource = '{{ !empty($affiliateReferral) ? 'affiliate' : 'club' }}';
                var ownerSlug = '{{ !empty($affiliateReferral) ? $affiliateReferral->slug : '' }}';
                var siteSlug = '{{ $data->slug }}';

                function fetchAutoDiscount() {
                    var cartItems = Array.isArray(window.cart) ? window.cart : [];
                    if (cartItems.length === 0) {
                        if (window.cartCoupon && window.cartCoupon.isAutomatic) {
                            window.cartCoupon = null;
                            _origCalcCartTotal();
                        }
                        return;
                    }
                    var packageIds = [];
                    var subtotal = 0;
                    var totalQty = 0;
                    cartItems.forEach(function (pkg) {
                        var pkgId = parseInt(pkg.packageId, 10) || 0;
                        if (pkgId > 0 && packageIds.indexOf(pkgId) === -1) packageIds.push(pkgId);
                        var guests = parseInt(pkg.guests, 10) || 1;
                        var billable = (pkg.isMultiple === true || pkg.isMultiple === 1 || pkg.isMultiple === '1') ? guests : 1;
                        subtotal += (parseFloat(pkg.packagePrice) || 0) * billable;
                        subtotal += (pkg.addons || []).reduce(function (s, a) { return s + (parseFloat(a.price) || 0); }, 0);
                        totalQty += guests;
                    });
                    $.get('/' + siteSlug + '/auto-discounts', {
                        source: promoSource,
                        owner_slug: ownerSlug,
                        package_ids: packageIds.join(','),
                        subtotal: subtotal.toFixed(2),
                        total_qty: totalQty
                    }, function (res) {
                        if (res.valid) {
                            window.cartCoupon = {
                                code: res.name,
                                id: res.id,
                                discount: parseFloat(res.discount),
                                type: res.type || 'percentage',
                                isAutomatic: true
                            };
                        } else if (window.cartCoupon && window.cartCoupon.isAutomatic) {
                            window.cartCoupon = null;
                        }
                        _origCalcCartTotal();
                    });
                }

                window.calculateCartTotal = function () {
                    _origCalcCartTotal();
                    if (!window.cartCoupon || window.cartCoupon.isAutomatic) {
                        clearTimeout(_autoDiscountTimer);
                        _autoDiscountTimer = setTimeout(fetchAutoDiscount, 400);
                    }
                };
            })();
        </script>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            function prepareCheckoutCartPayload(form) {
                syncCheckoutCartFields();
            }

            (function initRawCardNumberFormatting() {
                function detectCardMeta(digits) {
                    var number = String(digits || '');

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
                    var cursor = 0;
                    var parts = [];

                    for (var i = 0; i < grouping.length && cursor < digits.length; i++) {
                        var size = grouping[i];
                        var chunk = digits.slice(cursor, cursor + size);
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

                    var digits = String(input.value || '').replace(/\D/g, '');
                    var meta = detectCardMeta(digits);
                    var maxDigits = Math.min(meta.maxLen, 16);
                    var allowedLengths = meta.validLens.filter(function(len) { return len <= maxDigits; });

                    if (allowedLengths.length === 0) {
                        allowedLengths = [maxDigits];
                    }

                    if (digits.length > maxDigits) {
                        digits = digits.slice(0, maxDigits);
                    }

                    input.value = formatWithGrouping(digits, meta.grouping);
                    input.maxLength = formatWithGrouping(new Array(maxDigits + 1).join('9'), meta.grouping).length;
                    input.setAttribute('inputmode', 'numeric');
                    input.setAttribute('autocomplete', 'cc-number');
                    input.setCustomValidity('');

                    if (digits.length > 0 && allowedLengths.indexOf(digits.length) === -1) {
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

                var cardFields = document.querySelectorAll('input[name="card_number"]');
                cardFields.forEach(function(field) { bindField(field); });

                var form = document.getElementById('payment-form');
                if (form) {
                    form.addEventListener('submit', function(event) {
                        if (form.dataset.zeroTotalCheckout === '1') {
                            return;
                        }

                        var inputs = form.querySelectorAll('input[name="card_number"]');
                        var hasInvalid = false;

                        inputs.forEach(function(input) {
                            applyMask(input);
                            if (!input.checkValidity()) {
                                hasInvalid = true;
                            }
                            input.value = String(input.value || '').replace(/\D/g, '');
                        });

                        if (hasInvalid) {
                            event.preventDefault();
                            var first = inputs[0];
                            if (first) {
                                first.reportValidity();
                            }
                        }
                    });
                }
            })();

            // Package form: Use click event on button (not form submit) for iOS compatibility
            // iOS mobile Safari has issues with form submit events, but click events work reliably
            (function() {
                const submitBtn = document.getElementById('submitBtn');
                if (!submitBtn) return;

                const form = submitBtn.closest('form');
                if (!form) return;

                if (typeof window.updateCheckoutPaymentRequirement === 'function') {
                    window.updateCheckoutPaymentRequirement();
                }

                submitBtn.addEventListener('click', function(e) {
                    // Check SMS consent checkbox for package form
                    const smsConsentPackage = document.getElementById('smsConsent');
                    if (!smsConsentPackage || !smsConsentPackage.checked) {
                        e.preventDefault();
                        const errorMsg = document.getElementById('validation-error-msg-package') || document.createElement('div');
                        if (!errorMsg.id) {
                            errorMsg.id = 'validation-error-msg-package';
                            errorMsg.style.cssText = 'color: #ff6b6b; padding: 12px; margin: 10px 0; font-weight: 600; text-align: center; background: rgba(255, 107, 107, 0.1); border-radius: 6px; border-left: 4px solid #ff6b6b;';
                            form.parentElement.insertBefore(errorMsg, form);
                        }
                        errorMsg.textContent = 'Please agree to receive SMS communications regarding your reservation, transportation updates, VIP services, and related notifications.';
                        errorMsg.style.display = 'block';
                        errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }

                    // Check terms consent checkbox for package form
                    const termsConsentPackage = document.getElementById('termsConsent');
                    if (!termsConsentPackage || !termsConsentPackage.checked) {
                        e.preventDefault();
                        const errorMsg = document.getElementById('validation-error-msg-package') || document.createElement('div');
                        if (!errorMsg.id) {
                            errorMsg.id = 'validation-error-msg-package';
                            errorMsg.style.cssText = 'color: #ff6b6b; padding: 12px; margin: 10px 0; font-weight: 600; text-align: center; background: rgba(255, 107, 107, 0.1); border-radius: 6px; border-left: 4px solid #ff6b6b;';
                            form.parentElement.insertBefore(errorMsg, form);
                        }
                        errorMsg.textContent = 'Please accept the Terms of Service.';
                        errorMsg.style.display = 'block';
                        errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }

                    // Validation passed - show processing overlay
                    showCheckoutProcessingOverlay();

                    // Clean card number - remove spaces and special characters for Authorize.net
                    const cardNumberField = form.querySelector('input[name="card_number"]');
                    if (cardNumberField && cardNumberField.value) {
                        cardNumberField.value = cardNumberField.value.replace(/\s+/g, '').replace(/[^0-9]/g, '');
                    }

                    prepareCheckoutCartPayload(form);

                    // Replace visible phone fields with E.164 values before submission
                    const phoneFieldsToSync = [
                        { visible: 'package_phone', e164: 'package_phone_e164' },
                        { visible: 'reservation_phone', e164: 'reservation_phone_e164' },
                        { visible: 'transportation_phone', e164: 'transportation_phone_e164' }
                    ];

                    phoneFieldsToSync.forEach(pair => {
                        const e164Field = form.querySelector(`input[name="${pair.e164}"]`);
                        const visibleField = form.querySelector(`input[name="${pair.visible}"]`);
                        if (e164Field && visibleField && e164Field.value) {
                            // Use E.164 format for submission
                            visibleField.value = e164Field.value;
                        }
                    });

                    // Submit form after a tiny delay for iOS compatibility
                    setTimeout(() => {
                        form.submit();
                    }, 50);
                });
            })();

            const dailyOperatingHoursMap = @json($data->getDailyOperatingHoursMap());

            const transportationSchedule = {
                operatingDays: @json(array_values(array_map('strtolower', (array) ($data->operating_days ?? [])))),
                startTime: @json($data->pickup_start_time),
                endTime: @json($data->pickup_end_time),
            };

            const arrivalTransportationSchedule = {
                startTime: @json($data->operating_start_time),
                endTime: @json($data->operating_end_time),
            };

            function getDayOfWeekFromDateString(dateStr) {
                if (!dateStr || typeof dateStr !== 'string') return null;
                const parts = dateStr.trim().split('-');
                if (parts.length !== 3) return null;
                const year = parseInt(parts[0], 10);
                const month = parseInt(parts[1], 10) - 1;
                const day = parseInt(parts[2], 10);
                const dateObj = new Date(year, month, day);
                if (isNaN(dateObj.getTime())) return null;
                const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                return days[dateObj.getDay()];
            }

            function updateDynamicSchedulesForSelectedDate(dateStr) {
                const selectedDate = dateStr || (typeof getSelectedUseDate === 'function' ? getSelectedUseDate() : String($('#package_use_date').val() || '').trim());
                const dayName = getDayOfWeekFromDateString(selectedDate);
                if (dayName && dailyOperatingHoursMap && dailyOperatingHoursMap[dayName]) {
                    const dayConfig = dailyOperatingHoursMap[dayName];
                    transportationSchedule.startTime = dayConfig.pickup_start_time || @json($data->pickup_start_time);
                    transportationSchedule.endTime = dayConfig.pickup_end_time || @json($data->pickup_end_time);
                    arrivalTransportationSchedule.startTime = dayConfig.operating_start_time || @json($data->operating_start_time);
                    arrivalTransportationSchedule.endTime = dayConfig.operating_end_time || @json($data->operating_end_time);
                } else {
                    transportationSchedule.startTime = @json($data->pickup_start_time);
                    transportationSchedule.endTime = @json($data->pickup_end_time);
                    arrivalTransportationSchedule.startTime = @json($data->operating_start_time);
                    arrivalTransportationSchedule.endTime = @json($data->operating_end_time);
                }
                if (typeof updateTransportationHoursDisplay === 'function') {
                    updateTransportationHoursDisplay();
                }
            }

            function parseTimeToMinutes(timeValue) {
                if (!timeValue) {
                    return null;
                }

                const trimmedValue = String(timeValue).trim().replace(/[\u00A0\u202F]/g, ' ');
                const twelveHourMatch = trimmedValue.match(/^(\d{1,2}):(\d{2})(?::\d{2})?\s*([AaPp])\.?\s*[Mm]\.?$/);
                if (twelveHourMatch) {
                    let hours = parseInt(twelveHourMatch[1], 10) % 12;
                    const minutes = parseInt(twelveHourMatch[2], 10);
                    if (twelveHourMatch[3].toUpperCase() === 'P') {
                        hours += 12;
                    }

                    return (hours * 60) + minutes;
                }

                const twentyFourHourMatch = trimmedValue.match(/^(\d{1,2}):(\d{2})(?::\d{2})?$/);
                if (twentyFourHourMatch) {
                    return (parseInt(twentyFourHourMatch[1], 10) * 60) + parseInt(twentyFourHourMatch[2], 10);
                }

                return null;
            }

            function isDateAllowed(dateValue) {
                if (!transportationSchedule.operatingDays.length) {
                    return true;
                }

                const date = dateValue instanceof Date ? dateValue : new Date(`${dateValue}T00:00:00`);
                if (Number.isNaN(date.getTime())) {
                    return false;
                }

                const dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                return transportationSchedule.operatingDays.includes(dayNames[date.getDay()]);
            }

            function getFirstAvailableDate(startDate = new Date()) {
                const baseDate = new Date(startDate);
                baseDate.setHours(0, 0, 0, 0);

                for (let index = 0; index < 366; index += 1) {
                    const candidate = new Date(baseDate);
                    candidate.setDate(baseDate.getDate() + index);
                    if (isDateAllowed(candidate)) {
                        return candidate;
                    }
                }

                return baseDate;
            }

            function isTimeWithinOperatingHours(timeValue, schedule) {
                const inputMinutes = parseTimeToMinutes(timeValue);
                if (inputMinutes === null) {
                    return false;
                }

                const activeSchedule = schedule || transportationSchedule;
                const startMinutes = parseTimeToMinutes(activeSchedule.startTime);
                const endMinutes = parseTimeToMinutes(activeSchedule.endTime);

                if (startMinutes === null || endMinutes === null) {
                    return true;
                }

                if (endMinutes < startMinutes) {
                    return inputMinutes >= startMinutes || inputMinutes <= endMinutes;
                }

                return inputMinutes >= startMinutes && inputMinutes <= endMinutes;
            }

            function isTimeInPastForSelectedDate(timeValue, schedule, isPickup) {
                const timeMinutes = parseTimeToMinutes(timeValue);
                if (timeMinutes === null) {
                    return false;
                }

                const useDateStr = getSelectedUseDate();
                if (!useDateStr) {
                    return false;
                }

                const clubTz = @json($data->resolved_timezone ?? 'America/Los_Angeles');
                let nowClub;
                try {
                    const nowClubStr = new Date().toLocaleString('en-US', { timeZone: clubTz });
                    nowClub = new Date(nowClubStr);
                } catch (e) {
                    nowClub = new Date();
                }

                const nowYear = nowClub.getFullYear();
                const nowMonth = String(nowClub.getMonth() + 1).padStart(2, '0');
                const nowDate = String(nowClub.getDate()).padStart(2, '0');
                const todayInClub = nowYear + '-' + nowMonth + '-' + nowDate;

                const dateParts = useDateStr.split('-');
                if (dateParts.length !== 3) {
                    return false;
                }
                const reqYear = parseInt(dateParts[0], 10);
                const reqMonth = parseInt(dateParts[1], 10) - 1;
                const reqDay = parseInt(dateParts[2], 10);

                const activeSchedule = schedule || (isPickup ? transportationSchedule : arrivalTransportationSchedule);
                const startMinutes = parseTimeToMinutes(activeSchedule ? activeSchedule.startTime : null);
                const endMinutes = parseTimeToMinutes(activeSchedule ? activeSchedule.endTime : null);
                const isOvernight = (startMinutes !== null && endMinutes !== null && endMinutes < startMinutes);
                const cutoffMinutes = (endMinutes !== null) ? endMinutes : 360;
                const isEarlyMorning = (timeMinutes <= cutoffMinutes);

                const nowClubMinutes = (nowClub.getHours() * 60) + nowClub.getMinutes();
                const isNowEarlyMorning = (nowClubMinutes <= cutoffMinutes);
                const isSelectedDateToday = (useDateStr === todayInClub);

                let targetDate = new Date(reqYear, reqMonth, reqDay, 0, 0, 0);
                if ((isOvernight && timeMinutes <= endMinutes) || isEarlyMorning) {
                    if (!(isNowEarlyMorning && isSelectedDateToday)) {
                        targetDate.setDate(targetDate.getDate() + 1);
                    }
                }
                targetDate.setMinutes(timeMinutes);

                if (isPickup) {
                    return targetDate.getTime() < (nowClub.getTime() + 15 * 60 * 1000);
                }

                return targetDate.getTime() < nowClub.getTime();
            }

            function formatMinutesAsTwelveHour(totalMinutes) {
                const normalizedMinutes = ((totalMinutes % 1440) + 1440) % 1440;
                const hours24 = Math.floor(normalizedMinutes / 60);
                const minutes = normalizedMinutes % 60;
                const meridiem = hours24 >= 12 ? 'PM' : 'AM';
                const hours12 = (hours24 % 12) || 12;
                return String(hours12) + ':' + String(minutes).padStart(2, '0') + ' ' + meridiem;
            }

            function formatMinutesAsTwentyFourHour(totalMinutes) {
                const normalizedMinutes = ((totalMinutes % 1440) + 1440) % 1440;
                const hours24 = Math.floor(normalizedMinutes / 60);
                const minutes = normalizedMinutes % 60;
                return String(hours24).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
            }

            function normalizeTimeToFiveMinutes(timeValue, outputMode) {
                const parsedMinutes = parseTimeToMinutes(timeValue);
                if (parsedMinutes === null) {
                    return null;
                }

                const roundedMinutes = Math.ceil(parsedMinutes / 5) * 5;
                if (outputMode === '24h') {
                    return formatMinutesAsTwentyFourHour(roundedMinutes);
                }

                return formatMinutesAsTwelveHour(roundedMinutes);
            }

            function formatOperatingTimeForDisplay(timeValue) {
                const minutes = parseTimeToMinutes(timeValue);
                if (minutes === null) {
                    return String(timeValue || '').trim();
                }

                return formatMinutesAsTwelveHour(minutes);
            }

            function formatGroupDayRanges(daysInGroup) {
                if (!daysInGroup || daysInGroup.length === 0) return '';

                const contiguousSubgroups = [];
                let currentSub = [daysInGroup[0]];

                for (let i = 1; i < daysInGroup.length; i++) {
                    if (daysInGroup[i].index === daysInGroup[i - 1].index + 1) {
                        currentSub.push(daysInGroup[i]);
                    } else {
                        contiguousSubgroups.push(currentSub);
                        currentSub = [daysInGroup[i]];
                    }
                }
                contiguousSubgroups.push(currentSub);

                const formattedParts = contiguousSubgroups.map(sub => {
                    if (sub.length === 1) {
                        return sub[0].label;
                    } else {
                        return sub[0].label + '-' + sub[sub.length - 1].label;
                    }
                });

                return formattedParts.join(', ');
            }

            function renderGroupedScheduleBadge(scheduleType, targetElementId) {
                const targetEl = document.getElementById(targetElementId);
                if (!targetEl) return;

                if (!dailyOperatingHoursMap || Object.keys(dailyOperatingHoursMap).length === 0) {
                    const sched = scheduleType === 'pickup' ? transportationSchedule : arrivalTransportationSchedule;
                    const startLabel = formatOperatingTimeForDisplay(sched.startTime);
                    const endLabel = formatOperatingTimeForDisplay(sched.endTime);
                    if (startLabel && endLabel) {
                        const title = scheduleType === 'pickup' ? 'Pickup Hours' : 'Operating Hours';
                        targetEl.innerHTML = `
                            <div class="hours-title"><i class="fas fa-clock"></i> ${title}</div>
                            <div class="hours-list">
                                <div class="hours-line">
                                    <span class="hours-time">${startLabel} to ${endLabel}</span>
                                </div>
                            </div>
                        `;
                        targetEl.style.display = 'block';
                    } else {
                        targetEl.style.display = 'none';
                    }
                    return;
                }

                const dayKeys = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                const dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

                const daySchedules = [];
                dayKeys.forEach((key, idx) => {
                    const config = dailyOperatingHoursMap[key];
                    if (!config || config.enabled === false) {
                        return;
                    }

                    let startVal, endVal;
                    if (scheduleType === 'pickup') {
                        startVal = config.pickup_start_time || @json($data->pickup_start_time);
                        endVal = config.pickup_end_time || @json($data->pickup_end_time);
                    } else {
                        startVal = config.operating_start_time || @json($data->operating_start_time);
                        endVal = config.operating_end_time || @json($data->operating_end_time);
                    }

                    const startDisp = formatOperatingTimeForDisplay(startVal);
                    const endDisp = formatOperatingTimeForDisplay(endVal);

                    if (startDisp && endDisp) {
                        daySchedules.push({
                            index: idx,
                            key: key,
                            label: dayLabels[idx],
                            timeStr: startDisp + ' to ' + endDisp
                        });
                    }
                });

                if (daySchedules.length === 0) {
                    targetEl.style.display = 'none';
                    return;
                }

                const groupsByTime = {};
                const timeOrder = [];
                daySchedules.forEach(item => {
                    if (!groupsByTime[item.timeStr]) {
                        groupsByTime[item.timeStr] = [];
                        timeOrder.push(item.timeStr);
                    }
                    groupsByTime[item.timeStr].push(item);
                });

                let htmlLines = '';
                timeOrder.forEach(timeStr => {
                    const daysInGroup = groupsByTime[timeStr];
                    const dayRangeStr = formatGroupDayRanges(daysInGroup);
                    htmlLines += `
                        <div class="hours-line">
                            <span class="hours-days">${dayRangeStr}</span>
                            <span class="hours-time">${timeStr}</span>
                        </div>
                    `;
                });

                const badgeTitle = scheduleType === 'pickup' ? 'Pickup Hours' : 'Operating Hours';
                targetEl.innerHTML = `
                    <div class="hours-title"><i class="fas fa-clock"></i> ${badgeTitle}</div>
                    <div class="hours-list">${htmlLines}</div>
                `;
                targetEl.style.display = 'block';
            }

            function updateTransportationHoursDisplay() {
                renderGroupedScheduleBadge('pickup', 'pickup-hours-badge');
                renderGroupedScheduleBadge('operating', 'arrival-hours-badge');
            }

            $(document).on('change', '#package_use_date, #package_use_date_iframe, .package_use_date', function() {
                var selectedVal = String($(this).val() || '').trim();
                updateDynamicSchedulesForSelectedDate(selectedVal);

                var pickupEl = document.querySelector('input[name="transportation_pickup_time"]');
                if (pickupEl && pickupEl.value) {
                    if (isTimeInPastForSelectedDate(pickupEl.value, transportationSchedule, true) || !isTimeWithinOperatingHours(pickupEl.value, transportationSchedule)) {
                        pickupEl.value = '';
                    }
                }
                var arrivalEl = document.querySelector('input[name="transportation_arrival_time"]');
                if (arrivalEl && arrivalEl.value) {
                    if (isTimeInPastForSelectedDate(arrivalEl.value, arrivalTransportationSchedule, false) || !isTimeWithinOperatingHours(arrivalEl.value, arrivalTransportationSchedule)) {
                        arrivalEl.value = '';
                    }
                }
            });

            updateDynamicSchedulesForSelectedDate();

            function validateTransportationScheduleClient() {
                const pickupDateField = $('#package_use_date');
                const pickupTimeField = $('[name="transportation_pickup_time"]');
                const pickupLocationField = $('[name="transportation_address"]');
                const contactPhoneField = $('[name="transportation_phone"]');
                const pickupDate = pickupDateField.val().trim();
                const pickupTime = pickupTimeField.val().trim();
                const pickupLocation = pickupLocationField.val().trim();
                const contactPhone = contactPhoneField.val().trim();

                if (!pickupDate) {
                    pickupDateField.addClass('required-field');
                    return {
                        valid: false,
                        field: pickupDateField,
                        message: 'Please complete the required transportation details before proceeding.'
                    };
                }

                if (!isDateAllowed(pickupDate)) {
                    pickupDateField.addClass('required-field');
                    return {
                        valid: false,
                        field: pickupDateField,
                        message: 'Selected club is closed on that date.'
                    };
                }

                if (!pickupTime) {
                    pickupTimeField.prop('disabled', false).prop('readonly', false);
                    pickupTimeField.addClass('required-field');
                    return {
                        valid: false,
                        field: pickupTimeField,
                        message: 'Please complete the required transportation details before proceeding.'
                    };
                }

                if (!isTimeWithinOperatingHours(pickupTime)) {
                    pickupTimeField.prop('disabled', false).prop('readonly', false);
                    pickupTimeField.addClass('required-field');
                    return {
                        valid: false,
                        field: pickupTimeField,
                        message: 'Please Enter Valid Pickup Time.'
                    };
                }

                if (isTimeInPastForSelectedDate(pickupTime, transportationSchedule, true)) {
                    pickupTimeField.prop('disabled', false).prop('readonly', false);
                    pickupTimeField.addClass('required-field');
                    return {
                        valid: false,
                        field: pickupTimeField,
                        message: 'Pickup time must be at least 15 minutes from the current time for today\'s reservation date.'
                    };
                }

                if (!pickupLocation) {
                    pickupLocationField.addClass('required-field');
                    return {
                        valid: false,
                        field: pickupLocationField,
                        message: 'Please enter the pick-up location.'
                    };
                }



                return { valid: true, field: null, message: '' };
            }

            // Flatpickr time picker for pick-up time — visual picker on all devices including iOS.
            // Pick-up time picker: desktop uses Flatpickr, mobile uses the native time control.
            (function () {
                var el = document.querySelector('input[name="transportation_pickup_time"]');
                if (!el) return;
                function to24h(t) {
                    if (!t) return null;
                    var m = String(t).trim().match(/^(\d{1,2}):(\d{2})\s*(AM|PM)?$/i);
                    if (!m) return null;
                    var hh = parseInt(m[1], 10);
                    var mm = parseInt(m[2], 10);
                    if (m[3]) {
                        var mer = m[3].toUpperCase();
                        if (mer === 'PM' && hh < 12) hh += 12;
                        else if (mer === 'AM' && hh === 12) hh = 0;
                    }
                    return String(hh).padStart(2, '0') + ':' + String(mm).padStart(2, '0');
                }
                var minT = to24h(arrivalTransportationSchedule.startTime);
                var maxT = to24h(arrivalTransportationSchedule.endTime);
                var hasSameDayRange = false;
                var startMinutes = parseTimeToMinutes(arrivalTransportationSchedule.startTime);
                var endMinutes = parseTimeToMinutes(arrivalTransportationSchedule.endTime);
                if (startMinutes !== null && endMinutes !== null) {
                    hasSameDayRange = endMinutes >= startMinutes;
                }
                var isMobileDevice = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
                    || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

                if (isMobileDevice) {
                    el.type = 'time';
                    el.removeAttribute('readonly');
                    el.step = 300;
                    if (minT && maxT && hasSameDayRange) {
                        el.min = minT;
                        el.max = maxT;
                    } else {
                        el.removeAttribute('min');
                        el.removeAttribute('max');
                    }
                    el.addEventListener('input', function () {
                        $(el).removeClass('required-field');
                    });
                    el.addEventListener('change', function () {
                        const normalizedTime = normalizeTimeToFiveMinutes(el.value, '24h');
                        if (normalizedTime) {
                            el.value = normalizedTime;
                        }
                    });
                    return;
                }

                el.type = 'text';
                el.removeAttribute('readonly');
                if (typeof flatpickr === 'undefined') {
                    el.type = 'time';
                    if (minT && maxT && hasSameDayRange) {
                        el.min = minT;
                        el.max = maxT;
                    } else {
                        el.removeAttribute('min');
                        el.removeAttribute('max');
                    }
                    el.step = 300;
                    return;
                }

                var pickerConfig = {
                    enableTime: true,
                    noCalendar: true,
                    time_24hr: false,
                    minuteIncrement: 5,
                    dateFormat: 'h:i K',
                    allowInput: true,
                    clickOpens: true,
                    onChange: function (selectedDates, dateStr, instance) {
                        const normalizedTime = normalizeTimeToFiveMinutes(instance.input.value, '12h');
                        if (normalizedTime && normalizedTime !== instance.input.value) {
                            instance.setDate(normalizedTime, true, 'h:i K');
                        }
                        $(el).removeClass('required-field');
                    }
                };
                if (minT && maxT && hasSameDayRange) {
                    pickerConfig.minTime = minT;
                    pickerConfig.maxTime = maxT;
                }

                var pickupTimePicker = flatpickr(el, pickerConfig);

                el.addEventListener('focus', function () {
                    if (pickupTimePicker && typeof pickupTimePicker.open === 'function') {
                        pickupTimePicker.open();
                    }
                });

                el.addEventListener('click', function () {
                    if (pickupTimePicker && typeof pickupTimePicker.open === 'function') {
                        pickupTimePicker.open();
                    }
                });
            })();

            // Arrival time picker mirrors pickup-time behavior for cross-platform support.
            (function () {
                var el = document.querySelector('input[name="transportation_arrival_time"]');
                if (!el) return;
                function to24h(t) {
                    if (!t) return null;
                    var m = String(t).trim().match(/^(\d{1,2}):(\d{2})\s*(AM|PM)?$/i);
                    if (!m) return null;
                    var hh = parseInt(m[1], 10);
                    var mm = parseInt(m[2], 10);
                    if (m[3]) {
                        var mer = m[3].toUpperCase();
                        if (mer === 'PM' && hh < 12) hh += 12;
                        else if (mer === 'AM' && hh === 12) hh = 0;
                    }
                    return String(hh).padStart(2, '0') + ':' + String(mm).padStart(2, '0');
                }
                var minT = to24h(transportationSchedule.startTime);
                var maxT = to24h(transportationSchedule.endTime);
                var hasSameDayRange = false;
                var startMinutes = parseTimeToMinutes(transportationSchedule.startTime);
                var endMinutes = parseTimeToMinutes(transportationSchedule.endTime);
                if (startMinutes !== null && endMinutes !== null) {
                    hasSameDayRange = endMinutes >= startMinutes;
                }
                var isMobileDevice = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
                    || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

                if (isMobileDevice) {
                    el.type = 'time';
                    el.removeAttribute('readonly');
                    el.step = 300;
                    if (minT && maxT && hasSameDayRange) {
                        el.min = minT;
                        el.max = maxT;
                    } else {
                        el.removeAttribute('min');
                        el.removeAttribute('max');
                    }
                    el.addEventListener('input', function () {
                        $(el).removeClass('required-field');
                    });
                    el.addEventListener('change', function () {
                        const normalizedTime = normalizeTimeToFiveMinutes(el.value, '24h');
                        if (normalizedTime) {
                            el.value = normalizedTime;
                        }
                    });
                    return;
                }

                el.type = 'text';
                el.removeAttribute('readonly');
                if (typeof flatpickr === 'undefined') {
                    el.type = 'time';
                    if (minT && maxT && hasSameDayRange) {
                        el.min = minT;
                        el.max = maxT;
                    } else {
                        el.removeAttribute('min');
                        el.removeAttribute('max');
                    }
                    el.step = 300;
                    return;
                }

                var pickerConfig = {
                    enableTime: true,
                    noCalendar: true,
                    time_24hr: false,
                    minuteIncrement: 5,
                    dateFormat: 'h:i K',
                    allowInput: true,
                    clickOpens: true,
                    onChange: function (selectedDates, dateStr, instance) {
                        const normalizedTime = normalizeTimeToFiveMinutes(instance.input.value, '12h');
                        if (normalizedTime && normalizedTime !== instance.input.value) {
                            instance.setDate(normalizedTime, true, 'h:i K');
                        }
                        $(el).removeClass('required-field');
                    }
                };
                if (minT && maxT && hasSameDayRange) {
                    pickerConfig.minTime = minT;
                    pickerConfig.maxTime = maxT;
                }

                var arrivalTimePicker = flatpickr(el, pickerConfig);

                el.addEventListener('focus', function () {
                    if (arrivalTimePicker && typeof arrivalTimePicker.open === 'function') {
                        arrivalTimePicker.open();
                    }
                });

                el.addEventListener('click', function () {
                    if (arrivalTimePicker && typeof arrivalTimePicker.open === 'function') {
                        arrivalTimePicker.open();
                    }
                });
            })();

            updateTransportationHoursDisplay();

            function getPacificTodayDateString() {
                try {
                    const formatter = new Intl.DateTimeFormat('en-CA', {
                        timeZone: @json($data->resolved_timezone),
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit'
                    });
                    return formatter.format(new Date());
                } catch (error) {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    return year + '-' + month + '-' + day;
                }
            }

            const pacificTodayDate = getPacificTodayDateString();

            flatpickr("#package_use_date", {
                dateFormat: "Y-m-d",
                defaultDate: null,
                minDate: pacificTodayDate,
                allowInput: false,
                clickOpens: true,
                disable: [function(date) {
                    return !isDateAllowed(date);
                }],
                onReady: function(selectedDates, dateStr, instance) {
                    $('.package_use_date').val(dateStr || instance.input.value);
                    clearReservationDateError();
                },
                onChange: function(selectedDates, dateStr) {
                    $('.package_use_date').val(dateStr);
                    clearReservationDateError();
                }
            });

            if (document.body.classList.contains('embed-checkout-mode') && document.getElementById('package_use_date_iframe')) {
                flatpickr("#package_use_date_iframe", {
                    dateFormat: "Y-m-d",
                    defaultDate: null,
                    minDate: pacificTodayDate,
                    allowInput: false,
                    clickOpens: true,
                    disable: [function(date) {
                        return !isDateAllowed(date);
                    }],
                    onReady: function(selectedDates, dateStr, instance) {
                        $('#package_use_date').val(dateStr || instance.input.value);
                        $('.package_use_date').val(dateStr || instance.input.value);
                        clearReservationDateError();
                    },
                    onChange: function(selectedDates, dateStr) {
                        $('#package_use_date').val(dateStr).trigger('change');
                        clearReservationDateError();
                    }
                });
            }

            $('.custom-calendar-icon').on('click', function() {
                const picker = document.getElementById('package_use_date')._flatpickr;
                if (picker) {
                    picker.open();
                }
            });

            $('.custom-calendar-icon-iframe').on('click', function() {
                const picker = document.getElementById('package_use_date_iframe')?._flatpickr;
                if (picker) {
                    picker.open();
                }
            });

            $('.package_use_date').val('');
        </script>

        <script>
            $('#package_use_date').on('change', function() {
                const val = $('#package_use_date').val();
                if ($('#package_use_date_iframe').length && $('#package_use_date_iframe').val() !== val) {
                    $('#package_use_date_iframe').val(val);
                }
                $('.package_use_date').val(val);
                clearReservationDateError();
                refreshPackageAvailabilityForSelectedDate(true);
            });
        </script>

        @if ($data->payment_method == 'stripe')
            <script src="https://js.stripe.com/v3/"></script>

            @php
                $setting = \App\Models\Setting::where('id', 1)->first();

                if ($data->stripe_app_key != null) {
                    # code...
                    $app = $data->stripe_app_key;
                } else {
                    # code...
                    $app = $setting->stripe_key;
                }

            @endphp

            <script>
                const stripe = Stripe("{{ $app }}");
                const elements = stripe.elements();

                const style = {
                    base: {
                        fontSize: '14px',
                        color: '#fff',
                        width: '100%',
                        height: '40px',
                        paddingLeft: '10px',
                        paddingRight: '10px',
                        border: '1px solid #9b9b9b',
                        backgroundColor: 'transparent',
                        borderRadius: '10px',
                    },
                };

                const cardNumber = elements.create('cardNumber', {
                    style: style
                });
                const cardExpiry = elements.create('cardExpiry', {
                    style: style
                });
                const cardCvc = elements.create('cardCvc', {
                    style: style
                });

                cardNumber.mount('#card_number');
                cardExpiry.mount('#expiration_date');
                cardCvc.mount('#cvv');

                const form = document.getElementById('payment-form');
                if (form) {
                    // Improve iOS compatibility with form submission
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        // Ensure form is not already submitting
                        if (form._isSubmitting) {
                            return;
                        }
                        form._isSubmitting = true;

                        prepareCheckoutCartPayload(form);
                        showCheckoutProcessingOverlay();

                        if (typeof window.isZeroTotalCheckout === 'function' && window.isZeroTotalCheckout()) {
                            setTimeout(function() {
                                form.submit();
                            }, 100);
                            return;
                        }

                        // Use Promise instead of async/await for better iOS compatibility
                        stripe.createToken(cardNumber).then(function(result) {
                            if (result.error) {
                                hideCheckoutProcessingOverlay();
                                document.getElementById('card-errors').textContent = result.error.message;
                                form._isSubmitting = false;
                            } else {
                                const hiddenInput = document.createElement('input');
                                hiddenInput.setAttribute('type', 'hidden');
                                hiddenInput.setAttribute('name', 'stripeToken');
                                hiddenInput.setAttribute('value', result.token.id);
                                form.appendChild(hiddenInput);

                                // iOS fix: Use setTimeout to ensure form submission happens
                                setTimeout(function() {
                                    form.submit();
                                }, 100);
                            }
                        }).catch(function(err) {
                            hideCheckoutProcessingOverlay();
                            document.getElementById('card-errors').textContent = 'Payment error: ' + (err.message || 'Unknown error');
                            form._isSubmitting = false;
                        });
                    }, false); // Use capture phase
                }
            </script>
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mobileQuery = window.matchMedia('(max-width: 768px)');
                const collapsibleBlocks = document.querySelectorAll('[data-mobile-collapsible]');

                function refreshCollapsibleBlock(block) {
                    const content = block.querySelector('.story-copy-collapsible');
                    const toggle = block.querySelector('.story-copy-toggle');

                    if (!content || !toggle) {
                        return;
                    }

                    if (!mobileQuery.matches) {
                        block.classList.remove('is-collapsed');
                        block.classList.remove('is-expanded');
                        toggle.style.display = 'none';
                        toggle.textContent = 'See more';
                        toggle.setAttribute('aria-expanded', 'true');
                        return;
                    }

                    if (!block.classList.contains('is-expanded')) {
                        block.classList.add('is-collapsed');
                    }

                    requestAnimationFrame(function() {
                        const isOverflowing = content.scrollHeight > content.clientHeight + 1;

                        if (!isOverflowing && !block.classList.contains('is-expanded')) {
                            block.classList.remove('is-collapsed');
                            toggle.style.display = 'none';
                            return;
                        }

                        toggle.style.display = 'inline-flex';
                        toggle.textContent = block.classList.contains('is-expanded') ? 'See less' : 'See more';
                        toggle.setAttribute('aria-expanded', block.classList.contains('is-expanded') ? 'true' : 'false');
                    });
                }

                collapsibleBlocks.forEach(function(block) {
                    const toggle = block.querySelector('.story-copy-toggle');

                    if (!toggle) {
                        return;
                    }

                    toggle.addEventListener('click', function() {
                        block.classList.toggle('is-expanded');
                        block.classList.toggle('is-collapsed', !block.classList.contains('is-expanded'));
                        refreshCollapsibleBlock(block);
                    });

                    refreshCollapsibleBlock(block);
                });

                if (typeof mobileQuery.addEventListener === 'function') {
                    mobileQuery.addEventListener('change', function() {
                        collapsibleBlocks.forEach(refreshCollapsibleBlock);
                    });
                } else if (typeof mobileQuery.addListener === 'function') {
                    mobileQuery.addListener(function() {
                        collapsibleBlocks.forEach(refreshCollapsibleBlock);
                    });
                }
            });
        </script>

        <script>
            // Mobile: move Order Summary between package selection and the step indicator
            // (Package Details / Transportation / Payment). Desktop: restore to its original parent.
            document.addEventListener('DOMContentLoaded', function() {
                var sidebar = document.getElementById('cv-order-sidebar');
                var stepsAnchor = document.getElementById('checkout-steps');
                if (!sidebar || !stepsAnchor) return;

                var originalParent = sidebar.parentNode;
                var originalNext = sidebar.nextSibling;
                var mq = window.matchMedia('(max-width: 991px)');

                function applySidebarPlacement() {
                    if (mq.matches) {
                        if (sidebar.parentNode !== stepsAnchor.parentNode || sidebar.nextSibling !== stepsAnchor) {
                            stepsAnchor.parentNode.insertBefore(sidebar, stepsAnchor);
                        }
                    } else {
                        if (sidebar.parentNode !== originalParent) {
                            if (originalNext && originalNext.parentNode === originalParent) {
                                originalParent.insertBefore(sidebar, originalNext);
                            } else {
                                originalParent.appendChild(sidebar);
                            }
                        }
                    }
                }

                applySidebarPlacement();
                if (typeof mq.addEventListener === 'function') {
                    mq.addEventListener('change', applySidebarPlacement);
                } else if (typeof mq.addListener === 'function') {
                    mq.addListener(applySidebarPlacement);
                }
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const requestedPackageId = @json($requestedPackageId ?? null);
                const singlePackageHeroMode = @json(!empty($isSinglePackageCheckout));
                if (!requestedPackageId) {
                    return;
                }

                setTimeout(function() {
                    const targetButton = document.querySelector('.vip-btn[data-id="' + requestedPackageId + '"]');
                    if (targetButton) {
                        const card = targetButton.closest('.vip-card');
                        if (card) {
                            card.classList.add('selected');
                        }

                        if (!singlePackageHeroMode) {
                            targetButton.click();
                            const steps = document.getElementById('checkout-steps');
                            if (steps) {
                                steps.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        }
                    }
                }, 350);
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const popup = @json(isset($checkoutPopup) && $checkoutPopup ? ['id' => $checkoutPopup->id, 'show_once_per_session' => (bool) $checkoutPopup->show_once_per_session] : null);
                if (!popup) {
                    return;
                }

                const modalElement = document.getElementById('checkoutPopupModal');
                if (!modalElement) {
                    return;
                }

                const seenKey = 'checkout_popup_seen_' + popup.id;
                if (popup.show_once_per_session && sessionStorage.getItem(seenKey) === '1') {
                    return;
                }

                setTimeout(function() {
                    bootstrap.Modal.getOrCreateInstance(modalElement).show();
                    if (popup.show_once_per_session) {
                        sessionStorage.setItem(seenKey, '1');
                    }
                }, 450);
            });
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

        {{-- CartVIP Sidebar Enhancement JS --}}
        <script>
        (function() {
            /* ===== Sidebar DOM relocation ===== */
            function initSidebar() {
                var sidebarBody = document.getElementById('cv-sidebar-body');
                if (!sidebarBody) return;

                // Move functional elements to sidebar on desktop
                var cartSection = document.getElementById('cart-section');
                var pricingShell = document.querySelector('.pricing-shell');
                var shareContainer = document.getElementById('shareLinkContainer');

                if (cartSection) sidebarBody.appendChild(cartSection);
                if (pricingShell) sidebarBody.appendChild(pricingShell);
                if (shareContainer) sidebarBody.appendChild(shareContainer);

                // Move the promo code section to AFTER the deposit box so it sits below the Due Today box.
                var depositBox = document.getElementById('cv-deposit-box');
                var promoCol = pricingShell ? pricingShell.querySelector('.dynamic-price.col-md-6') : null;
                if (depositBox && promoCol && depositBox.parentNode) {
                    depositBox.parentNode.insertBefore(promoCol, depositBox.nextSibling);
                }

                // Show sidebar
                var sidebar = document.getElementById('cv-order-sidebar');
                if (sidebar) sidebar.style.display = '';
            }

            /* ===== Sidebar date sync ===== */
            function initSidebarDateSync() {
                var dateInput = document.getElementById('package_use_date');
                var sidebarDate = document.getElementById('cv-sidebar-date');
                if (!dateInput || !sidebarDate) return;

                function updateSidebarDate() {
                    var val = dateInput.value;
                    if (val) {
                        sidebarDate.innerHTML = '<i class="fas fa-calendar-alt" style="margin-right:4px;opacity:.6;"></i>' + val;
                    } else {
                        sidebarDate.innerHTML = '<i class="fas fa-calendar-alt" style="margin-right:4px;opacity:.6;"></i>Select a date';
                    }
                }

                dateInput.addEventListener('change', updateSidebarDate);
                dateInput.addEventListener('input', updateSidebarDate);
                // Also watch for flatpickr changes
                if (window.MutationObserver) {
                    new MutationObserver(updateSidebarDate).observe(dateInput, { attributes: true, attributeFilter: ['value'] });
                }
                // Interval fallback for flatpickr
                var lastDate = '';
                setInterval(function() {
                    if (dateInput.value !== lastDate) {
                        lastDate = dateInput.value;
                        updateSidebarDate();
                    }
                }, 500);
                updateSidebarDate();
            }

            /* ===== Sidebar CTA wiring ===== */
            function initSidebarCta() {
                var ctaBtn = document.getElementById('cv-sidebar-cta');
                if (!ctaBtn) return;

                // Show CTA when a package is selected
                if (window.MutationObserver) {
                    var cartList = document.getElementById('cart-list');
                    if (cartList) {
                        new MutationObserver(function() {
                            var hasItems = cartList.children.length > 0;
                            ctaBtn.disabled = !hasItems;
                            ctaBtn.style.display = hasItems ? '' : 'none';
                            var depositBox = document.getElementById('cv-deposit-box');
                            if (depositBox) depositBox.style.display = hasItems ? '' : 'none';
                            // Also show edit cart link
                            var editBtn = document.getElementById('cv-edit-cart');
                            if (editBtn) editBtn.style.display = hasItems ? '' : 'none';
                            // Update mobile cart count
                            var mobileCount = document.getElementById('cv-mobile-cart-count');
                            if (mobileCount) {
                                var count = cartList.querySelectorAll('.cart-line').length;
                                mobileCount.textContent = count + (count === 1 ? ' item' : ' items');
                            }
                            // Show mobile toggle only on mobile screens (< 992px)
                            var toggle = document.getElementById('cv-mobile-cart-toggle');
                            if (toggle) {
                                if (hasItems && window.innerWidth <= 991) {
                                    toggle.style.display = 'flex';
                                    toggle.classList.add('is-visible');
                                } else {
                                    toggle.style.display = 'none';
                                    toggle.classList.remove('is-visible');
                                }
                            }
                        }).observe(cartList, { childList: true });
                    }
                }

                // CTA triggers checkout flow
                ctaBtn.addEventListener('click', function() {
                    var nextBtn = document.getElementById('next-to-transport');
                    if (nextBtn && nextBtn.style.display !== 'none') {
                        nextBtn.click();
                    } else {
                        // Fallback: scroll to checkout steps
                        var steps = document.getElementById('checkout-steps');
                        if (steps) steps.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });

                // Deposit display is updated directly in calculateCartTotal — no observer needed.
            }

            /* ===== Mobile cart toggle ===== */
            function initMobileToggle() {
                var toggleBtn = document.getElementById('cv-mobile-cart-toggle');
                var sidebar = document.getElementById('cv-order-sidebar');
                if (!toggleBtn || !sidebar) return;

                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('cv-sidebar-open');
                    var isOpen = sidebar.classList.contains('cv-sidebar-open');
                    toggleBtn.querySelector('span:first-child').textContent = isOpen ? 'Hide Order Summary' : 'View Order Summary';
                    if (isOpen) {
                        sidebar.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            }

            /* ===== Hamburger menu ===== */
            function initHamburger() {
                var hamburger = document.getElementById('cv-hamburger');
                if (!hamburger) return;
                hamburger.addEventListener('click', function() {
                    var mobileActions = document.querySelector('.mobile-top-actions');
                    if (mobileActions) {
                        mobileActions.style.display = mobileActions.style.display === 'none' ? '' : 'none';
                    }
                });
            }

            /* ===== Visual step sync ===== */
            function initVisualStepSync() {
                // Show the new cv-steps when old checkout-steps are shown
                if (window.MutationObserver) {
                    var oldSteps = document.getElementById('checkout-steps');
                    var cvSteps = document.getElementById('cv-steps');
                    if (oldSteps && cvSteps) {
                        new MutationObserver(function() {
                            cvSteps.style.display = oldSteps.style.display === 'none' || oldSteps.style.display === '' ? 'none' : 'flex';
                        }).observe(oldSteps, { attributes: true, attributeFilter: ['style'] });
                    }
                }
                // Sync active step
                var stepMap = {
                    'step-1': 'cv-vstep-1',
                    'step-2': 'cv-vstep-2',
                    'step-3': 'cv-vstep-3'
                };
                Object.keys(stepMap).forEach(function(oldId) {
                    var oldStep = document.getElementById(oldId);
                    var newStep = document.getElementById(stepMap[oldId]);
                    if (oldStep && newStep && window.MutationObserver) {
                        new MutationObserver(function() {
                            newStep.className = 'cv-step' +
                                (oldStep.classList.contains('active') ? ' cv-step-active' : '') +
                                (oldStep.classList.contains('done') ? ' cv-step-done' : '');
                        }).observe(oldStep, { attributes: true, attributeFilter: ['class'] });
                    }
                });
            }

            /* ===== Dynamic checkout step indicator (cv-dstep) ===== */
            function checkPackageFormFilled() {
                var section = document.getElementById('section-1');
                if (!section) return false;
                var reqInputs = section.querySelectorAll('input[required], select[required]');
                if (reqInputs.length === 0) return false;
                for (var i = 0; i < reqInputs.length; i++) {
                    if (!reqInputs[i].value || reqInputs[i].value.trim() === '') return false;
                }
                return true;
            }

            function updateCheckoutSteps() {
                var stepEls = [
                    document.getElementById('cv-dstep-1'),
                    document.getElementById('cv-dstep-2'),
                    document.getElementById('cv-dstep-3'),
                    document.getElementById('cv-dstep-4')
                ];
                if (!stepEls[0]) return;

                stepEls.forEach(function(s) {
                    if (s) s.classList.remove('is-active', 'is-complete');
                });

                var dateInput = document.getElementById('package_use_date');
                var dateDone = !dateInput || (dateInput.value && dateInput.value.trim() !== '');

                var isSinglePackageCheckout = {{ !empty($isSinglePackageCheckout) ? 'true' : 'false' }};
                var accessTabs = document.querySelectorAll('.cv-access-tab');
                var accessDone = isSinglePackageCheckout || accessTabs.length === 0 || !!document.querySelector('.cv-access-tab.is-active');
                var hasAccessStep = !isSinglePackageCheckout && !!stepEls[3];

                var cartList = document.getElementById('cart-list');
                var cartDone = !!(cartList && cartList.children.length > 0);

                var formDone = checkPackageFormFilled();

                if (isSinglePackageCheckout) {
                    if (dateDone && stepEls[0]) stepEls[0].classList.add('is-complete');
                    if (dateDone && cartDone && stepEls[1]) stepEls[1].classList.add('is-complete');
                    if (dateDone && cartDone && formDone && stepEls[2]) stepEls[2].classList.add('is-complete');

                    if (!dateDone && stepEls[0]) stepEls[0].classList.add('is-active');
                    else if (!cartDone && stepEls[1]) stepEls[1].classList.add('is-active');
                    else if (stepEls[2]) stepEls[2].classList.add('is-active');
                } else if (!hasAccessStep) {
                    if (dateDone && stepEls[0]) stepEls[0].classList.add('is-complete');
                    if (dateDone && cartDone && stepEls[1]) stepEls[1].classList.add('is-complete');
                    if (dateDone && cartDone && formDone && stepEls[2]) stepEls[2].classList.add('is-complete');

                    if (!dateDone && stepEls[0]) stepEls[0].classList.add('is-active');
                    else if (!cartDone && stepEls[1]) stepEls[1].classList.add('is-active');
                    else if (stepEls[2]) stepEls[2].classList.add('is-active');
                } else {
                    if (dateDone && stepEls[0]) stepEls[0].classList.add('is-complete');
                    if (dateDone && accessDone && stepEls[1]) stepEls[1].classList.add('is-complete');
                    if (dateDone && accessDone && cartDone && stepEls[2]) stepEls[2].classList.add('is-complete');
                    if (dateDone && accessDone && cartDone && formDone && stepEls[3]) stepEls[3].classList.add('is-complete');

                    if (!dateDone && stepEls[0]) stepEls[0].classList.add('is-active');
                    else if (!accessDone && stepEls[1]) stepEls[1].classList.add('is-active');
                    else if (!cartDone && stepEls[2]) stepEls[2].classList.add('is-active');
                    else if (stepEls[3]) stepEls[3].classList.add('is-active');
                }

                if (typeof updateReservationSteps === 'function') updateReservationSteps();
            }
            window.updateCheckoutSteps = updateCheckoutSteps;

            // Reservation (guest) flow has its own 3-step indicator: Choose Date -> Your Details -> Submit
            function updateReservationSteps() {
                var stepEls = [
                    document.getElementById('cv-rstep-1'),
                    document.getElementById('cv-rstep-2'),
                    document.getElementById('cv-rstep-3')
                ];
                if (!stepEls[0]) return;

                stepEls.forEach(function(s) {
                    if (s) s.classList.remove('is-active', 'is-complete');
                });

                var dateInput = document.getElementById('package_use_date');
                var dateDone = !!(dateInput && dateInput.value && dateInput.value.trim() !== '');

                var form = document.querySelector('.guest form');
                var detailsDone = !!form;
                ['reservation_first_name', 'reservation_last_name', 'reservation_phone', 'reservation_email'].forEach(function(n) {
                    var el = form && form.querySelector('[name="' + n + '"]');
                    if (!el || !el.value || el.value.trim() === '') detailsDone = false;
                });

                if (dateDone) stepEls[0].classList.add('is-complete');
                if (dateDone && detailsDone) stepEls[1].classList.add('is-complete');

                if (!dateDone) stepEls[0].classList.add('is-active');
                else if (!detailsDone) stepEls[1].classList.add('is-active');
                else stepEls[2].classList.add('is-active');
            }
            window.updateReservationSteps = updateReservationSteps;

            function initCheckoutSteps() {
                if (!document.getElementById('cv-dstep-1')) return;
                updateCheckoutSteps();

                var dateInput = document.getElementById('package_use_date');
                if (dateInput) {
                    dateInput.addEventListener('change', updateCheckoutSteps);
                    dateInput.addEventListener('input', updateCheckoutSteps);
                    if (window.MutationObserver) {
                        new MutationObserver(updateCheckoutSteps).observe(dateInput, { attributes: true, attributeFilter: ['value'] });
                    }
                }

                document.querySelectorAll('.cv-access-tab').forEach(function(tab) {
                    tab.addEventListener('click', function() {
                        // Toggle is-guest-mode class based on active tab
                        var layout = document.getElementById('cv-checkout-layout');
                        if (layout) {
                            if (this.getAttribute('data-name') === 'guest') {
                                layout.classList.add('is-guest-mode');
                            } else {
                                layout.classList.remove('is-guest-mode');
                            }
                        }
                        setTimeout(updateCheckoutSteps, 0);
                    });
                });

                // Initialize is-guest-mode based on which tab is active on page load
                var activeTab = document.querySelector('.cv-access-tab.is-active');
                if (activeTab && activeTab.getAttribute('data-name') === 'guest') {
                    var layout = document.getElementById('cv-checkout-layout');
                    if (layout) {
                        layout.classList.add('is-guest-mode');
                    }
                }

                var cartList = document.getElementById('cart-list');
                if (cartList && window.MutationObserver) {
                    new MutationObserver(updateCheckoutSteps).observe(cartList, { childList: true });
                }

                document.addEventListener('input', function(e) {
                    if (e.target && e.target.closest && e.target.closest('#section-1')) {
                        updateCheckoutSteps();
                    }
                });
                document.addEventListener('change', function(e) {
                    if (e.target && e.target.closest && e.target.closest('#section-1')) {
                        updateCheckoutSteps();
                    }
                });

                var guestForm = document.querySelector('.guest form');
                if (guestForm) {
                    guestForm.addEventListener('input', updateReservationSteps);
                    guestForm.addEventListener('change', updateReservationSteps);
                }
                updateReservationSteps();
            }

            /* ===== Map Button Handler ===== */
            function initMapButton() {
                var mapBtn = document.querySelector('.cv-hero-location-map-btn');
                if (!mapBtn) return;

                mapBtn.addEventListener('click', function() {
                    var location = this.getAttribute('data-location');
                    if (location) {
                        window.open('https://www.google.com/maps/search/' + encodeURIComponent(location), '_blank');
                    }
                });
            }

            /* ===== Date Selection Notification ===== */
            function initDateNotification() {
                var dateInput = document.getElementById('package_use_date');
                if (!dateInput) return;

                dateInput.addEventListener('change', function() {
                    if (this.value && this.value.trim() !== '') {
                        var toast = document.getElementById('cv-cart-toast');
                        var title = document.querySelector('#cv-cart-toast .cv-toast-title');
                        var sub = document.getElementById('cv-cart-toast-sub');
                        var icon = document.querySelector('#cv-cart-toast .cv-toast-icon i');
                        
                        if (toast && title && sub && icon) {
                            title.textContent = 'Reservation date selected!';
                            sub.textContent = 'Choose your package';
                            icon.className = 'fas fa-calendar-check';
                            toast.classList.add('is-visible');
                            
                            setTimeout(function() {
                                toast.classList.remove('is-visible');
                            }, 3500);
                        }
                    }
                });
            }

            // Auto-format phone numbers as user types
            function initPhoneFormatters() {
                // DISABLED: Phone formatters conflict with country code picker
                // The country code picker (validateAndFormatPhone) now handles phone validation and formatting
                // This old formatter only worked for US numbers and was causing issues with international numbers
            }

            document.addEventListener('DOMContentLoaded', function() {
                initSidebar();
                initSidebarDateSync();
                initSidebarCta();
                initHamburger();
                initVisualStepSync();
                initCheckoutSteps();
                initDateNotification();
                initMapButton();
                initPhoneFormatters();
            });
        })();
        </script>

        <script>
        // ===== COUNTRY CODE PICKER - COMPREHENSIVE SOLUTION =====
        const COUNTRIES = [
            { name: 'Afghanistan', code: '+93', flag: '🇦🇫' },
            { name: 'Albania', code: '+355', flag: '🇦🇱' },
            { name: 'Algeria', code: '+213', flag: '🇩🇿' },
            { name: 'Andorra', code: '+376', flag: '🇦🇩' },
            { name: 'Angola', code: '+244', flag: '🇦🇴' },
            { name: 'Argentina', code: '+54', flag: '🇦🇷' },
            { name: 'Armenia', code: '+374', flag: '🇦🇲' },
            { name: 'Australia', code: '+61', flag: '🇦🇺' },
            { name: 'Austria', code: '+43', flag: '🇦🇹' },
            { name: 'Azerbaijan', code: '+994', flag: '🇦🇿' },
            { name: 'Bahamas', code: '+1-242', flag: '🇧🇸' },
            { name: 'Bahrain', code: '+973', flag: '🇧🇭' },
            { name: 'Bangladesh', code: '+880', flag: '🇧🇩' },
            { name: 'Barbados', code: '+1-246', flag: '🇧🇧' },
            { name: 'Belarus', code: '+375', flag: '🇧🇾' },
            { name: 'Belgium', code: '+32', flag: '🇧🇪' },
            { name: 'Belize', code: '+501', flag: '🇧🇿' },
            { name: 'Benin', code: '+229', flag: '🇧🇯' },
            { name: 'Bhutan', code: '+975', flag: '🇧🇹' },
            { name: 'Bolivia', code: '+591', flag: '🇧🇴' },
            { name: 'Bosnia & Herzegovina', code: '+387', flag: '🇧🇦' },
            { name: 'Botswana', code: '+267', flag: '🇧🇼' },
            { name: 'Brazil', code: '+55', flag: '🇧🇷' },
            { name: 'Brunei', code: '+673', flag: '🇧🇳' },
            { name: 'Bulgaria', code: '+359', flag: '🇧🇬' },
            { name: 'Burkina Faso', code: '+226', flag: '🇧🇫' },
            { name: 'Burundi', code: '+257', flag: '🇧🇮' },
            { name: 'Cambodia', code: '+855', flag: '🇰🇭' },
            { name: 'Cameroon', code: '+237', flag: '🇨🇲' },
            { name: 'Canada', code: '+1', flag: '🇨🇦' },
            { name: 'Cape Verde', code: '+238', flag: '🇨🇻' },
            { name: 'Central African Republic', code: '+236', flag: '🇨🇫' },
            { name: 'Chad', code: '+235', flag: '🇹🇩' },
            { name: 'Chile', code: '+56', flag: '🇨🇱' },
            { name: 'China', code: '+86', flag: '🇨🇳' },
            { name: 'Colombia', code: '+57', flag: '🇨🇴' },
            { name: 'Comoros', code: '+269', flag: '🇰🇲' },
            { name: 'Congo', code: '+242', flag: '🇨🇬' },
            { name: 'Costa Rica', code: '+506', flag: '🇨🇷' },
            { name: 'Croatia', code: '+385', flag: '🇭🇷' },
            { name: 'Cuba', code: '+53', flag: '🇨🇺' },
            { name: 'Cyprus', code: '+357', flag: '🇨🇾' },
            { name: 'Czech Republic', code: '+420', flag: '🇨🇿' },
            { name: 'Denmark', code: '+45', flag: '🇩🇰' },
            { name: 'Djibouti', code: '+253', flag: '🇩🇯' },
            { name: 'Dominica', code: '+1-767', flag: '🇩🇲' },
            { name: 'Dominican Republic', code: '+1-809', flag: '🇩🇴' },
            { name: 'Ecuador', code: '+593', flag: '🇪🇨' },
            { name: 'Egypt', code: '+20', flag: '🇪🇬' },
            { name: 'El Salvador', code: '+503', flag: '🇸🇻' },
            { name: 'Equatorial Guinea', code: '+240', flag: '🇬🇶' },
            { name: 'Eritrea', code: '+291', flag: '🇪🇷' },
            { name: 'Estonia', code: '+372', flag: '🇪🇪' },
            { name: 'Ethiopia', code: '+251', flag: '🇪🇹' },
            { name: 'Fiji', code: '+679', flag: '🇫🇯' },
            { name: 'Finland', code: '+358', flag: '🇫🇮' },
            { name: 'France', code: '+33', flag: '🇫🇷' },
            { name: 'Gabon', code: '+241', flag: '🇬🇦' },
            { name: 'Gambia', code: '+220', flag: '🇬🇲' },
            { name: 'Georgia', code: '+995', flag: '🇬🇪' },
            { name: 'Germany', code: '+49', flag: '🇩🇪' },
            { name: 'Ghana', code: '+233', flag: '🇬🇭' },
            { name: 'Greece', code: '+30', flag: '🇬🇷' },
            { name: 'Grenada', code: '+1-473', flag: '🇬🇩' },
            { name: 'Guatemala', code: '+502', flag: '🇬🇹' },
            { name: 'Guinea', code: '+224', flag: '🇬🇳' },
            { name: 'Guinea-Bissau', code: '+245', flag: '🇬🇼' },
            { name: 'Guyana', code: '+592', flag: '🇬🇾' },
            { name: 'Haiti', code: '+509', flag: '🇭🇹' },
            { name: 'Honduras', code: '+504', flag: '🇭🇳' },
            { name: 'Hong Kong', code: '+852', flag: '🇭🇰' },
            { name: 'Hungary', code: '+36', flag: '🇭🇺' },
            { name: 'Iceland', code: '+354', flag: '🇮🇸' },
            { name: 'India', code: '+91', flag: '🇮🇳' },
            { name: 'Indonesia', code: '+62', flag: '🇮🇩' },
            { name: 'Iran', code: '+98', flag: '🇮🇷' },
            { name: 'Iraq', code: '+964', flag: '🇮🇶' },
            { name: 'Ireland', code: '+353', flag: '🇮🇪' },
            { name: 'Israel', code: '+972', flag: '🇮🇱' },
            { name: 'Italy', code: '+39', flag: '🇮🇹' },
            { name: 'Jamaica', code: '+1-876', flag: '🇯🇲' },
            { name: 'Japan', code: '+81', flag: '🇯🇵' },
            { name: 'Jordan', code: '+962', flag: '🇯🇴' },
            { name: 'Kazakhstan', code: '+7', flag: '🇰🇿' },
            { name: 'Kenya', code: '+254', flag: '🇰🇪' },
            { name: 'Kiribati', code: '+686', flag: '🇰🇮' },
            { name: 'Kosovo', code: '+383', flag: '🇽🇰' },
            { name: 'Kuwait', code: '+965', flag: '🇰🇼' },
            { name: 'Kyrgyzstan', code: '+996', flag: '🇰🇬' },
            { name: 'Laos', code: '+856', flag: '🇱🇦' },
            { name: 'Latvia', code: '+371', flag: '🇱🇻' },
            { name: 'Lebanon', code: '+961', flag: '🇱🇧' },
            { name: 'Lesotho', code: '+266', flag: '🇱🇸' },
            { name: 'Liberia', code: '+231', flag: '🇱🇷' },
            { name: 'Libya', code: '+218', flag: '🇱🇾' },
            { name: 'Liechtenstein', code: '+423', flag: '🇱🇮' },
            { name: 'Lithuania', code: '+370', flag: '🇱🇹' },
            { name: 'Luxembourg', code: '+352', flag: '🇱🇺' },
            { name: 'Macau', code: '+853', flag: '🇲🇴' },
            { name: 'Madagascar', code: '+261', flag: '🇲🇬' },
            { name: 'Malawi', code: '+265', flag: '🇲🇼' },
            { name: 'Malaysia', code: '+60', flag: '🇲🇾' },
            { name: 'Maldives', code: '+960', flag: '🇲🇻' },
            { name: 'Mali', code: '+223', flag: '🇲🇱' },
            { name: 'Malta', code: '+356', flag: '🇲🇹' },
            { name: 'Marshall Islands', code: '+692', flag: '🇲🇭' },
            { name: 'Mauritania', code: '+222', flag: '🇲🇷' },
            { name: 'Mauritius', code: '+230', flag: '🇲🇺' },
            { name: 'Mexico', code: '+52', flag: '🇲🇽' },
            { name: 'Micronesia', code: '+691', flag: '🇫🇲' },
            { name: 'Moldova', code: '+373', flag: '🇲🇩' },
            { name: 'Monaco', code: '+377', flag: '🇲🇨' },
            { name: 'Mongolia', code: '+976', flag: '🇲🇳' },
            { name: 'Montenegro', code: '+382', flag: '🇲🇪' },
            { name: 'Morocco', code: '+212', flag: '🇲🇦' },
            { name: 'Mozambique', code: '+258', flag: '🇲🇿' },
            { name: 'Myanmar', code: '+95', flag: '🇲🇲' },
            { name: 'Namibia', code: '+264', flag: '🇳🇦' },
            { name: 'Nauru', code: '+674', flag: '🇳🇷' },
            { name: 'Nepal', code: '+977', flag: '🇳🇵' },
            { name: 'Netherlands', code: '+31', flag: '🇳🇱' },
            { name: 'New Zealand', code: '+64', flag: '🇳🇿' },
            { name: 'Nicaragua', code: '+505', flag: '🇳🇮' },
            { name: 'Niger', code: '+227', flag: '🇳🇪' },
            { name: 'Nigeria', code: '+234', flag: '🇳🇬' },
            { name: 'North Korea', code: '+850', flag: '🇰🇵' },
            { name: 'North Macedonia', code: '+389', flag: '🇲🇰' },
            { name: 'Norway', code: '+47', flag: '🇳🇴' },
            { name: 'Oman', code: '+968', flag: '🇴🇲' },
            { name: 'Pakistan', code: '+92', flag: '🇵🇰' },
            { name: 'Palau', code: '+680', flag: '🇵🇼' },
            { name: 'Palestine', code: '+970', flag: '🇵🇸' },
            { name: 'Panama', code: '+507', flag: '🇵🇦' },
            { name: 'Papua New Guinea', code: '+675', flag: '🇵🇬' },
            { name: 'Paraguay', code: '+595', flag: '🇵🇾' },
            { name: 'Peru', code: '+51', flag: '🇵🇪' },
            { name: 'Philippines', code: '+63', flag: '🇵🇭' },
            { name: 'Poland', code: '+48', flag: '🇵🇱' },
            { name: 'Portugal', code: '+351', flag: '🇵🇹' },
            { name: 'Qatar', code: '+974', flag: '🇶🇦' },
            { name: 'Romania', code: '+40', flag: '🇷🇴' },
            { name: 'Russia', code: '+7', flag: '🇷🇺' },
            { name: 'Rwanda', code: '+250', flag: '🇷🇼' },
            { name: 'Saint Kitts & Nevis', code: '+1-869', flag: '🇰🇳' },
            { name: 'Saint Lucia', code: '+1-758', flag: '🇱🇨' },
            { name: 'Saint Vincent & Grenadines', code: '+1-784', flag: '🇻🇨' },
            { name: 'Samoa', code: '+685', flag: '🇼🇸' },
            { name: 'San Marino', code: '+378', flag: '🇸🇲' },
            { name: 'Sao Tome & Principe', code: '+239', flag: '🇸🇹' },
            { name: 'Saudi Arabia', code: '+966', flag: '🇸🇦' },
            { name: 'Senegal', code: '+221', flag: '🇸🇳' },
            { name: 'Serbia', code: '+381', flag: '🇷🇸' },
            { name: 'Seychelles', code: '+248', flag: '🇸🇨' },
            { name: 'Sierra Leone', code: '+232', flag: '🇸🇱' },
            { name: 'Singapore', code: '+65', flag: '🇸🇬' },
            { name: 'Slovakia', code: '+421', flag: '🇸🇰' },
            { name: 'Slovenia', code: '+386', flag: '🇸🇮' },
            { name: 'Solomon Islands', code: '+677', flag: '🇸🇧' },
            { name: 'Somalia', code: '+252', flag: '🇸🇴' },
            { name: 'South Africa', code: '+27', flag: '🇿🇦' },
            { name: 'South Korea', code: '+82', flag: '🇰🇷' },
            { name: 'South Sudan', code: '+211', flag: '🇸🇸' },
            { name: 'Spain', code: '+34', flag: '🇪🇸' },
            { name: 'Sri Lanka', code: '+94', flag: '🇱🇰' },
            { name: 'Sudan', code: '+249', flag: '🇸🇩' },
            { name: 'Suriname', code: '+597', flag: '🇸🇷' },
            { name: 'Sweden', code: '+46', flag: '🇸🇪' },
            { name: 'Switzerland', code: '+41', flag: '🇨🇭' },
            { name: 'Syria', code: '+963', flag: '🇸🇾' },
            { name: 'Taiwan', code: '+886', flag: '🇹🇼' },
            { name: 'Tajikistan', code: '+992', flag: '🇹🇯' },
            { name: 'Tanzania', code: '+255', flag: '🇹🇿' },
            { name: 'Thailand', code: '+66', flag: '🇹🇭' },
            { name: 'Timor-Leste', code: '+670', flag: '🇹🇱' },
            { name: 'Togo', code: '+228', flag: '🇹🇬' },
            { name: 'Tonga', code: '+676', flag: '🇹🇴' },
            { name: 'Trinidad & Tobago', code: '+1-868', flag: '🇹🇹' },
            { name: 'Tunisia', code: '+216', flag: '🇹🇳' },
            { name: 'Turkey', code: '+90', flag: '🇹🇷' },
            { name: 'Turkmenistan', code: '+993', flag: '🇹🇲' },
            { name: 'Tuvalu', code: '+688', flag: '🇹🇻' },
            { name: 'Uganda', code: '+256', flag: '🇺🇬' },
            { name: 'Ukraine', code: '+380', flag: '🇺🇦' },
            { name: 'United Arab Emirates', code: '+971', flag: '🇦🇪' },
            { name: 'United Kingdom', code: '+44', flag: '🇬🇧' },
            { name: 'United States', code: '+1', flag: '🇺🇸' },
            { name: 'Uruguay', code: '+598', flag: '🇺🇾' },
            { name: 'Uzbekistan', code: '+998', flag: '🇺🇿' },
            { name: 'Vanuatu', code: '+678', flag: '🇻🇺' },
            { name: 'Vatican City', code: '+379', flag: '🇻🇦' },
            { name: 'Venezuela', code: '+58', flag: '🇻🇪' },
            { name: 'Vietnam', code: '+84', flag: '🇻🇳' },
            { name: 'Yemen', code: '+967', flag: '🇾🇪' },
            { name: 'Zambia', code: '+260', flag: '🇿🇲' },
            { name: 'Zimbabwe', code: '+263', flag: '🇿🇼' }
        ];

        function initCountryCodePickers() {
            const phoneFields = [
                { name: 'package_phone', label: 'Package Phone' },
                { name: 'reservation_phone', label: 'Reservation Phone' }
                // Note: transportation_phone is excluded intentionally - it's a simple phone field for driver contact only
            ];

            phoneFields.forEach(field => {
                const input = document.querySelector(`input[name="${field.name}"]`);
                if (input) {
                    setupCountryCodePicker(input, field.name);
                }
            });
        }

        function setupCountryCodePicker(phoneInput, fieldName) {
            // Prevent double-wrapping if already initialized
            if (phoneInput.parentElement.classList.contains('phone-input-wrapper')) {
                return;
            }

            const wrapper = document.createElement('div');
            wrapper.className = 'phone-input-wrapper';

            const countryCodeDiv = document.createElement('div');
            countryCodeDiv.className = 'country-code-input';

            const countryCodeInput = document.createElement('input');
            countryCodeInput.className = 'country-code-field';
            countryCodeInput.type = 'text';
            countryCodeInput.placeholder = '🇺🇸 +1';
            countryCodeInput.name = `${fieldName}_country`;
            countryCodeInput.setAttribute('data-phone-field', fieldName);
            countryCodeInput.setAttribute('autocomplete', 'off');

            const dropdown = document.createElement('div');
            dropdown.className = 'country-code-dropdown';

            COUNTRIES.forEach(country => {
                const option = document.createElement('div');
                option.className = 'country-option';
                option.innerHTML = `<span class="flag-icon">${country.flag}</span>${country.code} ${country.name}`;
                option.setAttribute('data-code', country.code);
                option.setAttribute('data-flag', country.flag);
                option.addEventListener('click', () => selectCountry(countryCodeInput, option, country, phoneInput));
                dropdown.appendChild(option);
            });

            countryCodeDiv.appendChild(countryCodeInput);
            countryCodeDiv.appendChild(dropdown);

            // Set default to United States
            const usOption = COUNTRIES.find(c => c.code === '+1' && c.name === 'United States');
            if (usOption) {
                countryCodeInput.value = `${usOption.flag} ${usOption.code}`;
                countryCodeInput.dataset.code = usOption.code;
            }

            // Insert wrapper before phone input
            phoneInput.parentElement.insertBefore(wrapper, phoneInput);
            wrapper.appendChild(countryCodeDiv);
            wrapper.appendChild(phoneInput);

            // Setup country code input behavior
            countryCodeInput.addEventListener('click', () => {
                dropdown.classList.add('active');
                countryCodeInput.select();
            });

            // Close the list whenever focus leaves the field (tab / enter / click away),
            // but not when the blur is caused by clicking an option inside the list.
            dropdown.addEventListener('mousedown', () => { dropdown.dataset.keepOpen = '1'; });
            countryCodeInput.addEventListener('blur', () => {
                if (dropdown.dataset.keepOpen === '1') { dropdown.dataset.keepOpen = ''; return; }
                dropdown.classList.remove('active');
            });

            countryCodeInput.addEventListener('input', (e) => {
                dropdown.classList.add('active');
                const searchValue = e.target.value.toLowerCase();
                const options = dropdown.querySelectorAll('.country-option');
                options.forEach(option => {
                    const text = option.textContent.toLowerCase();
                    option.style.display = text.includes(searchValue) ? 'block' : 'none';
                });
            });

            document.addEventListener('click', (e) => {
                if (!countryCodeDiv.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });

            // Phone number input validation
            phoneInput.addEventListener('input', () => {
                validateAndFormatPhone(phoneInput, countryCodeInput);
            });

            phoneInput.addEventListener('blur', () => {
                validateAndFormatPhone(phoneInput, countryCodeInput);
            });
        }

        function selectCountry(countryCodeInput, optionEl, country, phoneInput) {
            countryCodeInput.value = `${country.flag} ${country.code}`;
            countryCodeInput.dataset.code = country.code;

            const dropdown = countryCodeInput.nextElementSibling;
            dropdown.querySelectorAll('.country-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            optionEl.classList.add('selected');
            dropdown.classList.remove('active');

            validateAndFormatPhone(phoneInput, countryCodeInput);
        }

        // Country-specific phone number length requirements
        const PHONE_LENGTH_REQUIREMENTS = {
            '+1': { min: 10, max: 10, name: 'North America' },
            '+880': { min: 10, max: 11, name: 'Bangladesh' },
            '+44': { min: 9, max: 11, name: 'UK' },
            '+33': { min: 9, max: 9, name: 'France' },
            '+49': { min: 9, max: 11, name: 'Germany' },
            '+39': { min: 9, max: 11, name: 'Italy' },
            '+34': { min: 9, max: 9, name: 'Spain' },
            '+31': { min: 9, max: 9, name: 'Netherlands' },
            '+41': { min: 9, max: 9, name: 'Switzerland' },
            '+43': { min: 9, max: 10, name: 'Austria' },
            '+46': { min: 9, max: 9, name: 'Sweden' },
            '+47': { min: 8, max: 8, name: 'Norway' },
            '+45': { min: 8, max: 8, name: 'Denmark' },
            '+358': { min: 9, max: 9, name: 'Finland' },
            '+353': { min: 9, max: 10, name: 'Ireland' },
            '+32': { min: 9, max: 9, name: 'Belgium' },
            '+86': { min: 11, max: 11, name: 'China' },
            '+81': { min: 10, max: 11, name: 'Japan' },
            '+82': { min: 10, max: 11, name: 'South Korea' },
            '+91': { min: 10, max: 10, name: 'India' },
            '+62': { min: 10, max: 12, name: 'Indonesia' },
            '+60': { min: 9, max: 11, name: 'Malaysia' },
            '+66': { min: 9, max: 10, name: 'Thailand' },
            '+65': { min: 8, max: 8, name: 'Singapore' },
            '+61': { min: 9, max: 9, name: 'Australia' },
            '+64': { min: 9, max: 10, name: 'New Zealand' },
            '+27': { min: 9, max: 9, name: 'South Africa' },
            '+55': { min: 10, max: 11, name: 'Brazil' },
            '+52': { min: 10, max: 10, name: 'Mexico' },
            '+54': { min: 10, max: 10, name: 'Argentina' },
            '+56': { min: 9, max: 9, name: 'Chile' },
            '+57': { min: 10, max: 10, name: 'Colombia' },
            '+51': { min: 9, max: 9, name: 'Peru' },
            '+84': { min: 9, max: 11, name: 'Vietnam' },
            '+855': { min: 8, max: 9, name: 'Cambodia' },
            '+663': { min: 9, max: 10, name: 'Laos' },
            '+95': { min: 9, max: 10, name: 'Myanmar' },
            '+970': { min: 9, max: 9, name: 'Palestine' },
            '+972': { min: 9, max: 10, name: 'Israel' },
            '+966': { min: 9, max: 9, name: 'Saudi Arabia' },
            '+971': { min: 9, max: 9, name: 'UAE' },
            '+973': { min: 8, max: 8, name: 'Bahrain' },
            '+974': { min: 8, max: 8, name: 'Qatar' },
            '+965': { min: 8, max: 8, name: 'Kuwait' },
        };

        function formatPhoneNumber(digits, countryCode) {
            // Format based on country code
            if (countryCode === '+1' || countryCode === '+7') {
                // US/Canada/Russia: (XXX) XXX-XXXX
                if (digits.length <= 3) return digits;
                if (digits.length <= 6) return `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
                return `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
            } else if (countryCode === '+44') {
                // UK: +44 XXXX XXX XXXX
                if (digits.length <= 4) return digits;
                if (digits.length <= 7) return `${digits.slice(0, 4)} ${digits.slice(4)}`;
                return `${digits.slice(0, 4)} ${digits.slice(4, 7)} ${digits.slice(7)}`;
            } else if (countryCode === '+880') {
                // Bangladesh: XXXX XXXXXX or XXXX XXXXX
                if (digits.length <= 4) return digits;
                return `${digits.slice(0, 4)} ${digits.slice(4)}`;
            } else if (countryCode === '+86') {
                // China: XXXX XXXX XXXX
                if (digits.length <= 4) return digits;
                if (digits.length <= 8) return `${digits.slice(0, 4)} ${digits.slice(4)}`;
                return `${digits.slice(0, 4)} ${digits.slice(4, 8)} ${digits.slice(8)}`;
            } else if (countryCode === '+81') {
                // Japan: XX-XXXX-XXXX
                if (digits.length <= 2) return digits;
                if (digits.length <= 6) return `${digits.slice(0, 2)}-${digits.slice(2)}`;
                return `${digits.slice(0, 2)}-${digits.slice(2, 6)}-${digits.slice(6)}`;
            } else {
                // Default: just return digits, split every 4 digits
                if (digits.length <= 4) return digits;
                let formatted = '';
                for (let i = 0; i < digits.length; i += 4) {
                    if (formatted) formatted += ' ';
                    formatted += digits.slice(i, i + 4);
                }
                return formatted;
            }
        }

        // Detect the country whose dial code is the longest prefix of the typed digits.
        function detectCountryFromDigits(digits) {
            if (!digits) return null;
            let best = null;
            let bestLen = 0;
            COUNTRIES.forEach(function (country) {
                const cc = country.code.replace(/\D/g, '');
                if (cc && digits.startsWith(cc) && cc.length > bestLen) {
                    best = country;
                    bestLen = cc.length;
                }
            });
            return best;
        }

        function validateAndFormatPhone(phoneInput, countryCodeInput) {
            let phoneValue = phoneInput.value.trim();
            let countryCode = countryCodeInput.dataset.code || '+1';

            // If the user typed a leading "+<country code>" directly into the number box,
            // detect the country, sync the flag/dropdown to it, and strip the code from the
            // national number so the flag and the number stay in sync.
            if (phoneValue.startsWith('+')) {
                const typedDigits = phoneValue.replace(/\D/g, '');
                const detected = detectCountryFromDigits(typedDigits);
                if (detected) {
                    countryCodeInput.value = `${detected.flag} ${detected.code}`;
                    countryCodeInput.dataset.code = detected.code;
                    countryCode = detected.code;
                    const ccDigits = detected.code.replace(/\D/g, '');
                    const nationalDigits = typedDigits.startsWith(ccDigits) ? typedDigits.substring(ccDigits.length) : typedDigits;
                    phoneInput.value = nationalDigits;
                    phoneValue = nationalDigits;
                } else {
                    // Incomplete country code still being typed (e.g. "+3") — leave it so the
                    // user can finish, and don't format/validate yet.
                    phoneInput.style.borderColor = '';
                    phoneInput.classList.remove('is-invalid', 'is-valid');
                    return;
                }
            }

            // Get country-specific requirements
            const requirements = PHONE_LENGTH_REQUIREMENTS[countryCode] || { min: 7, max: 15, name: 'Default' };

            // Store max digits in dataset for JavaScript validation instead of HTML maxLength
            phoneInput.dataset.maxDigits = requirements.max;

            if (!phoneValue) {
                phoneInput.style.borderColor = '';
                phoneInput.classList.remove('is-invalid', 'is-valid');
                // Clear E.164 hidden field
                const hiddenField = document.querySelector(`input[name="${phoneInput.name}_e164"]`);
                if (hiddenField) hiddenField.value = '';
                return;
            }

            // Remove all non-digits (allow only numbers)
            let digitsOnly = phoneValue.replace(/\D/g, '');

            // Enforce max digits by truncating if needed (don't include formatting chars)
            const maxDigits = parseInt(phoneInput.dataset.maxDigits || requirements.max);
            if (digitsOnly.length > maxDigits) {
                digitsOnly = digitsOnly.substring(0, maxDigits);
            }

            // Remove leading 1 if it's a North American number (already in country code)
            let cleanNumber = digitsOnly;
            if (countryCode === '+1' && digitsOnly.startsWith('1')) {
                cleanNumber = digitsOnly.substring(1);
            }

            // Display formatted number with proper formatting
            phoneInput.value = formatPhoneNumber(cleanNumber, countryCode);

            // Check if number is valid length
            if (cleanNumber.length < requirements.min) {
                phoneInput.style.borderColor = '#ff6b6b';
                phoneInput.classList.add('is-invalid');
                phoneInput.classList.remove('is-valid');
                return;
            }

            if (cleanNumber.length > requirements.max) {
                phoneInput.style.borderColor = '#ff6b6b';
                phoneInput.classList.add('is-invalid');
                phoneInput.classList.remove('is-valid');
                return;
            }

            // Valid! Format to E.164
            const e164Number = countryCode + cleanNumber;

            // Final E.164 validation
            if (!/^\+\d{7,15}$/.test(e164Number)) {
                phoneInput.style.borderColor = '#ff6b6b';
                phoneInput.classList.add('is-invalid');
                phoneInput.classList.remove('is-valid');
                return;
            }

            // Valid - set green border
            phoneInput.style.borderColor = '#51cf66';
            phoneInput.classList.remove('is-invalid');
            phoneInput.classList.add('is-valid');

            // Create or update hidden field with E.164 format for SMS
            let hiddenField = document.querySelector(`input[name="${phoneInput.name}_e164"]`);
            if (!hiddenField) {
                hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = `${phoneInput.name}_e164`;
                phoneInput.parentElement.appendChild(hiddenField);
            }
            hiddenField.value = e164Number;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                initCountryCodePickers();
            }, 500);
        });
        </script>


    <script>
    (function () {
        // AJAX checkout/reservation submit: keep exact page state on failure and show
        // inline error; keep normal success redirect behavior.
        function isCheckoutForm(form) {
            var a = (form.getAttribute('action') || '');
            return a.indexOf('/checkout/store') !== -1
                || a.indexOf('/reservation/store') !== -1
                || a.indexOf('/reservations/store') !== -1;
        }

        function captureCheckoutState() {
            var activeSection = document.querySelector('.checkout-section.active[id^="section-"]');
            return {
                activeSectionId: activeSection ? activeSection.id : null,
                scrollY: window.pageYOffset || document.documentElement.scrollTop || 0
            };
        }

        function restoreCheckoutState(snapshot) {
            if (!snapshot) return;
            if (snapshot.activeSectionId) {
                var m = /^section-(\d+)$/.exec(snapshot.activeSectionId);
                if (m && typeof window.showStep === 'function') {
                    var step = parseInt(m[1], 10);
                    if (Number.isFinite(step)) {
                        try { window.showStep(step); } catch (e) {}
                    }
                }
            }
            try { window.scrollTo(0, snapshot.scrollY || 0); } catch (e) {}
        }

        function restoreButtons() {
            try { if (typeof hideCheckoutProcessingOverlay === 'function') hideCheckoutProcessingOverlay(); } catch (e) {}
            var overlay = document.getElementById('checkout-processing-overlay');
            if (overlay) { overlay.classList.remove('is-visible'); overlay.setAttribute('aria-hidden', 'true'); }
            ['submitBtn', 'submitBtn_two'].forEach(function (id) {
                var b = document.getElementById(id);
                if (b) {
                    b.disabled = false;
                    if (b.dataset && b.dataset.defaultText) { b.textContent = b.dataset.defaultText; }
                }
            });
        }

        function showCheckoutError(form, message, snapshot) {
            restoreButtons();
            restoreCheckoutState(snapshot);

            var prev = document.getElementById('cv-ajax-error-alert');
            if (prev && prev.parentNode) prev.parentNode.removeChild(prev);

            var alertEl = document.createElement('div');
            alertEl.className = 'alert alert-danger';
            alertEl.setAttribute('role', 'alert');
            alertEl.id = 'cv-ajax-error-alert';
            alertEl.textContent = message || 'Something went wrong. Please try again.';

            var mount = form.closest('.checkout-section.active')
                || form.closest('.checkout-section')
                || form;
            mount.insertBefore(alertEl, mount.firstChild);
        }

        function extractError(json) {
            if (!json) return null;
            if (json.error) return json.error;
            if (json.errors && typeof json.errors === 'object') {
                var keys = Object.keys(json.errors);
                if (keys.length) {
                    var v = json.errors[keys[0]];
                    return Array.isArray(v) ? v[0] : v;
                }
            }
            return json.message || null;
        }

        function submitCheckoutAjax(form) {
            if (form.dataset.ajaxSubmitting === '1') return;
            form.dataset.ajaxSubmitting = '1';

            var snapshot = captureCheckoutState();
            if (typeof showCheckoutProcessingOverlay === 'function') { try { showCheckoutProcessingOverlay(); } catch (e) {} }

            var tokens = form.querySelectorAll('input[name="stripeToken"]');
            for (var i = 0; i < tokens.length - 1; i++) {
                if (tokens[i].parentNode) tokens[i].parentNode.removeChild(tokens[i]);
            }

            fetch(form.getAttribute('action'), {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin'
            }).then(function (res) {
                return res.text().then(function (t) {
                    var json = null;
                    try { json = JSON.parse(t); } catch (e) {}
                    return { ok: res.ok, json: json };
                });
            }).then(function (result) {
                if (result.json && result.json.success && result.json.redirect) {
                    window.location.href = result.json.redirect;
                    return;
                }
                form.dataset.ajaxSubmitting = '0';
                showCheckoutError(form, extractError(result.json), snapshot);
            }).catch(function () {
                form.dataset.ajaxSubmitting = '0';
                showCheckoutError(form, 'Network error. Please check your connection and try again.', snapshot);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            Array.prototype.forEach.call(document.querySelectorAll('form'), function (form) {
                if (!isCheckoutForm(form)) return;
                if (form.dataset.ajaxCheckoutBound === '1') return;
                form.dataset.ajaxCheckoutBound = '1';

                if (form.id === 'payment-form' && typeof window.updateCheckoutPaymentRequirement === 'function') {
                    window.updateCheckoutPaymentRequirement();
                }

                form.submit = function () { submitCheckoutAjax(form); };
                form.addEventListener('submit', function (e) {
                    if (e.defaultPrevented) return;
                    e.preventDefault();
                    submitCheckoutAjax(form);
                });
            });
        });
    })();
    </script>

    <script>
    (function () {
        // Checkout UX: Enter advances to the next field instead of submitting the form.
        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.keyCode !== 13) return;
            if (e.defaultPrevented) return; // a field-specific handler already dealt with Enter
            var el = e.target;
            if (!el) return;
            var tag = (el.tagName || '').toLowerCase();
            var type = ((el.getAttribute && el.getAttribute('type')) || '').toLowerCase();
            if (tag === 'textarea' || tag === 'button' || tag === 'a') return; // keep normal behavior
            if (type === 'submit' || type === 'button') return;
            var form = el.form || (el.closest ? el.closest('form') : null);
            if (!form) return; // only intercept fields inside a form

            e.preventDefault(); // stop the implicit form submission

            var fields = Array.prototype.filter.call(
                form.querySelectorAll('input, select, textarea, button'),
                function (node) {
                    if (node.disabled || node.type === 'hidden' || node.tabIndex === -1) return false;
                    return node.offsetParent !== null || node.getClientRects().length > 0;
                }
            );
            var idx = fields.indexOf(el);
            if (idx > -1 && idx < fields.length - 1) {
                var next = fields[idx + 1];
                next.focus();
                if (typeof next.select === 'function') { try { next.select(); } catch (err) {} }
            } else if (typeof el.blur === 'function') {
                el.blur();
            }
        });
    })();
    </script>

    <script>
    (function () {
        if (!document.body.classList.contains('embed-checkout-mode')) return;
        if (window.top === window.self) return;

        var mobileQuery = window.matchMedia('(max-width: 991px)');
        var lastHeight = 0;
        var scheduled = false;

        function getContentHeight() {
            var activeSection = document.querySelector('.checkout-section.active') || document.querySelector('#section-1.active') || document.querySelector('#section-2.active') || document.querySelector('#section-3.active');
            var targets = [
                document.querySelector('#cv-checkout-layout'),
                document.querySelector('.cv-checkout-body'),
                document.querySelector('#cv-order-sidebar'),
                document.querySelector('.cv-sidebar'),
                document.querySelector('.pricing-shell'),
                activeSection
            ].filter(function (node) {
                return !!node;
            });

            var maxBottom = 0;
            targets.forEach(function (node) {
                var rect = node.getBoundingClientRect ? node.getBoundingClientRect() : null;
                if (!rect) return;
                var bottom = Math.ceil(rect.bottom + window.pageYOffset);
                if (bottom > maxBottom) {
                    maxBottom = bottom;
                }
            });

            if (!maxBottom) {
                var fallbackRoot = document.querySelector('#cv-checkout-layout') || document.querySelector('main .container.mt-4') || document.body;
                maxBottom = fallbackRoot && fallbackRoot.scrollHeight ? fallbackRoot.scrollHeight : 1000;
            }

            var viewportFloor = window.innerHeight ? Math.max(720, Math.round(window.innerHeight * 0.86)) : 720;
            return Math.max(maxBottom + 56, viewportFloor);
        }

        function postHeightNow() {
            if (!mobileQuery.matches) return;
            var nextHeight = Math.max(720, Math.min(getContentHeight(), 12000));
            var changed = Math.abs(nextHeight - lastHeight) >= 2;
            if (changed) {
                lastHeight = nextHeight;
                window.parent.postMessage({ type: 'checkoutEmbedHeight', height: nextHeight }, '*');
            }
            window.dispatchEvent(new CustomEvent('embed:height-posted', {
                detail: { height: nextHeight, changed: changed }
            }));
        }

        function queueHeightPost() {
            if (!mobileQuery.matches) return;
            if (scheduled) return;
            scheduled = true;
            requestAnimationFrame(function () {
                scheduled = false;
                postHeightNow();
            });
        }

        window.addEventListener('load', queueHeightPost);
        window.addEventListener('resize', queueHeightPost);
        window.addEventListener('orientationchange', function () {
            setTimeout(queueHeightPost, 120);
            setTimeout(queueHeightPost, 380);
        });
        document.addEventListener('DOMContentLoaded', queueHeightPost);
        document.addEventListener('click', function (e) {
            if (!e.target || !e.target.closest) return;
            if (!e.target.closest('.package-category-tile, .package_number_of_guestss, .add_to_cart, .remove-from-cart')) return;
            setTimeout(queueHeightPost, 120);
            setTimeout(queueHeightPost, 360);
            setTimeout(queueHeightPost, 760);
            setTimeout(queueHeightPost, 1200);
        });

        window.addEventListener('embed:category-toggle', function () {
            setTimeout(queueHeightPost, 80);
            setTimeout(queueHeightPost, 260);
            setTimeout(queueHeightPost, 620);
            setTimeout(queueHeightPost, 1100);
        });

        document.addEventListener('shown.bs.modal', queueHeightPost);
        document.addEventListener('hidden.bs.modal', queueHeightPost);

        document.addEventListener('load', function (e) {
            var t = e.target;
            if (!t || !t.closest || t.tagName !== 'IMG') return;
            if (!t.closest('.package-category-group, .vip-card')) return;
            queueHeightPost();
            setTimeout(queueHeightPost, 240);
        }, true);

        if ('MutationObserver' in window) {
            var observedRoot = document.getElementById('vip_packages') || document.body;
            if (observedRoot) {
                var observer = new MutationObserver(queueHeightPost);
                observer.observe(observedRoot, { childList: true, subtree: true, attributes: true });
            }
        }

        setInterval(function () {
            if (mobileQuery.matches) {
                queueHeightPost();
            }
        }, 1200);

        queueHeightPost();
        setTimeout(queueHeightPost, 180);
        setTimeout(queueHeightPost, 620);
    })();
    </script>

    <script>
    (function(){
        if (!window.matchMedia('(max-width: 767px)').matches) return;
        function ensureTimePickerBelow() {
            var selectors = ['#Pick-up-time', 'input[name="transportation_pickup_time"]'];
            selectors.forEach(function(sel){
                var el = document.querySelector(sel);
                if (!el) return;
                var hasError = el.classList.contains('is-invalid') || el.getAttribute('aria-invalid') === 'true' || (el.nextElementSibling && el.nextElementSibling.classList && el.nextElementSibling.classList.contains('invalid-feedback')) || (el.closest && el.closest('.form-group') && el.closest('.form-group').querySelector('.invalid-feedback'));
                if (hasError) {
                    var rect = el.getBoundingClientRect();
                    var desiredTop = 120;
                    var scrollY = window.scrollY + rect.top - desiredTop;
                    if (scrollY < 0) scrollY = 0;
                    window.scrollTo({ top: scrollY, behavior: 'smooth' });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function(){
            setTimeout(ensureTimePickerBelow, 60);
            setTimeout(ensureTimePickerBelow, 300);
            setTimeout(ensureTimePickerBelow, 900);
        });

        document.addEventListener('invalid', function(e){
            var t = e.target;
            if (!t) return;
            if (t.matches && (t.matches('#Pick-up-time') || t.matches('input[name="transportation_pickup_time"]'))) {
                setTimeout(function(){
                    var rect = t.getBoundingClientRect();
                    var scrollY = window.scrollY + rect.top - 120;
                    if (scrollY < 0) scrollY = 0;
                    window.scrollTo({ top: scrollY, behavior: 'smooth' });
                }, 10);
            }
        }, true);

        var timeEls = document.querySelectorAll('#Pick-up-time, input[name="transportation_pickup_time"]');
        Array.prototype.forEach.call(timeEls, function(el){
            el.addEventListener('focus', function(){
                setTimeout(function(){
                    var rect = el.getBoundingClientRect();
                    var spaceBelow = window.innerHeight - rect.bottom;
                    var needed = 360;
                    if (spaceBelow < needed) {
                        var scrollY = window.scrollY + rect.top - 120;
                        if (scrollY < 0) scrollY = 0;
                        window.scrollTo({ top: scrollY, behavior: 'smooth' });
                    }
                }, 120);
            });
        });
    })();
    </script>
    </html>

@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">

<style>
    .forms-wizard li.done em::before, .lnr-checkmark-circle::before {
  content: "\e87f";
}

.forms-wizard li.done em::before {
  display: block;
  font-size: 1.2rem;
  height: 42px;
  line-height: 40px;
  text-align: center;
  width: 42px;
}

.forms-wizard li.done em {
  font-family: Linearicons-Free;
}

.website-section-title {
    background: var(--admin-surface-2);
    color: var(--admin-text) !important;
    border: 1px solid var(--admin-border);
    border-radius: 8px;
    padding: 10px 12px;
}

.toggle-field {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    border: 1px solid var(--admin-border);
    border-radius: 10px;
    background: var(--admin-surface-2);
}

.toggle-field .toggle-text {
    margin: 0;
    color: var(--admin-text);
    font-weight: 600;
    font-size: 14px;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 28px;
}

.toggle-switch-input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
}

.toggle-switch-slider {
    position: absolute;
    inset: 0;
    border-radius: 999px;
    background: #d1d5db;
    transition: background .2s ease;
    cursor: pointer;
}

.toggle-switch-slider::before {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    left: 4px;
    top: 4px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
    transition: transform .2s ease;
}

.toggle-switch-input:checked + .toggle-switch-slider {
    background: #ffcc00;
}

.toggle-switch-input:checked + .toggle-switch-slider::before {
    transform: translateX(20px);
}

.toggle-switch-input:focus-visible + .toggle-switch-slider {
    box-shadow: 0 0 0 3px rgba(255, 204, 0, 0.25);
}

.payment-method-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 10px;
}

.payment-method-option {
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid var(--admin-border);
    border-radius: 10px;
    padding: 10px;
    background: var(--admin-surface-2);
}

.payment-method-option img {
    width: 44px;
    height: 28px;
    object-fit: contain;
    background: #fff;
    border-radius: 6px;
    padding: 3px;
}

.payment-method-option input[type="checkbox"] {
    width: 16px;
    height: 16px;
}
</style>

<style>
  #suggestions {
    list-style: none;
    padding: 0;
    border: 1px solid #ccc;
    max-width: 300px;
    margin-top: 0;
  }

  #suggestions li {
    padding: 8px;
    cursor: pointer;
    background: #fff;
    color: #000 !important;
    border: 1px solid #000;
  }

  #suggestions li:hover {
    background: #eee;
  }
</style>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-xxl-12 mb-6 order-0">
                    <div class="app-main__inner">
                        <div class="app-page-title mt-4" data-step="" data-title="" data-intro="">
                            <div class="page-title-wrapper">
                                <div class="page-title-heading">

                                    <div class="page-title-icon">
                                        <i class="fas fa-id-card icon-gradient bg-arielle-smile"></i>
                                    </div>

                                    <div>
                                        <span class="text-capitalize">
                                            Website
                                        </span>
                                    </div>

                                </div>
                                <div class="page-title-actions">
                                </div>
                            </div>

                            <div class="page-title-subheading opacity-10 mt-3"
                                style="white-space: nowrap; overflow-x: auto;">
                                <nav class="" aria-label="breadcrumb">
                                    <ol class="breadcrumb" style="float: left">

                                        <li class="breadcrumb-item opacity-10">
                                            <a href="#">
                                                <i class="fas fa-home" role="img" aria-hidden="true"></i>
                                                <span class="visually-hidden">Home</span>
                                            </a>
                                            <i class="fas fa-chevron-right ms-1"></i>
                                        </li>

                                        <li class="breadcrumb-item ">
                                            Setting
                                            <i class="fas fa-chevron-right ms-1"></i>
                                        </li>
                                        <li class="active breadcrumb-item" aria-current="page">
                                            Website
                                        </li>

                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg">
                                <div class="card-shadow-primary card-border text-white mb-3 card bg-primary">
                                    <form action="{{ route('admin.website.update', $data->id) }}" method="post" enctype="multipart/form-data">
                                        @csrf

                                        <div class="card-body">
                                            <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
                                                <span>Payment keys and fees were moved to a dedicated page.</span>
                                                <a href="{{ route('admin.website.payment-settings', $data->id) }}" class="btn btn-sm btn-primary">Open Payment Settings</a>
                                            </div>
                                            <h4 class="mb-3 website-section-title">Basic Information</h4>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Website Name <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The display name of the venue or club. Shown throughout the platform and in customer emails."></i></label>
                                                        <input type="text" name="name" class="form-control" id="name" placeholder="Website Name" value="{{ old('name', $data->name) }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="short_name" class="form-label">Club Short Name (Optional)</label>
                                                        <input type="text" name="short_name" class="form-control @error('short_name') is-invalid @enderror" id="short_name" value="{{ old('short_name', $data->short_name) }}" placeholder="Short name for dispatcher SMS">
                                                        <small class="form-text text-muted">Used in booking dispatcher SMS instead of the full club name when provided.</small>
                                                        @error('short_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="slug" class="form-label">Slug (URL Path) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The URL-friendly path for the checkout page (e.g. my-venue)."></i></label>
                                                        <input type="text" name="slug" class="form-control" id="slug" value="{{ old('slug', $data->slug) }}" placeholder="e.g., my-website">
                                                        <small class="form-text text-muted">Current URL: www.domain.com/<strong>{{ $data->slug }}</strong></small>
                                                    </div>
                                                </div>
                                            </div>

                                            <h4 class="mt-4 mb-3 website-section-title">Website Content & Branding</h4>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Domain <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The domain or subdomain for this venue (e.g. www.myvenue.com)."></i></label>
                                                        <input type="text" name="domain" class="form-control" id="name" value="{{ old('domain', $data->domain) }}" placeholder="Enter Domain" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="google_analytics_id" class="form-label">Google Analytics Measurement ID (Optional) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional GA4 Measurement ID for this website, for example G-XXXXXXXXXX."></i></label>
                                                        <input type="text" name="google_analytics_id" class="form-control @error('google_analytics_id') is-invalid @enderror" id="google_analytics_id" value="{{ old('google_analytics_id', $data->google_analytics_id) }}" placeholder="e.g. G-XXXXXXXXXX">
                                                        <small class="form-text text-muted">Leave blank to disable Google Analytics for this website.</small>
                                                        @error('google_analytics_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="timezone" class="form-label">Website Timezone <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Used for this club's local dates and times. Keeping Pacific preserves the current live setup."></i></label>
                                                        <select name="timezone" id="timezone" class="form-select @error('timezone') is-invalid @enderror">
                                                            @foreach($timezoneOptions as $timezoneValue => $timezoneLabel)
                                                                <option value="{{ $timezoneValue }}" {{ old('timezone', $data->resolved_timezone) === $timezoneValue ? 'selected' : '' }}>{{ $timezoneLabel }}</option>
                                                            @endforeach
                                                        </select>
                                                        <small class="form-text text-muted">Current clubs stay unchanged because Pacific remains the saved default unless you pick a different timezone.</small>
                                                        @error('timezone')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Payment Icons <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Select which payment method logos should appear on this website's checkout page."></i></label>
                                                        <div class="payment-method-grid">
                                                            @foreach($stockPaymentLogos as $paymentKey => $paymentMethod)
                                                                <label class="payment-method-option" for="payment_method_{{ $paymentKey }}">
                                                                    <input id="payment_method_{{ $paymentKey }}" type="checkbox" name="payment_methods[]" value="{{ $paymentKey }}" {{ in_array($paymentKey, old('payment_methods', $selectedPaymentMethodKeys ?? []), true) ? 'checked' : '' }}>
                                                                    <img src="{{ $paymentMethod['logo'] }}" alt="{{ $paymentMethod['name'] }}">
                                                                    <span>{{ $paymentMethod['name'] }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                        <small class="form-text text-muted">Only selected logos will be shown on checkout for this website.</small>
                                                        @error('payment_methods')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                        @error('payment_methods.*')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="logo" class="form-label">Logo <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload a new logo to replace the current one. Leave blank to keep the existing logo."></i></label>
                                                        <input type="file" name="logo" class="form-control" id="logo" placeholder="Logo">
                                                    </div>
                                                    @if(!empty($data->logo))
                                                        <div class="mb-3">
                                                            <img src="{{ asset('uploads/' . $data->logo) }}" width="200px" style="width: 200px; max-height: 80px; object-fit: contain;">
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="logo_width" class="form-label">Logo Width (px) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional pixel width for the logo. Leave blank for default auto-sizing."></i></label>
                                                        <input type="number" name="logo_width" class="form-control" id="logo_width" value="{{ old('logo_width', $data->logo_width) }}" placeholder="Width in pixels" min="1">
                                                        <small class="form-text text-muted">Optional: Leave blank for auto-sizing</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="logo_height" class="form-label">Logo Height (px) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional pixel height for the logo. Leave blank for default auto-sizing."></i></label>
                                                        <input type="number" name="logo_height" class="form-control" id="logo_height" value="{{ old('logo_height', $data->logo_height) }}" placeholder="Height in pixels" min="1">
                                                        <small class="form-text text-muted">Optional: Leave blank for auto-sizing</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <h4 class="mt-4 mb-3 website-section-title">Venue Contact & Location</h4>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="location" class="form-label">Location <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The physical address of the venue. Used for the map and location display on the checkout page."></i></label>
                                                        <input type="text" name="location" class="form-control" id="location-input" value="{{ $data->location }}" placeholder="Location" required autocomplete="off">
                                                        <ul id="suggestions"></ul>
                                                        <input type="hidden" name="lat" id="latitude" value="{{ $data->lat }}">
                                                        <input type="hidden" name="long" id="longitude" value="{{ $data->long }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="phone" class="form-label">Phone <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The venue's public contact phone number."></i></label>
                                                        <input type="text" name="phone" class="form-control" value="{{ $data->phone }}" id="phone" placeholder="Phone" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="dispatcher_phone" class="form-label">Dispatcher Phone (SMS)</label>
                                                        <input type="text" name="dispatcher_phone" class="form-control @error('dispatcher_phone') is-invalid @enderror" value="{{ old('dispatcher_phone', $data->dispatcher_phone) }}" id="dispatcher_phone" placeholder="+1 555 123 4567">
                                                        <small class="form-text text-muted">Optional. Receives a New Booking SMS for this club.</small>
                                                        @error('dispatcher_phone')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="password" class="form-label">Email <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The venue's main public contact email address."></i></label>
                                                        <input type="email" name="email" class="form-control" value="{{ $data->email }}" id="email" placeholder="Email" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="toggle-field">
                                                            <p class="toggle-text">Show Phone/Email on Checkout</p>
                                                            <label class="toggle-switch" for="show_contact_info">
                                                                <input id="show_contact_info" type="checkbox" name="show_contact_info" value="1" class="toggle-switch-input" {{ old('show_contact_info', $data->show_contact_info ?? true) ? 'checked' : '' }}>
                                                                <span class="toggle-switch-slider"></span>
                                                            </label>
                                                        </div>
                                                        <small class="form-text text-muted">Turn off to hide the club phone and email in checkout page contact blocks.</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="toggle-field">
                                                            <p class="toggle-text">Physical Product Checkout Mode</p>
                                                            <label class="toggle-switch" for="is_physical_product_checkout">
                                                                <input id="is_physical_product_checkout" type="checkbox" name="is_physical_product_checkout" value="1" class="toggle-switch-input" {{ old('is_physical_product_checkout', $data->is_physical_product_checkout ?? false) ? 'checked' : '' }}>
                                                                <span class="toggle-switch-slider"></span>
                                                            </label>
                                                        </div>
                                                        <small class="form-text text-muted">Enable to use product checkout flow with shipping details and no transportation step.</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <div class="toggle-field">
                                                            <p class="toggle-text">Enable ClubLifter bookings</p>
                                                            <label class="toggle-switch" for="clublifter_enabled">
                                                                <input id="clublifter_enabled" type="checkbox" name="clublifter_enabled" value="1" class="toggle-switch-input" {{ old('clublifter_enabled', $data->clublifter_enabled) ? 'checked' : '' }}>
                                                                <span class="toggle-switch-slider"></span>
                                                            </label>
                                                        </div>
                                                        <small class="form-text text-muted">When enabled, eligible package bookings for this website are sent to ClubLifter after checkout.</small>
                                                    </div>
                                                </div>
                                            </div>
                                                <div class="col-md-12 mt-2">
                                                    <h5 class="website-section-title">Website Admin Access</h5>
                                                    <p class="text-muted">This user will have full access for this website (except super-admin-only platform features).</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="website_admin_name" class="form-label">Website Admin Name <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Full name for the primary admin account of this website."></i></label>
                                                        <input type="text" name="website_admin_name" class="form-control" id="website_admin_name" value="{{ old('website_admin_name', optional($websiteAdminUser)->name) }}" placeholder="Admin Name" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="website_admin_email" class="form-label">Website Admin Email <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Login email for the primary admin account of this website."></i></label>
                                                        <input type="email" name="website_admin_email" class="form-control @error('website_admin_email') is-invalid @enderror" id="website_admin_email" value="{{ old('website_admin_email', optional($websiteAdminUser)->email) }}" placeholder="admin@website.com" required>
                                                        @error('website_admin_email')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="website_admin_password" class="form-label">Website Admin Password <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Leave blank to keep the current password. Enter a new password to change it."></i></label>
                                                        <input type="password" name="website_admin_password" class="form-control" id="website_admin_password" placeholder="Leave blank to keep current password">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="website_admin_password_confirmation" class="form-label">Confirm Password <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Re-enter the new password to confirm it matches."></i></label>
                                                        <input type="password" name="website_admin_password_confirmation" class="form-control" id="website_admin_password_confirmation" placeholder="Confirm new password">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Contact Emails <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Additional email addresses that receive booking confirmation notifications."></i></label>
                                                        <div id="emails-wrapper">
                                                            @forelse ($data->emails as $item)
                                                                <div class="row mb-2 email-group">
                                                                    <div class="col-5">
                                                                        <input type="text" class="form-control email-name" placeholder="Name" value="{{ $item->name }}">
                                                                    </div>
                                                                    <div class="col-5">
                                                                        <input type="email" class="form-control email-address" placeholder="Email Address" value="{{ $item->email }}">
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <button type="button" class="btn btn-danger remove-email w-100" title="Remove"><i class="fa fa-minus"></i></button>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div class="row mb-2 email-group">
                                                                    <div class="col-5">
                                                                        <input type="text" class="form-control email-name" placeholder="Name">
                                                                    </div>
                                                                    <div class="col-5">
                                                                        <input type="email" class="form-control email-address" placeholder="Email Address">
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <button type="button" class="btn btn-warning add-email w-100" title="Add Email"><i class="fa fa-plus"></i></button>
                                                                    </div>
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                        <input type="hidden" name="emails" id="emails-json">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Entertainer Submission Emails <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Email recipients for entertainer application submissions only."></i></label>
                                                        <div id="entertainer-submission-emails-wrapper">
                                                            @php $entEmails = (array) ($data->entertainer_submission_emails ?? []); @endphp
                                                            @forelse ($entEmails as $item)
                                                                <div class="row mb-2 entertainer-submission-email-group">
                                                                    <div class="col-5">
                                                                        <input type="text" class="form-control entertainer-submission-email-name" placeholder="Name" value="{{ is_array($item) ? ($item['name'] ?? '') : '' }}">
                                                                    </div>
                                                                    <div class="col-5">
                                                                        <input type="email" class="form-control entertainer-submission-email-address" placeholder="Email Address" value="{{ is_array($item) ? ($item['email'] ?? '') : '' }}">
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <button type="button" class="btn btn-danger remove-entertainer-submission-email w-100" title="Remove"><i class="fa fa-minus"></i></button>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div class="row mb-2 entertainer-submission-email-group">
                                                                    <div class="col-5">
                                                                        <input type="text" class="form-control entertainer-submission-email-name" placeholder="Name">
                                                                    </div>
                                                                    <div class="col-5">
                                                                        <input type="email" class="form-control entertainer-submission-email-address" placeholder="Email Address">
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <button type="button" class="btn btn-warning add-entertainer-submission-email w-100" title="Add Email"><i class="fa fa-plus"></i></button>
                                                                    </div>
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                        <input type="hidden" name="entertainer_submission_emails" id="entertainer-submission-emails-json">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="reservation" class="form-label">Guest-list visible? <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Controls whether a guest-list / reservation tab is shown on the checkout page."></i></label>
                                                        <select name="reservation" id="reservation" class="form-control">
                                                            <option value="1" {{ $data->reservation == 1 ? 'selected' : '' }}>Yes</option>
                                                            <option value="0" {{ $data->reservation == 0 ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Description <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Public description of the venue displayed on the checkout page."></i></label>
                                                        <div id="website-description-editor"></div>
                                                        <textarea name="description" id="description" style="display:none" required>{{ $data->description }}</textarea>
                                                    </div>
                                                </div> --}}

                                                <div class="col-md-12 mt-2">
                                                    <h4 class="mt-4 mb-3 website-section-title">Public Checkout Page Customization</h4>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="hero_title" class="form-label">Hero Title <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Main headline shown at the top of the public-facing checkout page."></i></label>
                                                        <input type="text" name="hero_title" class="form-control" id="hero_title" value="{{ $data->hero_title }}" placeholder="Main headline for public page">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="hero_subtitle" class="form-label">Hero Subtitle <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Supporting subtitle text shown below the hero title on the checkout page."></i></label>
                                                        <input type="text" name="hero_subtitle" class="form-control" id="hero_subtitle" value="{{ $data->hero_subtitle }}" placeholder="Short supporting line under hero title">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="hero_badge_1_label" class="form-label">Hero Badge 1 Label <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Top line text for the first hero badge on checkout (e.g. Open Daily)."></i></label>
                                                        <input type="text" name="hero_badge_1_label" class="form-control" id="hero_badge_1_label" value="{{ old('hero_badge_1_label', $data->hero_badge_1_label) }}" placeholder="Open Daily">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="hero_badge_1_sub" class="form-label">Hero Badge 1 Subtext <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Bottom line text for the first hero badge (e.g. 6PM - 7AM)."></i></label>
                                                        <input type="text" name="hero_badge_1_sub" class="form-control" id="hero_badge_1_sub" value="{{ old('hero_badge_1_sub', $data->hero_badge_1_sub) }}" placeholder="6PM - 7AM">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="hero_badge_2_label" class="form-label">Hero Badge 2 Label <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Top line text for the second hero badge on checkout (e.g. Top Rated Club)."></i></label>
                                                        <input type="text" name="hero_badge_2_label" class="form-control" id="hero_badge_2_label" value="{{ old('hero_badge_2_label', $data->hero_badge_2_label) }}" placeholder="Top Rated Club">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="hero_badge_2_sub" class="form-label">Hero Badge 2 Subtext <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Bottom line text for the second hero badge (e.g. #1 in Las Vegas)."></i></label>
                                                        <input type="text" name="hero_badge_2_sub" class="form-control" id="hero_badge_2_sub" value="{{ old('hero_badge_2_sub', $data->hero_badge_2_sub) }}" placeholder="#1 in Las Vegas">
                                                    </div>
                                                </div>

                                                {{-- <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Secondary Description <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional second content block displayed on the checkout page below the main description."></i></label>
                                                        <div id="website-secondary-editor"></div>
                                                        <textarea name="secondary_description" id="secondary_description" style="display:none">{{ $data->secondary_description }}</textarea>
                                                    </div>
                                                </div> --}}

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="website_gallery_picker" class="form-label">Gallery Images <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Photos displayed in the venue's image gallery on the checkout page."></i></label>
                                                        <input type="file" class="form-control" id="website_gallery_picker" accept="image/*" data-criteria-bound="1">
                                                        <input type="file" name="gallery_images[]" class="d-none" id="gallery_images" accept="image/*" multiple>
                                                        <input type="hidden" name="existing_gallery_images" id="existing_gallery_images" value='@json((array) ($data->gallery_images ?? []))'>
                                                        <small class="form-text text-muted">Upload one image at a time. Added images appear below and can be removed before saving.</small>
                                                        <div id="website-gallery-preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                                                    </div>
                                                </div>
                                                
                                                @php
                                                    $tabIconOptions = [
                                                        'fa-car-side'     => 'Car (Side)',
                                                        'fa-car'          => 'Car',
                                                        'fa-star'         => 'Star',
                                                        'fa-crown'        => 'Crown',
                                                        'fa-gem'          => 'Gem',
                                                        'fa-fire'         => 'Fire',
                                                        'fa-bolt'         => 'Bolt',
                                                        'fa-ticket-alt'   => 'Ticket',
                                                        'fa-glass-cheers' => 'Cheers',
                                                        'fa-cocktail'     => 'Cocktail',
                                                        'fa-wine-glass'   => 'Wine Glass',
                                                        'fa-wine-bottle'  => 'Wine Bottle',
                                                        'fa-user-shield'  => 'VIP',
                                                        'fa-shield-alt'   => 'Shield',
                                                        'fa-music'        => 'Music',
                                                        'fa-users'        => 'Group',
                                                        'fa-id-card'      => 'ID Card',
                                                        'fa-door-open'    => 'Door Open',
                                                        'fa-list-ul'      => 'List',
                                                        'fa-calendar-alt' => 'Calendar',
                                                    ];
                                                @endphp

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="guest_list_button_text" class="form-label">Guest Tab <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Icon, label text and accent color for the Guest List tab."></i></label>
                                                        <div class="d-flex gap-2 align-items-center">
                                                            <div class="tab-icon-preview flex-shrink-0" id="guest-tab-icon-preview" style="width:34px;height:34px;border-radius:8px;border:1px solid #d7dce4;display:flex;align-items:center;justify-content:center;background:#f5f6fa;font-size:16px;color:#5a6082;">
                                                                <i class="fas {{ $data->guest_tab_icon ?? 'fa-car-side' }}"></i>
                                                            </div>
                                                            <select name="guest_tab_icon" id="guest_tab_icon" class="form-control form-control-sm tab-icon-select flex-shrink-0" style="max-width:130px;" data-preview="#guest-tab-icon-preview i">
                                                                @foreach($tabIconOptions as $iconClass => $iconLabel)
                                                                    <option value="{{ $iconClass }}" {{ ($data->guest_tab_icon ?? 'fa-car-side') === $iconClass ? 'selected' : '' }}>{{ $iconLabel }}</option>
                                                                @endforeach
                                                            </select>
                                                            <input type="text" name="guest_list_button_text" class="form-control" id="guest_list_button_text" value="{{ $data->guest_list_button_text ?? 'Guest List' }}" placeholder="Label text">
                                                            <input type="color" name="guest_tab_color" id="guest_tab_color" value="{{ $data->guest_tab_color ?? '#34d399' }}" title="Guest tab accent color" class="form-control form-control-color flex-shrink-0" style="width:38px;height:34px;padding:2px;cursor:pointer;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="guest_tab_subtitle" class="form-label">Guest Tab Subtitle <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional subtitle shown below the guest tab title on the checkout page."></i></label>
                                                        <input type="text" name="guest_tab_subtitle" class="form-control" id="guest_tab_subtitle" value="{{ old('guest_tab_subtitle', $data->guest_tab_subtitle) }}" placeholder="Guest Tab Subtitle">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="package_button_text" class="form-label">Package Tab <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Icon, label text and accent color for the VIP Packages tab."></i></label>
                                                        <div class="d-flex gap-2 align-items-center">
                                                            <div class="tab-icon-preview flex-shrink-0" id="package-tab-icon-preview" style="width:34px;height:34px;border-radius:8px;border:1px solid #d7dce4;display:flex;align-items:center;justify-content:center;background:#f5f6fa;font-size:16px;color:#5a6082;">
                                                                <i class="fas {{ $data->package_tab_icon ?? 'fa-star' }}"></i>
                                                            </div>
                                                            <select name="package_tab_icon" id="package_tab_icon" class="form-control form-control-sm tab-icon-select flex-shrink-0" style="max-width:130px;" data-preview="#package-tab-icon-preview i">
                                                                @foreach($tabIconOptions as $iconClass => $iconLabel)
                                                                    <option value="{{ $iconClass }}" {{ ($data->package_tab_icon ?? 'fa-star') === $iconClass ? 'selected' : '' }}>{{ $iconLabel }}</option>
                                                                @endforeach
                                                            </select>
                                                            <input type="text" name="package_button_text" class="form-control" id="package_button_text" value="{{ $data->package_button_text ?? 'Packages' }}" placeholder="Label text">
                                                            <input type="color" name="package_tab_color" id="package_tab_color" value="{{ $data->package_tab_color ?? '#e8be6a' }}" title="Package tab accent color" class="form-control form-control-color flex-shrink-0" style="width:38px;height:34px;padding:2px;cursor:pointer;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="package_tab_subtitle" class="form-label">Package Tab Subtitle <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional subtitle shown below the package tab title on the checkout page."></i></label>
                                                        <input type="text" name="package_tab_subtitle" class="form-control" id="package_tab_subtitle" value="{{ old('package_tab_subtitle', $data->package_tab_subtitle) }}" placeholder="Package Tab Subtitle">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="package_tab_ribbon" class="form-label">Package Tab Ribbon Text <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional corner ribbon on the VIP Packages tab (e.g. 'Best Value', 'Popular'). Leave blank to hide."></i></label>
                                                        <input type="text" name="package_tab_ribbon" class="form-control" id="package_tab_ribbon" value="{{ $data->package_tab_ribbon ?? '' }}" placeholder="e.g. Best Value  (leave blank to hide)">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="package_section_title" class="form-label">Package Section Title <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Heading shown above package cards on checkout and promoter public pages."></i></label>
                                                        <input type="text" name="package_section_title" class="form-control" id="package_section_title" value="{{ old('package_section_title', $data->package_section_title) }}" placeholder="Select Your Package">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="package_section_subtext" class="form-label">Package Section Subtext <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Supporting line shown under the package section title."></i></label>
                                                        <input type="text" name="package_section_subtext" class="form-control" id="package_section_subtext" value="{{ old('package_section_subtext', $data->package_section_subtext) }}" placeholder="All packages include free ride, club entry, and priority access.">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-12 my-3">
                                                    <h3 class="website-section-title"><i class="fas fa-check-square me-2"></i>Checkout Agreement Checkboxes & Verbiage Settings</h3>
                                                </div>

                                                <!-- 1. Transportation Confirmation Checkbox -->
                                                <div class="col-md-12 mb-3">
                                                    <div class="toggle-field">
                                                        <p class="toggle-text">Show Transportation Arrival Checkbox <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Show or hide the transportation arrival confirmation checkbox at checkout."></i></p>
                                                        <label class="toggle-switch" for="show_transportation_consent">
                                                            <input id="show_transportation_consent" type="checkbox" name="show_transportation_consent" value="1" class="toggle-switch-input" {{ old('show_transportation_consent', $data->show_transportation_consent ?? 1) == 1 ? 'checked' : '' }}>
                                                            <span class="toggle-switch-slider"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label for="transportation_confirmation_text" class="form-label">Transportation Confirmation Verbiage <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Custom text shown next to the transportation checkbox. Leave blank to use fallback default."></i></label>
                                                    <textarea name="transportation_confirmation_text" class="form-control" id="transportation_confirmation_text" rows="2" placeholder="Leave blank to use default fallback text">{{ $data->raw_transportation_confirmation_text ?? $data->getRawOriginal('transportation_confirmation_text') }}</textarea>
                                                    <small class="text-muted">Fallback default: "I confirm I am arriving in a personal vehicle or approved venue transportation..."</small>
                                                </div>

                                                <!-- 2. SMS Consent Checkbox -->
                                                <div class="col-md-12 mb-3">
                                                    <div class="toggle-field">
                                                        <p class="toggle-text">Show SMS Communication Consent Checkbox <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Show or hide the SMS communication consent checkbox at checkout."></i></p>
                                                        <label class="toggle-switch" for="show_sms_consent">
                                                            <input id="show_sms_consent" type="checkbox" name="show_sms_consent" value="1" class="toggle-switch-input" {{ old('show_sms_consent', $data->show_sms_consent ?? 1) == 1 ? 'checked' : '' }}>
                                                            <span class="toggle-switch-slider"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label for="sms_consent_text" class="form-label">SMS Consent Verbiage <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Custom text shown next to the SMS consent checkbox. Leave blank to use fallback default."></i></label>
                                                    <textarea name="sms_consent_text" class="form-control" id="sms_consent_text" rows="2" placeholder="Leave blank to use default fallback text">{{ $data->getRawOriginal('sms_consent_text') }}</textarea>
                                                    <small class="text-muted">Fallback default: "I agree to receive SMS communications regarding my reservation..."</small>
                                                </div>

                                                <!-- 3. Terms & Policies Checkbox -->
                                                <div class="col-md-12 mb-3">
                                                    <div class="toggle-field">
                                                        <p class="toggle-text">Show Terms & Policies Agreement Checkbox <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Show or hide the Terms of Service / Venue Policies agreement checkbox at checkout."></i></p>
                                                        <label class="toggle-switch" for="show_terms_consent">
                                                            <input id="show_terms_consent" type="checkbox" name="show_terms_consent" value="1" class="toggle-switch-input" {{ old('show_terms_consent', $data->show_terms_consent ?? 1) == 1 ? 'checked' : '' }}>
                                                            <span class="toggle-switch-slider"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label for="terms_consent_text" class="form-label">Terms & Policies Verbiage <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Custom text shown next to the Terms agreement checkbox. Leave blank to use fallback default."></i></label>
                                                    <textarea name="terms_consent_text" class="form-control" id="terms_consent_text" rows="2" placeholder="Leave blank to use default fallback text">{{ $data->getRawOriginal('terms_consent_text') }}</textarea>
                                                    <small class="text-muted">Fallback default: "I have read and agree to the Terms of Service / Venue Policies"</small>
                                                </div>

                                                <!-- 4. Business Purpose Checkbox -->
                                                <div class="col-md-12 mb-3">
                                                    <div class="toggle-field">
                                                        <p class="toggle-text">Show Business Expense Checkbox <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Show or hide the 'This purchase is for business purposes' checkbox at checkout."></i></p>
                                                        <label class="toggle-switch" for="show_business_expense_consent">
                                                            <input id="show_business_expense_consent" type="checkbox" name="show_business_expense_consent" value="1" class="toggle-switch-input" {{ old('show_business_expense_consent', $data->show_business_expense_consent ?? 0) == 1 ? 'checked' : '' }}>
                                                            <span class="toggle-switch-slider"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label for="business_expense_text" class="form-label">Business Expense Verbiage <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Custom text shown next to the business expense checkbox. Leave blank to use fallback default."></i></label>
                                                    <input type="text" name="business_expense_text" class="form-control" id="business_expense_text" value="{{ $data->getRawOriginal('business_expense_text') }}" placeholder="Leave blank to use default fallback text">
                                                    <small class="text-muted">Fallback default: "This purchase is for business purposes"</small>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <div class="toggle-field">
                                                        <p class="toggle-text">Show Arrival Time Subtext / Verbiage <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Show or hide the 'Required when self-driving or when package transportation is not included.' note under the Arrival Time field at checkout."></i></p>
                                                        <label class="toggle-switch" for="show_arrival_time_verbiage">
                                                            <input id="show_arrival_time_verbiage" type="checkbox" name="show_arrival_time_verbiage" value="1" class="toggle-switch-input" {{ old('show_arrival_time_verbiage', $data->show_arrival_time_verbiage ?? 1) == 1 ? 'checked' : '' }}>
                                                            <span class="toggle-switch-slider"></span>
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">Enable to show the note below the Arrival Time picker at checkout. Disable to hide it.</small>
                                                </div>

                                                <div class="col-md-12">
                                                    <h3 class="website-section-title">Global Default Operating & Pickup Hours</h3>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="operating_start_time" class="form-label">Global Operating Start <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Default operating start time when day specific hours are not set."></i></label>
                                                        <input type="time" name="operating_start_time" class="form-control" id="operating_start_time" value="{{ old('operating_start_time', $data->operating_start_time) }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="operating_end_time" class="form-label">Global Operating End <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Default operating end time when day specific hours are not set."></i></label>
                                                        <input type="time" name="operating_end_time" class="form-control" id="operating_end_time" value="{{ old('operating_end_time', $data->operating_end_time) }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="pickup_start_time" class="form-label">Global Pickup Start <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Default pickup start time when day specific hours are not set."></i></label>
                                                        <input type="time" name="pickup_start_time" class="form-control" id="pickup_start_time" value="{{ old('pickup_start_time', $data->pickup_start_time) }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="pickup_end_time" class="form-label">Global Pickup End <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Default pickup end time when day specific hours are not set."></i></label>
                                                        <input type="time" name="pickup_end_time" class="form-control" id="pickup_end_time" value="{{ old('pickup_end_time', $data->pickup_end_time) }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mt-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h3 class="website-section-title mb-0">Daily Operating & Pickup Hours Schedule</h3>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-copy-global-hours">
                                                            <i class="fas fa-copy me-1"></i> Apply Global Hours to All Days
                                                        </button>
                                                    </div>
                                                    <p class="text-muted small">Select operating and pickup hours for each day of the week. Days marked closed will not allow reservations.</p>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered align-middle no-datatable">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th style="width: 15%;">Day</th>
                                                                    <th style="width: 10%; text-align: center;">Open</th>
                                                                    <th style="width: 37.5%;">Club Operating Hours</th>
                                                                    <th style="width: 37.5%;">Transportation Pickup Hours</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $daysList = [
                                                                        'monday' => 'Monday',
                                                                        'tuesday' => 'Tuesday',
                                                                        'wednesday' => 'Wednesday',
                                                                        'thursday' => 'Thursday',
                                                                        'friday' => 'Friday',
                                                                        'saturday' => 'Saturday',
                                                                        'sunday' => 'Sunday',
                                                                    ];
                                                                    $dailyScheduleMap = isset($data) ? $data->getDailyOperatingHoursMap() : [];
                                                                @endphp
                                                                @foreach($daysList as $dayKey => $dayLabel)
                                                                    @php
                                                                        $daySched = old('daily_operating_hours.'.$dayKey, $dailyScheduleMap[$dayKey] ?? []);
                                                                        $isDayEnabled = old('daily_operating_hours.'.$dayKey.'.enabled', $daySched['enabled'] ?? true);
                                                                        $opStart = old('daily_operating_hours.'.$dayKey.'.operating_start_time', $daySched['operating_start_time'] ?? '');
                                                                        $opEnd = old('daily_operating_hours.'.$dayKey.'.operating_end_time', $daySched['operating_end_time'] ?? '');
                                                                        $pickStart = old('daily_operating_hours.'.$dayKey.'.pickup_start_time', $daySched['pickup_start_time'] ?? '');
                                                                        $pickEnd = old('daily_operating_hours.'.$dayKey.'.pickup_end_time', $daySched['pickup_end_time'] ?? '');
                                                                    @endphp
                                                                    <tr>
                                                                        <td><strong>{{ $dayLabel }}</strong></td>
                                                                        <td class="text-center">
                                                                            <div class="d-inline-block mb-0">
                                                                                <label class="toggle-switch" for="daily_enabled_{{ $dayKey }}">
                                                                                    <input id="daily_enabled_{{ $dayKey }}" type="checkbox" name="daily_operating_hours[{{ $dayKey }}][enabled]" value="1" class="toggle-switch-input day-enabled-toggle" {{ $isDayEnabled ? 'checked' : '' }}>
                                                                                    <span class="toggle-switch-slider"></span>
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group input-group-sm">
                                                                                <span class="input-group-text">Start</span>
                                                                                <input type="time" name="daily_operating_hours[{{ $dayKey }}][operating_start_time]" class="form-control daily-op-start" value="{{ $opStart }}">
                                                                                <span class="input-group-text">End</span>
                                                                                <input type="time" name="daily_operating_hours[{{ $dayKey }}][operating_end_time]" class="form-control daily-op-end" value="{{ $opEnd }}">
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group input-group-sm">
                                                                                <span class="input-group-text">Start</span>
                                                                                <input type="time" name="daily_operating_hours[{{ $dayKey }}][pickup_start_time]" class="form-control daily-pick-start" value="{{ $pickStart }}">
                                                                                <span class="input-group-text">End</span>
                                                                                <input type="time" name="daily_operating_hours[{{ $dayKey }}][pickup_end_time]" class="form-control daily-pick-end" value="{{ $pickEnd }}">
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h3 class="website-section-title">SMTP Configuration</h3>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="host" class="form-label">Host <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The hostname of your outgoing email server (e.g. smtp.gmail.com)."></i></label>
                                                                <input type="text" name="host" class="form-control" id="host" value="{{ optional($data->smtp)->host }}" placeholder="SMTP Host">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="port" class="form-label">Port <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The port for your SMTP server (587 for TLS, 465 for SSL)."></i></label>
                                                                <input type="number" name="port" class="form-control" id="port" value="{{ optional($data->smtp)->port }}" placeholder="SMTP Port">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="username" class="form-label">Username <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The username or email used to authenticate with your SMTP server."></i></label>
                                                                <input type="text" name="username" class="form-control" value="{{ optional($data->smtp)->username }}" id="username" placeholder="SMTP Username">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="password" class="form-label">Password <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The password used to authenticate with your SMTP server."></i></label>
                                                                <input type="text" name="password" class="form-control" value="{{ optional($data->smtp)->password }}" id="password" placeholder="SMTP Password">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="encryption" class="form-label">Encryption <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The security protocol for the SMTP connection. TLS is recommended."></i></label>
                                                                <select name="encryption" class="form-select" id="encryption">
                                                                    <option {{ optional($data->smtp)->encryption == 'tls' ? 'selected' : '' }} value="tls">TLS</option>
                                                                    <option {{ optional($data->smtp)->encryption == 'ssl' ? 'selected' : '' }} value="ssl">SSL</option>
                                                                    <option {{ optional($data->smtp)->encryption == 'none' ? 'selected' : '' }} value="none">None</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="from_address" class="form-label">From Address <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The email address that booking confirmation emails are sent from."></i></label>
                                                                <input type="email" value="{{ optional($data->smtp)->from_email }}" name="from_address" class="form-control" id="from_address" placeholder="From Address">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="from_name" class="form-label">From Name <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The sender name that appears in customers' email inboxes."></i></label>
                                                                <input type="text" value="{{ optional($data->smtp)->from_name }}" name="from_name" class="form-control" id="from_name" placeholder="From Name">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h3 class="website-section-title">Redirect & Policy Pages</h3>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="back_text" class="form-label">Back Button Text <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Text label for the back navigation button on the checkout page."></i></label>
                                                                <input type="text" name="back_text" class="form-control" id="back_text" value="{{ $data->back_text }}" placeholder="Back Button Text">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="privacy_policy" class="form-label">Back Button Link <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="URL the back button redirects to when clicked."></i></label>
                                                                <input type="text" name="back_link" class="form-control" id="privacy_policy" value="{{ $data->back_link }}" placeholder="Back Button Link">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="terms_conditions" class="form-label">Footer Text <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional footer text shown at the bottom of the checkout page."></i></label>
                                                                <input type="text" name="footer_text" class="form-control" id="terms_conditions" value="{{ $data->footer_text }}" placeholder="Footer Text">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <input type="hidden" name="success_page" value="https://app.cartvip.com/thank-you">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="privacy_policy" class="form-label">Privacy & Policy Page <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="URL of your venue's privacy policy page."></i></label>
                                                                <input type="text" name="policy" class="form-control" value="{{ $data->policy }}" id="privacy_policy" placeholder="Privacy & Policy Page URL">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="terms_conditions" class="form-label">Terms & Conditions Page <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="URL of your venue's terms and conditions page."></i></label>
                                                                <input type="text" name="terms" class="form-control" value="{{ $data->terms }}" id="terms_conditions" placeholder="Terms & Conditions Page URL">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <input type="hidden" name="description_label" value="Description">
                                                        <input type="hidden" name="text_description" value="Plan your night with curated VIP options and seamless booking.">
                                                        <div class="col-md-4">
                                                            <div class="alert alert-info mb-0 mt-4">
                                                                Theme and font colors are fixed globally.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <div class="d-flex flex-wrap gap-2 my-3">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                <a href="{{ route('admin.website.index') }}" class="btn btn-danger">Cancel</a>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                function applyDayToggleToRow(checkbox) {
                    var row = checkbox.closest('tr');
                    if (!row) return;
                    var inputs = row.querySelectorAll('.daily-op-start, .daily-op-end, .daily-pick-start, .daily-pick-end');
                    inputs.forEach(function(inp) { inp.disabled = !checkbox.checked; });
                }

                document.querySelectorAll('.day-enabled-toggle').forEach(function(cb) {
                    applyDayToggleToRow(cb);
                    cb.addEventListener('change', function(){ applyDayToggleToRow(cb); });
                });
            });
            </script>
            <script>
            const input = document.getElementById("location-input");
            const suggestions = document.getElementById("suggestions");

            input.addEventListener("input", function () {
                const value = input.value;

                if (value.length < 3) {
                suggestions.innerHTML = "";
                return;
                }

                fetch(`https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(value)}&apiKey=f3d9d402ade54545afa0c674f26ea633`)
                .then(response => response.json())
                .then(result => {
                    suggestions.innerHTML = "";
                    result.features.forEach(place => {
                    const li = document.createElement("li");
                    li.textContent = place.properties.formatted;
                    li.addEventListener("click", () => {
                        input.value = place.properties.formatted;
                        suggestions.innerHTML = "";

                        // Get coordinates
                        const lat = place.geometry.coordinates[1];
                        const lon = place.geometry.coordinates[0];
                        document.getElementById("latitude").value = lat;
                        document.getElementById("longitude").value = lon;
                        console.log("Selected Location:", lat, lon);
                    });
                    suggestions.appendChild(li);
                    });
                });
            });
            </script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

            <script>
            // Dynamic name/email repeater logic with automatic button state management
            function refreshRepeaterButtons(wrapperId, groupClass, addClass, removeClass, nameClass, addressClass) {
                var wrapper = $(wrapperId);
                if (!wrapper.length) return;

                var rows = wrapper.find('.' + groupClass);
                if (rows.length === 0) {
                    var newRow = '<div class="row mb-2 ' + groupClass + '">'
                        + '<div class="col-5"><input type="text" class="form-control ' + nameClass + '" placeholder="Name"></div>'
                        + '<div class="col-5"><input type="email" class="form-control ' + addressClass + '" placeholder="Email Address"></div>'
                        + '<div class="col-2"></div>'
                        + '</div>';
                    wrapper.append(newRow);
                    rows = wrapper.find('.' + groupClass);
                }

                rows.each(function(index) {
                    var actionCol = $(this).find('.col-2');
                    if (index === rows.length - 1) {
                        actionCol.html('<button type="button" class="btn btn-warning ' + addClass + ' w-100" title="Add Email"><i class="fa fa-plus"></i></button>');
                    } else {
                        actionCol.html('<button type="button" class="btn btn-danger ' + removeClass + ' w-100" title="Remove"><i class="fa fa-minus"></i></button>');
                    }
                });
            }

            $(document).on('click', '.add-email', function() {
                var emailGroup = '<div class="row mb-2 email-group">'
                    + '<div class="col-5"><input type="text" class="form-control email-name" placeholder="Name"></div>'
                    + '<div class="col-5"><input type="email" class="form-control email-address" placeholder="Email Address"></div>'
                    + '<div class="col-2"></div>'
                    + '</div>';
                $('#emails-wrapper').append(emailGroup);
                refreshRepeaterButtons('#emails-wrapper', 'email-group', 'add-email', 'remove-email', 'email-name', 'email-address');
            });

            $(document).on('click', '.remove-email', function() {
                $(this).closest('.email-group').remove();
                refreshRepeaterButtons('#emails-wrapper', 'email-group', 'add-email', 'remove-email', 'email-name', 'email-address');
            });

            $(document).on('click', '.add-entertainer-submission-email', function() {
                var emailGroup = '<div class="row mb-2 entertainer-submission-email-group">'
                    + '<div class="col-5"><input type="text" class="form-control entertainer-submission-email-name" placeholder="Name"></div>'
                    + '<div class="col-5"><input type="email" class="form-control entertainer-submission-email-address" placeholder="Email Address"></div>'
                    + '<div class="col-2"></div>'
                    + '</div>';
                $('#entertainer-submission-emails-wrapper').append(emailGroup);
                refreshRepeaterButtons('#entertainer-submission-emails-wrapper', 'entertainer-submission-email-group', 'add-entertainer-submission-email', 'remove-entertainer-submission-email', 'entertainer-submission-email-name', 'entertainer-submission-email-address');
            });

            $(document).on('click', '.remove-entertainer-submission-email', function() {
                $(this).closest('.entertainer-submission-email-group').remove();
                refreshRepeaterButtons('#entertainer-submission-emails-wrapper', 'entertainer-submission-email-group', 'add-entertainer-submission-email', 'remove-entertainer-submission-email', 'entertainer-submission-email-name', 'entertainer-submission-email-address');
            });

            $(document).ready(function() {
                refreshRepeaterButtons('#emails-wrapper', 'email-group', 'add-email', 'remove-email', 'email-name', 'email-address');
                refreshRepeaterButtons('#entertainer-submission-emails-wrapper', 'entertainer-submission-email-group', 'add-entertainer-submission-email', 'remove-entertainer-submission-email', 'entertainer-submission-email-name', 'entertainer-submission-email-address');
            });

            // Serialize name/email pairs to JSON on submit with clear validation
            $('form').on('submit', function(e) {
                $('.email-name, .email-address, .entertainer-submission-email-name, .entertainer-submission-email-address').removeClass('is-invalid');
                $('.email-error-alert').remove();

                var emails = [];
                var emailValidationError = null;
                var emailErrorElement = null;
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                $('#emails-wrapper .email-group').each(function() {
                    if (emailValidationError) return;

                    var nameInput = $(this).find('.email-name');
                    var addressInput = $(this).find('.email-address');
                    var name = (nameInput.val() || '').trim();
                    var address = (addressInput.val() || '').trim();

                    if (!name && !address) {
                        return; // Ignore completely blank rows safely
                    }

                    if (name && !address) {
                        emailValidationError = 'Please enter an Email Address for "' + name + '" in Contact Emails.';
                        emailErrorElement = addressInput;
                        return;
                    }

                    if (!name && address) {
                        emailValidationError = 'Please enter a Name for "' + address + '" in Contact Emails.';
                        emailErrorElement = nameInput;
                        return;
                    }

                    if (address && !emailRegex.test(address)) {
                        emailValidationError = 'Please enter a valid email address (e.g. name@domain.com) for "' + (name || 'Contact Email') + '".';
                        emailErrorElement = addressInput;
                        return;
                    }

                    emails.push({name: name, email: address});
                });

                if (emailValidationError) {
                    e.preventDefault();
                    if (emailErrorElement) {
                        emailErrorElement.addClass('is-invalid').focus();
                        var alertHtml = '<div class="alert alert-danger alert-dismissible fade show mt-2 email-error-alert" role="alert">'
                            + '<i class="fas fa-exclamation-triangle me-2"></i>' + $('<div>').text(emailValidationError).html()
                            + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                            + '</div>';
                        $('#emails-wrapper').after(alertHtml);
                        emailErrorElement[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return false;
                }

                var entertainerSubmissionEmails = [];
                var entertainerValidationError = null;
                var entertainerErrorElement = null;

                $('#entertainer-submission-emails-wrapper .entertainer-submission-email-group').each(function() {
                    if (entertainerValidationError) return;

                    var nameInput = $(this).find('.entertainer-submission-email-name');
                    var addressInput = $(this).find('.entertainer-submission-email-address');
                    var name = (nameInput.val() || '').trim();
                    var address = (addressInput.val() || '').trim();

                    if (!name && !address) {
                        return; // Ignore completely blank rows safely
                    }

                    if (name && !address) {
                        entertainerValidationError = 'Please enter an Email Address for "' + name + '" in Entertainer Submission Emails.';
                        entertainerErrorElement = addressInput;
                        return;
                    }

                    if (!name && address) {
                        entertainerValidationError = 'Please enter a Name for "' + address + '" in Entertainer Submission Emails.';
                        entertainerErrorElement = nameInput;
                        return;
                    }

                    if (address && !emailRegex.test(address)) {
                        entertainerValidationError = 'Please enter a valid email address for "' + (name || 'Entertainer Submission Email') + '".';
                        entertainerErrorElement = addressInput;
                        return;
                    }

                    entertainerSubmissionEmails.push({name: name, email: address});
                });

                if (entertainerValidationError) {
                    e.preventDefault();
                    if (entertainerErrorElement) {
                        entertainerErrorElement.addClass('is-invalid').focus();
                        var alertHtml = '<div class="alert alert-danger alert-dismissible fade show mt-2 email-error-alert" role="alert">'
                            + '<i class="fas fa-exclamation-triangle me-2"></i>' + $('<div>').text(entertainerValidationError).html()
                            + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                            + '</div>';
                        $('#entertainer-submission-emails-wrapper').after(alertHtml);
                        entertainerErrorElement[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return false;
                }

                $('#emails-json').val(JSON.stringify(emails));
                if ($('#entertainer-submission-emails-json').length) {
                    $('#entertainer-submission-emails-json').val(JSON.stringify(entertainerSubmissionEmails));
                }
            });

            (function () {
                const picker = document.getElementById('website_gallery_picker');
                const galleryInput = document.getElementById('gallery_images');
                const preview = document.getElementById('website-gallery-preview');
                const existingInput = document.getElementById('existing_gallery_images');

                if (!picker || !galleryInput || !preview || !existingInput) {
                    return;
                }

                let existingImages = [];
                try {
                    existingImages = JSON.parse(existingInput.value || '[]');
                    if (!Array.isArray(existingImages)) {
                        existingImages = [];
                    }
                } catch (e) {
                    existingImages = [];
                }

                let dt = new DataTransfer();

                function syncExisting() {
                    existingInput.value = JSON.stringify(existingImages);
                }

                function syncFiles() {
                    galleryInput.files = dt.files;
                }

                function render() {
                    preview.innerHTML = '';

                    existingImages.forEach(function (name, index) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'position-relative';
                        wrapper.style.width = '96px';
                        wrapper.innerHTML = '<img src="/uploads/' + name + '" style="width:96px;height:64px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">'
                            + '<button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" style="line-height:1;padding:2px 6px;" data-existing-index="' + index + '">&times;</button>';
                        preview.appendChild(wrapper);
                    });

                    Array.from(dt.files).forEach(function (file, index) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'position-relative';
                        wrapper.style.width = '96px';
                        const url = URL.createObjectURL(file);
                        wrapper.innerHTML = '<img src="' + url + '" style="width:96px;height:64px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">'
                            + '<button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" style="line-height:1;padding:2px 6px;" data-new-index="' + index + '">&times;</button>';
                        preview.appendChild(wrapper);
                    });
                }

                picker.addEventListener('change', function () {
                    const file = picker.files && picker.files[0] ? picker.files[0] : null;
                    if (!file) {
                        return;
                    }

                    dt.items.add(file);
                    syncFiles();
                    render();
                    picker.value = '';
                });

                preview.addEventListener('click', function (event) {
                    const existingBtn = event.target.closest('[data-existing-index]');
                    if (existingBtn) {
                        const idx = Number(existingBtn.getAttribute('data-existing-index'));
                        if (!Number.isNaN(idx)) {
                            existingImages.splice(idx, 1);
                            syncExisting();
                            render();
                        }
                        return;
                    }

                    const newBtn = event.target.closest('[data-new-index]');
                    if (newBtn) {
                        const idx = Number(newBtn.getAttribute('data-new-index'));
                        if (!Number.isNaN(idx)) {
                            const next = new DataTransfer();
                            Array.from(dt.files).forEach(function (file, fileIndex) {
                                if (fileIndex !== idx) {
                                    next.items.add(file);
                                }
                            });
                            dt = next;
                            syncFiles();
                            render();
                        }
                    }
                });

                syncExisting();
                syncFiles();
                render();
            })();

            // Tab icon picker live preview
            $(document).on('change', '.tab-icon-select', function() {
                var val = $(this).val();
                var previewSel = $(this).data('preview');
                if (previewSel) {
                    $(previewSel).attr('class', 'fas ' + val);
                }
            });

            // If the admin email belongs to a website admin on another website, hide the password
            // fields (they keep their existing password). If used by another account type, warn.
            (function initWebsiteAdminEmailCheck() {
                var emailInput = document.getElementById('website_admin_email');
                var passInput = document.getElementById('website_admin_password');
                var confirmInput = document.getElementById('website_admin_password_confirmation');
                if (!emailInput || !passInput || !confirmInput) return;

                var passCol = passInput.closest('.col-md-6');
                var confirmCol = confirmInput.closest('.col-md-6');
                var checkUrl = "{{ route('admin.website.check-admin-email') }}";
                var websiteId = "{{ $data->id }}";
                var debounce;

                var note = document.createElement('small');
                note.id = 'website_admin_email_note';
                note.style.display = 'none';
                note.style.marginTop = '4px';
                emailInput.parentNode.appendChild(note);

                function showFields(show) {
                    if (passCol) passCol.style.display = show ? '' : 'none';
                    if (confirmCol) confirmCol.style.display = show ? '' : 'none';
                }
                function apply(status, name) {
                    if (status === 'reuse') {
                        // Shared admin: keep the password field available — a new password updates every site.
                        showFields(true);
                        note.style.display = 'block'; note.style.color = '#0d6efd';
                        note.textContent = 'This email also administers other website(s). Enter a new password to update it for all of them, or leave blank to keep the current one.';
                    } else if (status === 'blocked') {
                        showFields(true);
                        note.style.display = 'block'; note.style.color = '#dc2626';
                        note.textContent = 'This email is already registered to another account type and cannot be used as a website admin.';
                    } else {
                        showFields(true);
                        note.style.display = 'none'; note.textContent = '';
                    }
                }
                function check() {
                    var email = (emailInput.value || '').trim();
                    if (!email || email.indexOf('@') === -1) { apply('new'); return; }
                    fetch(checkUrl + '?email=' + encodeURIComponent(email) + '&website_id=' + encodeURIComponent(websiteId), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' })
                        .then(function (r) { return r.json(); })
                        .then(function (d) { apply(d && d.status ? d.status : 'new', d ? d.name : null); })
                        .catch(function () {});
                }
                emailInput.addEventListener('input', function () { clearTimeout(debounce); debounce = setTimeout(check, 400); });
                emailInput.addEventListener('blur', check);
                check(); // run on load so a shared-admin note shows immediately
            })();
            </script>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
<style>
.ql-toolbar.ql-snow{background:#1e2230;border:1px solid rgba(255,255,255,.12)!important;border-bottom:1px solid rgba(255,255,255,.07)!important;border-radius:6px 6px 0 0;padding:8px}
.ql-container.ql-snow{background:#161b2e;border:1px solid rgba(255,255,255,.12)!important;border-top:none!important;border-radius:0 0 6px 6px;font-size:14px}
.ql-editor{min-height:140px;color:#d8def0;line-height:1.7}
.ql-editor.ql-blank::before{color:rgba(216,222,240,.3);font-style:normal}
.ql-snow .ql-stroke{stroke:rgba(216,222,240,.6)}
.ql-snow .ql-fill,.ql-snow .ql-stroke.ql-fill{fill:rgba(216,222,240,.6)}
.ql-snow .ql-picker{color:rgba(216,222,240,.6)}
.ql-snow .ql-picker-options{background:#1e2230;border-color:rgba(255,255,255,.12)}
.ql-snow .ql-toolbar button.ql-active .ql-stroke,.ql-snow .ql-toolbar button:hover .ql-stroke{stroke:#ffcc00}
.ql-snow .ql-toolbar button.ql-active .ql-fill,.ql-snow .ql-toolbar button:hover .ql-fill{fill:#ffcc00}
.ql-snow .ql-toolbar button.ql-active,.ql-snow .ql-toolbar button:hover{color:#ffcc00}
.ql-snow a{color:#ffcc00}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var wDescTA = document.getElementById('description');
    var quillWDesc = new Quill('#website-description-editor', {
        theme: 'snow',
        placeholder: 'Describe the venue...',
        modules: { toolbar: [['bold','italic','underline'],[{'list':'ordered'},{'list':'bullet'}],['link','clean']] }
    });
    if (wDescTA && wDescTA.value) quillWDesc.root.innerHTML = wDescTA.value;

    var wSecTA = document.getElementById('secondary_description');
    var quillWSec = new Quill('#website-secondary-editor', {
        theme: 'snow',
        placeholder: 'Optional second content block...',
        modules: { toolbar: [['bold','italic','underline'],[{'list':'ordered'},{'list':'bullet'}],['link','clean']] }
    });
    if (wSecTA && wSecTA.value) quillWSec.root.innerHTML = wSecTA.value;

    var websiteForm = wDescTA ? wDescTA.closest('form') : null;
    if (websiteForm) websiteForm.addEventListener('submit', function() {
        wDescTA.value = quillWDesc.root.innerHTML === '<p><br></p>' ? '' : quillWDesc.root.innerHTML;
        wSecTA.value = quillWSec.root.innerHTML === '<p><br></p>' ? '' : quillWSec.root.innerHTML;
    });

    document.getElementById('btn-copy-global-hours')?.addEventListener('click', function() {
        const opStart = document.getElementById('operating_start_time')?.value || '';
        const opEnd = document.getElementById('operating_end_time')?.value || '';
        const pickStart = document.getElementById('pickup_start_time')?.value || opStart;
        const pickEnd = document.getElementById('pickup_end_time')?.value || opEnd;

        document.querySelectorAll('.daily-op-start').forEach(el => el.value = opStart);
        document.querySelectorAll('.daily-op-end').forEach(el => el.value = opEnd);
        document.querySelectorAll('.daily-pick-start').forEach(el => el.value = pickStart);
        document.querySelectorAll('.daily-pick-end').forEach(el => el.value = pickEnd);
    });
});
</script>
@endpush

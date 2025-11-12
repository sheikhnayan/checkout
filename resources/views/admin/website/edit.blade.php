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
                                            <a href="/admins">
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
                                <div class="card-shadow-primary card-border text-white mb-3 card bg-primary" style="background: #fff !important;">
                                    <form action="{{ route('admin.website.update', $data->id) }}" method="post" enctype="multipart/form-data">
                                        @csrf

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Website Name</label>
                                                        <input type="text" name="name" class="form-control" id="name" placeholder="Website Name" value="{{ $data->name }}" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Domain</label>
                                                        <input type="text" name="domain" class="form-control" id="name" value="{{ $data->domain }}" placeholder="Enter Domain" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="slug" class="form-label">Slug (URL Path)</label>
                                                        <input type="text" name="slug" class="form-control" id="slug" value="{{ $data->slug }}" placeholder="e.g., my-website">
                                                        <small class="form-text text-muted">Current URL: www.domain.com/<strong>{{ $data->slug }}</strong></small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="logo" class="form-label">Logo</label>
                                                        <input type="file" name="logo" class="form-control" id="logo" placeholder="Logo">
                                                    </div>
                                                    <div class="mb-3">
                                                        <img src="{{ asset('uploads/' . $data->logo) }}" width="200px" style="width: 200px;">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="logo_width" class="form-label">Logo Width (px)</label>
                                                        <input type="number" name="logo_width" class="form-control" id="logo_width" value="{{ $data->logo_width }}" placeholder="Width in pixels" min="1">
                                                        <small class="form-text text-muted">Optional: Leave blank for auto-sizing</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="logo_height" class="form-label">Logo Height (px)</label>
                                                        <input type="number" name="logo_height" class="form-control" id="logo_height" value="{{ $data->logo_height }}" placeholder="Height in pixels" min="1">
                                                        <small class="form-text text-muted">Optional: Leave blank for auto-sizing</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="location" class="form-label">Location</label>
                                                        <input type="text" name="location" class="form-control" id="location-input" value="{{ $data->location }}" placeholder="Location" required autocomplete="off">
                                                        <ul id="suggestions"></ul>
                                                        <input type="hidden" name="lat" id="latitude" value="{{ $data->lat }}">
                                                        <input type="hidden" name="long" id="longitude" value="{{ $data->long }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="phone" class="form-label">Phone</label>
                                                        <input type="text" name="phone" class="form-control" value="{{ $data->phone }}" id="phone" placeholder="Phone" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="password" class="form-label">Email</label>
                                                        <input type="email" name="email" class="form-control" value="{{ $data->email }}" id="email" placeholder="Email" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Contact Emails</label>
                                                        <div id="emails-wrapper">
                                                            @foreach ($data->emails as $item)
                                                                <div class="row mb-2 email-group">
                                                                    <div class="col-5">
                                                                        <input type="text" class="form-control email-name" placeholder="Name" value="{{ $item->name }}" required>
                                                                    </div>
                                                                    <div class="col-5">
                                                                        <input type="email" class="form-control email-address" placeholder="Email Address" value="{{ $item->email }}" required>
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <button type="button" class="btn btn-danger remove-email w-100" title="Remove"><i class="fa fa-minus"></i></button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            <div class="row mb-2 email-group">
                                                                <div class="col-5">
                                                                    <input type="text" class="form-control email-name" placeholder="Name">
                                                                </div>
                                                                <div class="col-5">
                                                                    <input type="email" class="form-control email-address" placeholder="Email Address">
                                                                </div>
                                                                <div class="col-2">
                                                                    <button type="button" class="btn btn-success add-email w-100" title="Add Email"><i class="fa fa-plus"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="emails" id="emails-json">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="gratuity_name" class="form-label">Gratuity Field Name</label>
                                                        <input type="text" name="gratuity_name" class="form-control" id="gratuity_name" placeholder="Gratuity Field name" value="{{ $data->gratuity_name }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="gratuity_fee" class="form-label">Gratuity Fee (%)</label>
                                                        <input type="number" name="gratuity_fee" step="0.000001" class="form-control" id="gratuity_fee" placeholder="Gratuity Fee" value="{{ $data->gratuity_fee }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="refundable_name" class="form-label">Non-refundable Field Name</label>
                                                        <input type="text" name="refundable_name" class="form-control" id="refundable_name" placeholder="Non-refundable Field name" value="{{ $data->refundable_name }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="password" class="form-label">Non-refundable Fee (%)</label>
                                                        <input type="number" name="refundable_fee" step="0.000001" class="form-control" id="refundable_fee" value="{{ $data->refundable_fee }}" placeholder="Refundable Fee" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="sales_tax_name" class="form-label">Sales Tax Field Name</label>
                                                        <input type="text" name="sales_tax_name" class="form-control" id="sales_tax_name" placeholder="Sales Tax Field name" value="{{ $data->sales_tax_name }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="password" class="form-label">Sales Tax Fee (%)</label>
                                                        <input type="number" name="sales_tax_fee" step="0.000001" class="form-control" id="sales_tax_fee" value="{{ $data->sales_tax_fee }}" placeholder="Sales Tax Fee" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="service_charge_name" class="form-label">Service Charge Field Name</label>
                                                        <input type="text" name="service_charge_name" class="form-control" id="service_charge_name" placeholder="Service Charge Field name" value="{{ $data->service_charge_name }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="password" class="form-label">Service Charge Fee (%)</label>
                                                        <input type="number" name="service_charge_fee" step="0.000001" class="form-control" id="service_charge_fee" value="{{ $data->service_charge_fee }}" placeholder="Service Charge Fee" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="promo_code_name" class="form-label">Promo Code Field Name</label>
                                                        <input type="text" name="promo_code_name" class="form-control" id="promo_code_name" placeholder="Promo Code Field name" value="{{ $data->promo_code_name }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="reservation" class="form-label">Guest-list visible?</label>
                                                        <select name="reservation" id="reservation" class="form-control">
                                                            <option value="1" {{ $data->reservation == 1 ? 'selected' : '' }}>Yes</option>
                                                            <option value="0" {{ $data->reservation == 0 ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="color" class="form-label">Color</label>
                                                        <input type="color" name="color" class="form-control form-control-color" id="color" value="{{ $data->color }}" placeholder="Color" required style="height: 38px; width: 100px; padding: 2px;">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="color" class="form-label">Secondary Color</label>
                                                        <input type="color" name="secondary_color" class="form-control form-control-color" id="secondary_color" value="{{ $data->secondary_color }}" placeholder="Secondary Color" required style="height: 38px; width: 100px; padding: 2px;">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="color" class="form-label">Background Color</label>
                                                        <input type="color" name="background_color" class="form-control form-control-color" id="background_color" value="{{ $data->background_color }}" placeholder="Background Color" required style="height: 38px; width: 100px; padding: 2px;">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="color" class="form-label">Authorize App Key</label>
                                                        <input type="text" name="authorize_app_key" class="form-control" id="authorize_app_key" value="{{ $data->authorize_app_key }}" placeholder="Authorize App Key">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="color" class="form-label">Authorize Secret Key</label>
                                                        <input type="text" name="authorize_secret_key" class="form-control" id="authorize_secret_key" value="{{ $data->authorize_secret_key }}" placeholder="Authorize Secret Key">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="color" class="form-label">Stripe App Key</label>
                                                        <input type="text" name="stripe_app_key" class="form-control" id="stripe_app_key" value="{{ $data->stripe_app_key }}" placeholder="Stripe App Key">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="color" class="form-label">Stripe Secret Key</label>
                                                        <input type="text" name="stripe_secret_key" class="form-control" id="stripe_secret_key" value="{{ $data->stripe_secret_key }}" placeholder="Stripe Secret Key">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="payment_method" class="form-label">Payment Method</label>
                                                        <select name="payment_method" id="payment_method" class="form-control">
                                                            <option {{ $data->payment_method == 'authorize' ? 'selected' : ''}} value="authorize">Authorize.net</option>
                                                            <option {{ $data->payment_method == 'stripe' ? 'selected' : ''}} value="stripe">Stripe</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="password" class="form-label">Description</label>
                                                        <textarea name="description" class="form-control" id="description" placeholder="Description" required>{{ $data->description }}</textarea>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="guest_list_button_text" class="form-label">Guest List Button Text</label>
                                                        <input type="text" name="guest_list_button_text" class="form-control" id="guest_list_button_text" value="{{ $data->guest_list_button_text ?? 'Guest List' }}" placeholder="Guest List Button Text">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="package_button_text" class="form-label">Package Button Text</label>
                                                        <input type="text" name="package_button_text" class="form-control" id="package_button_text" value="{{ $data->package_button_text ?? 'Packages' }}" placeholder="Package Button Text">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="transportation_confirmation_text" class="form-label">Transportation Confirmation Text</label>
                                                        <textarea name="transportation_confirmation_text" class="form-control" id="transportation_confirmation_text" rows="3" placeholder="Transportation confirmation checkbox text">{{ $data->transportation_confirmation_text ?? 'I confirm I am not arriving via Uber, Lyft, limo, taxi, ride-sharing or any other paid service. I am arriving in a personal vehicle.' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h3>SMTP Configuration</h3>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="host" class="form-label">Host</label>
                                                                <input type="text" name="host" class="form-control" id="host" value="{{ $data->smtp->host }}" placeholder="SMTP Host">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="port" class="form-label">Port</label>
                                                                <input type="number" name="port" class="form-control" id="port" value="{{ $data->smtp->port }}" placeholder="SMTP Port">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="username" class="form-label">Username</label>
                                                                <input type="text" name="username" class="form-control" value="{{ $data->smtp->username }}" id="username" placeholder="SMTP Username">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="password" class="form-label">Password</label>
                                                                <input type="text" name="password" class="form-control" value="{{ $data->smtp->password }}" id="password" placeholder="SMTP Password">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="encryption" class="form-label">Encryption</label>
                                                                <select name="encryption" class="form-select" id="encryption">
                                                                    <option {{ $data->smtp->encryption == 'tls' ? 'selected' : '' }} value="tls">TLS</option>
                                                                    <option {{ $data->smtp->encryption == 'ssl' ? 'selected' : '' }} value="ssl">SSL</option>
                                                                    <option {{ $data->smtp->encryption == 'none' ? 'selected' : '' }} value="none">None</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="from_address" class="form-label">From Address</label>
                                                                <input type="email" value="{{ $data->smtp->from_email }}" name="from_address" class="form-control" id="from_address" placeholder="From Address">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="from_name" class="form-label">From Name</label>
                                                                <input type="text" value="{{ $data->smtp->from_name }}" name="from_name" class="form-control" id="from_name" placeholder="From Name">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h3>Redirect Pages</h3>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="back_text" class="form-label">Back Button Text</label>
                                                                <input type="text" name="back_text" class="form-control" id="back_text" value="{{ $data->back_text }}" placeholder="Back Button Text">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="privacy_policy" class="form-label">Back Button Link</label>
                                                                <input type="text" name="back_link" class="form-control" id="privacy_policy" value="{{ $data->back_link }}" placeholder="Back Button Link">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="terms_conditions" class="form-label">Footer Text</label>
                                                                <input type="text" name="footer_text" class="form-control" id="terms_conditions" value="{{ $data->footer_text }}" placeholder="Footer Text">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="success_page" class="form-label">Success Page</label>
                                                                <input type="text" name="success_page" class="form-control" value="{{ $data->success_page }}" id="success_page" placeholder="Success Page URL">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="privacy_policy" class="form-label">Privacy & Policy Page</label>
                                                                <input type="text" name="policy" class="form-control" value="{{ $data->policy }}" id="privacy_policy" placeholder="Privacy & Policy Page URL">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="terms_conditions" class="form-label">Terms & Conditions Page</label>
                                                                <input type="text" name="terms" class="form-control" value="{{ $data->terms }}" id="terms_conditions" placeholder="Terms & Conditions Page URL">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="font_color" class="form-label">Font Color</label>
                                                                <input type="color" name="font_color" class="form-control form-control-color" id="font_color" value="{{ $data->font_color ?? '#000000' }}" title="Choose font color">
                                                                <small class="form-text text-muted">Select the font color for text elements on your website</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="description_label" class="form-label">Description Label</label>
                                                                <input type="text" name="description_label" class="form-control" id="description_label" value="{{ $data->description_label ?? 'Description' }}" placeholder="Description">
                                                                <small class="form-text text-muted">Label text that appears above the event description</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="text_description" class="form-label">Website Description</label>
                                                                <textarea name="text_description" class="form-control" id="text_description" rows="3" placeholder="Enter a description for your website that will be displayed on the front-end">{{ $data->text_description ?? '' }}</textarea>
                                                                <small class="form-text text-muted">This text will be displayed on your website's front-end</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Payment Logo Management -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h3>Payment Method Logos</h3>
                                                    <div id="payment-logos-wrapper">
                                                        @foreach($data->paymentLogos as $index => $logo)
                                                            <div class="row mb-2 payment-logo-group">
                                                                <div class="col-md-3">
                                                                    <input type="text" name="payment_logos[{{ $index }}][name]" class="form-control" placeholder="Payment Method Name" value="{{ $logo->name }}" required>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="file" name="payment_logos[{{ $index }}][logo]" class="form-control" accept="image/*">
                                                                    <small class="text-muted">Current: {{ $logo->logo ?: 'No logo' }}</small>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <input type="number" name="payment_logos[{{ $index }}][order]" class="form-control" placeholder="Order" value="{{ $logo->order }}" min="0">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <select name="payment_logos[{{ $index }}][is_active]" class="form-control">
                                                                        <option value="1" {{ $logo->is_active ? 'selected' : '' }}>Active</option>
                                                                        <option value="0" {{ !$logo->is_active ? 'selected' : '' }}>Inactive</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button" class="btn btn-danger remove-payment-logo w-100" title="Remove"><i class="fa fa-minus"></i></button>
                                                                </div>
                                                                <input type="hidden" name="payment_logos[{{ $index }}][id]" value="{{ $logo->id }}">
                                                            </div>
                                                        @endforeach
                                                        <div class="row mb-2 payment-logo-group">
                                                            <div class="col-md-3">
                                                                <input type="text" name="payment_logos[new][name]" class="form-control" placeholder="Payment Method Name">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="file" name="payment_logos[new][logo]" class="form-control" accept="image/*">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="number" name="payment_logos[new][order]" class="form-control" placeholder="Order" value="0" min="0">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <select name="payment_logos[new][is_active]" class="form-control">
                                                                    <option value="1">Active</option>
                                                                    <option value="0">Inactive</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-success add-payment-logo w-100" title="Add Logo"><i class="fa fa-plus"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <a href="{{ route('admin.website.index') }}" class="btn btn-danger">Cancel</a>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
            // Dynamic name/email pairs as objects in one array
            $(document).on('click', '.add-email', function() {
                const emailGroup = `<div class=\"row mb-2 email-group\">\
                    <div class=\"col-5\">\
                        <input type=\"text\" class=\"form-control email-name\" placeholder=\"Name\" required>\
                    </div>\
                    <div class=\"col-5\">\
                        <input type=\"email\" class=\"form-control email-address\" placeholder=\"Email Address\" required>\
                    </div>\
                    <div class=\"col-2\">\
                        <button type=\"button\" class=\"btn btn-danger remove-email w-100\" title=\"Remove\"><i class=\"fa fa-minus\"></i></button>\
                    </div>\
                </div>`;
                $('#emails-wrapper').append(emailGroup);
            });
            $(document).on('click', '.remove-email', function() {
                $(this).closest('.email-group').remove();
            });
            // Serialize name/email pairs to JSON on submit
            $('form').on('submit', function(e) {
                var emails = [];
                $('#emails-wrapper .email-group').each(function() {
                    var name = $(this).find('.email-name').val();
                    var address = $(this).find('.email-address').val();
                    if (name && address) {
                        emails.push({name: name, email: address});
                    }
                });
                $('#emails-json').val(JSON.stringify(emails));
            });
            
            // Dynamic payment logo functionality
            let paymentLogoIndex = {{ $data->paymentLogos->count() }};
            
            $(document).on('click', '.add-payment-logo', function() {
                paymentLogoIndex++;
                const paymentLogoGroup = `<div class="row mb-2 payment-logo-group">
                    <div class="col-md-3">
                        <input type="text" name="payment_logos[${paymentLogoIndex}][name]" class="form-control" placeholder="Payment Method Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="file" name="payment_logos[${paymentLogoIndex}][logo]" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="payment_logos[${paymentLogoIndex}][order]" class="form-control" placeholder="Order" value="0" min="0">
                    </div>
                    <div class="col-md-2">
                        <select name="payment_logos[${paymentLogoIndex}][is_active]" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-payment-logo w-100" title="Remove"><i class="fa fa-minus"></i></button>
                    </div>
                </div>`;
                $('#payment-logos-wrapper').append(paymentLogoGroup);
            });
            
            $(document).on('click', '.remove-payment-logo', function() {
                $(this).closest('.payment-logo-group').remove();
            });
            </script>
@endsection

@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">

<style>
    .website-section-title {
        background: var(--admin-surface-2);
        color: var(--admin-text) !important;
        border: 1px solid var(--admin-border);
        border-radius: 8px;
        padding: 10px 12px;
    }

    .website-section-title h5,
    .website-section-title h6,
    .website-section-title small,
    .website-section-title i {
        color: var(--admin-text) !important;
    }

    .card-body label {
        color: var(--admin-text) !important;
    }

    .card.card-border .card-body {
        padding-top: 1.35rem;
    }

    .card.card-border .card-body .row:first-child {
        margin-top: 0.25rem;
    }

    .sandbox-toggle-wrap {
        min-height: 100%;
        display: flex;
        align-items: flex-end;
    }

    .sandbox-toggle {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 0.65rem;
        font-weight: 600;
        color: var(--admin-text);
    }

    .sandbox-toggle input[type="checkbox"] {
        margin: 0;
        width: 18px;
        height: 18px;
    }

    .charge-help {
        background: rgba(255, 204, 0, 0.12);
        border: 1px solid rgba(255, 204, 0, 0.4);
        color: var(--admin-text);
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        margin-bottom: 14px;
    }
</style>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl-12 mb-6 order-0">
                <div class="app-main__inner">
                    <div class="app-page-title mt-4">
                        <div class="page-title-wrapper">
                            <div class="page-title-heading">
                                <div class="page-title-icon">
                                    <i class="fas fa-credit-card icon-gradient bg-arielle-smile"></i>
                                </div>
                                <div>
                                    <span class="text-capitalize">Payment Settings</span>
                                    <div class="page-subtitle">{{ $website->name }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="page-title-subheading opacity-10 mt-3" style="white-space: nowrap; overflow-x: auto;">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb" style="float: left">
                                    <li class="breadcrumb-item opacity-10">
                                        <a href="#"><i class="fas fa-home"></i></a>
                                        <i class="fas fa-chevron-right ms-1"></i>
                                    </li>
                                    <li class="breadcrumb-item">Setting <i class="fas fa-chevron-right ms-1"></i></li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.website.index') }}">Website</a>
                                        <i class="fas fa-chevron-right ms-1"></i>
                                    </li>
                                    <li class="active breadcrumb-item">Payment Settings</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    {{-- Back Button --}}
                    <div class="mb-4">
                        <a href="{{ route('admin.website.edit', $website->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Website Settings
                        </a>
                    </div>

                    {{-- Success Message --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Validation Errors:</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.website.payment-settings.update', $website->id) }}" method="POST">
                        @csrf
                        
                        {{-- Payment Method Section --}}
                        <div class="card card-border mb-4">
                            <div class="card-header website-section-title">
                                <h5 class="mb-0">
                                    <i class="fas fa-exchange-alt me-2"></i>Payment Provider
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label">Payment Gateway <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The payment processor used to handle card transactions for this website."></i></label>
                                            <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror">
                                                <option value="authorize" @selected(old('payment_method', $website->payment_method) == 'authorize')>Authorize.net</option>
                                                <option value="stripe" @selected(old('payment_method', $website->payment_method) == 'stripe')>Stripe</option>
                                            </select>
                                            @error('payment_method')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sandbox-toggle-wrap">
                                            <label for="sandbox_mode" class="sandbox-toggle">
                                                <input type="checkbox" name="sandbox_mode" id="sandbox_mode" value="1" @checked(old('sandbox_mode', $website->sandbox_mode))>
                                                <span>Enable Sandbox Mode (Testing)</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Stripe Credentials Section --}}
                        <div class="card card-border mb-4" id="stripe-section">
                            <div class="card-header website-section-title">
                                <h5 class="mb-0">
                                    <i class="fab fa-stripe me-2"></i>Stripe API Credentials
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stripe_public_key" class="form-label">Public Key <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The Stripe Publishable Key used in the frontend checkout form. Safe to expose."></i></label>
                                            <input type="text" name="stripe_public_key" id="stripe_public_key" class="form-control @error('stripe_public_key') is-invalid @enderror" 
                                                value="{{ old('stripe_public_key', $website->stripe_public_key) }}" placeholder="pk_live_...">
                                            @error('stripe_public_key')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stripe_app_key" class="form-label">Publishable Key <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="An alternate Stripe Publishable Key field. Used on the frontend checkout form."></i></label>
                                            <input type="text" name="stripe_app_key" id="stripe_app_key" class="form-control @error('stripe_app_key') is-invalid @enderror" 
                                                value="{{ old('stripe_app_key', $website->stripe_app_key) }}" placeholder="pk_live_...">
                                            @error('stripe_app_key')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="stripe_secret_key" class="form-label">Secret Key <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The Stripe Secret Key used server-side to process payments. Never expose this publicly."></i></label>
                                            <input type="password" name="stripe_secret_key" id="stripe_secret_key" class="form-control @error('stripe_secret_key') is-invalid @enderror" 
                                                value="{{ old('stripe_secret_key', $website->stripe_secret_key) }}" placeholder="sk_live_...">
                                            <small class="form-text text-muted">Click the eye icon to show/hide</small>
                                            @error('stripe_secret_key')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Authorize.net Credentials Section --}}
                        <div class="card card-border mb-4" id="authorize-section">
                            <div class="card-header website-section-title">
                                <h5 class="mb-0">
                                    <i class="fas fa-lock me-2"></i>Authorize.net API Credentials
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="authorize_login_id" class="form-label">Login ID <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Your Authorize.net API Login ID."></i></label>
                                            <input type="text" name="authorize_login_id" id="authorize_login_id" class="form-control @error('authorize_login_id') is-invalid @enderror" 
                                                value="{{ old('authorize_login_id', $website->authorize_login_id) }}" placeholder="Your API Login ID">
                                            @error('authorize_login_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="authorize_transaction_key" class="form-label">Transaction Key <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Your Authorize.net Transaction Key."></i></label>
                                            <input type="password" name="authorize_transaction_key" id="authorize_transaction_key" class="form-control @error('authorize_transaction_key') is-invalid @enderror" 
                                                value="{{ old('authorize_transaction_key', $website->authorize_transaction_key) }}" placeholder="Your Transaction Key">
                                            @error('authorize_transaction_key')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="authorize_app_key" class="form-label">App Key <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Your Authorize.net Client Key used on the frontend for secure card capture."></i></label>
                                            <input type="password" name="authorize_app_key" id="authorize_app_key" class="form-control @error('authorize_app_key') is-invalid @enderror" 
                                                value="{{ old('authorize_app_key', $website->authorize_app_key) }}" placeholder="Your App Key">
                                            @error('authorize_app_key')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="authorize_secret_key" class="form-label">Secret Key <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Your Authorize.net secret key used for server-side transaction processing."></i></label>
                                            <input type="password" name="authorize_secret_key" id="authorize_secret_key" class="form-control @error('authorize_secret_key') is-invalid @enderror" 
                                                value="{{ old('authorize_secret_key', $website->authorize_secret_key) }}" placeholder="Your Secret Key">
                                            @error('authorize_secret_key')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Fees Configuration Section --}}
                        <div class="card card-border mb-4">
                            <div class="card-header website-section-title">
                                <h5 class="mb-0">
                                    <i class="fas fa-percentage me-2"></i>Fee Configuration
                                </h5>
                                <small>Configure transaction fees applied to all charges</small>
                            </div>
                            <div class="card-body">
                                <div class="charge-help">
                                    To remove a charge from checkout totals and summary:
                                    set the related <strong>Field Name</strong> to <strong>0</strong> or leave it blank.
                                    For percentage/amount based charges, set the <strong>Fee</strong> to <strong>0</strong>.
                                </div>

                                {{-- Processing Fee --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="mb-3">Processing Fee</h6>
                                        <p class="text-muted small mb-3">Fee applied to all transactions. promoter commissions are calculated on the amount BEFORE this fee.</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="processing_fee" class="form-label">Processing Fee Amount <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The extra fee charged to customers per transaction. Can be a fixed dollar value or a percentage."></i></label>
                                            <input type="number" name="processing_fee" id="processing_fee" class="form-control @error('processing_fee') is-invalid @enderror" 
                                                step="0.01" value="{{ old('processing_fee', $website->processing_fee ?? 0) }}" placeholder="0.00">
                                            @error('processing_fee')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="processing_fee_type" class="form-label">Fee Type <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Whether the processing fee is a fixed dollar amount or a percentage of the order total."></i></label>
                                            <select name="processing_fee_type" id="processing_fee_type" class="form-control @error('processing_fee_type') is-invalid @enderror">
                                                <option value="percentage" @selected(old('processing_fee_type', $website->processing_fee_type ?? 'percentage') == 'percentage')>Percentage (%)</option>
                                                <option value="flat" @selected(old('processing_fee_type', $website->processing_fee_type) == 'flat')>Flat ($)</option>
                                            </select>
                                            @error('processing_fee_type')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                {{-- Gratuity Fee --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="mb-3">Gratuity/Tip</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="gratuity_name" class="form-label">Gratuity Field Name <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The label shown to customers for the gratuity/tip line item at checkout."></i></label>
                                            <input type="text" name="gratuity_name" id="gratuity_name" class="form-control @error('gratuity_name') is-invalid @enderror" 
                                                value="{{ old('gratuity_name', $website->gratuity_name) }}" placeholder="e.g., Gratuity, Tip">
                                            @error('gratuity_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="gratuity_fee" class="form-label">Gratuity Fee (%) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The gratuity percentage automatically applied to bookings for this website."></i></label>
                                            <input type="number" name="gratuity_fee" id="gratuity_fee" class="form-control @error('gratuity_fee') is-invalid @enderror" 
                                                step="0.000001" value="{{ old('gratuity_fee', $website->gratuity_fee) }}" placeholder="0.00">
                                            @error('gratuity_fee')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                {{-- Refundable Fee --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="mb-3">Refundable Fee/Deposit</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="refundable_name" class="form-label">Refundable Field Name <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The label shown to customers for the refundable deposit line item at checkout."></i></label>
                                            <input type="text" name="refundable_name" id="refundable_name" class="form-control @error('refundable_name') is-invalid @enderror" 
                                                value="{{ old('refundable_name', $website->refundable_name) }}" placeholder="e.g., Deposit, Security Deposit">
                                            @error('refundable_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="refundable_fee" class="form-label">Refundable Fee (%) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The percentage of the booking total held as a refundable security deposit."></i></label>
                                            <input type="number" name="refundable_fee" id="refundable_fee" class="form-control @error('refundable_fee') is-invalid @enderror" 
                                                step="0.000001" value="{{ old('refundable_fee', $website->refundable_fee) }}" placeholder="0.00">
                                            @error('refundable_fee')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                {{-- Sales Tax --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="mb-3">Sales Tax</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sales_tax_name" class="form-label">Sales Tax Field Name <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The label shown to customers for the sales tax line item at checkout."></i></label>
                                            <input type="text" name="sales_tax_name" id="sales_tax_name" class="form-control @error('sales_tax_name') is-invalid @enderror" 
                                                value="{{ old('sales_tax_name', $website->sales_tax_name) }}" placeholder="e.g., Sales Tax">
                                            @error('sales_tax_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sales_tax_fee" class="form-label">Sales Tax Fee (%) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The sales tax percentage applied to bookings for this website."></i></label>
                                            <input type="number" name="sales_tax_fee" id="sales_tax_fee" class="form-control @error('sales_tax_fee') is-invalid @enderror" 
                                                step="0.000001" value="{{ old('sales_tax_fee', $website->sales_tax_fee) }}" placeholder="0.00">
                                            @error('sales_tax_fee')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                {{-- Service Charge --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="mb-3">Service Charge</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="service_charge_name" class="form-label">Service Charge Field Name <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The label shown to customers for the service charge line item at checkout."></i></label>
                                            <input type="text" name="service_charge_name" id="service_charge_name" class="form-control @error('service_charge_name') is-invalid @enderror" 
                                                value="{{ old('service_charge_name', $website->service_charge_name) }}" placeholder="e.g., Service Charge">
                                            @error('service_charge_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="service_charge_fee" class="form-label">Service Charge Fee (%) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The service charge percentage applied to bookings for this website."></i></label>
                                            <input type="number" name="service_charge_fee" id="service_charge_fee" class="form-control @error('service_charge_fee') is-invalid @enderror" 
                                                step="0.000001" value="{{ old('service_charge_fee', $website->service_charge_fee) }}" placeholder="0.00">
                                            @error('service_charge_fee')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                {{-- Promo Code --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="mb-3">Promo Code Field</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="promo_code_name" class="form-label">Promo Code Field Name <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The label shown to customers for the promo code input field at checkout."></i></label>
                                            <input type="text" name="promo_code_name" id="promo_code_name" class="form-control @error('promo_code_name') is-invalid @enderror" 
                                                value="{{ old('promo_code_name', $website->promo_code_name) }}" placeholder="e.g., Promo Code, Coupon">
                                            @error('promo_code_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Commission Hold Days --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="mb-3">Commission Hold Period</h6>
                                        <p class="small mb-3" style="color: #6c757d;">Number of days after a transaction before promoter commissions are released. Leave blank to use the system default.</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="commission_hold_days" class="form-label">Hold Days (Stripe) <span class="text-muted small">— default: 60</span> <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Number of days before Stripe releases held funds to your account."></i></label>
                                            <input type="number" name="commission_hold_days" id="commission_hold_days" class="form-control @error('commission_hold_days') is-invalid @enderror"
                                                min="0" max="365" value="{{ old('commission_hold_days', $website->commission_hold_days) }}" placeholder="60">
                                            @error('commission_hold_days')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="commission_hold_days_authorize" class="form-label">Hold Days (Authorize.net) <span class="text-muted small">— default: 90</span> <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Number of days before Authorize.net releases held funds to your account."></i></label>
                                            <input type="number" name="commission_hold_days_authorize" id="commission_hold_days_authorize" class="form-control @error('commission_hold_days_authorize') is-invalid @enderror"
                                                min="0" max="365" value="{{ old('commission_hold_days_authorize', $website->commission_hold_days_authorize) }}" placeholder="90">
                                            @error('commission_hold_days_authorize')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-save me-2"></i>Save Payment Settings
                                </button>
                                <a href="{{ route('admin.website.edit', $website->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const stripeSection = document.getElementById('stripe-section');
        const authorizeSection = document.getElementById('authorize-section');

        function togglePaymentSections() {
            const method = paymentMethodSelect.value;
            stripeSection.style.display = method === 'stripe' ? 'block' : 'none';
            authorizeSection.style.display = method === 'authorize' ? 'block' : 'none';
        }

        paymentMethodSelect.addEventListener('change', togglePaymentSections);
        togglePaymentSections(); // Initial call
    });
</script>
@endsection

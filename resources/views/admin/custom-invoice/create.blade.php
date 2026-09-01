@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
/* Executive Light Professional Workspace - Zero Dark Backgrounds & Crisp Solid Black Fonts */
html body .custom-invoice-page-wrapper,
html body .content-wrapper,
html body .app-main__inner,
html body .layout-page,
html body .custom-invoice-page-wrapper .row,
html body .custom-invoice-page-wrapper div:not(.alert):not(.alert *) {
    background-color: #ffffff !important;
    background: #ffffff !important;
    color: #000000 !important;
}

html body .custom-invoice-page-wrapper .alert-success {
    background-color: #d1e7dd !important;
    background: #d1e7dd !important;
    border-color: #badbcc !important;
    color: #0f5132 !important;
    font-weight: 600 !important;
}

/* Card & Box Containers */
html body .custom-invoice-page-wrapper .card,
html body .custom-invoice-page-wrapper .card-shadow-primary,
html body .custom-invoice-page-wrapper .card-border {
    background-color: #ffffff !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04) !important;
    color: #000000 !important;
    padding: 0 !important;
    overflow: hidden !important;
}

html body .custom-invoice-page-wrapper .card-header {
    background-color: #ffffff !important;
    background: #ffffff !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 20px 24px !important;
    color: #000000 !important;
}

html body .custom-invoice-page-wrapper .card-body {
    background-color: #ffffff !important;
    background: #ffffff !important;
    padding: 24px !important;
    color: #000000 !important;
}

html body .custom-invoice-page-wrapper .card-footer {
    background-color: #ffffff !important;
    background: #ffffff !important;
    border-top: 1px solid #e2e8f0 !important;
    padding: 16px 24px !important;
}

/* ABSOLUTE FORCE PURE BLACK (#000000) FOR ALL CARD TITLES, CARD HEADERS, & HEADINGS */
html body .custom-invoice-page-wrapper .card-title,
html body .custom-invoice-page-wrapper .card-header .card-title,
html body .content-wrapper .custom-invoice-page-wrapper .card-title,
html body .content-wrapper .custom-invoice-page-wrapper .card-header .card-title,
html body .content-wrapper .card-header .card-title,
html body .custom-invoice-page-wrapper .card h1,
html body .custom-invoice-page-wrapper .card h2,
html body .custom-invoice-page-wrapper .card h3,
html body .custom-invoice-page-wrapper .card h4,
html body .custom-invoice-page-wrapper .card h5,
html body .custom-invoice-page-wrapper .card h6,
html body .custom-invoice-page-wrapper h1,
html body .custom-invoice-page-wrapper h2,
html body .custom-invoice-page-wrapper h3,
html body .custom-invoice-page-wrapper h4,
html body .custom-invoice-page-wrapper h5,
html body .custom-invoice-page-wrapper h6,
html body .custom-invoice-page-wrapper .page-title-heading span,
html body .custom-invoice-page-wrapper .app-page-title h1,
html body .custom-invoice-page-wrapper .app-page-title h2,
html body .custom-invoice-page-wrapper .app-page-title h3,
html body .custom-invoice-page-wrapper .app-page-title h4,
html body .custom-invoice-page-wrapper .app-page-title h5,
html body .custom-invoice-page-wrapper .app-page-title h6 {
    color: #000000 !important;
    font-weight: 800 !important;
    opacity: 1 !important;
}

/* ABSOLUTE 100% FORCE SOLID BLACK (#000000) FOR ALL LABELS, HEADINGS, PARAGRAPHS & TEXT */
html body .custom-invoice-page-wrapper form label,
html body .custom-invoice-page-wrapper form .form-label,
html body .custom-invoice-page-wrapper form .form-group label,
html body .custom-invoice-page-wrapper form .form-section-title,
html body .custom-invoice-page-wrapper form .section-title,
html body .custom-invoice-page-wrapper form .website-section-title,
html body .custom-invoice-page-wrapper form .card-title,
html body .custom-invoice-page-wrapper form h1,
html body .custom-invoice-page-wrapper form h2,
html body .custom-invoice-page-wrapper form h3,
html body .custom-invoice-page-wrapper form h4,
html body .custom-invoice-page-wrapper form h5,
html body .custom-invoice-page-wrapper form h6,
html body .custom-invoice-page-wrapper form span,
html body .custom-invoice-page-wrapper form p,
html body .custom-invoice-page-wrapper form small,
html body .custom-invoice-page-wrapper form i,
html body .custom-invoice-page-wrapper form .field-tip,
html body .custom-invoice-page-wrapper strong,
html body .custom-invoice-page-wrapper b,
html body .custom-invoice-page-wrapper p,
html body .custom-invoice-page-wrapper span,
html body .custom-invoice-page-wrapper li,
html body .custom-invoice-page-wrapper small,
html body .custom-invoice-page-wrapper .text-muted,
html body .custom-invoice-page-wrapper .help-text,
html body .custom-invoice-page-wrapper .field-tip,
html body .custom-invoice-page-wrapper td,
html body .custom-invoice-page-wrapper th,
html body .custom-invoice-page-wrapper i {
    color: #000000 !important;
    opacity: 1 !important;
}

html body .custom-invoice-page-wrapper label,
html body .custom-invoice-page-wrapper .form-label,
html body .custom-invoice-page-wrapper form label,
html body .custom-invoice-page-wrapper form .form-label {
    color: #000000 !important;
    font-weight: 800 !important;
    font-size: 14px !important;
}

html body .custom-invoice-page-wrapper label,
html body .custom-invoice-page-wrapper .form-label,
html body .custom-invoice-page-wrapper .card-title,
html body .custom-invoice-page-wrapper h1,
html body .custom-invoice-page-wrapper h2,
html body .custom-invoice-page-wrapper h3,
html body .custom-invoice-page-wrapper h4,
html body .custom-invoice-page-wrapper h5,
html body .custom-invoice-page-wrapper h6 {
    font-weight: 700 !important;
}

html body .custom-invoice-page-wrapper .breadcrumb-item,
html body .custom-invoice-page-wrapper .breadcrumb-item a,
html body .custom-invoice-page-wrapper .breadcrumb-item i {
    color: #334155 !important;
    font-weight: 600 !important;
}

/* Global Back Button Styling */
html body .admin-global-back-btn {
    background-color: #ffffff !important;
    background: #ffffff !important;
    color: #000000 !important;
    border: 1px solid #cbd5e1 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06) !important;
    border-radius: 8px !important;
    font-weight: 700 !important;
}

html body .admin-global-back-btn i {
    background: #f1f5f9 !important;
    color: #000000 !important;
}

/* Form Controls & Inputs */
html body .custom-invoice-page-wrapper .form-control,
html body .custom-invoice-page-wrapper .form-select,
html body .custom-invoice-page-wrapper textarea,
html body .custom-invoice-page-wrapper input,
html body .custom-invoice-page-wrapper select {
    background-color: #ffffff !important;
    background: #ffffff !important;
    color: #000000 !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
}

html body .custom-invoice-page-wrapper .form-select option {
    background-color: #ffffff !important;
    color: #000000 !important;
}

html body .custom-invoice-page-wrapper .form-control:focus,
html body .custom-invoice-page-wrapper .form-select:focus,
html body .custom-invoice-page-wrapper textarea:focus,
html body .custom-invoice-page-wrapper input:focus,
html body .custom-invoice-page-wrapper select:focus {
    background-color: #ffffff !important;
    background: #ffffff !important;
    color: #000000 !important;
    border-color: #000000 !important;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1) !important;
}

/* Invoice Item Row Card */
html body .custom-invoice-page-wrapper .invoice-item {
    background-color: #f8fafc !important;
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 18px !important;
    margin-bottom: 16px !important;
}

/* Badges & Status Pills: White Text Inside All Badges */
html body .custom-invoice-page-wrapper .badge,
html body .custom-invoice-page-wrapper .badge *,
html body .custom-invoice-page-wrapper span.badge,
html body .custom-invoice-page-wrapper span.badge *,
html body .custom-invoice-page-wrapper .badge-success,
html body .custom-invoice-page-wrapper .badge-info,
html body .custom-invoice-page-wrapper .badge-primary,
html body .custom-invoice-page-wrapper .badge-danger,
html body .custom-invoice-page-wrapper .badge-warning,
html body .custom-invoice-page-wrapper .badge-secondary,
html body .custom-invoice-page-wrapper .badge-dark {
    color: #ffffff !important;
    font-weight: 700 !important;
    opacity: 1 !important;
}

/* Colored / Dark Action Buttons: White Text & Icons */
html body .custom-invoice-page-wrapper .btn-primary,
html body .custom-invoice-page-wrapper .btn-primary *,
html body .custom-invoice-page-wrapper .btn-primary i,
html body .custom-invoice-page-wrapper .btn-success,
html body .custom-invoice-page-wrapper .btn-success *,
html body .custom-invoice-page-wrapper .btn-success i,
html body .custom-invoice-page-wrapper .btn-danger,
html body .custom-invoice-page-wrapper .btn-danger *,
html body .custom-invoice-page-wrapper .btn-danger i,
html body .custom-invoice-page-wrapper .btn-warning,
html body .custom-invoice-page-wrapper .btn-warning *,
html body .custom-invoice-page-wrapper .btn-warning i,
html body .custom-invoice-page-wrapper .btn-info,
html body .custom-invoice-page-wrapper .btn-info *,
html body .custom-invoice-page-wrapper .btn-info i,
html body .custom-invoice-page-wrapper .btn-dark,
html body .custom-invoice-page-wrapper .btn-dark *,
html body .custom-invoice-page-wrapper .btn-dark i {
    color: #ffffff !important;
    opacity: 1 !important;
}

/* White Background Buttons: Keep Dark Text & Dark Icons */
html body .custom-invoice-page-wrapper .btn-outline-secondary,
html body .custom-invoice-page-wrapper .btn-outline-secondary *,
html body .custom-invoice-page-wrapper .btn-outline-secondary i,
html body .custom-invoice-page-wrapper .btn-secondary,
html body .custom-invoice-page-wrapper .btn-secondary *,
html body .custom-invoice-page-wrapper .btn-secondary i,
html body .custom-invoice-page-wrapper .btn-light,
html body .custom-invoice-page-wrapper .btn-light *,
html body .custom-invoice-page-wrapper .btn-light i {
    color: #000000 !important;
    opacity: 1 !important;
}
</style>

<div class="custom-invoice-page-wrapper">
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-fluid px-3 px-md-4 flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="app-main__inner">
                        <div class="app-page-title mt-3">
                            <div class="page-title-wrapper">
                                <div class="page-title-heading">
                                    <div class="page-title-icon">
                                        <i class="fas fa-file-invoice text-primary"></i>
                                    </div>
                                    <div>
                                        <span class="text-capitalize fs-4 fw-bold">Create Custom Invoice</span>
                                    </div>
                                </div>
                            </div>

                            <div class="page-title-subheading opacity-10 mt-3">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb" style="float: left">
                                        <li class="breadcrumb-item opacity-10">
                                            <a href="#">
                                                <i class="fas fa-home"></i>
                                            </a>
                                            <i class="fas fa-chevron-right ms-1"></i>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('admin.custom-invoice.index') }}">Custom Invoices</a>
                                            <i class="fas fa-chevron-right ms-1"></i>
                                        </li>
                                        <li class="active breadcrumb-item">Create</li>
                                    </ol>
                                    <div style="float: right">
                                        <a href="{{ route('admin.custom-invoice.index') }}" class="btn btn-dark">
                                            <i class="fas fa-arrow-left text-white"></i> <span class="text-white">Back</span>
                                        </a>
                                    </div>
                                </nav>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card-shadow-primary card-border mb-3 card p-4">
                                    <div class="card-header border-bottom p-0 pb-3">
                                        <h5 class="card-title">Create New Invoice</h5>
                                    </div>

                                    <form action="{{ route('admin.custom-invoice.store') }}" method="POST" id="invoiceForm">
                                        @csrf

                                        @if ($errors->any())
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong>
                                                <ul class="mb-0" style="margin-left: 20px;">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            </div>
                                        @endif

                                        <div class="card-body pt-3">
                                            <!-- Website Selection -->
                                            <div class="form-group mb-3">
                                                <label for="website_id" class="form-label">Website <span class="text-danger">*</span> <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The club or venue this invoice is being issued on behalf of."></i></label>
                                                <select name="website_id" id="website_id" class="form-select @error('website_id') is-invalid @enderror" required>
                                                    <option value="">-- Select Website --</option>
                                                     @foreach($websites as $website)
                                                         <option value="{{ $website->id }}" 
                                                             {{ old('website_id') == $website->id ? 'selected' : '' }}
                                                             data-timezone="{{ $website->resolved_timezone ?? 'America/Los_Angeles' }}"
                                                             data-operating-start="{{ $website->operating_start_time ?? '' }}"
                                                             data-operating-end="{{ $website->operating_end_time ?? '' }}"
                                                             data-daily-hours="{{ htmlspecialchars(json_encode($website->getDailyOperatingHoursMap()), ENT_QUOTES, 'UTF-8') }}"
                                                            data-gratuity-fee="{{ $website->gratuity_fee ?? 0 }}"
                                                            data-gratuity-name="{{ $website->gratuity_name ?? 'Gratuity Fee' }}"
                                                            data-refundable-fee="{{ $website->refundable_fee ?? 0 }}"
                                                            data-refundable-name="{{ $website->refundable_name ?? 'Refundable Fee' }}"
                                                            data-sales-tax-fee="{{ $website->sales_tax_fee ?? 0 }}"
                                                            data-sales-tax-name="{{ $website->sales_tax_name ?? 'Sales Tax' }}"
                                                            data-service-charge-fee="{{ $website->service_charge_fee ?? 0 }}"
                                                            data-service-charge-name="{{ $website->service_charge_name ?? 'Service Charge' }}"
                                                            data-processing-fee="{{ $website->processing_fee ?? 0 }}"
                                                            data-processing-fee-type="{{ $website->processing_fee_type ?? 'percentage' }}">
                                                            {{ $website->name }}
                                                        </option>
                                                     @endforeach
                                                </select>
                                                @error('website_id')
                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Client Information -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="client_name" class="form-label">Client Name <span class="text-danger">*</span> <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Full name of the person or business being invoiced."></i></label>
                                                        <input type="text" name="client_name" id="client_name" class="form-control @error('client_name') is-invalid @enderror" 
                                                               value="{{ old('client_name') }}" required>
                                                        @error('client_name')
                                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="client_email" class="form-label">Client Email <span class="text-danger">*</span> <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Email address where the invoice and payment link will be sent."></i></label>
                                                        <input type="email" name="client_email" id="client_email" class="form-control @error('client_email') is-invalid @enderror" 
                                                               value="{{ old('client_email') }}" required>
                                                        @error('client_email')
                                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                             <!-- Reservation Date & Arrival Time (Optional Pre-selection) -->
                                             <div class="row mb-3">
                                                 <div class="col-md-6">
                                                     <div class="form-group mb-3">
                                                         <label for="package_use_date" class="form-label">Reservation / Visit Date (Optional Pre-selection) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Preselect visit date for client. If left blank, client must select date at checkout."></i></label>
                                                         <input type="date" name="package_use_date" id="package_use_date" class="form-control" value="{{ old('package_use_date') }}" min="{{ \Carbon\Carbon::now('America/Los_Angeles')->format('Y-m-d') }}">
                                                         <small class="text-muted">Optional: Preselect date or leave empty for client to pick at payment.</small>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-6">
                                                     <div class="form-group mb-3">
                                                         <label for="transportation_arrival_time" class="form-label">Estimated Arrival Time (Optional Pre-selection) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Preselect arrival time for client. If left blank, client must select time at checkout."></i></label>
                                                         <select name="transportation_arrival_time" id="transportation_arrival_time" class="form-select">
                                                             <option value="">-- Client Will Select at Checkout --</option>
                                                         </select>
                                                         <small class="text-muted">Optional: Preselect time or leave empty for client to pick at payment.</small>
                                                     </div>
                                                 </div>
                                             </div>
                                             
                                             <!-- Day-Wise Venue Operating Hours Badge -->
                                             <div class="row mb-3" id="arrival-hours-badge-row" style="display: none;">
                                                 <div class="col-12">
                                                     <div id="arrival-hours-badge" class="schedule-hours-badge"></div>
                                                 </div>
                                             </div>

                                            <!-- Customer Notes & Internal Notes -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="notes" class="form-label">Customer Notes (Public) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Message or notes shown to the client on their invoice and email."></i></label>
                                                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Optional notes for customer to see...">{{ old('notes') }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="internal_notes" class="form-label">Internal Notes (Private - Staff Only) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Private internal notes for staff reference. Transferred to transaction notes upon payment. NOT visible to customer."></i></label>
                                                        <textarea name="internal_notes" id="internal_notes" class="form-control" rows="3" style="background-color: #fffbeb; border-color: #fcd34d;" placeholder="Private staff notes (not visible to customer)...">{{ old('internal_notes') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Line Items -->
                                            <div class="form-group mb-3">
                                                <label class="form-label">Invoice Items <span class="text-danger">*</span> <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Add line items with name, quantity, and unit price for the invoice."></i></label>
                                                <div id="itemsContainer">
                                                    <div class="invoice-item mb-3 p-3 border rounded" style="background-color: #f9f9f9;">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label class="form-label small text-muted">Item Name</label>
                                                                <input type="text" name="items[0][name]" class="form-control mb-2" placeholder="Item Name" required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label small text-muted">Qty (Guests)</label>
                                                                <input type="number" name="items[0][quantity]" class="form-control mb-2 quantity" placeholder="Qty" value="1" min="1" required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label small text-muted">Price ($)</label>
                                                                <input type="number" name="items[0][price]" class="form-control mb-2 price" placeholder="Price" step="0.01" min="0.01" required>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">Line Total: <span class="line-total">$0.00</span></small>
                                                        <button type="button" class="btn btn-sm btn-danger float-end remove-item">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-dark btn-sm" id="addItemBtn">
                                                    <i class="fas fa-plus text-white"></i> <span class="text-white">Add Item</span>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card-footer border-top p-3">
                                            <a href="{{ route('admin.custom-invoice.index') }}" class="btn btn-dark text-white">Cancel</a>
                                            <button type="submit" name="action" value="draft" class="btn btn-primary">Save as Draft</button>
                                            <button type="submit" name="action" value="send" class="btn btn-success">Save & Send to Client</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="card-shadow-primary card-border mb-3 card p-4">
                                    <div class="card-header border-bottom p-0 pb-3">
                                        <h5 class="card-title text-dark">Invoice Summary</h5>
                                    </div>
                                    <div class="card-body pt-3 text-dark">
                                        <div style="padding: 20px 0;">
                                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                                <span>Subtotal:</span>
                                                <span id="summarySubtotal" style="font-weight: 500;">$0.00</span>
                                            </div>
                                            <div id="salesTaxRow" style="display: none; justify-content: space-between; margin-bottom: 10px;">
                                                <span id="salesTaxLabel">Sales Tax:</span>
                                                <span id="summarySalesTax" style="font-weight: 500;">$0.00</span>
                                            </div>
                                            <div id="serviceChargeRow" style="display: none; justify-content: space-between; margin-bottom: 10px;">
                                                <span id="serviceChargeLabel">Service Charge:</span>
                                                <span id="summaryServiceCharge" style="font-weight: 500;">$0.00</span>
                                            </div>
                                            <div id="gratuityRow" style="display: none; justify-content: space-between; margin-bottom: 10px;">
                                                <span id="gratuityLabel">Gratuity Fee:</span>
                                                <span id="summaryGratuity" style="font-weight: 500;">$0.00</span>
                                            </div>
                                            <div id="processingFeeRow" style="display: none; justify-content: space-between; margin-bottom: 10px;">
                                                <span id="processingFeeLabel">Processing Fee:</span>
                                                <span id="summaryProcessingFee" style="font-weight: 500;">$0.00</span>
                                            </div>
                                            <hr>
                                            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 18px;">
                                                <span>Total:</span>
                                                <span id="summaryTotal">$0.00</span>
                                            </div>
                                            <div id="refundableRow" style="display: none; justify-content: space-between; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ddd; font-size: 14px; color: #666;">
                                                <span id="refundableLabel" style="font-style: italic;">Non-Refundable Deposit:</span>
                                                <span id="summaryRefundable" style="font-weight: 500; font-style: italic;">$0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-shadow-primary card-border mb-3 card p-4">
                                    <div class="card-header border-bottom p-0 pb-3">
                                        <h5 class="card-title text-dark"><i class="fas fa-info-circle"></i> Custom Invoice Help</h5>
                                    </div>
                                    <div class="card-body pt-3 text-dark">
                                        <p style="font-size: 13px; line-height: 1.7; margin-bottom: 15px;">
                                            <strong>About Custom Invoices:</strong><br>
                                            Create invoices with custom items that are NOT connected to your existing packages. Perfect for one-off services, consulting, or special projects.
                                        </p>
                                        <p style="font-size: 13px; line-height: 1.7;">
                                            <strong>Workflow:</strong>
                                            <ol style="margin: 10px 0 0 20px; padding-left: 10px;">
                                                <li style="margin-bottom: 8px;">Select the website this invoice belongs to</li>
                                                <li style="margin-bottom: 8px;">Enter client name and email address</li>
                                                <li style="margin-bottom: 8px;">Add custom line items with quantity and price</li>
                                                <li style="margin-bottom: 8px;">Add optional notes for your reference</li>
                                                <li style="margin-bottom: 8px;"><strong>Save as Draft</strong> to edit later, or <strong>Save & Send</strong> to email immediately</li>
                                                <li style="margin-bottom: 8px;">Client receives a professional email with a secure payment link</li>
                                                <li style="margin-bottom: 8px;">Payment is processed via your website's configured gateway (Stripe/Authorize.net)</li>
                                            </ol>
                                        </p>
                                        <p style="font-size: 12px; color: #6c757d; margin-top: 15px;">
                                            <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> All fees (gratuity, refundable, sales tax, service charge) are automatically calculated in real-time based on your website settings.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
        let itemCount = 1;

        function updateSummary() {
            let subtotal = 0;
            document.querySelectorAll('.invoice-item').forEach((item, index) => {
                const qty = parseFloat(item.querySelector('.quantity').value) || 0;
                const price = parseFloat(item.querySelector('.price').value) || 0;
                const lineTotal = qty * price;
                item.querySelector('.line-total').textContent = '$' + lineTotal.toFixed(2);
                subtotal += lineTotal;
            });
            
            // Get selected website's fees
            const websiteSelect = document.getElementById('website_id');
            const selectedOption = websiteSelect.options[websiteSelect.selectedIndex];
            
            let total = subtotal;
            
            // Update subtotal
            document.getElementById('summarySubtotal').textContent = '$' + subtotal.toFixed(2);

            if (typeof window.updateAdminInvoiceArrivalTimes === 'function') {
                window.updateAdminInvoiceArrivalTimes();
            }
            
            // Calculate and display fees if website is selected
            if (selectedOption && selectedOption.value) {
                // Sales Tax (calculated first)
                const salesTaxFee = parseFloat(selectedOption.dataset.salesTaxFee) || 0;
                const salesTaxName = selectedOption.dataset.salesTaxName || 'Sales Tax';
                let salesTax = 0;
                if (salesTaxFee > 0) {
                    salesTax = subtotal * (salesTaxFee / 100);
                    document.getElementById('salesTaxLabel').textContent = salesTaxName + ':';
                    document.getElementById('summarySalesTax').textContent = '$' + salesTax.toFixed(2);
                    document.getElementById('salesTaxRow').style.display = 'flex';
                    total += salesTax;
                } else {
                    document.getElementById('salesTaxRow').style.display = 'none';
                }
                
                // Service Charge
                const serviceChargeFee = parseFloat(selectedOption.dataset.serviceChargeFee) || 0;
                const serviceChargeName = selectedOption.dataset.serviceChargeName || 'Service Charge';
                let serviceCharge = 0;
                if (serviceChargeFee > 0) {
                    serviceCharge = subtotal * (serviceChargeFee / 100);
                    document.getElementById('serviceChargeLabel').textContent = serviceChargeName + ':';
                    document.getElementById('summaryServiceCharge').textContent = '$' + serviceCharge.toFixed(2);
                    document.getElementById('serviceChargeRow').style.display = 'flex';
                    total += serviceCharge;
                } else {
                    document.getElementById('serviceChargeRow').style.display = 'none';
                }
                
                // Gratuity (calculated on subtotal + sales_tax + service_charge)
                const gratuityFee = parseFloat(selectedOption.dataset.gratuityFee) || 0;
                const gratuityName = selectedOption.dataset.gratuityName || 'Gratuity Fee';
                if (gratuityFee > 0) {
                    const baseForGratuity = subtotal + salesTax + serviceCharge;
                    const gratuity = baseForGratuity * (gratuityFee / 100);
                    document.getElementById('gratuityLabel').textContent = gratuityName + ':';
                    document.getElementById('summaryGratuity').textContent = '$' + gratuity.toFixed(2);
                    document.getElementById('gratuityRow').style.display = 'flex';
                    total += gratuity;
                } else {
                    document.getElementById('gratuityRow').style.display = 'none';
                }

                // Processing Fee
                const processingFeeVal = parseFloat(selectedOption.dataset.processingFee) || 0;
                const processingFeeType = (selectedOption.dataset.processingFeeType || 'percentage').toLowerCase();
                let processingFee = 0;
                if (processingFeeVal > 0) {
                    if (processingFeeType === 'flat') {
                        processingFee = processingFeeVal;
                    } else {
                        processingFee = subtotal * (processingFeeVal / 100);
                    }
                    document.getElementById('summaryProcessingFee').textContent = '$' + processingFee.toFixed(2);
                    document.getElementById('processingFeeRow').style.display = 'flex';
                    total += processingFee;
                } else {
                    document.getElementById('processingFeeRow').style.display = 'none';
                }
                
                // Refundable (shown separately, NOT added to total)
                const refundableFee = parseFloat(selectedOption.dataset.refundableFee) || 0;
                const refundableName = selectedOption.dataset.refundableName || 'Non-Refundable Deposit';
                if (refundableFee > 0) {
                    const refundable = subtotal * (refundableFee / 100);
                    document.getElementById('refundableLabel').textContent = refundableName + ' (' + refundableFee + '%):';
                    document.getElementById('summaryRefundable').textContent = '$' + refundable.toFixed(2);
                    document.getElementById('refundableRow').style.display = 'flex';
                } else {
                    document.getElementById('refundableRow').style.display = 'none';
                }
            } else {
                // Hide all fee rows if no website selected
                document.getElementById('gratuityRow').style.display = 'none';
                document.getElementById('refundableRow').style.display = 'none';
                document.getElementById('salesTaxRow').style.display = 'none';
                document.getElementById('serviceChargeRow').style.display = 'none';
            }
            
            // Update total
            document.getElementById('summaryTotal').textContent = '$' + total.toFixed(2);
        }
        
        // Listen for website selection changes
        document.getElementById('website_id').addEventListener('change', updateSummary);

        document.getElementById('addItemBtn').addEventListener('click', function() {
            const container = document.getElementById('itemsContainer');
            const newItem = document.createElement('div');
            newItem.className = 'invoice-item mb-3 p-3 border rounded';
            newItem.style.backgroundColor = '#ffffff';
            newItem.style.border = '1px solid #cbd5e1';
            newItem.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Item Name</label>
                        <input type="text" name="items[${itemCount}][name]" class="form-control mb-2" placeholder="Item Name" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Qty (Guests)</label>
                        <input type="number" name="items[${itemCount}][quantity]" class="form-control mb-2 quantity" placeholder="Qty" value="1" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Price ($)</label>
                        <input type="number" name="items[${itemCount}][price]" class="form-control mb-2 price" placeholder="Price" step="0.01" min="0.01" required>
                    </div>
                </div>
                <small class="text-muted">Line Total: <span class="line-total">$0.00</span></small>
                <button type="button" class="btn btn-sm btn-danger float-end remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            `;

            const inputs = newItem.querySelectorAll('.quantity, .price');
            inputs.forEach(input => {
                input.addEventListener('change', updateSummary);
                input.addEventListener('input', updateSummary);
            });

            newItem.querySelector('.remove-item').addEventListener('click', function() {
                newItem.remove();
                updateSummary();
            });

            container.appendChild(newItem);
            itemCount++;
            updateSummary();
        });

        // Event listeners for existing items
        document.querySelectorAll('.quantity, .price').forEach(input => {
            input.addEventListener('change', updateSummary);
            input.addEventListener('input', updateSummary);
        });

        // Remove item functionality
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.invoice-item').remove();
                updateSummary();
            });
        });

        // Initial summary
        updateSummary();
    </script>
    <script>
        (function() {
        let adminFpPickerInstance = null;

        function getPacificTodayDateString(tz) {
            try {
                const formatter = new Intl.DateTimeFormat('en-CA', {
                    timeZone: tz || 'America/Los_Angeles',
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

        function parseTimeToMinutes(timeValue) {
                if (!timeValue) return null;
                const trimmed = String(timeValue).trim().replace(/[\u00A0\u202F]/g, ' ');
                const twelveHourMatch = trimmed.match(/^(\d{1,2}):(\d{2})(?::\d{2})?\s*([AaPp])\.?\s*[Mm]\.?$/);
                if (twelveHourMatch) {
                    let hours = parseInt(twelveHourMatch[1], 10) % 12;
                    const minutes = parseInt(twelveHourMatch[2], 10);
                    if (twelveHourMatch[3].toUpperCase() === 'P') {
                        hours += 12;
                    }
                    return (hours * 60) + minutes;
                }
                const twentyFourHourMatch = trimmed.match(/^(\d{1,2}):(\d{2})(?::\d{2})?$/);
                if (twentyFourHourMatch) {
                    return (parseInt(twentyFourHourMatch[1], 10) * 60) + parseInt(twentyFourHourMatch[2], 10);
                }
                return null;
            }

            function formatMinutesToTwelveHour(totalMinutes) {
                const normalized = ((totalMinutes % 1440) + 1440) % 1440;
                const hours24 = Math.floor(normalized / 60);
                const minutes = normalized % 60;
                const meridiem = hours24 >= 12 ? 'PM' : 'AM';
                const hours12 = (hours24 % 12) || 12;
                const minStr = String(minutes).padStart(2, '0');
                if (hours12 === 12 && minutes === 0 && meridiem === 'AM') {
                    return '12:00 AM (Midnight)';
                }
                return hours12 + ':' + minStr + ' ' + meridiem;
            }

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

            function generateTimeSlotOptions(startTimeStr, endTimeStr) {
                let startMin = parseTimeToMinutes(startTimeStr);
                let endMin = parseTimeToMinutes(endTimeStr);

                if (startMin === null) startMin = 1260; // 9:00 PM
                if (endMin === null) endMin = 180;    // 3:00 AM

                if (endMin <= startMin) {
                    endMin += 1440; // Overnight
                }

                const options = [];
                for (let cur = startMin; cur <= endMin; cur += 30) {
                    const timeFormatted = formatMinutesToTwelveHour(cur);
                    const timeVal = timeFormatted.replace(' (Midnight)', '');
                    options.push({ value: timeVal, label: timeFormatted });
                }
                return options;
            }

            window.updateAdminInvoiceArrivalTimes = function() {
                const websiteSelect = document.getElementById('website_id');
                const selectedOpt = websiteSelect ? (websiteSelect.selectedOptions[0] || websiteSelect.options[websiteSelect.selectedIndex]) : null;
                const arrivalSelect = document.getElementById('transportation_arrival_time');
                const badgeRow = document.getElementById('arrival-hours-badge-row');
                
                if (!selectedOpt || !selectedOpt.value) {
                    if (arrivalSelect) {
                        arrivalSelect.innerHTML = '<option value="">-- Client Will Select at Checkout --</option>';
                    }
                    if (badgeRow) {
                        badgeRow.style.display = 'none';
                    }
                    return;
                }

                let dailyHoursMap = {};
                let websiteStartDefault = selectedOpt.getAttribute('data-operating-start') || '9:00 PM';
                let websiteEndDefault = selectedOpt.getAttribute('data-operating-end') || '3:00 AM';
                let clubTz = selectedOpt.getAttribute('data-timezone') || 'America/Los_Angeles';

                try {
                    dailyHoursMap = JSON.parse(selectedOpt.getAttribute('data-daily-hours') || '{}');
                } catch(e) { dailyHoursMap = {}; }

                const dateInput = document.getElementById('package_use_date');
                const pacificToday = getPacificTodayDateString(clubTz);
                if (dateInput) {
                    dateInput.min = pacificToday;
                    if (typeof flatpickr === 'function') {
                        if (adminFpPickerInstance) {
                            adminFpPickerInstance.set('minDate', pacificToday);
                        } else {
                            adminFpPickerInstance = flatpickr(dateInput, {
                                dateFormat: "Y-m-d",
                                minDate: pacificToday,
                                allowInput: false,
                                clickOpens: true,
                                onChange: function(selectedDates, dateStr) {
                                    dateInput.value = dateStr;
                                    window.updateAdminInvoiceArrivalTimes();
                                }
                            });
                        }
                    }
                }

                const dateStr = dateInput ? dateInput.value : '';
                const dayName = getDayOfWeekFromDateString(dateStr);

                let startStr = websiteStartDefault;
                let endStr = websiteEndDefault;

                if (dayName && dailyHoursMap && dailyHoursMap[dayName]) {
                    const dayCfg = dailyHoursMap[dayName];
                    if (dayCfg.operating_start_time) startStr = dayCfg.operating_start_time;
                    if (dayCfg.operating_end_time) endStr = dayCfg.operating_end_time;
                }

                const timeOptions = generateTimeSlotOptions(startStr, endStr);

                if (arrivalSelect) {
                    const currentVal = arrivalSelect.value || @json(old('transportation_arrival_time', ''));
                    arrivalSelect.innerHTML = '<option value="">-- Client Will Select at Checkout --</option>';

                    let foundMatch = false;
                    timeOptions.forEach(opt => {
                        const isSel = (currentVal && (currentVal === opt.value || currentVal === opt.label || currentVal.replace(' (Midnight)', '') === opt.value));
                        if (isSel) foundMatch = true;
                        const newOpt = document.createElement('option');
                        newOpt.value = opt.value;
                        newOpt.textContent = opt.label;
                        if (isSel) newOpt.selected = true;
                        arrivalSelect.appendChild(newOpt);
                    });

                    if (!foundMatch && currentVal) {
                        const customOpt = document.createElement('option');
                        customOpt.value = currentVal;
                        customOpt.textContent = currentVal;
                        customOpt.selected = true;
                        arrivalSelect.appendChild(customOpt);
                    }
                }

                // Render Day-Wise Operating Hours Badge
                const badgeEl = document.getElementById('arrival-hours-badge');
                if (badgeRow && badgeEl) {
                    const daysOrder = [
                        { key: 'monday', label: 'Mon' },
                        { key: 'tuesday', label: 'Tue' },
                        { key: 'wednesday', label: 'Wed' },
                        { key: 'thursday', label: 'Thu' },
                        { key: 'friday', label: 'Fri' },
                        { key: 'saturday', label: 'Sat' },
                        { key: 'sunday', label: 'Sun' }
                    ];

                    const daySchedules = [];
                    daysOrder.forEach((dayObj, idx) => {
                        const cfg = dailyHoursMap ? dailyHoursMap[dayObj.key] : null;
                        const sTime = (cfg && cfg.operating_start_time) ? cfg.operating_start_time : websiteStartDefault;
                        const eTime = (cfg && cfg.operating_end_time) ? cfg.operating_end_time : websiteEndDefault;

                        if (sTime && eTime) {
                            const startDisp = formatMinutesToTwelveHour(parseTimeToMinutes(sTime) || 0).replace(' (Midnight)', '');
                            const endDisp = formatMinutesToTwelveHour(parseTimeToMinutes(eTime) || 0).replace(' (Midnight)', '');
                            daySchedules.push({
                                index: idx,
                                label: dayObj.label,
                                timeStr: startDisp + ' to ' + endDisp
                            });
                        }
                    });

                    if (daySchedules.length === 0) {
                        badgeRow.style.display = 'none';
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
                        const daysGroup = groupsByTime[timeStr];
                        let dayRangeStr = '';
                        if (daysGroup.length === 1) {
                            dayRangeStr = daysGroup[0].label;
                        } else {
                            dayRangeStr = daysGroup[0].label + '-' + daysGroup[daysGroup.length - 1].label;
                        }
                        htmlLines += `
                            <div class="hours-line">
                                <span class="hours-days">${dayRangeStr}</span>
                                <span class="hours-time">${timeStr}</span>
                            </div>
                        `;
                    });

                    badgeEl.innerHTML = `
                        <div class="hours-title"><i class="fas fa-clock me-1"></i> Venue Operating Hours</div>
                        <div class="hours-list">${htmlLines}</div>
                    `;
                    badgeRow.style.display = 'block';
                }
            };

            function initAdminInvoiceArrivalEvents() {
                window.updateAdminInvoiceArrivalTimes();
                
                const websiteSelect = document.getElementById('website_id');
                if (websiteSelect) {
                    websiteSelect.addEventListener('change', window.updateAdminInvoiceArrivalTimes);
                }

                const dateInput = document.getElementById('package_use_date');
                if (dateInput) {
                    dateInput.addEventListener('change', window.updateAdminInvoiceArrivalTimes);
                    dateInput.addEventListener('input', window.updateAdminInvoiceArrivalTimes);
                }

                if (window.jQuery) {
                    window.jQuery(document).on('change select2:select', '#website_id', function() {
                        window.updateAdminInvoiceArrivalTimes();
                    });
                    window.jQuery(document).on('change input', '#package_use_date', function() {
                        window.updateAdminInvoiceArrivalTimes();
                    });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAdminInvoiceArrivalEvents);
            } else {
                initAdminInvoiceArrivalEvents();
            }
        })();
    </script>
@endsection

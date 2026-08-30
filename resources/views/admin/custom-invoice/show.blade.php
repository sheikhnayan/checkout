@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
/* Executive Light Professional Workspace - Zero Dark Backgrounds & Crisp Solid Black Fonts */
html body .custom-invoice-page-wrapper,
html body .content-wrapper,
html body .app-main__inner,
html body .layout-page,
html body .custom-invoice-page-wrapper .row,
html body .custom-invoice-page-wrapper div {
    background-color: #ffffff !important;
    background: #ffffff !important;
    color: #000000 !important;
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
html body .custom-invoice-page-wrapper i,
html body .custom-invoice-page-wrapper .card-body *,
html body .custom-invoice-page-wrapper code,
html body .custom-invoice-page-wrapper pre {
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

/* ABSOLUTE FORCE PURE BLACK (#000000) FOR DATATABLES, TABLES, CELLS, INPUTS, AND SUMMARY BREAKDOWN */
html body .custom-invoice-page-wrapper table,
html body .custom-invoice-page-wrapper table.dataTable,
html body .custom-invoice-page-wrapper table.dataTable tbody,
html body .custom-invoice-page-wrapper table.dataTable tbody tr,
html body .custom-invoice-page-wrapper table.dataTable tbody td,
html body .custom-invoice-page-wrapper table.dataTable thead,
html body .custom-invoice-page-wrapper table.dataTable thead tr,
html body .custom-invoice-page-wrapper table.dataTable thead th,
html body .custom-invoice-page-wrapper .table,
html body .custom-invoice-page-wrapper .table > :not(caption) > *,
html body .custom-invoice-page-wrapper .table > :not(caption) > * > *,
html body .content-wrapper .custom-invoice-page-wrapper table.dataTable tbody td,
html body .content-wrapper .custom-invoice-page-wrapper table.dataTable thead th {
    background-color: #ffffff !important;
    background: #ffffff !important;
    color: #000000 !important;
    font-weight: 700 !important;
    opacity: 1 !important;
    border-color: #e2e8f0 !important;
}

html body .custom-invoice-page-wrapper table.dataTable thead th,
html body .custom-invoice-page-wrapper .table thead th {
    background-color: #f1f5f9 !important;
    background: #f1f5f9 !important;
    color: #000000 !important;
    font-weight: 800 !important;
    font-size: 12px !important;
    text-transform: uppercase !important;
    border-bottom: 2px solid #cbd5e1 !important;
}

/* DataTables Filter Search Box & Controls */
html body .custom-invoice-page-wrapper .dataTables_wrapper,
html body .content-wrapper .custom-invoice-page-wrapper .dataTables_wrapper,
html body .custom-invoice-page-wrapper div.dataTables_wrapper .dataTables_filter input,
html body .content-wrapper .custom-invoice-page-wrapper div.dataTables_wrapper .dataTables_filter input {
    background-color: #ffffff !important;
    background: #ffffff !important;
    color: #000000 !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    padding: 6px 12px !important;
    font-weight: 600 !important;
}

html body .custom-invoice-page-wrapper .dataTables_info,
html body .custom-invoice-page-wrapper .dataTables_paginate,
html body .custom-invoice-page-wrapper .dataTables_paginate .paginate_button,
html body .content-wrapper .custom-invoice-page-wrapper .dataTables_info,
html body .content-wrapper .custom-invoice-page-wrapper .dataTables_paginate .paginate_button {
    color: #000000 !important;
    font-weight: 700 !important;
    opacity: 1 !important;
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
                                        <span class="text-capitalize fs-4 fw-bold">Invoice #{{ $customInvoice->id }}</span>
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
                                        <li class="active breadcrumb-item">View</li>
                                    </ol>
                                    <div style="float: right">
                                        <a href="{{ route('admin.custom-invoice.index') }}" class="btn btn-dark">
                                            <i class="fas fa-arrow-left text-white"></i> <span class="text-white">Back</span>
                                        </a>
                                    </div>
                                </nav>
                            </div>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card-shadow-primary card-border mb-3 card p-4">
                                    <div class="card-header border-bottom p-0 pb-3">
                                        <h5 class="card-title">Invoice Details</h5>
                                    </div>

                                    <div class="card-body pt-3">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <p><strong>Client Name:</strong> {{ $customInvoice->client_name }}</p>
                                                <p><strong>Client Email:</strong> {{ $customInvoice->client_email }}</p>
                                                <p><strong>Website:</strong> {{ $customInvoice->website->name }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Invoice Date:</strong> {{ $customInvoice->created_at->timezone('America/Los_Angeles')->format('M d, Y') }}</p>
                                                <p><strong>Status:</strong> 
                                                    @if($customInvoice->archived_at)
                                                        <span class="badge bg-dark">Archived</span>
                                                    @elseif($customInvoice->status === 'draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                    @elseif($customInvoice->status === 'sent')
                                                        <span class="badge bg-primary">Sent</span>
                                                    @elseif($customInvoice->status === 'paid')
                                                        <span class="badge bg-success">Paid</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ ucfirst($customInvoice->status) }}</span>
                                                    @endif
                                                </p>
                                                @if($customInvoice->paid_at)
                                                    <p><strong>Paid Date:</strong> {{ $customInvoice->paid_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') }} PT</p>
                                                @endif
                                            </div>
                                        </div>

                                        @if($customInvoice->notes)
                                        <div class="alert mb-3" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; border-radius: 8px;">
                                            <strong style="color: #1d4ed8;"><i class="fas fa-comment-alt me-1"></i> Customer Notes (Public - Shown on Invoice & Email):</strong><br>
                                            {{ $customInvoice->notes }}
                                        </div>
                                        @endif

                                        @if($customInvoice->internal_notes)
                                        <div class="alert mb-3" style="background: #fffbeb; border: 1px solid #fde68a; color: #92400e; border-radius: 8px;">
                                            <strong style="color: #78350f;"><i class="fas fa-lock me-1"></i> Internal Notes (Private - Staff Only):</strong><br>
                                            {{ $customInvoice->internal_notes }}
                                        </div>
                                        @endif

                                        <h5 class="mt-4 mb-3">Line Items</h5>
                                        <table class="table table-bordered" style="border-color: rgba(255,255,255,0.1);">
                                            <thead style="background: rgba(255,255,255,0.08);">
                                                <tr>
                                                    <th style="color: #ffffff; font-weight: 700; border-color: rgba(255,255,255,0.1);">Item</th>
                                                    <th style="text-align: center; color: #ffffff; font-weight: 700; border-color: rgba(255,255,255,0.1);">Qty</th>
                                                    <th style="text-align: right; color: #ffffff; font-weight: 700; border-color: rgba(255,255,255,0.1);">Price</th>
                                                    <th style="text-align: right; color: #ffffff; font-weight: 700; border-color: rgba(255,255,255,0.1);">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($customInvoice->items as $item)
                                                <tr style="border-color: rgba(255,255,255,0.08);">
                                                    <td>{{ $item->name }}</td>
                                                    <td style="text-align: center;">{{ $item->quantity }}</td>
                                                    <td style="text-align: right;">${{ number_format($item->price, 2) }}</td>
                                                    <td style="text-align: right;">${{ number_format($item->getLineTotal(), 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid rgba(255,255,255,0.15);">
                                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                                <span>Subtotal:</span>
                                                <span>${{ number_format($customInvoice->subtotal, 2) }}</span>
                                            </div>
                                            @if($customInvoice->sales_tax > 0)
                                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                                <span>{{ $customInvoice->sales_tax_name ?? 'Sales Tax' }}:</span>
                                                <span>${{ number_format($customInvoice->sales_tax, 2) }}</span>
                                            </div>
                                            @endif
                                            @if($customInvoice->service_charge > 0)
                                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                                <span>{{ $customInvoice->service_charge_name ?? 'Service Charge' }}:</span>
                                                <span>${{ number_format($customInvoice->service_charge, 2) }}</span>
                                            </div>
                                            @endif
                                            @if($customInvoice->gratuity > 0)
                                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                                <span>{{ $customInvoice->gratuity_name ?? 'Gratuity Fee' }}:</span>
                                                <span>${{ number_format($customInvoice->gratuity, 2) }}</span>
                                            </div>
                                            @endif
                                            @if($customInvoice->processing_fee > 0)
                                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                                <span>{{ $customInvoice->processing_fee_name ?? 'Processing Fee' }}:</span>
                                                <span>${{ number_format($customInvoice->processing_fee, 2) }}</span>
                                            </div>
                                            @endif
                                            <div style="display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.12); font-weight: bold; font-size: 18px; color: #ffffff;">
                                                <span>TOTAL:</span>
                                                <span>${{ number_format($customInvoice->total, 2) }}</span>
                                            </div>
                                            @if($customInvoice->refundable > 0)
                                            <div style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 10px; border-top: 1px dashed rgba(255,255,255,0.15); color: rgba(255,255,255,0.65); font-size: 14px;">
                                                <span>{{ $customInvoice->refundable_name ?? 'Non-Refundable Deposit' }} ({{ number_format($customInvoice->website->refundable_fee ?? 0) }}%):</span>
                                                <span>${{ number_format($customInvoice->refundable, 2) }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="card-footer border-top p-3">
                                        @if(!$customInvoice->archived_at && $customInvoice->status === 'draft')
                                            <a href="{{ route('admin.custom-invoice.edit', $customInvoice->id) }}" class="btn btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.custom-invoice.send', $customInvoice->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success" onclick="return confirm('Send invoice to {{ $customInvoice->client_email }}?');">
                                                    <i class="fas fa-paper-plane"></i> Send to Client
                                                </button>
                                            </form>
                                        @endif
                                        @if($customInvoice->archived_at)
                                            <form action="{{ route('admin.custom-invoice.unarchive', $customInvoice->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-warning" onclick="return confirm('Restore this archived invoice?');">
                                                    <i class="fas fa-box-open"></i> Restore
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.custom-invoice.archive', $customInvoice->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-dark" onclick="return confirm('Archive this invoice?');">
                                                    <i class="fas fa-box-archive"></i> Archive
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.custom-invoice.destroy', $customInvoice->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this invoice?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="card-shadow-primary card-border mb-3 card p-4">
                                    <div class="card-header border-bottom p-0 pb-3">
                                        <h5 class="card-title">Invoice Status</h5>
                                    </div>
                                    <div class="card-body pt-3">
                                        <div class="mb-3">
                                            <strong>Current Status:</strong><br>
                                            @if($customInvoice->archived_at)
                                                <span class="badge bg-dark" style="font-size: 14px;">Archived</span>
                                            @elseif($customInvoice->status === 'draft')
                                                <span class="badge bg-secondary" style="font-size: 14px;">Draft (Not Sent)</span>
                                            @elseif($customInvoice->status === 'sent')
                                                <span class="badge bg-primary" style="font-size: 14px;">Sent to Client</span>
                                            @elseif($customInvoice->status === 'paid')
                                                <span class="badge bg-success" style="font-size: 14px;">Paid</span>
                                            @else
                                                <span class="badge bg-danger" style="font-size: 14px;">{{ ucfirst($customInvoice->status) }}</span>
                                            @endif
                                        </div>

                                        @if($customInvoice->payment_transaction_id)
                                        <div class="mb-3">
                                            <strong>Payment ID:</strong><br>
                                            <code>{{ $customInvoice->payment_transaction_id }}</code>
                                        </div>
                                        @endif

                                        @if($customInvoice->sent_at)
                                        <div class="mb-3">
                                            <strong>Email Sent At:</strong><br>
                                            {{ $customInvoice->sent_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') }} PT
                                        </div>
                                        @endif

                                        @if($customInvoice->archived_at)
                                        <div class="mb-3">
                                            <strong>Archived At:</strong><br>
                                            {{ $customInvoice->archived_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') }} PT
                                        </div>
                                        @endif

                                        @if($customInvoice->paid_at)
                                        <div class="mb-3">
                                            <strong>Paid At:</strong><br>
                                            {{ $customInvoice->paid_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') }} PT
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-shadow-primary card-border mb-3 card p-4">
                                    <div class="card-header border-bottom p-0 pb-3">
                                        <h5 class="card-title">Payment Link</h5>
                                    </div>
                                    <div class="card-body pt-3">
                                        <p style="font-size: 12px; margin-bottom: 10px;">
                                            Share this link with client for payment:
                                        </p>
                                        <input type="text" class="form-control form-control-sm" value="{{ $customInvoice->getPaymentUrl() }}" readonly id="paymentLink" style="background: #ffffff !important; color: #2563eb !important; border: 1px solid #cbd5e1 !important; font-weight: 600;">
                                        <button class="btn btn-sm btn-dark mt-2" onclick="copyPaymentLink()">
                                            <i class="fas fa-copy text-white"></i> <span class="text-white">Copy Link</span>
                                        </button>
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
        function copyPaymentLink() {
            const link = document.getElementById('paymentLink');
            link.select();
            document.execCommand('copy');
            alert('Payment link copied to clipboard!');
        }
    </script>
@endsection

@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">

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
html body .custom-invoice-page-wrapper .card-border,
html body .custom-invoice-page-wrapper .dataTables_wrapper {
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

/* DataTables Light Styling */
html body .custom-invoice-page-wrapper .dataTables_wrapper {
    padding: 20px !important;
    background: #ffffff !important;
}

html body .custom-invoice-page-wrapper .table,
html body .custom-invoice-page-wrapper .table > :not(caption) > *,
html body .custom-invoice-page-wrapper .table > :not(caption) > * > *,
html body .custom-invoice-page-wrapper table.dataTable,
html body .custom-invoice-page-wrapper table.dataTable tbody tr,
html body .custom-invoice-page-wrapper table.dataTable tbody td {
    background-color: #ffffff !important;
    background: #ffffff !important;
    color: #000000 !important;
    border-color: #f1f5f9 !important;
    padding: 14px 16px !important;
    vertical-align: middle !important;
}

html body .custom-invoice-page-wrapper .table thead th,
html body .custom-invoice-page-wrapper table.dataTable thead th {
    background-color: #f1f5f9 !important;
    background: #f1f5f9 !important;
    color: #000000 !important;
    font-weight: 800 !important;
    font-size: 12px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.04em !important;
    border-bottom: 2px solid #cbd5e1 !important;
    padding: 14px 16px !important;
}

html body .custom-invoice-page-wrapper .dataTables_length,
html body .custom-invoice-page-wrapper .dataTables_filter,
html body .custom-invoice-page-wrapper .dataTables_info,
html body .custom-invoice-page-wrapper .dataTables_paginate {
    color: #000000 !important;
    font-weight: 600 !important;
    font-size: 13px !important;
    padding: 10px 0 !important;
}

html body .custom-invoice-page-wrapper .dataTables_length select,
html body .custom-invoice-page-wrapper .dataTables_filter input {
    background-color: #ffffff !important;
    background: #ffffff !important;
    color: #000000 !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    padding: 6px 12px !important;
}

html body .custom-invoice-page-wrapper .dataTables_paginate .paginate_button {
    background-color: #ffffff !important;
    background: #ffffff !important;
    color: #000000 !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    margin: 0 3px !important;
    font-weight: 700 !important;
}

html body .custom-invoice-page-wrapper .dataTables_paginate .paginate_button.current,
html body .custom-invoice-page-wrapper .dataTables_paginate .paginate_button:hover {
    background-color: #0f172a !important;
    background: #0f172a !important;
    color: #ffffff !important;
    border-color: #0f172a !important;
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
                                        <span class="text-capitalize fs-4 fw-bold">Custom Invoices</span>
                                    </div>
                                </div>
                            </div>

                            <div class="page-title-subheading opacity-10 mt-3" style="white-space: nowrap; overflow-x: auto;">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb" style="float: left">
                                        <li class="breadcrumb-item opacity-10">
                                            <a href="#">
                                                <i class="fas fa-home"></i>
                                                <span class="visually-hidden">Home</span>
                                            </a>
                                            <i class="fas fa-chevron-right ms-1"></i>
                                        </li>
                                        <li class="active breadcrumb-item">Custom Invoices</li>
                                    </ol>
                                    <div style="float: right">
                                        <a href="{{ route('admin.custom-invoice.index', ['include_archived' => $includeArchived ? 0 : 1]) }}" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-box-archive"></i> {{ $includeArchived ? 'Hide Archived' : 'Show Archived' }}
                                        </a>
                                        <a href="{{ route('admin.custom-invoice.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Create Invoice
                                        </a>
                                    </div>
                                </nav>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg">
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if (session('info'))
                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                        {{ session('info') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <div class="card-shadow-primary card-border mb-3 card p-2">
                                    <table class="table" id="invoicesTable">
                                        <thead>
                                            <tr>
                                                <th>Invoice #</th>
                                                <th>Client Name</th>
                                                <th>Email</th>
                                                @if(auth()->user()->isAdmin())
                                                <th>Website</th>
                                                @endif
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($invoices as $invoice)
                                            <tr>
                                                <td>#{{ $invoice->id }}</td>
                                                <td>{{ $invoice->client_name }}</td>
                                                <td>{{ $invoice->client_email }}</td>
                                                @if(auth()->user()->isAdmin())
                                                <td>{{ $invoice->website->name ?? 'N/A' }}</td>
                                                @endif
                                                <td>${{ number_format($invoice->total, 2) }}</td>
                                                <td>
                                                    @if($invoice->archived_at)
                                                        <span class="badge bg-dark">Archived</span>
                                                    @elseif($invoice->status === 'draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                    @elseif($invoice->status === 'sent')
                                                        <span class="badge bg-primary">Sent</span>
                                                        @if($invoice->sent_at)
                                                            <div style="font-size: 11px; color: rgba(255,255,255,0.6); margin-top: 4px;">{{ $invoice->sent_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') }} PT</div>
                                                        @endif
                                                    @elseif($invoice->status === 'paid')
                                                        <span class="badge bg-success">Paid</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ ucfirst($invoice->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $invoice->created_at->timezone('America/Los_Angeles')->format('M d, Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.custom-invoice.show', $invoice->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if(!$invoice->archived_at && $invoice->status === 'draft')
                                                        <a href="{{ route('admin.custom-invoice.edit', $invoice->id) }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('admin.custom-invoice.send', $invoice->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Send this invoice to {{ $invoice->client_email }}?');">
                                                                <i class="fas fa-paper-plane"></i> Send
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($invoice->archived_at)
                                                        <form action="{{ route('admin.custom-invoice.unarchive', $invoice->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Restore this archived invoice?');">
                                                                <i class="fas fa-box-open"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('admin.custom-invoice.archive', $invoice->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-dark" onclick="return confirm('Archive this invoice?');">
                                                                <i class="fas fa-box-archive"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('admin.custom-invoice.destroy', $invoice->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this invoice?');">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="{{ auth()->user()->isAdmin() ? '8' : '7' }}" class="text-center text-muted">
                                                    No invoices found. <a href="{{ route('admin.custom-invoice.create') }}">Create one now</a>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#invoicesTable').DataTable({
                "pageLength": 10,
                "order": [[6, 'desc']]
            });
        });
    </script>
@endsection

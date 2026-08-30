@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">

<style>
/* Executive Light Professional Workspace Theme Overlay */
html body .custom-invoice-page-wrapper,
html body .custom-invoice-page-wrapper .content-wrapper,
html body .custom-invoice-page-wrapper .app-main__inner {
    background-color: #f8fafc !important;
    background: #f8fafc !important;
    color: #0f172a !important;
}

html body .custom-invoice-page-wrapper .card,
html body .custom-invoice-page-wrapper .card-shadow-primary,
html body .custom-invoice-page-wrapper .card-header,
html body .custom-invoice-page-wrapper .card-body,
html body .custom-invoice-page-wrapper .card-footer {
    background-color: #ffffff !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    color: #0f172a !important;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04) !important;
}

html body .custom-invoice-page-wrapper h1,
html body .custom-invoice-page-wrapper h2,
html body .custom-invoice-page-wrapper h3,
html body .custom-invoice-page-wrapper h4,
html body .custom-invoice-page-wrapper h5,
html body .custom-invoice-page-wrapper h6,
html body .custom-invoice-page-wrapper label,
html body .custom-invoice-page-wrapper .form-label,
html body .custom-invoice-page-wrapper .card-title,
html body .custom-invoice-page-wrapper .page-title-heading span,
html body .custom-invoice-page-wrapper div {
    color: #0f172a;
}

html body .custom-invoice-page-wrapper .breadcrumb-item,
html body .custom-invoice-page-wrapper .breadcrumb-item a,
html body .custom-invoice-page-wrapper .breadcrumb-item.active {
    color: #475569 !important;
}

/* Light Table & Cell Backgrounds */
html body .custom-invoice-page-wrapper .table,
html body .custom-invoice-page-wrapper .table > :not(caption) > *,
html body .custom-invoice-page-wrapper .table > :not(caption) > * > *,
html body .custom-invoice-page-wrapper table.dataTable,
html body .custom-invoice-page-wrapper table.dataTable tbody tr,
html body .custom-invoice-page-wrapper table.dataTable tbody td {
    background-color: #ffffff !important;
    background: #ffffff !important;
    color: #1e293b !important;
    border-color: #f1f5f9 !important;
}

html body .custom-invoice-page-wrapper .table thead th,
html body .custom-invoice-page-wrapper table.dataTable thead th {
    background-color: #f1f5f9 !important;
    background: #f1f5f9 !important;
    color: #475569 !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.04em !important;
    border-bottom: 2px solid #cbd5e1 !important;
}

/* DataTables Light Controls */
html body .custom-invoice-page-wrapper .dataTables_wrapper,
html body .custom-invoice-page-wrapper .dataTables_length,
html body .custom-invoice-page-wrapper .dataTables_filter,
html body .custom-invoice-page-wrapper .dataTables_info,
html body .custom-invoice-page-wrapper .dataTables_paginate {
    color: #0f172a !important;
}

html body .custom-invoice-page-wrapper .dataTables_length select,
html body .custom-invoice-page-wrapper .dataTables_filter input {
    background-color: #ffffff !important;
    background: #ffffff !important;
    color: #0f172a !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    padding: 6px 10px !important;
}

html body .custom-invoice-page-wrapper .dataTables_paginate .paginate_button {
    background-color: #ffffff !important;
    background: #ffffff !important;
    color: #0f172a !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    margin: 0 2px !important;
}

html body .custom-invoice-page-wrapper .dataTables_paginate .paginate_button.current,
html body .custom-invoice-page-wrapper .dataTables_paginate .paginate_button:hover {
    background-color: #0f172a !important;
    background: #0f172a !important;
    color: #ffffff !important;
    border-color: #0f172a !important;
}

/* Action Buttons & Badges */
html body .custom-invoice-page-wrapper .badge {
    padding: 6px 12px !important;
    font-weight: 700 !important;
    border-radius: 999px !important;
    font-size: 11px !important;
    letter-spacing: 0.03em !important;
}

html body .custom-invoice-page-wrapper .btn-primary {
    background-color: #0f172a !important;
    background: #0f172a !important;
    border-color: #0f172a !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    border-radius: 8px !important;
}

html body .custom-invoice-page-wrapper .btn-outline-secondary,
html body .custom-invoice-page-wrapper .btn-secondary {
    background-color: #ffffff !important;
    background: #ffffff !important;
    color: #334155 !important;
    border: 1px solid #cbd5e1 !important;
    font-weight: 600 !important;
    border-radius: 8px !important;
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

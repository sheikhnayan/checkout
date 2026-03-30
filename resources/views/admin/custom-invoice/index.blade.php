@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">

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
                                        <i class="fas fa-file-invoice icon-gradient bg-arielle-smile"></i>
                                    </div>
                                    <div>
                                        <span class="text-capitalize">Custom Invoices</span>
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
                                                            <div style="font-size: 11px; color: #6c757d; margin-top: 4px;">{{ $invoice->sent_at->format('M d, Y h:i A') }}</div>
                                                        @endif
                                                    @elseif($invoice->status === 'paid')
                                                        <span class="badge bg-success">Paid</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ ucfirst($invoice->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $invoice->created_at->format('M d, Y') }}</td>
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

@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

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
                                        <span class="text-capitalize">Invoice #{{ $customInvoice->id }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="page-title-subheading opacity-10 mt-3">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb" style="float: left">
                                        <li class="breadcrumb-item opacity-10">
                                            <a href="/admins">
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
                                        <a href="{{ route('admin.custom-invoice.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Back
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
                                <div class="card-shadow-primary card-border text-white mb-3 card bg-primary p-4" style="background: #fff !important;">
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
                                                <p><strong>Invoice Date:</strong> {{ $customInvoice->created_at->format('M d, Y') }}</p>
                                                <p><strong>Status:</strong> 
                                                    @if($customInvoice->status === 'draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                    @elseif($customInvoice->status === 'sent')
                                                        <span class="badge bg-info">Sent</span>
                                                    @elseif($customInvoice->status === 'paid')
                                                        <span class="badge bg-success">Paid</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ ucfirst($customInvoice->status) }}</span>
                                                    @endif
                                                </p>
                                                @if($customInvoice->paid_at)
                                                    <p><strong>Paid Date:</strong> {{ $customInvoice->paid_at->format('M d, Y H:i') }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        @if($customInvoice->notes)
                                        <div class="alert alert-info">
                                            <strong>Notes:</strong><br>
                                            {{ $customInvoice->notes }}
                                        </div>
                                        @endif

                                        <h5 class="mt-4 mb-3">Line Items</h5>
                                        <table class="table table-bordered">
                                            <thead style="background-color: #f5f5f5;">
                                                <tr>
                                                    <th>Item</th>
                                                    <th style="text-align: center;">Qty</th>
                                                    <th style="text-align: right;">Price</th>
                                                    <th style="text-align: right;">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($customInvoice->items as $item)
                                                <tr>
                                                    <td>{{ $item->name }}</td>
                                                    <td style="text-align: center;">{{ $item->quantity }}</td>
                                                    <td style="text-align: right;">${{ number_format($item->price, 2) }}</td>
                                                    <td style="text-align: right;">${{ number_format($item->getLineTotal(), 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #2c3e50;">
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
                                            <div style="display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd; font-weight: bold; font-size: 18px;">
                                                <span>TOTAL:</span>
                                                <span>${{ number_format($customInvoice->total, 2) }}</span>
                                            </div>
                                            @if($customInvoice->refundable > 0)
                                            <div style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ddd; color: #666; font-size: 14px;">
                                                <span>{{ $customInvoice->refundable_name ?? 'Non-Refundable Deposit' }} ({{ number_format($customInvoice->website->refundable_fee ?? 0) }}%):</span>
                                                <span>${{ number_format($customInvoice->refundable, 2) }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="card-footer border-top p-3">
                                        @if($customInvoice->status === 'draft')
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
                                <div class="card-shadow-primary card-border text-white mb-3 card bg-primary p-4" style="background: #fff !important;">
                                    <div class="card-header border-bottom p-0 pb-3">
                                        <h5 class="card-title">Invoice Status</h5>
                                    </div>
                                    <div class="card-body pt-3">
                                        <div class="mb-3">
                                            <strong>Current Status:</strong><br>
                                            @if($customInvoice->status === 'draft')
                                                <span class="badge bg-secondary" style="font-size: 14px;">Draft (Not Sent)</span>
                                            @elseif($customInvoice->status === 'sent')
                                                <span class="badge bg-info" style="font-size: 14px;">Sent to Client</span>
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
                                            <strong>Sent At:</strong><br>
                                            {{ $customInvoice->sent_at->format('M d, Y H:i') }}
                                        </div>
                                        @endif

                                        @if($customInvoice->paid_at)
                                        <div class="mb-3">
                                            <strong>Paid At:</strong><br>
                                            {{ $customInvoice->paid_at->format('M d, Y H:i') }}
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-shadow-primary card-border text-white mb-3 card bg-primary p-4" style="background: #fff !important;">
                                    <div class="card-header border-bottom p-0 pb-3">
                                        <h5 class="card-title">Payment Link</h5>
                                    </div>
                                    <div class="card-body pt-3">
                                        <p style="font-size: 12px; margin-bottom: 10px;">
                                            Share this link with client for payment:
                                        </p>
                                        <input type="text" class="form-control form-control-sm" value="{{ $customInvoice->getPaymentUrl() }}" readonly id="paymentLink">
                                        <button class="btn btn-sm btn-secondary mt-2" onclick="copyPaymentLink()">
                                            <i class="fas fa-copy"></i> Copy Link
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

    <script>
        function copyPaymentLink() {
            const link = document.getElementById('paymentLink');
            link.select();
            document.execCommand('copy');
            alert('Payment link copied to clipboard!');
        }
    </script>
@endsection

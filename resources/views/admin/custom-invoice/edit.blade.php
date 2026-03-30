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
                                        <span class="text-capitalize">Edit Invoice #{{ $customInvoice->id }}</span>
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
                                        <li class="active breadcrumb-item">Edit</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card-shadow-primary card-border mb-3 card p-4">
                                    <div class="card-header border-bottom p-0 pb-3">
                                        <h5 class="card-title">Edit Invoice</h5>
                                    </div>

                                    <form action="{{ route('admin.custom-invoice.update', $customInvoice->id) }}" method="POST" id="invoiceForm">
                                        @csrf
                                        @method('PUT')

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
                                                <label for="website_id" class="form-label">Website <span class="text-danger">*</span></label>
                                                <select name="website_id" id="website_id" class="form-select @error('website_id') is-invalid @enderror" required>
                                                    <option value="">-- Select Website --</option>
                                                    @foreach($websites as $website)
                                                        <option value="{{ $website->id }}" 
                                                            {{ $customInvoice->website_id == $website->id ? 'selected' : '' }}
                                                            data-gratuity-fee="{{ $website->gratuity_fee ?? 0 }}"
                                                            data-gratuity-name="{{ $website->gratuity_name ?? 'Gratuity Fee' }}"
                                                            data-refundable-fee="{{ $website->refundable_fee ?? 0 }}"
                                                            data-refundable-name="{{ $website->refundable_name ?? 'Refundable Fee' }}"
                                                            data-sales-tax-fee="{{ $website->sales_tax_fee ?? 0 }}"
                                                            data-sales-tax-name="{{ $website->sales_tax_name ?? 'Sales Tax' }}"
                                                            data-service-charge-fee="{{ $website->service_charge_fee ?? 0 }}"
                                                            data-service-charge-name="{{ $website->service_charge_name ?? 'Service Charge' }}">
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
                                                        <label for="client_name" class="form-label">Client Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="client_name" id="client_name" class="form-control @error('client_name') is-invalid @enderror" 
                                                               value="{{ old('client_name', $customInvoice->client_name) }}" required>
                                                        @error('client_name')
                                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="client_email" class="form-label">Client Email <span class="text-danger">*</span></label>
                                                        <input type="email" name="client_email" id="client_email" class="form-control @error('client_email') is-invalid @enderror" 
                                                               value="{{ old('client_email', $customInvoice->client_email) }}" required>
                                                        @error('client_email')
                                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Notes -->
                                            <div class="form-group mb-3">
                                                <label for="notes" class="form-label">Notes (Optional)</label>
                                                <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $customInvoice->notes) }}</textarea>
                                            </div>

                                            <!-- Line Items -->
                                            <div class="form-group mb-3">
                                                <label class="form-label">Invoice Items <span class="text-danger">*</span></label>
                                                <div id="itemsContainer">
                                                    @foreach($customInvoice->items as $index => $item)
                                                    <div class="invoice-item mb-3 p-3 border rounded" style="background-color: #f9f9f9;">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <input type="text" name="items[{{ $index }}][name]" class="form-control mb-2" placeholder="Item Name" value="{{ $item->name }}" required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="number" name="items[{{ $index }}][quantity]" class="form-control mb-2 quantity" placeholder="Qty" value="{{ $item->quantity }}" min="1" required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="number" name="items[{{ $index }}][price]" class="form-control mb-2 price" placeholder="Price" step="0.01" min="0.01" value="{{ $item->price }}" required>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">Line Total: <span class="line-total">${{ number_format($item->getLineTotal(), 2) }}</span></small>
                                                        <button type="button" class="btn btn-sm btn-danger float-end remove-item">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-secondary btn-sm" id="addItemBtn">
                                                    <i class="fas fa-plus"></i> Add Item
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card-footer border-top p-3">
                                            <a href="{{ route('admin.custom-invoice.show', $customInvoice->id) }}" class="btn btn-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
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
                                                <span id="summarySubtotal" style="font-weight: 500;">${{ number_format($customInvoice->subtotal, 2) }}</span>
                                            </div>
                                            @if($customInvoice->sales_tax > 0)
                                            <div id="salesTaxRow" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                                <span id="salesTaxLabel">{{ $customInvoice->sales_tax_name ?? 'Sales Tax' }}:</span>
                                                <span id="summarySalesTax" style="font-weight: 500;">${{ number_format($customInvoice->sales_tax, 2) }}</span>
                                            </div>
                                            @else
                                            <div id="salesTaxRow" style="display: none; justify-content: space-between; margin-bottom: 10px;">
                                                <span id="salesTaxLabel">Sales Tax:</span>
                                                <span id="summarySalesTax" style="font-weight: 500;">$0.00</span>
                                            </div>
                                            @endif
                                            @if($customInvoice->service_charge > 0)
                                            <div id="serviceChargeRow" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                                <span id="serviceChargeLabel">{{ $customInvoice->service_charge_name ?? 'Service Charge' }}:</span>
                                                <span id="summaryServiceCharge" style="font-weight: 500;">${{ number_format($customInvoice->service_charge, 2) }}</span>
                                            </div>
                                            @else
                                            <div id="serviceChargeRow" style="display: none; justify-content: space-between; margin-bottom: 10px;">
                                                <span id="serviceChargeLabel">Service Charge:</span>
                                                <span id="summaryServiceCharge" style="font-weight: 500;">$0.00</span>
                                            </div>
                                            @endif
                                            @if($customInvoice->gratuity > 0)
                                            <div id="gratuityRow" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                                <span id="gratuityLabel">{{ $customInvoice->gratuity_name ?? 'Gratuity Fee' }}:</span>
                                                <span id="summaryGratuity" style="font-weight: 500;">${{ number_format($customInvoice->gratuity, 2) }}</span>
                                            </div>
                                            @else
                                            <div id="gratuityRow" style="display: none; justify-content: space-between; margin-bottom: 10px;">
                                                <span id="gratuityLabel">Gratuity Fee:</span>
                                                <span id="summaryGratuity" style="font-weight: 500;">$0.00</span>
                                            </div>
                                            @endif
                                            <hr>
                                            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 18px;">
                                                <span>Total:</span>
                                                <span id="summaryTotal">${{ number_format($customInvoice->total, 2) }}</span>
                                            </div>
                                            @if($customInvoice->refundable > 0)
                                            <div id="refundableRow" style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ddd; font-size: 14px; color: #666;">
                                                <span id="refundableLabel" style="font-style: italic;">{{ $customInvoice->refundable_name ?? 'Non-Refundable Deposit' }} ({{ number_format($customInvoice->website->refundable_fee ?? 0) }}%):</span>
                                                <span id="summaryRefundable" style="font-weight: 500; font-style: italic;">${{ number_format($customInvoice->refundable, 2) }}</span>
                                            </div>
                                            @else
                                            <div id="refundableRow" style="display: none; justify-content: space-between; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ddd; font-size: 14px; color: #666;">
                                                <span id="refundableLabel" style="font-style: italic;">Non-Refundable Deposit:</span>
                                                <span id="summaryRefundable" style="font-weight: 500; font-style: italic;">$0.00</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <strong>Status:</strong> {{ ucfirst($customInvoice->status) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let itemCount = {{ count($customInvoice->items) }};

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
            newItem.style.backgroundColor = '#f9f9f9';
            newItem.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="items[${itemCount}][name]" class="form-control mb-2" placeholder="Item Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="items[${itemCount}][quantity]" class="form-control mb-2 quantity" placeholder="Qty" value="1" min="1" required>
                    </div>
                    <div class="col-md-3">
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
@endsection

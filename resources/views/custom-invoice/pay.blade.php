<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment for Invoice #{{ $invoice->id }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('user/assets/img/favicon/favicon.svg') }}?v={{ time() }}" />
    <link rel="mask-icon" href="{{ asset('user/assets/img/favicon/safari-mask.svg') }}?v={{ time() }}" color="#ffcc00" />
    <link rel="shortcut icon" href="{{ asset('user/assets/img/favicon/favicon.ico') }}?v={{ time() }}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }
        .payment-container {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06);
            max-width: 720px;
            width: 100%;
            overflow: hidden;
        }
        .payment-header {
            background: #0f172a;
            color: #ffffff;
            padding: 28px 30px;
            text-align: center;
        }
        .payment-header .header-title {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #ffffff;
        }
        .payment-header .header-subtitle {
            margin-top: 6px;
            font-size: 13px;
            color: #94a3b8;
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        .invoice-details, .items-section, .totals-section, .payment-section {
            padding: 28px 30px;
            border-bottom: 1px solid #f1f5f9;
        }
        .section-heading {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        .detail-row label {
            font-weight: 600;
            color: #64748b;
        }
        .detail-row span {
            color: #0f172a;
            font-weight: 600;
        }
        .items-section {
            padding: 28px 30px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table thead {
            background-color: #f8fafc;
        }
        .items-table th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 700;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            color: #1e293b;
        }
        .text-right {
            text-align: right;
        }
        .totals-section {
            background: #f8fafc;
            padding: 24px 30px;
        }
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .total-row label {
            width: 180px;
            text-align: right;
            margin-right: 20px;
            color: #64748b;
            font-weight: 600;
        }
        .total-row span {
            width: 110px;
            text-align: right;
            color: #0f172a;
            font-weight: 600;
        }
        .total-row.grand-total {
            font-weight: 800;
            font-size: 18px;
            padding-top: 12px;
            border-top: 2px solid #cbd5e1;
            color: #0f172a;
        }
        .payment-section {
            padding: 28px 30px;
        }
        .payment-form {
            display: none;
        }
        .payment-form.active {
            display: block;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #334155;
            font-size: 13px;
        }
        .form-control, .form-select {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 14px;
            color: #0f172a;
            background: #ffffff;
            box-sizing: border-box;
        }
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #0f172a;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
        }
        .pay-button {
            display: block;
            width: 100%;
            background: #0f172a;
            color: #ffffff;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s ease;
            margin-top: 24px;
        }
        .pay-button:hover {
            background: #1e293b;
            color: #ffffff;
        }
        .alert {
            padding: 14px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
    </style>
</head>
<body>
    @php
        $payLogoUrl = null;
        if ($website && !empty($website->logo)) {
            $logo = ltrim((string) $website->logo, '/');
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
                $payLogoUrl = $logo;
            } else {
                $logoPath = str_starts_with($logo, 'storage/') ? $logo : ('storage/' . $logo);
                if (file_exists(public_path($logoPath))) {
                    $payLogoUrl = url($logoPath);
                }
            }
        }
    @endphp

    <div class="payment-container">
        <div class="payment-header">
            @if($payLogoUrl)
                <div style="margin-bottom: 10px;">
                    <img src="{{ $payLogoUrl }}" alt="{{ $website->name ?? 'Venue' }}" style="max-height: 48px; max-width: 220px; object-fit: contain;" onerror="this.parentElement.style.display='none';">
                </div>
            @endif
            <h1 class="header-title">{{ $website->name ?? 'CartVIP' }}</h1>
            <div class="header-subtitle">Invoice #{{ $invoice->id }} &bull; Secure Payment Portal</div>
        </div>

        @if($invoice->status === 'paid')
            <div class="invoice-details">
                <div class="alert alert-success" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
                    <i class="fas fa-check-circle"></i> <strong>Payment Already Received</strong>
                    <p style="margin-bottom: 0; margin-top: 5px;">This invoice has been paid on {{ $invoice->paid_at->format('M d, Y H:i') }}.</p>
                </div>
            </div>
        @elseif($invoice->status === 'expired')
            <div class="invoice-details">
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> <strong>Invoice Expired</strong>
                    <p style="margin-bottom: 0; margin-top: 5px;">This invoice has expired and can no longer be paid online. Please contact us.</p>
                </div>
            </div>
        @else
            <div class="invoice-details">
                @if(session('success'))
                    <div class="alert alert-success" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> {{ session('error') }}
                    </div>
                @endif

                <h3>Bill To</h3>
                <div class="detail-row">
                    <label>Name</label>
                    <span>{{ $invoice->client_name }}</span>
                </div>
                <div class="detail-row">
                    <label>Email</label>
                    <span>{{ $invoice->client_email }}</span>
                </div>
                <div class="detail-row">
                    <label>Invoice Date</label>
                    <span>{{ $invoice->created_at->format('M d, Y') }}</span>
                </div>
                @if($invoice->notes)
                <div class="detail-row">
                    <label>Notes</label>
                    <span>{{ $invoice->notes }}</span>
                </div>
                @endif
            </div>

            <div class="items-section">
                <h3>Items</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="text-align: center;">Guests</th>
                            <th style="text-align: center;">Qty</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td style="text-align: center;">{{ $item->guests ?? 1 }}</td>
                            <td style="text-align: center;">{{ $item->quantity }}</td>
                            <td class="text-right">${{ number_format($item->price, 2) }}</td>
                            <td class="text-right"><strong>${{ number_format($item->getLineTotal(), 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="totals-section">
                <div class="total-row">
                    <label>Subtotal</label>
                    <span>${{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if($invoice->sales_tax > 0)
                <div class="total-row">
                    <label>{{ $invoice->sales_tax_name ?? 'Sales Tax' }}</label>
                    <span>${{ number_format($invoice->sales_tax, 2) }}</span>
                </div>
                @endif
                @if($invoice->service_charge > 0)
                <div class="total-row">
                    <label>{{ $invoice->service_charge_name ?? 'Service Charge' }}</label>
                    <span>${{ number_format($invoice->service_charge, 2) }}</span>
                </div>
                @endif
                @if($invoice->gratuity > 0)
                <div class="total-row">
                    <label>{{ $invoice->gratuity_name ?? 'Gratuity Fee' }}</label>
                    <span>${{ number_format($invoice->gratuity, 2) }}</span>
                </div>
                @endif
                @if($invoice->processing_fee > 0)
                <div class="total-row">
                    <label>{{ $invoice->processing_fee_name ?? 'Processing Fee' }}</label>
                    <span>${{ number_format($invoice->processing_fee, 2) }}</span>
                </div>
                @endif
                <div class="total-row grand-total">
                    <label>TOTAL DUE</label>
                    <span id="totalAmount">${{ number_format($invoice->total, 2) }}</span>
                </div>
                @if($invoice->refundable > 0)
                <div class="total-row" style="border-top: 1px dashed #ddd; margin-top: 10px; padding-top: 10px; font-size: 14px; color: #666;">
                    <label style="font-style: italic;">{{ $invoice->refundable_name ?? 'Non-Refundable Deposit' }} ({{ number_format($invoice->website->refundable_fee ?? 0) }}%)</label>
                    <span style="font-style: italic;">${{ number_format($invoice->refundable, 2) }}</span>
                </div>
                @endif
            </div>

            <div class="payment-section">
                @if(!empty($website->operating_hours))
                <div style="margin-bottom: 20px; padding: 15px; background-color: #f8fafc; border: 1px solid #cbd5e1; border-left: 4px solid #667eea; border-radius: 6px;">
                    <h4 style="margin: 0 0 6px 0; font-size: 14px; color: #1e293b; font-weight: 600;">
                        <i class="fas fa-clock me-1 text-primary"></i> Club Operating Hours
                    </h4>
                    <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.5;">
                        {{ $website->operating_hours }}
                    </p>
                </div>
                @endif

                @if($invoice->refundable > 0)
                <!-- Payment Type Selection -->
                <div style="margin-bottom: 25px; padding: 20px; background-color: #f9f9f9; border-radius: 5px;">
                    <h4 style="margin: 0 0 15px 0; font-size: 16px; color: #333;">Select Payment Option:</h4>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 10px; background: white; border: 2px solid #667eea; border-radius: 5px; flex: 1; min-width: 200px;">
                            <input type="radio" name="payment_type" value="deposit" checked style="margin-right: 10px;">
                            <div>
                                <strong style="display: block; color: #667eea;">Pay Deposit Only</strong>
                                <span style="font-size: 14px; color: #666;">${{ number_format($invoice->refundable, 2) }} now</span>
                            </div>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 10px; background: white; border: 2px solid #ddd; border-radius: 5px; flex: 1; min-width: 200px;">
                            <input type="radio" name="payment_type" value="full" style="margin-right: 10px;">
                            <div>
                                <strong style="display: block; color: #333;">Pay Full Amount</strong>
                                <span style="font-size: 14px; color: #666;">${{ number_format($invoice->total, 2) }} now</span>
                            </div>
                        </label>
                    </div>
                </div>
                @endif

                <h3 style="margin-bottom: 20px; color: #333;">Reservation & Payment Information</h3>
                
                @if($website->payment_method === 'stripe')
                    <div id="stripe-form" class="payment-form active">
                        <form id="payment-form" method="POST" action="{{ route('custom-invoice.process-payment', $invoice->payment_token) }}">
                            @csrf
                            <input type="hidden" name="payment_type" id="payment_type_input" value="{{ $invoice->refundable > 0 ? 'deposit' : 'full' }}">
                            <input type="hidden" name="payment_amount" id="payment_amount_input" value="{{ $invoice->refundable > 0 ? $invoice->refundable : $invoice->total }}">
                            
                            <!-- Reservation Date & Arrival Time -->
                            <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px; background: #eff6ff; padding: 15px; border-radius: 8px; border: 1px solid #bfdbfe;">
                                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                                    <label style="font-weight: 600; color: #1e3a8a;">Reservation / Visit Date <span style="color:red;">*</span></label>
                                    <input type="date" name="package_use_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('package_use_date', date('Y-m-d')) }}" required>
                                    <small style="font-size: 11px; color: #475569;">Select your planned date of visit</small>
                                </div>
                                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                                    <label style="font-weight: 600; color: #1e3a8a;">Estimated Arrival Time <span style="color:red;">*</span></label>
                                    <select name="transportation_arrival_time" class="form-select" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:5px; background:white;" required>
                                        <option value="" disabled selected>Select Arrival Time</option>
                                        <option value="9:00 PM">9:00 PM</option>
                                        <option value="9:30 PM">9:30 PM</option>
                                        <option value="10:00 PM">10:00 PM</option>
                                        <option value="10:30 PM">10:30 PM</option>
                                        <option value="11:00 PM">11:00 PM</option>
                                        <option value="11:30 PM">11:30 PM</option>
                                        <option value="12:00 AM">12:00 AM (Midnight)</option>
                                        <option value="12:30 AM">12:30 AM</option>
                                        <option value="1:00 AM">1:00 AM</option>
                                        <option value="1:30 AM">1:30 AM</option>
                                        <option value="2:00 AM">2:00 AM</option>
                                        <option value="2:30 AM">2:30 AM</option>
                                        <option value="3:00 AM">3:00 AM</option>
                                    </select>
                                    <small style="font-size: 11px; color: #475569;">Select expected arrival time</small>
                                </div>
                            </div>

                            <div class="form-row" style="display: flex; gap: 15px;">
                                <div class="form-group" style="flex: 1;">
                                    <label>First Name</label>
                                    <input type="text" name="firstName" class="form-control" value="{{ explode(' ', $invoice->client_name)[0] ?? '' }}" required>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>Last Name</label>
                                    <input type="text" name="lastName" class="form-control" value="{{ implode(' ', array_slice(explode(' ', $invoice->client_name), 1)) }}" required>
                                </div>
                            </div>

                            <div class="form-row" style="display: flex; gap: 15px;">
                                <div class="form-group" style="flex: 1;">
                                    <label>Email Address</label>
                                    <input type="email" name="billing_email" class="form-control" value="{{ $invoice->client_email }}" required>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>Phone Number</label>
                                    <input type="tel" name="billing_phone" class="form-control" placeholder="(555) 000-0000" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Billing Address</label>
                                <input type="text" name="billing_address" class="form-control" placeholder="Street address" required>
                            </div>

                            <div class="form-row" style="display: flex; gap: 15px;">
                                <div class="form-group" style="flex: 1;">
                                    <label>Country</label>
                                    <select name="billing_country" class="form-select country-selector" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
                                        <option value="US" selected>United States</option>
                                        <option value="CA">Canada</option>
                                        <option value="GB">United Kingdom</option>
                                        <option value="AU">Australia</option>
                                        <option value="MX">Mexico</option>
                                        <option value="DE">Germany</option>
                                        <option value="FR">France</option>
                                        <option value="IT">Italy</option>
                                        <option value="ES">Spain</option>
                                        <option value="BR">Brazil</option>
                                    </select>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>State / Province</label>
                                    <select name="billing_state" class="form-select state-selector" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required></select>
                                </div>
                            </div>

                            <div class="form-row" style="display: flex; gap: 15px;">
                                <div class="form-group" style="flex: 1;">
                                    <label>City</label>
                                    <input type="text" name="billing_city" class="form-control" placeholder="City" required>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>ZIP / Postal Code</label>
                                    <input type="text" name="billing_zip" class="form-control" placeholder="ZIP / Postal code" required>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: 15px;">
                                <label>Card Number</label>
                                <div id="card-number" style="padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: white;"></div>
                            </div>
                            <div class="form-row" style="display: flex; gap: 15px;">
                                <div class="form-group" style="flex: 1;">
                                    <label>Expiry Date</label>
                                    <div id="card-expiry" style="padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: white;"></div>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>CVC</label>
                                    <div id="card-cvc" style="padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: white;"></div>
                                </div>
                            </div>
                            <div id="card-errors" style="color: red; margin: 10px 0; font-size: 14px;"></div>
                            <button type="submit" class="pay-button" id="pay-btn">
                                <i class="fas fa-lock"></i> <span id="pay-btn-text">Pay ${{ number_format($invoice->refundable > 0 ? $invoice->refundable : $invoice->total, 2) }} Securely</span>
                            </button>
                        </form>
                    </div>
                @elseif($website->payment_method === 'authorize')
                    <div id="authorize-form" class="payment-form active">
                        <form id="payment-form" method="POST" action="{{ route('custom-invoice.process-payment', $invoice->payment_token) }}">
                            @csrf
                            <input type="hidden" name="payment_type" id="payment_type_input" value="{{ $invoice->refundable > 0 ? 'deposit' : 'full' }}">
                            <input type="hidden" name="payment_amount" id="payment_amount_input" value="{{ $invoice->refundable > 0 ? $invoice->refundable : $invoice->total }}">
                            
                            <!-- Reservation Date & Arrival Time -->
                            <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px; background: #eff6ff; padding: 15px; border-radius: 8px; border: 1px solid #bfdbfe;">
                                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                                    <label style="font-weight: 600; color: #1e3a8a;">Reservation / Visit Date <span style="color:red;">*</span></label>
                                    <input type="date" name="package_use_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('package_use_date', date('Y-m-d')) }}" required>
                                    <small style="font-size: 11px; color: #475569;">Select your planned date of visit</small>
                                </div>
                                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                                    <label style="font-weight: 600; color: #1e3a8a;">Estimated Arrival Time <span style="color:red;">*</span></label>
                                    <select name="transportation_arrival_time" class="form-select" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:5px; background:white;" required>
                                        <option value="" disabled selected>Select Arrival Time</option>
                                        <option value="9:00 PM">9:00 PM</option>
                                        <option value="9:30 PM">9:30 PM</option>
                                        <option value="10:00 PM">10:00 PM</option>
                                        <option value="10:30 PM">10:30 PM</option>
                                        <option value="11:00 PM">11:00 PM</option>
                                        <option value="11:30 PM">11:30 PM</option>
                                        <option value="12:00 AM">12:00 AM (Midnight)</option>
                                        <option value="12:30 AM">12:30 AM</option>
                                        <option value="1:00 AM">1:00 AM</option>
                                        <option value="1:30 AM">1:30 AM</option>
                                        <option value="2:00 AM">2:00 AM</option>
                                        <option value="2:30 AM">2:30 AM</option>
                                        <option value="3:00 AM">3:00 AM</option>
                                    </select>
                                    <small style="font-size: 11px; color: #475569;">Select expected arrival time</small>
                                </div>
                            </div>

                            <div class="form-row" style="display: flex; gap: 15px;">
                                <div class="form-group" style="flex: 1;">
                                    <label>First Name</label>
                                    <input type="text" name="firstName" class="form-control" value="{{ explode(' ', $invoice->client_name)[0] ?? '' }}" required>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>Last Name</label>
                                    <input type="text" name="lastName" class="form-control" value="{{ implode(' ', array_slice(explode(' ', $invoice->client_name), 1)) }}" required>
                                </div>
                            </div>

                            <div class="form-row" style="display: flex; gap: 15px;">
                                <div class="form-group" style="flex: 1;">
                                    <label>Email Address</label>
                                    <input type="email" name="billing_email" class="form-control" value="{{ $invoice->client_email }}" required>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>Phone Number</label>
                                    <input type="tel" name="billing_phone" class="form-control" placeholder="(555) 000-0000" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Billing Address</label>
                                <input type="text" name="billing_address" class="form-control" placeholder="Street address" required>
                            </div>

                            <div class="form-row" style="display: flex; gap: 15px;">
                                <div class="form-group" style="flex: 1;">
                                    <label>Country</label>
                                    <select name="billing_country" class="form-select country-selector" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
                                        <option value="US" selected>United States</option>
                                        <option value="CA">Canada</option>
                                        <option value="GB">United Kingdom</option>
                                        <option value="AU">Australia</option>
                                        <option value="MX">Mexico</option>
                                        <option value="DE">Germany</option>
                                        <option value="FR">France</option>
                                        <option value="IT">Italy</option>
                                        <option value="ES">Spain</option>
                                        <option value="BR">Brazil</option>
                                    </select>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>State / Province</label>
                                    <select name="billing_state" class="form-select state-selector" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required></select>
                                </div>
                            </div>

                            <div class="form-row" style="display: flex; gap: 15px;">
                                <div class="form-group" style="flex: 1;">
                                    <label>City</label>
                                    <input type="text" name="billing_city" class="form-control" placeholder="City" required>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>ZIP / Postal Code</label>
                                    <input type="text" name="billing_zip" class="form-control" placeholder="ZIP / Postal code" required>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: 15px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                    <label style="margin: 0;">Card Number</label>
                                    <span class="card-brand-icon"><i class="fas fa-credit-card text-muted" style="font-size: 18px;"></i></span>
                                </div>
                                <input type="text" name="cardNumber" class="form-control" placeholder="4111 1111 1111 1111" required>
                            </div>
                            <div class="form-row" style="display: flex; gap: 15px;">
                                <div class="form-group" style="flex: 1;">
                                    <label>Expiry Date (MM/YY)</label>
                                    <input type="text" name="expirationDate" class="form-control" placeholder="12/28" required>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>CVV</label>
                                    <input type="text" name="cvv" class="form-control" placeholder="123" required>
                                </div>
                            </div>

                            <button type="submit" class="pay-button" id="pay-btn">
                                <i class="fas fa-lock"></i> <span id="pay-btn-text">Pay ${{ number_format($invoice->refundable > 0 ? $invoice->refundable : $invoice->total, 2) }} Securely</span>
                            </button>
                        </form>
                    </div>
                @endif

                <div style="margin-top: 20px; padding: 15px; background-color: #f9f9f9; border-radius: 5px;">
                    <p style="margin: 0; font-size: 12px; color: #666;">
                        <i class="fas fa-shield-alt"></i> <strong>Secure Payment:</strong> Your payment information is encrypted and secure. We never store your card details.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @if($website->payment_method === 'stripe')
    <script src="https://js.stripe.com/v3/"></script>
    
    @php
        $setting = \App\Models\Setting::find(1);
    @endphp
    
    <script>
        const depositAmount = {{ $invoice->refundable ?? 0 }};
        const fullAmount = {{ $invoice->total }};
        const hasDeposit = {{ $invoice->refundable > 0 ? 'true' : 'false' }};

        // Initialize Stripe
        const stripe = Stripe("{{ $website->stripe_public_key ?? $setting->stripe_key }}");
        const elements = stripe.elements();

        const style = {
            base: {
                fontSize: '16px',
                color: '#32325d',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        const cardNumber = elements.create('cardNumber', {style: style});
        const cardExpiry = elements.create('cardExpiry', {style: style});
        const cardCvc = elements.create('cardCvc', {style: style});

        cardNumber.mount('#card-number');
        cardExpiry.mount('#card-expiry');
        cardCvc.mount('#card-cvc');

        // Handle payment type selection
        if (hasDeposit) {
            $('input[name="payment_type"]').on('change', function() {
                const type = $(this).val();
                const amount = type === 'deposit' ? depositAmount : fullAmount;
                
                $('#payment_type_input').val(type);
                $('#payment_amount_input').val(amount);
                $('#pay-btn-text').text('Pay $' + amount.toFixed(2) + ' Securely');
                
                // Update border styling
                $('input[name="payment_type"]').parent().css('border-color', '#ddd');
                $(this).parent().css('border-color', '#667eea');
            });
        }

        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn = $('#pay-btn');
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            const {token, error} = await stripe.createToken(cardNumber);

            if (error) {
                document.getElementById('card-errors').textContent = error.message;
                btn.prop('disabled', false);
                const amount = $('#payment_amount_input').val();
                btn.html('<i class="fas fa-lock"></i> <span id="pay-btn-text">Pay $' + parseFloat(amount).toFixed(2) + ' Securely</span>');
            } else {
                const hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);
                form.submit();
            }
        });
    </script>
    @else
    <script>
        const depositAmount = {{ $invoice->refundable ?? 0 }};
        const fullAmount = {{ $invoice->total }};
        const hasDeposit = {{ $invoice->refundable > 0 ? 'true' : 'false' }};

        $(document).ready(function() {
            // Handle payment type selection
            if (hasDeposit) {
                $('input[name="payment_type"]').on('change', function() {
                    const type = $(this).val();
                    const amount = type === 'deposit' ? depositAmount : fullAmount;
                    
                    $('#payment_type_input').val(type);
                    $('#payment_amount_input').val(amount);
                    $('#pay-btn-text').text('Pay $' + amount.toFixed(2) + ' Securely');
                    
                    // Update border styling
                    $('input[name="payment_type"]').parent().css('border-color', '#ddd');
                    $(this).parent().css('border-color', '#667eea');
                });
            }

            $('#payment-form').on('submit', function(e) {
                const btn = $('#pay-btn');
                btn.prop('disabled', true);
                btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            });
        });
    </script>
    @endif
    <script>
        (function() {
            const usStates = [
                {code: "AL", name: "Alabama"}, {code: "AK", name: "Alaska"}, {code: "AZ", name: "Arizona"}, {code: "AR", name: "Arkansas"}, {code: "CA", name: "California"},
                {code: "CO", name: "Colorado"}, {code: "CT", name: "Connecticut"}, {code: "DE", name: "Delaware"}, {code: "FL", name: "Florida"}, {code: "GA", name: "Georgia"},
                {code: "HI", name: "Hawaii"}, {code: "ID", name: "Idaho"}, {code: "IL", name: "Illinois"}, {code: "IN", name: "Indiana"}, {code: "IA", name: "Iowa"},
                {code: "KS", name: "Kansas"}, {code: "KY", name: "Kentucky"}, {code: "LA", name: "Louisiana"}, {code: "ME", name: "Maine"}, {code: "MD", name: "Maryland"},
                {code: "MA", name: "Massachusetts"}, {code: "MI", name: "Michigan"}, {code: "MN", name: "Minnesota"}, {code: "MS", name: "Mississippi"}, {code: "MO", name: "Missouri"},
                {code: "MT", name: "Montana"}, {code: "NE", name: "Nebraska"}, {code: "NV", name: "Nevada"}, {code: "NH", name: "New Hampshire"}, {code: "NJ", name: "New Jersey"},
                {code: "NM", name: "New Mexico"}, {code: "NY", name: "New York"}, {code: "NC", name: "North Carolina"}, {code: "ND", name: "North Dakota"}, {code: "OH", name: "Ohio"},
                {code: "OK", name: "Oklahoma"}, {code: "OR", name: "Oregon"}, {code: "PA", name: "Pennsylvania"}, {code: "RI", name: "Rhode Island"}, {code: "SC", name: "South Carolina"},
                {code: "SD", name: "South Dakota"}, {code: "TN", name: "Tennessee"}, {code: "TX", name: "Texas"}, {code: "UT", name: "Utah"}, {code: "VT", name: "Vermont"},
                {code: "VA", name: "Virginia"}, {code: "WA", name: "Washington"}, {code: "WV", name: "West Virginia"}, {code: "WI", name: "Wisconsin"}, {code: "WY", name: "Wyoming"},
                {code: "DC", name: "District of Columbia"}, {code: "PR", name: "Puerto Rico"}
            ];

            const caProvinces = [
                {code: "AB", name: "Alberta"}, {code: "BC", name: "British Columbia"}, {code: "MB", name: "Manitoba"}, {code: "NB", name: "New Brunswick"},
                {code: "NL", name: "Newfoundland and Labrador"}, {code: "NS", name: "Nova Scotia"}, {code: "ON", name: "Ontario"}, {code: "PE", name: "Prince Edward Island"},
                {code: "QC", name: "Quebec"}, {code: "SK", name: "Saskatchewan"}, {code: "NT", name: "Northwest Territories"}, {code: "NU", name: "Nunavut"}, {code: "YT", name: "Yukon"}
            ];

            function updateStates(countrySelect, stateSelect) {
                const country = $(countrySelect).val();
                const $state = $(stateSelect);
                $state.empty();

                let list = [];
                if (country === 'US') {
                    list = usStates;
                } else if (country === 'CA') {
                    list = caProvinces;
                }

                if (list.length > 0) {
                    $state.append('<option value="" disabled selected>Select State / Province</option>');
                    list.forEach(function(item) {
                        $state.append('<option value="' + item.code + '">' + item.name + ' (' + item.code + ')</option>');
                    });
                } else {
                    $state.append('<option value="N/A" selected>N/A - Other Region</option>');
                }
            }

            $(document).ready(function() {
                $('.country-selector').each(function() {
                    const stateSelect = $(this).closest('form').find('.state-selector');
                    updateStates(this, stateSelect);
                    $(this).on('change', function() {
                        updateStates(this, stateSelect);
                    });
                });

                $('input[name="expirationDate"]').each(function() {
                    $(this).attr('placeholder', 'MM/YY').attr('maxlength', '5');
                    $(this).on('input', function() {
                        let val = $(this).val().replace(/\D/g, '');
                        if (val.length >= 3) {
                            $(this).val(val.substring(0, 2) + '/' + val.substring(2, 4));
                        } else {
                            $(this).val(val);
                        }
                    });
                });

                // Card Number Auto-Formatter (groups of 4) & Brand Detector
                $('input[name="cardNumber"]').each(function() {
                    $(this).attr('placeholder', '4111 1111 1111 1111').attr('maxlength', '19');
                    $(this).on('input', function() {
                        let val = $(this).val().replace(/\D/g, '');
                        if (val.length > 16) val = val.substring(0, 16);
                        let formatted = val.match(/.{1,4}/g)?.join(' ') || val;
                        $(this).val(formatted);

                        const iconSpan = $(this).closest('.form-group').find('.card-brand-icon');
                        if (iconSpan.length > 0) {
                            if (/^4/.test(val)) {
                                iconSpan.html('<i class="fab fa-cc-visa text-primary" style="font-size: 20px;"></i>');
                            } else if (/^5[1-5]|^2[2-7]/.test(val)) {
                                iconSpan.html('<i class="fab fa-cc-mastercard text-warning" style="font-size: 20px;"></i>');
                            } else if (/^3[47]/.test(val)) {
                                iconSpan.html('<i class="fab fa-cc-amex text-info" style="font-size: 20px;"></i>');
                            } else if (/^6(?:011|5)/.test(val)) {
                                iconSpan.html('<i class="fab fa-cc-discover text-warning" style="font-size: 20px;"></i>');
                            } else {
                                iconSpan.html('<i class="fas fa-credit-card text-muted" style="font-size: 20px;"></i>');
                            }
                        }
                    });
                });
            });
        })();
    </script>
</body>
</html>

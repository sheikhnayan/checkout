<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Reservation Payment - {{ $website->name ?? 'CartVIP' }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('user/assets/img/favicon/favicon.svg') }}?v={{ time() }}" />
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
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 35px rgba(15, 23, 42, 0.08);
            max-width: 760px;
            width: 100%;
            overflow: hidden;
        }
        .payment-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #ffffff;
            padding: 28px 30px;
            text-align: center;
            border-bottom: 3px solid #d97706;
        }
        .payment-header .header-title {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #ffffff;
        }
        .payment-header .header-subtitle {
            margin-top: 6px;
            font-size: 13px;
            color: #fbbf24;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }
        .section-box {
            padding: 24px 30px;
            border-bottom: 1px solid #f1f5f9;
        }
        .section-heading {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f8fafc;
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
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 700;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            font-size: 12px;
            text-transform: uppercase;
            background: #f8fafc;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        .price-summary {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px 20px;
            margin-top: 16px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
            color: #475569;
        }
        .summary-row.total {
            border-top: 2px solid #cbd5e1;
            padding-top: 10px;
            margin-top: 8px;
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            font-size: 14px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #d97706;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15);
        }
        .btn-submit-payment {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 16px;
            border: none;
            padding: 14px 28px;
            border-radius: 8px;
            width: 100%;
            transition: opacity 0.2s;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit-payment:hover {
            opacity: 0.95;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="payment-container">
    <!-- Header -->
    <div class="payment-header">
        <h1 class="header-title">{{ $website->name ?? 'CartVIP' }}</h1>
        <div class="header-subtitle">Reservation #{{ $transaction->transaction_id }} &bull; Secure Payment Portal</div>
    </div>

    <!-- Alert / Notice Box -->
    <div class="section-box" style="background:#fffbeb;border-bottom:1px solid #fde68a;">
        <div class="d-flex gap-3">
            <i class="fas fa-exclamation-triangle text-warning fs-4 mt-1"></i>
            <div>
                <strong class="d-block text-dark mb-1" style="font-size:15px;">Payment Resubmission Required</strong>
                <p class="mb-0 text-muted" style="font-size:14px;line-height:1.6;">
                    Due to a processing issue, your original CartVIP transaction was not completed successfully. Your reservation information was received, but the payment will need to be resubmitted. You were not charged for the original transaction.
                </p>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger mx-4 mt-4 mb-0" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Reservation Info -->
    <div class="section-box">
        <div class="section-heading">Reservation Details</div>
        <div class="detail-row">
            <label>Guest Name</label>
            <span>{{ $transaction->package_first_name }} {{ $transaction->package_last_name }}</span>
        </div>
        <div class="detail-row">
            <label>Email Address</label>
            <span>{{ $transaction->package_email }}</span>
        </div>
        @if(!empty($transaction->package_use_date))
        <div class="detail-row">
            <label>Visit / Reservation Date</label>
            <span>{{ \Carbon\Carbon::parse($transaction->package_use_date)->format('M d, Y') }}</span>
        </div>
        @endif
        @if(!empty($transaction->transportation_arrival_time))
        <div class="detail-row">
            <label>Estimated Arrival Time</label>
            <span>{{ $transaction->transportation_arrival_time }}</span>
        </div>
        @endif
    </div>

    <!-- Purchased Items & Breakdown -->
    <div class="section-box">
        <div class="section-heading">Order Summary</div>
        @php
            $cartItems = is_array($transaction->cart_items) ? $transaction->cart_items : (json_decode($transaction->cart_items, true) ?: []);
        @endphp
        @if(!empty($cartItems))
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
                @php
                    $itemName = $item['package_name'] ?? $item['name'] ?? 'Package Item';
                    $itemQty = max(1, (int) ($item['guests'] ?? $item['quantity'] ?? 1));
                    $itemUnitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
                    $itemTotal = (float) ($item['line_total'] ?? $item['total'] ?? ($itemUnitPrice * $itemQty));
                @endphp
                <tr>
                    <td>{{ $itemName }}</td>
                    <td class="text-center">{{ $itemQty }}</td>
                    <td class="text-end">${{ number_format($itemUnitPrice, 2) }}</td>
                    <td class="text-end">${{ number_format($itemTotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="price-summary">
            <div class="summary-row total">
                <span>Total Due Now</span>
                <span class="text-warning-emphasis">${{ number_format((float) $transaction->total, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Payment Form -->
    <div class="section-box">
        <div class="section-heading">Payment Information</div>
        <form action="{{ route('transaction.repay.process', $transaction->repay_token) }}" method="POST" id="repayPaymentForm">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">First Name on Card <span class="text-danger">*</span></label>
                    <input type="text" name="firstName" class="form-control" value="{{ old('firstName', $transaction->payment_first_name ?: $transaction->package_first_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">Last Name on Card <span class="text-danger">*</span></label>
                    <input type="text" name="lastName" class="form-control" value="{{ old('lastName', $transaction->payment_last_name ?: $transaction->package_last_name) }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold small text-muted">Card Number <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-credit-card text-muted"></i></span>
                        <input type="text" name="cardNumber" id="cardNumber" class="form-control" placeholder="4000 0000 0000 0000" maxlength="19" required autocomplete="cc-number">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">Expiration Date (MM/YY) <span class="text-danger">*</span></label>
                    <input type="text" name="expirationDate" id="expirationDate" class="form-control" placeholder="MM/YY" maxlength="7" required autocomplete="cc-exp">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">Security Code (CVV) <span class="text-danger">*</span></label>
                    <input type="text" name="cvv" class="form-control" placeholder="123" maxlength="4" required autocomplete="cc-csc">
                </div>

                <!-- Billing Address -->
                <div class="col-12 mt-4">
                    <div class="section-heading" style="font-size:12px;margin-bottom:10px;">Billing Address</div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold small text-muted">Street Address</label>
                    <input type="text" name="billing_address" class="form-control" value="{{ old('billing_address', $transaction->payment_address) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">City</label>
                    <input type="text" name="billing_city" class="form-control" value="{{ old('billing_city', $transaction->payment_city) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">State</label>
                    <input type="text" name="billing_state" class="form-control" value="{{ old('billing_state', $transaction->payment_state) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Zip Code</label>
                    <input type="text" name="billing_zip" class="form-control" value="{{ old('billing_zip', $transaction->payment_zip_code) }}">
                </div>
            </div>

            <div class="mt-4 pt-2">
                <button type="submit" class="btn-submit-payment" id="btnSubmitPayment">
                    <i class="fas fa-lock"></i> Pay ${{ number_format((float) $transaction->total, 2) }} &amp; Confirm Reservation
                </button>
            </div>
            <div class="text-center mt-3 text-muted" style="font-size:12px;">
                <i class="fas fa-shield-alt text-success me-1"></i> 256-bit SSL encrypted &bull; Your card details are processed directly via secure payment gateway.
            </div>
        </form>
    </div>
</div>

<script>
    // Format card number with spaces
    document.getElementById('cardNumber')?.addEventListener('input', function (e) {
        let val = e.target.value.replace(/\D/g, '');
        let formatted = val.match(/.{1,4}/g)?.join(' ') || val;
        e.target.value = formatted.substring(0, 19);
    });

    // Format expiration date with slash
    document.getElementById('expirationDate')?.addEventListener('input', function (e) {
        let val = e.target.value.replace(/\D/g, '');
        if (val.length >= 2) {
            e.target.value = val.substring(0, 2) + '/' + val.substring(2, 4);
        } else {
            e.target.value = val;
        }
    });

    // Disable button on submit to avoid duplicate charges
    document.getElementById('repayPaymentForm')?.addEventListener('submit', function () {
        const btn = document.getElementById('btnSubmitPayment');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing Secure Live Payment...';
        }
    });
</script>
</body>
</html>

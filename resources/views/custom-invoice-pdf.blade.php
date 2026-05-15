<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice - #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Helvetica, Arial, 'DejaVu Sans', sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header-content {
            flex: 1;
        }
        .header-title {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
            margin: 0 0 10px 0;
        }
        .header-subtitle {
            font-size: 14px;
            color: #666;
            margin: 0;
        }
        .header-qr {
            text-align: center;
            margin-left: 20px;
        }
        .header-qr img {
            width: 120px;
            height: 120px;
            border: 1px solid #ddd;
            padding: 5px;
            background: white;
        }
        .header-qr-label {
            font-size: 10px;
            color: #999;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #333;
            text-transform: uppercase;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 13px;
        }
        .info-label {
            color: #666;
            font-weight: 600;
        }
        .info-value {
            color: #333;
        }
        .grid-2 {
            display: flex;
            gap: 40px;
            margin-bottom: 20px;
        }
        .grid-2 .section {
            flex: 1;
            margin-bottom: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
        }
        thead {
            background-color: #f5f5f5;
        }
        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 700;
            color: #333;
            border-bottom: 2px solid #ddd;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 3px solid #667eea;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 13px;
        }
        .total-row.grand-total {
            font-size: 16px;
            font-weight: 700;
            color: #667eea;
            border-top: 1px solid #ddd;
            padding-top: 12px;
            margin-top: 8px;
        }
        .payment-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .payment-status.deposit {
            background-color: #fff3cd;
            color: #856404;
        }
        .payment-status.full {
            background-color: #d4edda;
            color: #155724;
        }
        .footer {
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
            margin-top: 30px;
            font-size: 11px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="invoice-container">
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <h1 class="header-title">INVOICE</h1>
            <p class="header-subtitle">Invoice #{{ $invoice->id }} | Transaction ID: {{ $transaction->transaction_id }}</p>
        </div>
        @if($transaction && $transaction->ticket_qr_code)
        <div class="header-qr">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($transaction->ticket_qr_code) }}" alt="QR Code">
            <div class="header-qr-label">Scan for Details</div>
        </div>
        @endif
    </div>

    <!-- Billing & Order Info -->
    <div class="grid-2">
        <div class="section">
            <div class="section-title">Bill To</div>
            <div class="info-row">
                <span class="info-label">Client Name</span>
                <span class="info-value">{{ $invoice->client_name ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $invoice->client_email ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Order Details</div>
            <div class="info-row">
                <span class="info-label">Invoice Date</span>
                <span class="info-value">{{ $invoice->created_at->format('M d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Date</span>
                <span class="info-value">{{ $transaction->created_at->format('M d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Status</span>
                <span class="info-value">
                    @if($paymentType === 'deposit')
                        <span class="payment-status deposit">Deposit Paid</span>
                    @else
                        <span class="payment-status full">Paid In Full</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="section">
        <div class="section-title">Order Items</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if($invoice->items && count($invoice->items) > 0)
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description ?? 'Item' }}</td>
                        <td class="text-right">{{ $item->quantity ?? 1 }}</td>
                        <td class="text-right">${{ number_format((float)($item->unit_price ?? 0), 2) }}</td>
                        <td class="text-right">${{ number_format((float)($item->total ?? 0), 2) }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">No items listed</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Price Summary -->
        <div class="total-section">
            <div class="total-row">
                <span>Subtotal</span>
                <span>${{ number_format((float)($invoice->subtotal ?? $invoice->total), 2) }}</span>
            </div>

            @if((float)($invoice->discount ?? 0) > 0)
            <div class="total-row">
                <span>Discount</span>
                <span>-${{ number_format((float)($invoice->discount ?? 0), 2) }}</span>
            </div>
            @endif

            @if((float)($invoice->tax ?? 0) > 0)
            <div class="total-row">
                <span>Tax</span>
                <span>${{ number_format((float)($invoice->tax ?? 0), 2) }}</span>
            </div>
            @endif

            <div class="total-row grand-total">
                <span>Order Total</span>
                <span>${{ number_format((float)($invoice->total ?? 0), 2) }}</span>
            </div>

            @if($paymentType === 'deposit')
            <div class="total-row" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #ddd;">
                <span>Amount Paid (Deposit)</span>
                <span>${{ number_format((float)($transaction->total ?? 0), 2) }}</span>
            </div>
            <div class="total-row">
                <span>Remaining Balance Due</span>
                <span>${{ number_format((float)($invoice->total - $transaction->total), 2) }}</span>
            </div>
            @else
            <div class="total-row" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #ddd;">
                <span>Amount Paid</span>
                <span>${{ number_format((float)($transaction->total ?? 0), 2) }}</span>
            </div>
            @endif
        </div>

        @if(!empty($invoice->notes))
        <div style="margin-top: 20px; padding: 12px; background: #f9f9f9; border-radius: 4px; font-size: 12px;">
            <strong>Notes:</strong>
            <p style="margin: 6px 0 0 0;">{{ $invoice->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your payment! This is an automated invoice. Please do not reply to this email.</p>
        <p style="margin-top: 10px; font-size: 10px; color: #999;">CartVIP - Payment Processing</p>
    </div>
</div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #f9f9f9;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            background-color: white;
            padding: 30px;
        }
        .invoice-details {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .invoice-details table {
            width: 100%;
            font-size: 14px;
        }
        .invoice-details table td {
            padding: 8px 0;
        }
        .invoice-details table td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table thead {
            background-color: #f5f5f5;
            border-bottom: 2px solid #2c3e50;
        }
        .items-table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            color: #2c3e50;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            width: 100%;
            margin-bottom: 30px;
        }
        .totals table {
            width: 100%;
            margin-left: auto;
            width: 300px;
        }
        .totals table tr {
            border-bottom: 1px solid #ddd;
        }
        .totals table td {
            padding: 10px;
        }
        .totals table td:first-child {
            text-align: right;
        }
        .totals table tr.total-row {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
            border: none;
        }
        .totals table tr.total-row td {
            padding: 15px 10px;
            font-size: 18px;
        }
        .payment-button {
            display: inline-block;
            background-color: #27ae60;
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            margin-top: 20px;
        }
        .payment-button:hover {
            background-color: #229954;
        }
        .notes {
            background-color: #f5f5f5;
            padding: 15px;
            border-left: 4px solid #2c3e50;
            margin-top: 20px;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
        }
        .footer-link {
            color: #3498db;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Invoice #{{ $invoice->id }}</h1>
            <p>Payment Required</p>
        </div>

        <div class="content">
            <p>Dear {{ $invoice->client_name }},</p>

            <p>Thank you for your business! We have prepared an invoice for you. Please review the details below and proceed with payment using the secure payment link provided.</p>

            <div class="invoice-details">
                <table>
                    <tr>
                        <td>Invoice #:</td>
                        <td>{{ $invoice->id }}</td>
                    </tr>
                    <tr>
                        <td>Date:</td>
                        <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td>Bill To:</td>
                        <td>{{ $invoice->client_name }}<br>{{ $invoice->client_email }}</td>
                    </tr>
                    <tr>
                        <td>Status:</td>
                        <td><strong>PENDING PAYMENT</strong></td>
                    </tr>
                </table>
            </div>

            <h3 style="margin-top: 30px; margin-bottom: 15px; color: #2c3e50;">Order Details</h3>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Price</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">${{ number_format($item->price, 2) }}</td>
                        <td style="text-align: right;">${{ number_format($item->getLineTotal(), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <table>
                    <tr>
                        <td style="text-align: left;">Subtotal:</td>
                        <td style="text-align: right;">${{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    @if($invoice->sales_tax > 0)
                    <tr>
                        <td style="text-align: left;">{{ $invoice->sales_tax_name ?? 'Sales Tax' }}:</td>
                        <td style="text-align: right;">${{ number_format($invoice->sales_tax, 2) }}</td>
                    </tr>
                    @endif
                    @if($invoice->service_charge > 0)
                    <tr>
                        <td style="text-align: left;">{{ $invoice->service_charge_name ?? 'Service Charge' }}:</td>
                        <td style="text-align: right;">${{ number_format($invoice->service_charge, 2) }}</td>
                    </tr>
                    @endif
                    @if($invoice->gratuity > 0)
                    <tr>
                        <td style="text-align: left;">{{ $invoice->gratuity_name ?? 'Gratuity Fee' }}:</td>
                        <td style="text-align: right;">${{ number_format($invoice->gratuity, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td style="text-align: left;">TOTAL DUE:</td>
                        <td style="text-align: right;">${{ number_format($invoice->total, 2) }}</td>
                    </tr>
                    @if($invoice->refundable > 0)
                    <tr style="border-top: 1px dashed #ddd;">
                        <td colspan="2" style="text-align: left; padding-top: 10px; font-size: 13px; color: #666;">
                            <em>{{ $invoice->refundable_name ?? 'Non-Refundable Deposit' }} ({{ number_format($invoice->website->refundable_fee ?? 0) }}%): ${{ number_format($invoice->refundable, 2) }}</em>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>

            @if($invoice->notes)
            <div class="notes">
                <h4 style="margin-top: 0; color: #2c3e50;">Notes:</h4>
                <p>{{ $invoice->notes }}</p>
            </div>
            @endif

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ $invoice->getPaymentUrl() }}" class="payment-button">PAY NOW</a>
            </div>

            <p style="margin-top: 30px; color: #666; font-size: 13px;">
                If you have any questions about this invoice, please reply to this email or contact us directly.
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0;">
                © {{ now()->year }} {{ $invoice->website->name ?? 'CartVIP' }}. All rights reserved.
            </p>
            <p style="margin: 5px 0 0 0; font-size: 11px;">
                This is an automated message. Please do not reply with card or payment information.
            </p>
        </div>
    </div>
</body>
</html>

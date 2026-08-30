<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->id }} - Payment Required</title>
    <style>
        body {
            font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
            background-color: #0b0f19;
            color: #e2e8f0;
            margin: 0;
            padding: 20px 0;
            line-height: 1.6;
        }
        .email-wrapper {
            max-width: 650px;
            margin: 0 auto;
            background-color: #1e293b;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(255, 204, 0, 0.25);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.6);
        }
        .email-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 35px 30px;
            text-align: center;
            border-bottom: 2px solid #ffcc00;
        }
        .club-name {
            color: #ffcc00;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin: 0 0 8px 0;
        }
        .invoice-tag {
            display: inline-block;
            background: rgba(255, 204, 0, 0.15);
            color: #ffcc00;
            border: 1px solid rgba(255, 204, 0, 0.35);
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.05em;
        }
        .email-body {
            padding: 35px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #f8fafc;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .intro-text {
            color: #cbd5e1;
            font-size: 15px;
            margin-bottom: 25px;
        }
        .details-card {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .details-table td {
            padding: 6px 0;
            color: #cbd5e1;
        }
        .details-table td.label {
            font-weight: 600;
            color: #94a3b8;
            width: 35%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background: #0f172a;
            color: #ffcc00;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 204, 0, 0.3);
        }
        .items-table td {
            padding: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            color: #e2e8f0;
            font-size: 14px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-card {
            background: rgba(15, 23, 42, 0.7);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .totals-table td {
            padding: 7px 0;
            color: #cbd5e1;
        }
        .totals-table tr.grand-total-row td {
            padding-top: 14px;
            border-top: 1px solid rgba(255, 204, 0, 0.4);
            font-size: 18px;
            font-weight: 800;
            color: #ffcc00;
        }
        .cta-container {
            text-align: center;
            margin: 35px 0 25px 0;
        }
        .pay-button {
            display: inline-block;
            background: linear-gradient(135deg, #ffcc00 0%, #e6b800 100%);
            color: #0f172a !important;
            padding: 16px 42px;
            border-radius: 30px;
            font-weight: 800;
            font-size: 16px;
            text-decoration: none !important;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            box-shadow: 0 8px 25px rgba(255, 204, 0, 0.3);
        }
        .notes-box {
            background: rgba(255, 204, 0, 0.05);
            border-left: 4px solid #ffcc00;
            border-radius: 4px;
            padding: 16px;
            margin-top: 25px;
            color: #cbd5e1;
            font-size: 14px;
        }
        .notes-title {
            color: #ffcc00;
            font-weight: 700;
            margin: 0 0 6px 0;
            font-size: 14px;
        }
        .email-footer {
            background-color: #0f172a;
            padding: 25px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            @if($invoice->website && $invoice->website->logo)
                <img src="{{ asset('storage/' . $invoice->website->logo) }}" alt="{{ $invoice->website->name }}" style="max-height: 50px; margin-bottom: 12px;">
            @endif
            <h1 class="club-name">{{ $invoice->website->name ?? 'CartVIP' }}</h1>
            <span class="invoice-tag">INVOICE #{{ $invoice->id }} &bull; PAYMENT REQUIRED</span>
        </div>

        <div class="email-body">
            <h2 class="greeting">Hello {{ $invoice->client_name }},</h2>
            <p class="intro-text">
                An invoice has been issued to you by <strong>{{ $invoice->website->name ?? 'CartVIP' }}</strong>. Please review your order summary below and complete your payment using the secure link.
            </p>

            <div class="details-card">
                <table class="details-table">
                    <tr>
                        <td class="label">Invoice Number:</td>
                        <td><strong>#{{ $invoice->id }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Issue Date:</td>
                        <td>{{ $invoice->created_at->timezone('America/Los_Angeles')->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Bill To:</td>
                        <td>{{ $invoice->client_name }} ({{ $invoice->client_email }})</td>
                    </tr>
                    <tr>
                        <td class="label">Payment Status:</td>
                        <td><span style="color: #ffcc00; font-weight: 700;">UNPAID</span></td>
                    </tr>
                </table>
            </div>

            <h3 style="color: #f8fafc; font-size: 16px; margin: 25px 0 12px 0;">Itemized Details</h3>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td><strong>{{ $item->name }}</strong></td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">${{ number_format($item->price, 2) }}</td>
                        <td class="text-right">${{ number_format($item->getLineTotal(), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals-card">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    @if($invoice->sales_tax > 0)
                    <tr>
                        <td class="label">{{ $invoice->sales_tax_name ?? 'Sales Tax' }}:</td>
                        <td class="text-right">${{ number_format($invoice->sales_tax, 2) }}</td>
                    </tr>
                    @endif
                    @if($invoice->service_charge > 0)
                    <tr>
                        <td class="label">{{ $invoice->service_charge_name ?? 'Service Charge' }}:</td>
                        <td class="text-right">${{ number_format($invoice->service_charge, 2) }}</td>
                    </tr>
                    @endif
                    @if($invoice->gratuity > 0)
                    <tr>
                        <td class="label">{{ $invoice->gratuity_name ?? 'Gratuity Fee' }}:</td>
                        <td class="text-right">${{ number_format($invoice->gratuity, 2) }}</td>
                    </tr>
                    @endif
                    @if($invoice->processing_fee > 0)
                    <tr>
                        <td class="label">{{ $invoice->processing_fee_name ?? 'Processing Fee' }}:</td>
                        <td class="text-right">${{ number_format($invoice->processing_fee, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="grand-total-row">
                        <td class="label" style="color:#ffcc00;">TOTAL DUE:</td>
                        <td class="text-right">${{ number_format($invoice->total, 2) }}</td>
                    </tr>
                </table>
            </div>

            @if($invoice->notes)
            <div class="notes-box">
                <p class="notes-title">Additional Notes:</p>
                <p style="margin: 0; font-size: 14px;">{{ $invoice->notes }}</p>
            </div>
            @endif

            <div class="cta-container">
                <a href="{{ $invoice->getPaymentUrl() }}" class="pay-button" target="_blank">
                    PAY NOW &bull; ${{ number_format($invoice->total, 2) }}
                </a>
            </div>

            <p style="color: #94a3b8; font-size: 13px; text-align: center; margin-top: 25px;">
                If you have questions regarding this invoice, please reach out to us directly.
            </p>
        </div>

        <div class="email-footer">
            <p style="margin: 0 0 5px 0;">
                &copy; {{ now()->year }} {{ $invoice->website->name ?? 'CartVIP' }}. All rights reserved.
            </p>
            <p style="margin: 0; font-size: 11px;">
                This is an automated invoice email. Please do not reply with credit card information.
            </p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->id }} - Payment Required</title>
    <style>
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f8;
            color: #1e293b;
            margin: 0;
            padding: 30px 0;
            line-height: 1.6;
        }
        .email-wrapper {
            max-width: 620px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
        }
        .email-header {
            background-color: #ffffff;
            padding: 32px 30px 24px 30px;
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
        }
        .club-name {
            color: #0f172a;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0 0 6px 0;
        }
        .invoice-tag {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 4px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.03em;
        }
        .email-body {
            padding: 32px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 8px;
        }
        .intro-text {
            color: #475569;
            font-size: 15px;
            margin-bottom: 24px;
        }
        .details-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 24px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .details-table td {
            padding: 6px 0;
            color: #334155;
        }
        .details-table td.label {
            font-weight: 600;
            color: #64748b;
            width: 35%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .items-table th {
            background: #f1f5f9;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 10px 12px;
            text-align: left;
            border-bottom: 2px solid #cbd5e1;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
            font-size: 14px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-card {
            background: #f8fafc;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 28px;
            border: 1px solid #e2e8f0;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .totals-table td {
            padding: 6px 0;
            color: #475569;
        }
        .totals-table tr.grand-total-row td {
            padding-top: 12px;
            border-top: 2px solid #cbd5e1;
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }
        .cta-container {
            text-align: center;
            margin: 30px 0 20px 0;
        }
        .pay-button {
            display: inline-block;
            background: #0f172a;
            color: #ffffff !important;
            padding: 15px 40px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none !important;
            letter-spacing: 0.02em;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }
        .notes-box {
            background: #f8fafc;
            border-left: 4px solid #0f172a;
            border-radius: 4px;
            padding: 14px 16px;
            margin-top: 24px;
            color: #334155;
            font-size: 14px;
        }
        .notes-title {
            color: #0f172a;
            font-weight: 700;
            margin: 0 0 4px 0;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 22px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    @php
        $logoUrl = null;
        if ($invoice->website && !empty($invoice->website->logo)) {
            $logo = ltrim((string) $invoice->website->logo, '/');
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
                $logoUrl = $logo;
            } else {
                $logoPath = str_starts_with($logo, 'storage/') ? $logo : ('storage/' . $logo);
                if (file_exists(public_path($logoPath))) {
                    $logoUrl = url($logoPath);
                }
            }
        }
    @endphp

    <div class="email-wrapper">
        <div class="email-header">
            @if($logoUrl)
                <div style="margin-bottom: 12px;">
                    <img src="{{ $logoUrl }}" alt="{{ $invoice->website->name ?? 'Venue' }}" style="max-height: 48px; max-width: 220px; object-fit: contain;" onerror="this.parentElement.style.display='none';">
                </div>
            @endif
            <h1 class="club-name">{{ $invoice->website->name ?? 'CartVIP' }}</h1>
            <span class="invoice-tag">INVOICE #{{ $invoice->id }} &bull; PAYMENT REQUIRED</span>
        </div>

        <div class="email-body">
            <h2 class="greeting">Hello {{ $invoice->client_name }},</h2>
            
            <div style="background-color: #fff1f2; border: 1px solid #fecdd3; border-left: 4px solid #e11d48; padding: 16px 20px; border-radius: 8px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                    <span style="color: #be123c; font-weight: 800; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em;">
                        ⚠️ PAYMENT REQUIRED
                    </span>
                    <span style="color: #9f1239; font-weight: 800; font-size: 15px;">
                        Total Due: ${{ number_format($invoice->total, 2) }}
                    </span>
                </div>
                <p style="margin: 8px 0 0 0; color: #881337; font-size: 13.5px; line-height: 1.5;">
                    Payment is required for invoice <strong>#{{ $invoice->id }}</strong> issued by <strong>{{ $invoice->website->name ?? 'CartVIP' }}</strong>. Please review your itemized summary below and complete your payment online.
                </p>
            </div>

            <p class="intro-text">
                An invoice has been prepared for you by <strong>{{ $invoice->website->name ?? 'CartVIP' }}</strong>. Please review your order details below and complete payment via the secure link.
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
                        <td><span style="color: #d97706; font-weight: 700;">UNPAID</span></td>
                    </tr>
                </table>
            </div>

            <h3 style="color: #0f172a; font-size: 15px; margin: 24px 0 10px 0; font-weight: 700;">Itemized Summary</h3>

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
                        <td class="label" style="color:#0f172a;">TOTAL DUE:</td>
                        <td class="text-right">${{ number_format($invoice->total, 2) }}</td>
                    </tr>
                </table>
            </div>

            @if($invoice->notes)
            <div class="notes-box">
                <p class="notes-title">Additional Notes:</p>
                <p style="margin: 0;">{{ $invoice->notes }}</p>
            </div>
            @endif

            <div class="cta-container">
                <a href="{{ $invoice->getPaymentUrl() }}" class="pay-button" target="_blank">
                    PAY INVOICE &bull; ${{ number_format($invoice->total, 2) }}
                </a>
            </div>

            <p style="color: #64748b; font-size: 13px; text-align: center; margin-top: 24px;">
                If you have any questions regarding this invoice, please contact {{ $invoice->website->name ?? 'us' }} directly.
            </p>
        </div>

        <div class="email-footer">
            <p style="margin: 0 0 4px 0;">
                &copy; {{ now()->year }} {{ $invoice->website->name ?? 'CartVIP' }}. All rights reserved.
            </p>
            <p style="margin: 0; font-size: 11px;">
                This is an automated invoice communication. Please do not reply with credit card credentials.
            </p>
        </div>
    </div>
</body>
</html>

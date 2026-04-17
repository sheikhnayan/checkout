<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Custom Invoice Payment Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f8f8; color: #222; margin: 0; padding: 0; }
        .container { background: #fff; max-width: 640px; margin: 30px auto; border-radius: 8px; box-shadow: 0 2px 8px #eee; padding: 28px; }
        h2 { color: #2a7ae2; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { text-align: left; padding: 8px 6px; border-bottom: 1px solid #eee; }
        th { background: #f0f4f8; color: #333; }
        .section-title { margin-top: 26px; color: #2a7ae2; font-size: 1.05em; border-bottom: 1px solid #e0e0e0; padding-bottom: 4px; }
        .right { text-align: right; }
        .total-row td { font-weight: bold; background: #eef5ff; }
    </style>
</head>
<body>
<div class="container">
    <h2>Custom Invoice Payment Confirmation</h2>

    <table>
        <tr><th>Transaction ID</th><td>{{ $transaction->transaction_id }}</td></tr>
        <tr><th>Invoice</th><td>#{{ $invoice->id }}</td></tr>
        <tr><th>Client</th><td>{{ $invoice->client_name }}</td></tr>
        <tr><th>Email</th><td>{{ $invoice->client_email }}</td></tr>
        <tr><th>Payment Type</th><td>{{ $paymentType === 'deposit' ? 'Deposit' : 'Full Payment' }}</td></tr>
        <tr><th>Status</th><td>{{ strtoupper((string) $invoice->status) }}</td></tr>
    </table>

    <div class="section-title">Invoice Items</div>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Qty</th>
                <th class="right">Unit Price</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">${{ number_format((float) $item->price, 2) }}</td>
                <td class="right">${{ number_format((float) $item->getLineTotal(), 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="section-title">Payment Summary</div>
    <table>
        <tr><th>Subtotal</th><td class="right">${{ number_format((float) $invoice->subtotal, 2) }}</td></tr>
        @if((float) $invoice->sales_tax > 0)
            <tr><th>{{ $invoice->sales_tax_name ?: 'Sales Tax' }}</th><td class="right">${{ number_format((float) $invoice->sales_tax, 2) }}</td></tr>
        @endif
        @if((float) $invoice->service_charge > 0)
            <tr><th>{{ $invoice->service_charge_name ?: 'Service Charge' }}</th><td class="right">${{ number_format((float) $invoice->service_charge, 2) }}</td></tr>
        @endif
        @if((float) $invoice->gratuity > 0)
            <tr><th>{{ $invoice->gratuity_name ?: 'Gratuity' }}</th><td class="right">${{ number_format((float) $invoice->gratuity, 2) }}</td></tr>
        @endif
        <tr class="total-row"><th>Order Total</th><td class="right">${{ number_format((float) $invoice->total, 2) }}</td></tr>
        <tr><th>Amount Paid</th><td class="right">${{ number_format((float) $transaction->total, 2) }}</td></tr>
    </table>

    @if(!empty($invoice->notes))
        <div class="section-title">Notes</div>
        <p style="margin:8px 0 0 0;">{{ $invoice->notes }}</p>
    @endif

    <p style="margin-top: 24px; color: #888; font-size: 13px;">This is an automated email. Please do not reply.</p>
</div>
</body>
</html>

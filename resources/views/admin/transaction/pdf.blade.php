<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transaction Details - {{ $transaction->transaction_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 900px; margin: 0 auto; padding: 20px; }
        .header { border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        .section { margin-bottom: 30px; }
        .section-title { font-size: 16px; font-weight: bold; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .row { display: flex; margin-bottom: 10px; }
        .label { width: 30%; font-weight: bold; }
        .value { width: 70%; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f0f0f0; padding: 10px; text-align: left; font-weight: bold; border: 1px solid #ddd; }
        td { padding: 10px; border: 1px solid #ddd; }
        .footer { margin-top: 50px; border-top: 1px solid #ddd; padding-top: 20px; text-align: center; color: #666; font-size: 12px; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; }
        .badge-paid { background: #d4edda; color: #155724; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-partial { background: #ffc107; color: #000; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Transaction Details</h1>
            <p><strong>Order ID:</strong> {{ $transaction->transaction_id ?? '#' . $transaction->id }}</p>
            <p><strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}</p>
        </div>

        <div class="section">
            <div class="section-title">Transaction Information</div>
            <div class="row">
                <div class="label">Order Date:</div>
                <div class="value">{{ $transaction->created_at->format('M d, Y h:i A') }}</div>
            </div>
            <div class="row">
                <div class="label">Event/Package:</div>
                <div class="value">{{ $transaction->type === 'package' ? ($transaction->package_table_label ?: 'Package') : 'Reservation' }}</div>
            </div>
            <div class="row">
                <div class="label">Club/Venue:</div>
                <div class="value">{{ $transaction->website->name ?? 'N/A' }}</div>
            </div>
            <div class="row">
                <div class="label">Reservation Date:</div>
                <div class="value">
                    @if($transaction->package_use_date)
                        {{ optional($transaction->package_use_date)->format('M d, Y') }}
                    @else
                        N/A
                    @endif
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Customer Information</div>
            <div class="row">
                <div class="label">Name:</div>
                <div class="value">{{ $transaction->package_first_name }} {{ $transaction->package_last_name }}</div>
            </div>
            <div class="row">
                <div class="label">Email:</div>
                <div class="value">{{ $transaction->package_email }}</div>
            </div>
            <div class="row">
                <div class="label">Phone:</div>
                <div class="value">{{ $transaction->package_phone ?? 'N/A' }}</div>
            </div>
            <div class="row">
                <div class="label">Number of Guests:</div>
                <div class="value">{{ $transaction->package_number_of_guest ?? 1 }}</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Payment Information</div>
            <div class="row">
                <div class="label">Total Amount:</div>
                <div class="value">${{ number_format((float)$transaction->total, 2) }}</div>
            </div>
            <div class="row">
                <div class="label">Paid Amount:</div>
                <div class="value">${{ number_format((float)($transaction->actual_total ?? $transaction->total), 2) }}</div>
            </div>
            @php
                $paidAmount = (float)($transaction->actual_total ?? $transaction->total ?? 0);
                $totalAmount = (float)($transaction->total ?? 0);
                $dueAmount = $totalAmount - $paidAmount;
                $paymentStatus = $paidAmount >= $totalAmount ? 'Paid' : ($paidAmount > 0 ? 'Partial' : 'Pending');
            @endphp
            <div class="row">
                <div class="label">Due Amount:</div>
                <div class="value">
                    @if($dueAmount > 0)
                        ${{ number_format($dueAmount, 2) }}
                    @else
                        $0.00
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="label">Payment Status:</div>
                <div class="value">
                    <span class="badge badge-{{ $paymentStatus === 'Paid' ? 'paid' : ($paymentStatus === 'Partial' ? 'partial' : 'pending') }}">
                        {{ $paymentStatus }}
                    </span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Commission Information</div>
            @php
                $commission = (float)($transaction->affiliate_commission_amount ?? 0) + (float)($transaction->entertainer_commission_amount ?? 0);
                $commDisplay = ($commission == intval($commission)) ? number_format($commission, 0) : number_format($commission, 2);
            @endphp
            <div class="row">
                <div class="label">Total Commission:</div>
                <div class="value">${{ $commDisplay }}</div>
            </div>
            @if(!empty($transaction->affiliate_id) && !empty($transaction->affiliate))
            <div class="row">
                <div class="label">Affiliate Commission:</div>
                <div class="value">${{ number_format((float)($transaction->affiliate_commission_amount ?? 0), 2) }}</div>
            </div>
            <div class="row">
                <div class="label">Affiliate:</div>
                <div class="value">{{ $transaction->affiliate->display_name ?: optional($transaction->affiliate->user)->name ?: 'N/A' }}</div>
            </div>
            @endif
            @if(!empty($transaction->entertainer_id) && !empty($transaction->entertainer))
            <div class="row">
                <div class="label">Entertainer Commission:</div>
                <div class="value">${{ number_format((float)($transaction->entertainer_commission_amount ?? 0), 2) }}</div>
            </div>
            <div class="row">
                <div class="label">Entertainer:</div>
                <div class="value">{{ $transaction->entertainer->display_name ?: optional($transaction->entertainer->user)->name ?: 'N/A' }}</div>
            </div>
            @endif
        </div>

        @if($transaction->package_note)
        <div class="section">
            <div class="section-title">Booking Notes</div>
            <div class="row">
                <div class="value">{{ $transaction->package_note }}</div>
            </div>
        </div>
        @endif

        @if($transaction->transportation_note)
        <div class="section">
            <div class="section-title">Transportation Notes</div>
            <div class="row">
                <div class="value">{{ $transaction->transportation_note }}</div>
            </div>
        </div>
        @endif

        <div class="footer">
            <p>This is a confidential document. CartVIP © {{ now()->year }}</p>
        </div>
    </div>
</body>
</html>

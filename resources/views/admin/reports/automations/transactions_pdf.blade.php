<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transactions Report</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #0f172a; padding-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; color: #0f172a; }
        .subtitle { color: #666; font-size: 11px; margin-top: 4px; }
        .summary-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px 14px; border-radius: 6px; margin-bottom: 18px; }
        .summary-table { width: 100%; }
        .summary-table td { padding: 4px 8px; font-size: 11px; }
        .summary-val { font-weight: bold; color: #0f172a; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { background: #0f172a; color: #fff; padding: 8px 6px; font-size: 10px; text-align: left; }
        table.data-table td { border-bottom: 1px solid #e2e8f0; padding: 7px 6px; font-size: 10px; }
        .host-badge { background: #e0f2fe; color: #0369a1; padding: 2px 5px; border-radius: 4px; font-weight: bold; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Automated Transactions Report</div>
        <div class="subtitle">Period: {{ $periodLabel }} &bull; Date Range: {{ $startAt->format('M d, Y') }} - {{ $endAt->format('M d, Y') }} (PST)</div>
    </div>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td>Total Transactions: <span class="summary-val">{{ number_format($transactions->count()) }}</span></td>
                <td>Total Revenue: <span class="summary-val">${{ number_format($transactions->sum('total'), 2) }}</span></td>
                <td>With Host Name: <span class="summary-val">{{ number_format($transactions->filter(fn($t) => !empty($t->host_name))->count()) }}</span></td>
                <td>Total Guests: <span class="summary-val">{{ number_format($transactions->sum(fn($t) => max(1, (int)($t->package_number_of_guest ?? 1)))) }}</span></td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Ref #</th>
                <th>Date & Time</th>
                <th>Club</th>
                <th>Host Name</th>
                <th>Customer Name</th>
                <th>Email / Phone</th>
                <th>Package</th>
                <th>Guests</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
                <tr>
                    <td>#{{ $tx->id }}</td>
                    <td>{{ $tx->created_at ? $tx->created_at->copy()->timezone('America/Los_Angeles')->format('M d, Y H:i') : '' }}</td>
                    <td>{{ optional($tx->website)->name ?: ('Club #' . $tx->website_id) }}</td>
                    <td>
                        @if(!empty($tx->host_name))
                            <span class="host-badge">{{ $tx->host_name }}</span>
                        @else
                            &mdash;
                        @endif
                    </td>
                    <td>{{ trim(($tx->package_first_name ?? '') . ' ' . ($tx->package_last_name ?? '')) ?: ($tx->package_name ?: 'N/A') }}</td>
                    <td>{{ $tx->package_email ?: 'N/A' }}</td>
                    <td>{{ $tx->package_table_label ?: (optional($tx->package)->name ?: 'N/A') }}</td>
                    <td>{{ $tx->package_number_of_guest ?? 1 }}</td>
                    <td>${{ number_format((float)($tx->total ?? 0), 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">No transactions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

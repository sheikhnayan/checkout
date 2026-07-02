<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Automation Executive Report</title>
    <style>
        @page { margin: 22px 24px; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 0;
            color: #0f172a;
            font-size: 11px;
            line-height: 1.35;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .title {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }
        .subtitle {
            margin: 3px 0 0;
            color: #475569;
            font-size: 12px;
        }
        .meta {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-top: 8px;
        }
        .meta td {
            border: 1px solid #dbeafe;
            background: #f8fbff;
            border-radius: 6px;
            padding: 6px 8px;
            vertical-align: top;
        }
        .meta .label {
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
        }
        .meta .value {
            font-size: 11px;
            color: #0f172a;
            font-weight: 700;
            margin-top: 2px;
        }
        .section {
            margin-top: 14px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            margin: 0 0 8px;
            border-left: 4px solid #2563eb;
            padding-left: 8px;
        }
        .cards {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
        }
        .card {
            border: 1px solid #dbeafe;
            border-radius: 8px;
            background: #f8fbff;
            padding: 8px 10px;
            vertical-align: top;
            width: 25%;
        }
        .card .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 3px;
        }
        .card .value {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
        }
        .data-table th {
            background: #f1f5f9;
            color: #0f172a;
            text-align: left;
            padding: 7px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
            color: #111827;
        }
        .data-table tr:nth-child(even) td {
            background: #fcfdff;
        }
        .right { text-align: right; }
        .bars {
            margin-top: 6px;
        }
        .bar-row {
            margin-bottom: 8px;
        }
        .bar-head {
            width: 100%;
            border-collapse: collapse;
        }
        .bar-head td {
            font-size: 10px;
            padding: 0;
        }
        .bar-head .name {
            width: 55%;
            color: #0f172a;
            font-weight: 600;
        }
        .bar-head .metric {
            width: 15%;
            text-align: right;
            color: #475569;
        }
        .bar-track {
            margin-top: 4px;
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
        }
        .bar-fill {
            height: 8px;
            border-radius: 999px;
        }
        .bar-fill.revenue { background: #2563eb; }
        .bar-fill.txn { background: #22c55e; }
        .bar-fill.patrons { background: #f59e0b; }
        .footer {
            margin-top: 16px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            color: #64748b;
            font-size: 10px;
        }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
@php
    $formatMoney = fn ($n) => '$' . number_format((float) $n, 2);
    $formatNum = fn ($n) => number_format((float) $n, 0);

    $clubRevenueMax = $clubSnapshot->max('revenue') ?: 0;
    $clubTxnMax = $clubSnapshot->max('transactions') ?: 0;
    $clubPatronMax = $clubSnapshot->max('unique_patrons') ?: 0;

    $daysScanning = max(1, (int) $dailyTrend->count());
@endphp

    <div class="header">
        <h1 class="title">Automation Executive Report</h1>
        <div class="subtitle">Transactions, packages, and venue performance summary</div>

        <table class="meta">
            <tr>
                <td>
                    <div class="label">Report Window</div>
                    <div class="value">{{ $periodLabel }}: {{ $startAt->format('M d, Y h:i A') }} to {{ $endAt->format('M d, Y h:i A') }} PT</div>
                </td>
                <td>
                    <div class="label">Venues Included</div>
                    <div class="value">{{ $selectedWebsites->count() > 0 ? $selectedWebsites->pluck('name')->implode(', ') : 'All Venues' }}</div>
                </td>
                <td>
                    <div class="label">Generated At</div>
                    <div class="value">{{ $generatedAt->format('M d, Y h:i A') }} PT</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Executive Snapshot</h2>
        <table class="cards">
            <tr>
                <td class="card"><div class="label">Total Revenue</div><div class="value">{{ $formatMoney($summary['total_revenue']) }}</div></td>
                <td class="card"><div class="label">Transactions</div><div class="value">{{ $formatNum($summary['total_transactions']) }}</div></td>
                <td class="card"><div class="label">Unique Patrons</div><div class="value">{{ $formatNum($summary['unique_patrons']) }}</div></td>
                <td class="card"><div class="label">Avg Order Value</div><div class="value">{{ $formatMoney($summary['avg_order_value']) }}</div></td>
            </tr>
            <tr>
                <td class="card"><div class="label">Total Guests</div><div class="value">{{ $formatNum($summary['total_guests']) }}</div></td>
                <td class="card"><div class="label">Add-ons Sold (Qty)</div><div class="value">{{ $formatNum($summary['total_addons_qty']) }}</div></td>
                <td class="card"><div class="label">Total Commission</div><div class="value">{{ $formatMoney($summary['total_commission']) }}</div></td>
                <td class="card"><div class="label">Net Revenue</div><div class="value">{{ $formatMoney($summary['net_revenue']) }}</div></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Acquisition Mix</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Source</th>
                    <th class="right">Transactions</th>
                    <th class="right">Revenue</th>
                    <th class="right">Share of Transactions</th>
                </tr>
            </thead>
            <tbody>
                @php $totalTx = max(1, (int) $summary['total_transactions']); @endphp
                <tr>
                    <td>Direct</td>
                    <td class="right">{{ $formatNum($sourceSnapshot['direct']['transactions']) }}</td>
                    <td class="right">{{ $formatMoney($sourceSnapshot['direct']['revenue']) }}</td>
                    <td class="right">{{ number_format(($sourceSnapshot['direct']['transactions'] / $totalTx) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td>Affiliate</td>
                    <td class="right">{{ $formatNum($sourceSnapshot['affiliate']['transactions']) }}</td>
                    <td class="right">{{ $formatMoney($sourceSnapshot['affiliate']['revenue']) }}</td>
                    <td class="right">{{ number_format(($sourceSnapshot['affiliate']['transactions'] / $totalTx) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td>Entertainer</td>
                    <td class="right">{{ $formatNum($sourceSnapshot['entertainer']['transactions']) }}</td>
                    <td class="right">{{ $formatMoney($sourceSnapshot['entertainer']['revenue']) }}</td>
                    <td class="right">{{ number_format(($sourceSnapshot['entertainer']['transactions'] / $totalTx) * 100, 1) }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section page-break">
        <h2 class="section-title">Club Performance (Multi-Venue)</h2>
        <div class="bars">
            @foreach($clubSnapshot as $club)
                @php
                    $revPct = $clubRevenueMax > 0 ? (($club['revenue'] / $clubRevenueMax) * 100) : 0;
                    $txnPct = $clubTxnMax > 0 ? (($club['transactions'] / $clubTxnMax) * 100) : 0;
                    $patronPct = $clubPatronMax > 0 ? (($club['unique_patrons'] / $clubPatronMax) * 100) : 0;
                @endphp
                <div class="bar-row">
                    <table class="bar-head">
                        <tr>
                            <td class="name">{{ $club['website_name'] }}</td>
                            <td class="metric">Revenue {{ $formatMoney($club['revenue']) }}</td>
                            <td class="metric">Txn {{ $formatNum($club['transactions']) }}</td>
                            <td class="metric">Patrons {{ $formatNum($club['unique_patrons']) }}</td>
                        </tr>
                    </table>
                    <div class="bar-track"><div class="bar-fill revenue" style="width: {{ max(0, min(100, $revPct)) }}%;"></div></div>
                    <div class="bar-track"><div class="bar-fill txn" style="width: {{ max(0, min(100, $txnPct)) }}%;"></div></div>
                    <div class="bar-track"><div class="bar-fill patrons" style="width: {{ max(0, min(100, $patronPct)) }}%;"></div></div>
                </div>
            @endforeach
        </div>

        <table class="data-table" style="margin-top: 12px;">
            <thead>
                <tr>
                    <th>Club</th>
                    <th class="right">Transactions</th>
                    <th class="right">Revenue</th>
                    <th class="right">Unique Patrons</th>
                    <th class="right">Guests</th>
                    <th class="right">Avg Order</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clubSnapshot as $club)
                    <tr>
                        <td>{{ $club['website_name'] }}</td>
                        <td class="right">{{ $formatNum($club['transactions']) }}</td>
                        <td class="right">{{ $formatMoney($club['revenue']) }}</td>
                        <td class="right">{{ $formatNum($club['unique_patrons']) }}</td>
                        <td class="right">{{ $formatNum($club['guests']) }}</td>
                        <td class="right">{{ $formatMoney($club['avg_order_value']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section page-break">
        <h2 class="section-title">Package + Daily Activity Insights</h2>

        <table class="data-table" style="margin-bottom: 12px;">
            <thead>
                <tr>
                    <th>Top Packages</th>
                    <th class="right">Transactions</th>
                    <th class="right">Guests</th>
                    <th class="right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topPackages as $pkg)
                    <tr>
                        <td>{{ $pkg['package_name'] }}</td>
                        <td class="right">{{ $formatNum($pkg['transactions']) }}</td>
                        <td class="right">{{ $formatNum($pkg['guests']) }}</td>
                        <td class="right">{{ $formatMoney($pkg['revenue']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Day</th>
                    <th class="right">Transactions</th>
                    <th class="right">Unique Patrons</th>
                    <th class="right">Guests</th>
                    <th class="right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailyTrend as $day)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($day['day'])->format('D, M d') }}</td>
                        <td class="right">{{ $formatNum($day['transactions']) }}</td>
                        <td class="right">{{ $formatNum($day['unique_patrons']) }}</td>
                        <td class="right">{{ $formatNum($day['guests']) }}</td>
                        <td class="right">{{ $formatMoney($day['revenue']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            Automation executive report for management review | Period: {{ $daysScanning }} day(s) | Generated: {{ $generatedAt->format('Y-m-d h:i A T') }}
        </div>
    </div>
</body>
</html>

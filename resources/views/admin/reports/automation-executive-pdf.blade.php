<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Automation Executive Report</title>
    <style>
        @page { margin: 20px 22px; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 0;
            color: #0f172a;
            font-size: 10px;
            line-height: 1.35;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .title {
            margin: 0;
            font-size: 21px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }
        .subtitle {
            margin: 2px 0 0;
            color: #334155;
            font-size: 11px;
        }
        .meta {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-top: 8px;
        }
        .meta td {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 6px 8px;
            vertical-align: top;
        }
        .meta .label {
            color: #64748b;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .meta .value {
            color: #0f172a;
            font-size: 10px;
            font-weight: 700;
        }
        .section {
            margin-top: 12px;
            page-break-inside: avoid;
        }
        .section-title {
            margin: 0 0 7px;
            padding: 5px 8px;
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .cards {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
        }
        .card {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            padding: 6px 8px;
            width: 25%;
            vertical-align: top;
        }
        .card .label {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 2px;
            letter-spacing: 0.4px;
        }
        .card .value {
            font-size: 14px;
            color: #0f172a;
            font-weight: 700;
        }
        .two-col {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
        }
        .panel {
            width: 50%;
            vertical-align: top;
        }
        .graph-box {
            border: 1px solid #cbd5e1;
            padding: 7px;
        }
        .graph-title {
            margin: 0 0 6px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #334155;
            letter-spacing: 0.4px;
        }
        .bar-row {
            margin-bottom: 5px;
        }
        .bar-label {
            font-size: 9px;
            margin-bottom: 2px;
            color: #0f172a;
        }
        .bar-track {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            overflow: hidden;
        }
        .bar-fill { height: 8px; }
        .c1 { background: #0ea5e9; }
        .c2 { background: #22c55e; }
        .c3 { background: #f97316; }
        .c4 { background: #a855f7; }
        .c5 { background: #ef4444; }
        .c6 { background: #14b8a6; }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            margin-top: 8px;
        }
        .data-table th {
            background: #f1f5f9;
            border-bottom: 1px solid #cbd5e1;
            padding: 5px 6px;
            text-align: left;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #334155;
        }
        .data-table td {
            border-bottom: 1px solid #eef2f7;
            padding: 5px 6px;
            font-size: 9px;
            color: #111827;
        }
        .data-table tr:nth-child(even) td { background: #fbfdff; }
        .right { text-align: right; }
        .insight-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
        }
        .insight {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 6px;
            vertical-align: top;
            width: 33.33%;
        }
        .insight .k { font-size: 8px; text-transform: uppercase; color: #64748b; }
        .insight .v { font-size: 11px; color: #0f172a; font-weight: 700; margin-top: 2px; }
        .trend-up { color: #15803d; font-weight: 700; }
        .trend-down { color: #b91c1c; font-weight: 700; }
        .trend-flat { color: #334155; font-weight: 700; }
        .donut-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-top: 8px;
        }
        .donut-card {
            width: 33.33%;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            text-align: center;
            vertical-align: top;
            padding: 7px;
        }
        .donut-title {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.4px;
            margin-bottom: 4px;
        }
        .donut-value {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 3px;
        }
        .donut-fallback {
            width: 86px;
            height: 86px;
            margin: 0 auto;
            border-radius: 50%;
            border: 8px solid #d1d5db;
            position: relative;
            box-sizing: border-box;
            background: #ffffff;
        }
        .donut-fallback::after {
            content: '';
            position: absolute;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #ffffff;
            top: 14px;
            left: 14px;
            border: 1px solid #e5e7eb;
        }
        .donut-fallback .center {
            position: absolute;
            top: 34px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            z-index: 3;
        }
        .page-break { page-break-before: always; }
        .footer {
            margin-top: 12px;
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>
@php
    $formatMoney = fn ($n) => '$' . number_format((float) $n, 2);
    $formatNum = fn ($n) => number_format((float) $n, 0);
    $pct = function ($value, $max) {
        if ((float) $max <= 0) {
            return 0;
        }
        return max(0, min(100, ((float) $value / (float) $max) * 100));
    };

    $totalTx = max(1, (int) ($summary['total_transactions'] ?? 0));
    $totalGuests = max(1, (int) ($summary['total_guests'] ?? 0));
    $hourlyRevenueMax = (float) ($hourlyTrend->max('revenue') ?? 0);
    $hourlyTxnMax = (int) ($hourlyTrend->max('transactions') ?? 0);
    $weekdayRevenueMax = (float) ($weekdayTrend->max('revenue') ?? 0);
    $geoMax = max((int) ($topCities->max('transactions') ?? 0), 1);
    $stateMax = max((int) ($topStates->max('transactions') ?? 0), 1);
    $countryMax = max((int) ($topCountries->max('transactions') ?? 0), 1);
    $bandMax = max((int) ($orderValueBands->max('transactions') ?? 0), 1);
    $leadMax = max((int) ($leadTimeBands->max('transactions') ?? 0), 1);
    $clubRevMax = max((float) ($clubSnapshot->max('revenue') ?? 0), 1);
    $pkgRevMax = max((float) ($topPackages->max('revenue') ?? 0), 1);
    $dailyRevMax = max((float) ($dailyTrend->max('revenue') ?? 0), 1);
    $addonQtyMax = max((int) ($topAddons->max('qty') ?? 0), 1);
    $packageAddonComboMax = max((int) ($topPackageAddonCombinations->max('transactions') ?? 0), 1);
    $trendRevenue = (float) ($insights['trend']['revenue_delta_pct'] ?? 0);
    $trendTransactions = (float) ($insights['trend']['transactions_delta_pct'] ?? 0);
    $trendGuests = (float) ($insights['trend']['guests_delta_pct'] ?? 0);

    $directSharePct = ($sourceSnapshot['direct']['transactions'] / $totalTx) * 100;
    $addonAttachPct = (float) ($summary['addon_attach_rate'] ?? 0);
    $maleGuestPct = ($summary['total_men'] / $totalGuests) * 100;
    $femaleGuestPct = ($summary['total_women'] / $totalGuests) * 100;
    $zeroValueSharePct = (float) ($summary['zero_value_share'] ?? 0);
    $commissionRatePct = ($summary['total_revenue'] ?? 0) > 0
        ? (((float) ($summary['total_commission'] ?? 0) / (float) ($summary['total_revenue'] ?? 0)) * 100)
        : 0;
    $leadTimeTotalCount = (int) $leadTimeBands->sum('transactions');
    $genderCoveragePct = (float) ($summary['gender_coverage_pct'] ?? 0);

    $transportTx = (int) ($transportSnapshot['transport']['transactions'] ?? 0);
    $selfDriveTx = (int) ($transportSnapshot['self_drive']['transactions'] ?? 0);
    $transportSharePct = ($transportTx / $totalTx) * 100;

    $packageOnlyTx = (int) ($packageModeSnapshot['package_only']['transactions'] ?? 0);
    $packageWithAddonsTx = (int) ($packageModeSnapshot['package_with_addons']['transactions'] ?? 0);
    $packageOnlySharePct = ($packageOnlyTx / $totalTx) * 100;

    $donutSvg = function ($percent, $color) {
        $p = max(0, min(100, (float) $percent));
        $radius = 30;
        $circumference = 2 * pi() * $radius;
        $offset = $circumference * (1 - ($p / 100));
        return sprintf(
            '<svg width="86" height="86" viewBox="0 0 86 86" xmlns="http://www.w3.org/2000/svg"><circle cx="43" cy="43" r="30" fill="none" stroke="#e2e8f0" stroke-width="10"/><circle cx="43" cy="43" r="30" fill="none" stroke="%s" stroke-width="10" stroke-linecap="round" transform="rotate(-90 43 43)" stroke-dasharray="%.2f" stroke-dashoffset="%.2f"/><text x="43" y="47" text-anchor="middle" font-size="12" font-weight="700" fill="#0f172a">%s%%</text></svg>',
            $color,
            $circumference,
            $offset,
            number_format($p, 1)
        );
    };

    $donutFallback = function ($percent, $color) {
        $p = max(0, min(100, (float) $percent));
        return '<div class="donut-fallback" style="border-color:' . $color . ';"><div class="center">' . number_format($p, 1) . '%</div></div>';
    };

    $trendClass = function ($value) {
        if ($value > 0.01) {
            return 'trend-up';
        }
        if ($value < -0.01) {
            return 'trend-down';
        }
        return 'trend-flat';
    };

    $trendLabel = function ($value) {
        if ($value > 0.01) {
            return 'UP';
        }
        if ($value < -0.01) {
            return 'DOWN';
        }
        return 'FLAT';
    };
@endphp

    <div class="header">
        <h1 class="title">Automation Executive Intelligence Report</h1>
        <div class="subtitle">Graph-heavy management view across guests, spending, geography, time patterns, and club/package performance</div>
        <table class="meta">
            <tr>
                <td>
                    <div class="label">Window</div>
                    <div class="value">{{ $periodLabel }} | {{ $startAt->copy()->timezone($timezone)->format('M d, Y h:i A') }} to {{ $endAt->copy()->timezone($timezone)->format('M d, Y h:i A') }}</div>
                </td>
                <td>
                    <div class="label">Timezone</div>
                    <div class="value">{{ $timezone }}</div>
                </td>
                <td>
                    <div class="label">Venues</div>
                    <div class="value">{{ $selectedWebsites->count() > 0 ? $selectedWebsites->pluck('name')->implode(', ') : 'All Venues' }}</div>
                </td>
                <td>
                    <div class="label">Generated</div>
                    <div class="value">{{ $generatedAt->copy()->timezone($timezone)->format('M d, Y h:i A') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Executive Snapshot</div>
        <table class="cards">
            <tr>
                <td class="card"><div class="label">Revenue</div><div class="value">{{ $formatMoney($summary['total_revenue']) }}</div></td>
                <td class="card"><div class="label">Transactions</div><div class="value">{{ $formatNum($summary['total_transactions']) }}</div></td>
                <td class="card"><div class="label">Guests</div><div class="value">{{ $formatNum($summary['total_guests']) }}</div></td>
                <td class="card"><div class="label">Unique Patrons</div><div class="value">{{ $formatNum($summary['unique_patrons']) }}</div></td>
            </tr>
            <tr>
                <td class="card"><div class="label">Avg Order</div><div class="value">{{ $formatMoney($summary['avg_order_value']) }}</div></td>
                <td class="card"><div class="label">Highest Order</div><div class="value">{{ $formatMoney($summary['max_order_value']) }}</div></td>
                <td class="card"><div class="label">Add-on Attach Rate</div><div class="value">{{ number_format($summary['addon_attach_rate'] ?? 0, 1) }}%</div></td>
                <td class="card"><div class="label">Add-on Txns</div><div class="value">{{ $formatNum($summary['transactions_with_addons'] ?? 0) }}</div></td>
            </tr>
            <tr>
                <td class="card"><div class="label">Total Commission</div><div class="value">{{ $formatMoney($summary['total_commission']) }}</div></td>
                <td class="card"><div class="label">Net Revenue</div><div class="value">{{ $formatMoney($summary['net_revenue']) }}</div></td>
                <td class="card"><div class="label">Avg Lead Days</div><div class="value">{{ number_format($summary['avg_lead_days'] ?? 0, 1) }}</div></td>
                <td class="card"><div class="label">Avg Check-in Lag (min)</div><div class="value">{{ number_format($summary['avg_checkin_lag_minutes'] ?? 0, 0) }}</div></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">High-Impact Insights</div>
        <table class="insight-grid">
            <tr>
                <td class="insight">
                    <div class="k">Peak Volume Hour</div>
                    <div class="v">{{ $insights['peak_hour_transactions']['label'] ?? 'N/A' }} ({{ $formatNum($insights['peak_hour_transactions']['transactions'] ?? 0) }} txns)</div>
                </td>
                <td class="insight">
                    <div class="k">Peak Revenue Hour</div>
                    <div class="v">{{ $insights['peak_hour_revenue']['label'] ?? 'N/A' }} ({{ $formatMoney($insights['peak_hour_revenue']['revenue'] ?? 0) }})</div>
                </td>
                <td class="insight">
                    <div class="k">Largest Transaction</div>
                    <div class="v">
                        @if(!empty($insights['largest_transaction']))
                            {{ $formatMoney($insights['largest_transaction']['amount']) }} at {{ $insights['largest_transaction']['created_at'] }}
                        @else
                            N/A
                        @endif
                    </div>
                </td>
            </tr>
            <tr>
                <td class="insight"><div class="k">Male Guests</div><div class="v">{{ $formatNum($summary['total_men']) }}</div></td>
                <td class="insight"><div class="k">Female Guests</div><div class="v">{{ $formatNum($summary['total_women']) }}</div></td>
                <td class="insight"><div class="k">Unknown Gender Guests</div><div class="v">{{ $formatNum($summary['unknown_gender_guests']) }}</div></td>
            </tr>
            <tr>
                <td class="insight">
                    <div class="k">Revenue Change (Latest Day Vs Prior)</div>
                    <div class="v {{ $trendClass($trendRevenue) }}">{{ $trendLabel($trendRevenue) }} {{ number_format(abs($trendRevenue), 1) }}%</div>
                </td>
                <td class="insight">
                    <div class="k">Transaction Change</div>
                    <div class="v {{ $trendClass($trendTransactions) }}">{{ $trendLabel($trendTransactions) }} {{ number_format(abs($trendTransactions), 1) }}%</div>
                </td>
                <td class="insight">
                    <div class="k">Guest Change</div>
                    <div class="v {{ $trendClass($trendGuests) }}">{{ $trendLabel($trendGuests) }} {{ number_format(abs($trendGuests), 1) }}%</div>
                </td>
            </tr>
        </table>

        <table class="donut-grid">
            <tr>
                <td class="donut-card">
                    <div class="donut-title">Direct Source Share</div>
                    {!! $donutFallback($directSharePct, '#0ea5e9') !!}
                    <div class="donut-value">{{ number_format($directSharePct, 1) }}%</div>
                </td>
                <td class="donut-card">
                    <div class="donut-title">Add-on Attach Rate</div>
                    {!! $donutFallback($addonAttachPct, '#22c55e') !!}
                    <div class="donut-value">{{ number_format($addonAttachPct, 1) }}%</div>
                </td>
                <td class="donut-card">
                    <div class="donut-title">Male Guest Share</div>
                    {!! $donutFallback($maleGuestPct, '#f97316') !!}
                    <div class="donut-value">{{ number_format($maleGuestPct, 1) }}%</div>
                </td>
            </tr>
            <tr>
                <td class="donut-card">
                    <div class="donut-title">Female Guest Share</div>
                    {!! $donutFallback($femaleGuestPct, '#a855f7') !!}
                    <div class="donut-value">{{ number_format($femaleGuestPct, 1) }}%</div>
                </td>
                <td class="donut-card">
                    <div class="donut-title">Zero-Value Order Share</div>
                    {!! $donutFallback($zeroValueSharePct, '#14b8a6') !!}
                    <div class="donut-value">{{ number_format($zeroValueSharePct, 1) }}%</div>
                </td>
                <td class="donut-card">
                    <div class="donut-title">Commission Rate</div>
                    {!! $donutFallback($commissionRatePct, '#ef4444') !!}
                    <div class="donut-value">{{ number_format($commissionRatePct, 1) }}%</div>
                </td>
            </tr>
            <tr>
                <td class="donut-card">
                    <div class="donut-title">Transportation Share</div>
                    {!! $donutFallback($transportSharePct, '#0f766e') !!}
                    <div class="donut-value">{{ number_format($transportSharePct, 1) }}%</div>
                </td>
                <td class="donut-card">
                    <div class="donut-title">Package-Only Share</div>
                    {!! $donutFallback($packageOnlySharePct, '#6366f1') !!}
                    <div class="donut-value">{{ number_format($packageOnlySharePct, 1) }}%</div>
                </td>
                <td class="donut-card">
                    <div class="donut-title">Gender Data Coverage</div>
                    {!! $donutFallback($genderCoveragePct, '#7c3aed') !!}
                    <div class="donut-value">{{ number_format($genderCoveragePct, 1) }}%</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section page-break">
        <div class="section-title">Acquisition Mix And Time Distribution</div>
        <table class="two-col">
            <tr>
                <td class="panel">
                    <div class="graph-box">
                        <div class="graph-title">Source Mix (Transactions)</div>
                        @php
                            $sourceRows = [
                                ['name' => 'Direct', 'transactions' => $sourceSnapshot['direct']['transactions'], 'revenue' => $sourceSnapshot['direct']['revenue'], 'class' => 'c1'],
                                ['name' => 'Affiliate', 'transactions' => $sourceSnapshot['affiliate']['transactions'], 'revenue' => $sourceSnapshot['affiliate']['revenue'], 'class' => 'c2'],
                                ['name' => 'Entertainer', 'transactions' => $sourceSnapshot['entertainer']['transactions'], 'revenue' => $sourceSnapshot['entertainer']['revenue'], 'class' => 'c3'],
                            ];
                        @endphp
                        @foreach($sourceRows as $row)
                            <div class="bar-row">
                                <div class="bar-label">{{ $row['name'] }}: {{ $formatNum($row['transactions']) }} txns | {{ number_format(($row['transactions'] / $totalTx) * 100, 1) }}% | {{ $formatMoney($row['revenue']) }}</div>
                                <div class="bar-track"><div class="bar-fill {{ $row['class'] }}" style="width: {{ $pct($row['transactions'], $totalTx) }}%;"></div></div>
                            </div>
                        @endforeach
                    </div>
                </td>
                <td class="panel">
                    <div class="graph-box">
                        <div class="graph-title">Hourly Transactions ({{ $timezone }})</div>
                        @foreach($hourlyTrend as $hour)
                            @if(($hour['transactions'] ?? 0) > 0)
                                <div class="bar-row">
                                    <div class="bar-label">{{ $hour['label'] }} | {{ $formatNum($hour['transactions']) }} txns | {{ $formatMoney($hour['revenue']) }}</div>
                                    <div class="bar-track"><div class="bar-fill c4" style="width: {{ $pct($hour['transactions'], $hourlyTxnMax) }}%;"></div></div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </td>
            </tr>
        </table>

        <div class="graph-box" style="margin-top: 8px;">
            <div class="graph-title">Weekday Revenue Pattern</div>
            @foreach($weekdayTrend as $day)
                <div class="bar-row">
                    <div class="bar-label">{{ $day['weekday'] }} | {{ $formatNum($day['transactions']) }} txns | {{ $formatMoney($day['revenue']) }}</div>
                    <div class="bar-track"><div class="bar-fill c5" style="width: {{ $pct($day['revenue'], $weekdayRevenueMax) }}%;"></div></div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="section page-break">
        <div class="section-title">Guest Demographics And Spend Behavior</div>
        <table class="two-col">
            <tr>
                <td class="panel">
                    <div class="graph-box">
                        <div class="graph-title">Gender Data Coverage (Package Transactions Limited)</div>
                        <div class="bar-row">
                            <div class="bar-label">Transactions with explicit men/women data: {{ $formatNum($summary['gender_eligible_transactions'] ?? 0) }} / {{ $formatNum($summary['total_transactions']) }} ({{ number_format($summary['gender_coverage_pct'] ?? 0, 1) }}%)</div>
                            <div class="bar-track"><div class="bar-fill c4" style="width: {{ $pct($summary['gender_coverage_pct'] ?? 0, 100) }}%;"></div></div>
                        </div>
                        <div class="bar-row">
                            <div class="bar-label">Men count from covered rows: {{ $formatNum($summary['total_men']) }}</div>
                            <div class="bar-track"><div class="bar-fill c1" style="width: {{ $pct($summary['total_men'], max(1, $summary['total_men'] + $summary['total_women'])) }}%;"></div></div>
                        </div>
                        <div class="bar-row">
                            <div class="bar-label">Women count from covered rows: {{ $formatNum($summary['total_women']) }}</div>
                            <div class="bar-track"><div class="bar-fill c2" style="width: {{ $pct($summary['total_women'], max(1, $summary['total_men'] + $summary['total_women'])) }}%;"></div></div>
                        </div>
                        <div class="bar-label" style="margin-top: 6px; color:#475569;">
                            Note: Package-only flows often do not collect guest gender split, so this is a partial-data indicator.
                        </div>
                    </div>
                </td>
                <td class="panel">
                    <div class="graph-box">
                        <div class="graph-title">Order Value Bands</div>
                        @foreach($orderValueBands as $band)
                            <div class="bar-row">
                                <div class="bar-label">{{ $band['label'] }} | {{ $formatNum($band['transactions']) }} txns | {{ $formatMoney($band['revenue']) }}</div>
                                <div class="bar-track"><div class="bar-fill c6" style="width: {{ $pct($band['transactions'], $bandMax) }}%;"></div></div>
                            </div>
                        @endforeach
                    </div>
                </td>
            </tr>
        </table>

        <div class="graph-box" style="margin-top: 8px;">
            <div class="graph-title">Booking Lead Time (Creation To Use Date)</div>
            <div class="bar-label" style="margin-bottom: 6px; color:#475569;">
                Total bucketed transactions: {{ $formatNum($leadTimeTotalCount) }} / {{ $formatNum($summary['total_transactions']) }}
            </div>
            @foreach($leadTimeBands as $band)
                <div class="bar-row">
                    <div class="bar-label">{{ $band['label'] }} | {{ $formatNum($band['transactions']) }} transactions</div>
                    <div class="bar-track"><div class="bar-fill c4" style="width: {{ $pct($band['transactions'], $leadMax) }}%;"></div></div>
                </div>
            @endforeach
        </div>

        <table class="two-col" style="margin-top: 8px;">
            <tr>
                <td class="panel">
                    <div class="graph-box">
                        <div class="graph-title">Transportation Added Vs Self Drive</div>
                        <div class="bar-row">
                            <div class="bar-label">Transportation Added | {{ $formatNum($transportSnapshot['transport']['transactions'] ?? 0) }} txns | {{ $formatMoney($transportSnapshot['transport']['revenue'] ?? 0) }}</div>
                            <div class="bar-track"><div class="bar-fill c6" style="width: {{ $pct($transportSnapshot['transport']['transactions'] ?? 0, $totalTx) }}%;"></div></div>
                        </div>
                        <div class="bar-row">
                            <div class="bar-label">Self Drive / No Transport | {{ $formatNum($transportSnapshot['self_drive']['transactions'] ?? 0) }} txns | {{ $formatMoney($transportSnapshot['self_drive']['revenue'] ?? 0) }}</div>
                            <div class="bar-track"><div class="bar-fill c3" style="width: {{ $pct($transportSnapshot['self_drive']['transactions'] ?? 0, $totalTx) }}%;"></div></div>
                        </div>
                    </div>
                </td>
                <td class="panel">
                    <div class="graph-box">
                        <div class="graph-title">Package-Only Vs Package+Add-ons</div>
                        <div class="bar-row">
                            <div class="bar-label">Package Only | {{ $formatNum($packageModeSnapshot['package_only']['transactions'] ?? 0) }} txns | {{ $formatMoney($packageModeSnapshot['package_only']['revenue'] ?? 0) }}</div>
                            <div class="bar-track"><div class="bar-fill c5" style="width: {{ $pct($packageModeSnapshot['package_only']['transactions'] ?? 0, $totalTx) }}%;"></div></div>
                        </div>
                        <div class="bar-row">
                            <div class="bar-label">Package + Add-ons | {{ $formatNum($packageModeSnapshot['package_with_addons']['transactions'] ?? 0) }} txns | {{ $formatMoney($packageModeSnapshot['package_with_addons']['revenue'] ?? 0) }}</div>
                            <div class="bar-track"><div class="bar-fill c2" style="width: {{ $pct($packageModeSnapshot['package_with_addons']['transactions'] ?? 0, $totalTx) }}%;"></div></div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section page-break">
        <div class="section-title">Add-on Intelligence And Combination Graphs</div>
        <div class="graph-box">
            <div class="graph-title">Most Purchased Add-ons</div>
            @forelse($topAddons as $addon)
                <div class="bar-row">
                    <div class="bar-label">{{ $addon['addon_name'] }} | Qty {{ $formatNum($addon['qty']) }} | Txns {{ $formatNum($addon['transactions']) }} | {{ $formatMoney($addon['revenue']) }}</div>
                    <div class="bar-track"><div class="bar-fill c2" style="width: {{ $pct($addon['qty'], $addonQtyMax) }}%;"></div></div>
                </div>
            @empty
                <div>No add-on data found in this period.</div>
            @endforelse
        </div>

        <div class="graph-box" style="margin-top: 8px;">
            <div class="graph-title">Package + Add-on Combination Leaderboard</div>
            @forelse($topPackageAddonCombinations as $combo)
                <div class="bar-row">
                    <div class="bar-label">{{ $combo['label'] }} | {{ $formatNum($combo['transactions']) }} txns | {{ $formatMoney($combo['revenue']) }}</div>
                    <div class="bar-track"><div class="bar-fill c6" style="width: {{ $pct($combo['transactions'], $packageAddonComboMax) }}%;"></div></div>
                </div>
            @empty
                <div>No package/add-on combinations found in this period.</div>
            @endforelse
        </div>
    </div>

    <div class="section page-break">
        <div class="section-title">Guest Location Intelligence</div>
        <table class="two-col">
            <tr>
                <td class="panel">
                    <div class="graph-box">
                        <div class="graph-title">Top Cities By Transaction Count</div>
                        @forelse($topCities as $city)
                            <div class="bar-row">
                                <div class="bar-label">{{ $city['name'] }} | {{ $formatNum($city['transactions']) }} txns | {{ $formatMoney($city['revenue']) }}</div>
                                <div class="bar-track"><div class="bar-fill c1" style="width: {{ $pct($city['transactions'], $geoMax) }}%;"></div></div>
                            </div>
                        @empty
                            <div>No city data available.</div>
                        @endforelse
                    </div>
                </td>
                <td class="panel">
                    <div class="graph-box">
                        <div class="graph-title">Top States</div>
                        @forelse($topStates as $state)
                            <div class="bar-row">
                                <div class="bar-label">{{ $state['name'] }} | {{ $formatNum($state['transactions']) }} txns | {{ $formatMoney($state['revenue']) }}</div>
                                <div class="bar-track"><div class="bar-fill c2" style="width: {{ $pct($state['transactions'], $stateMax) }}%;"></div></div>
                            </div>
                        @empty
                            <div>No state data available.</div>
                        @endforelse
                    </div>
                </td>
            </tr>
        </table>

        <div class="graph-box" style="margin-top: 8px;">
            <div class="graph-title">Top Countries</div>
            @forelse($topCountries as $country)
                <div class="bar-row">
                    <div class="bar-label">{{ $country['name'] }} | {{ $formatNum($country['transactions']) }} txns | {{ $formatMoney($country['revenue']) }}</div>
                    <div class="bar-track"><div class="bar-fill c3" style="width: {{ $pct($country['transactions'], $countryMax) }}%;"></div></div>
                </div>
            @empty
                <div>No country data available.</div>
            @endforelse
        </div>
    </div>

    <div class="section page-break">
        <div class="section-title">Club, Package, And Daily Momentum</div>
        <table class="two-col">
            <tr>
                <td class="panel">
                    <div class="graph-box">
                        <div class="graph-title">Club Revenue Rank</div>
                        @foreach($clubSnapshot as $club)
                            <div class="bar-row">
                                <div class="bar-label">{{ $club['website_name'] }} | {{ $formatMoney($club['revenue']) }} | {{ $formatNum($club['transactions']) }} txns</div>
                                <div class="bar-track"><div class="bar-fill c5" style="width: {{ $pct($club['revenue'], $clubRevMax) }}%;"></div></div>
                            </div>
                        @endforeach
                    </div>
                </td>
                <td class="panel">
                    <div class="graph-box">
                        <div class="graph-title">Top Package Revenue</div>
                        @foreach($topPackages as $pkg)
                            <div class="bar-row">
                                <div class="bar-label">{{ $pkg['package_name'] }} | {{ $formatMoney($pkg['revenue']) }} | {{ $formatNum($pkg['transactions']) }} txns</div>
                                <div class="bar-track"><div class="bar-fill c6" style="width: {{ $pct($pkg['revenue'], $pkgRevMax) }}%;"></div></div>
                            </div>
                        @endforeach
                    </div>
                </td>
            </tr>
        </table>

        <div class="graph-box" style="margin-top: 8px;">
            <div class="graph-title">Daily Revenue Trend</div>
            @foreach($dailyTrend as $day)
                <div class="bar-row">
                    <div class="bar-label">{{ \Carbon\Carbon::parse($day['day'])->format('D, M d') }} | {{ $formatNum($day['transactions']) }} txns | {{ $formatMoney($day['revenue']) }}</div>
                    <div class="bar-track"><div class="bar-fill c4" style="width: {{ $pct($day['revenue'], $dailyRevMax) }}%;"></div></div>
                </div>
            @endforeach
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="right">Transactions</th>
                    <th class="right">Unique Patrons</th>
                    <th class="right">Guests</th>
                    <th class="right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailyTrend as $day)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($day['day'])->format('D, M d, Y') }}</td>
                        <td class="right">{{ $formatNum($day['transactions']) }}</td>
                        <td class="right">{{ $formatNum($day['unique_patrons']) }}</td>
                        <td class="right">{{ $formatNum($day['guests']) }}</td>
                        <td class="right">{{ $formatMoney($day['revenue']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            Automation executive report | Timezone-normalized analytics in {{ $timezone }} | Includes source, hourly, weekday, demographic, location, club, package, and daily graph sets.
        </div>
    </div>
</body>
</html>

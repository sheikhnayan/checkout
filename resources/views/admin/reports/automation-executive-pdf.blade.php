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
                <td class="card"><div class="label">Total Commission</div><div class="value">{{ $formatMoney($summary['total_commission']) }}</div></td>
                <td class="card"><div class="label">Net Revenue</div><div class="value">{{ $formatMoney($summary['net_revenue']) }}</div></td>
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
                        <div class="graph-title">Gender Guest Split</div>
                        <div class="bar-row">
                            <div class="bar-label">Men {{ $formatNum($summary['total_men']) }} ({{ number_format(($summary['total_men'] / $totalGuests) * 100, 1) }}%)</div>
                            <div class="bar-track"><div class="bar-fill c1" style="width: {{ $pct($summary['total_men'], $totalGuests) }}%;"></div></div>
                        </div>
                        <div class="bar-row">
                            <div class="bar-label">Women {{ $formatNum($summary['total_women']) }} ({{ number_format(($summary['total_women'] / $totalGuests) * 100, 1) }}%)</div>
                            <div class="bar-track"><div class="bar-fill c2" style="width: {{ $pct($summary['total_women'], $totalGuests) }}%;"></div></div>
                        </div>
                        <div class="bar-row">
                            <div class="bar-label">Unknown {{ $formatNum($summary['unknown_gender_guests']) }} ({{ number_format(($summary['unknown_gender_guests'] / $totalGuests) * 100, 1) }}%)</div>
                            <div class="bar-track"><div class="bar-fill c3" style="width: {{ $pct($summary['unknown_gender_guests'], $totalGuests) }}%;"></div></div>
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
            @foreach($leadTimeBands as $band)
                <div class="bar-row">
                    <div class="bar-label">{{ $band['label'] }} | {{ $formatNum($band['transactions']) }} transactions</div>
                    <div class="bar-track"><div class="bar-fill c4" style="width: {{ $pct($band['transactions'], $leadMax) }}%;"></div></div>
                </div>
            @endforeach
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

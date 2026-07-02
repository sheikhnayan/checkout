<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Automation Executive Report</title>
    <style>
        @page { margin: 14px 16px; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 0;
            color: #0f172a;
            font-size: 9px;
            line-height: 1.4;
            background: #f5f7fb;
        }
        .header {
            background: #0b0e1a;
            border: 1px solid #24304a;
            border-radius: 12px;
            padding: 10px 11px 9px;
            margin-bottom: 8px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.2);
        }
        .title {
            margin: 0;
            font-size: 19px;
            font-weight: 700;
            letter-spacing: 0.25px;
            color: #f8fafc;
        }
        .subtitle {
            margin: 2px 0 4px;
            color: #c9d4ef;
            font-size: 10px;
        }
        .header-note {
            display: inline-block;
            margin: 2px 0 0;
            padding: 2px 8px;
            border: 1px solid #7c3aed;
            background: #2a1c4f;
            color: #e9ddff;
            font-size: 8px;
            letter-spacing: 0.3px;
            border-radius: 999px;
        }
        .meta {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            margin-top: 5px;
        }
        .meta td {
            border: 1px solid #334155;
            background: #0f172a;
            padding: 4px 6px;
            vertical-align: top;
            border-radius: 8px;
        }
        .meta .label {
            color: #94a3b8;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .meta .value {
            color: #e2e8f0;
            font-size: 9px;
            font-weight: 700;
        }
        .section {
            margin-top: 7px;
            page-break-inside: auto;
            border: 1px solid #d6e1ef;
            border-radius: 12px;
            background: #ffffff;
            padding: 7px;
            box-shadow: 0 2px 7px rgba(15, 23, 42, 0.06);
        }
        .section-title {
            margin: 0 0 5px;
            padding: 4px 6px;
            background: #0f172a;
            color: #f8fafc;
            border-left: 4px solid #ffcc00;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            border-radius: 8px;
        }
        .cards {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
        }
        .card {
            border: 1px solid #d0dae8;
            border-top: 3px solid #7c3aed;
            background: #ffffff;
            padding: 4px 6px;
            width: 25%;
            vertical-align: top;
            border-radius: 10px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.8);
        }
        .card .label {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 2px;
            letter-spacing: 0.4px;
        }
        .card .value {
            font-size: 13px;
            color: #0b1324;
            font-weight: 700;
            line-height: 1.2;
        }
        .two-col {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px 0;
        }
        .panel {
            width: 50%;
            vertical-align: top;
        }
        .graph-box {
            border: 1px solid #d0dae8;
            background: #ffffff;
            padding: 5px;
            border-radius: 10px;
        }
        .graph-title {
            margin: 0 0 4px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1e293b;
            letter-spacing: 0.4px;
        }
        .bar-row {
            margin-bottom: 3px;
            border: 1px solid #e4ebf5;
            border-radius: 8px;
            background: #f8fafc;
            padding: 3px 4px 4px;
        }
        .bar-label {
            font-size: 9px;
            margin-bottom: 2px;
            color: #0f172a;
        }
        .bar-track {
            width: 100%;
            height: 7px;
            background: #e8edf5;
            overflow: hidden;
            border-radius: 999px;
        }
        .bar-fill { height: 8px; border-radius: 999px; }
        .c1 { background: #3b82f6; }
        .c2 { background: #10b981; }
        .c3 { background: #f59e0b; }
        .c4 { background: #7c3aed; }
        .c5 { background: #ef4444; }
        .c6 { background: #ffcc00; }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d2deee;
            background: #ffffff;
            margin-top: 5px;
            border-radius: 10px;
            overflow: hidden;
        }
        .data-table th {
            background: #edf2fb;
            border-bottom: 1px solid #d2deee;
            padding: 4px 5px;
            text-align: left;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #1f2937;
        }
        .data-table td {
            border-bottom: 1px solid #eef2f7;
            padding: 4px 5px;
            font-size: 9px;
            color: #111827;
        }
        .data-table th.right,
        .data-table td.right {
            text-align: right !important;
        }
        .data-table th:first-child,
        .data-table td:first-child {
            width: 30%;
            text-align: left;
        }
        .data-table th:nth-child(n+2),
        .data-table td:nth-child(n+2) {
            width: 17.5%;
        }
        .data-table tr:nth-child(even) td { background: #f7faff; }
        .right { text-align: right; }
        .insight-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
        }
        .insight {
            border: 1px solid #d0dae8;
            background: #ffffff;
            padding: 4px;
            vertical-align: top;
            width: 33.33%;
            border-radius: 10px;
        }
        .insight .k { font-size: 8px; text-transform: uppercase; color: #64748b; }
        .insight .v { font-size: 11px; color: #0f172a; font-weight: 700; margin-top: 2px; }
        .trend-up { color: #15803d; font-weight: 700; }
        .trend-down { color: #b91c1c; font-weight: 700; }
        .trend-flat { color: #334155; font-weight: 700; }
        .pie-box {
            border: 1px solid #d0dae8;
            padding: 5px;
            background: #ffffff;
            border-radius: 10px;
        }
        .pie-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1f2937;
            margin: 0 0 6px;
            letter-spacing: 0.4px;
        }
        .pie-svg-wrap {
            text-align: center;
            margin-bottom: 4px;
        }
        .pie-img {
            width: 150px;
            height: 150px;
            display: block;
            margin: 0 auto;
        }
        .pie-total {
            margin-top: 4px;
            text-align: center;
            font-size: 9px;
            color: #334155;
            font-weight: 700;
        }
        .circle-grid {
            border-collapse: collapse;
            margin: 0 auto;
        }
        .circle-grid td {
            width: 8px;
            height: 8px;
            padding: 0;
            border: 0;
            line-height: 0;
            font-size: 0;
        }
        .circle-center-label {
            text-align: center;
            font-size: 8px;
            color: #475569;
            margin-top: 3px;
        }
        .legend-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }
        .legend-table td {
            border-bottom: 1px solid #e7edf6;
            padding: 3px 2px;
            font-size: 8px;
            vertical-align: top;
        }
        .swatch {
            display: inline-block;
            width: 8px;
            height: 8px;
            margin-right: 4px;
            vertical-align: middle;
        }
        .page-break { page-break-before: auto; }
        .footer {
            margin-top: 7px;
            border-top: 1px solid #d0dae8;
            padding-top: 4px;
            font-size: 8px;
            color: #475569;
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
    $directSharePct = ($sourceSnapshot['direct']['transactions'] / $totalTx) * 100;
    $addonAttachPct = (float) ($summary['addon_attach_rate'] ?? 0);
    $zeroValueSharePct = (float) ($summary['zero_value_share'] ?? 0);
    $commissionRatePct = ($summary['total_revenue'] ?? 0) > 0
        ? (((float) ($summary['total_commission'] ?? 0) / (float) ($summary['total_revenue'] ?? 0)) * 100)
        : 0;
    $leadTimeTotalCount = (int) $leadTimeBands->sum('transactions');

    $transportTx = (int) ($transportSnapshot['transport']['transactions'] ?? 0);
    $transportSharePct = ($transportTx / $totalTx) * 100;

    $packageOnlyTx = (int) ($packageModeSnapshot['package_only']['transactions'] ?? 0);
    $packageOnlySharePct = ($packageOnlyTx / $totalTx) * 100;

    $colors = ['#0ea5e9', '#22c55e', '#f97316', '#a855f7', '#ef4444', '#14b8a6', '#6366f1', '#f59e0b'];
    $buildPieData = function ($rows, $nameKey, $valueKey, $limit = 6) use ($colors) {
        $base = collect($rows)
            ->map(function ($row) use ($nameKey, $valueKey) {
                return [
                    'name' => (string) ($row[$nameKey] ?? 'N/A'),
                    'value' => (float) ($row[$valueKey] ?? 0),
                ];
            })
            ->filter(fn ($row) => $row['value'] > 0)
            ->sortByDesc('value')
            ->values();

        $top = $base->take($limit)->values();
        $otherValue = max(0, (float) $base->skip($limit)->sum('value'));

        $segments = $top->map(function ($row, $idx) use ($colors) {
            return [
                'name' => $row['name'],
                'value' => $row['value'],
                'color' => $colors[$idx % count($colors)],
            ];
        })->values();

        if ($otherValue > 0) {
            $segments->push([
                'name' => 'Other',
                'value' => $otherValue,
                'color' => '#94a3b8',
            ]);
        }

        $total = max(0.0, (float) $segments->sum('value'));
        $segments = $segments->map(function ($row) use ($total) {
            $row['pct'] = $total > 0 ? (($row['value'] / $total) * 100) : 0;
            return $row;
        })->values();

        return ['segments' => $segments, 'total' => $total];
    };

    $renderCircleGrid = function ($segments, $totalLabel) {
        $n = 17;
        $mid = ($n - 1) / 2;
        $rOuter = 7.6;
        $rInner = 3.2;

        $ranges = [];
        $cursor = 0.0;
        foreach ($segments as $segment) {
            $pct = max(0.0, min(100.0, (float) ($segment['pct'] ?? 0)));
            $span = 360.0 * ($pct / 100.0);
            if ($span <= 0) {
                continue;
            }
            $ranges[] = [
                'start' => $cursor,
                'end' => $cursor + $span,
                'color' => (string) ($segment['color'] ?? '#94a3b8'),
            ];
            $cursor += $span;
        }

        $html = '<table class="circle-grid">';
        for ($y = 0; $y < $n; $y++) {
            $html .= '<tr>';
            for ($x = 0; $x < $n; $x++) {
                $dx = $x - $mid;
                $dy = $y - $mid;
                $dist = sqrt(($dx * $dx) + ($dy * $dy));

                if ($dist > $rOuter) {
                    $html .= '<td style="background:#ffffff;"></td>';
                    continue;
                }

                if ($dist < $rInner) {
                    $html .= '<td style="background:#ffffff;"></td>';
                    continue;
                }

                $angle = rad2deg(atan2($dy, $dx));
                $angle = $angle + 90.0;
                if ($angle < 0) {
                    $angle += 360.0;
                }

                $color = '#e2e8f0';
                foreach ($ranges as $range) {
                    if ($angle >= $range['start'] && $angle < $range['end']) {
                        $color = $range['color'];
                        break;
                    }
                }

                $html .= '<td style="background:' . e($color) . ';"></td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        $html .= '<div class="circle-center-label">Total: ' . e($totalLabel) . '</div>';

        return $html;
    };

    $renderPieImage = function ($segments, $totalLabel) use ($renderCircleGrid) {
        if (!function_exists('imagecreatetruecolor') || !function_exists('imagefilledarc')) {
            return $renderCircleGrid($segments, $totalLabel);
        }

        $size = 420;
        $center = (int) ($size / 2);
        $radius = 170;
        $diameter = $radius * 2;
        $innerRadius = 98;
        $innerDiameter = $innerRadius * 2;

        $im = imagecreatetruecolor($size, $size);
        if (!$im) {
            return $renderCircleGrid($segments, $totalLabel);
        }

        imagealphablending($im, true);
        imagesavealpha($im, true);
        $transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
        imagefill($im, 0, 0, $transparent);

        if (function_exists('imageantialias')) {
            @imageantialias($im, true);
        }

        $angleStart = -90.0;
        foreach ($segments as $segment) {
            $pct = max(0.0, min(100.0, (float) ($segment['pct'] ?? 0)));
            if ($pct <= 0.0) {
                continue;
            }

            $sweep = 360.0 * ($pct / 100.0);
            $angleEnd = $angleStart + $sweep;

            $hex = (string) ($segment['color'] ?? '#94a3b8');
            if (!preg_match('/^#?[0-9a-fA-F]{6}$/', $hex)) {
                $hex = '#94a3b8';
            }
            $hex = ltrim($hex, '#');
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            $col = imagecolorallocate($im, $r, $g, $b);

            imagefilledarc(
                $im,
                $center,
                $center,
                $diameter,
                $diameter,
                $angleStart,
                $angleEnd,
                $col,
                IMG_ARC_PIE
            );

            $angleStart = $angleEnd;
        }

        $white = imagecolorallocate($im, 255, 255, 255);
        imagefilledellipse($im, $center, $center, $innerDiameter, $innerDiameter, $white);

        ob_start();
        imagepng($im);
        $pngData = ob_get_clean();
        imagedestroy($im);

        if (!$pngData) {
            return $renderCircleGrid($segments, $totalLabel);
        }

        $base64 = base64_encode($pngData);
        return '<img class="pie-img" src="data:image/png;base64,' . $base64 . '" alt="Pie chart" />'
            . '<div class="pie-total">Total: ' . e($totalLabel) . '</div>';
    };

    $clubPie = $buildPieData($clubSnapshot, 'website_name', 'revenue', 6);
    $packagePieRows = $topPackages->map(function ($row) {
        return [
            'display_name' => (string) $row['package_name'] . ' (' . ((string) ($row['club_names'] ?? 'Unknown Club')) . ')',
            'revenue' => (float) ($row['revenue'] ?? 0),
        ];
    });
    $packagePie = $buildPieData($packagePieRows, 'display_name', 'revenue', 7);
@endphp

    <div class="header">
        <h1 class="title">CartVIP Business Intelligence | Executive Presentation Edition</h1>
        <div class="subtitle">Comprehensive business intelligence across revenue, customer acquisition, package performance, venue contribution, operational efficiency, demand trends, and conversion analytics.</div>
        {{-- <div class="header-note">CartVIP Business Intelligence | Executive Presentation Edition</div> --}}
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
        <div class="section-title">Executive Highlights</div>
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
        </table>
    </div>

    <div class="section">
        <div class="section-title">Circular Revenue Stream Split</div>
        <table class="two-col">
            <tr>
                <td class="panel">
                    <div class="pie-box">
                        <div class="pie-title">Club Revenue Share</div>
                        <div class="pie-svg-wrap">{!! $renderPieImage($clubPie['segments'], $formatMoney($clubPie['total'])) !!}</div>
                        <table class="legend-table">
                            @foreach($clubPie['segments'] as $seg)
                                <tr>
                                    <td><span class="swatch" style="background: {{ $seg['color'] }};"></span>{{ $seg['name'] }}</td>
                                    <td class="right">{{ $formatMoney($seg['value']) }}</td>
                                    <td class="right">{{ number_format($seg['pct'], 1) }}%</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </td>
                <td class="panel">
                    <div class="pie-box">
                        <div class="pie-title">Package Revenue Share</div>
                        <div class="pie-svg-wrap">{!! $renderPieImage($packagePie['segments'], $formatMoney($packagePie['total'])) !!}</div>
                        <table class="legend-table">
                            @foreach($packagePie['segments'] as $seg)
                                <tr>
                                    <td><span class="swatch" style="background: {{ $seg['color'] }};"></span>{{ $seg['name'] }}</td>
                                    <td class="right">{{ $formatMoney($seg['value']) }}</td>
                                    <td class="right">{{ number_format($seg['pct'], 1) }}%</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
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
                        <div class="graph-title">Day-wise Revenue Pattern</div>
            @foreach($weekdayTrend as $day)
                <div class="bar-row">
                    <div class="bar-label">{{ $day['weekday'] }} | {{ $formatNum($day['transactions']) }} txns | {{ $formatMoney($day['revenue']) }}</div>
                    <div class="bar-track"><div class="bar-fill c5" style="width: {{ $pct($day['revenue'], $weekdayRevenueMax) }}%;"></div></div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="section">
        <div class="section-title">Guest Behavior And Spend Architecture</div>
        <table class="two-col">
            <tr>
                <td class="panel">
                    <div class="graph-box">
                        <div class="graph-title">Package Mode Mix</div>
                        <div class="bar-row">
                            <div class="bar-label">Package Only | {{ $formatNum($packageModeSnapshot['package_only']['transactions'] ?? 0) }} txns | {{ $formatMoney($packageModeSnapshot['package_only']['revenue'] ?? 0) }}</div>
                            <div class="bar-track"><div class="bar-fill c5" style="width: {{ $pct($packageModeSnapshot['package_only']['transactions'] ?? 0, $totalTx) }}%;"></div></div>
                        </div>
                        <div class="bar-row">
                            <div class="bar-label">Package + Add-ons | {{ $formatNum($packageModeSnapshot['package_with_addons']['transactions'] ?? 0) }} txns | {{ $formatMoney($packageModeSnapshot['package_with_addons']['revenue'] ?? 0) }}</div>
                            <div class="bar-track"><div class="bar-fill c2" style="width: {{ $pct($packageModeSnapshot['package_with_addons']['transactions'] ?? 0, $totalTx) }}%;"></div></div>
                        </div>
                        <div class="bar-row">
                            <div class="bar-label">Zero-value orders | {{ $formatNum($summary['zero_value_transactions'] ?? 0) }} txns | {{ number_format($summary['zero_value_share'] ?? 0, 1) }}%</div>
                            <div class="bar-track"><div class="bar-fill c6" style="width: {{ $pct($summary['zero_value_share'] ?? 0, 100) }}%;"></div></div>
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
                        <div class="graph-title">Conversion-style Mix Metrics</div>
                        <div class="bar-row">
                            <div class="bar-label">Add-on Attach Rate | {{ number_format($summary['addon_attach_rate'] ?? 0, 1) }}%</div>
                            <div class="bar-track"><div class="bar-fill c2" style="width: {{ $pct($summary['addon_attach_rate'] ?? 0, 100) }}%;"></div></div>
                        </div>
                        <div class="bar-row">
                            <div class="bar-label">Direct Source Share | {{ number_format($directSharePct, 1) }}%</div>
                            <div class="bar-track"><div class="bar-fill c1" style="width: {{ $pct($directSharePct, 100) }}%;"></div></div>
                        </div>
                        <div class="bar-row">
                            <div class="bar-label">Commission Rate | {{ number_format($commissionRatePct, 1) }}%</div>
                            <div class="bar-track"><div class="bar-fill c4" style="width: {{ $pct($commissionRatePct, 100) }}%;"></div></div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Add-on Intelligence And Combination Patterns</div>
        <div class="graph-box" style="margin-bottom: 8px;">
            <div class="graph-title">Top Package Revenue</div>
            @foreach($topPackages as $pkg)
                <div class="bar-row">
                    <div class="bar-label">{{ $pkg['package_name'] }} | Club: {{ $pkg['club_names'] ?? 'Unknown Club' }} | {{ $formatMoney($pkg['revenue']) }} | {{ $formatNum($pkg['transactions']) }} txns</div>
                    <div class="bar-track"><div class="bar-fill c6" style="width: {{ $pct($pkg['revenue'], $pkgRevMax) }}%;"></div></div>
                </div>
            @endforeach
        </div>

        <div class="graph-box">
            <div class="graph-title">Most Purchased Add-ons</div>
            @forelse($topAddons as $addon)
                <div class="bar-row">
                    <div class="bar-label">{{ $addon['addon_name'] }} | Club: {{ $addon['club_names'] ?? 'Unknown Club' }} | Qty {{ $formatNum($addon['qty']) }} | Txns {{ $formatNum($addon['transactions']) }} | {{ $formatMoney($addon['revenue']) }}</div>
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
                    <div class="bar-label">{{ $combo['label'] }} | Club: {{ $combo['club_names'] ?? 'Unknown Club' }} | {{ $formatNum($combo['transactions']) }} txns | {{ $formatMoney($combo['revenue']) }}</div>
                    <div class="bar-track"><div class="bar-fill c6" style="width: {{ $pct($combo['transactions'], $packageAddonComboMax) }}%;"></div></div>
                </div>
            @empty
                <div>No package/add-on combinations found in this period.</div>
            @endforelse
        </div>
    </div>

    <div class="section">
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

    <div class="section">
        <div class="section-title">Club And Daily Momentum</div>
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
                        <div class="graph-title">Top Package + Add-on Combination Momentum</div>
                        @foreach($topPackageAddonCombinations->take(10) as $combo)
                            <div class="bar-row">
                                <div class="bar-label">{{ $combo['label'] }} | {{ $formatNum($combo['transactions']) }} txns</div>
                                <div class="bar-track"><div class="bar-fill c6" style="width: {{ $pct($combo['transactions'], $packageAddonComboMax) }}%;"></div></div>
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

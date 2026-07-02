<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $report->name }} Summary Report</title>
    <style>
        @page { margin: 22px 24px; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #111827;
            font-size: 12px;
            line-height: 1.35;
            margin: 0;
        }
        .report-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .report-title {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.2px;
            margin: 0;
            color: #0f172a;
        }
        .report-subtitle {
            margin: 4px 0 0;
            color: #475569;
            font-size: 12px;
        }
        .meta-grid {
            width: 100%;
            margin-top: 8px;
            border-collapse: separate;
            border-spacing: 6px;
        }
        .meta-cell {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 7px 8px;
            vertical-align: top;
        }
        .meta-label {
            color: #64748b;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .meta-value {
            color: #0f172a;
            font-size: 12px;
            font-weight: 600;
            word-break: break-word;
        }
        .section {
            margin-top: 14px;
            page-break-inside: avoid;
        }
        .section-title {
            margin: 0 0 8px;
            font-size: 14px;
            color: #0f172a;
            border-left: 4px solid #2563eb;
            padding-left: 8px;
            font-weight: 700;
        }
        .card-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
        }
        .kpi-card {
            border: 1px solid #dbeafe;
            background: #f8fbff;
            border-radius: 8px;
            padding: 8px 10px;
            vertical-align: top;
            width: 25%;
        }
        .kpi-label {
            color: #475569;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 3px;
        }
        .kpi-value {
            color: #0f172a;
            font-size: 16px;
            font-weight: 700;
        }
        .kpi-note {
            color: #64748b;
            font-size: 10px;
            margin-top: 2px;
        }
        .bar-row {
            margin-bottom: 8px;
        }
        .bar-head {
            width: 100%;
            border-collapse: collapse;
        }
        .bar-name {
            color: #0f172a;
            font-weight: 600;
            font-size: 11px;
            width: 70%;
            vertical-align: top;
        }
        .bar-val {
            color: #334155;
            text-align: right;
            font-size: 11px;
            width: 30%;
            vertical-align: top;
        }
        .bar-track {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
            margin-top: 4px;
        }
        .bar-fill {
            height: 8px;
            background: #2563eb;
            border-radius: 999px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
        }
        .data-table thead th {
            background: #f1f5f9;
            color: #0f172a;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 7px 8px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #1f2937;
            font-size: 11px;
            vertical-align: top;
            word-break: break-word;
        }
        .data-table tbody tr:nth-child(even) {
            background: #fcfdff;
        }
        .text-right { text-align: right; }
        .muted { color: #64748b; }
        .small { font-size: 10px; }
        .footer {
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 10px;
        }
    </style>
</head>
<body>
@php
    $filters = $filters ?? [];
    $generatedAt = $generatedAt ?? now();
    $type = $data['type'] ?? 'unknown';

    $formatLabel = function ($value) {
        return ucwords(str_replace('_', ' ', (string) $value));
    };

    $periodText = $formatLabel($filters['date_range'] ?? 'last_30_days');
    if (($filters['date_range'] ?? null) === 'custom') {
        $from = !empty($filters['start_date']) ? $filters['start_date'] : ($filters['custom_from'] ?? null);
        $to = !empty($filters['end_date']) ? $filters['end_date'] : ($filters['custom_to'] ?? null);
        if ($from && $to) {
            $periodText = $from . ' to ' . $to;
        }
    }

    $rows = [];
    if (!empty($data['raw_data']) && is_array($data['raw_data'])) {
        $rows = $data['raw_data'];
    } elseif (!empty($data['data']) && is_array($data['data']) && isset($data['data'][0]) && is_array($data['data'][0])) {
        $rows = $data['data'];
    }

    $metrics = [];
    if (!empty($data['metrics']) && is_array($data['metrics'])) {
        foreach ($data['metrics'] as $k => $v) {
            $isNumber = is_numeric($v);
            $formatted = $isNumber ? (abs((float) $v) >= 1000 ? number_format((float) $v, 2) : number_format((float) $v, 2)) : (string) $v;
            $metrics[] = [
                'label' => $formatLabel($k),
                'value' => $formatted,
                'raw' => $v,
                'is_numeric' => $isNumber,
            ];
        }
    }

    $numericColumns = [];
    foreach ($rows as $r) {
        if (!is_array($r)) {
            continue;
        }
        foreach ($r as $col => $val) {
            if (is_numeric($val)) {
                if (!isset($numericColumns[$col])) {
                    $numericColumns[$col] = [];
                }
                $numericColumns[$col][] = (float) $val;
            }
        }
    }

    $stats = [];
    foreach ($numericColumns as $col => $vals) {
        if (!count($vals)) {
            continue;
        }
        $stats[] = [
            'column' => $formatLabel($col),
            'sum' => array_sum($vals),
            'avg' => array_sum($vals) / count($vals),
            'min' => min($vals),
            'max' => max($vals),
            'count' => count($vals),
        ];
    }

    $chartLabels = [];
    $chartValues = [];
    if (!empty($data['data']['labels']) && is_array($data['data']['labels']) && !empty($data['data']['datasets'][0]['data'])) {
        $chartLabels = array_values($data['data']['labels']);
        $chartValues = array_map('floatval', array_values($data['data']['datasets'][0]['data']));
    } elseif (!empty($rows) && is_array($rows[0])) {
        $keys = array_keys($rows[0]);
        $labelKey = $keys[0] ?? null;
        $valueKey = null;
        foreach ($keys as $k) {
            if (isset($numericColumns[$k])) {
                $valueKey = $k;
                break;
            }
        }
        if ($labelKey && $valueKey) {
            foreach ($rows as $r) {
                if (!is_array($r) || !array_key_exists($labelKey, $r) || !array_key_exists($valueKey, $r)) {
                    continue;
                }
                $chartLabels[] = (string) $r[$labelKey];
                $chartValues[] = (float) $r[$valueKey];
            }
        }
    }

    if (count($chartLabels) > 10) {
        $chartLabels = array_slice($chartLabels, 0, 10);
        $chartValues = array_slice($chartValues, 0, 10);
    }

    $chartMax = !empty($chartValues) ? max($chartValues) : 0;

    $formatNumber = function ($v) {
        if (!is_numeric($v)) {
            return (string) $v;
        }
        return number_format((float) $v, 2);
    };
@endphp

    <div class="report-header">
        <h1 class="report-title">{{ $report->name }} - Summarized</h1>
        <p class="report-subtitle">{{ $report->description }}</p>

        <table class="meta-grid">
            <tr>
                <td class="meta-cell">
                    <div class="meta-label">Report Slug</div>
                    <div class="meta-value">{{ $report->slug }}</div>
                </td>
                <td class="meta-cell">
                    <div class="meta-label">Category</div>
                    <div class="meta-value">{{ $report->category }}</div>
                </td>
                <td class="meta-cell">
                    <div class="meta-label">Period</div>
                    <div class="meta-value">{{ $periodText }}</div>
                </td>
                <td class="meta-cell">
                    <div class="meta-label">Generated At</div>
                    <div class="meta-value">{{ $generatedAt->format('Y-m-d h:i A T') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Executive Summary</h2>
        <table class="card-table">
            <tr>
                <td class="kpi-card">
                    <div class="kpi-label">Report Type</div>
                    <div class="kpi-value" style="font-size: 14px;">{{ $formatLabel($type) }}</div>
                    <div class="kpi-note">Source payload type</div>
                </td>
                <td class="kpi-card">
                    <div class="kpi-label">Records</div>
                    <div class="kpi-value">{{ number_format(count($rows)) }}</div>
                    <div class="kpi-note">Rows in this export</div>
                </td>
                <td class="kpi-card">
                    <div class="kpi-label">Metrics</div>
                    <div class="kpi-value">{{ number_format(count($metrics)) }}</div>
                    <div class="kpi-note">Calculated KPIs</div>
                </td>
                <td class="kpi-card">
                    <div class="kpi-label">Numeric Fields</div>
                    <div class="kpi-value">{{ number_format(count($numericColumns)) }}</div>
                    <div class="kpi-note">Available for stats</div>
                </td>
            </tr>
        </table>
    </div>

    @if(!empty($metrics))
        <div class="section">
            <h2 class="section-title">KPI Snapshot</h2>
            <table class="card-table">
                <tr>
                    @foreach($metrics as $idx => $metric)
                        <td class="kpi-card" style="width: 25%;">
                            <div class="kpi-label">{{ $metric['label'] }}</div>
                            <div class="kpi-value">{{ $metric['value'] }}</div>
                        </td>
                        @if(($idx + 1) % 4 === 0)
                            </tr><tr>
                        @endif
                    @endforeach
                </tr>
            </table>
        </div>
    @endif

    @if(!empty($chartLabels) && !empty($chartValues))
        <div class="section">
            <h2 class="section-title">Visual Highlights</h2>
            @foreach($chartLabels as $i => $label)
                @php
                    $v = $chartValues[$i] ?? 0;
                    $pct = $chartMax > 0 ? max(0, min(100, ($v / $chartMax) * 100)) : 0;
                @endphp
                <div class="bar-row">
                    <table class="bar-head">
                        <tr>
                            <td class="bar-name">{{ $label }}</td>
                            <td class="bar-val">{{ $formatNumber($v) }}</td>
                        </tr>
                    </table>
                    <div class="bar-track">
                        <div class="bar-fill" style="width: {{ $pct }}%;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if(!empty($stats))
        <div class="section">
            <h2 class="section-title">Statistical Summary</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th class="text-right">Count</th>
                        <th class="text-right">Sum</th>
                        <th class="text-right">Avg</th>
                        <th class="text-right">Min</th>
                        <th class="text-right">Max</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats as $s)
                        <tr>
                            <td>{{ $s['column'] }}</td>
                            <td class="text-right">{{ number_format($s['count']) }}</td>
                            <td class="text-right">{{ $formatNumber($s['sum']) }}</td>
                            <td class="text-right">{{ $formatNumber($s['avg']) }}</td>
                            <td class="text-right">{{ $formatNumber($s['min']) }}</td>
                            <td class="text-right">{{ $formatNumber($s['max']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="section">
        <h2 class="section-title">Detailed Data</h2>
        @if(!empty($rows) && isset($rows[0]) && is_array($rows[0]))
            <table class="data-table">
                <thead>
                    <tr>
                        @foreach(array_keys($rows[0]) as $column)
                            <th>{{ $formatLabel($column) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr>
                            @foreach($row as $value)
                                <td class="{{ is_numeric($value) ? 'text-right' : '' }}">
                                    {{ is_numeric($value) ? $formatNumber($value) : $value }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif(!empty($data['metrics']) && is_array($data['metrics']))
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th class="text-right">Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['metrics'] as $k => $v)
                        <tr>
                            <td>{{ $formatLabel($k) }}</td>
                            <td class="text-right">{{ is_numeric($v) ? $formatNumber($v) : $v }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="muted">No detailed rows were returned for this report and filter combination.</p>
        @endif
    </div>

    <div class="footer">
        Generated by CartVIP Reports System | {{ $report->name }} | {{ $generatedAt->format('Y-m-d h:i A T') }}
    </div>
</body>
</html>

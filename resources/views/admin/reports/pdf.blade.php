<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $report->name }} Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            border-bottom: 2px solid #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #1f1400;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .content {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table thead {
            background-color: #f5f5f5;
        }
        table th {
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            font-weight: bold;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .metric-box {
            display: inline-block;
            background-color: #f5f5f5;
            padding: 15px;
            margin: 10px;
            border-radius: 5px;
            min-width: 200px;
        }
        .metric-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #1f1400;
            margin-top: 5px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $report->name }}</h1>
        <p>{{ $report->description }}</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="content">
        @if($data['type'] === 'table')
            @if(!empty($data['data']))
                <table>
                    <thead>
                        <tr>
                            @foreach(array_keys($data['data'][0]) as $column)
                                <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['data'] as $row)
                            <tr>
                                @foreach($row as $value)
                                    <td>
                                        @if(is_numeric($value) && strpos($value, '.') !== false)
                                            ${{ number_format($value, 2) }}
                                        @elseif(is_numeric($value))
                                            {{ number_format($value) }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No data available for this report.</p>
            @endif
        @elseif($data['type'] === 'metric')
            <div style="display: flex; flex-wrap: wrap;">
                @foreach($data['metrics'] as $label => $value)
                    <div class="metric-box">
                        <div class="metric-label">{{ ucfirst(str_replace('_', ' ', $label)) }}</div>
                        <div class="metric-value">
                            @if(is_numeric($value) && strpos($value, '.') !== false)
                                ${{ number_format($value, 2) }}
                            @elseif(is_numeric($value) && $value > 100)
                                {{ number_format($value) }}
                            @else
                                {{ $value }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif(in_array($data['type'], ['line_chart', 'bar_chart', 'pie_chart', 'stacked_bar']))
            <p style="text-align: center; color: #999;">
                Chart data ({{ count($data['raw_data'] ?? []) }} records)
            </p>
            @if(!empty($data['raw_data']))
                <table>
                    <thead>
                        <tr>
                            @foreach(array_keys($data['raw_data'][0]) as $column)
                                <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['raw_data'] as $row)
                            <tr>
                                @foreach($row as $value)
                                    <td>
                                        @if(is_numeric($value) && strpos($value, '.') !== false)
                                            {{ number_format($value, 2) }}
                                        @elseif(is_numeric($value))
                                            {{ number_format($value) }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif
    </div>

    <div class="footer">
        <p>This report was generated by CartVIP Reports System.</p>
        <p>Report Slug: {{ $report->slug }}</p>
    </div>
</body>
</html>

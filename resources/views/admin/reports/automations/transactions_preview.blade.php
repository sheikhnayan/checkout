<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions Report - {{ $periodLabel }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #0f172a;
            color: #f8fafc;
            font-family: system-ui, -apple-system, sans-serif;
            padding-bottom: 40px;
        }
        .header-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 10px;
            padding: 20px;
        }
        .stat-title {
            color: #94a3b8;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #38bdf8;
        }
        .badge-hostname {
            background: rgba(56, 189, 248, 0.15);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.3);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
        }
        .table-custom {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 10px;
            overflow: hidden;
        }
        .table-custom table {
            color: #f8fafc;
            margin-bottom: 0;
        }
        .table-custom th {
            background: #0f172a;
            color: #94a3b8;
            border-bottom: 1px solid #334155;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 16px;
        }
        .table-custom td {
            border-bottom: 1px solid #334155;
            padding: 12px 16px;
            vertical-align: middle;
            font-size: 0.9rem;
        }
        .table-custom tr:hover td {
            background: rgba(255, 255, 255, 0.03);
        }
        .search-box {
            background: #0f172a;
            border: 1px solid #334155;
            color: #fff;
            border-radius: 8px;
            padding: 8px 14px;
        }
        .search-box:focus {
            background: #0f172a;
            color: #fff;
            border-color: #38bdf8;
            box-shadow: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid px-4 py-4">
        <!-- Header -->
        <div class="header-card d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h2 class="h3 mb-0 text-white">Automated Transactions Report</h2>
                    <span class="badge bg-primary px-3 py-2" style="font-size: 0.8rem;">{{ $periodLabel }}</span>
                    @if(($hostnameFilter ?? 'all') === 'with_hostname')
                        <span class="badge badge-hostname"><i class="fas fa-user-tag me-1"></i> WITH Host Name Only</span>
                    @elseif(($hostnameFilter ?? 'all') === 'without_hostname')
                        <span class="badge bg-secondary px-2 py-1"><i class="fas fa-user-slash me-1"></i> WITHOUT Host Name Only</span>
                    @else
                        <span class="badge bg-dark border border-secondary px-2 py-1"><i class="fas fa-globe me-1"></i> All Transactions</span>
                    @endif
                </div>
                <p class="text-muted mb-0 small">
                    <i class="far fa-clock me-1"></i> Date Range: {{ $startAt->format('M d, Y') }} - {{ $endAt->format('M d, Y') }} (PST) &bull; Generated: {{ $generatedAt->format('M d, Y h:i A T') }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ request()->fullUrlWithQuery(['format' => 'csv']) }}" class="btn btn-outline-info btn-sm px-3">
                    <i class="fas fa-file-csv me-1"></i> Export CSV
                </a>
                <button type="button" class="btn btn-outline-light btn-sm px-3" onclick="window.print();">
                    <i class="fas fa-print me-1"></i> Print
                </button>
            </div>
        </div>

        <!-- Clubs Filter Banner if any -->
        @if(!empty($selectedWebsites) && count($selectedWebsites) > 0)
            <div class="mb-3 px-1 text-muted small">
                <strong>Clubs Included:</strong>
                @foreach($selectedWebsites as $club)
                    <span class="badge bg-dark border border-secondary me-1 text-light">{{ $club->name }}</span>
                @endforeach
            </div>
        @endif

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-title">Total Transactions</div>
                    <div class="stat-value">{{ number_format($transactions->count()) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-title">Total Revenue</div>
                    <div class="stat-value text-success">${{ number_format($transactions->sum('total'), 2) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-title">Transactions with Host Name</div>
                    <div class="stat-value text-info">{{ number_format($transactions->filter(fn($t) => !empty($t->host_name))->count()) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-title">Total Guests</div>
                    <div class="stat-value text-warning">{{ number_format($transactions->sum(fn($t) => max(1, (int)($t->package_number_of_guest ?? 1)))) }}</div>
                </div>
            </div>
        </div>

        <!-- Transactions Table Card -->
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 text-white"><i class="fas fa-list-alt me-2 text-primary"></i>Transactions List ({{ $transactions->count() }})</h5>
                <div style="width: 280px;">
                    <input type="text" id="tableSearchInput" class="form-control form-control-sm search-box" placeholder="Search transactions...">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle" id="transactionsTable">
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
                                <th>Gateway</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $tx)
                                <tr>
                                    <td class="fw-bold">#{{ $tx->id }}</td>
                                    <td class="text-nowrap small text-muted">
                                        {{ $tx->created_at ? $tx->created_at->copy()->timezone('America/Los_Angeles')->format('M d, Y h:i A') : 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ optional($tx->website)->name ?: ('Club #' . $tx->website_id) }}</span>
                                    </td>
                                    <td>
                                        @if(!empty($tx->host_name))
                                            <span class="badge-hostname fw-bold"><i class="fas fa-user-tag me-1"></i>{{ $tx->host_name }}</span>
                                        @else
                                            <span class="text-muted">&mdash;</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">
                                        {{ trim(($tx->package_first_name ?? '') . ' ' . ($tx->package_last_name ?? '')) ?: ($tx->package_name ?: 'N/A') }}
                                    </td>
                                    <td class="small">
                                        <div>{{ $tx->package_email ?: 'N/A' }}</div>
                                        <div class="text-muted">{{ $tx->package_phone ?: '' }}</div>
                                    </td>
                                    <td class="small">
                                        {{ $tx->package_table_label ?: (optional($tx->package)->name ?: 'N/A') }}
                                    </td>
                                    <td class="text-center">{{ $tx->package_number_of_guest ?? 1 }}</td>
                                    <td class="fw-bold text-success">${{ number_format((float)($tx->total ?? 0), 2) }}</td>
                                    <td><span class="badge bg-dark border border-secondary text-uppercase">{{ $tx->payment_gateway ?: 'N/A' }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                        No transactions found matching the selected criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tableSearchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#transactionsTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });
    </script>
</body>
</html>

@extends('admin.main')

@section('content')
<style>
    .activity-stat-card {
        background: var(--admin-surface, #1e293b);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .activity-stat-card:hover {
        border-color: rgba(124, 58, 237, 0.4);
        transform: translateY(-2px);
    }
    .activity-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .activity-stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #fff;
        line-height: 1.2;
    }
    .activity-stat-label {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 2px;
    }
    .activity-filter-card {
        background: var(--admin-surface, #1e293b);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .activity-filter-card .form-control,
    .activity-filter-card .form-select {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #fff;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    .activity-filter-card .form-control:focus,
    .activity-filter-card .form-select:focus {
        background: rgba(15, 23, 42, 0.9);
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.25);
        color: #fff;
    }
    .activity-filter-card label {
        font-size: 0.75rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.7);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.35rem;
    }
    .activity-table-card {
        background: var(--admin-surface, #1e293b);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        overflow: hidden;
    }
    .activity-table {
        width: 100%;
        margin-bottom: 0;
        color: rgba(255, 255, 255, 0.85);
        border-collapse: separate;
        border-spacing: 0;
    }
    .activity-table th {
        background: rgba(15, 23, 42, 0.8);
        padding: 0.85rem 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: rgba(255, 255, 255, 0.6);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .activity-table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        font-size: 0.875rem;
    }
    .activity-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }
    .action-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .action-badge-login { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
    .action-badge-logout { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
    .action-badge-failed_login { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
    .action-badge-create { background: rgba(59, 130, 246, 0.15); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3); }
    .action-badge-update { background: rgba(168, 85, 247, 0.15); color: #a855f7; border: 1px solid rgba(168, 85, 247, 0.3); }
    .action-badge-delete { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
    .action-badge-check_in { background: rgba(6, 182, 212, 0.15); color: #06b6d4; border: 1px solid rgba(6, 182, 212, 0.3); }
    .action-badge-default { background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3); }
    
    .role-badge {
        font-size: 0.7rem;
        padding: 0.15rem 0.45rem;
        border-radius: 4px;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.8);
    }
    .club-badge {
        font-size: 0.75rem;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        background: rgba(124, 58, 237, 0.15);
        color: #c084fc;
        border: 1px solid rgba(124, 58, 237, 0.25);
    }
    .ip-pill {
        font-family: monospace;
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.5);
        background: rgba(0, 0, 0, 0.3);
        padding: 0.15rem 0.4rem;
        border-radius: 4px;
    }
</style>

<div class="container-fluid px-4 py-4">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-white mb-1">Activity Logs</h1>
            <p class="text-muted mb-0" style="font-size:0.9rem;">
                Track user logins, logouts, system actions, and audit trails.
                @if(auth()->user()->isAdmin())
                    <span class="badge bg-purple-subtle text-purple ms-2">Global System View</span>
                @elseif(auth()->user()->isManager())
                    <span class="badge bg-info-subtle text-info ms-2">Allocated Clubs View</span>
                @else
                    <span class="badge bg-secondary-subtle text-light ms-2">Venue View</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="activity-stat-card">
                <div class="activity-stat-icon" style="background: rgba(124, 58, 237, 0.15); color: #a78bfa;">
                    <i class="fas fa-history"></i>
                </div>
                <div>
                    <div class="activity-stat-label">Logs Today</div>
                    <div class="activity-stat-value">{{ number_format($stats['total_today']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="activity-stat-card">
                <div class="activity-stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <div>
                    <div class="activity-stat-label">Logins Today</div>
                    <div class="activity-stat-value">{{ number_format($stats['logins_today']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="activity-stat-card">
                <div class="activity-stat-icon" style="background: rgba(6, 182, 212, 0.15); color: #38bdf8;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="activity-stat-label">Active Users Today</div>
                    <div class="activity-stat-value">{{ number_format($stats['active_users_today']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="activity-stat-card">
                <div class="activity-stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
                    <i class="fas fa-bolt"></i>
                </div>
                <div>
                    <div class="activity-stat-label">System Actions Today</div>
                    <div class="activity-stat-value">{{ number_format($stats['actions_today']) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="activity-filter-card">
        <form method="GET" action="{{ route('admin.activity-log.index') }}">
            <div class="row g-3">
                <div class="col-12 col-md-3">
                    <label for="search">Search User / Description / IP</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search name, email, IP..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label for="action">Action Type</label>
                    <select name="action" id="action" class="form-select">
                        <option value="">All Actions</option>
                        @foreach($actionTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('action') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @if(count($websites) > 1 || auth()->user()->isAdmin())
                <div class="col-6 col-md-2">
                    <label for="website_id">Club / Venue</label>
                    <select name="website_id" id="website_id" class="form-select">
                        <option value="">All Venues</option>
                        @foreach($websites as $site)
                            <option value="{{ $site->id }}" {{ request('website_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-6 col-md-2">
                    <label for="date_from">Date From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label for="date_to">Date To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-12 col-md-1 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100" style="background:#7c3aed;border:none;padding:0.5rem;"><i class="fas fa-filter"></i></button>
                    @if(request()->anyFilled(['search', 'action', 'website_id', 'date_from', 'date_to']))
                        <a href="{{ route('admin.activity-log.index') }}" class="btn btn-outline-secondary" title="Reset Filters" style="padding:0.5rem;"><i class="fas fa-undo"></i></a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Activity Logs Table --}}
    <div class="activity-table-card">
        <div class="table-responsive">
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Venue / Club</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activityLogs as $log)
                    <tr>
                        <td style="white-space: nowrap;">
                            <div class="fw-semibold text-white">{{ $log->created_at ? $log->created_at->format('M d, Y') : 'N/A' }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $log->created_at ? $log->created_at->format('h:i:s A') : '' }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-sm rounded-circle bg-purple-subtle text-purple d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:0.8rem;background:rgba(124,58,237,0.2);color:#c084fc;">
                                    {{ strtoupper(substr($log->user_name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-white" style="font-size:0.875rem;">{{ $log->user_name ?? 'System' }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">
                                        {{ $log->user_email ?? 'N/A' }}
                                        @if($log->user_type)
                                            <span class="role-badge ms-1">{{ ucfirst($log->user_type) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($log->website)
                                <span class="club-badge"><i class="fas fa-building me-1"></i>{{ $log->website->name }}</span>
                            @else
                                <span class="text-muted" style="font-size:0.8rem;">Global / System</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $act = strtolower($log->action);
                                $badgeClass = 'action-badge-default';
                                $icon = 'fas fa-info-circle';
                                if ($act === 'login') { $badgeClass = 'action-badge-login'; $icon = 'fas fa-sign-in-alt'; }
                                elseif ($act === 'logout') { $badgeClass = 'action-badge-logout'; $icon = 'fas fa-sign-out-alt'; }
                                elseif ($act === 'failed_login') { $badgeClass = 'action-badge-failed_login'; $icon = 'fas fa-exclamation-triangle'; }
                                elseif (str_contains($act, 'create') || str_contains($act, 'store')) { $badgeClass = 'action-badge-create'; $icon = 'fas fa-plus-circle'; }
                                elseif (str_contains($act, 'update') || str_contains($act, 'edit')) { $badgeClass = 'action-badge-update'; $icon = 'fas fa-edit'; }
                                elseif (str_contains($act, 'delete') || str_contains($act, 'archive')) { $badgeClass = 'action-badge-delete'; $icon = 'fas fa-trash'; }
                                elseif (str_contains($act, 'check_in') || str_contains($act, 'scan')) { $badgeClass = 'action-badge-check_in'; $icon = 'fas fa-qrcode'; }
                            @endphp
                            <span class="action-badge {{ $badgeClass }}">
                                <i class="{{ $icon }}"></i> {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td>
                            <div class="text-white" style="max-width: 450px; line-height: 1.4;">
                                {{ $log->description }}
                            </div>
                        </td>
                        <td>
                            @if($log->ip_address)
                                <span class="ip-pill">{{ $log->ip_address }}</span>
                            @else
                                <span class="text-muted" style="font-size:0.75rem;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-history fa-2x mb-3 d-block opacity-50"></i>
                            No activity logs found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activityLogs->hasPages())
        <div class="p-3 border-top border-secondary-subtle d-flex justify-content-end">
            {{ $activityLogs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

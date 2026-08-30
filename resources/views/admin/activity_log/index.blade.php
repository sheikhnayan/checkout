@extends('admin.main')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');

.activity-log-page,
.activity-log-page *,
.activity-table,
.activity-table th,
.activity-table td {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
}

.activity-stat-card {
    background: var(--admin-surface, #1e293b);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.2s ease, border-color 0.2s ease;
}
.activity-stat-card:hover {
    border-color: rgba(124, 58, 237, 0.5);
    transform: translateY(-2px);
}
.activity-stat-icon {
    width: 46px;
    height: 46px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.activity-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #f8fafc !important;
    line-height: 1.2;
    letter-spacing: -0.02em;
}
.activity-stat-label {
    font-size: 0.75rem;
    color: #94a3b8 !important;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 3px;
}
.activity-filter-card {
    background: var(--admin-surface, #1e293b);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
.activity-filter-card .form-control,
.activity-filter-card .form-select {
    background: rgba(15, 23, 42, 0.8) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: #f8fafc !important;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    font-size: 0.85rem;
    font-weight: 500;
}
.activity-filter-card .form-control::placeholder {
    color: #64748b !important;
}
.activity-filter-card .form-control:focus,
.activity-filter-card .form-select:focus {
    background: rgba(15, 23, 42, 0.95) !important;
    border-color: #a78bfa !important;
    box-shadow: 0 0 0 2px rgba(167, 139, 250, 0.25) !important;
    color: #ffffff !important;
}
.activity-filter-card label {
    font-size: 0.72rem;
    font-weight: 700;
    color: #94a3b8 !important;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 0.4rem;
    display: block;
}
.activity-table-card {
    background: var(--admin-surface, #1e293b);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    overflow: hidden;
}
.activity-table {
    width: 100%;
    margin-bottom: 0;
    color: #f8fafc !important;
    border-collapse: separate;
    border-spacing: 0;
}
.activity-table th {
    background: #0f172a;
    padding: 0.85rem 1.1rem;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: #94a3b8 !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.activity-table td {
    padding: 0.95rem 1.1rem;
    vertical-align: middle;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    font-size: 0.85rem;
    color: #f8fafc !important;
}
.activity-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.03);
}
.action-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.02em;
}
.action-badge-login { background: rgba(16, 185, 129, 0.18); color: #34d399 !important; border: 1px solid rgba(52, 211, 153, 0.35); }
.action-badge-logout { background: rgba(245, 158, 11, 0.18); color: #fbbf24 !important; border: 1px solid rgba(251, 191, 36, 0.35); }
.action-badge-failed_login { background: rgba(239, 68, 68, 0.18); color: #f87171 !important; border: 1px solid rgba(248, 113, 113, 0.35); }
.action-badge-create { background: rgba(59, 130, 246, 0.18); color: #60a5fa !important; border: 1px solid rgba(96, 165, 250, 0.35); }
.action-badge-update { background: rgba(168, 85, 247, 0.18); color: #c084fc !important; border: 1px solid rgba(192, 132, 252, 0.35); }
.action-badge-delete { background: rgba(239, 68, 68, 0.18); color: #f87171 !important; border: 1px solid rgba(248, 113, 113, 0.35); }
.action-badge-check_in { background: rgba(6, 182, 212, 0.18); color: #38bdf8 !important; border: 1px solid rgba(56, 189, 248, 0.35); }
.action-badge-default { background: rgba(148, 163, 184, 0.18); color: #cbd5e1 !important; border: 1px solid rgba(203, 213, 225, 0.3); }

.role-badge {
    font-size: 0.7rem;
    padding: 0.15rem 0.45rem;
    border-radius: 4px;
    font-weight: 500;
    background: rgba(255, 255, 255, 0.1);
    color: #cbd5e1 !important;
    border: 1px solid rgba(255, 255, 255, 0.18);
}
.club-badge {
    font-size: 0.78rem;
    padding: 0.22rem 0.55rem;
    border-radius: 6px;
    font-weight: 600;
    background: rgba(124, 58, 237, 0.18);
    color: #e9d5ff !important;
    border: 1px solid rgba(192, 132, 252, 0.35);
}
.ip-pill {
    font-family: 'JetBrains Mono', monospace !important;
    font-size: 0.75rem;
    font-weight: 500;
    color: #94a3b8 !important;
    background: rgba(15, 23, 42, 0.6);
    padding: 0.2rem 0.5rem;
    border-radius: 5px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}
</style>

<div class="container-fluid px-4 py-4">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-white mb-1">Activity Logs</h1>
            <p class="mb-0" style="font-size:0.95rem; color:#ffffff !important; opacity:0.9;">
                Track user logins, logouts, system actions, and audit trails.
                @if(auth()->user()->isAdmin())
                    <span class="badge bg-purple text-white ms-2" style="background:#7c3aed !important; font-size:0.8rem; font-weight:700;">Global System View</span>
                @elseif(auth()->user()->isManager())
                    <span class="badge bg-info text-dark ms-2" style="font-size:0.8rem; font-weight:700;">Allocated Clubs View</span>
                @else
                    <span class="badge bg-secondary text-white ms-2" style="font-size:0.8rem; font-weight:700;">Venue View</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="activity-stat-card">
                <div class="activity-stat-icon" style="background: rgba(124, 58, 237, 0.25); color: #c084fc;">
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
                <div class="activity-stat-icon" style="background: rgba(16, 185, 129, 0.25); color: #34d399;">
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
                <div class="activity-stat-icon" style="background: rgba(6, 182, 212, 0.25); color: #38bdf8;">
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
                <div class="activity-stat-icon" style="background: rgba(245, 158, 11, 0.25); color: #fbbf24;">
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
                    <button type="submit" class="btn btn-primary w-100" style="background:#7c3aed;border:none;padding:0.55rem;font-weight:700;" title="Filter"><i class="fas fa-filter me-1"></i></button>
                    @if(request()->anyFilled(['search', 'action', 'website_id', 'date_from', 'date_to']))
                        <a href="{{ route('admin.activity-log.index') }}" class="btn btn-outline-light" title="Reset Filters" style="padding:0.55rem;font-weight:700;"><i class="fas fa-undo"></i></a>
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
                            @php
                                $ptTime = $log->created_at ? $log->created_at->timezone('America/Los_Angeles') : null;
                            @endphp
                            <div style="font-size:0.85rem; font-weight:600; color:#f8fafc; letter-spacing:-0.01em;">{{ $ptTime ? $ptTime->format('M d, Y') : 'N/A' }}</div>
                            <div style="font-size:0.78rem; font-weight:400; color:#94a3b8; margin-top:2px; font-variant-numeric: tabular-nums;">
                                {{ $ptTime ? $ptTime->format('h:i:s A') : '' }}
                                <span style="font-size:0.65rem; font-weight:600; background:rgba(148,163,184,0.15); color:#cbd5e1; border:1px solid rgba(148,163,184,0.25); border-radius:4px; padding:1px 5px; margin-left:4px;">PT</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center fw-semibold" style="width:34px;height:34px;font-size:0.85rem;background:rgba(124,58,237,0.2);color:#e9d5ff;border:1px solid rgba(192,132,252,0.3);flex-shrink:0;">
                                    {{ strtoupper(substr($log->user_name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size:0.85rem; font-weight:600; color:#f8fafc; letter-spacing:-0.01em;">{{ $log->user_name ?? 'System' }}</div>
                                    <div style="font-size:0.78rem; font-weight:400; color:#94a3b8; margin-top:1px;">
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
                                <span style="font-size:0.8rem; color:#ffffff !important; opacity:0.7;">Global / System</span>
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
                            <div class="text-white fw-medium" style="max-width: 450px; line-height: 1.4; color:#ffffff !important;">
                                {{ $log->description }}
                            </div>
                        </td>
                        <td>
                            @if($log->ip_address)
                                <span class="ip-pill">{{ $log->ip_address }}</span>
                            @else
                                <span style="font-size:0.8rem; color:#ffffff !important; opacity:0.6;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5" style="color:#ffffff !important;">
                            <i class="fas fa-history fa-2x mb-3 d-block opacity-75" style="color:#c084fc;"></i>
                            <div class="fw-bold fs-5 text-white mb-1">No Activity Logs Found</div>
                            <div style="font-size:0.88rem; color:#ffffff !important; opacity:0.85;">No activity logs found matching your criteria. Logs will appear here as users log in, log out, or perform actions.</div>
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

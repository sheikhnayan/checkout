@extends('admin.main')

@section('content')
@php $authUser = auth()->user(); @endphp

@if($authUser->isAdmin())
{{-- ADMIN DASHBOARD (global stats) --}}
<style>
    .dashboard-stat-card {
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,0.08);
        background: linear-gradient(155deg, rgba(255,255,255,0.045), rgba(255,255,255,0.02));
        transition: transform .15s ease, border-color .15s ease;
        height: 100%;
    }
    .dashboard-stat-card:hover { transform: translateY(-2px); border-color: rgba(255,204,0,0.45); }
    .dashboard-stat-icon {
        width: 42px; height: 42px; border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center;
        background: rgba(255,204,0,0.12); color: #ffcc00;
    }
    .dashboard-main-card, .dashboard-side-card { border-radius: 14px; overflow: hidden; }
    .dashboard-main-card .table td, .dashboard-main-card .table th {
        padding-top: .8rem; padding-bottom: .8rem; vertical-align: middle;
    }
    .dashboard-quick-actions .list-group-item {
        background: transparent; border-color: rgba(255,255,255,0.08); color: #e8eaf6;
    }
    .dashboard-quick-actions .list-group-item:hover { background: rgba(255,204,0,0.08); }
</style>
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Dashboard</h4>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card dashboard-stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-0">{{ App\Models\Website::count() }}</h4>
                                <p class="text-muted mb-0">Total Clubs</p>
                            </div>
                            <div class="dashboard-stat-icon"><i class="fas fa-globe fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card dashboard-stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-0">{{ App\Models\Event::count() }}</h4>
                                <p class="text-muted mb-0">Total Events</p>
                            </div>
                            <div class="dashboard-stat-icon"><i class="fas fa-calendar-alt fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card dashboard-stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-0">{{ App\Models\User::where('user_type', 'website_user')->count() }}</h4>
                                <p class="text-muted mb-0">Website Users</p>
                            </div>
                            <div class="dashboard-stat-icon"><i class="fas fa-users fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card dashboard-stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-0">{{ App\Models\Transaction::count() }}</h4>
                                <p class="text-muted mb-0">Total Transactions</p>
                            </div>
                            <div class="dashboard-stat-icon"><i class="fas fa-credit-card fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-lg-8">
                <div class="card dashboard-main-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-chart-line me-2"></i>Recent Transactions</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr><th>ID</th><th>Customer</th><th>Event/Package</th><th>Amount</th><th>Status</th><th>Date</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions ?? [] as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>{{ $transaction->full_name }}<small class="text-muted d-block">{{ $transaction->email }}</small></td>
                                            <td>
                                                @if($transaction->event)
                                                    <span class="badge bg-primary">Event: {{ $transaction->event->name }}</span>
                                                @elseif($transaction->package)
                                                    <span class="badge bg-info">Package: {{ $transaction->package->name }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($transaction->total, 2) }}</td>
                                            <td>
                                                @if($transaction->status == 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif($transaction->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $transaction->status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->created_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') }} PT</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted">No transactions found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.transaction.index') }}" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i>View All Transactions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card dashboard-side-card dashboard-quick-actions">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('admin.website.create') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-plus-circle text-primary me-2"></i>Create New Club
                            </a>
                            <a href="{{ route('admin.event.index') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-calendar-plus text-success me-2"></i>Manage Events
                            </a>
                            <a href="{{ route('admin.website-users.create') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-plus text-info me-2"></i>Add Website User
                            </a>
                            <a href="{{ route('admin.transaction.index') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-list text-warning me-2"></i>View All Transactions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@else
{{-- WEBSITE USER / MANAGER WELCOME DASHBOARD --}}
<style>
    .welcome-hero {
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(255,204,0,0.10) 0%, rgba(255,255,255,0.03) 100%);
        border: 1px solid rgba(255,204,0,0.22);
        padding: 1.75rem 2rem;
        margin-bottom: 1.75rem;
    }
    .welcome-avatar {
        width: 58px; height: 58px; border-radius: 50%;
        background: rgba(255,204,0,0.16); border: 2px solid rgba(255,204,0,0.38);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; color: #ffcc00; flex-shrink: 0;
    }
    .welcome-badge {
        display: inline-block; padding: .22rem .7rem; border-radius: 20px;
        font-size: .73rem; font-weight: 600; letter-spacing: .03em;
    }
    .scope-stat-card {
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,0.08);
        background: linear-gradient(155deg, rgba(255,255,255,0.045), rgba(255,255,255,0.02));
        transition: transform .15s ease, border-color .15s ease;
    }
    .scope-stat-card:hover { transform: translateY(-2px); border-color: rgba(255,204,0,0.38); }
    .scope-stat-icon {
        width: 42px; height: 42px; border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center;
        background: rgba(255,204,0,0.12); color: #ffcc00;
    }
    .clubs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: .65rem;
    }
    .club-chip {
        background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09);
        border-radius: 10px; padding: .6rem .9rem;
        display: flex; align-items: center; gap: .55rem;
        font-size: .875rem; color: #e8eaf6; transition: border-color .15s;
    }
    .club-chip:hover { border-color: rgba(255,204,0,0.38); }
    .club-chip-icon {
        width: 30px; height: 30px; border-radius: 7px;
        background: rgba(255,204,0,0.10); color: #ffcc00;
        display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .quick-links-card .list-group-item {
        background: transparent; border-color: rgba(255,255,255,0.08); color: #e8eaf6;
    }
    .quick-links-card .list-group-item:hover { background: rgba(255,204,0,0.07); }
    .recent-tx-card .table td, .recent-tx-card .table th {
        padding-top: .72rem; padding-bottom: .72rem; vertical-align: middle;
    }
</style>
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="welcome-hero d-flex align-items-center gap-3 flex-wrap">
            <div class="welcome-avatar"><i class="fas fa-user"></i></div>
            <div class="flex-grow-1">
                <h4 class="mb-1 fw-semibold">Welcome back, {{ $authUser->name }}!</h4>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($authUser->isManager())
                        <span class="welcome-badge" style="background:rgba(102,126,234,0.18);color:#a78bfa;">
                            <i class="fas fa-user-tie me-1"></i>Manager
                        </span>
                    @else
                        <span class="welcome-badge" style="background:rgba(52,211,153,0.14);color:#6ee7b7;">
                            <i class="fas fa-id-badge me-1"></i>Club Admin
                        </span>
                    @endif
                    @if($authUser->websiteRole)
                        <span class="text-muted" style="font-size:.83rem;">
                            Role: <strong class="text-white">{{ $authUser->websiteRole->name }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <a href="{{ route('admin.profile.edit') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-user-edit me-1"></i>Edit Profile
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card scope-stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-0">{{ $allocatedWebsites->count() }}</h4>
                                <p class="text-muted mb-0">{{ $authUser->isManager() ? 'Allocated Clubs' : 'Your Club' }}</p>
                            </div>
                            <div class="scope-stat-icon"><i class="fas fa-building fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card scope-stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-0">{{ $scopedEventCount ?? 0 }}</h4>
                                <p class="text-muted mb-0">Events</p>
                            </div>
                            <div class="scope-stat-icon"><i class="fas fa-calendar-alt fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card scope-stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-0">{{ $scopedTransactionCount ?? 0 }}</h4>
                                <p class="text-muted mb-0">Transactions</p>
                            </div>
                            <div class="scope-stat-icon"><i class="fas fa-receipt fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card scope-stat-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div>
                            <p class="text-muted mb-1" style="font-size:.78rem;">SIGNED IN AS</p>
                            <p class="mb-0 fw-semibold text-truncate" style="max-width:165px;">{{ $authUser->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">

                @if($allocatedWebsites->isNotEmpty())
                <div class="card mb-4" style="border-radius:14px;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-building me-2 text-warning"></i>
                            @if($authUser->isManager())
                                Allocated Clubs &nbsp;<span class="badge bg-secondary">{{ $allocatedWebsites->count() }}</span>
                            @else
                                Your Club
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="clubs-grid">
                            @foreach($allocatedWebsites as $site)
                                <div class="club-chip">
                                    <div class="club-chip-icon"><i class="fas fa-store"></i></div>
                                    <span class="text-truncate">{{ $site->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                @if($authUser->hasRoutePermission('admin.transaction.index'))
                <div class="card recent-tx-card" style="border-radius:14px;overflow:hidden;">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2 text-warning"></i>Recent Transactions
                        </h5>
                        <a href="{{ route('admin.transaction.index') }}" class="btn btn-sm btn-outline-warning">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr><th>ID</th><th>Customer</th><th>Event / Package</th><th>Amount</th><th>Status</th><th>Date</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>{{ $transaction->full_name }}<small class="text-muted d-block">{{ $transaction->email }}</small></td>
                                            <td>
                                                @if($transaction->event)
                                                    <span class="badge bg-primary">{{ $transaction->event->name }}</span>
                                                @elseif($transaction->package)
                                                    <span class="badge bg-info">{{ $transaction->package->name }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($transaction->total, 2) }}</td>
                                            <td>
                                                @if($transaction->status == 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif($transaction->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $transaction->status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->created_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') }} PT</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-4">No transactions yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card quick-links-card" style="border-radius:14px;overflow:hidden;">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Quick Links</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @if($authUser->hasRoutePermission('admin.transaction.index'))
                        <a href="{{ route('admin.transaction.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-receipt text-warning me-2"></i>Transactions
                        </a>
                        @endif
                        @if($authUser->hasRoutePermission('admin.event.index'))
                        <a href="{{ route('admin.event.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar-alt text-success me-2"></i>Events
                        </a>
                        @endif
                        @if($authUser->hasRoutePermission('admin.package.index'))
                        <a href="{{ route('admin.package.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-box text-info me-2"></i>Packages
                        </a>
                        @endif
                        @if($authUser->hasRoutePermission('admin.addon.index'))
                        <a href="{{ route('admin.addon.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-puzzle-piece text-primary me-2"></i>Add-ons
                        </a>
                        @endif
                        @if($authUser->hasRoutePermission('admin.custom-invoice.index'))
                        <a href="{{ route('admin.custom-invoice.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-invoice text-secondary me-2"></i>Custom Invoices
                        </a>
                        @endif
                        @if($authUser->hasRoutePermission('admin.feed-post.index'))
                        <a href="{{ route('admin.feed-post.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-photo-video me-2"></i>Feed Posts
                        </a>
                        @endif
                        @if($authUser->hasRoutePermission('admin.feed-model.index'))
                        <a href="{{ route('admin.feed-model.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-circle me-2"></i>Feed Models
                        </a>
                        @endif
                        @if($authUser->hasRoutePermission('admin.jobs.index'))
                        <a href="{{ route('admin.jobs.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-briefcase me-2"></i>Job Marketplace
                        </a>
                        @endif
                        <a href="{{ route('admin.profile.edit') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-cog me-2"></i>My Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

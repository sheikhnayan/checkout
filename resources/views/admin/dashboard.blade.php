@extends('admin.main')

@section('content')
<style>
    .dashboard-title {
        margin-bottom: 1.25rem;
    }

    .dashboard-title .page-title-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255, 204, 0, 0.14);
        border: 1px solid rgba(255, 204, 0, 0.35);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .dashboard-stat-card {
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: linear-gradient(155deg, rgba(255,255,255,0.045), rgba(255,255,255,0.02));
        transition: transform .15s ease, border-color .15s ease;
        height: 100%;
    }

    .dashboard-stat-card:hover {
        transform: translateY(-2px);
        border-color: rgba(255, 204, 0, 0.45);
    }

    .dashboard-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 204, 0, 0.12);
        color: #ffcc00;
    }

    .dashboard-main-card,
    .dashboard-side-card {
        border-radius: 14px;
        overflow: hidden;
    }

    .dashboard-main-card .table td,
    .dashboard-main-card .table th {
        padding-top: .8rem;
        padding-bottom: .8rem;
        vertical-align: middle;
    }

    .dashboard-quick-actions .list-group-item {
        background: transparent;
        border-color: rgba(255,255,255,0.08);
        color: #e8eaf6;
    }

    .dashboard-quick-actions .list-group-item:hover {
        background: rgba(255, 204, 0, 0.08);
    }
</style>
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl-12 mb-6 order-0">
                <div class="app-main__inner">
                    <div class="app-page-title mt-4 dashboard-title">
                        <div class="page-title-wrapper">
                            <div class="page-title-heading">
                                <div class="page-title-icon">
                                    <i class="fas fa-tachometer-alt icon-gradient bg-arielle-smile"></i>
                                </div>
                                <div>
                                    <span class="text-capitalize">Dashboard</span>
                                    <div class="page-title-subheading">
                                        Welcome back, {{ auth()->user()->name }}!
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="card dashboard-stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title mb-0">{{ App\Models\Website::count() }}</h4>
                                            <p class="text-muted mb-0">Total Websites</p>
                                        </div>
                                        <div class="dashboard-stat-icon">
                                            <i class="fas fa-globe fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card dashboard-stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title mb-0">{{ App\Models\Event::count() }}</h4>
                                            <p class="text-muted mb-0">Total Events</p>
                                        </div>
                                        <div class="dashboard-stat-icon">
                                            <i class="fas fa-calendar-alt fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card dashboard-stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title mb-0">{{ App\Models\User::where('user_type', 'website_user')->count() }}</h4>
                                            <p class="text-muted mb-0">Website Users</p>
                                        </div>
                                        <div class="dashboard-stat-icon">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card dashboard-stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title mb-0">{{ App\Models\Transaction::count() }}</h4>
                                            <p class="text-muted mb-0">Total Transactions</p>
                                        </div>
                                        <div class="dashboard-stat-icon">
                                            <i class="fas fa-credit-card fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-8">
                            <div class="card dashboard-main-card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Recent Transactions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Customer</th>
                                                    <th>Event/Package</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($recentTransactions ?? [] as $transaction)
                                                    <tr>
                                                        <td>{{ $transaction->id }}</td>
                                                        <td>
                                                            {{ $transaction->full_name }}
                                                            <small class="text-muted d-block">{{ $transaction->email }}</small>
                                                        </td>
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
                                                        <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">No transactions found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="text-center mt-3">
                                        <a href="{{ route('admin.transaction.index') }}" class="btn btn-primary">
                                            <i class="fas fa-eye me-1"></i>
                                            View All Transactions
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card dashboard-side-card dashboard-quick-actions">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Quick Actions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <a href="{{ route('admin.website.create') }}" class="list-group-item list-group-item-action">
                                            <i class="fas fa-plus-circle text-primary me-2"></i>
                                            Create New Website
                                        </a>
                                        <a href="{{ route('admin.event.index') }}" class="list-group-item list-group-item-action">
                                            <i class="fas fa-calendar-plus text-success me-2"></i>
                                            Manage Events
                                        </a>
                                        <a href="{{ route('admin.website-users.create') }}" class="list-group-item list-group-item-action">
                                            <i class="fas fa-user-plus text-info me-2"></i>
                                            Add Website User
                                        </a>
                                        <a href="{{ route('admin.transaction.index') }}" class="list-group-item list-group-item-action">
                                            <i class="fas fa-list text-warning me-2"></i>
                                            View All Transactions
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3 dashboard-side-card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user-circle me-2"></i>
                                        Account Info
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ auth()->user()->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ auth()->user()->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Role:</strong></td>
                                            <td>
                                                @if(auth()->user()->isAdmin())
                                                    <span class="badge bg-danger">Administrator</span>
                                                @else
                                                    <span class="badge bg-info">Website User</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if(auth()->user()->website)
                                        <tr>
                                            <td><strong>Website:</strong></td>
                                            <td>{{ auth()->user()->website->name }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
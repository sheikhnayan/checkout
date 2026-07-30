@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card p-4">
                    <h6>Wallet Balance</h6>
                    <h3 class="mb-0">${{ number_format($affiliate->wallet_balance, 2) }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4">
                    <h6>Total Commission Earned</h6>
                    <h3 class="mb-0">${{ number_format($commissions, 2) }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4">
                    <h6>Selected Packages</h6>
                    <h3 class="mb-0">{{ $affiliate->affiliate_packages_count }}</h3>
                </div>
            </div>
        </div>

        <div class="card p-4 mt-4">
            <h5>Welcome, {{ $affiliate->display_name ?: $affiliate->user->name }}</h5>
            <p class="mb-1">Public Page: <a href="{{ route('affiliate.public', $affiliate->slug) }}" target="_blank">{{ route('affiliate.public', $affiliate->slug) }}</a></p>
            <p class="mb-0 text-muted">Use the left menu to customize your page, choose packages, and monitor your wallet.</p>
        </div>
    </div>
</div>
@endsection

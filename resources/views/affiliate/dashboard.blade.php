@extends('admin.main')

@section('title', 'Promoter Dashboard')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold py-1 mb-1 text-white">Welcome back, {{ $affiliate->display_name ?: $affiliate->user->name }}</h4>
                <p class="text-muted mb-0">Here is your promoter overview and referral share links.</p>
            </div>
        </div>

        @if(!$affiliate->isSubAffiliate() || $affiliate->hasSubPermission('show_sales_stats'))
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card p-4 border border-secondary bg-dark text-white">
                    <span class="text-muted fs-7 text-uppercase fw-bold">Wallet Balance</span>
                    <h3 class="mb-0 mt-2 text-white">${{ number_format($affiliate->wallet_balance, 2) }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 border border-secondary bg-dark text-white">
                    <span class="text-muted fs-7 text-uppercase fw-bold">Total Commission Earned</span>
                    <h3 class="mb-0 mt-2 text-white">${{ number_format($commissions, 2) }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 border border-secondary bg-dark text-white">
                    <span class="text-muted fs-7 text-uppercase fw-bold">Active Packages</span>
                    <h3 class="mb-0 mt-2 text-white">{{ $affiliate->affiliate_packages_count }}</h3>
                </div>
            </div>
        </div>
        @endif

        @if(!$affiliate->isSubAffiliate() || $affiliate->hasSubPermission('show_qr_code'))
        @php
            $publicUrl = route('affiliate.public', $affiliate->slug);
            $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($publicUrl);
        @endphp
        <div class="card p-4 mb-4 border border-secondary bg-dark text-white">
            <div class="row align-items-center g-4">
                <div class="col-md-8">
                    <h5 class="fw-bold text-white mb-2"><i class="bx bx-qr-scan text-primary me-2"></i> Your Shareable Page & QR Code</h5>
                    <p class="text-muted fs-7 mb-3">Share your unique page link or print your custom QR code on flyers and social posts to start receiving bookings.</p>
                    
                    <div class="input-group mb-3 max-w-lg">
                        <input type="text" id="affiliateShareUrl" class="form-control bg-dark text-white border-secondary" value="{{ $publicUrl }}" readonly>
                        <button class="btn btn-primary" type="button" onclick="copyShareUrl()">
                            <i class="bx bx-copy me-1"></i> Copy Link
                        </button>
                        <a href="{{ $publicUrl }}" target="_blank" class="btn btn-outline-secondary">
                            <i class="bx bx-external-link"></i> Visit
                        </a>
                    </div>
                    <span id="copySuccessMsg" class="text-success fs-7 d-none"><i class="bx bx-check me-1"></i> Link copied to clipboard!</span>
                </div>
                <div class="col-md-4 text-center">
                    <div class="p-3 bg-white d-inline-block rounded shadow">
                        <img src="{{ $qrApiUrl }}" alt="Promoter QR Code" width="160" height="160" class="img-fluid">
                    </div>
                    <div class="mt-2">
                        <a href="{{ $qrApiUrl }}" download="promoter-qr-code.png" target="_blank" class="btn btn-sm btn-outline-light mt-1">
                            <i class="bx bx-download me-1"></i> Download QR Code
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

<script>
function copyShareUrl() {
    const input = document.getElementById('affiliateShareUrl');
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value);
    
    const msg = document.getElementById('copySuccessMsg');
    msg.classList.remove('d-none');
    setTimeout(() => {
        msg.classList.add('d-none');
    }, 3000);
}
</script>
@endsection

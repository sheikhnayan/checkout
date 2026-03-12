@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card p-4 mb-4">
            <h4>{{ $affiliate->display_name ?: $affiliate->user->name }}</h4>
            <p class="mb-1"><strong>Email:</strong> {{ $affiliate->user->email }}</p>
            <p class="mb-1"><strong>Status:</strong> {{ ucfirst($affiliate->status) }}</p>
            <p class="mb-3"><strong>Public Page:</strong> <a href="{{ route('affiliate.public', $affiliate->slug) }}" target="_blank">{{ route('affiliate.public', $affiliate->slug) }}</a></p>

            @if($affiliate->status !== 'approved')
                <form method="POST" action="{{ route('admin.affiliate.approve', $affiliate->id) }}" class="d-flex gap-2 align-items-end mb-3">
                    @csrf
                    <div>
                        <label class="form-label">Default Commission %</label>
                        <input type="number" min="0" max="100" step="0.01" name="default_commission_percentage" class="form-control" value="{{ old('default_commission_percentage', $affiliate->default_commission_percentage) }}" required>
                    </div>
                    <button type="submit" class="btn btn-success">Approve</button>
                </form>
            @endif

            @if($affiliate->status !== 'rejected')
                <form method="POST" action="{{ route('admin.affiliate.reject', $affiliate->id) }}" class="mb-2">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Rejection Reason (optional)</label>
                        <textarea name="rejection_reason" rows="2" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </form>
            @endif
        </div>

        <div class="card p-4">
            <h5 class="mb-3">Assign Packages + Commission</h5>
            <form method="POST" action="{{ route('admin.affiliate.packages.update', $affiliate->id) }}">
                @csrf
                @foreach($websites as $website)
                    <div class="border rounded p-3 mb-3">
                        <h6 class="mb-3">{{ $website->name }}</h6>
                        @if($website->packages->count())
                            <div class="row g-3">
                                @foreach($website->packages as $package)
                                    @php
                                        $mapping = $affiliate->affiliatePackages->firstWhere('package_id', $package->id);
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center justify-content-between border rounded p-2">
                                            <div>
                                                <label class="form-check-label">
                                                    <input class="form-check-input me-2" type="checkbox" name="package_ids[]" value="{{ $package->id }}" {{ $mapping ? 'checked' : '' }}>
                                                    {{ $package->name }}
                                                </label>
                                                <div class="text-muted" style="font-size:12px;">${{ number_format($package->price, 2) }}</div>
                                            </div>
                                            <div style="max-width: 110px;">
                                                <input type="number" class="form-control form-control-sm" min="0" max="100" step="0.01" name="commissions[{{ $package->id }}]" value="{{ $mapping ? $mapping->commission_percentage : $affiliate->default_commission_percentage }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No active packages.</p>
                        @endif
                    </div>
                @endforeach

                <button type="submit" class="btn btn-primary">Save Package Commissions</button>
            </form>
        </div>
    </div>
</div>
@endsection

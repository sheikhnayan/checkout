@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card p-4">
            <h4 class="mb-3">Select Packages From Your Assigned Clubs</h4>
            <form method="POST" action="{{ route('affiliate.portal.packages.save') }}">
                @csrf
                @if($websites->isEmpty())
                    <div class="alert alert-warning">
                        No clubs are assigned to your affiliate account yet. Please contact super admin.
                    </div>
                @endif
                @foreach($websites as $website)
                    <div class="border rounded p-3 mb-3">
                        <h6>{{ $website->name }}</h6>
                        <div class="row g-2">
                            @forelse($website->packages as $package)
                                <div class="col-md-6">
                                    <label class="d-flex align-items-center border rounded p-2">
                                        <input type="checkbox" class="form-check-input me-2" name="package_ids[]" value="{{ $package->id }}" {{ in_array($package->id, $selected) ? 'checked' : '' }}>
                                        <div>
                                            <div>{{ $package->name }}</div>
                                            <small class="text-muted">${{ number_format($package->price, 2) }}</small>
                                        </div>
                                    </label>
                                </div>
                            @empty
                                <div class="col-12"><small class="text-muted">No active packages in this website.</small></div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
                <button type="submit" class="btn btn-primary">Save Selection</button>
            </form>
        </div>
    </div>
</div>
@endsection

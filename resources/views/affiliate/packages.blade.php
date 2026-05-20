@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <style>
            :root {
                --accent: #a774ff;
                --accent-dark: #7c3aed;
                --border-light: rgba(255, 255, 255, 0.12);
            }
            .card {
                border: 1px solid var(--border-light);
                background: rgba(255, 255, 255, 0.03);
            }
            .website-section {
                border: 1px solid var(--border-light);
                border-radius: 12px;
                padding: 20px;
                margin-bottom: 20px;
                background: rgba(255, 255, 255, 0.02);
            }
            .website-section h6 {
                font-weight: 800;
                color: #f8f9ff;
                margin-bottom: 16px;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .package-checkbox {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 14px;
                border: 1px solid var(--border-light);
                border-radius: 10px;
                cursor: pointer;
                transition: all 0.2s ease;
                background: rgba(255, 255, 255, 0.02);
            }
            .package-checkbox:hover {
                border-color: rgba(255, 255, 255, 0.28);
                background: rgba(255, 255, 255, 0.04);
            }
            .package-checkbox input[type="checkbox"] {
                width: 18px;
                height: 18px;
                cursor: pointer;
                flex-shrink: 0;
                accent-color: var(--accent);
            }
            .package-checkbox input[type="checkbox"]:checked {
                border-color: var(--accent);
            }
            .package-info {
                flex: 1;
            }
            .package-name {
                font-weight: 600;
                font-size: 14px;
                margin-bottom: 4px;
                color: #f8f9ff;
            }
            .package-price {
                font-size: 12px;
                color: var(--accent);
                font-weight: 700;
            }
            .btn-primary {
                background: linear-gradient(135deg, #a774ff 0%, #7c3aed 100%);
                border: none;
                padding: 11px 28px;
                border-radius: 8px;
                font-weight: 700;
                box-shadow: 0 4px 12px rgba(167, 116, 255, 0.3);
                transition: all 0.2s ease;
            }
            .btn-primary:hover {
                box-shadow: 0 6px 16px rgba(167, 116, 255, 0.45);
                transform: translateY(-2px);
            }
        </style>

        <div class="card p-4">
            <h4 class="mb-4">Select Packages From Your Assigned Clubs</h4>
            <form method="POST" action="{{ route('affiliate.portal.packages.save') }}">
                @csrf
                @if($websites->isEmpty())
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        No clubs are assigned to your affiliate account yet. Please contact super admin.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @foreach($websites as $website)
                    <div class="website-section">
                        <h6>📍 {{ $website->name }}</h6>
                        <div class="row g-3">
                            @forelse($website->packages as $package)
                                <div class="col-md-6">
                                    <label class="package-checkbox">
                                        <input type="checkbox" name="package_ids[]" value="{{ $package->id }}" {{ in_array($package->id, $selected) ? 'checked' : '' }}>
                                        <div class="package-info">
                                            <div class="package-name">{{ $package->name }}</div>
                                            <div class="package-price">${{ number_format($package->price, 2) }}</div>
                                        </div>
                                    </label>
                                </div>
                            @empty
                                <div class="col-12"><small class="text-muted">No active packages in this club.</small></div>
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

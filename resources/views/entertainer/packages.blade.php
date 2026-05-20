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
            <h4 class="mb-4">Select Packages From {{ $entertainer->website->name ?? 'Your Club' }}</h4>
            <form method="POST" action="{{ route('entertainer.portal.packages.save') }}">
                @csrf

                <div class="row g-3">
                    @forelse($packages as $package)
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
                        <div class="col-12"><small class="text-muted">No active packages in your club.</small></div>
                    @endforelse
                </div>

                <button type="submit" class="btn btn-primary mt-4">Save Selection</button>
            </form>
        </div>
    </div>
</div>
@endsection

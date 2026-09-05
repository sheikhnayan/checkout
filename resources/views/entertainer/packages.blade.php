@extends('admin.main')

@section('title', 'Select Entertainer Packages')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">
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
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-1 text-white">Select Packages From {{ $entertainer->website->name ?? 'Your Club' }}</h4>
                    <p class="text-muted mb-0">Choose which packages to showcase on your entertainer public page.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllEntPackagesBtn">
                        <i class="bx bx-check-double me-1"></i> Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllEntPackagesBtn">
                        <i class="bx bx-x-circle me-1"></i> Deselect All
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('entertainer.portal.packages.save') }}">
                @csrf

                <div class="row g-3">
                    @forelse($packages as $package)
                        <div class="col-md-6">
                            <div class="d-flex align-items-stretch gap-2 h-100">
                                <label class="package-checkbox flex-grow-1 mb-0">
                                    <input type="checkbox" name="package_ids[]" value="{{ $package->id }}" class="ent-package-cb" {{ in_array($package->id, $selected) ? 'checked' : '' }}>
                                    <div class="package-info">
                                        <div class="package-name">{{ $package->name }}</div>
                                        <div class="package-price">${{ number_format($package->price, 2) }}</div>
                                    </div>
                                </label>
                                <button type="button" 
                                    class="btn btn-sm btn-dark js-copy-single-checkout d-flex align-items-center justify-content-center" 
                                    data-checkout-url="{{ route('package.checkout.single', ['slug' => $package->website->slug ?? 'club', 'packageId' => ((\Illuminate\Support\Str::slug((string) $package->name) ?: 'package') . '-' . $package->id)]) }}?owner_slug={{ $entertainer->slug }}" 
                                    title="Copy Direct Checkout Link"
                                    style="border-radius: 10px; border: 1px solid rgba(255,255,255,0.12); padding: 0 15px; transition: all 0.2s ease;">
                                    <i class="bx bx-link"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-12"><small class="text-muted">No active packages in your club.</small></div>
                    @endforelse
                </div>

                <button type="submit" class="btn btn-primary mt-4">
                    <i class="bx bx-save me-1"></i> Save Selection
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllBtn = document.getElementById('selectAllEntPackagesBtn');
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function () {
            document.querySelectorAll('.ent-package-cb').forEach(cb => cb.checked = true);
        });
    }

    const deselectAllBtn = document.getElementById('deselectAllEntPackagesBtn');
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function () {
            document.querySelectorAll('.ent-package-cb').forEach(cb => cb.checked = false);
        });
    }

    // Copy Single Checkout Link
    document.querySelectorAll('.js-copy-single-checkout').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var url = this.getAttribute('data-checkout-url');
            navigator.clipboard.writeText(url).then(() => {
                var icon = this.querySelector('i');
                icon.className = 'bx bx-check text-success';
                setTimeout(() => {
                    icon.className = 'bx bx-link';
                }, 2000);
            });
        });
    });
});
</script>
@endsection

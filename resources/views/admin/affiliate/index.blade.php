@extends('admin.main')

@section('content')
<style>
    .modal-content-admin-dark {
        background-color: #1b1b29 !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 14px !important;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6) !important;
        color: #ffffff !important;
    }
    .alloc-box-admin {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 8px !important;
        max-height: 200px;
        overflow-y: auto;
        padding: 12px;
    }
    .text-white-force {
        color: #ffffff !important;
    }
    .text-muted-white {
        color: rgba(255, 255, 255, 0.75) !important;
    }
    /* Custom CartVIP Switch Toggle */
    .cartvip-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
        flex-shrink: 0;
    }
    .cartvip-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .cartvip-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #334155;
        transition: .3s ease;
        border-radius: 26px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .cartvip-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: #ffffff;
        transition: .3s ease;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .cartvip-switch input:checked + .cartvip-slider {
        background-color: #4f46e5;
        border-color: #6366f1;
    }
    .cartvip-switch input:checked + .cartvip-slider:before {
        transform: translateX(22px);
    }
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="mb-0 text-white-force">Promoter Applications & Management</h4>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 me-2" data-bs-toggle="modal" data-bs-target="#adminCreateSubAffiliateModal">
                        <i class="bx bx-user-plus"></i> Add Sub-Promoter
                    </button>
                    <span class="badge bg-label-info d-inline-flex align-items-center gap-1">
                        <i class="bx bx-sort-alt-2"></i> Sort by status
                    </span>
                    <a href="{{ route('admin.affiliate.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-warning' : 'btn-outline-warning' }} d-inline-flex align-items-center gap-1">
                        <i class="bx bx-time-five"></i> Pending
                    </a>
                    <a href="{{ route('admin.affiliate.index', ['status' => 'approved']) }}" class="btn btn-sm {{ $status === 'approved' ? 'btn-success' : 'btn-outline-success' }} d-inline-flex align-items-center gap-1">
                        <i class="bx bx-check-circle"></i> Approved
                    </a>
                    <a href="{{ route('admin.affiliate.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ $status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }} d-inline-flex align-items-center gap-1">
                        <i class="bx bx-x-circle"></i> Rejected
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th class="text-white-force">Name</th>
                        <th class="text-white-force">Type</th>
                        <th class="text-white-force">Email</th>
                        <th class="text-white-force">Status</th>
                        <th class="text-white-force">Application Date</th>
                        @if($status === 'approved')
                            <th class="text-white-force">Approved Date</th>
                            <th class="text-white-force">Approved By</th>
                        @elseif($status === 'rejected')
                            <th class="text-white-force">Rejected Date</th>
                            <th class="text-white-force">Rejected By</th>
                        @endif
                        <th class="text-white-force">Wallet</th>
                        <th class="text-white-force">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($affiliates as $affiliate)
                        <tr>
                            <td>
                                <strong class="text-white-force">{{ $affiliate->display_name ?: $affiliate->user->name }}</strong>
                                @if($affiliate->isSubAffiliate())
                                    <div class="fs-8 text-primary">
                                        <i class="bx bx-subdirectory-right"></i> Sub-Promoter (Parent: {{ $affiliate->parent->display_name ?? 'Primary Promoter' }})
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($affiliate->isSubAffiliate())
                                    <span class="badge bg-label-info">Sub-Promoter</span>
                                @else
                                    <span class="badge bg-label-primary">Primary Promoter</span>
                                @endif
                            </td>
                            <td><span class="text-white-force">{{ $affiliate->user->email }}</span></td>
                            <td><span class="badge bg-{{ $affiliate->status === 'approved' ? 'success' : ($affiliate->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($affiliate->status) }}</span></td>
                            <td style="white-space: nowrap;">
                                <span class="text-white-force">{{ optional($affiliate->created_at)->timezone('America/Los_Angeles')->format('M d, Y') }}</span><br>
                                <small class="text-muted-white">{{ optional($affiliate->created_at)->timezone('America/Los_Angeles')->format('h:i A') }}</small>
                            </td>
                            @if($status === 'approved')
                                <td style="white-space: nowrap;">
                                    @if($affiliate->approved_at)
                                        <span class="text-white-force">{{ optional($affiliate->approved_at)->timezone('America/Los_Angeles')->format('M d, Y') }}</span><br>
                                        <small class="text-muted-white">{{ optional($affiliate->approved_at)->timezone('America/Los_Angeles')->format('h:i A') }}</small>
                                    @else
                                        <span class="text-muted-white">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($affiliate->approved_by_user)
                                        <span class="text-white-force">{{ $affiliate->approved_by_user->name ?? 'Admin' }}</span>
                                    @else
                                        <span class="text-muted-white">—</span>
                                    @endif
                                </td>
                            @elseif($status === 'rejected')
                                <td style="white-space: nowrap;">
                                    @if($affiliate->rejected_at)
                                        <span class="text-white-force">{{ optional($affiliate->rejected_at)->timezone('America/Los_Angeles')->format('M d, Y') }}</span><br>
                                        <small class="text-muted-white">{{ optional($affiliate->rejected_at)->timezone('America/Los_Angeles')->format('h:i A') }}</small>
                                    @else
                                        <span class="text-muted-white">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($affiliate->rejected_by_user)
                                        <span class="text-white-force">{{ $affiliate->rejected_by_user->name ?? 'Admin' }}</span>
                                    @else
                                        <span class="text-muted-white">—</span>
                                    @endif
                                </td>
                            @endif
                            <td><span class="text-white-force">${{ number_format($affiliate->wallet_balance, 2) }}</span></td>
                            <td>
                                <a href="{{ route('admin.affiliate.show', $affiliate->id) }}" class="btn btn-sm btn-primary">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $status === 'pending' ? 7 : 9 }}" class="text-center text-white-force">No promoter records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SUPER ADMIN CREATE SUB-PROMOTER MODAL -->
<div class="modal fade" id="adminCreateSubAffiliateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('admin.affiliate.sub-affiliate.store') }}" method="POST" class="modal-content modal-content-admin-dark">
            @csrf
            <div class="modal-header border-bottom border-secondary pb-3">
                <h5 class="modal-title font-weight-bold text-white-force">Super Admin: Create Sub-Promoter Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-white-force">Select Primary Parent Promoter <span class="text-danger">*</span></label>
                    <select name="parent_affiliate_id" class="form-select" required>
                        <option value="">-- Select Parent Promoter --</option>
                        @foreach($parentAffiliates as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->display_name ?: $parent->user->name }} ({{ $parent->user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-white-force">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., Jane Doe" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-force">Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" class="form-control" placeholder="e.g., Jane VIP Events" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-force">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="jane@example.com" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-force">Initial Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="At least 6 characters" minlength="6" required>
                    </div>
                </div>

                <hr class="border-secondary my-4">

                <!-- ALLOCATE CLUBS / WEBSITES -->
                <h6 class="font-weight-bold mb-2 text-white-force"><i class="bx bx-building-house me-1 text-primary"></i> Allocate Clubs / Websites</h6>
                <p class="text-muted-white fs-7 mb-3">Sub-promoters will be allowed to sell packages under the allocated clubs below.</p>
                <div class="mb-4">
                    <div class="alloc-box-admin">
                        @foreach($allWebsites as $web)
                            <div class="form-check mb-2">
                                <input type="checkbox" name="website_ids[]" value="{{ $web->id }}" id="admin_web_{{ $web->id }}" class="form-check-input" checked>
                                <label class="form-check-label text-white-force fs-7" for="admin_web_{{ $web->id }}">{{ $web->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr class="border-secondary my-4">

                <!-- ONBOARDING & DASHBOARD TOGGLES -->
                <h6 class="font-weight-bold mb-3 text-white-force"><i class="bx bx-slider-alt me-1 text-primary"></i> Onboarding Rules & Dashboard Toggles</h6>

                <!-- Require Extra Onboarding Form Toggle -->
                <div class="p-3 bg-dark rounded border border-secondary mb-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-0 font-weight-bold text-white-force">Require Extra Onboarding Form Before Activation</h6>
                        <small class="text-muted-white">If enabled, an email with a form link is sent. Status remains pending until form is submitted.</small>
                    </div>
                    <label class="cartvip-switch mb-0">
                        <input type="checkbox" name="require_onboarding_form" value="1">
                        <span class="cartvip-slider"></span>
                    </label>
                </div>

                <!-- Dashboard Feature Visibility Toggles -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-dark rounded border border-secondary d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-0 text-white-force fs-7">Show Packages Tab</h6>
                            </div>
                            <label class="cartvip-switch mb-0">
                                <input type="checkbox" name="show_packages" value="1" checked>
                                <span class="cartvip-slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-dark rounded border border-secondary d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-0 text-white-force fs-7">Show Page Customization Tab</h6>
                            </div>
                            <label class="cartvip-switch mb-0">
                                <input type="checkbox" name="show_settings" value="1" checked>
                                <span class="cartvip-slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-dark rounded border border-secondary d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-0 text-white-force fs-7">Show QR Code & Share Link</h6>
                            </div>
                            <label class="cartvip-switch mb-0">
                                <input type="checkbox" name="show_qr_code" value="1" checked>
                                <span class="cartvip-slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-dark rounded border border-secondary d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-0 text-white-force fs-7">Show Sales Statistics</h6>
                            </div>
                            <label class="cartvip-switch mb-0">
                                <input type="checkbox" name="show_sales_stats" value="1" checked>
                                <span class="cartvip-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-secondary pt-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Sub-Promoter Account</button>
            </div>
        </form>
    </div>
</div>
@endsection

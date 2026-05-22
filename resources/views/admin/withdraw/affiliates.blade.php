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
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Affiliate Withdrawal Requests</h4>
        </div>

        <div class="row g-3 mb-4">
            {{-- Global Charge Setting --}}
            <div class="col-md-4">
                <div class="card p-4">
                    <h6 class="mb-2">Global Affiliate Withdraw Charge</h6>
                    <form method="POST" action="{{ route('admin.withdraw.affiliates.charge') }}" class="d-flex gap-2 align-items-center">
                        @csrf
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" max="100" class="form-control"
                                   name="affiliate_withdraw_charge"
                                   value="{{ $setting ? number_format($setting->affiliate_withdraw_charge, 2) : '0.00' }}"
                                   placeholder="0.00">
                            <span class="input-group-text">%</span>
                        </div>
                        <button type="submit" class="btn btn-warning btn-sm text-nowrap">Save</button>
                    </form>
                    <small class="text-muted mt-1 d-block">Applied to all new affiliate withdrawal requests.</small>
                </div>
            </div>

            {{-- Stats --}}
            <div class="col-md-2">
                <div class="card p-4 text-center">
                    <h6 class="text-muted mb-1">Pending</h6>
                    <h3 class="mb-0">{{ $requests->getCollection()->where('status','pending')->count() }}</h3>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card p-4 text-center">
                    <h6 class="text-muted mb-1">Done</h6>
                    <h3 class="mb-0">{{ $requests->getCollection()->where('status','done')->count() }}</h3>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card p-4 text-center">
                    <h6 class="text-muted mb-1">Rejected</h6>
                    <h3 class="mb-0">{{ $requests->getCollection()->where('status','rejected')->count() }}</h3>
                </div>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="mb-3">
            @foreach(['all' => 'All', 'pending' => 'Pending', 'done' => 'Done', 'rejected' => 'Rejected'] as $s => $label)
                <a href="{{ route('admin.withdraw.affiliates') }}?status={{ $s }}"
                   class="btn btn-sm me-1 {{ $status === $s ? 'btn-warning' : 'btn-outline-secondary' }}">
                   {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Affiliate</th>
                            <th>Amount</th>
                            <th>Fee</th>
                            <th>Net Payout</th>
                            <th>Payout Method</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $wr)
                        <tr>
                            <td>{{ $wr->id }}</td>
                            <td>{{ $wr->created_at->timezone('America/Los_Angeles')->format('M d, Y') }}</td>
                            <td>
                                @php $aff = $affiliates->get($wr->owner_id) @endphp
                                @if($aff)
                                    <a href="{{ route('admin.affiliate.show', $aff->id) }}">
                                        {{ $aff->display_name ?: $aff->user->name ?? 'Affiliate #'.$wr->owner_id }}
                                    </a>
                                @else
                                    Affiliate #{{ $wr->owner_id }}
                                @endif
                            </td>
                            <td>${{ number_format($wr->amount, 2) }}</td>
                            <td>
                                @if($wr->fee_amount > 0)
                                    ${{ number_format($wr->fee_amount, 2) }}
                                    <small class="text-muted">({{ $wr->fee_percentage }}%)</small>
                                @else —
                                @endif
                            </td>
                            <td>${{ number_format($wr->net_amount, 2) }}</td>
                            <td>
                                @php $snap = $wr->method_snapshot @endphp
                                @if($snap)
                                    <strong>{{ $snap['label'] ?? '' }}</strong>
                                    <small class="text-muted d-block">({{ $snap['type'] ?? '' }})</small>
                                    @foreach($snap['details'] ?? [] as $k => $v)
                                        @if($v)
                                            <small class="d-block" style="color:#374151;font-weight:500">
                                                <span style="color:#6b7280">{{ ucwords(str_replace('_', ' ', $k)) }}:</span> {{ $v }}
                                            </small>
                                        @endif
                                    @endforeach
                                @else —
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $wr->statusBadgeClass() }}">{{ $wr->statusLabel() }}</span>
                            </td>
                            <td>
                                @if($wr->admin_notes)
                                    <small><em>{{ $wr->admin_notes }}</em></small>
                                @elseif($wr->notes)
                                    <small>{{ $wr->notes }}</small>
                                @else —
                                @endif
                            </td>
                            <td style="min-width:180px;">
                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="txn-action-eye view-btn btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal" data-bs-target="#viewWithdrawalModal{{ $wr->id }}"
                                        data-withdrawal_id="{{ $wr->id }}"
                                        data-owner_name="{{ $aff ? ($aff->display_name ?: ($aff->user->name ?? 'Affiliate #'.$wr->owner_id)) : 'Affiliate #'.$wr->owner_id }}"
                                        data-amount="${{ number_format($wr->amount, 2) }}"
                                        data-fee="${{ number_format($wr->fee_amount, 2) }}"
                                        data-net="${{ number_format($wr->net_amount, 2) }}"
                                        data-method="{{ $snap['label'] ?? '' }}"
                                        data-status="{{ $wr->statusLabel() }}"
                                        data-notes="{{ $wr->admin_notes ?? $wr->notes }}"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <div class="dropdown">
                                        <button class="txn-action-more btn p-0" data-bs-toggle="dropdown" type="button" style="border:none;background:none">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" style="background:#1e293b;border:1px solid rgba(255,255,255,0.1)">
                                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="{{ route('admin.withdraw.affiliates.status', [$wr->id, 'done']) }}"><i class="fas fa-check me-2 text-success"></i>Mark Done</a></li>
                                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="{{ route('admin.withdraw.affiliates.status', [$wr->id, 'rejected']) }}"><i class="fas fa-times me-2 text-danger"></i>Mark Rejected</a></li>
                                        </ul>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary ms-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#statusModal{{ $wr->id }}">
                                        Update Status
                                    </button>
                                </div>

                                {{-- View Modal --}}
                                <div class="modal fade" id="viewWithdrawalModal{{ $wr->id }}" tabindex="-1" aria-labelledby="viewWithdrawalModalLabel{{ $wr->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="viewWithdrawalModalLabel{{ $wr->id }}">Withdrawal Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item"><strong>ID:</strong> {{ $wr->id }}</li>
                                                    <li class="list-group-item"><strong>Affiliate:</strong> {{ $aff ? ($aff->display_name ?: ($aff->user->name ?? 'Affiliate #'.$wr->owner_id)) : 'Affiliate #'.$wr->owner_id }}</li>
                                                    <li class="list-group-item"><strong>Amount:</strong> ${{ number_format($wr->amount, 2) }}</li>
                                                    <li class="list-group-item"><strong>Fee:</strong> ${{ number_format($wr->fee_amount, 2) }}</li>
                                                    <li class="list-group-item"><strong>Net Payout:</strong> ${{ number_format($wr->net_amount, 2) }}</li>
                                                    <li class="list-group-item">
                                                        <strong>Payout Method:</strong> {{ $snap['label'] ?? '—' }}
                                                        @if(!empty($snap['type']))
                                                            <span class="text-muted ms-1">({{ $snap['type'] }})</span>
                                                        @endif
                                                        @foreach($snap['details'] ?? [] as $k => $v)
                                                            @if($v)
                                                                <div class="mt-1" style="font-size:0.9rem">
                                                                    <span class="text-muted">{{ ucwords(str_replace('_', ' ', $k)) }}:</span>
                                                                    <strong>{{ $v }}</strong>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </li>
                                                    <li class="list-group-item"><strong>Status:</strong> <span class="badge {{ $wr->statusBadgeClass() }}">{{ $wr->statusLabel() }}</span></li>
                                                    <li class="list-group-item"><strong>Notes:</strong> {{ $wr->admin_notes ?? $wr->notes ?? '—' }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Status Modal --}}
                                <div class="modal fade" id="statusModal{{ $wr->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('admin.withdraw.affiliates.status', $wr->id) }}">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Update Withdrawal #{{ $wr->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Status <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Update the withdrawal status. 'Done' confirms payment was sent. 'Rejected' automatically refunds the amount to the affiliate's wallet."></i></label>
                                                        <select class="form-select" name="status" required>
                                                            <option value="pending"  {{ $wr->status==='pending'  ? 'selected':'' }}>Pending</option>
                                                            <option value="done"     {{ $wr->status==='done'     ? 'selected':'' }}>Done</option>
                                                            <option value="rejected" {{ $wr->status==='rejected' ? 'selected':'' }}>Rejected</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Admin Notes <small class="text-muted">(optional)</small> <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Internal notes visible to the affiliate. Use to explain a rejection or confirm payment details."></i></label>
                                                        <textarea class="form-control" name="admin_notes" rows="3"
                                                                  placeholder="Notes visible to the affiliate…">{{ $wr->admin_notes }}</textarea>
                                                    </div>
                                                    <div class="alert alert-info mb-0" style="font-size:0.85rem;">
                                                        Rejecting a <strong>pending</strong> request will automatically refund ${{ number_format($wr->amount, 2) }} to the affiliate's wallet.
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">No withdrawal requests found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $requests->links() }}
        </div>

    </div>
</div>
@endsection

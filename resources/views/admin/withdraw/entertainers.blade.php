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
            <h4 class="mb-0">Entertainer Withdrawal Requests</h4>
        </div>

        <div class="row g-3 mb-4">
            {{-- Withdraw Charge Setting --}}
            <div class="col-md-5">
                <div class="card p-4">
                    <h6 class="mb-2">Set Withdraw Charge</h6>
                    <form method="POST" action="{{ route('admin.withdraw.entertainers.charge') }}" class="d-flex gap-2 align-items-end flex-wrap">
                        @csrf
                        @if(auth()->user()->isAdmin())
                            <div>
                                <label class="form-label mb-1" style="font-size:0.8rem;">Website <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Select which venue's withdrawal charge rate you want to update."></i></label>
                                <select class="form-select form-select-sm" name="website_id" required>
                                    <option value="">— Select website —</option>
                                    @foreach($websites as $ws)
                                        <option value="{{ $ws->id }}"
                                            {{ ($website && $website->id == $ws->id) ? 'selected' : '' }}>
                                            {{ $ws->name }}
                                            ({{ number_format($ws->withdraw_charge, 2) }}%)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="website_id" value="{{ $website->id ?? '' }}">
                            <small class="text-muted">
                                Current charge for <strong>{{ $website->name ?? '' }}</strong>:
                                {{ number_format($website->withdraw_charge ?? 0, 2) }}%
                            </small>
                        @endif
                        <div>
                            <label class="form-label mb-1" style="font-size:0.8rem;">Charge % <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The fee percentage deducted from all entertainer withdrawal requests for the selected venue."></i></label>
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.01" min="0" max="100" class="form-control"
                                       name="withdraw_charge"
                                       value="{{ $website ? number_format($website->withdraw_charge, 2) : '0.00' }}"
                                       placeholder="0.00" style="width:90px;">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning btn-sm">Save</button>
                    </form>
                    <small class="text-muted mt-1 d-block">Applied to all new entertainer withdrawal requests for the selected website.</small>
                </div>
            </div>

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
                <a href="{{ route('admin.withdraw.entertainers') }}?status={{ $s }}"
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
                            <th>Entertainer</th>
                            <th>Website</th>
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
                                @php $ent = $entertainers->get($wr->owner_id) @endphp
                                @if($ent)
                                    <a href="{{ route('admin.entertainer.show', $ent->id) }}">
                                        {{ $ent->display_name ?: ($ent->user->name ?? 'Entertainer #'.$wr->owner_id) }}
                                    </a>
                                @else
                                    Entertainer #{{ $wr->owner_id }}
                                @endif
                            </td>
                            <td>{{ $wr->website->name ?? '—' }}</td>
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
                                            <small class="d-block text-muted">
                                                <b>{{ ucwords(str_replace('_', ' ', $k)) }}:</b> {{ $v }}
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
                                <button class="btn btn-sm btn-outline-primary w-100"
                                        data-bs-toggle="modal"
                                        data-bs-target="#statusModal{{ $wr->id }}">
                                    Update Status
                                </button>

                                {{-- Modal --}}
                                <div class="modal fade" id="statusModal{{ $wr->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('admin.withdraw.entertainers.status', $wr->id) }}">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Update Withdrawal #{{ $wr->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Status <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Update the withdrawal status. 'Done' confirms payment was sent. 'Rejected' automatically refunds the amount to the entertainer's wallet."></i></label>
                                                        <select class="form-select" name="status" required>
                                                            <option value="pending"  {{ $wr->status==='pending'  ? 'selected':'' }}>Pending</option>
                                                            <option value="done"     {{ $wr->status==='done'     ? 'selected':'' }}>Done</option>
                                                            <option value="rejected" {{ $wr->status==='rejected' ? 'selected':'' }}>Rejected</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Admin Notes <small class="text-muted">(optional)</small> <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Internal notes visible to the entertainer. Use to explain a rejection or confirm payment details."></i></label>
                                                        <textarea class="form-control" name="admin_notes" rows="3"
                                                                  placeholder="Notes visible to the entertainer…">{{ $wr->admin_notes }}</textarea>
                                                    </div>
                                                    <div class="alert alert-info mb-0" style="font-size:0.85rem;">
                                                        Rejecting a <strong>pending</strong> request will automatically refund ${{ number_format($wr->amount, 2) }} to the entertainer's wallet.
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
                            <td colspan="11" class="text-center text-muted py-4">No withdrawal requests found.</td>
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

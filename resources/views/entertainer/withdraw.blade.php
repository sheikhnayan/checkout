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

        {{-- ── Balance Banner ───────────────────────────────────────────────── --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card p-4 text-center">
                    <h6 class="text-muted mb-1">Available Balance</h6>
                    <h2 class="mb-0">${{ number_format($owner->wallet_balance, 2) }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 text-center">
                    <h6 class="text-muted mb-1">Withdraw Charge</h6>
                    <h2 class="mb-0">{{ number_format($charge, 2) }}%</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 text-center">
                    <h6 class="text-muted mb-1">Total Requests</h6>
                    <h2 class="mb-0">{{ $requests->total() }}</h2>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- ── Left Column ──────────────────────────────────────────────── --}}
            <div class="col-lg-5">

                {{-- Request Withdraw Form --}}
                <div class="card p-4 mb-4">
                    <h5 class="mb-3">Request Withdrawal</h5>

                    @if($payoutMethods->isEmpty())
                        <div class="alert alert-warning mb-0">
                            You must add at least one payout method before requesting a withdrawal.
                        </div>
                    @else
                        <form method="POST" action="{{ route('entertainer.portal.withdraw.request') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Amount ($)</label>
                                <input type="number" step="0.01" min="1" max="{{ $owner->wallet_balance }}"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       name="amount" value="{{ old('amount') }}" required>
                                @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">
                                    Max: ${{ number_format($owner->wallet_balance, 2) }}
                                    @if($charge > 0)
                                        &nbsp;·&nbsp; {{ number_format($charge, 2) }}% fee will be deducted
                                    @endif
                                </small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Payout Method</label>
                                <select class="form-select @error('payout_method_id') is-invalid @enderror"
                                        name="payout_method_id" required>
                                    <option value="">— Select method —</option>
                                    @foreach($payoutMethods as $m)
                                        <option value="{{ $m->id }}"
                                            {{ old('payout_method_id') == $m->id || $m->is_default ? 'selected' : '' }}>
                                            {{ $m->label }} ({{ $m->typeLabel() }}){{ $m->is_default ? ' ★' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('payout_method_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes <small class="text-muted">(optional)</small></label>
                                <textarea class="form-control" name="notes" rows="2"
                                          placeholder="Any instructions for the admin…">{{ old('notes') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-warning w-100">Submit Withdrawal Request</button>
                        </form>
                    @endif
                </div>

                {{-- Add Payout Method --}}
                <div class="card p-4">
                    <h5 class="mb-3">Add Payout Method</h5>
                    <form method="POST" action="{{ route('entertainer.portal.withdraw.methods.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Label</label>
                            <input type="text" class="form-control" name="label"
                                   value="{{ old('label') }}" placeholder="e.g. My Chase Checking" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Method Type</label>
                            <select class="form-select" name="type" id="entertainerMethodType" required
                                    onchange="renderDetailsFields(this.value, 'entertainerDetailsContainer')">
                                <option value="">— Select type —</option>
                                @foreach($typeLabels as $val => $label)
                                    <option value="{{ $val }}" {{ old('type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="entertainerDetailsContainer"></div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_default" value="1"
                                   id="entertainerIsDefault" {{ old('is_default') ? 'checked' : '' }}>
                            <label class="form-check-label" for="entertainerIsDefault">Set as default</label>
                        </div>

                        <button type="submit" class="btn btn-outline-primary w-100">Save Payout Method</button>
                    </form>
                </div>

            </div>

            {{-- ── Right Column ─────────────────────────────────────────────── --}}
            <div class="col-lg-7">

                {{-- Saved Payout Methods --}}
                @if($payoutMethods->isNotEmpty())
                <div class="card p-4 mb-4">
                    <h5 class="mb-3">Saved Payout Methods</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Label</th>
                                    <th>Type</th>
                                    <th>Details</th>
                                    <th>Default</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payoutMethods as $m)
                                <tr>
                                    <td>{{ $m->label }}</td>
                                    <td>{{ $m->typeLabel() }}</td>
                                    <td>
                                        @foreach($m->details ?? [] as $key => $val)
                                            @if($val)
                                                <small class="d-block text-muted">
                                                    <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $val }}
                                                </small>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($m->is_default)
                                            <span class="badge bg-warning text-dark">Default</span>
                                        @else
                                            <form method="POST" action="{{ route('entertainer.portal.withdraw.methods.default', $m->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">Set Default</button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('entertainer.portal.withdraw.methods.destroy', $m->id) }}"
                                              onsubmit="return confirm('Remove this payout method?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Withdrawal History --}}
                <div class="card p-4">
                    <h5 class="mb-3">Withdrawal History</h5>
                    @if($requests->isEmpty())
                        <p class="text-muted">No withdrawal requests yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Fee</th>
                                        <th>You Receive</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $wr)
                                    <tr>
                                        <td>{{ $wr->created_at->format('M d, Y') }}</td>
                                        <td>${{ number_format($wr->amount, 2) }}</td>
                                        <td>
                                            @if($wr->fee_amount > 0)
                                                ${{ number_format($wr->fee_amount, 2) }}
                                                <small class="text-muted">({{ $wr->fee_percentage }}%)</small>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>${{ number_format($wr->net_amount, 2) }}</td>
                                        <td>
                                            @php $snap = $wr->method_snapshot @endphp
                                            @if($snap)
                                                <small>{{ $snap['label'] ?? '' }}
                                                    <span class="text-muted">({{ $snap['type'] ?? '' }})</span>
                                                </small>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $wr->statusBadgeClass() }}">
                                                {{ $wr->statusLabel() }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($wr->admin_notes)
                                                <small class="text-muted"><em>Admin: {{ $wr->admin_notes }}</em></small>
                                            @elseif($wr->notes)
                                                <small>{{ $wr->notes }}</small>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $requests->links() }}
                    @endif
                </div>

            </div>
        </div>

    </div>
</div>

<script>
const detailFields = {
    bank_transfer: [
        { key: 'bank_name',       label: 'Bank Name',        placeholder: 'e.g. Chase Bank' },
        { key: 'account_name',    label: 'Account Holder',   placeholder: 'Name on account' },
        { key: 'account_number',  label: 'Account Number',   placeholder: '••••••••' },
        { key: 'routing_number',  label: 'Routing Number',   placeholder: '9-digit routing number' },
    ],
    wire: [
        { key: 'bank_name',      label: 'Bank Name',         placeholder: 'e.g. Wells Fargo' },
        { key: 'account_name',   label: 'Account Holder',    placeholder: 'Name on account' },
        { key: 'account_number', label: 'Account Number',    placeholder: '••••••••' },
        { key: 'routing_number', label: 'Routing / SWIFT',   placeholder: 'Routing or SWIFT code' },
        { key: 'bank_address',   label: 'Bank Address',      placeholder: 'Optional' },
    ],
    check: [
        { key: 'payable_to',   label: 'Payable To',  placeholder: 'Name on check' },
        { key: 'mail_address', label: 'Mailing Address', placeholder: 'Full mailing address' },
    ],
    paypal: [
        { key: 'paypal_email', label: 'PayPal Email', placeholder: 'your@paypal.com' },
    ],
    zelle: [
        { key: 'zelle_phone_or_email', label: 'Zelle Phone or Email', placeholder: 'Phone or email linked to Zelle' },
    ],
    other: [
        { key: 'instructions', label: 'Instructions', placeholder: 'Describe how to send payment' },
    ],
};

function renderDetailsFields(type, containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    const fields = detailFields[type] || [];
    fields.forEach(f => {
        container.innerHTML += `
            <div class="mb-3">
                <label class="form-label">${f.label}</label>
                <input type="text" class="form-control" name="details[${f.key}]" placeholder="${f.placeholder}">
            </div>`;
    });
}
</script>
@endsection

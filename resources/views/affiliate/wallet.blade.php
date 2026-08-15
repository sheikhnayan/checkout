@extends('admin.main')

@section('title', 'Promoter Wallet & Package Sales')

@section('content')
@php
    $totalSalesVolume = $bookingTransactions->sum(fn($t) => (float)($t->actual_total ?? $t->total ?? 0));
    $totalCommissionsEarned = $bookingTransactions->sum(fn($t) => (float)($t->affiliate_commission_amount ?? 0));
@endphp
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold py-1 mb-1 text-white">Promoter Wallet & Package Sales</h4>
                <p class="text-muted mb-0">Track all package bookings, customer transactions, team sub-promoter sales, and wallet payouts.</p>
            </div>
            @if(!$affiliate->isSubAffiliate())
            <a href="{{ route('affiliate.portal.withdraw') }}" class="btn btn-primary">
                <i class="bx bx-money-withdraw me-1"></i> Request Payout
            </a>
            @endif
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card p-4 border border-secondary bg-dark text-white">
                    <span class="text-muted fs-7 text-uppercase fw-bold">Available Wallet Balance</span>
                    <h3 class="mb-0 mt-2 text-white">${{ number_format($affiliate->wallet_balance, 2) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4 border border-secondary bg-dark text-white">
                    <span class="text-muted fs-7 text-uppercase fw-bold">Total Sales Count</span>
                    <h3 class="mb-0 mt-2 text-white">{{ $bookingTransactions->count() }} <span class="fs-7 text-muted font-normal">Bookings</span></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4 border border-secondary bg-dark text-white">
                    <span class="text-muted fs-7 text-uppercase fw-bold">Gross Sales Volume</span>
                    <h3 class="mb-0 mt-2 text-white">${{ number_format($totalSalesVolume, 2) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4 border border-secondary bg-dark text-white">
                    <span class="text-muted fs-7 text-uppercase fw-bold">Total Commission Earned</span>
                    <h3 class="mb-0 mt-2 text-white">${{ number_format($totalCommissionsEarned, 2) }}</h3>
                </div>
            </div>
        </div>

        <div class="card p-4 mb-4 border border-secondary bg-dark text-white">
            <h5 class="mb-3 text-white"><i class="bx bx-shopping-bag me-1 text-primary"></i> Package Sales & Customer Bookings</h5>
            @include('partials.transaction-table', [
                'transactions'    => $bookingTransactions,
                'tableId'         => 'affiliateWalletTransactionTable',
                'detailsBase'     => url('/affiliate-portal/transaction'),
                'commissionField' => 'affiliate',
                'emptyText'       => 'No package sales recorded yet.',
            ])
        </div>

        <div class="card p-4 border border-secondary bg-dark text-white">
            <h5 class="mb-3 text-white"><i class="bx bx-history me-1 text-primary"></i> Wallet Ledger & Activity History</h5>
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th class="text-white">Date</th>
                        <th class="text-white">Type</th>
                        <th class="text-white">Description</th>
                        <th class="text-white">Amount</th>
                        <th class="text-white">Balance After</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $item)
                        @php
                            $linkedTransaction = $item->transaction;
                            $displayTransactionId = $linkedTransaction->transaction_id ?? $item->transaction_id;
                            $descriptionText = (string) ($item->description ?? '');
                            $hasPurchaseRef = preg_match('/purchase\s*#\d+/i', $descriptionText) === 1;
                        @endphp
                        <tr>
                            <td>{{ $item->created_at->format('M d, Y H:i') }}</td>
                            <td><span class="badge bg-label-{{ $item->type === 'commission' ? 'success' : 'warning' }}">{{ ucfirst($item->type) }}</span></td>
                            <td>
                                @if($linkedTransaction && $displayTransactionId && $hasPurchaseRef)
                                    {!! preg_replace(
                                        '/purchase\s*#\d+/i',
                                        'purchase <a href="#" class="wallet-transaction-link text-primary text-decoration-underline" data-table-id="affiliateWalletTransactionTable" data-transaction-row-id="' . e($linkedTransaction->id) . '">#' . e($displayTransactionId) . '</a>',
                                        e($descriptionText),
                                        1
                                    ) !!}
                                @elseif($linkedTransaction && $displayTransactionId)
                                    {{ $descriptionText }}
                                    <span class="ms-1">(<a href="#" class="wallet-transaction-link text-primary text-decoration-underline" data-table-id="affiliateWalletTransactionTable" data-transaction-row-id="{{ $linkedTransaction->id }}">#{{ $displayTransactionId }}</a>)</span>
                                @else
                                    {{ $item->description }}
                                @endif
                            </td>
                            <td class="fw-bold {{ $item->amount >= 0 ? 'text-success' : 'text-danger' }}">${{ number_format($item->amount, 2) }}</td>
                            <td>${{ number_format($item->balance_after, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">No wallet activity yet.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('click', function (e) {
    var link = e.target.closest('.wallet-transaction-link');
    if (!link) return;
    e.preventDefault();

    var tableId = link.getAttribute('data-table-id');
    var rowId = link.getAttribute('data-transaction-row-id');
    if (!tableId || !rowId || typeof window.focusWalletTransactionRow !== 'function') return;

    window.focusWalletTransactionRow(tableId, rowId);
});
</script>
@endpush

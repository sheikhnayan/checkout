@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card p-4 mb-4">
            <h4 class="mb-2">Entertainer Wallet</h4>
            <p class="mb-0"><strong>Current Balance:</strong> ${{ number_format($entertainer->wallet_balance, 2) }}</p>
        </div>

        <div class="card p-4 mb-4">
            <h5 class="mb-3">Transactions</h5>
            @include('partials.transaction-table', [
                'transactions'    => $bookingTransactions,
                'tableId'         => 'entertainerWalletTransactionTable',
                'detailsBase'     => url('/entertainer-portal/transaction'),
                'commissionField' => 'entertainer',
                'emptyText'       => 'No transactions yet.',
            ])
        </div>

        <div class="card p-4">
            <h5 class="mb-3">Wallet Activity</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Balance After</th>
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
                            <td>{{ ucfirst($item->type) }}</td>
                            <td>
                                @if($linkedTransaction && $displayTransactionId && $hasPurchaseRef)
                                    {!! preg_replace(
                                        '/purchase\s*#\d+/i',
                                        'purchase <a href="#" class="wallet-transaction-link" data-table-id="entertainerWalletTransactionTable" data-transaction-row-id="' . e($linkedTransaction->id) . '">#' . e($displayTransactionId) . '</a>',
                                        e($descriptionText),
                                        1
                                    ) !!}
                                @elseif($linkedTransaction && $displayTransactionId)
                                    {{ $descriptionText }}
                                    <span class="ms-1">(<a href="#" class="wallet-transaction-link" data-table-id="entertainerWalletTransactionTable" data-transaction-row-id="{{ $linkedTransaction->id }}">#{{ $displayTransactionId }}</a>)</span>
                                @else
                                    {{ $item->description }}
                                @endif
                            </td>
                            <td>${{ number_format($item->amount, 2) }}</td>
                            <td>${{ number_format($item->balance_after, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No wallet activity yet.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{ $transactions->links() }}
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

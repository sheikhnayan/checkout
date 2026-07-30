@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card p-4">
            <h4 class="mb-2">Affiliate Wallet</h4>
            <p><strong>Current Balance:</strong> ${{ number_format($affiliate->wallet_balance, 2) }}</p>

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
                        <tr>
                            <td>{{ $item->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ ucfirst($item->type) }}</td>
                            <td>{{ $item->description }}</td>
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

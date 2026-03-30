@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card p-4 mb-4">
            <h4>{{ $entertainer->display_name ?: $entertainer->user->name }}</h4>
            <p class="mb-1"><strong>Email:</strong> {{ $entertainer->user->email }}</p>
            <p class="mb-1"><strong>Club:</strong> {{ $entertainer->website->name ?? 'N/A' }}</p>
            <p class="mb-1"><strong>Status:</strong> {{ ucfirst($entertainer->status) }}</p>
            @if($entertainer->slug)
                <p class="mb-3"><strong>Public Page:</strong> <a href="{{ route('entertainer.public', $entertainer->slug) }}" target="_blank">{{ route('entertainer.public', $entertainer->slug) }}</a></p>
            @endif

            @if($entertainer->status !== 'approved')
                <form method="POST" action="{{ route('admin.entertainer.approve', $entertainer->id) }}" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-success">Approve</button>
                </form>
            @endif

            @if($entertainer->status !== 'rejected')
                <form method="POST" action="{{ route('admin.entertainer.reject', $entertainer->id) }}" class="mb-2">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Rejection Reason (optional)</label>
                        <textarea name="rejection_reason" rows="2" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

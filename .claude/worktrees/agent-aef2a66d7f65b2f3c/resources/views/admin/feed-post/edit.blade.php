@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1">Edit Feed Post</h4>
                    <p class="text-muted mb-0">Update caption, gallery, and visibility for this post.</p>
                    <p class="mb-0 mt-1">
                        <span class="badge {{ ($feedPost->approval_status ?? 'approved') === 'pending' ? 'bg-warning text-dark' : (($feedPost->approval_status ?? 'approved') === 'rejected' ? 'bg-danger' : 'bg-success') }}">
                            Moderation: {{ ucfirst($feedPost->approval_status ?? 'approved') }}
                        </span>
                        @if($feedPost->review_required)
                            <span class="badge bg-warning text-dark ms-1">Review Needed</span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('admin.feed-post.show', $feedPost) }}" class="btn btn-outline-info">Manage Comments</a>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.feed-post.update', $feedPost) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('admin.feed-post._form', ['feedPost' => $feedPost])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
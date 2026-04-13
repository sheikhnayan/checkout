@extends('admin.main')

@section('content')
@php $firstWebsite = $websites->first(); @endphp
@php
    $isApprover = auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isWebsiteUser());
    $pendingCount = $posts->where('approval_status', 'pending')->count();
@endphp
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 mt-4">
            <div>
                <h3 class="mb-1">Feed Posts</h3>
                <p class="text-muted mb-0">Publish club or entertainer posts with uploaded or external image/video media.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if($firstWebsite)
                    <a href="{{ route('club.feed', $firstWebsite->slug) }}" target="_blank" class="btn btn-outline-primary">View Feed Example</a>
                @endif
                <a href="{{ route('admin.feed-post.create') }}" class="btn btn-primary">Create Post</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($isApprover && $pendingCount > 0)
            <div class="alert alert-warning">
                <strong>{{ $pendingCount }}</strong> entertainer post(s) are waiting for approval.
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                @if($websites->count() > 1)
                <form method="GET" action="{{ route('admin.feed-post.index') }}" class="mb-3 d-flex align-items-center gap-2">
                    <select name="website_id" class="form-select w-auto" onchange="this.form.submit()">
                        <option value="">All Clients</option>
                        @foreach($websites as $site)
                            <option value="{{ $site->id }}" {{ request('website_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                        @endforeach
                    </select>
                    @if(request('website_id'))
                        <a href="{{ route('admin.feed-post.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                    @endif
                </form>
                @endif
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Post</th>
                            <th>Website</th>
                            <th>Posted By</th>
                            <th>Identity</th>
                            <th>Media</th>
                            <th>Comments</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($post->caption ?: 'Untitled post', 70) }}</div>
                                    <small class="text-muted">{{ optional($post->posted_at)->format('M d, Y h:i A') }}</small>
                                </td>
                                <td>{{ $post->website->name ?? 'N/A' }}</td>
                                <td>{{ $post->author_name }}</td>
                                <td>
                                    @if($post->author_mode === 'club')
                                        <span class="badge bg-label-success">Real (Club)</span>
                                    @elseif($post->feedModel)
                                        <span class="badge {{ $post->feedModel->is_real_profile ? 'bg-label-success' : 'bg-label-warning' }}">
                                            {{ $post->feedModel->is_real_profile ? 'Real (Model)' : 'Fake (Model)' }}
                                        </span>
                                    @else
                                        <span class="badge bg-label-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>{{ count((array) $post->resolved_media_items) }} media item(s)</td>
                                <td>{{ $post->comments_count }}</td>
                                <td>
                                    @if(($post->approval_status ?? 'approved') === 'pending')
                                        <span class="badge bg-warning text-dark">Pending Approval</span>
                                    @elseif(($post->approval_status ?? 'approved') === 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge {{ $post->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $post->is_active ? 'Live' : 'Hidden' }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('club.feed', $post->website->slug) }}#post-{{ $post->id }}" target="_blank" class="btn btn-sm btn-outline-secondary">View Live</a>
                                    <a href="{{ route('admin.feed-post.show', $post) }}" class="btn btn-sm btn-outline-info">Comments</a>
                                    <a href="{{ route('admin.feed-post.edit', $post) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    @if($isApprover && ($post->approval_status ?? 'approved') === 'pending')
                                        <form action="{{ route('admin.feed-post.approve', $post) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this entertainer post?')">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.feed-post.reject', $post) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Reject this entertainer post?')">Reject</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.feed-post.destroy', $post) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this post?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">No feed posts created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('admin.main')

@section('content')
@php $firstWebsite = $websites->first(); @endphp
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 mt-4">
            <div>
                <h3 class="mb-1">Feed Posts</h3>
                <p class="text-muted mb-0">Publish club or model posts with uploaded or external image/video media.</p>
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

        <div class="card">
            <div class="card-body">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Post</th>
                            <th>Website</th>
                            <th>Posted By</th>
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
                                <td>{{ count((array) $post->resolved_media_items) }} media item(s)</td>
                                <td>{{ $post->comments_count }}</td>
                                <td>
                                    <span class="badge {{ $post->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $post->is_active ? 'Live' : 'Hidden' }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('club.feed', $post->website->slug) }}#post-{{ $post->id }}" target="_blank" class="btn btn-sm btn-outline-secondary">View Live</a>
                                    <a href="{{ route('admin.feed-post.show', $post) }}" class="btn btn-sm btn-outline-info">Comments</a>
                                    <a href="{{ route('admin.feed-post.edit', $post) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.feed-post.destroy', $post) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this post?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No feed posts created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
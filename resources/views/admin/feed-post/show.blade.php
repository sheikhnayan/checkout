@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 mt-4">
            <div>
                <h3 class="mb-1">Post Comments</h3>
                <p class="text-muted mb-0">Moderate comments and review the live post details.</p>
            </div>
            <a href="{{ route('admin.feed-post.edit', $feedPost) }}" class="btn btn-primary">Edit Post</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            @if($feedPost->author_avatar)
                                <img src="{{ asset('uploads/' . $feedPost->author_avatar) }}" alt="{{ $feedPost->author_name }}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;">
                            @else
                                <div class="d-inline-flex align-items-center justify-content-center" style="width:60px;height:60px;border-radius:50%;background:#eef2ff;color:#4338ca;font-weight:700;">
                                    {{ strtoupper(substr($feedPost->author_name ?? 'FM', 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $feedPost->author_name }}</div>
                                <small class="text-muted">{{ $feedPost->website->name ?? 'No website' }}</small>
                            </div>
                        </div>
                        <p style="white-space:pre-wrap;">{{ $feedPost->caption }}</p>
                        <div class="row g-2">
                            @foreach((array) $feedPost->resolved_media_items as $item)
                                @php $mediaUrl = ($item['source'] ?? 'upload') === 'upload' ? asset('uploads/' . $item['url']) : $item['url']; @endphp
                                <div class="col-6">
                                    @if(($item['type'] ?? 'image') === 'video')
                                        <video src="{{ $mediaUrl }}" style="width:100%;height:180px;object-fit:cover;border-radius:12px;" controls></video>
                                    @else
                                        <img src="{{ $mediaUrl }}" alt="Post media" style="width:100%;height:180px;object-fit:cover;border-radius:12px;">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Comments</h5>
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Comment</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feedPost->comments as $comment)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $comment->commenter_name }}</div>
                                            @if($comment->commenter_email)
                                                <small class="text-muted">{{ $comment->commenter_email }}</small>
                                            @endif
                                        </td>
                                        <td><div style="max-width:360px;white-space:normal;">{{ $comment->body }}</div></td>
                                        <td>
                                            <span class="badge {{ $comment->is_visible ? 'bg-success' : 'bg-secondary' }}">{{ $comment->is_visible ? 'Visible' : 'Hidden' }}</span>
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.feed-post.comments.toggle', [$feedPost, $comment]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning">{{ $comment->is_visible ? 'Hide' : 'Show' }}</button>
                                            </form>
                                            <form action="{{ route('admin.feed-post.comments.destroy', [$feedPost, $comment]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this comment?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No comments on this post yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
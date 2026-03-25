@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 mt-4">
            <div>
                <h3 class="mb-1">Feed Models</h3>
                <p class="text-muted mb-0">Create and manage the personalities that appear to post in the social feed.</p>
            </div>
            <a href="{{ route('admin.feed-model.create') }}" class="btn btn-primary">Add Model</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Model</th>
                            <th>Website</th>
                            <th>Bio</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($models as $model)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($model->profile_image)
                                            <img src="{{ asset('uploads/' . $model->profile_image) }}" alt="{{ $model->name }}" style="width:54px;height:54px;border-radius:50%;object-fit:cover;">
                                        @else
                                            <div class="d-inline-flex align-items-center justify-content-center" style="width:54px;height:54px;border-radius:50%;background:#eef2ff;color:#4338ca;font-weight:700;">
                                                {{ strtoupper(substr($model->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $model->name }}</div>
                                            <small class="text-muted">{{ $model->posts()->count() }} posts</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $model->website->name ?? 'N/A' }}</td>
                                <td><div style="max-width:380px;white-space:normal;">{{ \Illuminate\Support\Str::limit($model->bio, 120) }}</div></td>
                                <td>
                                    <span class="badge {{ $model->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $model->is_active ? 'Active' : 'Hidden' }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.feed-model.edit', $model) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.feed-model.destroy', $model) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this model and all of its posts?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No feed models created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
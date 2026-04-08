@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <h4 class="mb-1">Job Marketplace</h4>
                <p class="text-muted mb-0">Manage all job posts for clubs.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.jobs.applications') }}" class="btn btn-outline-light">Applications</a>
                <a href="{{ route('admin.jobs.preference-requests') }}" class="btn btn-outline-light">Preferred-Work Requests</a>
                <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary">Create Job Post</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Club</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Applications</th>
                            <th>Posted At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $index => $job)
                            <tr>
                                <td>{{ $jobs->firstItem() + $index }}</td>
                                <td>{{ $job->website->name ?? '-' }}</td>
                                <td>{{ $job->title }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ ucfirst($job->job_type) }}</span>
                                </td>
                                <td>{{ $job->location ?: '-' }}</td>
                                <td>
                                    @if($job->status && !$job->is_archived)
                                        <span class="badge bg-success">Live</span>
                                    @elseif($job->is_archived)
                                        <span class="badge bg-secondary">Archived</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Paused</span>
                                    @endif
                                </td>
                                <td>{{ $job->applications_count }}</td>
                                <td>{{ optional($job->created_at)->format('M d, Y h:i A') }}</td>
                                <td>
                                    <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-sm btn-primary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">No job posts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer justify-content-between">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

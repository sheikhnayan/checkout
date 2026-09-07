@extends(request()->routeIs('admin.nightly-reports.*') ? 'admin.nightly-reports.layout' : 'admin.main')

@section('content')
@php
  $isNightly = request()->routeIs('admin.nightly-reports.*');
  $createRoute = $isNightly ? 'admin.nightly-reports.jobs.create' : 'admin.jobs.create';
  $appsRoute = $isNightly ? 'admin.nightly-reports.jobs.applications' : 'admin.jobs.applications';
  $prefRoute = $isNightly ? 'admin.nightly-reports.jobs.preference-requests' : 'admin.jobs.preference-requests';
  $editRoute = $isNightly ? 'admin.nightly-reports.jobs.edit' : 'admin.jobs.edit';
@endphp
<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <h4 class="mb-1 text-white">Job Marketplace</h4>
                <p class="text-muted mb-0">Manage all job posts for clubs.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route($appsRoute) }}" class="btn btn-outline-light">Applications</a>
                <a href="{{ route($prefRoute) }}" class="btn btn-outline-light">Preferred-Work Requests</a>
                <a href="{{ route($createRoute) }}" class="btn btn-gold">Create Job Post</a>
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
                                <td>
                                    <a href="{{ route($appsRoute, ['job_id' => $job->id]) }}" class="badge bg-primary text-decoration-none px-2 py-1" style="font-size: 0.85rem;" title="Filter applications for {{ $job->title }}">
                                        <i class="fas fa-users me-1"></i> {{ $job->applications_count }}
                                    </a>
                                </td>
                                <td>{{ optional($job->created_at)?->timezone('America/Los_Angeles')->format('M d, Y h:i A') }} PT</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route($appsRoute, ['job_id' => $job->id]) }}" class="btn btn-sm btn-info text-dark font-weight-bold" title="View applications for this job">
                                            <i class="fas fa-users me-1"></i> Applications ({{ $job->applications_count }})
                                        </a>
                                        <a href="{{ route($editRoute, $job) }}" class="btn btn-sm btn-gold">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No job posts found.</td>
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

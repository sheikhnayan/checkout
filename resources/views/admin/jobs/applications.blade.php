@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h4 class="mb-0">Job Applications</h4>
            <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-light">Back to Jobs</a>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.jobs.applications') }}" class="row g-2">
                    <div class="col-md-4">
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="entertainer" {{ request('type') === 'entertainer' ? 'selected' : '' }}>Entertainer</option>
                            <option value="employee" {{ request('type') === 'employee' ? 'selected' : '' }}>Employee</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            @foreach(['new','reviewed','shortlisted','rejected','hired'] as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Club</th>
                            <th>Job</th>
                            <th>Applicant</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $index => $application)
                            <tr>
                                <td>{{ $applications->firstItem() + $index }}</td>
                                <td>{{ $application->website->name ?? '-' }}</td>
                                <td>{{ $application->jobPost->title ?? '-' }}</td>
                                <td>
                                    {{ trim(($application->legal_first_name ?? '') . ' ' . ($application->legal_last_name ?? '')) ?: '-' }}
                                    <small class="d-block text-muted">{{ $application->email }}</small>
                                </td>
                                <td>{{ ucfirst($application->application_type) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.jobs.applications.status', $application->id) }}" class="d-flex gap-2">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm">
                                            @foreach(['new','reviewed','shortlisted','rejected','hired'] as $status)
                                                <option value="{{ $status }}" {{ $application->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </form>
                                </td>
                                <td>{{ optional($application->submitted_at)->format('M d, Y h:i A') }}</td>
                                <td>
                                    <a href="{{ route('admin.jobs.applications.show', $application->id) }}" class="btn btn-sm btn-outline-light">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No applications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer justify-content-between">
                {{ $applications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

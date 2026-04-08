@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h4 class="mb-0">Preferred-Work Requests</h4>
            <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-light">Back to Jobs</a>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Club</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Preferred Role</th>
                            <th>Status</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $index => $requestItem)
                            <tr>
                                <td>{{ $requests->firstItem() + $index }}</td>
                                <td>{{ $requestItem->website->name ?? '-' }}</td>
                                <td>{{ $requestItem->name }}</td>
                                <td>
                                    <div>{{ $requestItem->email }}</div>
                                    <small class="text-muted">{{ $requestItem->phone ?: '-' }}</small>
                                </td>
                                <td>{{ $requestItem->preferred_role ?: '-' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.jobs.preference-requests.status', $requestItem->id) }}" class="d-flex gap-2">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm">
                                            @foreach(['new','reviewed','contacted','closed'] as $status)
                                                <option value="{{ $status }}" {{ $requestItem->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </form>
                                </td>
                                <td>{{ optional($requestItem->submitted_at)->format('M d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <strong>Experience:</strong> {{ $requestItem->experience['summary'] ?? '-' }}
                                    <br>
                                    <strong>Message:</strong> {{ $requestItem->message ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No preferred-work requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer justify-content-between">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

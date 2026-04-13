@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="app-main__inner">
            <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                <h4 class="mb-0">Incident Reports - {{ $website->name }}</h4>
                <a href="{{ route('admin.incident.create', $websiteId) }}" class="btn btn-primary">Create Incident</a>
            </div>

            <div class="card bg-primary text-white p-2">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Incident Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Reporter</th>
                            <th>Witness Reports</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incidents as $index => $incident)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ optional($incident->incident_calendar_date)->format('Y-m-d') }}</td>
                                <td>{{ $incident->incident_time }}</td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'open' => 'bg-danger',
                                            'under_review' => 'bg-warning text-dark',
                                            'closed' => 'bg-success',
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusClasses[$incident->status] ?? 'bg-secondary' }}">{{ ucwords(str_replace('_', ' ', $incident->status)) }}</span>
                                </td>
                                <td>{{ $incident->reporter_name }}</td>
                                <td>{{ $incident->witnessReports->count() }}</td>
                                <td>{{ $incident->created_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.incident.details', $incident->id) }}" class="btn btn-sm btn-secondary">Details</a>
                                    <a href="{{ route('admin.incident.export', $incident->id) }}" class="btn btn-sm btn-info">Export PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No incident reports yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

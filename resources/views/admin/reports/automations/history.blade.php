@extends('admin.main')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <a href="{{ route('admin.reports.automation.schedules') }}" class="btn btn-outline-secondary btn-sm mb-2">Back to Schedules</a>
            <h1 class="h2 mb-1" style="color: #fff">Generated & Sent Reports</h1>
            <p class="text-muted mb-0">Track automation runs, delivery status, and failures.</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($runs->isEmpty())
                <div class="p-4 text-muted">No generated reports yet.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th>Recipients</th>
                            <th>Sent At</th>
                            <th>Triggered By</th>
                            <th>Error</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($runs as $run)
                            <tr>
                                <td>{{ $run->id }}</td>
                                <td>{{ optional($run->schedule)->name ?: 'N/A' }}</td>
                                <td>
                                    @if($run->status === 'sent')
                                        <span class="badge bg-success">Sent</span>
                                    @elseif($run->status === 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($run->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ implode(', ', $run->email_recipients ?? []) }}</small>
                                </td>
                                <td>{{ $run->sent_at ? $run->sent_at->format('Y-m-d H:i') : '-' }}</td>
                                <td>{{ optional($run->triggeredBy)->name ?: 'Scheduler' }}</td>
                                <td><small class="text-danger">{{ $run->error_message }}</small></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        {{ $runs->links() }}
    </div>
</div>
@endsection

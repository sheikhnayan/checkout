@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">
        
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <h4 class="mb-1 text-white"><i class="bx bx-receipt me-2"></i>Submissions for: {{ $form->title }}</h4>
                <p class="text-muted mb-0 small">Viewing all submitted data records collected through this form.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.forms.submissions.export', $form->id) }}" class="btn btn-outline-success">
                    <i class="bx bx-download me-1"></i> Export to CSV
                </a>
                <a href="{{ route('admin.forms.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-left-arrow-alt me-1"></i> Back to Forms
                </a>
            </div>
        </div>

        <!-- Submissions Table Card -->
        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr class="table-dark">
                            <th># ID</th>
                            <th>Submitted Date</th>
                            <th>Club / Venue</th>
                            <th>Submission Payload Preview</th>
                            <th>Submitter IP</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($submissions as $sub)
                            <tr>
                                <td><strong class="text-white">#{{ $sub->id }}</strong></td>
                                <td>
                                    <i class="bx bx-time me-1 text-primary"></i>
                                    {{ $sub->created_at ? $sub->created_at->format('M d, Y h:i:s A') : '-' }}
                                </td>
                                <td>
                                    @if($sub->website)
                                        <span class="badge bg-label-primary">{{ $sub->website->name }}</span>
                                    @else
                                        <span class="badge bg-label-secondary">General</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small text-truncate" style="max-width: 320px;">
                                        @foreach(($sub->submission_data ?: []) as $k => $v)
                                            <span class="badge bg-label-info me-1">
                                                <strong>{{ ucfirst(str_replace('_', ' ', $k)) }}:</strong> 
                                                {{ is_array($v) ? implode(', ', $v) : Str::limit((string)$v, 20) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td><span class="font-monospace small text-muted">{{ $sub->submitter_ip ?: '127.0.0.1' }}</span></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-info view-sub-btn" 
                                            data-id="{{ $sub->id }}" 
                                            data-date="{{ $sub->created_at ? $sub->created_at->format('M d, Y h:i A') : '' }}"
                                            data-ip="{{ $sub->submitter_ip }}"
                                            data-club="{{ $sub->website ? $sub->website->name : 'General' }}"
                                            data-payload="{{ json_encode($sub->submission_data) }}">
                                        <i class="bx bx-show me-1"></i> Inspect Data
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bx bx-inbox fs-1 d-block mb-2"></i>
                                    No submissions received for this form yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($submissions->hasPages())
                <div class="card-footer">
                    {{ $submissions->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Modal Inspection Payload -->
<div class="modal fade" id="payloadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-white"><i class="bx bx-receipt me-2"></i>Submission Details <span id="modalSubId"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3 p-2 rounded bg-secondary bg-opacity-25 small">
                    <div><strong>Submitted At:</strong> <span id="modalDate"></span></div>
                    <div><strong>Club / Venue:</strong> <span id="modalClub"></span></div>
                    <div><strong>IP Address:</strong> <span id="modalIp"></span></div>
                </div>

                <h6 class="text-white mb-2"><i class="bx bx-list-ul me-1"></i>Form Answers</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-white">
                        <thead>
                            <tr class="table-dark">
                                <th style="width: 35%;">Field Name</th>
                                <th>Submitted Value</th>
                            </tr>
                        </thead>
                        <tbody id="modalPayloadTable">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('payloadModal'));
    document.querySelectorAll('.view-sub-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const date = this.getAttribute('data-date');
            const ip = this.getAttribute('data-ip');
            const club = this.getAttribute('data-club');
            const payload = JSON.parse(this.getAttribute('data-payload') || '{}');

            document.getElementById('modalSubId').innerText = '#' + id;
            document.getElementById('modalDate').innerText = date;
            document.getElementById('modalIp').innerText = ip || '127.0.0.1';
            document.getElementById('modalClub').innerText = club;

            const tbody = document.getElementById('modalPayloadTable');
            tbody.innerHTML = '';

            for (const [key, val] of Object.entries(payload)) {
                const tr = document.createElement('tr');
                let displayVal = val;
                if (Array.isArray(val)) {
                    displayVal = val.join(', ');
                } else if (typeof val === 'string' && (val.startsWith('http://') || val.startsWith('https://'))) {
                    displayVal = `<a href="${val}" target="_blank" class="btn btn-xs btn-outline-info"><i class="bx bx-download me-1"></i> View Attachment</a>`;
                }

                tr.innerHTML = `
                    <td class="fw-bold text-info">${key.replace(/_/g, ' ').toUpperCase()}</td>
                    <td>${displayVal}</td>
                `;
                tbody.appendChild(tr);
            }

            modal.show();
        });
    });
});
</script>
@endsection

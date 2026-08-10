@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        @php
            $fieldMap = [];
            foreach (($form->fields_schema ?: []) as $f) {
                $key = $f['name'] ?? $f['id'] ?? null;
                if ($key) {
                    $fieldMap[$key] = $f['label'] ?? $key;
                }
            }
        @endphp

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h4 class="mb-1 text-white fw-bold"><i class="bx bx-receipt me-2 text-primary"></i>Submissions for: {{ $form->title }}</h4>
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
        <div class="card bg-dark text-white border-secondary">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle">
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
                                    <div class="d-flex flex-wrap gap-1.5" style="max-width: 450px;">
                                        @foreach(($sub->submission_data ?: []) as $k => $v)
                                            @php
                                                $fieldLabel = $fieldMap[$k] ?? ucfirst(str_replace(['field_', '_'], ['', ' '], $k));
                                            @endphp
                                            <span class="badge bg-dark border border-secondary text-white py-1 px-2.5 font-sans me-1 mb-1 d-inline-flex align-items-center gap-1" style="font-size:0.78rem;">
                                                <strong class="text-primary">{{ $fieldLabel }}:</strong> 
                                                <span class="text-light">{{ is_array($v) ? implode(', ', $v) : Str::limit((string)$v, 25) }}</span>
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
                                            data-payload="{{ json_encode($sub->submission_data) }}"
                                            data-fieldmap="{{ json_encode($fieldMap) }}">
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
                <div class="card-footer border-top border-secondary">
                    {{ $submissions->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Modal Inspection Payload -->
<div class="modal fade" id="payloadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-white"><i class="bx bx-receipt me-2 text-primary"></i>Submission Details <span id="modalSubId" class="text-primary fw-bold"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap justify-content-between mb-3 p-3 rounded bg-secondary bg-opacity-25 small">
                    <div><strong class="text-muted">Submitted At:</strong> <span id="modalDate" class="text-white"></span></div>
                    <div><strong class="text-muted">Club / Venue:</strong> <span id="modalClub" class="text-white"></span></div>
                    <div><strong class="text-muted">IP Address:</strong> <span id="modalIp" class="font-monospace text-white"></span></div>
                </div>

                <h6 class="text-white mb-3 fw-bold"><i class="bx bx-list-ul me-1 text-primary"></i>Submitted Field Answers</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-dark table-sm text-white">
                        <thead>
                            <tr class="table-secondary text-dark">
                                <th style="width: 40%;">Field Label</th>
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
            const fieldMap = JSON.parse(this.getAttribute('data-fieldmap') || '{}');

            document.getElementById('modalSubId').innerText = '#' + id;
            document.getElementById('modalDate').innerText = date;
            document.getElementById('modalIp').innerText = ip || '127.0.0.1';
            document.getElementById('modalClub').innerText = club;

            const tbody = document.getElementById('modalPayloadTable');
            tbody.innerHTML = '';

            for (const [key, val] of Object.entries(payload)) {
                const label = fieldMap[key] || key.replace(/field_/g, '').replace(/_/g, ' ');
                const tr = document.createElement('tr');
                let displayVal = val;
                if (Array.isArray(val)) {
                    displayVal = val.join(', ');
                } else if (typeof val === 'string' && (val.startsWith('http://') || val.startsWith('https://'))) {
                    displayVal = `<a href="${val}" target="_blank" class="btn btn-xs btn-outline-info"><i class="bx bx-download me-1"></i> View Attachment</a>`;
                }

                tr.innerHTML = `
                    <td class="fw-bold text-info">${label}</td>
                    <td class="text-white">${displayVal}</td>
                `;
                tbody.appendChild(tr);
            }

            modal.show();
        });
    });
});
</script>
@endsection

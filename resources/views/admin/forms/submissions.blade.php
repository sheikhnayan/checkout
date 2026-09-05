@extends(request()->routeIs('admin.nightly-reports.*') ? 'admin.nightly-reports.layout' : 'admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">
        
        @php
            $isNightly = request()->routeIs('admin.nightly-reports.*');
            $formRoutePrefix = $isNightly ? 'admin.nightly-reports.forms.' : 'admin.forms.';
            $fieldMap = [];
            foreach (($form->fields_schema ?: []) as $f) {
                $key = $f['name'] ?? $f['id'] ?? null;
                $type = $f['type'] ?? '';
                if ($key && $type !== 'captcha' && $type !== 'heading' && $type !== 'paragraph') {
                    $fieldMap[$key] = $f['label'] ?? $key;
                }
            }
        @endphp

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h4 class="mb-1 text-white fw-bold"><i class="bx bx-receipt me-2 text-primary"></i>Submissions for: {{ $form->title }}</h4>
                <p class="text-muted mb-0 small">Viewing all submitted data records collected through this form.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <button type="button" id="btnExportSelected" class="btn btn-success d-none" data-base-url="{{ route($formRoutePrefix . 'submissions.export', $form->id) }}">
                    <i class="bx bx-download me-1"></i> Export Selected (<span id="selectedCount">0</span>)
                </button>
                <a href="{{ route($formRoutePrefix . 'submissions.export', $form->id) }}" id="btnExportAll" class="btn btn-outline-success">
                    <i class="bx bx-download me-1"></i> Export All to CSV
                </a>
                <a href="{{ route($formRoutePrefix . 'index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-left-arrow-alt me-1"></i> Back to Forms
                </a>
            </div>
        </div>

        <!-- Submissions Table Card -->
        <div class="card bg-dark text-white border-secondary">
            <div class="table-responsive text-nowrap" style="overflow-x: auto !important; -webkit-overflow-scrolling: touch;">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="table-dark">
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" id="selectAllSubmissions" class="form-check-input cursor-pointer" title="Select All Submissions">
                            </th>
                            <th># ID</th>
                            <th>Submitted Date</th>
                            <th>Club / Venue</th>
                            <th>Submitter IP</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($submissions as $sub)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" value="{{ $sub->id }}" class="form-check-input sub-checkbox cursor-pointer">
                                </td>
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
                    <table class="table table-bordered table-dark table-sm text-white align-middle">
                        <thead>
                            <tr class="table-secondary text-dark">
                                <th style="width: 38%;">Field Label</th>
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
    const selectAllCb = document.getElementById('selectAllSubmissions');
    const btnExportSelected = document.getElementById('btnExportSelected');
    const selectedCountSpan = document.getElementById('selectedCount');

    function updateSelectionUI() {
        const subCbs = document.querySelectorAll('.sub-checkbox');
        const checkedCbs = document.querySelectorAll('.sub-checkbox:checked');
        const count = checkedCbs.length;

        if (selectedCountSpan) selectedCountSpan.innerText = count;

        if (count > 0) {
            if (btnExportSelected) btnExportSelected.classList.remove('d-none');
        } else {
            if (btnExportSelected) btnExportSelected.classList.add('d-none');
        }

        if (selectAllCb && subCbs.length > 0) {
            selectAllCb.checked = (count === subCbs.length);
        }
    }

    if (selectAllCb) {
        selectAllCb.addEventListener('change', function() {
            document.querySelectorAll('.sub-checkbox').forEach(cb => {
                cb.checked = selectAllCb.checked;
            });
            updateSelectionUI();
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('sub-checkbox')) {
            updateSelectionUI();
        }
    });

    if (btnExportSelected) {
        btnExportSelected.addEventListener('click', function() {
            const checkedCbs = Array.from(document.querySelectorAll('.sub-checkbox:checked'));
            const ids = checkedCbs.map(cb => cb.value);
            if (ids.length === 0) {
                alert('Please select at least one submission to export.');
                return;
            }
            const baseUrl = this.getAttribute('data-base-url');
            window.location.href = baseUrl + '?ids=' + ids.join(',');
        });
    }

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

            for (const [key, rawVal] of Object.entries(payload)) {
                // Skip captcha fields
                if (key.toLowerCase().includes('captcha') || key === '_hp_security_check' || key === '_form_render_timestamp') {
                    continue;
                }

                const label = fieldMap[key] || key.replace(/field_/g, '').replace(/_/g, ' ');
                const tr = document.createElement('tr');

                // Format values properly (never display [object Object])
                let displayHtml = '';
                if (rawVal === null || rawVal === undefined || rawVal === '') {
                    displayHtml = '<span class="text-muted italic">- Empty -</span>';
                } else if (typeof rawVal === 'object') {
                    if (Array.isArray(rawVal)) {
                        displayHtml = rawVal.map(v => typeof v === 'object' ? Object.values(v).filter(Boolean).join(' ') : v).filter(Boolean).join(', ');
                    } else {
                        displayHtml = Object.values(rawVal).filter(Boolean).join(' ');
                    }
                } else {
                    displayHtml = String(rawVal);
                }

                // Check if value contains File Upload URL(s)
                if (typeof displayHtml === 'string' && (displayHtml.includes('/storage/') || displayHtml.startsWith('http://') || displayHtml.startsWith('https://') || /\.(pdf|png|jpg|jpeg|doc|docx)$/i.test(displayHtml))) {
                    const urls = displayHtml.split(',').map(u => u.trim()).filter(Boolean);
                    if (urls.length > 0 && (urls[0].includes('/') || urls[0].startsWith('http'))) {
                        displayHtml = urls.map(url => {
                            const fileName = url.split('/').pop() || 'Attachment File';
                            return `<a href="${url}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary me-2 mb-1">
                                <i class="bx bx-file me-1"></i> ${fileName} <i class="bx bx-export ms-1 micro-text"></i>
                            </a>`;
                        }).join('');
                    }
                }

                tr.innerHTML = `
                    <td class="fw-bold text-info">${label}</td>
                    <td class="text-white">${displayHtml}</td>
                `;
                tbody.appendChild(tr);
            }

            modal.show();
        });
    });
});
</script>
@endsection

<div style="display: flex; flex-direction: column; height: 100%;">
    <!-- Tab Navigation -->
    <div style="display: flex; gap: 10px; padding: 20px; background: rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.1);">
        <button type="button" class="w9-tab-btn" data-tab="details" style="padding: 10px 20px; background: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;">
            <i class="fas fa-file-alt"></i> Submission Details
        </button>
        <button type="button" class="w9-tab-btn" data-tab="pdf" style="padding: 10px 20px; background: transparent; color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.2); border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;">
            <i class="fas fa-file-pdf"></i> Form W-9
        </button>
    </div>

    <!-- Tab Content -->
    <div style="flex: 1; overflow-y: auto;">

        <!-- Details Tab -->
        <div id="w9-details-tab" style="display: block;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; padding: 20px;">
                <!-- Left Column -->
                <div>
                    <div style="margin-bottom: 20px;">
                        <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">📝 Form Status</label>
                        <p style="font-weight: 600;">✓ W-9 Form Submitted</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">📅 Submitted On</label>
                        <p style="font-weight: 600;">{{ $w9Form->created_at->format('M d, Y h:i A') }}</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">🆔 ID Document Type</label>
                        <p style="font-weight: 600;">{{ ucwords(str_replace('_', ' ', $w9Form->id_document_type)) }}</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">✅ Certification</label>
                        <p style="font-weight: 600;">{{ $w9Form->certification_signed ? '✓ Signed' : 'Pending' }}</p>
                    </div>

                    @if($w9Form->certification_date)
                    <div style="margin-bottom: 20px;">
                        <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">🕐 Certified On</label>
                        <p style="font-weight: 600;">{{ $w9Form->certification_date->format('M d, Y h:i A') }}</p>
                    </div>
                    @endif

                    @if($w9Form->certification_ip)
                    <div style="margin-bottom: 20px;">
                        <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">🌐 Submission IP</label>
                        <p style="font-weight: 600; font-family: monospace;">{{ $w9Form->certification_ip }}</p>
                    </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div>
                    <div style="margin-bottom: 20px;">
                        <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">📋 Review Status</label>
                        <p style="font-weight: 600;">
                            @if($w9Form->status === 'approved')
                                <span class="badge bg-success">✓ Approved</span>
                            @elseif($w9Form->status === 'submitted')
                                <span class="badge bg-warning text-dark">⏳ Pending Review</span>
                            @elseif($w9Form->status === 'rejected')
                                <span class="badge bg-danger">✗ Rejected</span>
                            @else
                                <span class="badge bg-secondary">◯ Not Started</span>
                            @endif
                        </p>
                    </div>

                    @if($w9Form->reviewed_by && $w9Form->reviewed_at)
                    <div style="margin-bottom: 20px;">
                        <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Reviewed By</label>
                        <p style="font-weight: 600;">{{ $w9Form->reviewedBy?->name ?? 'System' }} on {{ $w9Form->reviewed_at->format('M d, Y') }}</p>
                    </div>
                    @endif

                    @if($w9Form->admin_notes)
                    <div style="margin-bottom: 20px;">
                        <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Admin Notes</label>
                        <p style="font-weight: 600; color: #fbbf24;">{{ $w9Form->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- ID Documents -->
            <div style="border-top: 1px solid rgba(255,255,255,0.1); padding: 20px;">
                <h6 style="font-weight: 700; margin-bottom: 15px;">Government-Issued ID Documents</h6>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    @if($w9Form->id_front_image)
                    <div>
                        <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase; display: block; margin-bottom: 10px;">ID Front</label>
                        <img src="{{ asset('storage/' . $w9Form->id_front_image) }}" alt="ID Front" style="max-width: 100%; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                        <a href="{{ asset('storage/' . $w9Form->id_front_image) }}" target="_blank" style="display: inline-block; margin-top: 8px; color: #3b82f6; text-decoration: none; font-size: 13px;">
                            <i class="fas fa-external-link-alt"></i> Open in new tab
                        </a>
                    </div>
                    @endif

                    @if($w9Form->id_back_image)
                    <div>
                        <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase; display: block; margin-bottom: 10px;">ID Back</label>
                        <img src="{{ asset('storage/' . $w9Form->id_back_image) }}" alt="ID Back" style="max-width: 100%; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                        <a href="{{ asset('storage/' . $w9Form->id_back_image) }}" target="_blank" style="display: inline-block; margin-top: 8px; color: #3b82f6; text-decoration: none; font-size: 13px;">
                            <i class="fas fa-external-link-alt"></i> Open in new tab
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- PDF Tab -->
        <div id="w9-pdf-tab" style="display: none; height: 100%;">
            <iframe src="{{ asset('fw9.pdf') }}" style="width: 100%; height: 100%; border: none;"></iframe>
        </div>

    </div>
</div>

<script>
// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    var tabButtons = document.querySelectorAll('.w9-tab-btn');
    var detailsTab = document.getElementById('w9-details-tab');
    var pdfTab = document.getElementById('w9-pdf-tab');

    tabButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tabName = this.getAttribute('data-tab');

            // Update button styles
            tabButtons.forEach(function(b) {
                b.style.background = 'transparent';
                b.style.color = 'rgba(255,255,255,0.7)';
                b.style.border = '1px solid rgba(255,255,255,0.2)';
            });
            this.style.background = '#0066cc';
            this.style.color = 'white';
            this.style.border = 'none';

            // Show/hide tabs
            if (tabName === 'details') {
                detailsTab.style.display = 'block';
                pdfTab.style.display = 'none';
            } else if (tabName === 'pdf') {
                detailsTab.style.display = 'none';
                pdfTab.style.display = 'block';
            }
        });
    });
});
</script>

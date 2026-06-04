<div style="max-height: 600px; overflow-y: auto;">
    <div style="padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; gap: 10px; justify-content: flex-end;">
        <button type="button" class="btn btn-primary" style="display: inline-block; padding: 8px 16px; background: #0066cc; color: white; text-decoration: none; border-radius: 4px; font-size: 13px; border: 1px solid #0066cc; cursor: pointer;" onclick="window.openW9PdfViewer && window.openW9PdfViewer()">
            <i class="fas fa-file-pdf"></i> View & Download PDF
        </button>
    </div>
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

            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Certification Date</label>
                <p style="font-weight: 600;">{{ $w9Form->certification_date?->format('M d, Y h:i A') ?? 'Not certified' }}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Status</label>
                <p style="font-weight: 600;">
                    @if($w9Form->status === 'approved')
                        <span class="badge bg-success">Approved</span>
                    @elseif($w9Form->status === 'submitted')
                        <span class="badge bg-warning text-dark">Submitted - Pending Review</span>
                    @elseif($w9Form->status === 'rejected')
                        <span class="badge bg-danger">Rejected</span>
                    @else
                        <span class="badge bg-secondary">Pending</span>
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

<!-- PDF Viewer Modal -->
<div id="w9PdfViewerModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 10000; padding: 20px;">
    <div style="position: relative; width: 100%; height: 100%; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #222; border-radius: 4px 4px 0 0;">
            <h5 style="margin: 0; color: white; font-size: 16px;">Form W-9 - {{ $w9Form->id_document_type ? ucwords(str_replace('_', ' ', $w9Form->id_document_type)) : 'Submitted' }}</h5>
            <button type="button" class="w9-pdf-close" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px;">×</button>
        </div>
        <iframe id="w9PdfViewerFrame" src="{{ asset('fw9.pdf') }}" style="flex: 1; border: none; border-radius: 0 0 4px 4px;"></iframe>
    </div>
</div>

<script>
// Ensure functions are globally accessible
if (typeof window.openW9PdfViewer === 'undefined') {
    window.openW9PdfViewer = function() {
        var modal = document.getElementById('w9PdfViewerModal');
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeW9PdfViewer = function() {
        var modal = document.getElementById('w9PdfViewerModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    };
}

// Setup event listeners
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('w9PdfViewerModal');
    if (modal) {
        // Close button
        var closeBtn = modal.querySelector('.w9-pdf-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', window.closeW9PdfViewer);
        }

        // Click outside to close
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                window.closeW9PdfViewer();
            }
        });
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var modal = document.getElementById('w9PdfViewerModal');
        if (modal && modal.style.display !== 'none') {
            window.closeW9PdfViewer();
        }
    }
});
</script>

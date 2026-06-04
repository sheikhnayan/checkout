<style>
.w9-modal-container {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 100px);
    width: 100%;
}

.w9-details-section {
    flex: 0 0 auto;
    max-height: 40%;
    overflow-y: auto;
    padding: 20px;
    border-bottom: 2px solid rgba(255,255,255,0.1);
}

.w9-pdf-section {
    flex: 1;
    overflow: hidden;
    display: flex;
    min-height: 300px;
}

.w9-pdf-section iframe {
    width: 100%;
    height: 100%;
    border: none;
}

.w9-field {
    margin-bottom: 15px;
}

.w9-field-label {
    color: rgba(255,255,255,0.5);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 700;
    margin-bottom: 5px;
}

.w9-field-value {
    color: white;
    font-weight: 600;
    font-size: 14px;
}

.w9-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
</style>

<div class="w9-modal-container">

    <!-- Submission Details Section - Full Width -->
    <div class="w9-details-section">
        <h5 style="color: white; margin-bottom: 20px; margin-top: 0;">W-9 Submission Details</h5>

        <div class="w9-grid">
            <!-- Left Column -->
            <div>
                <div class="w9-field">
                    <div class="w9-field-label">📝 Form Status</div>
                    <div class="w9-field-value">✓ W-9 Form Submitted</div>
                </div>

                <div class="w9-field">
                    <div class="w9-field-label">📅 Submitted On</div>
                    <div class="w9-field-value">{{ $w9Form->created_at->format('M d, Y h:i A') }}</div>
                </div>

                <div class="w9-field">
                    <div class="w9-field-label">🆔 ID Document Type</div>
                    <div class="w9-field-value">{{ ucwords(str_replace('_', ' ', $w9Form->id_document_type ?: 'Not specified')) }}</div>
                </div>

                <div class="w9-field">
                    <div class="w9-field-label">✅ Certification</div>
                    <div class="w9-field-value">{{ $w9Form->certification_signed ? '✓ Certified' : 'Not certified' }}</div>
                </div>

                @if($w9Form->certification_date)
                <div class="w9-field">
                    <div class="w9-field-label">🕐 Certified On</div>
                    <div class="w9-field-value">{{ $w9Form->certification_date->format('M d, Y h:i A') }}</div>
                </div>
                @endif

                @if($w9Form->certification_ip)
                <div class="w9-field">
                    <div class="w9-field-label">🌐 Submission IP</div>
                    <div class="w9-field-value" style="font-family: monospace;">{{ $w9Form->certification_ip }}</div>
                </div>
                @endif

                <div class="w9-field">
                    <div class="w9-field-label">📋 Review Status</div>
                    <div class="w9-field-value">
                        @if($w9Form->status === 'approved')
                            <span style="background: #10b981; padding: 4px 8px; border-radius: 3px;">✓ Approved</span>
                        @elseif($w9Form->status === 'submitted')
                            <span style="background: #f59e0b; padding: 4px 8px; border-radius: 3px; color: black;">⏳ Pending Review</span>
                        @elseif($w9Form->status === 'rejected')
                            <span style="background: #ef4444; padding: 4px 8px; border-radius: 3px;">✗ Rejected</span>
                        @else
                            <span style="background: #6b7280; padding: 4px 8px; border-radius: 3px;">◯ Pending</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                @if($w9Form->id_front_image)
                <div class="w9-field">
                    <div class="w9-field-label">📸 ID Front Image</div>
                    <div class="w9-field-value">
                        <img src="{{ asset('storage/' . $w9Form->id_front_image) }}" alt="ID Front" style="max-width: 100%; max-height: 100px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.2); margin-bottom: 8px;">
                        <br>
                        <a href="{{ asset('storage/' . $w9Form->id_front_image) }}" target="_blank" style="color: #3b82f6; text-decoration: none; font-size: 12px;">
                            <i class="fas fa-external-link-alt"></i> Open full size
                        </a>
                    </div>
                </div>
                @endif

                @if($w9Form->id_back_image)
                <div class="w9-field">
                    <div class="w9-field-label">📸 ID Back Image</div>
                    <div class="w9-field-value">
                        <img src="{{ asset('storage/' . $w9Form->id_back_image) }}" alt="ID Back" style="max-width: 100%; max-height: 100px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.2); margin-bottom: 8px;">
                        <br>
                        <a href="{{ asset('storage/' . $w9Form->id_back_image) }}" target="_blank" style="color: #3b82f6; text-decoration: none; font-size: 12px;">
                            <i class="fas fa-external-link-alt"></i> Open full size
                        </a>
                    </div>
                </div>
                @endif

                @if($w9Form->reviewed_by && $w9Form->reviewed_at)
                <div class="w9-field">
                    <div class="w9-field-label">👤 Reviewed By</div>
                    <div class="w9-field-value">{{ $w9Form->reviewedBy?->name ?? 'System' }} on {{ $w9Form->reviewed_at->format('M d, Y') }}</div>
                </div>
                @endif

                @if($w9Form->admin_notes)
                <div class="w9-field">
                    <div class="w9-field-label">📝 Admin Notes</div>
                    <div class="w9-field-value" style="color: #fbbf24;">{{ $w9Form->admin_notes }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- PDF Section - Full Height -->
    <div class="w9-pdf-section">
        <iframe src="{{ asset('fw9.pdf') }}" style="width: 100%; height: 100%; border: none;"></iframe>
    </div>

</div>

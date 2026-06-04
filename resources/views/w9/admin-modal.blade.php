<style>
.w9-modal-container {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 100px);
    width: 100%;
    background: #1a1a2e;
}

.w9-details-section {
    flex: 0 0 auto;
    max-height: 45%;
    overflow-y: auto;
    padding: 20px;
    border-bottom: 2px solid rgba(255,255,255,0.1);
    background: #16213e;
}

.w9-pdf-section {
    flex: 1;
    overflow: hidden;
    display: flex;
    min-height: 300px;
    background: white;
}

.w9-pdf-section iframe {
    width: 100%;
    height: 100%;
    border: none;
}

.w9-field {
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.w9-field-label {
    color: rgba(255,255,255,0.4);
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    font-weight: 700;
    margin-bottom: 8px;
    display: block;
}

.w9-field-value {
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    word-break: break-word;
    padding: 8px 12px;
    background: rgba(255,255,255,0.03);
    border-left: 3px solid #0066cc;
    border-radius: 2px;
}

.w9-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

.w9-title {
    color: #0066cc;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 20px;
    margin-top: 0;
}

.w9-status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.w9-status-approved {
    background: #10b981;
    color: white;
}

.w9-status-pending {
    background: #f59e0b;
    color: #000;
}

.w9-status-rejected {
    background: #ef4444;
    color: white;
}

.w9-id-image {
    max-width: 100%;
    max-height: 120px;
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 4px;
    margin: 8px 0;
}

.w9-warning {
    background: #d97706;
    color: white;
    padding: 12px;
    border-radius: 4px;
    font-size: 12px;
    margin-bottom: 15px;
    border-left: 4px solid #dc2626;
}
</style>

<div class="w9-modal-container">

    <!-- Submission Details Section -->
    <div class="w9-details-section">
        <h5 class="w9-title">✓ W-9 Submission Details (READ-ONLY)</h5>

        <div class="w9-grid">
            <!-- Left Column -->
            <div>
                <div class="w9-field">
                    <span class="w9-field-label">👤 Full Name</span>
                    <div class="w9-field-value">{{ $w9Form->full_name ?? 'Not provided' }}</div>
                </div>

                <div class="w9-field">
                    <span class="w9-field-label">🆔 Tax ID/SSN</span>
                    <div class="w9-field-value">{{ $w9Form->tax_id_number ?? 'Not provided' }}</div>
                </div>

                <div class="w9-field">
                    <span class="w9-field-label">📍 Street Address</span>
                    <div class="w9-field-value">{{ $w9Form->street_address ?? 'Not provided' }}</div>
                </div>

                <div class="w9-field">
                    <span class="w9-field-label">🏙️ City, State, ZIP</span>
                    <div class="w9-field-value">
                        @if($w9Form->city_state_zip)
                            {{ $w9Form->city_state_zip }}
                        @else
                            Not provided
                        @endif
                    </div>
                </div>

                <div class="w9-field">
                    <span class="w9-field-label">📝 ID Document Type</span>
                    <div class="w9-field-value">{{ ucwords(str_replace('_', ' ', $w9Form->id_document_type ?? 'Not specified')) }}</div>
                </div>

                <div class="w9-field">
                    <span class="w9-field-label">📅 Submitted On</span>
                    <div class="w9-field-value">{{ $w9Form->created_at ? $w9Form->created_at->format('M d, Y h:i A') : 'N/A' }}</div>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <div class="w9-field">
                    <span class="w9-field-label">📋 Review Status</span>
                    <div class="w9-field-value">
                        @if($w9Form->status === 'approved')
                            <span class="w9-status-badge w9-status-approved">✓ APPROVED</span>
                        @elseif($w9Form->status === 'submitted')
                            <span class="w9-status-badge w9-status-pending">⏳ PENDING REVIEW</span>
                        @elseif($w9Form->status === 'rejected')
                            <span class="w9-status-badge w9-status-rejected">✗ REJECTED</span>
                        @else
                            <span class="w9-status-badge" style="background: #6b7280; color: white;">◯ PENDING</span>
                        @endif
                    </div>
                </div>

                <div class="w9-field">
                    <span class="w9-field-label">✅ Certification</span>
                    <div class="w9-field-value">{{ $w9Form->certification_signed ? '✓ Certified' : '✗ Not Certified' }}</div>
                </div>

                @if($w9Form->certification_date)
                <div class="w9-field">
                    <span class="w9-field-label">🕐 Certified On</span>
                    <div class="w9-field-value">{{ $w9Form->certification_date->format('M d, Y h:i A') }}</div>
                </div>
                @endif

                @if($w9Form->certification_ip)
                <div class="w9-field">
                    <span class="w9-field-label">🌐 Submission IP Address</span>
                    <div class="w9-field-value" style="font-family: 'Courier New', monospace;">{{ $w9Form->certification_ip }}</div>
                </div>
                @endif

                @if($w9Form->reviewed_by && $w9Form->reviewed_at)
                <div class="w9-field">
                    <span class="w9-field-label">👤 Reviewed By</span>
                    <div class="w9-field-value">{{ $w9Form->reviewedBy?->name ?? 'System' }} on {{ $w9Form->reviewed_at->format('M d, Y') }}</div>
                </div>
                @endif

                @if($w9Form->admin_notes)
                <div class="w9-field">
                    <span class="w9-field-label">📝 Admin Notes</span>
                    <div class="w9-field-value" style="color: #fbbf24;">{{ $w9Form->admin_notes }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- ID Documents -->
        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1);">
            <h6 style="color: #0066cc; font-weight: 700; margin: 0 0 15px 0; font-size: 12px; text-transform: uppercase;">Government-Issued ID Documents</h6>
            <div class="w9-grid">
                @if($w9Form->id_front_image)
                <div class="w9-field">
                    <span class="w9-field-label">📸 ID Front</span>
                    <img src="{{ asset('storage/' . $w9Form->id_front_image) }}" alt="ID Front" class="w9-id-image">
                    <a href="{{ asset('storage/' . $w9Form->id_front_image) }}" target="_blank" style="color: #3b82f6; text-decoration: none; font-size: 12px;">
                        <i class="fas fa-external-link-alt"></i> Open full size
                    </a>
                </div>
                @endif

                @if($w9Form->id_back_image)
                <div class="w9-field">
                    <span class="w9-field-label">📸 ID Back</span>
                    <img src="{{ asset('storage/' . $w9Form->id_back_image) }}" alt="ID Back" class="w9-id-image">
                    <a href="{{ asset('storage/' . $w9Form->id_back_image) }}" target="_blank" style="color: #3b82f6; text-decoration: none; font-size: 12px;">
                        <i class="fas fa-external-link-alt"></i> Open full size
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- PDF Section -->
    <div class="w9-pdf-section">
        <iframe src="{{ asset('fw9.pdf') }}" title="IRS Form W-9"></iframe>
    </div>

</div>

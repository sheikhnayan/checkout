<div style="display: flex; flex-direction: column; max-height: 600px;">

    <!-- Submission Details Section -->
    <div style="flex: 0 0 auto; overflow-y: auto;">
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

    <!-- PDF Section at Bottom -->
    <div style="flex: 1; border-top: 1px solid rgba(255,255,255,0.1); overflow: hidden;">
        <iframe src="{{ asset('fw9.pdf') }}" style="width: 100%; height: 100%; border: none;"></iframe>
    </div>

</div>

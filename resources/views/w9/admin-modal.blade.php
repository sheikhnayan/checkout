<div style="max-height: 600px; overflow-y: auto;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; padding: 20px;">
        <!-- Left Column -->
        <div>
            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Full Name</label>
                <p style="font-weight: 600;">{{ $w9Form->full_name }}</p>
            </div>

            @if($w9Form->business_name)
            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Business Name / DBA</label>
                <p style="font-weight: 600;">{{ $w9Form->business_name }}</p>
            </div>
            @endif

            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Tax Classification</label>
                <p style="font-weight: 600;">{{ ucwords(str_replace('_', ' ', $w9Form->tax_classification)) }}</p>
            </div>

            @if($w9Form->tax_classification_other)
            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Specified Classification</label>
                <p style="font-weight: 600;">{{ $w9Form->tax_classification_other }}</p>
            </div>
            @endif

            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Tax ID Type</label>
                <p style="font-weight: 600;">{{ strtoupper($w9Form->tax_id_type) }}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Tax ID Number</label>
                <p style="font-weight: 600; font-family: monospace; letter-spacing: 2px;">{{ $w9Form->tax_id_number }}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Street Address</label>
                <p style="font-weight: 600;">{{ $w9Form->street_address }}</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
                <div>
                    <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">City</label>
                    <p style="font-weight: 600;">{{ $w9Form->city }}</p>
                </div>
                <div>
                    <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">State</label>
                    <p style="font-weight: 600;">{{ strtoupper($w9Form->state) }}</p>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">ZIP Code</label>
                <p style="font-weight: 600;">{{ $w9Form->zip_code }}</p>
            </div>

            @if($w9Form->account_numbers)
            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Account Numbers</label>
                <p style="font-weight: 600;">{{ $w9Form->account_numbers }}</p>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div>
            @if($w9Form->requester_name)
            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Requester Name</label>
                <p style="font-weight: 600;">{{ $w9Form->requester_name }}</p>
            </div>
            @endif

            @if($w9Form->requester_phone)
            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Requester Phone</label>
                <p style="font-weight: 600;">{{ $w9Form->requester_phone }}</p>
            </div>
            @endif

            @if($w9Form->requester_email)
            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Requester Email</label>
                <p style="font-weight: 600;">{{ $w9Form->requester_email }}</p>
            </div>
            @endif

            @if($w9Form->exempt_payee_code)
            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">Exempt Payee Code</label>
                <p style="font-weight: 600;">{{ $w9Form->exempt_payee_code }}</p>
            </div>
            @endif

            @if($w9Form->fatca_exemption_code)
            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">FATCA Exemption Code</label>
                <p style="font-weight: 600;">{{ $w9Form->fatca_exemption_code }}</p>
            </div>
            @endif

            <div style="margin-bottom: 20px;">
                <label style="color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">ID Document Type</label>
                <p style="font-weight: 600;">{{ ucwords(str_replace('_', ' ', $w9Form->id_document_type)) }}</p>
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

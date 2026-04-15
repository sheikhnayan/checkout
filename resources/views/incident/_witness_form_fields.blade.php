<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Legal Name of the Location *</label>
        <input type="text" name="location_legal_name" class="form-control" value="{{ old('location_legal_name', $incident->location_legal_name) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">The DBA of the Location *</label>
        <input type="text" name="location_dba_name" class="form-control" value="{{ old('location_dba_name', $incident->location_dba_name) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Address of Location *</label>
        <input type="text" name="location_address" class="form-control" value="{{ old('location_address', $incident->location_address) }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Calendar Date of incident *</label>
        <input type="date" name="incident_calendar_date" class="form-control" value="{{ old('incident_calendar_date', optional($incident->incident_calendar_date)->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Date Submitted *</label>
        <input type="date" name="date_submitted" class="form-control" value="{{ old('date_submitted', now('America/Los_Angeles')->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Time of incident *</label>
        <input type="time" name="incident_time" class="form-control" value="{{ old('incident_time', $incident->incident_time) }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Type of Incident *</label>
        <input type="text" name="incident_type" class="form-control" value="{{ old('incident_type') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Your Full Name (Legal Name) *</label>
        <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Your Address *</label>
        <input type="text" name="address" class="form-control" value="{{ old('address') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Your phone number *</label>
        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}" required>
    </div>

    <div class="col-md-12">
        <label class="form-label d-block mb-2">Type *</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="participant_type" id="ptype_witness" value="Witness" {{ old('participant_type', 'Witness') === 'Witness' ? 'checked' : '' }} required>
            <label class="form-check-label" for="ptype_witness">Witness</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="participant_type" id="ptype_involved" value="Involved Person" {{ old('participant_type') === 'Involved Person' ? 'checked' : '' }} required>
            <label class="form-check-label" for="ptype_involved">Involved Person</label>
        </div>
    </div>

    <div class="col-md-12">
        <label class="form-label">Detailed statement *</label>
        <textarea name="detailed_statement" class="form-control" rows="6" required>{{ old('detailed_statement') }}</textarea>
    </div>

    <div class="col-md-12">
        <label class="form-label">Upload photos/video evidence (optional, max 1 file, 4MB)</label>
        <input type="file" name="evidence_file" class="form-control">
    </div>

    <div class="col-md-12">
        <label class="form-label d-block mb-2">E-signature confirmation *</label>
        <div class="form-check mb-1">
            <input class="form-check-input" type="radio" name="signature_choice" id="w_sig_accept" value="accept" {{ old('signature_choice') === 'accept' ? 'checked' : '' }} required>
            <label class="form-check-label" for="w_sig_accept">I Accept</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="signature_choice" id="w_sig_opt_out" value="opt_out" {{ old('signature_choice') === 'opt_out' ? 'checked' : '' }} required>
            <label class="form-check-label" for="w_sig_opt_out">I opt out and will physically sign.</label>
        </div>
    </div>

    <div class="col-md-12">
        <label class="form-label">Your Digital Signature (full name) *</label>
        <input type="text" name="digital_signature_name" class="form-control" value="{{ old('digital_signature_name') }}" required>
    </div>
</div>

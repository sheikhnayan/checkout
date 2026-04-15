@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="app-main__inner">
            <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                <h4 class="mb-0">Create Incident - {{ $website->name }}</h4>
                <a href="{{ route('admin.incident.show', $websiteId) }}" class="btn btn-secondary">Back</a>
            </div>

            <div class="card bg-primary text-white p-3">
                <form method="POST" action="{{ route('admin.incident.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="website_id" value="{{ $websiteId }}">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Legal Name of the Location *</label>
                            <input type="text" name="location_legal_name" class="form-control" value="{{ old('location_legal_name', $website->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">The DBA of the Location *</label>
                            <input type="text" name="location_dba_name" class="form-control" value="{{ old('location_dba_name', $website->name) }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address of Location *</label>
                            <input type="text" name="location_address" class="form-control" value="{{ old('location_address') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Calendar Date of incident *</label>
                            <input type="date" name="incident_calendar_date" class="form-control" value="{{ old('incident_calendar_date') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date Submitted *</label>
                            <input type="date" name="date_submitted" class="form-control" value="{{ old('date_submitted', now('America/Los_Angeles')->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Time of incident *</label>
                            <input type="time" name="incident_time" class="form-control" value="{{ old('incident_time') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Police Report Number</label>
                            <input type="text" name="police_report_number" class="form-control" value="{{ old('police_report_number') }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Police Officers and Badge #'s</label>
                            <input type="text" name="police_officers_badges" class="form-control" value="{{ old('police_officers_badges') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Attach Police Report (max 4MB)</label>
                            <input type="file" name="police_report_file" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Your Name *</label>
                            <input type="text" name="reporter_name" class="form-control" value="{{ old('reporter_name') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Manager(s) on Duty *</label>
                            <input type="text" name="managers_on_duty" class="form-control" value="{{ old('managers_on_duty') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Manager's phone number</label>
                            <input type="text" name="manager_phone" class="form-control" value="{{ old('manager_phone') }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Involved / Injured Persons *</label>
                            <textarea name="involved_injured_persons" class="form-control" rows="3" required>{{ old('involved_injured_persons') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Detailed description of the incident *</label>
                            <textarea name="incident_description" class="form-control" rows="5" required>{{ old('incident_description') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Witnesses statement *</label>
                            <textarea name="witnesses_statement" class="form-control" rows="4" required>{{ old('witnesses_statement') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Load Witness Report Here (up to 10 files, 4MB each)</label>
                            <input type="file" name="witness_report_files[]" class="form-control" multiple>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Which cameras (angles) will show the incident? *</label>
                            <textarea name="camera_angles" class="form-control" rows="2" required>{{ old('camera_angles') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Timestamp of the camera *</label>
                            <input type="text" name="camera_timestamp" class="form-control" value="{{ old('camera_timestamp') }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Cast member involved or on duty? *</label>
                            <textarea name="cast_members_involved" class="form-control" rows="3" required>{{ old('cast_members_involved') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Any additional photos or recordings notes</label>
                            <textarea name="additional_media_notes" class="form-control" rows="3">{{ old('additional_media_notes') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Additional Cell Phone Photos or Footage (up to 5 files, 4MB each)</label>
                            <input type="file" name="additional_media_files[]" class="form-control" multiple>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label d-block mb-2">E-signature confirmation *</label>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="radio" name="signature_choice" id="sig_accept" value="accept" {{ old('signature_choice') === 'accept' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="sig_accept">I Accept</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="signature_choice" id="sig_opt_out" value="opt_out" {{ old('signature_choice') === 'opt_out' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="sig_opt_out">I opt out and will physically sign.</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Your Digital Signature (full name) *</label>
                            <input type="text" name="digital_signature_name" class="form-control" value="{{ old('digital_signature_name') }}" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Submit Incident Report</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

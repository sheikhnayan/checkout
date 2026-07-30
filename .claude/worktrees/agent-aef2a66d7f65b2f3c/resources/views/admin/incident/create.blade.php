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
                            <label class="form-label">Legal Name of the Location * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The officially registered business name of the venue."></i></label>
                            <input type="text" name="location_legal_name" class="form-control" value="{{ old('location_legal_name', $website->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">The DBA of the Location * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The 'Doing Business As' trade name if different from the legal name."></i></label>
                            <input type="text" name="location_dba_name" class="form-control" value="{{ old('location_dba_name', $website->name) }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address of Location * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Full street address where the incident occurred."></i></label>
                            <input type="text" name="location_address" class="form-control" value="{{ old('location_address') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Calendar Date of incident * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The exact date the incident took place."></i></label>
                            <input type="date" name="incident_calendar_date" class="form-control" value="{{ old('incident_calendar_date') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date Submitted * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The date this incident report is being filed."></i></label>
                            <input type="date" name="date_submitted" class="form-control" value="{{ old('date_submitted', now('America/Los_Angeles')->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Time of incident * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The time the incident occurred."></i></label>
                            <input type="time" name="incident_time" class="form-control" value="{{ old('incident_time') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Police Report Number <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Reference number of any associated police report."></i></label>
                            <input type="text" name="police_report_number" class="form-control" value="{{ old('police_report_number') }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Police Officers and Badge #'s <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Names and badge numbers of responding police officers."></i></label>
                            <input type="text" name="police_officers_badges" class="form-control" value="{{ old('police_officers_badges') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Attach Police Report (max 4MB) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload a scanned copy of the police report."></i></label>
                            <input type="file" name="police_report_file" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Your Name * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The name of the person filing this incident report."></i></label>
                            <input type="text" name="reporter_name" class="form-control" value="{{ old('reporter_name') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Manager(s) on Duty * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Names of all managers on duty at the time of the incident."></i></label>
                            <input type="text" name="managers_on_duty" class="form-control" value="{{ old('managers_on_duty') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Manager's phone number <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Contact phone number for the manager on duty."></i></label>
                            <input type="text" name="manager_phone" class="form-control" value="{{ old('manager_phone') }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Involved / Injured Persons * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Names and details of all persons involved or injured."></i></label>
                            <textarea name="involved_injured_persons" class="form-control" rows="3" required>{{ old('involved_injured_persons') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Detailed description of the incident * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="A thorough written account of exactly what happened."></i></label>
                            <textarea name="incident_description" class="form-control" rows="5" required>{{ old('incident_description') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Witnesses statement * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Written statements from any witnesses present."></i></label>
                            <textarea name="witnesses_statement" class="form-control" rows="4" required>{{ old('witnesses_statement') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Load Witness Report Here (up to 10 files, 4MB each) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload scanned witness statement documents."></i></label>
                            <input type="file" name="witness_report_files[]" class="form-control" multiple>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Which cameras (angles) will show the incident? * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Specify which camera views and angles cover the incident area."></i></label>
                            <textarea name="camera_angles" class="form-control" rows="2" required>{{ old('camera_angles') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Timestamp of the camera * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The time code on the camera footage corresponding to when the incident occurred."></i></label>
                            <input type="text" name="camera_timestamp" class="form-control" value="{{ old('camera_timestamp') }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Cast member involved or on duty? * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Names of any venue staff directly involved or on duty."></i></label>
                            <textarea name="cast_members_involved" class="form-control" rows="3" required>{{ old('cast_members_involved') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Any additional photos or recordings notes <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Any extra photos, footage notes, or relevant context."></i></label>
                            <textarea name="additional_media_notes" class="form-control" rows="3">{{ old('additional_media_notes') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Additional Cell Phone Photos or Footage (up to 5 files, 4MB each) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload supporting photos or video files from mobile devices."></i></label>
                            <input type="file" name="additional_media_files[]" class="form-control" multiple>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label d-block mb-2">E-signature confirmation * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Confirm that all information provided in this report is accurate and true."></i></label>
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
                            <label class="form-label">Your Digital Signature (full name) * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Type your full legal name as your digital signature."></i></label>
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

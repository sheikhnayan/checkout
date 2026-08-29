@extends(request()->routeIs('admin.nightly-reports.*') ? 'admin.nightly-reports.layout' : 'admin.main')

@section('content')
@php
  $isNightly = request()->routeIs('admin.nightly-reports.*');
  $indexRoute = $isNightly ? 'admin.nightly-reports.jobs.index' : 'admin.jobs.index';
  $storeRoute = $isNightly ? 'admin.nightly-reports.jobs.store' : 'admin.jobs.store';
@endphp
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 text-white">Create Job Post</h4>
            <a href="{{ route($indexRoute) }}" class="btn btn-outline-light">Back</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route($storeRoute) }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Club / Website <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The club or venue listing this job opportunity."></i></label>
                            <select name="website_id" class="form-select" required>
                                <option value="">Select club</option>
                                @foreach($websites as $website)
                                    <option value="{{ $website->id }}" {{ old('website_id') == $website->id ? 'selected' : '' }}>{{ $website->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Job Type <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The category of job (e.g. security, bartender, promoter, hostess)."></i></label>
                            <select name="job_type" class="form-select" required>
                                <option value="entertainer" {{ old('job_type') === 'entertainer' ? 'selected' : '' }}>Entertainer</option>
                                <option value="employee" {{ old('job_type') === 'employee' ? 'selected' : '' }}>Employee</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Live Status <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Whether this job listing is currently visible to applicants."></i></label>
                            <select name="status" class="form-select">
                                <option value="1" selected>Live</option>
                                <option value="0">Paused</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Job Title <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The title displayed in the job listing."></i></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">State <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="State where this job is based. Select State first to choose City."></i></label>
                            <select name="state" id="stateSelect" class="form-select">
                                <option value="">Select State</option>
                                @foreach($states as $st)
                                    <option value="{{ $st }}" {{ old('state') === $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">City <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="City where this job is based. Populated based on selected State."></i></label>
                            <select name="city" id="citySelect" class="form-select" disabled>
                                <option value="">Select State First</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Employment Type <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Full-time, Part-time, Freelance, or Contract."></i></label>
                            <select name="employment_type" class="form-select">
                                <option value="">Select Employment Type</option>
                                <option value="Full-time" {{ old('employment_type') === 'Full-time' ? 'selected' : '' }}>Full-time</option>
                                <option value="Part-time" {{ old('employment_type') === 'Part-time' ? 'selected' : '' }}>Part-time</option>
                                <option value="Freelance" {{ old('employment_type') === 'Freelance' ? 'selected' : '' }}>Freelance</option>
                                <option value="Contract" {{ old('employment_type') === 'Contract' ? 'selected' : '' }}>Contract</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Compensation <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Pay rate or compensation details for this role."></i></label>
                            <input type="text" name="compensation" class="form-control" value="{{ old('compensation') }}" placeholder="$25 / $200/night + tips">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Pay Frequency <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="How pay is calculated or paid."></i></label>
                            <select name="pay_frequency" class="form-select">
                                <option value="">Select Pay Frequency</option>
                                <option value="per_hour" {{ old('pay_frequency') === 'per_hour' ? 'selected' : '' }}>Per Hour</option>
                                <option value="per_year" {{ old('pay_frequency') === 'per_year' ? 'selected' : '' }}>Per Year</option>
                                <option value="other" {{ old('pay_frequency') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Short Description <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="A brief summary of the role shown in job search result cards."></i></label>
                            <textarea name="short_description" class="form-control" rows="2" required>{{ old('short_description') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Full Description <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The complete job description including responsibilities and requirements."></i></label>
                            <textarea name="description" class="form-control" rows="6" required>{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Suggested Traits (one per line) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Personality traits ideal for this role, one per line."></i></label>
                            <textarea name="traits_text" class="form-control" rows="6" placeholder="Outgoing&#10;Friendly&#10;Reliable">{{ old('traits_text') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Suggested Skills (one per line) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Professional or technical skills required for this role, one per line."></i></label>
                            <textarea name="skills_text" class="form-control" rows="6" placeholder="Sales&#10;Hospitality&#10;Event Planning">{{ old('skills_text') }}</textarea>
                        </div>

                        <!-- Notification Settings Card -->
                        <div class="col-12 mt-4">
                            <div class="card border-0 shadow-sm" style="background: rgba(15, 23, 42, 0.03); border: 1px solid #cbd5e1 !important;">
                                <div class="card-header bg-dark text-white fw-bold d-flex align-items-center justify-content-between">
                                    <span><i class="fas fa-bell text-warning me-2"></i> Application Email Notification Settings</span>
                                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25">Multi-Recipient Supported</span>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="notify_enabled" id="notifyEnabled" value="1" {{ old('notify_enabled', true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="notifyEnabled">Enable Email Notifications for New Applicants</label>
                                        <div class="text-muted small">Automatically send email alerts to managers when an application is submitted for this job.</div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Send To Email Address(es) <span class="text-danger">*</span></label>
                                            <textarea name="notify_send_to" class="form-control" rows="2" placeholder="admin@cartvip.com, sales@cartvip.com">{{ old('notify_send_to', 'admin@cartvip.com') }}</textarea>
                                            <span class="form-text text-muted small"><i class="fas fa-info-circle me-1 text-primary"></i>Enter one or multiple notification email addresses separated by commas (<code>,</code>), semicolons (<code>;</code>), or new lines.</span>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Email Subject Line</label>
                                            <input type="text" name="notify_subject" class="form-control" value="{{ old('notify_subject', 'New Application: {job_title}') }}" placeholder="New Application: {job_title}">
                                            <span class="form-text text-muted small">Supports placeholder <code>{job_title}</code></span>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">From Name</label>
                                            <input type="text" name="notify_from_name" class="form-control" value="{{ old('notify_from_name', 'CartVIP Job Portal') }}" placeholder="CartVIP Job Portal">
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">From Email</label>
                                            <input type="email" name="notify_from_email" class="form-control" value="{{ old('notify_from_email', 'no-reply@cartvip.com') }}" placeholder="no-reply@cartvip.com">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Create Job Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statesAndCities = {!! json_encode($statesAndCities) !!};
    const stateSelect = document.getElementById('stateSelect');
    const citySelect = document.getElementById('citySelect');
    const oldCity = "{!! old('city') !!}";

    function updateCities(selectedState, selectedCity = '') {
        citySelect.innerHTML = '<option value="">Select City</option>';
        if (selectedState && statesAndCities[selectedState]) {
            citySelect.disabled = false;
            statesAndCities[selectedState].forEach(c => {
                const opt = document.createElement('option');
                opt.value = c;
                opt.textContent = c;
                if (c === selectedCity) {
                    opt.selected = true;
                }
                citySelect.appendChild(opt);
            });
        } else {
            citySelect.disabled = true;
            citySelect.innerHTML = '<option value="">Select State First</option>';
        }
    }

    stateSelect.addEventListener('change', function() {
        updateCities(this.value);
    });

    if (stateSelect.value) {
        updateCities(stateSelect.value, oldCity);
    }
});
</script>
@endsection

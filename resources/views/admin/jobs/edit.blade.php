@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Edit Job Post</h4>
            <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-light">Back</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.jobs.update', $job) }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Club / Website <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The club or venue listing this job opportunity."></i></label>
                            <select name="website_id" class="form-select" required>
                                @foreach($websites as $website)
                                    <option value="{{ $website->id }}" {{ (old('website_id', $job->website_id) == $website->id) ? 'selected' : '' }}>{{ $website->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Job Type <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The category of job (e.g. security, bartender, promoter, hostess)."></i></label>
                            <select name="job_type" class="form-select" required>
                                <option value="entertainer" {{ old('job_type', $job->job_type) === 'entertainer' ? 'selected' : '' }}>Entertainer</option>
                                <option value="employee" {{ old('job_type', $job->job_type) === 'employee' ? 'selected' : '' }}>Employee</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Live Status <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Whether this job listing is currently visible to applicants."></i></label>
                            <select name="status" class="form-select">
                                <option value="1" {{ old('status', $job->status) ? 'selected' : '' }}>Live</option>
                                <option value="0" {{ !old('status', $job->status) ? 'selected' : '' }}>Paused</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Archive <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Move this listing to the archive to hide it without deleting."></i></label>
                            <select name="is_archived" class="form-select">
                                <option value="0" {{ !old('is_archived', $job->is_archived) ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('is_archived', $job->is_archived) ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Job Title <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The title displayed in the job listing."></i></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $job->title) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">State <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="State where this job is based. Select State first to choose City."></i></label>
                            <select name="state" id="stateSelect" class="form-select">
                                <option value="">Select State</option>
                                @foreach($states as $st)
                                    <option value="{{ $st }}" {{ old('state', $job->state) === $st ? 'selected' : '' }}>{{ $st }}</option>
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
                                <option value="Full-time" {{ old('employment_type', $job->employment_type) === 'Full-time' ? 'selected' : '' }}>Full-time</option>
                                <option value="Part-time" {{ old('employment_type', $job->employment_type) === 'Part-time' ? 'selected' : '' }}>Part-time</option>
                                <option value="Freelance" {{ old('employment_type', $job->employment_type) === 'Freelance' ? 'selected' : '' }}>Freelance</option>
                                <option value="Contract" {{ old('employment_type', $job->employment_type) === 'Contract' ? 'selected' : '' }}>Contract</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Compensation <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Pay rate or compensation details for this role."></i></label>
                            <input type="text" name="compensation" class="form-control" value="{{ old('compensation', $job->compensation) }}" placeholder="$25 / $200/night + tips">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Pay Frequency <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="How pay is calculated or paid."></i></label>
                            <select name="pay_frequency" class="form-select">
                                <option value="">Select Pay Frequency</option>
                                <option value="per_hour" {{ old('pay_frequency', $job->pay_frequency) === 'per_hour' ? 'selected' : '' }}>Per Hour</option>
                                <option value="per_year" {{ old('pay_frequency', $job->pay_frequency) === 'per_year' ? 'selected' : '' }}>Per Year</option>
                                <option value="other" {{ old('pay_frequency', $job->pay_frequency) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Short Description <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="A brief summary of the role shown in job search result cards."></i></label>
                            <textarea name="short_description" class="form-control" rows="2" required>{{ old('short_description', $job->short_description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Full Description <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The complete job description including responsibilities and requirements."></i></label>
                            <textarea name="description" class="form-control" rows="6" required>{{ old('description', $job->description) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Suggested Traits (one per line) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Personality traits ideal for this role, one per line."></i></label>
                            <textarea name="traits_text" class="form-control" rows="6">{{ old('traits_text', is_array($job->traits) ? implode("\n", $job->traits) : '') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Suggested Skills (one per line) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Professional or technical skills required for this role, one per line."></i></label>
                            <textarea name="skills_text" class="form-control" rows="6">{{ old('skills_text', is_array($job->skills) ? implode("\n", $job->skills) : '') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Job Post</button>
                        <a href="{{ route('admin.jobs.index') }}" class="btn btn-secondary ms-2">Cancel</a>
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
    const currentCity = "{!! old('city', $job->city) !!}";

    function updateCities(selectedState, selectedCity = '') {
        citySelect.innerHTML = '<option value="">Select City</option>';
        if (selectedState && statesAndCities[selectedState]) {
            citySelect.disabled = false;
            let cityFound = false;
            statesAndCities[selectedState].forEach(c => {
                const opt = document.createElement('option');
                opt.value = c;
                opt.textContent = c;
                if (c === selectedCity) {
                    opt.selected = true;
                    cityFound = true;
                }
                citySelect.appendChild(opt);
            });

            if (selectedCity && !cityFound) {
                const opt = document.createElement('option');
                opt.value = selectedCity;
                opt.textContent = selectedCity;
                opt.selected = true;
                citySelect.appendChild(opt);
            }
        } else {
            citySelect.disabled = true;
            citySelect.innerHTML = '<option value="">Select State First</option>';
        }
    }

    stateSelect.addEventListener('change', function() {
        updateCities(this.value);
    });

    if (stateSelect.value) {
        updateCities(stateSelect.value, currentCity);
    }
});
</script>
@endsection

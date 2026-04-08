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
                            <label class="form-label">Club / Website</label>
                            <select name="website_id" class="form-select" required>
                                @foreach($websites as $website)
                                    <option value="{{ $website->id }}" {{ (old('website_id', $job->website_id) == $website->id) ? 'selected' : '' }}>{{ $website->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Job Type</label>
                            <select name="job_type" class="form-select" required>
                                <option value="entertainer" {{ old('job_type', $job->job_type) === 'entertainer' ? 'selected' : '' }}>Entertainer</option>
                                <option value="employee" {{ old('job_type', $job->job_type) === 'employee' ? 'selected' : '' }}>Employee</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Live Status</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ old('status', $job->status) ? 'selected' : '' }}>Live</option>
                                <option value="0" {{ !old('status', $job->status) ? 'selected' : '' }}>Paused</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Archive</label>
                            <select name="is_archived" class="form-select">
                                <option value="0" {{ !old('is_archived', $job->is_archived) ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('is_archived', $job->is_archived) ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Job Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $job->title) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location', $job->location) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Employment Type</label>
                            <input type="text" name="employment_type" class="form-control" value="{{ old('employment_type', $job->employment_type) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Compensation</label>
                            <input type="text" name="compensation" class="form-control" value="{{ old('compensation', $job->compensation) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" class="form-control" rows="2" required>{{ old('short_description', $job->short_description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Full Description</label>
                            <textarea name="description" class="form-control" rows="6" required>{{ old('description', $job->description) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Suggested Traits (one per line)</label>
                            <textarea name="traits_text" class="form-control" rows="6">{{ old('traits_text', is_array($job->traits) ? implode("\n", $job->traits) : '') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Suggested Skills (one per line)</label>
                            <textarea name="skills_text" class="form-control" rows="6">{{ old('skills_text', is_array($job->skills) ? implode("\n", $job->skills) : '') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Job Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

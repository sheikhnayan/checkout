@extends(request()->routeIs('admin.nightly-reports.*') ? 'admin.nightly-reports.layout' : 'admin.main')

@section('content')
@php
  $isNightly = request()->routeIs('admin.nightly-reports.*');
  $appsRoute = $isNightly ? 'admin.nightly-reports.jobs.applications' : 'admin.jobs.applications';
  $statusRoute = $isNightly ? 'admin.nightly-reports.jobs.applications.status' : 'admin.jobs.applications.status';
  
  $legalName = trim(($application->legal_first_name ?? '') . ' ' . ($application->legal_last_name ?? ''));
  $displayName = trim(($application->display_first_name ?? '') . ' ' . ($application->display_last_name ?? ''));
  $fullName = $legalName ?: ($displayName ?: 'Applicant #' . $application->id);
  
  $statusBadgeClass = match($application->status) {
      'new' => 'bg-primary text-white',
      'reviewed' => 'bg-info text-dark',
      'shortlisted' => 'bg-warning text-dark',
      'hired' => 'bg-success text-white',
      'rejected' => 'bg-danger text-white',
      default => 'bg-secondary text-white'
  };

  $socials = is_array($application->social_handles) ? array_filter($application->social_handles) : [];
  $positions = is_array($application->positions) ? array_filter($application->positions) : [];
  $availability = is_array($application->availability) ? array_filter($application->availability) : [];
  $skills = is_array($application->skills) ? array_filter($application->skills) : [];
  $traits = is_array($application->traits) ? array_filter($application->traits) : [];
  $history = is_array($application->employment_history) ? array_filter($application->employment_history) : [];
  $education = is_array($application->education) ? array_filter($application->education) : [];
  $attachments = is_array($application->attachments) ? array_filter($application->attachments) : [];
@endphp

<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">
        
        <!-- Header Row -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="mb-0 text-white font-weight-bold">{{ $fullName }}</h3>
                    <span class="badge bg-secondary text-uppercase">{{ ucfirst($application->application_type) }} Applicant</span>
                    <span class="badge {{ $statusBadgeClass }} text-uppercase fs-6 px-3 py-1">{{ ucfirst($application->status) }}</span>
                </div>
                <p class="text-white-50 mb-0">
                    Applied for <strong>{{ $application->jobPost->title ?? 'General Position' }}</strong> at <strong>{{ $application->website->name ?? 'Affiliated Venue' }}</strong>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route($appsRoute) }}" class="btn btn-outline-light"><i class="fas fa-arrow-left me-1"></i> Back to Applications</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Left Column: Primary Details -->
            <div class="col-lg-8">
                
                <!-- Contact & Basic Info Card -->
                <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; color: #1e293b;">
                    <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center py-3">
                        <span><i class="fas fa-user text-warning me-2"></i> Applicant Profile & Contact Info</span>
                        <span class="badge bg-primary bg-opacity-25 text-primary">Ref #{{ $application->id }}</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-uppercase text-muted fw-bold small d-block mb-1">Legal Name</label>
                                <div class="fs-6 fw-bold text-dark">{{ $legalName ?: 'Not specified' }}</div>
                            </div>
                            @if($displayName && $displayName !== $legalName)
                                <div class="col-md-6">
                                    <label class="text-uppercase text-muted fw-bold small d-block mb-1">Display / Stage Name</label>
                                    <div class="fs-6 fw-bold text-dark">{{ $displayName }}</div>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <label class="text-uppercase text-muted fw-bold small d-block mb-1">Email Address</label>
                                <div><a href="mailto:{{ $application->email }}" class="text-primary fw-bold text-decoration-none"><i class="fas fa-envelope me-1"></i> {{ $application->email }}</a></div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-uppercase text-muted fw-bold small d-block mb-1">Phone Number</label>
                                <div>
                                    @if($application->phone)
                                        <a href="tel:{{ $application->phone }}" class="text-primary fw-bold text-decoration-none"><i class="fas fa-phone me-1"></i> {{ $application->phone }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-uppercase text-muted fw-bold small d-block mb-1">City / Location</label>
                                <div class="fw-semibold text-dark"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ trim(($application->city ?? '') . ', ' . ($application->state ?? ''), ', ') ?: 'Not listed' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-uppercase text-muted fw-bold small d-block mb-1">Preferred Contact Method</label>
                                <div><span class="badge bg-light text-dark border border-secondary text-uppercase px-3 py-2"><i class="fas fa-comment me-1"></i> {{ $application->preferred_contact_method ?: 'Any' }}</span></div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-uppercase text-muted fw-bold small d-block mb-1">Submitted At</label>
                                <div class="fw-semibold text-dark"><i class="fas fa-clock me-1 text-info"></i> {{ optional($application->submitted_at)?->timezone('America/Los_Angeles')->format('M d, Y \a\t h:i A') ?? 'N/A' }} PT</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-uppercase text-muted fw-bold small d-block mb-1">Venue / Club</label>
                                <div class="fw-bold text-dark"><i class="fas fa-building me-1 text-primary"></i> {{ $application->website->name ?? 'Affiliated Location' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Positions & Shift Availability (If present) -->
                @if(!empty($positions) || !empty($availability))
                    <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; color: #1e293b;">
                        <div class="card-header bg-dark text-white fw-bold py-3">
                            <i class="fas fa-briefcase text-info me-2"></i> Positions Applied & Shift Availability
                        </div>
                        <div class="card-body p-4">
                            @if(!empty($positions))
                                <div class="mb-3">
                                    <label class="text-uppercase text-muted fw-bold small d-block mb-2">Positions Applied For</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($positions as $pos)
                                            <span class="badge bg-primary px-3 py-2 fs-6"><i class="fas fa-check-circle me-1"></i> {{ $pos }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($availability))
                                <div>
                                    <label class="text-uppercase text-muted fw-bold small d-block mb-2">Days & Shifts Available</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($availability as $avail)
                                            <span class="badge bg-success bg-opacity-25 text-success border border-success px-3 py-2 fs-6"><i class="fas fa-calendar-alt me-1"></i> {{ $avail }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Skills & Traits (If present) -->
                @if(!empty($skills) || !empty($traits))
                    <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; color: #1e293b;">
                        <div class="card-header bg-dark text-white fw-bold py-3">
                            <i class="fas fa-star text-warning me-2"></i> Skills & Key Traits
                        </div>
                        <div class="card-body p-4">
                            @if(!empty($skills))
                                <div class="mb-3">
                                    <label class="text-uppercase text-muted fw-bold small d-block mb-2">Relevant Skills</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($skills as $sk)
                                            <span class="badge bg-info text-dark px-3 py-2 fs-6">{{ $sk }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($traits))
                                <div>
                                    <label class="text-uppercase text-muted fw-bold small d-block mb-2">Personal Traits</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($traits as $tr)
                                            <span class="badge bg-secondary text-white px-3 py-2 fs-6">{{ $tr }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Employment & Work History -->
                @if(!empty($history))
                    <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; color: #1e293b;">
                        <div class="card-header bg-dark text-white fw-bold py-3">
                            <i class="fas fa-history text-primary me-2"></i> Work & Employment History
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="table-light text-uppercase small">
                                        <tr>
                                            <th>Employer / Venue</th>
                                            <th>Position</th>
                                            <th>Dates</th>
                                            <th>Contact Phone</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($history as $jobItem)
                                            @if(is_array($jobItem) && (filled($jobItem['employer'] ?? null) || filled($jobItem['position'] ?? null)))
                                                <tr>
                                                    <td class="fw-bold text-dark">{{ $jobItem['employer'] ?? '-' }}</td>
                                                    <td><span class="badge bg-light text-dark border">{{ $jobItem['position'] ?? '-' }}</span></td>
                                                    <td class="text-muted">{{ $jobItem['dates'] ?? '-' }}</td>
                                                    <td>{{ $jobItem['phone'] ?? '-' }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Additional Notes / Experience Details -->
                @if($application->additional_notes)
                    <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; color: #1e293b;">
                        <div class="card-header bg-dark text-white fw-bold py-3">
                            <i class="fas fa-comment-alt text-light me-2"></i> Applicant Statement & Notes
                        </div>
                        <div class="card-body p-4">
                            <div class="p-3 rounded bg-light border text-dark fs-6" style="line-height: 1.6;">
                                {!! nl2br(e($application->additional_notes)) !!}
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Right Column: Actions, Social Handles & Documents -->
            <div class="col-lg-4">

                <!-- Application Status Control Card -->
                <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; color: #1e293b;">
                    <div class="card-header bg-dark text-white fw-bold py-3">
                        <i class="fas fa-tasks text-warning me-2"></i> Update Application Status
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route($statusRoute, $application->id) }}">
                            @csrf
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2">Change Status</label>
                            <select name="status" class="form-select form-select-lg mb-3 fw-bold">
                                @foreach(['new','reviewed','shortlisted','rejected','hired'] as $stOption)
                                    <option value="{{ $stOption }}" {{ $application->status === $stOption ? 'selected' : '' }}>
                                        {{ ucfirst($stOption) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                                <i class="fas fa-save me-1"></i> Save Status
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Social Handles (If present) -->
                @if(!empty($socials))
                    <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; color: #1e293b;">
                        <div class="card-header bg-dark text-white fw-bold py-3">
                            <i class="fas fa-share-alt text-info me-2"></i> Social Media & Profiles
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex flex-column gap-2">
                                @foreach($socials as $network => $handle)
                                    @if(filled($handle))
                                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light border">
                                            <span class="text-uppercase fw-bold small text-muted"><i class="fab fa-{{ strtolower($network) }} me-1 text-primary"></i> {{ $network }}</span>
                                            <span class="fw-bold text-dark">{{ $handle }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Uploaded Documents & Attachments -->
                <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; color: #1e293b;">
                    <div class="card-header bg-dark text-white fw-bold py-3">
                        <i class="fas fa-paperclip text-success me-2"></i> Uploaded Documents & Media
                    </div>
                    <div class="card-body p-4">
                        @if(empty($attachments))
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-file-alt fa-2x mb-2 d-block text-secondary"></i>
                                No document or photo attachments uploaded.
                            </div>
                        @else
                            <div class="d-flex flex-column gap-2">
                                @foreach($attachments as $key => $fileData)
                                    @if(is_array($fileData) && isset($fileData['path']))
                                        <a href="{{ asset($fileData['path']) }}" target="_blank" class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between p-3">
                                            <div>
                                                <div class="fw-bold">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                                <small class="text-muted">{{ $fileData['name'] ?? basename($fileData['path']) }}</small>
                                            </div>
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @elseif(is_array($fileData))
                                        @foreach($fileData as $nested)
                                            @if(is_array($nested) && isset($nested['path']))
                                                <a href="{{ asset($nested['path']) }}" target="_blank" class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between p-3">
                                                    <div>
                                                        <div class="fw-bold">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                                        <small class="text-muted">{{ $nested['name'] ?? basename($nested['path']) }}</small>
                                                    </div>
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

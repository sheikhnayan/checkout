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

<style>
    .app-detail-card {
        background: #111827 !important;
        border: 1px solid #1f2937 !important;
        border-radius: 12px !important;
        color: #f9fafb !important;
        overflow: hidden;
    }
    .app-detail-card .card-header {
        background: #1f2937 !important;
        border-bottom: 1px solid #374151 !important;
        color: #f9fafb !important;
        font-weight: 700;
        padding: 14px 20px;
    }
    .app-label {
        color: #9ca3af !important;
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        margin-bottom: 4px;
        display: block;
    }
    .app-val {
        color: #f9fafb !important;
        font-size: 0.95rem !important;
        font-weight: 600 !important;
    }
    .app-link {
        color: #38bdf8 !important;
        text-decoration: none !important;
        font-weight: 700 !important;
        transition: color 0.15s ease;
    }
    .app-link:hover {
        color: #7dd3fc !important;
        text-decoration: underline !important;
    }
    .contact-method-pill {
        background: rgba(56, 189, 248, 0.15) !important;
        color: #38bdf8 !important;
        border: 1px solid rgba(56, 189, 248, 0.3) !important;
        padding: 6px 14px !important;
        border-radius: 20px !important;
        font-weight: 800 !important;
        font-size: 0.82rem !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .app-notes-box {
        background: #1f2937 !important;
        border: 1px solid #374151 !important;
        color: #f3f4f6 !important;
        padding: 16px !important;
        border-radius: 8px !important;
        font-size: 0.95rem !important;
        line-height: 1.6 !important;
    }
    .app-social-row {
        background: #1f2937 !important;
        border: 1px solid #374151 !important;
        border-radius: 8px !important;
        padding: 12px 16px !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .app-social-label {
        color: #cbd5e1 !important;
        font-weight: 700 !important;
        font-size: 0.82rem !important;
        text-transform: uppercase !important;
    }
    .app-doc-link {
        background: #1f2937 !important;
        border: 1px solid #374151 !important;
        color: #f9fafb !important;
        border-radius: 8px !important;
        padding: 14px 16px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        text-decoration: none !important;
        transition: all 0.15s ease !important;
        margin-bottom: 8px;
    }
    .app-doc-link:hover {
        background: #374151 !important;
        border-color: #38bdf8 !important;
    }
    .app-doc-title {
        color: #f9fafb !important;
        font-weight: 700 !important;
        font-size: 0.92rem !important;
    }
    .app-doc-name {
        color: #9ca3af !important;
        font-size: 0.82rem !important;
        word-break: break-all !important;
    }
    .app-doc-icon {
        color: #38bdf8 !important;
        font-size: 1.2rem !important;
    }
    .app-status-select {
        background-color: #1f2937 !important;
        color: #f9fafb !important;
        border: 1px solid #475569 !important;
        border-radius: 8px !important;
        padding: 10px 14px !important;
        font-weight: 700 !important;
    }
    .app-status-btn {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        padding: 12px !important;
        border-radius: 8px !important;
    }
    .app-status-btn:hover {
        background-color: #1d4ed8 !important;
        border-color: #1d4ed8 !important;
    }
</style>

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
                    Applied for <strong class="text-white">{{ $application->jobPost->title ?? 'General Application' }}</strong> at <strong class="text-white">{{ $application->website->name ?? 'Affiliated Venue' }}</strong>
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
                <div class="card app-detail-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-user text-warning me-2"></i> Applicant Profile & Contact Info</span>
                        <span class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-25">Ref #{{ $application->id }}</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <span class="app-label">Legal Name</span>
                                <div class="app-val">{{ $legalName ?: 'Not specified' }}</div>
                            </div>
                            @if($displayName && $displayName !== $legalName)
                                <div class="col-md-6">
                                    <span class="app-label">Display / Stage Name</span>
                                    <div class="app-val">{{ $displayName }}</div>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <span class="app-label">Email Address</span>
                                <div><a href="mailto:{{ $application->email }}" class="app-link"><i class="fas fa-envelope me-1"></i> {{ $application->email }}</a></div>
                            </div>
                            <div class="col-md-6">
                                <span class="app-label">Phone Number</span>
                                <div>
                                    @if($application->phone)
                                        <a href="tel:{{ $application->phone }}" class="app-link"><i class="fas fa-phone me-1"></i> {{ $application->phone }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <span class="app-label">City / Location</span>
                                <div class="app-val"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ trim(($application->city ?? '') . ', ' . ($application->state ?? ''), ', ') ?: 'Not listed' }}</div>
                            </div>
                            <div class="col-md-6">
                                <span class="app-label">Preferred Contact Method</span>
                                <div>
                                    <span class="contact-method-pill">
                                        <i class="fas fa-comment"></i> {{ strtoupper($application->preferred_contact_method ?: 'ANY') }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <span class="app-label">Submitted At</span>
                                <div class="app-val"><i class="fas fa-clock me-1 text-info"></i> {{ optional($application->submitted_at)?->timezone('America/Los_Angeles')->format('M d, Y \a\t h:i A') ?? 'N/A' }} PT</div>
                            </div>
                            <div class="col-md-6">
                                <span class="app-label">Venue / Club</span>
                                <div class="app-val"><i class="fas fa-building me-1 text-primary"></i> {{ $application->website->name ?? 'Affiliated Location' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Positions & Shift Availability (If present) -->
                @if(!empty($positions) || !empty($availability))
                    <div class="card app-detail-card mb-4">
                        <div class="card-header">
                            <i class="fas fa-briefcase text-info me-2"></i> Positions Applied & Shift Availability
                        </div>
                        <div class="card-body p-4">
                            @if(!empty($positions))
                                <div class="mb-4">
                                    <span class="app-label mb-2">Positions Applied For</span>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($positions as $pos)
                                            <span class="badge bg-primary px-3 py-2 fs-6"><i class="fas fa-check-circle me-1"></i> {{ $pos }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($availability))
                                <div>
                                    <span class="app-label mb-2">Days & Shifts Available</span>
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
                    <div class="card app-detail-card mb-4">
                        <div class="card-header">
                            <i class="fas fa-star text-warning me-2"></i> Skills & Key Traits
                        </div>
                        <div class="card-body p-4">
                            @if(!empty($skills))
                                <div class="mb-4">
                                    <span class="app-label mb-2">Relevant Skills</span>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($skills as $sk)
                                            <span class="badge bg-info text-dark px-3 py-2 fs-6">{{ $sk }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($traits))
                                <div>
                                    <span class="app-label mb-2">Personal Traits</span>
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
                    <div class="card app-detail-card mb-4">
                        <div class="card-header">
                            <i class="fas fa-history text-primary me-2"></i> Work & Employment History
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-dark table-bordered align-middle mb-0">
                                    <thead class="text-uppercase small text-muted">
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
                                                    <td class="fw-bold text-white">{{ $jobItem['employer'] ?? '-' }}</td>
                                                    <td><span class="badge bg-secondary">{{ $jobItem['position'] ?? '-' }}</span></td>
                                                    <td class="text-white-50">{{ $jobItem['dates'] ?? '-' }}</td>
                                                    <td class="text-info">{{ $jobItem['phone'] ?? '-' }}</td>
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
                <div class="card app-detail-card mb-4">
                    <div class="card-header">
                        <i class="fas fa-comment-alt text-light me-2"></i> Applicant Statement & Notes
                    </div>
                    <div class="card-body p-4">
                        <div class="app-notes-box">
                            @if(filled($application->additional_notes))
                                {!! nl2br(e($application->additional_notes)) !!}
                            @else
                                <span class="text-muted italic">No additional notes provided by applicant.</span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Actions, Social Handles & Documents -->
            <div class="col-lg-4">

                <!-- Application Status Control Card -->
                <div class="card app-detail-card mb-4">
                    <div class="card-header">
                        <i class="fas fa-tasks text-warning me-2"></i> Update Application Status
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route($statusRoute, $application->id) }}">
                            @csrf
                            <span class="app-label mb-2">Change Status</span>
                            <select name="status" class="form-select app-status-select mb-3">
                                @foreach(['new','reviewed','shortlisted','rejected','hired'] as $stOption)
                                    <option value="{{ $stOption }}" {{ $application->status === $stOption ? 'selected' : '' }}>
                                        {{ ucfirst($stOption) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn app-status-btn w-100">
                                <i class="fas fa-save me-1"></i> Save Status
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Social Handles (If present) -->
                <div class="card app-detail-card mb-4">
                    <div class="card-header">
                        <i class="fas fa-share-alt text-info me-2"></i> Social Media & Profiles
                    </div>
                    <div class="card-body p-4">
                        @if(empty($socials))
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-hashtag fa-2x mb-2 d-block opacity-50"></i>
                                No social handles provided.
                            </div>
                        @else
                            @foreach($socials as $network => $handle)
                                @if(filled($handle))
                                    @php
                                        $handleClean = trim((string)$handle);
                                        $url = '#';
                                        if (str_starts_with($handleClean, 'http://') || str_starts_with($handleClean, 'https://')) {
                                            $url = $handleClean;
                                        } elseif (strtolower($network) === 'instagram') {
                                            $url = 'https://instagram.com/' . ltrim($handleClean, '@');
                                        } elseif (strtolower($network) === 'facebook') {
                                            $url = 'https://facebook.com/' . ltrim($handleClean, '@');
                                        } elseif (strtolower($network) === 'tiktok') {
                                            $url = 'https://tiktok.com/@' . ltrim($handleClean, '@');
                                        } elseif (strtolower($network) === 'x' || strtolower($network) === 'x_handle') {
                                            $url = 'https://x.com/' . ltrim($handleClean, '@');
                                        }
                                    @endphp
                                    <div class="app-social-row">
                                        <span class="app-social-label">
                                            <i class="fab fa-{{ strtolower($network) }} me-1 text-info"></i> {{ $network }}
                                        </span>
                                        @if($url !== '#')
                                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="app-social-val">
                                                {{ $handleClean }} <i class="fas fa-external-link-alt ms-1 fs-7"></i>
                                            </a>
                                        @else
                                            <span class="app-social-val">{{ $handleClean }}</span>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Uploaded Documents & Attachments -->
                <div class="card app-detail-card mb-4">
                    <div class="card-header">
                        <i class="fas fa-paperclip text-success me-2"></i> Uploaded Documents & Media
                    </div>
                    <div class="card-body p-4">
                        @if(empty($attachments))
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                                No document or photo attachments uploaded.
                            </div>
                        @else
                            @foreach($attachments as $key => $fileData)
                                @if(is_array($fileData) && isset($fileData['path']))
                                    <a href="{{ asset($fileData['path']) }}" target="_blank" class="app-doc-link">
                                        <div>
                                            <div class="app-doc-title">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                            <div class="app-doc-name">{{ $fileData['name'] ?? basename($fileData['path']) }}</div>
                                        </div>
                                        <i class="fas fa-external-link-alt app-doc-icon"></i>
                                    </a>
                                @elseif(is_array($fileData))
                                    @foreach($fileData as $nested)
                                        @if(is_array($nested) && isset($nested['path']))
                                            <a href="{{ asset($nested['path']) }}" target="_blank" class="app-doc-link">
                                                <div>
                                                    <div class="app-doc-title">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                                    <div class="app-doc-name">{{ $nested['name'] ?? basename($nested['path']) }}</div>
                                                </div>
                                                <i class="fas fa-external-link-alt app-doc-icon"></i>
                                            </a>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

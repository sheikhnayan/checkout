<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application - {{ $job->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a;
            --primary-hover: #1e293b;
            --accent: #2563eb;
            --accent-soft: #eff6ff;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --border: #e2e8f0;
            --border-focus: #3b82f6;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-light: #475569;
            --radius-lg: 12px;
            --radius-md: 8px;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.05);
            --shadow-md: 0 4px 16px -2px rgba(15, 23, 42, 0.08);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 920px;
            margin: 0 auto;
            padding: 32px 20px 48px;
        }
        .corp-header {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }
        .corp-header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0f172a 0%, #2563eb 100%);
        }
        .company-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        h1.job-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0 0 6px 0;
            letter-spacing: -0.02em;
        }
        .job-location {
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 12px;
        }
        .job-desc {
            color: var(--text-light);
            font-size: 0.95rem;
            margin: 0;
        }
        .corp-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 28px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0 0 18px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .section-title span.tag {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .grid-3 {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .full { grid-column: 1 / -1; }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        label.form-label {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .req { color: #dc2626; font-weight: 700; }
        .opt { color: var(--text-muted); font-weight: 500; font-size: 0.78rem; }
        input[type="text"],
        input[type="email"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            border: 1px solid var(--border);
            background: #ffffff;
            border-radius: var(--radius-md);
            padding: 10px 14px;
            font-size: 0.92rem;
            font-family: inherit;
            color: var(--text-main);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="date"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        input[type="file"] {
            font-size: 0.88rem;
            color: var(--text-light);
            background: #f1f5f9;
            border: 1px dashed #cbd5e1;
            border-radius: var(--radius-md);
            padding: 10px 12px;
            cursor: pointer;
        }
        input[type="file"]:hover {
            border-color: var(--accent);
            background: #eff6ff;
        }
        .help-text {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 4px;
        }
        .checkbox-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        }
        .check-card {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #f8fafc;
            border: 1px solid var(--border);
            padding: 10px 12px;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: background 0.15s ease, border-color 0.15s ease;
        }
        .check-card:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        .check-card input[type="checkbox"] {
            margin-top: 3px;
            accent-color: var(--accent);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }
        .check-card span {
            font-size: 0.86rem;
            font-weight: 500;
            color: var(--text-main);
        }
        .consent-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 16px;
            margin-bottom: 14px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            transition: border-color 0.15s ease;
        }
        .consent-card:hover {
            border-color: #cbd5e1;
        }
        .consent-card input[type="checkbox"] {
            margin-top: 4px;
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            accent-color: var(--accent);
            cursor: pointer;
        }
        .consent-text {
            font-size: 0.85rem;
            color: var(--text-light);
            line-height: 1.5;
        }
        .consent-text strong {
            color: var(--text-main);
        }
        .notice-box {
            background: #f1f5f9;
            border-left: 4px solid var(--primary);
            padding: 14px 16px;
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
            margin-bottom: 20px;
            font-size: 0.88rem;
            color: var(--text-light);
        }
        .submit-btn {
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-md);
            padding: 14px 32px;
            font-size: 1rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: background-color 0.15s ease, transform 0.1s ease;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
            width: 100%;
        }
        .submit-btn:hover {
            background-color: var(--primary-hover);
        }
        .submit-btn:active {
            transform: translateY(1px);
        }
        .alert {
            border-radius: var(--radius-md);
            padding: 14px 18px;
            margin-bottom: 20px;
            font-size: 0.92rem;
        }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-danger ul { margin: 6px 0 0 18px; padding: 0; }

        @media (max-width: 768px) {
            .container { padding: 16px 12px 32px; }
            .corp-header, .corp-card { padding: 20px 16px; }
            .grid, .grid-3 { grid-template-columns: 1fr; }
            .checkbox-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="corp-header">
        <div class="company-badge">{{ $job->website->name ?? 'Corporate Careers' }}</div>
        <h1 class="job-title">{{ $job->title }}</h1>
        <div class="job-location">📍 {{ $job->location }} • {{ ucfirst($job->employment_type ?? 'Full-time') }}</div>
        @if($job->short_description)
            <p class="job-desc">{{ $job->short_description }}</p>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <strong>Application Submitted:</strong> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the errors below before submitting:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('jobs.apply.submit', $job) }}" enctype="multipart/form-data">
        @csrf

        @if($job->job_type === 'entertainer')
            <!-- ENTERTAINER APPLICATION FORM -->
            <div class="corp-card">
                <div class="section-title">
                    Personal & Contact Information
                    <span class="tag">Step 1 of 4</span>
                </div>
                <div class="grid">
                    <div class="form-group">
                        <label class="form-label">Legal First Name <span class="req">*</span></label>
                        <input type="text" name="legal_first_name" value="{{ old('legal_first_name') }}" required placeholder="First name as shown on legal ID">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Legal Last Name <span class="req">*</span></label>
                        <input type="text" name="legal_last_name" value="{{ old('legal_last_name') }}" required placeholder="Last name as shown on legal ID">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stage / Preferred First Name <span class="req">*</span></label>
                        <input type="text" name="display_first_name" value="{{ old('display_first_name') }}" required placeholder="Preferred name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stage / Preferred Last Name <span class="opt">Optional</span></label>
                        <input type="text" name="display_last_name" value="{{ old('display_last_name') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address <span class="req">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Email Address <span class="req">*</span></label>
                        <input type="email" name="email_confirmation" value="{{ old('email_confirmation') }}" required placeholder="Re-enter email address">
                    </div>
                    <div class="form-group">
                        <label class="form-label">City <span class="req">*</span></label>
                        <input type="text" name="city" value="{{ old('city') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">State / Province <span class="req">*</span></label>
                        <input type="text" name="state" value="{{ old('state') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number <span class="req">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="(555) 000-0000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Preferred Contact Method <span class="req">*</span></label>
                        <select name="preferred_contact_method" required>
                            <option value="">Select contact preference</option>
                            <option value="phone" {{ old('preferred_contact_method') === 'phone' ? 'selected' : '' }}>Phone Call</option>
                            <option value="text" {{ old('preferred_contact_method') === 'text' ? 'selected' : '' }}>Text Message (SMS)</option>
                            <option value="email" {{ old('preferred_contact_method') === 'email' ? 'selected' : '' }}>Email</option>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Previous Entertainment Employment <span class="opt">Optional</span></label>
                        <textarea name="previous_employment" placeholder="Summarize your prior performance experience and venues">{{ old('previous_employment') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="corp-card">
                <div class="section-title">
                    Media & Document Submissions
                    <span class="tag">Step 2 of 4</span>
                </div>
                <div class="grid">
                    <div class="form-group">
                        <label class="form-label">Resume / Experience Document <span class="req">*</span></label>
                        <input type="file" name="entertainer_resume" required>
                        <div class="help-text">Accepted: PDF, DOC, DOCX, JPG, PNG. Max: 4MB.</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Personality Video <span class="opt">Optional</span></label>
                        <input type="file" name="personality_video">
                        <div class="help-text">Short intro video. Accepted: MP4, MOV, WEBM, AVI. Max: 4MB.</div>
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Portfolio Photos (Exactly 3 Photos Required) <span class="req">*</span></label>
                        <input type="file" name="portfolio_photos[]" multiple required>
                        <div class="help-text">Please select exactly 3 clear photos. Max 4MB per photo.</div>
                    </div>
                </div>
            </div>

            <div class="corp-card">
                <div class="section-title">
                    Skills & Attributes
                    <span class="tag">Step 3 of 4</span>
                </div>
                <div class="form-group mb-4">
                    <label class="form-label">Select your core personality traits <span class="req">*</span></label>
                    <div class="checkbox-grid">
                        @foreach(['Outgoing','Vibrant','Fun','Friendly','Dedicated','Team-Oriented','Reliable','Multi-Talented','Organized','Leader'] as $trait)
                            <label class="check-card">
                                <input type="checkbox" name="traits[]" value="{{ $trait }}">
                                <span>{{ $trait }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Select your professional skills & talents <span class="req">*</span></label>
                    <div class="checkbox-grid">
                        @foreach(['Retail','Sales','Dancer','Party / Event Planning','Ballerina','Cheerleader','Tap Dance','GoGo','Gymnast','Yoga','Stylist','Choreography'] as $skill)
                            <label class="check-card">
                                <input type="checkbox" name="skills[]" value="{{ $skill }}">
                                <span>{{ $skill }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

        @else
            <!-- STANDARD EMPLOYEE APPLICATION FORM -->
            <div class="corp-card">
                <div class="section-title">
                    Applicant Information
                    <span class="tag">Step 1 of 4</span>
                </div>
                <div class="grid">
                    <div class="form-group">
                        <label class="form-label">First Name <span class="req">*</span></label>
                        <input type="text" name="legal_first_name" value="{{ old('legal_first_name') }}" required placeholder="Legal first name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name <span class="req">*</span></label>
                        <input type="text" name="legal_last_name" value="{{ old('legal_last_name') }}" required placeholder="Legal last name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address <span class="req">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Email Address <span class="req">*</span></label>
                        <input type="email" name="email_confirmation" value="{{ old('email_confirmation') }}" required placeholder="Re-enter email address">
                    </div>
                    <div class="form-group">
                        <label class="form-label">City <span class="req">*</span></label>
                        <input type="text" name="city" value="{{ old('city') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">State / Province <span class="req">*</span></label>
                        <input type="text" name="state" value="{{ old('state') }}" required>
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Mobile Phone Number <span class="req">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="(555) 000-0000">
                    </div>
                </div>
            </div>

            <div class="corp-card">
                <div class="section-title">
                    Positions & Availability
                    <span class="tag">Step 2 of 4</span>
                </div>
                <div class="form-group mb-4 full">
                    <label class="form-label">Interested Position(s) <span class="req">*</span></label>
                    <div class="checkbox-grid">
                        @foreach(['Server / Model Server','Bartender / Model Bartender','Hospitality','Box Office Cashier','Support','Manager or Manager in Training','Retail','Other'] as $position)
                            <label class="check-card">
                                <input type="checkbox" name="positions[]" value="{{ $position }}">
                                <span>{{ $position }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid" style="margin-bottom: 16px;">
                    <div class="form-group">
                        <label class="form-label">How did you hear about us? <span class="req">*</span></label>
                        <input type="text" name="heard_about" value="{{ old('heard_about') }}" required placeholder="e.g. Indeed, Referral, Social Media">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Target Start Date <span class="req">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required>
                    </div>
                </div>

                <div class="form-group full">
                    <label class="form-label">Shift Availability <span class="req">*</span></label>
                    <div class="checkbox-grid">
                        @foreach(['Monday (Dayshift)','Monday (Night Shift)','Tuesday (Dayshift)','Tuesday (Night Shift)','Wednesday (Dayshift)','Wednesday (Night Shift)','Thursday (Dayshift)','Thursday (Night Shift)','Friday (Dayshift)','Friday (Night Shift)','Saturday (Dayshift)','Saturday (Night Shift)','Sunday (Dayshift)','Sunday (Night Shift)'] as $slot)
                            <label class="check-card">
                                <input type="checkbox" name="availability[]" value="{{ $slot }}">
                                <span>{{ $slot }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="corp-card">
                <div class="section-title">
                    Media & Experience Documentation
                    <span class="tag">Step 3 of 4</span>
                </div>
                <div class="grid" style="margin-bottom: 16px;">
                    <div class="form-group">
                        <label class="form-label">Headshot / Photo Upload <span class="req">*</span></label>
                        <input type="file" name="picture_upload" required>
                        <div class="help-text">Recent photo. Accepted: JPG, PNG, WEBP. Max: 4MB.</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Video Upload <span class="opt">Optional</span></label>
                        <input type="file" name="video_upload">
                        <div class="help-text">Accepted: MP4, MOV, WEBM, AVI. Max: 4MB.</div>
                    </div>
                </div>

                <div class="form-group full mb-4">
                    <label class="form-label">Skills & Certifications <span class="req">*</span></label>
                    <div class="checkbox-grid">
                        @foreach(['Retail','Sales','Hospitality','Hotels','Bartending','Barback','Server / Waitress','Host / Hostess','VIP Hosting','Restaurant','Breastaurant','Management','Security / Law Enforcement','Car Sales','TABC','TAM Card','Guard Card','RAMP Certification','Other Industry Related Certifications','Reliable Transportation','Martial Arts','Nightlife','Entertainment','Event Planning','Corporate Event Management','Lighting, Sound','Project Management','Dispatch','Medical (EMS / Fire)','Valid Government-Issued Identification'] as $skill)
                            <label class="check-card">
                                <input type="checkbox" name="skills[]" value="{{ $skill }}">
                                <span>{{ $skill }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="section-title" style="margin-top: 24px;">Employment & Educational Background</div>
                <div class="grid">
                    <div class="form-group">
                        <label class="form-label">Recent Employer <span class="opt">Optional</span></label>
                        <input type="text" name="employment_history[0][employer]" value="{{ old('employment_history.0.employer') }}" placeholder="Company name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Employment Dates <span class="opt">Optional</span></label>
                        <input type="text" name="employment_history[0][dates]" value="{{ old('employment_history.0.dates') }}" placeholder="e.g. 2022 - 2024">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Position Held <span class="opt">Optional</span></label>
                        <input type="text" name="employment_history[0][position]" value="{{ old('employment_history.0.position') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Employer Phone Number <span class="opt">Optional</span></label>
                        <input type="text" name="employment_history[0][phone]" value="{{ old('employment_history.0.phone') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Upload Resume <span class="opt">Optional</span></label>
                        <input type="file" name="resume">
                        <div class="help-text">Accepted: PDF, DOC, DOCX, JPG, PNG. Max: 4MB.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">May we contact your previous employer? <span class="req">*</span></label>
                        <select name="contact_previous_employer" required>
                            <option value="yes" {{ old('contact_previous_employer') === 'yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ old('contact_previous_employer') === 'no' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>

                <div class="grid" style="margin-top: 14px;">
                    <div class="form-group">
                        <label class="form-label">High School Diploma / GED</label>
                        <input type="text" name="education[]" value="{{ old('education.0') }}" placeholder="School name & year">
                    </div>
                    <div class="form-group">
                        <label class="form-label">College / University</label>
                        <input type="text" name="education[]" value="{{ old('education.1') }}" placeholder="Degree & institution">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Business / Management Studies</label>
                        <input type="text" name="education[]" value="{{ old('education.2') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Specialized Vocational / Bartending School</label>
                        <input type="text" name="education[]" value="{{ old('education.3') }}">
                    </div>
                </div>

                <div class="form-group full" style="margin-top: 14px;">
                    <label class="form-label">Additional Qualifications & Notes <span class="opt">Optional</span></label>
                    <textarea name="extra_notes" placeholder="Share any relevant background details or notes for the hiring committee">{{ old('extra_notes') }}</textarea>
                </div>
            </div>
        @endif

        <!-- SOCIAL HANDLES -->
        <div class="corp-card">
            <div class="section-title">
                Social & Professional Profiles
                <span class="tag">Optional</span>
            </div>
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Instagram Handle</label>
                    <input type="text" name="instagram" value="{{ old('instagram') }}" placeholder="@username">
                </div>
                <div class="form-group">
                    <label class="form-label">Facebook Profile</label>
                    <input type="text" name="facebook" value="{{ old('facebook') }}" placeholder="facebook.com/profile">
                </div>
                <div class="form-group">
                    <label class="form-label">TikTok Handle</label>
                    <input type="text" name="tiktok" value="{{ old('tiktok') }}" placeholder="@username">
                </div>
                <div class="form-group">
                    <label class="form-label">X (Twitter) Handle</label>
                    <input type="text" name="x_handle" value="{{ old('x_handle') }}" placeholder="@username">
                </div>
            </div>
        </div>

        <!-- DECLARATIONS & CONSENTS -->
        <div class="corp-card">
            <div class="section-title">
                Applicant Consents & Legal Disclosures
                <span class="tag">Final Step</span>
            </div>

            <div class="notice-box">
                I confirm that I meet the minimum age requirement for the selected position and location, which may be 18 or 21 years of age.
            </div>

            <label class="consent-card">
                <input type="checkbox" name="age_confirm" value="1" required>
                <div class="consent-text">
                    <strong>Minimum Age Confirmation:</strong><br>
                    I meet the minimum age requirement for this position and location (18 or 21, as applicable).
                </div>
            </label>

            <label class="consent-card">
                <input type="checkbox" name="consent_contact" value="1" required>
                <div class="consent-text">
                    <strong>Communication Authorization:</strong><br>
                    I authorize the Company and its affiliated locations to contact me by phone, email, and/or SMS regarding my application and future employment opportunities. Standard message and data rates may apply. Consent is not a condition of employment.
                </div>
            </label>

            <label class="consent-card">
                <input type="checkbox" name="consent_sms" value="1" required>
                <div class="consent-text">
                    <strong>SMS Consent & Application Certification:</strong><br>
                    By providing my mobile number, I consent to receive employment-related text messages regarding my application, interview scheduling, hiring updates, and future job opportunities. Message frequency may vary. Reply STOP to opt out and HELP for assistance. Message and data rates may apply. I certify that the information provided in this application is true and complete to the best of my knowledge. I understand that any false or misleading information may result in the rejection of my application or, if hired, termination of employment.
                </div>
            </label>

            <label class="consent-card">
                <input type="checkbox" name="terms" value="1" required>
                <div class="consent-text">
                    <strong>Terms & Conditions Agreement:</strong><br>
                    I agree with the Terms and Conditions of employment application submission.
                </div>
            </label>

            <div style="margin-top: 24px;">
                <button type="submit" class="submit-btn">Submit Official Application</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>

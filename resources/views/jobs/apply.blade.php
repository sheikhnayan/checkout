<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply - {{ $job->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Roboto', sans-serif; background: #f3f2f1; color: #2d2d2d; }
        .wrap { max-width: 980px; margin: 0 auto; padding: 22px 16px 30px; }
        .panel { background: #fff; border: 1px solid #d4d2d0; border-radius: 12px; padding: 18px; margin-bottom: 14px; }
        h1 { font-size: 1.7rem; margin: 0; color: #2557a7; }
        .muted { color: #595959; }
        .grid { display: grid; gap: 12px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { display: grid; gap: 12px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid > *, .grid-3 > * { min-width: 0; }
        .full { grid-column: 1 / -1; }
        label { font-size: .92rem; font-weight: 700; display: block; margin-bottom: 5px; }
        input, textarea, select { width: 100%; border: 1px solid #c9c7c5; border-radius: 8px; padding: 10px 12px; font-size: .95rem; }
        textarea { min-height: 92px; resize: vertical; }
        .checks { display: grid; gap: 8px; grid-template-columns: repeat(auto-fit,minmax(190px,1fr)); }
        .check-item { display: flex; align-items: flex-start; gap: 8px; background: #fafafa; border: 1px solid #ece9e7; padding: 8px 10px; border-radius: 8px; white-space: normal; word-break: break-word; }
        .check-item input { width: auto; }
        .submit-btn { background: #2557a7; border: 0; border-radius: 10px; color: #fff; padding: 12px 20px; font-weight: 700; cursor: pointer; }
        .small { font-size: .85rem; color: #6b6b6b; }
        .alert { border-radius: 10px; padding: 12px; margin-bottom: 12px; }
        .alert-success { background: #e8f4ec; border: 1px solid #7cc096; color: #114f2a; }
        .alert-danger { background: #fcebeb; border: 1px solid #d77; color: #7e1f1f; }
        @media (max-width: 820px) {
            .grid, .grid-3 { grid-template-columns: 1fr; }
            .checks { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="panel">
        <h1>{{ $job->title }}</h1>
        <p class="muted mb-0">{{ $job->website->name ?? 'Club' }} • {{ $job->location }}</p>
        <p class="muted">{{ $job->short_description }}</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Fix the form and try again.</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="panel" method="POST" action="{{ route('jobs.apply.submit', $job) }}" enctype="multipart/form-data">
        @csrf

        @if($job->job_type === 'entertainer')
            <div class="grid">
                <div>
                    <label>Legal First Name (Required)</label>
                    <input type="text" name="legal_first_name" value="{{ old('legal_first_name') }}" required>
                </div>
                <div>
                    <label>Legal Last Name (Required)</label>
                    <input type="text" name="legal_last_name" value="{{ old('legal_last_name') }}" required>
                </div>
                <div>
                    <label>Entertainer First Name (Required)</label>
                    <input type="text" name="display_first_name" value="{{ old('display_first_name') }}" required>
                </div>
                <div>
                    <label>Entertainer Last Name</label>
                    <input type="text" name="display_last_name" value="{{ old('display_last_name') }}">
                </div>
                <div>
                    <label>Email (Required)</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div>
                    <label>Confirm Email (Required)</label>
                    <input type="email" name="email_confirmation" value="{{ old('email_confirmation') }}" required>
                </div>
                <div>
                    <label>City (Required)</label>
                    <input type="text" name="city" value="{{ old('city') }}" required>
                </div>
                <div>
                    <label>State / Province / Region (Required)</label>
                    <input type="text" name="state" value="{{ old('state') }}" required>
                </div>
                <div>
                    <label>Phone Number (Required)</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required>
                </div>
                <div>
                    <label>Preferred Contact Method (Required)</label>
                    <select name="preferred_contact_method" required>
                        <option value="">Select option</option>
                        <option value="phone" {{ old('preferred_contact_method') === 'phone' ? 'selected' : '' }}>Phone</option>
                        <option value="text" {{ old('preferred_contact_method') === 'text' ? 'selected' : '' }}>Text</option>
                        <option value="email" {{ old('preferred_contact_method') === 'email' ? 'selected' : '' }}>Email</option>
                    </select>
                </div>

                <div class="full">
                    <label>Previous Employment</label>
                    <textarea name="previous_employment">{{ old('previous_employment') }}</textarea>
                </div>

                <div>
                    <label>Upload resume / prior entertainer experience (Required)</label>
                    <input type="file" name="entertainer_resume" required>
                    <div class="small">Max file size: 4MB.</div>
                </div>

                <div>
                    <label>Upload personality video (Required)</label>
                    <input type="file" name="personality_video" required>
                    <div class="small">Max file size: 4MB.</div>
                </div>

                <div class="full">
                    <label>Upload 3 photos (Required)</label>
                    <input type="file" name="portfolio_photos[]" multiple required>
                    <div class="small">Upload exactly 3 files. Max 4MB each.</div>
                </div>
            </div>

            <div class="panel" style="padding: 14px; margin-top: 12px;">
                <label>Do you have the following personality traits? (Required)</label>
                <div class="checks">
                    @foreach(['Outgoing','Vibrant','Fun','Friendly','Dedicated','Team-Oriented','Reliable','Multi-Talented','Organized','Leader'] as $trait)
                        <label class="check-item"><input type="checkbox" name="traits[]" value="{{ $trait }}"> {{ $trait }}</label>
                    @endforeach
                </div>
            </div>

            <div class="panel" style="padding: 14px;">
                <label>Do you have any of the following skills? (Required)</label>
                <div class="checks">
                    @foreach(['Retail','Sales','Dancer','Party / Event Planning','Ballerina','Cheerleader','Tap Dance','GoGo','Gymnast','Yoga','Stylist','Choreography'] as $skill)
                        <label class="check-item"><input type="checkbox" name="skills[]" value="{{ $skill }}"> {{ $skill }}</label>
                    @endforeach
                </div>
            </div>
        @else
            <div class="grid">
                <div>
                    <label>Your First Name (Required)</label>
                    <input type="text" name="legal_first_name" value="{{ old('legal_first_name') }}" required>
                </div>
                <div>
                    <label>Your Last Name (Required)</label>
                    <input type="text" name="legal_last_name" value="{{ old('legal_last_name') }}" required>
                </div>
                <div>
                    <label>Email (Required)</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div>
                    <label>Confirm Email (Required)</label>
                    <input type="email" name="email_confirmation" value="{{ old('email_confirmation') }}" required>
                </div>
                <div>
                    <label>City (Required)</label>
                    <input type="text" name="city" value="{{ old('city') }}" required>
                </div>
                <div>
                    <label>State / Province / Region (Required)</label>
                    <input type="text" name="state" value="{{ old('state') }}" required>
                </div>
                <div>
                    <label>Your Phone (Required)</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required>
                </div>

                <div class="full">
                    <label>Interested Position(s) (Required)</label>
                    <div class="checks">
                        @foreach(['Server / Model Server','Bartender / Model Bartender','Hospitality','Box Office Cashier','Support','Manager or Manager in Training','Retail','Other'] as $position)
                            <label class="check-item"><input type="checkbox" name="positions[]" value="{{ $position }}"> {{ $position }}</label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label>Picture upload (Required)</label>
                    <input type="file" name="picture_upload" required>
                    <div class="small">Max file size: 4MB.</div>
                </div>
                <div>
                    <label>Video upload (Required)</label>
                    <input type="file" name="video_upload" required>
                    <div class="small">Max file size: 4MB.</div>
                </div>

                <div class="full">
                    <label>Skills / Experience / Documentation (Required)</label>
                    <div class="checks">
                        @foreach(['Retail','Sales','Hospitality','Hotels','Bartending','Barback','Server / Waitress','Host / Hostess','VIP Hosting','Restaurant','Breastaurant','Management','Security / Law Enforcement','Car Sales','TABC','TAM Card','Guard Card','RAMP Certification','Other Industry Related Certifications','Reliable Transportation','Martial Arts','Nightlife','Entertainment','Event Planning','Corporate Event Management','Lighting, Sound','Project Management','Dispatch','Medical (EMS / Fire)','Valid Government-Issued Identification'] as $skill)
                            <label class="check-item"><input type="checkbox" name="skills[]" value="{{ $skill }}"> {{ $skill }}</label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label>How did you hear about us? (Required)</label>
                    <input type="text" name="heard_about" value="{{ old('heard_about') }}" required>
                </div>

                <div>
                    <label>What date can you start? (Required)</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required>
                </div>

                <div class="full">
                    <label>Availability (Required)</label>
                    <div class="checks">
                        @foreach(['Monday (Dayshift)','Monday (Night Shift)','Tuesday (Dayshift)','Tuesday (Night Shift)','Wednesday (Dayshift)','Wednesday (Night Shift)','Thursday (Dayshift)','Thursday (Night Shift)','Friday (Dayshift)','Friday (Night Shift)','Saturday (Dayshift)','Saturday (Night Shift)','Sunday (Dayshift)','Sunday (Night Shift)'] as $slot)
                            <label class="check-item"><input type="checkbox" name="availability[]" value="{{ $slot }}"> {{ $slot }}</label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label>Employment History (Employer)</label>
                    <input type="text" name="employment_history[0][employer]" value="{{ old('employment_history.0.employer') }}">
                </div>
                <div>
                    <label>Employment Dates</label>
                    <input type="text" name="employment_history[0][dates]" value="{{ old('employment_history.0.dates') }}">
                </div>
                <div>
                    <label>Employment Position</label>
                    <input type="text" name="employment_history[0][position]" value="{{ old('employment_history.0.position') }}">
                </div>
                <div>
                    <label>Employer Phone</label>
                    <input type="text" name="employment_history[0][phone]" value="{{ old('employment_history.0.phone') }}">
                </div>

                <div>
                    <label>Resume (Optional)</label>
                    <input type="file" name="resume">
                    <div class="small">Accepted: pdf, doc, docx, jpg, jpeg, png. Max 4MB.</div>
                </div>

                <div>
                    <label>May we contact your previous employer? (Required)</label>
                    <select name="contact_previous_employer" required>
                        <option value="yes" {{ old('contact_previous_employer') === 'yes' ? 'selected' : '' }}>Yes</option>
                        <option value="no" {{ old('contact_previous_employer') === 'no' ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div>
                    <label>Education: High School Diploma</label>
                    <input type="text" name="education[]" value="{{ old('education.0') }}">
                </div>
                <div>
                    <label>Education: College</label>
                    <input type="text" name="education[]" value="{{ old('education.1') }}">
                </div>
                <div>
                    <label>Education: Business Management</label>
                    <input type="text" name="education[]" value="{{ old('education.2') }}">
                </div>
                <div>
                    <label>Education: School of Bartending</label>
                    <input type="text" name="education[]" value="{{ old('education.3') }}">
                </div>

                <div class="full">
                    <label>Anything else you'd like to share?</label>
                    <textarea name="extra_notes">{{ old('extra_notes') }}</textarea>
                </div>
            </div>
        @endif

        <div class="panel" style="padding: 14px; margin-top: 14px;">
            <div class="grid-3">
                <div>
                    <label>Instagram</label>
                    <input type="text" name="instagram" value="{{ old('instagram') }}">
                </div>
                <div>
                    <label>Facebook</label>
                    <input type="text" name="facebook" value="{{ old('facebook') }}">
                </div>
                <div>
                    <label>Tik Tok</label>
                    <input type="text" name="tiktok" value="{{ old('tiktok') }}">
                </div>
                <div>
                    <label>X</label>
                    <input type="text" name="x_handle" value="{{ old('x_handle') }}">
                </div>
            </div>
        </div>

        <div class="checks" style="margin-top: 12px;">
            <label class="check-item"><input type="checkbox" name="age_confirm" value="1" required> I am at least 21 years old</label>
            <label class="check-item"><input type="checkbox" name="terms" value="1" required> I agree with the Terms and Conditions</label>
        </div>

        <div style="margin-top: 14px;">
            <button type="submit" class="submit-btn">Submit Application</button>
        </div>
    </form>
</div>
</body>
</html>

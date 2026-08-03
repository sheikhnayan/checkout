<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Employment Application</title>
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
            max-width: 900px;
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
        h1.page-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0 0 6px 0;
            letter-spacing: -0.02em;
        }
        .page-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 14px;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--accent);
            text-decoration: none;
        }
        .back-link:hover { text-decoration: underline; }
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
        }
        .grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
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
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        textarea { min-height: 100px; resize: vertical; }
        input[type="file"] {
            font-size: 0.88rem;
            color: var(--text-light);
            background: #f1f5f9;
            border: 1px dashed #cbd5e1;
            border-radius: var(--radius-md);
            padding: 10px 12px;
            cursor: pointer;
        }
        .checkbox-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
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
        }
        .check-card input[type="checkbox"] {
            margin-top: 3px;
            accent-color: var(--accent);
            width: 16px;
            height: 16px;
        }
        .check-card span {
            font-size: 0.86rem;
            font-weight: 500;
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
        }
        .consent-card input[type="checkbox"] {
            margin-top: 4px;
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            accent-color: var(--accent);
        }
        .consent-text {
            font-size: 0.85rem;
            color: var(--text-light);
            line-height: 1.5;
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
            width: 100%;
        }
        .submit-btn:hover { background-color: var(--primary-hover); }
        .alert {
            border-radius: var(--radius-md);
            padding: 14px 18px;
            margin-bottom: 20px;
            font-size: 0.92rem;
        }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        @media (max-width: 768px) {
            .container { padding: 16px 12px 32px; }
            .corp-header, .corp-card { padding: 20px 16px; }
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="corp-header">
        <h1 class="page-title">General Employment Application</h1>
        <p class="page-subtitle">Submit your general employment profile for current or upcoming opportunities across affiliated locations.</p>
        <a href="{{ route('jobs.marketplace') }}" class="back-link">← Return to Jobs Marketplace</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <strong>Profile Received:</strong> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the form errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('jobs.pre-apply.submit') }}" enctype="multipart/form-data">
        @csrf
        <div class="corp-card">
            <div class="section-title">General Application Details</div>
            <div class="grid">
                <div class="form-group full">
                    <label class="form-label">Preferred Location / Club <span class="req">*</span></label>
                    <select name="website_id" required>
                        <option value="">Select preferred location</option>
                        @foreach($websites as $website)
                            <option value="{{ $website->id }}" {{ old('website_id') == $website->id ? 'selected' : '' }}>{{ $website->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Full Name <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="First and last name">
                </div>
                <div class="form-group">
                    <label class="form-label">Preferred Role / Position <span class="req">*</span></label>
                    <input type="text" name="preferred_role" value="{{ old('preferred_role') }}" placeholder="e.g. Bartender, Model Server, Hospitality" required>
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
                    <label class="form-label">Phone Number <span class="req">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="(555) 000-0000">
                </div>
                <div class="form-group">
                    <label class="form-label">City <span class="req">*</span></label>
                    <input type="text" name="city" value="{{ old('city') }}" required>
                </div>
                <div class="form-group full">
                    <label class="form-label">State / Province <span class="req">*</span></label>
                    <input type="text" name="state" value="{{ old('state') }}" required>
                </div>

                <div class="form-group full">
                    <label class="form-label">Availability <span class="opt">Optional</span></label>
                    <div class="checkbox-grid">
                        @foreach(['Mon Day','Mon Night','Tue Day','Tue Night','Wed Day','Wed Night','Thu Day','Thu Night','Fri Day','Fri Night','Sat Day','Sat Night','Sun Day','Sun Night'] as $slot)
                            <label class="check-card">
                                <input type="checkbox" name="availability[]" value="{{ $slot }}">
                                <span>{{ $slot }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Instagram <span class="opt">Optional</span></label>
                    <input type="text" name="instagram" value="{{ old('instagram') }}" placeholder="@username">
                </div>
                <div class="form-group">
                    <label class="form-label">Facebook <span class="opt">Optional</span></label>
                    <input type="text" name="facebook" value="{{ old('facebook') }}" placeholder="facebook.com/profile">
                </div>
                <div class="form-group">
                    <label class="form-label">TikTok <span class="opt">Optional</span></label>
                    <input type="text" name="tiktok" value="{{ old('tiktok') }}" placeholder="@username">
                </div>
                <div class="form-group">
                    <label class="form-label">X (Twitter) <span class="opt">Optional</span></label>
                    <input type="text" name="x_handle" value="{{ old('x_handle') }}" placeholder="@username">
                </div>

                <div class="form-group full">
                    <label class="form-label">Experience Summary <span class="req">*</span></label>
                    <textarea name="experience_summary" required placeholder="Briefly detail your experience and background">{{ old('experience_summary') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Resume <span class="opt">Optional</span></label>
                    <input type="file" name="resume">
                </div>
                <div class="form-group">
                    <label class="form-label">Headshot <span class="opt">Optional</span></label>
                    <input type="file" name="headshot">
                </div>

                <div class="form-group full">
                    <label class="form-label">Additional Message <span class="opt">Optional</span></label>
                    <textarea name="message" placeholder="Any extra information for the recruitment team">{{ old('message') }}</textarea>
                </div>
            </div>
        </div>

        <div class="corp-card">
            <div class="section-title">Consents & Declarations</div>

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

            <div style="margin-top: 20px;">
                <button type="submit" class="submit-btn">Submit Preferred-Work Profile</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preferred Club Work Form</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Roboto', sans-serif; background: #f3f2f1; color: #2d2d2d; }
        .wrap { max-width: 900px; margin: 0 auto; padding: 24px 16px 30px; }
        .panel { background: #fff; border: 1px solid #d4d2d0; border-radius: 12px; padding: 18px; margin-bottom: 14px; }
        h1 { margin: 0; color: #2557a7; }
        .grid { display: grid; gap: 12px; grid-template-columns: repeat(2, minmax(0,1fr)); }
        .grid > * { min-width: 0; }
        .full { grid-column: 1 / -1; }
        label { font-weight: 700; font-size: .92rem; display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; border: 1px solid #c9c7c5; border-radius: 8px; padding: 10px 12px; }
        textarea { min-height: 90px; }
        .checks { display: grid; gap: 8px; grid-template-columns: repeat(auto-fit,minmax(190px,1fr)); }
        .check-item { display: flex; align-items: flex-start; gap: 8px; background: #fafafa; border: 1px solid #ece9e7; padding: 8px 10px; border-radius: 8px; white-space: normal; word-break: break-word; }
        .check-item input { width: auto; }
        .btn { background: #2557a7; border: 0; color: #fff; border-radius: 10px; padding: 12px 18px; font-weight: 700; cursor: pointer; }
        .alert { border-radius: 10px; padding: 12px; margin-bottom: 12px; }
        .alert-success { background: #e8f4ec; border: 1px solid #7cc096; color: #114f2a; }
        .alert-danger { background: #fcebeb; border: 1px solid #d77; color: #7e1f1f; }
        @media (max-width: 820px) {
            .grid { grid-template-columns: 1fr; }
            .checks { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="panel">
        <h1>Preferred Club Work Form</h1>
        <p>Didn't find the role you want? Send your profile to your preferred club and role.</p>
        <a href="{{ route('jobs.marketplace') }}">Back to Jobs Marketplace</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the form errors.</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="panel" method="POST" action="{{ route('jobs.pre-apply.submit') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid">
            <div class="full">
                <label>Preferred Club (Required)</label>
                <select name="website_id" required>
                    <option value="">Select club</option>
                    @foreach($websites as $website)
                        <option value="{{ $website->id }}" {{ old('website_id') == $website->id ? 'selected' : '' }}>{{ $website->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Your Name (Required)</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label>Preferred Role (Required)</label>
                <input type="text" name="preferred_role" value="{{ old('preferred_role') }}" placeholder="Bartender / Entertainer / Hospitality" required>
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
                <label>Phone (Required)</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required>
            </div>
            <div>
                <label>City (Required)</label>
                <input type="text" name="city" value="{{ old('city') }}" required>
            </div>
            <div>
                <label>State (Required)</label>
                <input type="text" name="state" value="{{ old('state') }}" required>
            </div>

            <div class="full">
                <label>Availability</label>
                <div class="checks">
                    @foreach(['Mon Day','Mon Night','Tue Day','Tue Night','Wed Day','Wed Night','Thu Day','Thu Night','Fri Day','Fri Night','Sat Day','Sat Night','Sun Day','Sun Night'] as $slot)
                        <label class="check-item"><input type="checkbox" name="availability[]" value="{{ $slot }}"> {{ $slot }}</label>
                    @endforeach
                </div>
            </div>

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

            <div class="full">
                <label>Experience Summary (Required)</label>
                <textarea name="experience_summary" required>{{ old('experience_summary') }}</textarea>
            </div>

            <div>
                <label>Resume (Optional)</label>
                <input type="file" name="resume">
            </div>
            <div>
                <label>Headshot (Optional)</label>
                <input type="file" name="headshot">
            </div>

            <div class="full">
                <label>Additional Message</label>
                <textarea name="message">{{ old('message') }}</textarea>
            </div>
        </div>

        <div class="checks" style="margin-top: 12px;">
            <label class="check-item"><input type="checkbox" name="age_confirm" value="1" required> I am at least 21 years old</label>
            <label class="check-item"><input type="checkbox" name="terms" value="1" required> I agree with the terms and conditions</label>
        </div>

        <div style="margin-top: 14px;">
            <button type="submit" class="btn">Send Preferred-Work Form</button>
        </div>
    </form>
</div>
</body>
</html>

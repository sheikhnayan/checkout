<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }} | Online Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

    <style>
        body {
            background-color: #12131c;
            color: #e0e2ec;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }
        .form-card {
            background: #1e1f2e;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
            max-width: 780px;
            width: 100%;
            padding: 36px;
        }
        .form-control, .form-select {
            background-color: #28293d;
            border: 1px solid #3f425e;
            color: #ffffff !important;
            padding: 11px 15px;
            border-radius: 6px;
        }
        .form-control::placeholder {
            color: #8c90ad;
        }
        .form-control:focus, .form-select:focus {
            background-color: #28293d;
            border-color: #696cff;
            color: #ffffff !important;
            box-shadow: 0 0 10px rgba(105, 108, 255, 0.4);
        }
        .form-check-input {
            background-color: #28293d;
            border-color: #3f425e;
            width: 1.25em;
            height: 1.25em;
        }
        .form-check-input:checked {
            background-color: #696cff;
            border-color: #696cff;
        }
        .btn-submit {
            background: linear-gradient(135deg, #696cff 0%, #393bbf 100%);
            border: none;
            color: #ffffff;
            padding: 12px 32px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(105, 108, 255, 0.5);
        }
        .flatpickr-calendar {
            background: #1e1f2e !important;
            border: 1px solid #3f425e !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5) !important;
        }
        .thank-you-card {
            background: #1e1f2e;
            border: 1px solid #696cff;
            border-radius: 12px;
            padding: 40px 24px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="form-card">
    
    @if(session('form_success'))
        <!-- High-Contrast Accessible Thank You Screen -->
        <div class="thank-you-card">
            <div class="mb-3">
                <i class="bx bx-check-circle text-success" style="font-size: 5rem;"></i>
            </div>
            <h2 class="text-white fw-bold mb-3">Submission Received!</h2>
            <p class="fs-5 text-light mb-4" style="max-width: 600px; margin: 0 auto; color: #d0d2e0 !important;">
                {{ session('form_success') }}
            </p>
            <a href="{{ url()->current() }}" class="btn btn-outline-light mt-2">
                <i class="bx bx-refresh me-1"></i> Submit Another Response
            </a>
        </div>
    @else

        <div class="border-bottom border-secondary pb-3 mb-4">
            <h2 class="text-white fw-bold mb-1">{{ $form->title }}</h2>
            @if($form->description)
                <p class="text-muted mb-0 fs-6">{{ $form->description }}</p>
            @endif
        </div>
        
        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('forms.public.submit', $form->slug) }}" enctype="multipart/form-data">
            @csrf
            
            <div class="row g-3">
                @foreach(($form->fields_schema ?: []) as $field)
                    @php
                        $type = $field['type'] ?? 'text';
                        $key = $field['name'] ?? $field['id'] ?? 'field_' . $loop->index;
                        $label = $field['label'] ?? ucfirst($type);
                        $placeholder = $field['placeholder'] ?? '';
                        $helpText = $field['help_text'] ?? '';
                        $widthClass = $field['width_class'] ?? 'col-12';
                        $required = !empty($field['required']);
                        $options = $field['options'] ?? [];
                    @endphp

                    <div class="{{ $widthClass }}">
                        @if($type === 'heading')
                            <h4 class="text-white mt-3 mb-1 border-bottom border-secondary pb-2">{{ $label }}</h4>
                            @if($helpText)
                                <div class="text-muted small mb-2">{{ $helpText }}</div>
                            @endif
                        @elseif($type === 'checkbox')
                            <!-- Standalone Single Agreement Checkbox -->
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="{{ $key }}" id="{{ $key }}" value="1" {{ old($key) ? 'checked' : '' }} {{ $required ? 'required' : '' }}>
                                <label class="form-check-label text-white fw-medium ms-1" for="{{ $key }}">
                                    {{ $label }}
                                    @if($required)<span class="text-danger">*</span>@endif
                                </label>
                            </div>
                            @if($helpText)
                                <div class="form-text text-muted small mt-1 ms-4">{{ $helpText }}</div>
                            @endif
                        @else
                            <label class="form-label text-white fw-medium">
                                {{ $label }}
                                @if($required)<span class="text-danger">*</span>@endif
                            </label>

                            @if($type === 'textarea')
                                <textarea name="{{ $key }}" class="form-control" rows="4" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}>{{ old($key) }}</textarea>
                            
                            @elseif($type === 'select')
                                <select name="{{ $key }}" class="form-select" {{ $required ? 'required' : '' }}>
                                    <option value="">-- Choose Option --</option>
                                    @foreach($options as $opt)
                                        <option value="{{ $opt }}" {{ old($key) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>

                            @elseif($type === 'radio')
                                <div class="mt-1">
                                    @foreach($options as $opt)
                                        <div class="form-check me-3 d-inline-block">
                                            <input class="form-check-input" type="radio" name="{{ $key }}" id="{{ $key }}_{{ $loop->index }}" value="{{ $opt }}" {{ old($key) === $opt ? 'checked' : '' }} {{ $required ? 'required' : '' }}>
                                            <label class="form-check-label text-light" for="{{ $key }}_{{ $loop->index }}">{{ $opt }}</label>
                                        </div>
                                    @endforeach
                                </div>

                            @elseif($type === 'time')
                                <!-- Safari Compatible Flatpickr Time Picker -->
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white border-secondary"><i class="bx bx-time"></i></span>
                                    <input type="text" name="{{ $key }}" class="form-control flatpickr-time-input" placeholder="{{ $placeholder ?: 'Select Time (e.g. 10:30 PM)' }}" value="{{ old($key) }}" {{ $required ? 'required' : '' }}>
                                </div>

                            @elseif($type === 'file')
                                <input type="file" name="{{ $key }}" class="form-control" {{ $required ? 'required' : '' }}>

                            @else
                                <input type="{{ $type === 'phone' ? 'tel' : $type }}" name="{{ $key }}" class="form-control" placeholder="{{ $placeholder }}" value="{{ old($key) }}" {{ $required ? 'required' : '' }}>
                            @endif

                            @if($helpText)
                                <div class="form-text text-muted small mt-1">{{ $helpText }}</div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-submit">
                    <i class="bx bx-paper-plane me-1"></i> Submit Form
                </button>
            </div>
        </form>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr('.flatpickr-time-input', {
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K",
        time_24hr: false,
        minuteIncrement: 5
    });
});
</script>
</body>
</html>

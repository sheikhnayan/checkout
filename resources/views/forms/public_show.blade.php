<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }} | Online Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <style>
        body {
            background-color: #161722;
            color: #e0e0e0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 10px;
        }
        .form-card {
            background: #232333;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            max-width: 760px;
            width: 100%;
            padding: 36px;
        }
        .form-control, .form-select {
            background-color: #2b2c40;
            border: 1px solid #444564;
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 6px;
        }
        .form-control:focus, .form-select:focus {
            background-color: #2b2c40;
            border-color: #696cff;
            color: #ffffff;
            box-shadow: 0 0 8px rgba(105, 108, 255, 0.4);
        }
        .btn-submit {
            background: linear-gradient(135deg, #696cff 0%, #393bbf 100%);
            border: none;
            color: #ffffff;
            padding: 12px 28px;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(105, 108, 255, 0.4);
        }
    </style>
</head>
<body>

<div class="form-card">
    
    <div class="border-bottom border-secondary pb-3 mb-4">
        <h2 class="text-white fw-bold mb-1">{{ $form->title }}</h2>
        @if($form->description)
            <p class="text-muted mb-0 fs-6">{{ $form->description }}</p>
        @endif
    </div>

    @if(session('form_success'))
        <div class="alert alert-success p-4 rounded text-center">
            <i class="bx bx-check-circle fs-1 text-success d-block mb-2"></i>
            <h4 class="alert-heading text-white fw-bold">Submission Received!</h4>
            <p class="mb-0 text-light">{{ session('form_success') }}</p>
        </div>
    @else
        
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

                            @elseif($type === 'checkbox')
                                <div class="mt-1">
                                    @foreach($options as $opt)
                                        <div class="form-check me-3 d-inline-block">
                                            <input class="form-check-input" type="checkbox" name="{{ $key }}[]" id="{{ $key }}_{{ $loop->index }}" value="{{ $opt }}">
                                            <label class="form-check-label text-light" for="{{ $key }}_{{ $loop->index }}">{{ $opt }}</label>
                                        </div>
                                    @endforeach
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

</body>
</html>

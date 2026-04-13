<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Witness Statement Submission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6fb; }
        .panel { max-width: 980px; margin: 28px auto; background: #fff; border-radius: 14px; box-shadow: 0 8px 24px rgba(0,0,0,.08); padding: 24px; }
    </style>
</head>
<body>
    <div class="panel">
        <h3 class="mb-1">Witness Statement Form</h3>
        <p class="text-muted mb-4">Incident #{{ $incident->id }} | {{ $incident->location_legal_name }}</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('incident.witness.submit', $incident->public_witness_token) }}" enctype="multipart/form-data">
            @csrf
            @include('incident._witness_form_fields', ['incident' => $incident])
            <button type="submit" class="btn btn-primary mt-4">Submit Witness Report</button>
        </form>
    </div>
</body>
</html>

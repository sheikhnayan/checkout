@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Application #{{ $application->id }}</h4>
            <a href="{{ route('admin.jobs.applications') }}" class="btn btn-outline-light">Back</a>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4"><strong>Club:</strong> {{ $application->website->name ?? '-' }}</div>
                    <div class="col-md-4"><strong>Job:</strong> {{ $application->jobPost->title ?? '-' }}</div>
                    <div class="col-md-4"><strong>Type:</strong> {{ ucfirst($application->application_type) }}</div>
                    <div class="col-md-4"><strong>Name:</strong> {{ trim(($application->legal_first_name ?? '') . ' ' . ($application->legal_last_name ?? '')) }}</div>
                    <div class="col-md-4"><strong>Email:</strong> {{ $application->email }}</div>
                    <div class="col-md-4"><strong>Phone:</strong> {{ $application->phone ?: '-' }}</div>
                    <div class="col-md-4"><strong>City:</strong> {{ $application->city ?: '-' }}</div>
                    <div class="col-md-4"><strong>State:</strong> {{ $application->state ?: '-' }}</div>
                    <div class="col-md-4"><strong>Status:</strong> {{ ucfirst($application->status) }}</div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><strong>Structured Data</strong></div>
            <div class="card-body">
                <pre class="mb-0" style="white-space: pre-wrap;">{{ json_encode([
                    'social_handles' => $application->social_handles,
                    'traits' => $application->traits,
                    'skills' => $application->skills,
                    'availability' => $application->availability,
                    'positions' => $application->positions,
                    'employment_history' => $application->employment_history,
                    'education' => $application->education,
                ], JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><strong>Attachments</strong></div>
            <div class="card-body">
                @php $attachments = $application->attachments ?? []; @endphp
                @if(empty($attachments))
                    <p class="mb-0">No attachments.</p>
                @else
                    @foreach($attachments as $key => $value)
                        @if(is_array($value) && isset($value['path']))
                            <p class="mb-1">
                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                <a href="{{ asset($value['path']) }}" target="_blank">{{ $value['name'] ?? $value['path'] }}</a>
                            </p>
                        @elseif(is_array($value))
                            <p class="mb-1"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong></p>
                            @foreach($value as $nested)
                                @if(is_array($nested) && isset($nested['path']))
                                    <div class="ms-3">
                                        <a href="{{ asset($nested['path']) }}" target="_blank">{{ $nested['name'] ?? $nested['path'] }}</a>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header"><strong>Additional Notes</strong></div>
            <div class="card-body">
                <p class="mb-0">{{ $application->additional_notes ?: '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

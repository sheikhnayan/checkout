@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="app-main__inner">
            <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                <h4 class="mb-0">Add Witness Report - Incident #{{ $incident->id }}</h4>
                <a href="{{ route('admin.incident.details', $incident->id) }}" class="btn btn-secondary">Back</a>
            </div>

            <div class="card bg-primary text-white p-3">
                <form method="POST" action="{{ route('admin.incident.witness.store', $incident->id) }}" enctype="multipart/form-data">
                    @csrf
                    @include('incident._witness_form_fields', ['incident' => $incident])
                    <button type="submit" class="btn btn-primary mt-4">Submit Witness Report</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

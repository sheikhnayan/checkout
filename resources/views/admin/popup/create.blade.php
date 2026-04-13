@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="mb-3">Create Checkout Popup - {{ $website->name }}</h4>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card p-4">
            <form action="{{ route('admin.popup.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="website_id" value="{{ $id }}">

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="5">{{ old('message') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Image (optional)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Button Text (optional)</label>
                        <input type="text" name="button_text" class="form-control" value="{{ old('button_text') }}" placeholder="Learn More">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Button URL (optional)</label>
                        <input type="url" name="button_url" class="form-control" value="{{ old('button_url') }}" placeholder="https://example.com">
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Start Date/Time (optional)</label>
                        <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date/Time (optional)</label>
                        <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at') }}">
                    </div>
                </div>

                <div class="form-check mt-3">
                    <input type="hidden" name="show_once_per_session" value="0">
                    <input class="form-check-input" type="checkbox" name="show_once_per_session" value="1" id="show_once_per_session" {{ old('show_once_per_session', 1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="show_once_per_session">Show only once per browser session</label>
                </div>

                <div class="form-check mt-2 mb-4">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', 1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Save Popup</button>
                <a href="{{ route('admin.popup.show', $id) }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

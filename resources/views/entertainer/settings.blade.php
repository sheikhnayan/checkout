@extends('admin.main')

@section('content')
@php
    $performanceDates = collect(old('performance_dates', $entertainer->feedModel?->performanceDates?->pluck('performance_date')->map(fn ($date) => optional($date)->format('Y-m-d'))->all() ?? []))
        ->filter(fn ($date) => !empty($date))
        ->values()
        ->all();

    if (empty($performanceDates)) {
        $performanceDates = [''];
    }
@endphp
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card p-4">
            <h4 class="mb-3">Entertainer Page Customization</h4>
            <form method="POST" action="{{ route('entertainer.portal.settings.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Display Name</label>
                        <input type="text" class="form-control" name="display_name" value="{{ old('display_name', $entertainer->display_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Font Family</label>
                        <input type="text" class="form-control" name="font_family" value="{{ old('font_family', $entertainer->font_family) }}" placeholder="Poppins, sans-serif">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hero Title</label>
                        <input type="text" class="form-control" name="hero_title" value="{{ old('hero_title', $entertainer->hero_title) }}" placeholder="Main headline on your page">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Hero Subtitle</label>
                        <input type="text" class="form-control" name="hero_subtitle" value="{{ old('hero_subtitle', $entertainer->hero_subtitle) }}" placeholder="Short supporting line under the headline">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="4" name="description">{{ old('description', $entertainer->description) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Secondary Description</label>
                        <textarea class="form-control" rows="4" name="secondary_description">{{ old('secondary_description', $entertainer->secondary_description) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label mb-2">Feed Performance Dates</label>
                        <p class="text-muted mb-2">These dates are shown on your feed profile roll call section.</p>
                        <div class="border rounded p-3" id="performanceDateRoster">
                            <div id="performanceDateRows" class="d-grid" style="gap:10px;">
                                @foreach($performanceDates as $date)
                                    <div class="performance-date-row d-flex align-items-center gap-2">
                                        <input type="date" name="performance_dates[]" class="form-control" value="{{ $date }}">
                                        <button type="button" class="btn btn-outline-danger remove-performance-date" title="Remove date">Remove</button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" id="addPerformanceDate" class="btn btn-outline-primary btn-sm mt-3">Add Another Date</button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Profile Image</label>
                        <input type="file" class="form-control" name="profile_image" accept="image/*">
                        @if(!empty($entertainer->profile_image))
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <img src="{{ asset('uploads/' . $entertainer->profile_image) }}" alt="Profile image" style="width:72px;height:72px;border-radius:50%;object-fit:cover;">
                                <small class="text-muted">Current profile image</small>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Banner Image</label>
                        <input type="file" class="form-control" name="banner_image" accept="image/*">
                        @if(!empty($entertainer->banner_image))
                            <div class="mt-2">
                                <img src="{{ asset('uploads/' . $entertainer->banner_image) }}" alt="Banner image" style="width:100%;max-width:320px;height:90px;border-radius:10px;object-fit:cover;">
                            </div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="form-label">Gallery Image</label>
                        <input type="file" class="form-control" name="gallery_image" accept="image/*">
                        <small class="text-muted">Upload one image at a time (maximum 6 total).</small>

                        @if(!empty($entertainer->gallery_images) && count((array) $entertainer->gallery_images))
                            <div class="row g-2 mt-2">
                                @foreach((array) $entertainer->gallery_images as $index => $galleryImage)
                                    <div class="col-md-4 col-sm-6">
                                        <label class="border rounded p-2 d-block" style="background:rgba(255,255,255,0.02);cursor:pointer;">
                                            <img src="{{ asset('uploads/' . $galleryImage) }}" alt="Gallery image {{ $index + 1 }}" style="width:100%;height:120px;object-fit:cover;border-radius:8px;">
                                            <div class="form-check mt-2 mb-0">
                                                <input class="form-check-input" type="checkbox" name="remove_gallery_images[]" value="{{ $index }}" id="remove_gallery_{{ $index }}">
                                                <label class="form-check-label" for="remove_gallery_{{ $index }}">Remove this image</label>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Facebook URL</label>
                        <input type="url" class="form-control" name="facebook_url" value="{{ old('facebook_url', $entertainer->facebook_url) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Instagram URL</label>
                        <input type="url" class="form-control" name="instagram_url" value="{{ old('instagram_url', $entertainer->instagram_url) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">YouTube URL</label>
                        <input type="url" class="form-control" name="youtube_url" value="{{ old('youtube_url', $entertainer->youtube_url) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">TikTok URL</label>
                        <input type="url" class="form-control" name="tiktok_url" value="{{ old('tiktok_url', $entertainer->tiktok_url) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Website URL</label>
                        <input type="url" class="form-control" name="website_url" value="{{ old('website_url', $entertainer->website_url) }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-4">Save Customization</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addDateButton = document.getElementById('addPerformanceDate');
        const dateRows = document.getElementById('performanceDateRows');
        if (!addDateButton || !dateRows) {
            return;
        }

        const bindRemoveActions = () => {
            dateRows.querySelectorAll('.remove-performance-date').forEach((button) => {
                button.onclick = function () {
                    const rows = dateRows.querySelectorAll('.performance-date-row');
                    if (rows.length === 1) {
                        rows[0].querySelector('input').value = '';
                        return;
                    }
                    this.closest('.performance-date-row').remove();
                };
            });
        };

        addDateButton.addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'performance-date-row d-flex align-items-center gap-2';
            row.innerHTML = '<input type="date" name="performance_dates[]" class="form-control"><button type="button" class="btn btn-outline-danger remove-performance-date" title="Remove date">Remove</button>';
            dateRows.appendChild(row);
            bindRemoveActions();
        });

        bindRemoveActions();
    });
</script>
@endsection

@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card p-4">
            <h4 class="mb-3">Affiliate Page Customization</h4>
            <form method="POST" action="{{ route('affiliate.portal.settings.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Display Name</label>
                        <input type="text" class="form-control" name="display_name" value="{{ old('display_name', $affiliate->display_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Font Family</label>
                        <input type="text" class="form-control" name="font_family" value="{{ old('font_family', $affiliate->font_family) }}" placeholder="Poppins, sans-serif">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hero Title</label>
                        <input type="text" class="form-control" name="hero_title" value="{{ old('hero_title', $affiliate->hero_title) }}" placeholder="Main headline on your affiliate page">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Hero Subtitle</label>
                        <input type="text" class="form-control" name="hero_subtitle" value="{{ old('hero_subtitle', $affiliate->hero_subtitle) }}" placeholder="Short supporting line under the headline">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="4" name="description">{{ old('description', $affiliate->description) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Secondary Description</label>
                        <textarea class="form-control" rows="4" name="secondary_description">{{ old('secondary_description', $affiliate->secondary_description) }}</textarea>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            Theme colors are fixed globally and are no longer editable here.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Profile Image</label>
                        <input type="file" class="form-control" name="profile_image" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Banner Image</label>
                        <input type="file" class="form-control" name="banner_image" accept="image/*">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Gallery Images</label>
                        <input type="file" class="form-control" name="gallery_images[]" accept="image/*" multiple>
                        <small class="text-muted">Upload up to 6 gallery images. Uploading new ones replaces the current gallery.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Facebook URL</label>
                        <input type="url" class="form-control" name="facebook_url" value="{{ old('facebook_url', $affiliate->facebook_url) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Instagram URL</label>
                        <input type="url" class="form-control" name="instagram_url" value="{{ old('instagram_url', $affiliate->instagram_url) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">YouTube URL</label>
                        <input type="url" class="form-control" name="youtube_url" value="{{ old('youtube_url', $affiliate->youtube_url) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">TikTok URL</label>
                        <input type="url" class="form-control" name="tiktok_url" value="{{ old('tiktok_url', $affiliate->tiktok_url) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Website URL</label>
                        <input type="url" class="form-control" name="website_url" value="{{ old('website_url', $affiliate->website_url) }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-4">Save Customization</button>
            </form>
        </div>
    </div>
</div>
@endsection

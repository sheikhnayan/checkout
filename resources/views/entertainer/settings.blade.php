@extends('admin.main')

@section('content')
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
                        <label class="form-label">Secondary Description (optional)</label>
                        <textarea class="form-control" rows="4" name="secondary_description">{{ old('secondary_description', $entertainer->secondary_description) }}</textarea>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            Roll call performance dates are managed by club managers from the manager portal.
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
                        <label for="page_gallery_picker" class="form-label">Gallery Images</label>
                        <input type="file" class="form-control" id="page_gallery_picker" accept="image/*">
                        <input type="file" name="gallery_images[]" class="d-none" id="gallery_images" accept="image/*" multiple>
                        <input type="hidden" name="existing_gallery_images" id="existing_gallery_images" value='@json((array) ($entertainer->gallery_images ?? []))'>
                        <small class="form-text text-muted">Upload one image at a time. Added images appear below and can be removed before saving. Maximum 6 total.</small>
                        <div id="page-gallery-preview" class="d-flex flex-wrap gap-2 mt-2"></div>
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
                </div>

                <button type="submit" class="btn btn-primary mt-4">Save Customization</button>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        const picker = document.getElementById('page_gallery_picker');
        const galleryInput = document.getElementById('gallery_images');
        const preview = document.getElementById('page-gallery-preview');
        const existingInput = document.getElementById('existing_gallery_images');

        if (!picker || !galleryInput || !preview || !existingInput) {
            return;
        }

        let existingImages = [];
        try {
            existingImages = JSON.parse(existingInput.value || '[]');
            if (!Array.isArray(existingImages)) {
                existingImages = [];
            }
        } catch (e) {
            existingImages = [];
        }

        let dt = new DataTransfer();

        function syncExisting() {
            existingInput.value = JSON.stringify(existingImages);
        }

        function syncFiles() {
            galleryInput.files = dt.files;
        }

        function render() {
            preview.innerHTML = '';

            existingImages.forEach(function (name, index) {
                const wrapper = document.createElement('div');
                wrapper.className = 'position-relative';
                wrapper.style.width = '96px';
                wrapper.innerHTML = '<img src="/uploads/' + name + '" style="width:96px;height:64px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">'
                    + '<button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" style="line-height:1;padding:2px 6px;" data-existing-index="' + index + '">&times;</button>';
                preview.appendChild(wrapper);
            });

            Array.from(dt.files).forEach(function (file, index) {
                const wrapper = document.createElement('div');
                wrapper.className = 'position-relative';
                wrapper.style.width = '96px';
                const url = URL.createObjectURL(file);
                wrapper.innerHTML = '<img src="' + url + '" style="width:96px;height:64px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">'
                    + '<button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" style="line-height:1;padding:2px 6px;" data-new-index="' + index + '">&times;</button>';
                preview.appendChild(wrapper);
            });
        }

        picker.addEventListener('change', function () {
            const file = picker.files && picker.files[0] ? picker.files[0] : null;
            if (!file) {
                return;
            }

            if ((existingImages.length + dt.files.length) >= 6) {
                alert('Gallery is full. Remove one image before adding another.');
                picker.value = '';
                return;
            }

            dt.items.add(file);
            syncFiles();
            render();
            picker.value = '';
        });

        preview.addEventListener('click', function (event) {
            const existingIndex = event.target.getAttribute('data-existing-index');
            const newIndex = event.target.getAttribute('data-new-index');

            if (existingIndex !== null) {
                existingImages.splice(parseInt(existingIndex, 10), 1);
                syncExisting();
                render();
                return;
            }

            if (newIndex !== null) {
                const idx = parseInt(newIndex, 10);
                const nextDt = new DataTransfer();
                Array.from(dt.files).forEach(function (file, fileIndex) {
                    if (fileIndex !== idx) {
                        nextDt.items.add(file);
                    }
                });
                dt = nextDt;
                syncFiles();
                render();
            }
        });

        syncExisting();
        syncFiles();
        render();
    })();
</script>

@endsection

@extends('admin.main')

@section('content')
<style>
.icon-label {
  display: flex !important;
  flex-direction: column;
  align-items: center;
  padding: 12px;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s;
}

.icon-label.selected {
  border-color: #a774ff;
  background: rgba(167, 116, 255, 0.1);
}

.icon-label i {
  font-size: 24px;
  margin-bottom: 4px;
  color: #999;
  transition: color 0.2s;
}

.icon-label.selected i {
  color: #a774ff;
}
</style>
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card p-4">
            <h4 class="mb-3">affiliate Page Customization</h4>
            <form method="POST" action="{{ route('affiliate.portal.settings.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Display Name</label>
                        <input type="text" class="form-control" name="display_name" value="{{ old('display_name', $affiliate->display_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hero Title</label>
                        <input type="text" class="form-control" name="hero_title" value="{{ old('hero_title', $affiliate->hero_title) }}" placeholder="Main headline on your affiliate page">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Hero Subtitle</label>
                        <input type="text" class="form-control" name="hero_subtitle" value="{{ old('hero_subtitle', $affiliate->hero_subtitle) }}" placeholder="Short supporting line under the headline">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hero Badge 1 Label</label>
                        <input type="text" class="form-control" name="hero_badge_1_label" value="{{ old('hero_badge_1_label', $affiliate->hero_badge_1_label) }}" placeholder="Featured">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hero Badge 1 Subtext</label>
                        <input type="text" class="form-control" name="hero_badge_1_sub" value="{{ old('hero_badge_1_sub', $affiliate->hero_badge_1_sub) }}" placeholder="Premium Partner">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hero Badge 2 Label</label>
                        <input type="text" class="form-control" name="hero_badge_2_label" value="{{ old('hero_badge_2_label', $affiliate->hero_badge_2_label) }}" placeholder="Verified">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hero Badge 2 Subtext</label>
                        <input type="text" class="form-control" name="hero_badge_2_sub" value="{{ old('hero_badge_2_sub', $affiliate->hero_badge_2_sub) }}" placeholder="Trusted Source">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="4" name="description">{{ old('description', $affiliate->description) }}</textarea>
                    </div>

                    <!-- Featured Card Icon Picker -->
                    <div class="col-12">
                        <label class="form-label">Featured Card Icon</label>
                        <small class="d-block text-muted mb-2">Choose an icon for your featured affiliate card</small>
                        <div class="icon-picker-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(70px, 1fr)); gap: 10px; margin-bottom: 12px;">
                            @php
                                $iconOptions = [
                                    'fa-music' => 'Music',
                                    'fa-compact-disc' => 'Disc',
                                    'fa-record-vinyl' => 'Vinyl',
                                    'fa-microphone' => 'Microphone',
                                    'fa-guitar' => 'Guitar',
                                    'fa-headphones' => 'Headphones',
                                    'fa-dumbbell' => 'Fitness',
                                    'fa-fire' => 'Fire',
                                    'fa-star' => 'Star',
                                    'fa-gem' => 'Gem',
                                    'fa-crown' => 'Crown',
                                    'fa-sparkles' => 'Sparkles',
                                    'fa-heart' => 'Heart',
                                    'fa-martini-glass' => 'Cocktail',
                                    'fa-champagne-glasses' => 'Celebration',
                                    'fa-party-horn' => 'Party',
                                    'fa-camera' => 'Camera',
                                    'fa-film' => 'Film',
                                    'fa-theater-masks' => 'Theater',
                                    'fa-palette' => 'Art',
                                ];
                            @endphp
                            @foreach($iconOptions as $icon => $label)
                                @php $isSelected = old('featured_icon', $affiliate->featured_icon ?? '') === $icon; @endphp
                                <div class="icon-option" style="text-align: center; cursor: pointer;">
                                    <input type="radio" name="featured_icon" value="{{ $icon }}" id="icon-{{ $icon }}" class="d-none" {{ $isSelected ? 'checked' : '' }}>
                                    <label for="icon-{{ $icon }}" class="icon-label{{ $isSelected ? ' selected' : '' }}">
                                        <i class="fas {{ $icon }}"></i>
                                        <small style="font-size: 11px; color: #666;">{{ $label }}</small>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Secondary Description (About Panel)</label>
                        <textarea class="form-control" rows="4" name="secondary_description">{{ old('secondary_description', $affiliate->secondary_description) }}</textarea>
                    </div>

                    <div class="col-12">
                        <input type="hidden" name="show_location_section" value="0">
                        <div class="form-check mt-1">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="show_location_section"
                                name="show_location_section"
                                value="1"
                                @checked(old('show_location_section', $affiliate->show_location_section ?? true))
                            >
                            <label class="form-check-label" for="show_location_section">
                                Show primary club info and map section on my public page
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Profile Image</label>
                        <input type="file" class="form-control" name="profile_image" accept="image/*">
                        @if(!empty($affiliate->profile_image))
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <img src="{{ asset('uploads/' . $affiliate->profile_image) }}" alt="Profile image" style="width:72px;height:72px;border-radius:50%;object-fit:cover;">
                                <small class="text-muted">Current profile image</small>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Banner Image</label>
                        <input type="file" class="form-control" name="banner_image" accept="image/*">
                        @if(!empty($affiliate->banner_image))
                            <div class="mt-2">
                                <img src="{{ asset('uploads/' . $affiliate->banner_image) }}" alt="Banner image" style="width:100%;max-width:320px;height:90px;border-radius:10px;object-fit:cover;">
                            </div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label for="page_gallery_picker" class="form-label">Gallery Images</label>
                        <input type="file" class="form-control" id="page_gallery_picker" accept="image/*">
                        <input type="file" name="gallery_images[]" class="d-none" id="gallery_images" accept="image/*" multiple>
                        <input type="hidden" name="existing_gallery_images" id="existing_gallery_images" value='@json((array) ($affiliate->gallery_images ?? []))'>
                        <small class="form-text text-muted">Upload one image at a time. Added images appear below and can be removed before saving. Maximum 6 total.</small>
                        <div id="page-gallery-preview" class="d-flex flex-wrap gap-2 mt-2"></div>
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

    // Icon picker functionality
    document.querySelectorAll('input[name="featured_icon"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove selected class from all labels
            document.querySelectorAll('.icon-label').forEach(label => {
                label.classList.remove('selected');
            });

            // Add selected class to the checked radio's label
            if (this.checked) {
                const label = document.querySelector('label[for="' + this.id + '"]');
                if (label) {
                    label.classList.add('selected');
                }
            }
        });

        // Trigger change event on page load for already-checked radio
        if (radio.checked) {
            const label = document.querySelector('label[for="' + radio.id + '"]');
            if (label) {
                label.classList.add('selected');
            }
        }
    });
</script>
@endsection

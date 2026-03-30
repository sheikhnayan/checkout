@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">

<style>
    .forms-wizard li.done em::before, .lnr-checkmark-circle::before {
  content: "\e87f";
}

.forms-wizard li.done em::before {
  display: block;
  font-size: 1.2rem;
  height: 42px;
  line-height: 40px;
  text-align: center;
  width: 42px;
}

.forms-wizard li.done em {
  font-family: Linearicons-Free;
}

label{
    color: #000 !important;
}
</style>
<style>
  #suggestions {
    list-style: none;
    padding: 0;
    border: 1px solid #ccc;
    max-width: 300px;
    margin-top: 0;
  }

  #suggestions li {
    padding: 8px;
    cursor: pointer;
    background: #fff;
    color: #000;
    border: 1px solid #000;
  }

  #suggestions li:hover {
    background: #eee;
  }
</style>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-xxl-12 mb-6 order-0">
                    <div class="app-main__inner">
                        <div class="app-page-title mt-4" data-step="" data-title="" data-intro="">
                            <div class="page-title-wrapper">
                                <div class="page-title-heading">

                                    <div class="page-title-icon">
                                        <i class="fas fa-id-card icon-gradient bg-arielle-smile"></i>
                                    </div>

                                    <div>
                                        <span class="text-capitalize">
                                            Website
                                        </span>
                                    </div>

                                </div>
                                <div class="page-title-actions">
                                </div>
                            </div>

                            <div class="page-title-subheading opacity-10 mt-3"
                                style="white-space: nowrap; overflow-x: auto;">
                                <nav class="" aria-label="breadcrumb">
                                    <ol class="breadcrumb" style="float: left">

                                        <li class="breadcrumb-item opacity-10">
                                            <a href="#">
                                                <i class="fas fa-home" role="img" aria-hidden="true"></i>
                                                <span class="visually-hidden">Home</span>
                                            </a>
                                            <i class="fas fa-chevron-right ms-1"></i>
                                        </li>

                                        <li class="breadcrumb-item ">
                                            Setting
                                            <i class="fas fa-chevron-right ms-1"></i>
                                        </li>
                                        <li class="active breadcrumb-item" aria-current="page">
                                            Event
                                        </li>

                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg">
                                <div class="card-shadow-primary card-border text-white mb-3 card bg-primary">
                                    <form action="{{ route('admin.event.update', $id) }}" method="post" enctype="multipart/form-data">
                                        @csrf

                                        <div class="card-body">
                                             <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Name</label>
                                                        <input type="text" name="name" class="form-control" id="name" value="{{ $data->name }}" placeholder="Event Name" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="hero_title" class="form-label">Hero Title</label>
                                                        <input type="text" name="hero_title" class="form-control" id="hero_title" value="{{ $data->hero_title }}" placeholder="Optional hero title for event page">
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="hero_subtitle" class="form-label">Hero Subtitle</label>
                                                        <input type="text" name="hero_subtitle" class="form-control" id="hero_subtitle" value="{{ $data->hero_subtitle }}" placeholder="Optional hero subtitle for event page">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="image" class="form-label">Image</label>
                                                        <input type="file" name="image" class="form-control" id="image" placeholder="Image">
                                                        <img src="{{ asset('uploads/'.$data->image) }}" width="200px" style="width: 200px;">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="logo_width" class="form-label">Logo Width (px)</label>
                                                        <input type="number" name="logo_width" class="form-control" id="logo_width" placeholder="Optional" min="1" value="{{ $data->logo_width }}">
                                                        <small class="text-muted">Leave blank for default size</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="logo_height" class="form-label">Logo Height (px)</label>
                                                        <input type="number" name="logo_height" class="form-control" id="logo_height" placeholder="Optional" min="1" value="{{ $data->logo_height }}">
                                                        <small class="text-muted">Leave blank for default size</small>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="date" class="form-label">Date</label>
                                                        <input type="date" name="date" class="form-control" id="date" placeholder="Event Date" value="{{ $data->date }}" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="time" class="form-label">Time Range</label>
                                                        <div style="display: flex; gap: 10px;">
                                                            <input type="text" name="time_start" class="form-control flatpickr-time" id="time_start"
                                                            style="height: 35.1166px"
                                                                placeholder="Start Time"
                                                                value="{{ old('time_start', isset($data->time) && strpos($data->time, '-') !== false ? trim(explode('-', $data->time)[0]) : $data->time) }}"
                                                                required>
                                                            <span style="align-self: center;">to</span>
                                                            <input type="text" name="time_end" class="form-control flatpickr-time" id="time_end"
                                                                style="height: 35.1166px"
                                                                placeholder="End Time"
                                                                value="{{ old('time_end', isset($data->time) && strpos($data->time, '-') !== false ? trim(explode('-', $data->time)[1]) : '') }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Description</label>
                                                        <textarea name="description" class="form-control" id="description" rows="4" placeholder="Event Description" required> {{ $data->description }} </textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="secondary_description" class="form-label">Secondary Description</label>
                                                        <textarea name="secondary_description" class="form-control" id="secondary_description" rows="3" placeholder="Optional secondary description shown on the event page">{{ $data->secondary_description }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="event_gallery_picker" class="form-label">Event Gallery Images</label>
                                                        <input type="file" class="form-control" id="event_gallery_picker" accept="image/*">
                                                        <input type="file" name="gallery_images[]" class="d-none" id="gallery_images" accept="image/*" multiple>
                                                        <input type="hidden" name="existing_gallery_images" id="existing_gallery_images" value='@json((array) ($data->gallery_images ?? []))'>
                                                        <small class="form-text text-muted">Upload one image at a time. Added images appear below and can be removed before saving.</small>
                                                        <div id="event-gallery-preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="status">Status</label>
                                                        <select name="status" class="form-control" id="status" required>
                                                            <option value="1">Active</option>
                                                            <option value="0">Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="website_id" value="{{ $id }}">
                                            </div>

                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <a href="{{ route('admin.event.index') }}" class="btn btn-danger">Cancel</a>



                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add these scripts at the end of your file, before </body> -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr(".flatpickr-time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: false
    });

    (function () {
        const picker = document.getElementById('event_gallery_picker');
        const galleryInput = document.getElementById('gallery_images');
        const preview = document.getElementById('event-gallery-preview');
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

            dt.items.add(file);
            syncFiles();
            render();
            picker.value = '';
        });

        preview.addEventListener('click', function (event) {
            const existingBtn = event.target.closest('[data-existing-index]');
            if (existingBtn) {
                const idx = Number(existingBtn.getAttribute('data-existing-index'));
                if (!Number.isNaN(idx)) {
                    existingImages.splice(idx, 1);
                    syncExisting();
                    render();
                }
                return;
            }

            const newBtn = event.target.closest('[data-new-index]');
            if (newBtn) {
                const idx = Number(newBtn.getAttribute('data-new-index'));
                if (!Number.isNaN(idx)) {
                    const next = new DataTransfer();
                    Array.from(dt.files).forEach(function (file, fileIndex) {
                        if (fileIndex !== idx) {
                            next.items.add(file);
                        }
                    });
                    dt = next;
                    syncFiles();
                    render();
                }
            }
        });

        syncExisting();
        syncFiles();
        render();
    })();
</script>

@endsection


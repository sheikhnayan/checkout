@extends('admin.main')

@section('content')
@php
    $eventTimezone = $website->resolved_timezone ?? \App\Support\WebsiteTimezone::forWebsite($website);
    $eventTimezoneLabel = \App\Support\WebsiteTimezone::label($eventTimezone);
    $eventTimezoneShort = trim(explode(' - ', $eventTimezoneLabel)[0]);
@endphp
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

.toggle-field {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    border: 1px solid var(--admin-border);
    border-radius: 10px;
    background: var(--admin-surface-2);
}

.toggle-field .toggle-text {
    margin: 0;
    color: var(--admin-text);
    font-weight: 600;
    font-size: 14px;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 28px;
}

.toggle-switch-input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
}

.toggle-switch-slider {
    position: absolute;
    inset: 0;
    border-radius: 999px;
    background: #d1d5db;
    transition: background .2s ease;
    cursor: pointer;
}

.toggle-switch-slider::before {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    left: 4px;
    top: 4px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
    transition: transform .2s ease;
}

.toggle-switch-input:checked + .toggle-switch-slider {
    background: #ffcc00;
}

.toggle-switch-input:checked + .toggle-switch-slider::before {
    transform: translateX(20px);
}

.toggle-switch-input:focus-visible + .toggle-switch-slider {
    box-shadow: 0 0 0 3px rgba(255, 204, 0, 0.25);
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

    .package-select {
        background: #131a2a !important;
        color: #f3f4f6 !important;
        border: 1px solid #2c3650 !important;
    }

    .package-select option {
        color: #f3f4f6;
        background: #131a2a;
        padding: 6px 8px;
    }

    .package-select option:checked {
        background: #ffcc00 !important;
        color: #16120a !important;
    }

    .package-row {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px;
    }

    .package-row .package-select {
        flex: 1;
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
                                    <form action="{{ route('admin.event.store') }}" method="post" enctype="multipart/form-data">
                                        @csrf

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Name <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The public name of the event shown in listings and on the checkout page."></i></label>
                                                        <input type="text" name="name" class="form-control" id="name" placeholder="Event Name" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="hero_title" class="form-label">Hero Title <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional large headline displayed at the top of the event page. Leave blank to use the event name."></i></label>
                                                        <input type="text" name="hero_title" class="form-control" id="hero_title" placeholder="Optional hero title for event page">
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="hero_subtitle" class="form-label">Hero Subtitle <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional supporting line shown below the hero title."></i></label>
                                                        <input type="text" name="hero_subtitle" class="form-control" id="hero_subtitle" placeholder="Optional hero subtitle for event page">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="image" class="form-label">Image <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The main promotional image for this event. Used as the banner or thumbnail in listings."></i></label>
                                                        <input type="file" name="image" class="form-control" id="image" placeholder="Image" required>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="logo_width" class="form-label">Logo Width (px) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional pixel width for the event image/logo. Leave blank for default auto-sizing."></i></label>
                                                        <input type="number" name="logo_width" class="form-control" id="logo_width" placeholder="Optional" min="1">
                                                        <small class="text-muted">Leave blank for default size</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="logo_height" class="form-label">Logo Height (px) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional pixel height for the event image/logo. Leave blank for default auto-sizing."></i></label>
                                                        <input type="number" name="logo_height" class="form-control" id="logo_height" placeholder="Optional" min="1">
                                                        <small class="text-muted">Leave blank for default size</small>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Event Dates <span class="text-danger">*</span> <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Select one or more dates on which this event takes place. Click a date again to deselect it."></i></label>
                                                        @php($operatingDays = $website->operating_days ?? [])
                                                        @if(!empty($operatingDays))
                                                            <p class="text-muted small mb-2"><i class="fas fa-info-circle"></i> This club operates on: <strong>{{ implode(', ', array_map('ucfirst', $operatingDays)) }}</strong>. Other days are disabled.</p>
                                                        @else
                                                            <p class="text-muted small mb-2"><i class="fas fa-info-circle"></i> No operating-day restrictions — all days are available.</p>
                                                        @endif
                                                        <input type="text" id="event_dates_picker" class="form-control" placeholder="Click to select one or more event dates" readonly style="cursor:pointer;">
                                                        <input type="hidden" name="event_dates" id="event_dates_hidden" value="{{ old('event_dates', '[]') }}">
                                                        <div id="event-dates-tags" class="d-flex flex-wrap gap-1 mt-2"></div>
                                                        <small class="text-muted">Select multiple non-continuous dates (e.g. Apr 1, Apr 15, Apr 19). Click a date again to deselect it.</small>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="website_id" value="{{ $id }}">

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="attendee_limit" class="form-label">Attendee Limit <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Maximum total number of attendees allowed across all tickets sold for this event. Leave blank for unlimited."></i></label>
                                                        <input type="number" name="attendee_limit" class="form-control" id="attendee_limit" placeholder="Leave blank for unlimited" min="1">
                                                        <small class="text-muted">Maximum number of people allowed to attend this event.</small>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3 mt-3">
                                                        <div class="toggle-field">
                                                            <p class="toggle-text">Show Time Range <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="When enabled, the event time range is shown on the event page and checkout pages."></i></p>
                                                            <label class="toggle-switch" for="show_time_range">
                                                                <input id="show_time_range" type="checkbox" name="show_time_range" class="toggle-switch-input" @checked(old('show_time_range', true))>
                                                                <span class="toggle-switch-slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6" id="event-time-range-wrap">
                                                    <div class="mb-3">
                                                        <label for="time" class="form-label">Time Range ({{ $eventTimezoneShort }}) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The start and end time of the event displayed to customers. Leave both blank if no specific time is shown."></i></label>
                                                        <div style="display: flex; gap: 10px;">
                                                            <input type="text" name="time_start" class="form-control flatpickr-time" id="time_start"
                                                            style="height: 35.1166px"
                                                                placeholder="Start Time"
                                                                value="{{ old('time_start') }}">
                                                            <span style="align-self: center;">to</span>
                                                            <input type="text" name="time_end" class="form-control flatpickr-time" id="time_end"
                                                                style="height: 35.1166px"
                                                                placeholder="End Time"
                                                                value="{{ old('time_end') }}">
                                                        </div>
                                                        <small class="text-muted">Leave both fields blank if no event time is needed. Times use {{ $eventTimezoneLabel }}.</small>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="package_ids" class="form-label">Select Packages <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Link existing packages to this event. Customers selecting this event will choose from these packages."></i></label>
                                                        @php($packageRows = collect(old('package_ids', ['']))->map(fn($id) => (string) $id)->values()->all())
                                                        @if(empty($packageRows))
                                                            @php($packageRows = [''])
                                                        @endif
                                                        <div id="package-rows">
                                                            @foreach($packageRows as $selectedPackageId)
                                                                <div class="package-row">
                                                                    <select name="package_ids[]" class="form-control package-select">
                                                                        <option value="">Select Package</option>
                                                                        @foreach($packages as $package)
                                                                            <option value="{{ $package->id }}" {{ (string) $package->id === $selectedPackageId ? 'selected' : '' }}>{{ $package->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <button type="button" class="btn btn-danger remove-package-row">Remove</button>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button type="button" id="add-package-row" class="btn btn-primary mt-1">Add Package</button>
                                                        <small class="text-muted d-block mt-2">Click Add Package to insert another row.</small>
                                                    </div>
                                                </div>

                                                {{-- <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Description <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Main description text displayed on the event page."></i></label>
                                                        <textarea name="description" class="form-control" id="description" rows="4" placeholder="Event Description" required>{{ old('description') }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="secondary_description" class="form-label">Secondary Description <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional second content block shown beneath the main description on the event page."></i></label>
                                                        <textarea name="secondary_description" class="form-control" id="secondary_description" rows="3" placeholder="Optional secondary description shown on the event page">{{ old('secondary_description') }}</textarea>
                                                    </div>
                                                </div> --}}

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="event_gallery_picker" class="form-label">Event Gallery Images <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload photos shown in a gallery section on the event page. Add one image at a time."></i></label>
                                                        <input type="file" class="form-control" id="event_gallery_picker" accept="image/*" data-criteria-bound="1">
                                                        <input type="file" name="gallery_images[]" class="d-none" id="gallery_images" accept="image/*" multiple>
                                                        <input type="hidden" name="existing_gallery_images" id="existing_gallery_images" value='[]'>
                                                        <small class="form-text text-muted">Upload one image at a time. Added images appear below and can be removed before saving.</small>
                                                        <div id="event-gallery-preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="status" value="{{ old('status', '1') }}">
                                            </div>
                                            <input type="hidden" name="website_id" value="{{ $id }}">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <a href="{{ route('admin.event.index') }}" class="btn btn-danger">Cancel</a>
                                        </div>
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
        const toggle = document.getElementById('show_time_range');
        const wrap = document.getElementById('event-time-range-wrap');

        if (!toggle || !wrap) {
            return;
        }

        function syncVisibility() {
            wrap.style.display = toggle.checked ? '' : 'none';
        }

        toggle.addEventListener('change', syncVisibility);
        syncVisibility();
    })();

    (function () {
        const rowsContainer = document.getElementById('package-rows');
        const addButton = document.getElementById('add-package-row');

        if (!rowsContainer || !addButton) {
            return;
        }

        const firstSelect = rowsContainer.querySelector('select.package-select');
        const optionsMarkup = firstSelect ? firstSelect.innerHTML : '<option value="">Select Package</option>';

        function bindRemove(button) {
            button.addEventListener('click', function () {
                const row = button.closest('.package-row');
                if (!row) {
                    return;
                }
                row.remove();
                if (!rowsContainer.querySelector('.package-row')) {
                    addRow('');
                }
            });
        }

        function addRow(selectedValue) {
            const row = document.createElement('div');
            row.className = 'package-row';

            const select = document.createElement('select');
            select.name = 'package_ids[]';
            select.className = 'form-control package-select';
            select.innerHTML = optionsMarkup;
            if (selectedValue) {
                select.value = selectedValue;
            }

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-danger remove-package-row';
            removeBtn.textContent = 'Remove';

            row.appendChild(select);
            row.appendChild(removeBtn);
            rowsContainer.appendChild(row);
            bindRemove(removeBtn);
        }

        rowsContainer.querySelectorAll('.remove-package-row').forEach(bindRemove);
        addButton.addEventListener('click', function () { addRow(''); });
    })();

    (function () {
        var operatingDays = @json($operatingDays ?? []);
        var dayMap = { 0: 'sunday', 1: 'monday', 2: 'tuesday', 3: 'wednesday', 4: 'thursday', 5: 'friday', 6: 'saturday' };
        var hiddenInput = document.getElementById('event_dates_hidden');
        var tagsContainer = document.getElementById('event-dates-tags');

        if (!hiddenInput || !tagsContainer) { return; }

        var selectedDates = [];
        try {
            var parsed = JSON.parse(hiddenInput.value || '[]');
            selectedDates = Array.isArray(parsed) ? parsed : [];
        } catch(e) { selectedDates = []; }

        function formatDisplay(d) {
            var parts = d.split('-');
            return new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]))
                .toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }

        function renderTags() {
            tagsContainer.innerHTML = '';
            selectedDates.forEach(function (date) {
                var tag = document.createElement('span');
                tag.className = 'badge bg-primary d-inline-flex align-items-center gap-1 me-1 mb-1';
                tag.style.cssText = 'font-size:.8em;padding:5px 8px;';
                tag.appendChild(document.createTextNode(formatDisplay(date) + '\u00a0'));
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.style.cssText = 'background:none;border:none;color:white;cursor:pointer;padding:0;font-size:1.1em;line-height:1;';
                btn.innerHTML = '&times;';
                btn.dataset.date = date;
                btn.addEventListener('click', function () {
                    selectedDates = selectedDates.filter(function (d) { return d !== date; });
                    fp.setDate(selectedDates.map(function (d) { return d + 'T00:00:00'; }), false);
                    renderTags();
                });
                tag.appendChild(btn);
                tagsContainer.appendChild(tag);
            });
            hiddenInput.value = JSON.stringify(selectedDates);
        }

        var disableFn = operatingDays.length > 0 ? [function (date) {
            return !operatingDays.includes(dayMap[date.getDay()]);
        }] : [];

        var fp = flatpickr('#event_dates_picker', {
            mode: 'multiple',
            dateFormat: 'Y-m-d',
            defaultDate: selectedDates.map(function (d) { return d + 'T00:00:00'; }),
            disable: disableFn,
            onChange: function (dates) {
                selectedDates = dates.map(function (d) {
                    var y = d.getFullYear();
                    var m = String(d.getMonth() + 1).padStart(2, '0');
                    var day = String(d.getDate()).padStart(2, '0');
                    return y + '-' + m + '-' + day;
                });
                renderTags();
            }
        });

        renderTags();
    })();

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


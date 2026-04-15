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
                                                        <label for="name" class="form-label">Name</label>
                                                        <input type="text" name="name" class="form-control" id="name" placeholder="Event Name" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="hero_title" class="form-label">Hero Title</label>
                                                        <input type="text" name="hero_title" class="form-control" id="hero_title" placeholder="Optional hero title for event page">
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="hero_subtitle" class="form-label">Hero Subtitle</label>
                                                        <input type="text" name="hero_subtitle" class="form-control" id="hero_subtitle" placeholder="Optional hero subtitle for event page">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="image" class="form-label">Image</label>
                                                        <input type="file" name="image" class="form-control" id="image" placeholder="Image" required>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="logo_width" class="form-label">Logo Width (px)</label>
                                                        <input type="number" name="logo_width" class="form-control" id="logo_width" placeholder="Optional" min="1">
                                                        <small class="text-muted">Leave blank for default size</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="logo_height" class="form-label">Logo Height (px)</label>
                                                        <input type="number" name="logo_height" class="form-control" id="logo_height" placeholder="Optional" min="1">
                                                        <small class="text-muted">Leave blank for default size</small>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="start_date" class="form-label">Start Date</label>
                                                        <input type="date" name="start_date" class="form-control" id="start_date" placeholder="Event Start Date" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="end_date" class="form-label">End Date</label>
                                                        <input type="date" name="end_date" class="form-control" id="end_date" placeholder="Event End Date (optional)">
                                                        <small class="text-muted">Leave blank for a single-day event.</small>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="attendee_limit" class="form-label">Attendee Limit</label>
                                                        <input type="number" name="attendee_limit" class="form-control" id="attendee_limit" placeholder="Leave blank for unlimited" min="1">
                                                        <small class="text-muted">Maximum number of people allowed to attend this event.</small>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="time" class="form-label">Time Range</label>
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
                                                        <small class="text-muted">Leave both fields blank if no event time is needed.</small>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="package_ids" class="form-label">Select Packages</label>
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


            markAllSelected();
        }

        function markAllSelected() {
            Array.from(selected.options).forEach(function (option) {
                option.selected = true;
            });
        }

        addBtn.addEventListener('click', function () {
            moveSelected(available, selected);
        });

        removeBtn.addEventListener('click', function () {
            moveSelected(selected, available);
        });

        addAllBtn.addEventListener('click', function () {
            moveAll(available, selected);
        });

        removeAllBtn.addEventListener('click', function () {
            moveAll(selected, available);
        });

        if (form) {
            form.addEventListener('submit', markAllSelected);
        }

        markAllSelected();
    })();
</script>

<script>
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
</script>

@endsection


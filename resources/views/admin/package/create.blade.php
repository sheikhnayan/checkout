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

.toggle-field {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    border: 1px solid #d7dce4;
    border-radius: 10px;
    background: #fff;
}

.toggle-field .toggle-text {
    margin: 0;
    color: #111827;
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
    background: #00b074;
}

.toggle-switch-input:checked + .toggle-switch-slider::before {
    transform: translateX(20px);
}

.toggle-switch-input:focus-visible + .toggle-switch-slider {
    box-shadow: 0 0 0 3px rgba(0, 176, 116, 0.25);
}

.guest-limit-help {
    display: block;
    color: #6b7280;
    font-size: 12px;
    margin-top: 6px;
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

    .addon-row {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px;
    }

    .addon-row .addon-select {
        flex: 1;
        min-height: 40px;
    }

    .package-feature-row {
        display: grid;
        grid-template-columns: 46px 220px 1fr auto;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px;
    }

    .package-feature-icon-preview {
        width: 46px;
        height: 40px;
        border: 1px solid #d7dce4;
        border-radius: 8px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
    }

    .package-feature-icon-preview i {
        font-size: 16px;
    }

    @media (max-width: 992px) {
        .package-feature-row {
            grid-template-columns: 46px 1fr;
        }
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
                                            Packages
                                        </li>

                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg">
                                <div class="card-shadow-primary card-border text-white mb-3 card bg-primary">
                                    <form action="{{ route('admin.package.store') }}" method="post" enctype="multipart/form-data">
                                        @csrf

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="event_id" class="form-label">Event <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optionally link this package to a specific event. Leave as 'No Event' for standalone packages."></i></label>
                                                        <select name="event_id" class="form-control" id="event_id">
                                                            <option value="" selected>No Event</option>
                                                            @foreach($events as $event)
                                                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Name <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The package name displayed to customers at checkout."></i></label>
                                                        <input type="text" name="name" class="form-control" id="name" placeholder="Package Name" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Price <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The base price of this package. When 'Charge per Quantity' is on, this is multiplied by guest count."></i></label>
                                                        <input type="text" name="price" class="form-control" id="name" placeholder="Enter Price" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="category_id" class="form-label">Category <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Assign to an existing category to organise packages in the checkout shop view."></i></label>
                                                        <select name="category_id" class="form-control" id="category_id">
                                                            <option value="">Select Existing Category</option>
                                                            @foreach($categories as $category)
                                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <small class="text-muted">Categories are scoped to this website.</small>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="new_category_name" class="form-label">Or Create New Category <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Type a new category name here to create it and assign this package to it automatically."></i></label>
                                                        <input type="text" name="new_category_name" class="form-control" id="new_category_name" placeholder="Example: VIP Tables">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="package_type" class="form-label">Product Type * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="'Ticket' = individual entry tickets with a daily limit. 'Package' = table reservations with guest capacity."></i></label>
                                                        <select name="package_type" class="form-control" id="package_type" required onchange="togglePackageTypeFields()">
                                                            <option value="ticket" selected>Ticket</option>
                                                            <option value="table">Package</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6" id="daily_ticket_limit_field">
                                                    <div class="mb-3">
                                                        <label for="daily_ticket_limit" class="form-label">Daily Ticket Limit * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Maximum number of tickets that can be sold for this package in a single day."></i></label>
                                                        <input type="number" name="daily_ticket_limit" class="form-control" id="daily_ticket_limit" placeholder="Maximum tickets per day" min="1">
                                                        <small>Maximum number of tickets that can be sold in a single day.</small>
                                                    </div>
                                                </div>

                                                <div class="col-md-6" id="daily_table_limit_field" style="display:none;">
                                                    <div class="mb-3">
                                                        <label for="daily_table_limit" class="form-label">Daily Table Limit * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Maximum number of tables that can be booked for this package in a single day."></i></label>
                                                        <input type="number" name="daily_table_limit" class="form-control" id="daily_table_limit" placeholder="Maximum tables per day" min="1">
                                                        <small>Maximum number of tables that can be booked in a single day.</small>
                                                    </div>
                                                </div>

                                                <div class="col-md-6" id="guests_per_table_field" style="display:none;">
                                                    <div class="mb-3">
                                                        <label for="guests_per_table" class="form-label">Guests Per Table * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Maximum number of guests allowed per table reservation booking."></i></label>
                                                        <input type="number" name="guests_per_table" class="form-control" id="guests_per_table" placeholder="Number of guests per table" min="1">
                                                        <small>Maximum guests allowed per table reservation.</small>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="toggle-field">
                                                            <p class="toggle-text">Charge per Quantity <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="When enabled, the package price is multiplied by the number of guests the customer selects."></i></p>
                                                            <label class="toggle-switch" for="multiple">
                                                                <input id="multiple" type="checkbox" name="multiple" class="toggle-switch-input" @checked(old('multiple'))>
                                                                <span class="toggle-switch-slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="toggle-field">
                                                            <p class="toggle-text">Transportation <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="When enabled, customers can add a pick-up time and location for transportation during checkout."></i></p>
                                                            <label class="toggle-switch" for="transportation">
                                                                <input id="transportation" type="checkbox" name="transportation" class="toggle-switch-input" @checked(old('transportation'))>
                                                                <span class="toggle-switch-slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                @php
                                                    $packageFeatureIconOptions = [
                                                        'fa-chair' => 'VIP Table',
                                                        'fa-wine-bottle' => 'Bottle',
                                                        'fa-user-shield' => 'VIP Hosts',
                                                        'fa-shield-alt' => 'Entry / Priority',
                                                        'fa-crown' => 'Crown',
                                                        'fa-star' => 'Star',
                                                        'fa-gem' => 'Gem',
                                                        'fa-fire' => 'Fire',
                                                        'fa-bolt' => 'Bolt',
                                                    ];

                                                    $defaultFeatureRows = [
                                                        ['icon' => 'fa-chair', 'text' => 'VIP Table'],
                                                        ['icon' => 'fa-wine-bottle', 'text' => '1 Premium Bottle'],
                                                        ['icon' => 'fa-user-shield', 'text' => 'VIP Hosts'],
                                                        ['icon' => 'fa-shield-alt', 'text' => 'Free Entry'],
                                                    ];

                                                    $oldTexts = old('package_feature_text', []);
                                                    $oldIcons = old('package_feature_icon', []);
                                                    $packageFeatureRows = [];

                                                    if (is_array($oldTexts) && !empty($oldTexts)) {
                                                        foreach ($oldTexts as $index => $oldText) {
                                                            $text = trim((string) $oldText);
                                                            $icon = trim((string) ($oldIcons[$index] ?? 'fa-chair'));

                                                            if ($text === '') {
                                                                continue;
                                                            }

                                                            if (!array_key_exists($icon, $packageFeatureIconOptions)) {
                                                                $icon = 'fa-chair';
                                                            }

                                                            $packageFeatureRows[] = [
                                                                'icon' => $icon,
                                                                'text' => $text,
                                                            ];
                                                        }
                                                    }

                                                    if (empty($packageFeatureRows)) {
                                                        $packageFeatureRows = $defaultFeatureRows;
                                                    }
                                                @endphp

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Description <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="A detailed description of what this package includes. Shown to customers on the checkout page."></i></label>
                                                        <textarea name="description" class="form-control" id="description" rows="4" placeholder="Package Description" required></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Package Features <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Add custom feature rows for this package. Each row includes icon + short text displayed on checkout."></i></label>
                                                        <div id="package-feature-rows">
                                                            @foreach($packageFeatureRows as $featureRow)
                                                                <div class="package-feature-row">
                                                                    <div class="package-feature-icon-preview"><i class="fas {{ $featureRow['icon'] }}"></i></div>
                                                                    <select class="form-control package-feature-icon-select" name="package_feature_icon[]">
                                                                        @foreach($packageFeatureIconOptions as $iconClass => $iconLabel)
                                                                            <option value="{{ $iconClass }}" {{ $featureRow['icon'] === $iconClass ? 'selected' : '' }}>{{ $iconLabel }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <input type="text" class="form-control" name="package_feature_text[]" value="{{ $featureRow['text'] }}" maxlength="120" placeholder="Feature text (e.g., VIP Hosts)">
                                                                    <button type="button" class="btn btn-danger remove-package-feature-row">Remove</button>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button type="button" id="add-package-feature-row" class="btn btn-primary mt-1">Add Feature</button>
                                                        <small class="text-muted d-block mt-2">Only rows with text are saved and shown on checkout.</small>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="status">Status <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Active packages are visible and purchasable on the checkout page. Inactive ones are hidden."></i></label>
                                                        <select name="status" class="form-control" id="status" required>
                                                            <option value="1">Active</option>
                                                            <option value="0">Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" id="addons-row">
                                                <div class="col-12 mb-2">
                                                    <label class="form-label">Add-ons <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Select optional add-ons customers can attach to this package during checkout. Use multiple rows for the same add-on."></i></label>
                                                    @php($addonRows = collect(explode(',', (string) old('addons', '')))->map(fn($id) => trim((string) $id))->filter(fn($id) => $id !== '')->values()->all())
                                                    @if(empty($addonRows))
                                                        @php($addonRows = [''])
                                                    @endif
                                                    <div id="addon-rows">
                                                        @foreach($addonRows as $selectedAddonId)
                                                            <div class="addon-row">
                                                                <select class="form-control addon-select">
                                                                    <option value="">Select Add-on</option>
                                                                    @foreach($addons as $addon)
                                                                        <option value="{{ $addon->id }}" {{ (string) $addon->id === (string) $selectedAddonId ? 'selected' : '' }}>{{ $addon->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <button type="button" class="btn btn-danger remove-addon-row">Remove</button>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button type="button" id="add-addon-row" class="btn btn-primary mt-1">Add Add-on</button>
                                                    <small class="text-muted d-block mt-2">You can select the same add-on multiple times using separate rows.</small>
                                                </div>
                                            </div>
                                            <input type="hidden" name="addons" id="addons-hidden">
                                            <input type="hidden" name="website_id" value="{{ $id }}">
                                            <div id="addons-list"></div>

                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <a href="{{ route('admin.package.index') }}" class="btn btn-danger">Cancel</a>



                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<script>
    function togglePackageTypeFields() {
        const packageType = document.getElementById('package_type' ).value;
        const ticketField = document.getElementById('daily_ticket_limit_field');
        const tableField = document.getElementById('daily_table_limit_field');
        const guestTableField = document.getElementById('guests_per_table_field');
        const ticketInput = document.getElementById('daily_ticket_limit');
        const tableInput = document.getElementById('daily_table_limit');
        const guestTableInput = document.getElementById('guests_per_table');
        
        if (packageType === 'ticket') {
            ticketField.style.display = 'block';
            tableField.style.display = 'none';
            guestTableField.style.display = 'none';
            ticketInput.required = true;
            tableInput.required = false;
            guestTableInput.required = false;
        } else {
            ticketField.style.display = 'none';
            tableField.style.display = 'block';
            guestTableField.style.display = 'block';
            ticketInput.required = false;
            tableInput.required = true;
            guestTableInput.required = true;
        }
    }

    (function () {
        const rowsContainer = document.getElementById('addon-rows');
        const addButton = document.getElementById('add-addon-row');
        const addonsHidden = document.getElementById('addons-hidden');
        const form = rowsContainer ? rowsContainer.closest('form') : null;

        if (!rowsContainer || !addButton || !addonsHidden) {
            return;
        }

        const firstSelect = rowsContainer.querySelector('select.addon-select');
        const optionsMarkup = firstSelect ? firstSelect.innerHTML : '<option value="">Select Add-on</option>';

        function syncAddonsHidden() {
            const values = Array.from(rowsContainer.querySelectorAll('select.addon-select'))
                .map(function (select) {
                    return (select.value || '').trim();
                })
                .filter(function (value) {
                    return value !== '';
                });
            addonsHidden.value = values.join(',');
        }

        function bindRowEvents(row) {
            const select = row.querySelector('select.addon-select');
            const removeBtn = row.querySelector('.remove-addon-row');

            if (select) {
                select.addEventListener('change', syncAddonsHidden);
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', function () {
                    row.remove();
                    if (!rowsContainer.querySelector('.addon-row')) {
                        addRow('');
                    }
                    syncAddonsHidden();
                });
            }
        }

        function addRow(selectedValue) {
            const row = document.createElement('div');
            row.className = 'addon-row';

            const select = document.createElement('select');
            select.className = 'form-control addon-select';
            select.innerHTML = optionsMarkup;
            if (selectedValue) {
                select.value = selectedValue;
            }

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-danger remove-addon-row';
            removeBtn.textContent = 'Remove';

            row.appendChild(select);
            row.appendChild(removeBtn);
            rowsContainer.appendChild(row);
            bindRowEvents(row);
            syncAddonsHidden();
        }

        rowsContainer.querySelectorAll('.addon-row').forEach(bindRowEvents);
        addButton.addEventListener('click', function () { addRow(''); });

        if (form) {
            form.addEventListener('submit', syncAddonsHidden);
        }
        syncAddonsHidden();
    })();

    (function () {
        const rowsContainer = document.getElementById('package-feature-rows');
        const addButton = document.getElementById('add-package-feature-row');

        if (!rowsContainer || !addButton) {
            return;
        }

        const iconOptionsMarkup = Array.from(rowsContainer.querySelectorAll('.package-feature-icon-select option'))
            .map(function (option) {
                return '<option value="' + option.value + '">' + option.textContent + '</option>';
            })
            .join('');

        function updatePreview(row) {
            const select = row.querySelector('.package-feature-icon-select');
            const icon = row.querySelector('.package-feature-icon-preview i');
            if (!select || !icon) {
                return;
            }
            icon.className = 'fas ' + select.value;
        }

        function bindRow(row) {
            const select = row.querySelector('.package-feature-icon-select');
            const removeButton = row.querySelector('.remove-package-feature-row');

            if (select) {
                select.addEventListener('change', function () {
                    updatePreview(row);
                });
            }

            if (removeButton) {
                removeButton.addEventListener('click', function () {
                    row.remove();
                    if (!rowsContainer.querySelector('.package-feature-row')) {
                        addRow('fa-chair', '');
                    }
                });
            }
        }

        function addRow(iconValue, textValue) {
            const row = document.createElement('div');
            row.className = 'package-feature-row';
            row.innerHTML = ''
                + '<div class="package-feature-icon-preview"><i class="fas ' + iconValue + '"></i></div>'
                + '<select class="form-control package-feature-icon-select" name="package_feature_icon[]">' + iconOptionsMarkup + '</select>'
                + '<input type="text" class="form-control" name="package_feature_text[]" maxlength="120" placeholder="Feature text (e.g., VIP Hosts)">'
                + '<button type="button" class="btn btn-danger remove-package-feature-row">Remove</button>';

            rowsContainer.appendChild(row);
            const select = row.querySelector('.package-feature-icon-select');
            const input = row.querySelector('input[name="package_feature_text[]"]');

            if (select) {
                select.value = iconValue;
            }

            if (input) {
                input.value = textValue;
            }

            bindRow(row);
            updatePreview(row);
        }

        rowsContainer.querySelectorAll('.package-feature-row').forEach(function (row) {
            bindRow(row);
            updatePreview(row);
        });

        addButton.addEventListener('click', function () {
            addRow('fa-chair', '');
        });
    })();

    // Initialize package type fields on page load
    document.addEventListener('DOMContentLoaded', function() {
        togglePackageTypeFields();
    });
</script>

@endsection


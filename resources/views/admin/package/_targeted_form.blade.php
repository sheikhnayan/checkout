@php
    $formData = $data ?? null;
    $selectedWebsiteId = (int) old('website_id', $selectedWebsiteId ?? optional($formData)->website_id);
    $selectedAudience = old('audience', $audience ?? optional($formData)->audience);
    $selectedAffiliateValue = (string) old('affiliate_id', ($selectAllAffiliate ?? false) ? '__all__' : (optional($formData)->affiliate_id ?? ''));
    $selectedEntertainerValue = (string) old('entertainer_id', ($selectAllEntertainer ?? false) ? '__all__' : (optional($formData)->entertainer_id ?? ''));
    $selectedType = old('package_type', optional($formData)->package_type ?? 'ticket');
    $selectedAddons = [];

    if ($formData) {
        $selectedAddons = is_array($formData->addons)
            ? $formData->addons
            : (is_object($formData->addons) ? $formData->addons->pluck('addon_id')->toArray() : []);
    }

    if (is_string(old('addons')) && trim((string) old('addons')) !== '') {
        $selectedAddons = collect(explode(',', (string) old('addons')))
            ->map(fn ($id) => trim((string) $id))
            ->filter(fn ($id) => $id !== '')
            ->values()
            ->all();
    }

    if (empty($selectedAddons)) {
        $selectedAddons = [''];
    }
@endphp

<style>
    label { color: #000 !important; }

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

    .targeted-package-note {
        margin-bottom: 18px;
        padding: 12px 14px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.16);
        color: #fff;
        font-size: 13px;
        line-height: 1.5;
    }
</style>

<form action="{{ $formAction }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card-body">
        <div class="targeted-package-note">
            This package will stay out of the general club checkout and only appear on the selected {{ $selectedAudience === 'affiliate' ? 'affiliate' : 'entertainer' }} public page.
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="website_id" class="form-label">Club <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The club or venue this targeted package belongs to."></i></label>
                    <select name="website_id" class="form-control" id="targeted_website_id" {{ $canSelectWebsite ? '' : 'disabled' }}>
                        <option value="">Select Club</option>
                        @foreach($websiteOptions as $websiteOption)
                            <option value="{{ $websiteOption->id }}" @selected($selectedWebsiteId === (int) $websiteOption->id)>{{ $websiteOption->name }}</option>
                        @endforeach
                    </select>
                    @if(!$canSelectWebsite && $selectedWebsiteId)
                        <input type="hidden" name="website_id" value="{{ $selectedWebsiteId }}">
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Audience <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Choose who can see this package: all visitors, customers from a specific affiliate, or via an entertainer link."></i></label>
                    <input type="text" class="form-control" value="{{ ucfirst($selectedAudience) }}" disabled>
                    <input type="hidden" name="audience" value="{{ $selectedAudience }}">
                </div>
            </div>

            @if($selectedAudience === 'affiliate')
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="affiliate_id" class="form-label">Affiliate <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Restrict this package so only the selected affiliate's customers can see and purchase it."></i></label>
                        <select name="affiliate_id" class="form-control" id="affiliate_id">
                            <option value="">Select Affiliate</option>
                            <option value="__all__" @selected($selectedAffiliateValue === '__all__')>Select All Affiliates</option>
                            @foreach($targetOptions['affiliates'] as $affiliate)
                                <option value="{{ $affiliate->id }}" @selected((string) $affiliate->id === $selectedAffiliateValue)>
                                    {{ $affiliate->display_name ?: optional($affiliate->user)->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @else
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="entertainer_id" class="form-label">Entertainer <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Restrict this package to be visible only via the selected entertainer's referral link."></i></label>
                        <select name="entertainer_id" class="form-control" id="entertainer_id">
                            <option value="">Select Entertainer</option>
                            <option value="__all__" @selected($selectedEntertainerValue === '__all__')>Select All Entertainers</option>
                            @foreach($targetOptions['entertainers'] as $entertainer)
                                <option value="{{ $entertainer->id }}" @selected((string) $entertainer->id === $selectedEntertainerValue)>
                                    {{ $entertainer->display_name ?: optional($entertainer->user)->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">Name <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The package name displayed to customers at checkout."></i></label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Package Name" value="{{ old('name', optional($formData)->name) }}" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="price" class="form-label">Price <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The base price of this package. When 'Charge per Quantity' is on, this is multiplied by guest count."></i></label>
                    <input type="text" name="price" class="form-control" id="price" value="{{ old('price', optional($formData)->price) }}" placeholder="Enter Price" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="category_id" class="form-label">Category <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Assign to an existing category to organise packages in the checkout shop view."></i></label>
                    <select name="category_id" class="form-control" id="category_id">
                        <option value="">Select Existing Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', optional($formData)->package_category_id) === (int) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="new_category_name" class="form-label">Or Create New Category <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Type a new category name here to create it and assign this package to it automatically."></i></label>
                    <input type="text" name="new_category_name" class="form-control" id="new_category_name" placeholder="Example: VIP Tables" value="{{ old('new_category_name') }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="package_type" class="form-label">Product Type * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="'Ticket' = individual entry tickets with a daily limit. 'Package' = table reservations with guest capacity."></i></label>
                    <select name="package_type" class="form-control" id="package_type" required onchange="togglePackageTypeFields()">
                        <option value="ticket" @selected($selectedType === 'ticket')>Ticket</option>
                        <option value="table" @selected($selectedType === 'table')>Package</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6" id="daily_ticket_limit_field" {{ $selectedType === 'ticket' ? '' : 'style=display:none;' }}>
                <div class="mb-3">
                    <label for="daily_ticket_limit" class="form-label">Daily Ticket Limit * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Maximum number of tickets that can be sold for this package in a single day."></i></label>
                    <input type="number" name="daily_ticket_limit" class="form-control" id="daily_ticket_limit" value="{{ old('daily_ticket_limit', optional($formData)->daily_ticket_limit) }}" min="1">
                </div>
            </div>

            <div class="col-md-6" id="daily_table_limit_field" {{ $selectedType === 'table' ? '' : 'style=display:none;' }}>
                <div class="mb-3">
                    <label for="daily_table_limit" class="form-label">Daily Table Limit * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Maximum number of tables that can be booked for this package in a single day."></i></label>
                    <input type="number" name="daily_table_limit" class="form-control" id="daily_table_limit" value="{{ old('daily_table_limit', optional($formData)->daily_table_limit) }}" min="1">
                </div>
            </div>

            <div class="col-md-6" id="guests_per_table_field" {{ $selectedType === 'table' ? '' : 'style=display:none;' }}>
                <div class="mb-3">
                    <label for="guests_per_table" class="form-label">Guests Per Table * <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Maximum number of guests allowed per table reservation booking."></i></label>
                    <input type="number" name="guests_per_table" class="form-control" id="guests_per_table" value="{{ old('guests_per_table', optional($formData)->guests_per_table) }}" min="1">
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <div class="toggle-field">
                        <p class="toggle-text">Charge per Quantity <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="When enabled, the package price is multiplied by the number of guests the customer selects."></i></p>
                        <label class="toggle-switch" for="multiple">
                            <input id="multiple" type="checkbox" name="multiple" class="toggle-switch-input" @checked(old('multiple', optional($formData)->multiple == 1))>
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
                            <input id="transportation" type="checkbox" name="transportation" class="toggle-switch-input" @checked(old('transportation', optional($formData)->transportation == 1))>
                            <span class="toggle-switch-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label for="description" class="form-label">Description <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="A detailed description of what this package includes. Shown to customers on the checkout page."></i></label>
                    <textarea name="description" class="form-control" id="description" rows="4" required>{{ old('description', optional($formData)->description) }}</textarea>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label for="status">Status <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Active packages are visible and purchasable on the checkout page. Inactive ones are hidden."></i></label>
                    <select name="status" class="form-control" id="status" required>
                        <option value="1" @selected((string) old('status', optional($formData)->status ?? '1') === '1')>Active</option>
                        <option value="0" @selected((string) old('status', optional($formData)->status ?? '1') === '0')>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row" id="addons-row">
            <div class="col-12 mb-2">
                <label class="form-label">Add-ons <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Select optional add-ons customers can attach to this package during checkout."></i></label>
                <div id="addon-rows">
                    @foreach($selectedAddons as $selectedAddonId)
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

        <input type="hidden" name="addons" id="addons-hidden" value="{{ implode(',', array_filter($selectedAddons, fn ($id) => $id !== '')) }}">

        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        <a href="{{ ($selectedWebsiteId ?: optional($formData)->website_id) ? route('admin.package.show', $selectedWebsiteId ?: optional($formData)->website_id) : route('admin.package.index') }}" class="btn btn-danger">Cancel</a>
    </div>
</form>

<script>
    function togglePackageTypeFields() {
        const packageType = document.getElementById('package_type').value;
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
        const websiteSelect = document.getElementById('targeted_website_id');
        const firstSelect = rowsContainer ? rowsContainer.querySelector('select.addon-select') : null;
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
            row.innerHTML = '<select class="form-control addon-select">' + optionsMarkup + '</select><button type="button" class="btn btn-danger remove-addon-row">Remove</button>';
            rowsContainer.appendChild(row);
            const select = row.querySelector('select.addon-select');
            if (select && selectedValue) {
                select.value = selectedValue;
            }
            bindRowEvents(row);
        }

        if (rowsContainer && addButton && addonsHidden) {
            rowsContainer.querySelectorAll('.addon-row').forEach(bindRowEvents);
            addButton.addEventListener('click', function () {
                addRow('');
            });
            syncAddonsHidden();
        }

        if (websiteSelect && !websiteSelect.disabled) {
            websiteSelect.addEventListener('change', function () {
                const audience = '{{ $selectedAudience }}';
                const websiteId = this.value;
                const basePath = '{{ isset($formData) ? route('admin.package.edit-targeted', $formData->id) : route('admin.package.create-targeted', ['audience' => '__AUDIENCE__']) }}';
                const targetPath = basePath.replace('__AUDIENCE__', audience);
                if (websiteId) {
                    window.location.href = targetPath + '?website_id=' + encodeURIComponent(websiteId);
                } else {
                    window.location.href = targetPath;
                }
            });
        }

        togglePackageTypeFields();
    })();
</script>
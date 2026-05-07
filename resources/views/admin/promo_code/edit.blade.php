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
                                            Promo Code
                                        </li>

                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg">
                                <div class="card-shadow-primary card-border text-white mb-3 card bg-primary">
                                    <form action="{{ route('admin.promo_code.update', $id) }}" method="post" enctype="multipart/form-data">
                                        @csrf

                                        @php
                                            $selectedPackageIds = old('applies_to_package_ids', (array) ($data->applies_to_package_ids ?? []));
                                            $selectedPackageIds = array_map('strval', $selectedPackageIds);
                                            $discountMethod = old('discount_method', $data->discount_method ?? 'code');
                                            $discountType = old('discount_value_type', $data->discount_value_type ?? $data->type ?? 'percentage');
                                            $discountValue = old('discount_value', $data->discount_value ?? $data->percentage ?? 0);
                                            $appliesTo = old('applies_to', $data->applies_to ?? 'all_packages');
                                            $minReqType = old('min_requirement_type', $data->min_requirement_type ?? 'none');
                                        @endphp

                                        <div class="card-body">
                                            <h5 class="mb-3 text-dark">{{ $title ?? 'Promo Code' }}</h5>

                                            <h6 class="form-section-title">Amount Off Products</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="discount_method" class="form-label">Method <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="How the discount is applied — currently only via a code entered by the customer at checkout."></i></label>
                                                        <select name="discount_method" id="discount_method" class="form-control" required>
                                                            <option value="code" {{ $discountMethod === 'code' ? 'selected' : '' }}>Discount Code</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Internal Name <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="A private label for this promo code for your reference only. Not visible to customers."></i></label>
                                                        <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $data->name) }}" placeholder="VIP Spring Offer">
                                                    </div>
                                                </div>
                                                <div class="col-md-4" id="promo-code-col">
                                                    <div class="mb-3">
                                                        <label for="code" class="form-label">Discount Code <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The code customers enter at checkout to apply this discount."></i></label>
                                                        <input type="text" name="promo_code" class="form-control" value="{{ old('promo_code', $data->promo_code) }}" id="code" placeholder="VIP100" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <h6 class="form-section-title">Discount Value</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="discount_value_type" class="form-label">Value Type <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Choose 'Percentage' to discount by a percentage, or 'Fixed Amount' for a set dollar value."></i></label>
                                                        <select name="discount_value_type" class="form-control" id="discount_value_type" required>
                                                            <option value="percentage" {{ $discountType === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                                            <option value="fixed" {{ $discountType === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="discount_value" class="form-label">Value <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The discount amount — enter a percentage number (e.g. 10 for 10%) or a fixed dollar value."></i></label>
                                                        <input type="number" step="0.01" min="0" name="discount_value" class="form-control" id="discount_value" value="{{ $discountValue }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="applies_to" class="form-label">Applies To <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Whether this discount applies to all packages sitewide or only specific selected packages."></i></label>
                                                        <select name="applies_to" class="form-control" id="applies_to" required>
                                                            <option value="all_packages" {{ $appliesTo === 'all_packages' ? 'selected' : '' }}>All Packages</option>
                                                            <option value="specific_packages" {{ $appliesTo === 'specific_packages' ? 'selected' : '' }}>Specific Packages</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" id="specific-packages-row" style="display:none;">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="applies_to_package_ids" class="form-label">Select Packages <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Hold Ctrl / Cmd to select multiple packages this promo code applies to."></i></label>
                                                        <select name="applies_to_package_ids[]" id="applies_to_package_ids" class="form-control" multiple size="6">
                                                            @foreach(($packageOptions ?? []) as $pkg)
                                                                <option value="{{ $pkg['id'] }}" {{ in_array((string) $pkg['id'], $selectedPackageIds, true) ? 'selected' : '' }}>{{ $pkg['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <h6 class="form-section-title">Eligibility</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="eligibility" class="form-label">Customer Eligibility <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Defines who is allowed to use this promo code."></i></label>
                                                        <select name="eligibility" class="form-control" id="eligibility">
                                                            <option value="all_customers" {{ old('eligibility', $data->eligibility ?? 'all_customers') === 'all_customers' ? 'selected' : '' }}>All Customers</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <h6 class="form-section-title">Minimum Purchase Requirements</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="min_requirement_type" class="form-label">Requirement Type <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optionally set a minimum purchase amount or item quantity required before this discount can be applied."></i></label>
                                                        <select name="min_requirement_type" class="form-control" id="min_requirement_type">
                                                            <option value="none" {{ $minReqType === 'none' ? 'selected' : '' }}>No Minimum Requirements</option>
                                                            <option value="amount" {{ $minReqType === 'amount' ? 'selected' : '' }}>Minimum Purchase Amount</option>
                                                            <option value="quantity" {{ $minReqType === 'quantity' ? 'selected' : '' }}>Minimum Quantity of Items</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4" id="min-amount-col" style="display:none;">
                                                    <div class="mb-3">
                                                        <label for="min_purchase_amount" class="form-label">Minimum Amount ($) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Minimum order value in dollars the customer must reach before this code can be applied."></i></label>
                                                        <input type="number" min="0" step="0.01" name="min_purchase_amount" id="min_purchase_amount" class="form-control" value="{{ old('min_purchase_amount', $data->min_purchase_amount) }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4" id="min-qty-col" style="display:none;">
                                                    <div class="mb-3">
                                                        <label for="min_purchase_quantity" class="form-label">Minimum Quantity <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Minimum number of items in the cart required to use this code."></i></label>
                                                        <input type="number" min="1" step="1" name="min_purchase_quantity" id="min_purchase_quantity" class="form-control" value="{{ old('min_purchase_quantity', $data->min_purchase_quantity) }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <h6 class="form-section-title">Maximum Discount Uses</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="usage_limit_total" class="form-label">Usage Limit (Total) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Maximum number of times this code can be used across all customers. Leave blank for unlimited."></i></label>
                                                        <input type="number" min="1" step="1" name="usage_limit_total" id="usage_limit_total" class="form-control" value="{{ old('usage_limit_total', $data->usage_limit_total) }}" placeholder="Leave blank for unlimited">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 d-flex align-items-end">
                                                    <div class="mb-3 form-check">
                                                        <input type="checkbox" class="form-check-input" id="limit_one_per_customer" name="limit_one_per_customer" value="1" {{ old('limit_one_per_customer', $data->limit_one_per_customer) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="limit_one_per_customer">Limit to one use per email address <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Prevents the same email address from using this code more than once."></i></label>
                                                    </div>
                                                </div>
                                            </div>

                                            <h6 class="form-section-title">Combinations</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3 form-check">
                                                        <input type="checkbox" class="form-check-input" id="combine_product_discounts" name="combine_product_discounts" value="1" {{ old('combine_product_discounts', $data->combine_product_discounts) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="combine_product_discounts">Product Discounts <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Allow this code to stack with other product-level discounts."></i></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3 form-check">
                                                        <input type="checkbox" class="form-check-input" id="combine_order_discounts" name="combine_order_discounts" value="1" {{ old('combine_order_discounts', $data->combine_order_discounts) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="combine_order_discounts">Order Discounts <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Allow this code to stack with order-level discounts."></i></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3 form-check">
                                                        <input type="checkbox" class="form-check-input" id="combine_shipping_discounts" name="combine_shipping_discounts" value="1" {{ old('combine_shipping_discounts', $data->combine_shipping_discounts) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="combine_shipping_discounts">Shipping Discounts <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Allow this code to stack with shipping discounts."></i></label>
                                                    </div>
                                                </div>
                                            </div>

                                            <h6 class="form-section-title">Active Dates</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="starts_at" class="form-label">Start Date <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The date and time from which this code becomes valid. Leave blank to make it active immediately."></i></label>
                                                        <input type="datetime-local" name="starts_at" class="form-control" id="starts_at" value="{{ old('starts_at', optional($data->starts_at)->format('Y-m-d\\TH:i')) }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="ends_at" class="form-label">End Date <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The date and time after which this code expires. Leave blank for no expiry."></i></label>
                                                        <input type="datetime-local" name="ends_at" class="form-control" id="ends_at" value="{{ old('ends_at', optional($data->ends_at)->format('Y-m-d\\TH:i')) }}">
                                                    </div>
                                                </div>
                                            </div>

                                            @if(($promoAudience ?? ($data->audience ?? 'club')) === 'affiliate')
                                            <h6 class="form-section-title">Affiliate Target</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="affiliate_id" class="form-label">Specific Affiliate <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Restrict this promo code to customers of a specific affiliate only."></i></label>
                                                        <select name="affiliate_id" class="form-control" id="affiliate_id">
                                                            <option value="">Select affiliate</option>
                                                            @foreach(($targetOptions['affiliates'] ?? collect()) as $affiliate)
                                                                <option value="{{ $affiliate->id }}" {{ old('affiliate_id', $data->affiliate_id) == $affiliate->id ? 'selected' : '' }}>
                                                                    {{ $affiliate->display_name ?: optional($affiliate->user)->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if(($promoAudience ?? ($data->audience ?? 'club')) === 'entertainer')
                                            <h6 class="form-section-title">Entertainer Target</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="entertainer_id" class="form-label">Specific Entertainer <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Restrict this promo code to customers coming via a specific entertainer's link only."></i></label>
                                                        <select name="entertainer_id" class="form-control" id="entertainer_id">
                                                            <option value="">Select entertainer</option>
                                                            @foreach(($targetOptions['entertainers'] ?? collect()) as $entertainer)
                                                                <option value="{{ $entertainer->id }}" {{ old('entertainer_id', $data->entertainer_id) == $entertainer->id ? 'selected' : '' }}>
                                                                    {{ $entertainer->display_name ?: optional($entertainer->user)->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Description <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Internal notes about this promo code. Not visible to customers."></i></label>
                                                        <textarea name="description" class="form-control" id="description" rows="4" placeholder="Promo Code Description">{{ old('description', $data->description) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="audience" value="{{ $promoAudience ?? ($data->audience ?? 'club') }}">
                                            <input type="hidden" name="website_id" value="{{ $data->website_id }}">

                                            <button type="submit" class="btn btn-primary">Update Promo Code</button>
                                            <a href="{{ route('admin.promo_code.index') }}" class="btn btn-danger">Cancel</a>
                                        </div>
                                    </form>

                                    <script>
                                        (function () {
                                            var appliesTo = document.getElementById('applies_to');
                                            var specificPackagesRow = document.getElementById('specific-packages-row');
                                            var minRequirementType = document.getElementById('min_requirement_type');
                                            var minAmountCol = document.getElementById('min-amount-col');
                                            var minQtyCol = document.getElementById('min-qty-col');
                                            var discountMethod = document.getElementById('discount_method');
                                            var promoCodeCol = document.getElementById('promo-code-col');
                                            var codeInput = document.getElementById('code');

                                            function syncAppliesTo() {
                                                if (!appliesTo || !specificPackagesRow) return;
                                                specificPackagesRow.style.display = appliesTo.value === 'specific_packages' ? '' : 'none';
                                            }

                                            function syncMinimumRequirement() {
                                                if (!minRequirementType) return;
                                                var val = minRequirementType.value;
                                                if (minAmountCol) minAmountCol.style.display = val === 'amount' ? '' : 'none';
                                                if (minQtyCol) minQtyCol.style.display = val === 'quantity' ? '' : 'none';
                                            }

                                            function syncDiscountMethod() {
                                                if (!discountMethod || !promoCodeCol || !codeInput) return;
                                                var isCode = discountMethod.value === 'code';
                                                promoCodeCol.style.display = isCode ? '' : 'none';
                                                codeInput.required = isCode;
                                            }

                                            if (appliesTo) appliesTo.addEventListener('change', syncAppliesTo);
                                            if (minRequirementType) minRequirementType.addEventListener('change', syncMinimumRequirement);
                                            if (discountMethod) discountMethod.addEventListener('change', syncDiscountMethod);

                                            syncAppliesTo();
                                            syncMinimumRequirement();
                                            syncDiscountMethod();
                                        })();
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

@endsection


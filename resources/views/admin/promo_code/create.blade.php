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
                                            Add-on
                                        </li>

                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg">
                                <div class="card-shadow-primary card-border text-white mb-3 card bg-primary">
                                    <form action="{{ route('admin.promo_code.store') }}" method="post" enctype="multipart/form-data">
                                        @csrf

                                        <div class="card-body">
                                            <h5 class="mb-3 text-dark">{{ $title ?? 'Promo Code' }}</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Name</label>
                                                        <input type="text" name="name" class="form-control" id="name" placeholder="Promo Code Name" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="percentage" class="form-label">Percentage</label>
                                                        <input type="number" step="0.00001" name="percentage" class="form-control" id="percentage" placeholder="Promo Code Percentage" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="type" class="form-label">Type</label>
                                                        <select name="type" class="form-control" id="type" required>
                                                            <option value="percentage">Percentage</option>
                                                            <option value="fixed">Fixed Amount</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                @if(($promoAudience ?? 'club') === 'affiliate')
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="affiliate_id" class="form-label">Specific Affiliate</label>
                                                        <select name="affiliate_id" class="form-control" id="affiliate_id">
                                                            <option value="">Select affiliate</option>
                                                            @foreach(($targetOptions['affiliates'] ?? collect()) as $affiliate)
                                                                <option value="{{ $affiliate->id }}" {{ old('affiliate_id') == $affiliate->id ? 'selected' : '' }}>
                                                                    {{ $affiliate->display_name ?: optional($affiliate->user)->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                @endif

                                                @if(($promoAudience ?? 'club') === 'entertainer')
                                                @if(($canSelectWebsite ?? false) === true)
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="website_id" class="form-label">Club</label>
                                                        <select name="website_id" class="form-control" id="website_id" required>
                                                            <option value="">Select club first</option>
                                                            @foreach(($websiteOptions ?? collect()) as $website)
                                                                <option value="{{ $website->id }}" {{ (string) old('website_id', $selectedWebsiteId ?? '') === (string) $website->id ? 'selected' : '' }}>
                                                                    {{ $website->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <small class="text-muted">Select a club to load only its entertainers.</small>
                                                    </div>
                                                </div>
                                                @endif

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="entertainer_id" class="form-label">Specific Entertainer</label>
                                                        <select name="entertainer_id" class="form-control" id="entertainer_id" {{ (($canSelectWebsite ?? false) && empty(old('website_id', $selectedWebsiteId ?? null))) ? 'disabled' : '' }}>
                                                            <option value="">Select entertainer</option>
                                                            @foreach(($targetOptions['entertainers'] ?? collect()) as $entertainer)
                                                                <option value="{{ $entertainer->id }}" {{ old('entertainer_id') == $entertainer->id ? 'selected' : '' }}>
                                                                    {{ $entertainer->display_name ?: optional($entertainer->user)->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @if(($canSelectWebsite ?? false) && empty(old('website_id', $selectedWebsiteId ?? null)))
                                                            <small class="text-muted">Choose a club first to enable entertainer selection.</small>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="code" class="form-label">Promo Code</label>
                                                        <input type="text" name="promo_code" class="form-control" id="code" placeholder="Promo Code" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Description</label>
                                                        <textarea name="description" class="form-control" id="description" rows="4" placeholder="Promo Code Description"></textarea>
                                                    </div>
                                                </div>


                                            </div>
                                            <input type="hidden" name="audience" value="{{ $promoAudience ?? 'club' }}">
                                            @if(($promoAudience ?? 'club') !== 'entertainer' || !($canSelectWebsite ?? false))
                                                <input type="hidden" name="website_id" value="{{ old('website_id', $id) }}">
                                            @endif
                                            <div id="addons-list"></div>

                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <a href="{{ route('admin.promo_code.index') }}" class="btn btn-danger">Cancel</a>



                                    </form>

                                    @if(($promoAudience ?? 'club') === 'entertainer' && ($canSelectWebsite ?? false))
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            var websiteSelect = document.getElementById('website_id');

                                            if (websiteSelect) {
                                                websiteSelect.addEventListener('change', function () {
                                                    var selectedId = websiteSelect.value || '';
                                                    var baseUrl = '{{ route('admin.promo_code.create-targeted', 'entertainer') }}';
                                                    window.location.href = selectedId
                                                        ? baseUrl + '?website_id=' + encodeURIComponent(selectedId)
                                                        : baseUrl;
                                                });
                                            }
                                        });
                                    </script>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

@endsection


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
                                            Setting
                                        </li>

                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg">
                                <div class="card-shadow-primary card-border text-white mb-3 card bg-primary">
                                    <form action="{{ route('admin.setting.update', $data->id) }}" method="post" enctype="multipart/form-data">
                                        @csrf

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="authorize_key" class="form-label">Authorize App Key <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The API Login ID for your Authorize.net payment account."></i></label>
                                                        <input type="text" name="authorize_key" class="form-control" id="authorize_key" value="{{ $data->authorize_key }}" placeholder="Authorize App Key" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="authorize_secret" class="form-label">Authorize Secret Key <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The Transaction Key for your Authorize.net payment account."></i></label>
                                                        <input type="text" name="authorize_secret" value="{{ $data->authorize_secret }}" class="form-control" id="authorize_secret" placeholder="Authorize Secret Key" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="stripe_key" class="form-label">Stripe App Key <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The Publishable Key for your Stripe account, used in the frontend checkout form."></i></label>
                                                        <input type="text" name="stripe_key" class="form-control" id="stripe_key" value="{{ $data->stripe_key }}" placeholder="Stripe App Key" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="stripe_secret" class="form-label">Stripe Secret Key <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The Secret Key for your Stripe account. Keep this private — never expose it publicly."></i></label>
                                                        <input type="text" name="stripe_secret" value="{{ $data->stripe_secret }}" class="form-control" id="stripe_secret" placeholder="Stripe Secret Key" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3 p-3" style="border:1px solid #e5e7eb; border-radius:8px; background:#f9fafb;">
                                                        <label class="form-label d-block mb-2">
                                                            Payment Gateway Mode
                                                            <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Controls the Authorize.Net / Stripe environment for clubs that use the GLOBAL keys above. Sandbox = test only, no real charges. Uncheck to process REAL payments. A per-club override on the website Payment Settings page still takes precedence."></i>
                                                        </label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" name="sandbox_mode" id="sandbox_mode" value="1" @checked(old('sandbox_mode', $data->sandbox_mode ?? true))>
                                                            <label class="form-check-label" for="sandbox_mode">
                                                                Sandbox (test) mode — <strong>uncheck to go LIVE</strong> and process real payments
                                                            </label>
                                                        </div>
                                                        <small class="d-block mt-2">
                                                            Current global mode:
                                                            <strong class="{{ ($data->sandbox_mode ?? true) ? 'text-warning' : 'text-success' }}">
                                                                {{ ($data->sandbox_mode ?? true) ? 'SANDBOX (test)' : 'LIVE (real charges)' }}
                                                            </strong>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Submit</button>


                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

@endsection


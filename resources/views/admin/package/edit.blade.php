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
                                            <a href="/admins">
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
                                <div class="card-shadow-primary card-border text-white mb-3 card bg-primary" style="background: #fff !important;">
                                    <form action="{{ route('admin.package.update', $id) }}" method="post" enctype="multipart/form-data">
                                        @csrf

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="event_id" class="form-label">Event</label>
                                                        <select name="event_id" class="form-control" id="event_id">
                                                            <option value="null" selected disabled>Select Event</option>
                                                            @foreach($events as $event)
                                                                <option value="{{ $event->id }}" {{ $data->event_id == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Name</label>
                                                        <input type="text" name="name" class="form-control" id="name" placeholder="Package Name" value="{{ $data->name }}" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Price</label>
                                                        <input type="text" name="price" class="form-control" id="name" value="{{ $data->price }}" placeholder="Enter Price" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Number Of Guests</label>
                                                        <input type="number" name="number_of_guest" class="form-control" value="{{ $data->number_of_guest }}" id="name" placeholder="Enter Number Of Guests" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label>
                                                            <input type="checkbox" name="multiple" @if ($data->multiple == 1)
                                                                checked
                                                            @endif />
                                                            Multiple
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label>
                                                            <input type="checkbox" name="transportation" @if ($data->transportation == 1)
                                                                checked
                                                            @endif />
                                                            Transportation
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Description</label>
                                                        <textarea name="description" class="form-control" id="description" rows="4" placeholder="Package Description" required>{{ $data->description }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="status">Status</label>
                                                        <select name="status" class="form-control" id="status" required>
                                                            <option {{ $data->status == 1 ? 'selected' : '' }} value="1">Active</option>
                                                            <option {{ $data->status == 0 ? 'selected' : '' }} value="0">Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                                // $data->addons should be an array of IDs for selection
                                                $selectedAddons = is_array($data->addons) ? $data->addons : (is_object($data->addons) ? $data->addons->pluck('addon_id')->toArray() : []);
                                            @endphp
                                            <div class="row" id="addons-row">
                                                <div class="col-12 mb-2">
                                                    <label class="form-label">Add-ons</label>
                                                    <select id="addon-select" class="form-control" multiple>
                                                        @foreach($addons as $addon)
                                                            <option value="{{ $addon->id }}" {{ in_array($addon->id, $selectedAddons) ? 'selected' : '' }}>{{ $addon->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple.</small>
                                                </div>
                                            </div>
                                            <input type="hidden" name="addons" id="addons-hidden" value="{{ implode(',', $selectedAddons) }}">
                                            <input type="hidden" name="website_id" value="{{ $id }}">
                                            <div id="addons-list"></div>
                                            </div>

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
    // When the addon select changes, update the hidden input with selected IDs (comma separated)
    document.getElementById('addon-select').addEventListener('change', function() {
        const selected = Array.from(this.selectedOptions).map(opt => opt.value);
        document.getElementById('addons-hidden').value = selected.join(',');
    });
</script>

@endsection


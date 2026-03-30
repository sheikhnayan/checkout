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
                                                        <label for="event_id" class="form-label">Event</label>
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
                                                        <label for="name" class="form-label">Name</label>
                                                        <input type="text" name="name" class="form-control" id="name" placeholder="Package Name" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Price</label>
                                                        <input type="text" name="price" class="form-control" id="name" placeholder="Enter Price" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="category_id" class="form-label">Category</label>
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
                                                        <label for="new_category_name" class="form-label">Or Create New Category</label>
                                                        <input type="text" name="new_category_name" class="form-control" id="new_category_name" placeholder="Example: VIP Tables">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Number Of Guests</label>
                                                        <input type="number" name="number_of_guest" class="form-control" id="name" placeholder="Enter Number Of Guests" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="toggle-field">
                                                            <p class="toggle-text">Multiple</p>
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
                                                            <p class="toggle-text">Transportation</p>
                                                            <label class="toggle-switch" for="transportation">
                                                                <input id="transportation" type="checkbox" name="transportation" class="toggle-switch-input" @checked(old('transportation'))>
                                                                <span class="toggle-switch-slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Description</label>
                                                        <textarea name="description" class="form-control" id="description" rows="4" placeholder="Package Description" required></textarea>
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

                                            <div class="row" id="addons-row">
                                                <div class="col-12 mb-2">
                                                    <label class="form-label">Add-ons</label>
                                                    <select id="addon-select" class="form-control" multiple>
                                                        @foreach($addons as $addon)
                                                            <option value="{{ $addon->id }}">{{ $addon->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple.</small>
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
    // When the addon select changes, update the hidden input with selected IDs (comma separated)
    document.getElementById('addon-select').addEventListener('change', function() {
        const selected = Array.from(this.selectedOptions).map(opt => opt.value);
        document.getElementById('addons-hidden').value = selected.join(',');
    });
</script>

@endsection


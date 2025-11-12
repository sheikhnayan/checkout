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
                                            Website
                                        </li>

                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg">
                                <div class="card-shadow-primary card-border text-white mb-3 card bg-primary" style="background: #fff !important;">
                                    <form action="{{ route('admin.website.store') }}" method="post" enctype="multipart/form-data">
                                        @csrf

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Website Name</label>
                                                        <input type="text" name="name" class="form-control" id="name" placeholder="Website Name" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Domain</label>
                                                        <input type="text" name="domain" class="form-control" id="name" placeholder="Enter Domain" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="logo" class="form-label">Logo</label>
                                                        <input type="file" name="logo" class="form-control" id="logo" placeholder="Logo" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="location" class="form-label">Location</label>
                                                        <input type="text" name="location" class="form-control" id="location-input" placeholder="Location" required autocomplete="off">
                                                        <ul id="suggestions"></ul>
                                                        <input type="hidden" name="lat" id="latitude">
                                                        <input type="hidden" name="long" id="longitude">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="phone" class="form-label">Phone</label>
                                                        <input type="text" name="phone" class="form-control" id="phone" placeholder="Phone" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="phone" class="form-label">Email</label>
                                                        <input type="text" name="email" class="form-control" id="email" placeholder="Email" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Contact Emails</label>
                                                        <div id="emails-wrapper">
                                                            <div class="row mb-2 email-group">
                                                                <div class="col-5">
                                                                    <input type="text" class="form-control email-name" placeholder="Name">
                                                                </div>
                                                                <div class="col-5">
                                                                    <input type="email" class="form-control email-address" placeholder="Email Address">
                                                                </div>
                                                                <div class="col-2">
                                                                    <button type="button" class="btn btn-success add-email w-100" title="Add Email"><i class="fa fa-plus"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="emails" id="emails-json">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="gratuity_fee" class="form-label">Gratuity Fee (%)</label>
                                                        <input type="number" name="gratuity_fee" class="form-control" id="gratuity_fee" placeholder="Gratuity Fee" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="password" class="form-label">Refundable Fee (%)</label>
                                                        <input type="number" name="refundable_fee" class="form-control" id="refundable_fee" placeholder="Refundable Fee" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="password" class="form-label">Description</label>
                                                        <textarea name="description" class="form-control" id="description" placeholder="Description" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h3>SMTP Configuration</h3>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="host" class="form-label">Host</label>
                                                                <input type="text" name="host" class="form-control" id="host" placeholder="SMTP Host">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="port" class="form-label">Port</label>
                                                                <input type="number" name="port" class="form-control" id="port" placeholder="SMTP Port">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="username" class="form-label">Username</label>
                                                                <input type="text" name="username" class="form-control" id="username" placeholder="SMTP Username">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="password" class="form-label">Password</label>
                                                                <input type="password" name="password" class="form-control" id="password" placeholder="SMTP Password">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="encryption" class="form-label">Encryption</label>
                                                                <select name="encryption" class="form-select" id="encryption">
                                                                    <option value="tls">TLS</option>
                                                                    <option value="ssl">SSL</option>
                                                                    <option value="none">None</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="from_address" class="form-label">From Address</label>
                                                                <input type="email" name="from_address" class="form-control" id="from_address" placeholder="From Address">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="from_name" class="form-label">From Name</label>
                                                                <input type="text" name="from_name" class="form-control" id="from_name" placeholder="From Name">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h3>Redirect Pages</h3>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="success_page" class="form-label">Success Page</label>
                                                                <input type="text" name="success_page" class="form-control" id="success_page" placeholder="Success Page URL">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="privacy_policy" class="form-label">Privacy & Policy Page</label>
                                                                <input type="text" name="policy" class="form-control" id="privacy_policy" placeholder="Privacy & Policy Page URL">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="terms_conditions" class="form-label">Terms & Conditions Page</label>
                                                                <input type="text" name="terms" class="form-control" id="terms_conditions" placeholder="Terms & Conditions Page URL">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <a href="{{ route('admin.website.index') }}" class="btn btn-danger">Cancel</a>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Geoapify Place Autocomplete -->
            {{-- <link rel="stylesheet" href="https://unpkg.com/@geoapify/geocoder-autocomplete@2.1.0/styles/minimal.css">
            <script src="https://unpkg.com/@geoapify/geocoder-autocomplete@2.1.0/dist/umd/geocoder-autocomplete.min.js"></script>
            <script>
                function initGeoapifyAutocomplete() {
                    var input = document.getElementById('location-input');
                    if (!input || typeof window.Geoapify === 'undefined' || typeof window.Geoapify.GeocoderAutocomplete === 'undefined') {
                        // Try again in 100ms if script not loaded yet
                        setTimeout(initGeoapifyAutocomplete, 100);
                        return;
                    }
                    var autocomplete = new window.Geoapify.GeocoderAutocomplete(
                        input,
                        'f3d9d402ade54545afa0c674f26ea633',
                        {
                            placeholder: 'Search for location',
                            lang: 'en',
                            limit: 5,
                            type: 'city,street,address',
                        }
                    );
                    autocomplete.on('select', function (value) {
                        if (value && value.properties && value.properties.lat && value.properties.lon) {
                            document.getElementById('latitude').value = value.properties.lat;
                            document.getElementById('longitude').value = value.properties.lon;
                        }
                    });
                }
                window.addEventListener('DOMContentLoaded', initGeoapifyAutocomplete);
            </script> --}}
            <script>
            const input = document.getElementById("location-input");
            const suggestions = document.getElementById("suggestions");

            input.addEventListener("input", function () {
                const value = input.value;

                if (value.length < 3) {
                suggestions.innerHTML = "";
                return;
                }

                fetch(`https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(value)}&apiKey=f3d9d402ade54545afa0c674f26ea633`)
                .then(response => response.json())
                .then(result => {
                    suggestions.innerHTML = "";
                    result.features.forEach(place => {
                    const li = document.createElement("li");
                    li.textContent = place.properties.formatted;
                    li.addEventListener("click", () => {
                        input.value = place.properties.formatted;
                        suggestions.innerHTML = "";

                        // Get coordinates
                        const lat = place.geometry.coordinates[1];
                        const lon = place.geometry.coordinates[0];
                        document.getElementById("latitude").value = lat;
                        document.getElementById("longitude").value = lon;
                        console.log("Selected Location:", lat, lon);
                    });
                    suggestions.appendChild(li);
                    });
                });
            });
            </script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
            // Dynamic name/email pairs as objects in one array
            $(document).on('click', '.add-email', function() {
                const emailGroup = `<div class=\"row mb-2 email-group\">\
                    <div class=\"col-5\">\
                        <input type=\"text\" class=\"form-control email-name\" placeholder=\"Name\" required>\
                    </div>\
                    <div class=\"col-5\">\
                        <input type=\"email\" class=\"form-control email-address\" placeholder=\"Email Address\" required>\
                    </div>\
                    <div class=\"col-2\">\
                        <button type=\"button\" class=\"btn btn-danger remove-email w-100\" title=\"Remove\"><i class=\"fa fa-minus\"></i></button>\
                    </div>\
                </div>`;
                $('#emails-wrapper').append(emailGroup);
            });
            $(document).on('click', '.remove-email', function() {
                $(this).closest('.email-group').remove();
            });
            // Serialize name/email pairs to JSON on submit
            $('form').on('submit', function(e) {
                var emails = [];
                $('#emails-wrapper .email-group').each(function() {
                    var name = $(this).find('.email-name').val();
                    var address = $(this).find('.email-address').val();
                    if (name && address) {
                        emails.push({name: name, email: address});
                    }
                });
                $('#emails-json').val(JSON.stringify(emails));
            });
            </script>

@endsection


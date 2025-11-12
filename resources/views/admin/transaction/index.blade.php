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
                                            Transaction
                                        </li>

                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg">
                                <div class="card-shadow-primary card-border text-white mb-3 card bg-primary p-4" style="background: #fff !important;">
                                    <div class="row mb-3">
                                        @if(auth()->user()->isAdmin())
                                        <div class="col-md-3">
                                            <label>Filter by Website:</label>
                                            <select id="websiteFilter" class="form-select">
                                                <option value="">All Websites</option>
                                                @foreach(\App\Models\Website::all() as $website)
                                                    <option value="{{ $website->name }}">{{ $website->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif
                                        <div class="col-md-3">
                                            <label>Filter by Type:</label>
                                            <select id="typeFilter" class="form-select">
                                                <option value="">All Types</option>
                                                <option value="package">Package</option>
                                                <option value="reservation">Reservation</option>
                                            </select>
                                        </div>
                                        {{-- <div class="col-md-4">
                                            <label>Date Range:</label>
                                            <input type="text" id="dateRange" class="form-control" placeholder="Select date range" autocomplete="off">
                                        </div> --}}
                                    </div>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll"></th>
                                                <th>Order ID</th>
                                                <th>Transaction ID</th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Package</th>
                                                <th>Event</th>
                                                <th>Type</th>
                                                @if(auth()->user()->isAdmin())
                                                <th>Website</th>
                                                @endif
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($data->isEmpty())
                                                <tr>
                                                    <td colspan="{{ auth()->user()->isAdmin() ? '11' : '10' }}" class="text-center">No donations found.</td>
                                                </tr>
                                            @else
                                                @foreach ($data as $item)
                                                @if ($item->type == 'package')
                                                    <tr>
                                                        <td><input type="checkbox" class="row-check" value="{{ $item->id }}"></td>
                                                        <td>#0{{ $item->id }}</td>
                                                        <td class="text-break"> {{ $item->transaction_id }} </td>
                                                        <td>{{ $item->package_first_name }} {{ $item->package_last_name }}</td>
                                                        <td>{{ $item->package_phone }}</td>
                                                        <td>{{ $item->package_email }}</td>
                                                        <td>{{ $item->package->name }}</td>
                                                        <td>{{ $item->event->name ?? null}}</td>
                                                        @if ($item->type == 'package')
                                                            <td>Package</td>
                                                        @else
                                                            <td>Reservation</td>
                                                        @endif
                                                        @if(auth()->user()->isAdmin())
                                                        <td>{{ $item->website->name ?? 'N/A' }}</td>
                                                        @endif
                                                        <td>${{ $item->total }}</td>
                                                        <td>
                                                            @if($item->status == 1)
                                                                <span class="badge bg-success">Completed</span>
                                                            @elseif($item->status == 0)
                                                                <span class="badge bg-danger">Canceled</span>
                                                            @elseif($item->status == 2)
                                                                <span class="badge bg-warning text-dark">Refunded</span>
                                                            @else
                                                                <span class="badge bg-secondary">Unknown</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}</td>
                                                        <td>
                                                            @php
                                                                $addons = '';
                                                                $ads = explode(',', $item->addons);
                                                                foreach ($ads as $key => $value) {
                                                                    $addon = \App\Models\Addon::find($value);
                                                                    if ($addon) {
                                                                        $addons .= $addon->name . ', ';
                                                                    }
                                                                }
                                                            @endphp
                                                            @if ($item->type == 'package')
                                                            <button type="button" class="btn btn-info btn-sm view-btn" data-bs-toggle="modal" data-bs-target="#viewTransactionModal"
                                                                data-transaction_id="{{ $item->transaction_id }}"
                                                                data-package_id="{{ $item->package->name }}"
                                                                data-package_first_name="{{ $item->package_first_name }}"
                                                                data-package_last_name="{{ $item->package_last_name }}"
                                                                data-package_phone="{{ $item->package_phone }}"
                                                                data-package_email="{{ $item->package_email }}"
                                                                data-package_dob="{{ $item->package_dob }}"
                                                                data-package_note="{{ $item->package_note }}"
                                                                data-package_number_of_guest="{{ $item->package_number_of_guest }}"
                                                                data-transportation_pickup_time="{{ $item->transportation_pickup_time }}"
                                                                data-transportation_address="{{ $item->transportation_address }}"
                                                                data-transportation_phone="{{ $item->transportation_phone }}"
                                                                data-transportation_guest="{{ $item->transportation_guest }}"
                                                                data-transportation_note="{{ $item->transportation_note }}"
                                                                data-payment_first_name="{{ $item->payment_first_name }}"
                                                                data-payment_last_name="{{ $item->payment_last_name }}"
                                                                data-payment_phone="{{ $item->payment_phone }}"
                                                                data-payment_email="{{ $item->payment_email }}"
                                                                data-payment_address="{{ $item->payment_address }}"
                                                                data-payment_city="{{ $item->payment_city }}"
                                                                data-payment_state="{{ $item->payment_state }}"
                                                                data-payment_country="{{ $item->payment_country }}"
                                                                data-payment_dob="{{ $item->payment_dob }}"
                                                                data-payment_zip_code="{{ $item->payment_zip_code }}"
                                                                data-type="{{ $item->type }}"
                                                                data-status="{{ $item->status }}"
                                                                data-ip_address="{{ $item->ip_address }}"
                                                                data-website_id="{{ $item->website->name }}"
                                                                data-event_id="{{ $item->event->name ?? null}}"
                                                                data-addons="{{ $addons }}"
                                                                data-business_company="{{ $item->business_company }}"
                                                                data-business_vat="{{ $item->business_vat }}"
                                                                data-business_address="{{ $item->business_address }}"
                                                                data-business_purpose="{{ $item->business_purpose }}"
                                                                data-total="{{ $item->total }}"
                                                                data-subtotal="{{ $item->actual_total }}"
                                                                data-refundable="{{ ($item->actual_total / 100)* $item->website->refundable_fee }}"
                                                                data-gratuity="{{ ($item->actual_total / 100)* $item->website->gratuity_fee }}"
                                                                data-due="{{ $item->actual_total - $item->total }}"
                                                                @php
                                                                    $promo_name = \App\Models\PromoCode::where('id', $item->promo_code)->first();
                                                                    if ($promo_name) {
                                                                        $promo_code_name = $promo_name->name;
                                                                    }else{
                                                                        $promo_code_name = null;
                                                                    }
                                                                @endphp
                                                                data-promo_code="{{ $promo_code_name }}"
                                                                data-discounted_amount="{{ $item->discounted_amount }}"
                                                                data-package_use_date="{{ $item->package_use_date }}"
                                                                data-date="{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d h:i A') }}"
                                                                title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @endif
                                                            <!-- Change Status Dropdown -->
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Change Status
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item" href="/admins/transaction/change/{{ $item->id }}/1">Completed</a></li>
                                                                    <li><a class="dropdown-item" href="/admins/transaction/change/{{ $item->id }}/0">Canceled</a></li>
                                                                    <li><a class="dropdown-item" href="/admins/transaction/change/{{ $item->id }}/2">Refunded</a></li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td><input type="checkbox" class="row-check" value="{{ $item->id }}"></td>
                                                        <td>#0{{ $item->id }}</td>
                                                        <td class="text-break"> {{ $item->transaction_id  ?? 'Free'}} </td>
                                                        <td>{{ $item->package_first_name }} {{ $item->package_last_name }}</td>
                                                        <td>{{ $item->package_phone }}</td>
                                                        <td>{{ $item->package_email }}</td>
                                                        <td>Reservation</td>
                                                        <td>{{ $item->event->name ?? null}}</td>
                                                        @if ($item->type == 'package')
                                                            <td>Package</td>
                                                        @else
                                                            <td>Reservation</td>
                                                        @endif
                                                        @if(auth()->user()->isAdmin())
                                                        <td>{{ $item->website->name ?? 'N/A' }}</td>
                                                        @endif
                                                        <td>${{ $item->total }}</td>
                                                        <td>
                                                            @if($item->status == 1)
                                                                <span class="badge bg-success">Completed</span>
                                                            @elseif($item->status == 0)
                                                                <span class="badge bg-danger">Canceled</span>
                                                            @elseif($item->status == 2)
                                                                <span class="badge bg-warning text-dark">Refunded</span>
                                                            @else
                                                                <span class="badge bg-secondary">Unknown</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-info btn-sm view-btn" data-bs-toggle="modal" data-bs-target="#viewTransactionModal"
                                                                data-transaction_id="{{ $item->transaction_id ?? 'Free'}}"
                                                                {{-- data-package_id="{{ $item->package->name }}" --}}
                                                                data-package_first_name="{{ $item->package_first_name }}"
                                                                data-package_last_name="{{ $item->package_last_name }}"
                                                                data-package_phone="{{ $item->package_phone }}"
                                                                data-package_email="{{ $item->package_email }}"
                                                                data-package_dob="{{ $item->package_dob }}"
                                                                data-package_note="{{ $item->package_note }}"
                                                                data-payment_first_name="{{ $item->payment_first_name }}"
                                                                data-payment_last_name="{{ $item->payment_last_name }}"
                                                                data-payment_phone="{{ $item->payment_phone }}"
                                                                data-payment_email="{{ $item->payment_email }}"
                                                                data-payment_address="{{ $item->payment_address }}"
                                                                data-payment_city="{{ $item->payment_city }}"
                                                                data-payment_state="{{ $item->payment_state }}"
                                                                data-payment_country="{{ $item->payment_country }}"
                                                                data-payment_dob="{{ $item->payment_dob }}"
                                                                data-payment_zip_code="{{ $item->payment_zip_code }}"
                                                                data-type="{{ $item->type }}"
                                                                data-status="{{ $item->status == 1 ? 'Approved' : 'Pending' }}"
                                                                data-ip_address = "{{ $item->ip_address }}"
                                                                data-website_id="{{ $item->website->name }}"
                                                                data-event_id="{{ $item->event->name ?? null}}"
                                                                data-package_use_date="{{ $item->package_use_date }}"
                                                                data-men="{{ $item->men }}"
                                                                data-women="{{ $item->women }}"
                                                                data-total="{{ $item->booking_fee }}"
                                                                data-date="{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d h:i A') }}"
                                                                title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <!-- Change Status Dropdown -->
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Change Status
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item" href="/transaction/{{ $item->id }}/1">Completed</a></li>
                                                                    <li><a class="dropdown-item" href="/transaction/{{ $item->id }}/0">Canceled</a></li>
                                                                    <li><a class="dropdown-item" href="/transaction/{{ $item->id }}/2">Refunded</a></li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="{{ auth()->user()->isAdmin() ? '10' : '9' }}" class="text-end">Total:</th>
                                                <th id="amount-total"></th>
                                                <th colspan="2"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- / Content -->

            <!-- View Transaction Modal -->
            <div class="modal fade" id="viewTransactionModal" tabindex="-1" aria-labelledby="viewTransactionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewTransactionModalLabel">Transaction Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="transaction-modal-content">
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group">
                                        <li class="list-group-item"><strong>Transaction ID:</strong> <span id="modal-transaction_id"></span></li>
                                        <li class="list-group-item"><strong>IP Address:</strong> <span id="modal-ip_address"></span></li>
                                        <li class="list-group-item"><strong>Package Name:</strong> <span id="modal-package_id"></span></li>
                                        <li class="list-group-item"><strong>Package Date Of Use:</strong> <span id="modal-package_date_of_use"></span></li>
                                        <li class="list-group-item"><strong>First Name:</strong> <span id="modal-package_first_name"></span></li>
                                        <li class="list-group-item"><strong>Last Name:</strong> <span id="modal-package_last_name"></span></li>
                                        <li class="list-group-item"><strong>Phone:</strong> <span id="modal-package_phone"></span></li>
                                        <li class="list-group-item"><strong>Email:</strong> <span id="modal-package_email"></span></li>
                                        <li class="list-group-item"><strong>DOB:</strong> <span id="modal-package_dob"></span></li>
                                        <li class="list-group-item"><strong>Note:</strong> <span id="modal-package_note"></span></li>
                                        <li class="list-group-item"><strong>Number of Guests:</strong> <span id="modal-package_number_of_guest"></span></li>
                                        <li class="list-group-item"><strong>Male Guests:</strong> <span id="modal-package_men_guest"></span></li>
                                        <li class="list-group-item"><strong>Female Guests:</strong> <span id="modal-package_women_guest"></span></li>
                                        <li class="list-group-item"><strong>Transportation Pickup Time:</strong> <span id="modal-transportation_pickup_time"></span></li>
                                        <li class="list-group-item"><strong>Transportation Address:</strong> <span id="modal-transportation_address"></span></li>
                                        <li class="list-group-item"><strong>Transportation Phone:</strong> <span id="modal-transportation_phone"></span></li>
                                        <li class="list-group-item"><strong>Transportation Guest:</strong> <span id="modal-transportation_guest"></span></li>
                                        <li class="list-group-item"><strong>Transportation Note:</strong> <span id="modal-transportation_note"></span></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group">
                                        <li class="list-group-item"><strong>Payment First Name:</strong> <span id="modal-payment_first_name"></span></li>
                                        <li class="list-group-item"><strong>Payment Last Name:</strong> <span id="modal-payment_last_name"></span></li>
                                        <li class="list-group-item"><strong>Payment Phone:</strong> <span id="modal-payment_phone"></span></li>
                                        <li class="list-group-item"><strong>Payment Email:</strong> <span id="modal-payment_email"></span></li>
                                        <li class="list-group-item"><strong>Payment Address:</strong> <span id="modal-payment_address"></span></li>
                                        <li class="list-group-item"><strong>Payment City:</strong> <span id="modal-payment_city"></span></li>
                                        <li class="list-group-item"><strong>Payment State:</strong> <span id="modal-payment_state"></span></li>
                                        <li class="list-group-item"><strong>Payment Country:</strong> <span id="modal-payment_country"></span></li>
                                        <li class="list-group-item"><strong>Payment DOB:</strong> <span id="modal-payment_dob"></span></li>
                                        <li class="list-group-item"><strong>Payment Zip Code:</strong> <span id="modal-payment_zip_code"></span></li>
                                        <li class="list-group-item"><strong>Business Company Name:</strong> <span id="modal-business_company"></span></li>
                                        <li class="list-group-item"><strong>Business Vat Number:</strong> <span id="modal-business_vat"></span></li>
                                        <li class="list-group-item"><strong>Business Address:</strong> <span id="modal-business_address"></span></li>
                                        <li class="list-group-item"><strong>Business Purpose:</strong> <span id="modal-business_purpose"></span></li>
                                        <li class="list-group-item"><strong>Type:</strong> <span id="modal-type"></span></li>
                                        <li class="list-group-item"><strong>Status:</strong> <span id="modal-status-badge"></span></li>
                                        <li class="list-group-item"><strong>Website ID:</strong> <span id="modal-website_id"></span></li>
                                        <li class="list-group-item"><strong>Event ID:</strong> <span id="modal-event_id"></span></li>
                                        <li class="list-group-item"><strong>Add-ons:</strong> <span id="modal-addons"></span></li>
                                        <li class="list-group-item"><strong>Promo Code:</strong> <span id="modal-promo_code"></span></li>
                                        <li class="list-group-item"><strong>Discounted Amount:</strong> <span id="modal-discounted_amount"></span></li>
                                        <li class="list-group-item"><strong>Total Amount:</strong> <span id="modal-sub_total"></span></li>
                                        <li class="list-group-item"><strong>Gratuity:</strong> <span id="modal-gratuity"></span></li>
                                        <li class="list-group-item"><strong>Non refundable deposit:</strong> <span id="modal-refundable"></span></li>
                                        <li class="list-group-item"><strong>Total Amount Paid:</strong> <span id="modal-total"></span></li>
                                        <li class="list-group-item"><strong>Total Due:</strong> <span id="modal-total_due"></span></li>
                                        <li class="list-group-item"><strong>Date:</strong> <span id="modal-date"></span></li>
                                        <li class="list-group-item"><strong>Accepted Terms and Conditions:</strong> <span id="modal-terms">Yes</span></li>
                                        <li class="list-group-item"><strong>Accepted SMS:</strong> <span id="modal-sms">Yes</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="download-transaction-pdf">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- jQuery -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <!-- DataTables CSS -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
            <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
            <!-- Date Range Picker CSS -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

            <!-- DataTables JS -->
            <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

            <!-- Moment.js (MUST be before daterangepicker) -->
            <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
            <!-- Date Range Picker JS -->
            <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

            <script>
                $(document).ready(function() {
                    // Initialize DataTable
                    let table = new DataTable('.table', {
                        dom: 'Bfrtip',
                        buttons: [
                            {
                                extend: 'csv',
                                text: 'Export CSV',
                                exportOptions: {
                                    rows: function(idx, data, node) {
                                        let checked = $('.row-check:checked');
                                        if (checked.length === 0) return true; // export all if none checked
                                        return $(node).find('.row-check').prop('checked');
                                    },
                                    columns: ':visible:not(:first-child):not(:last-child)' // Exclude checkbox and action columns
                                }
                            },
                            {
                                extend: 'excel',
                                text: 'Export Excel',
                                exportOptions: {
                                    rows: function(idx, data, node) {
                                        let checked = $('.row-check:checked');
                                        if (checked.length === 0) return true;
                                        return $(node).find('.row-check').prop('checked');
                                    },
                                    columns: ':visible:not(:first-child):not(:last-child)'
                                }
                            },
                            // ...inside your DataTable initialization, in the buttons array for PDF...

                        {
                            extend: 'pdf',
                            text: 'Export PDF',
                            exportOptions: {
                                rows: function(idx, data, node) {
                                    let checked = $('.row-check:checked');
                                    if (checked.length === 0) return true;
                                    return $(node).find('.row-check').prop('checked');
                                },
                                columns: ':visible:not(:first-child):not(:last-child)'
                            },
                            customize: function (doc) {
                            doc.pageOrientation = 'landscape'; // Landscape mode
                            doc.defaultStyle.fontSize = 9; // Even smaller font
                            doc.styles.tableHeader.fontSize = 10;
                            doc.styles.tableHeader.alignment = 'left';
                            doc.content[1].margin = [0, 0, 0, 0];
                            doc.pageMargins = [5, 5, 5, 5]; // Tighter margins
                            // Set all column widths to auto
                            var table = doc.content[1].table;
                            if (table && table.body && table.body.length > 0) {
                                var colCount = table.body[0].length;
                                table.widths = Array(colCount).fill('auto');
                            }
}
                        },
                            {
                                extend: 'print',
                                text: 'Print',
                                exportOptions: {
                                    rows: function(idx, data, node) {
                                        let checked = $('.row-check:checked');
                                        if (checked.length === 0) return true;
                                        return $(node).find('.row-check').prop('checked');
                                    },
                                    columns: ':visible:not(:first-child):not(:last-child)'
                                }
                            }
                        ]
                    });

                    // Website filter
                    $('#websiteFilter').on('change', function() {
                        table.column(9).search(this.value).draw();
                    });

                    // Type filter
                    $('#typeFilter').on('change', function() {
                        table.column(8).search(this.value).draw();
                    });



                    // Checklist: Select all
                    $('#selectAll').on('change', function() {
                        $('.row-check').prop('checked', this.checked);
                    });

                    function updateAmountTotal() {
                        let total = 0;
                        table.rows({ search: 'applied' }).every(function () {
                            let data = this.data();
                            let amountCell = data[10]; // Amount column is at index 10
                            // Remove HTML tags if present
                            let tempDiv = document.createElement('div');
                            tempDiv.innerHTML = amountCell;
                            let text = tempDiv.textContent || tempDiv.innerText || "";
                            // Remove $ and commas, parse as float
                            let amount = parseFloat(text.replace(/[^0-9.-]+/g,"")) || 0;
                            total += amount;
                        });
                        $('#amount-total').html('$' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
                    }

                    table.on('draw', updateAmountTotal);
                    updateAmountTotal();
                });
                </script>

                <script>
                $(document).on('click', '.view-btn', function() {
                    $('#modal-package_date_of_use').text($(this).data('package_use_date'));
                    $('#modal-promo_code').text($(this).data('promo_code'));
                    $('#modal-discounted_amount').text($(this).data('discounted_amount'));
                    $('#modal-package_men_guest').text($(this).data('men'));
                    $('#modal-package_women_guest').text($(this).data('women'));

                    $('#modal-transaction_id').text($(this).data('transaction_id'));
                    $('#modal-package_id').text($(this).data('package_id'));
                    $('#modal-package_first_name').text($(this).data('package_first_name'));
                    $('#modal-package_last_name').text($(this).data('package_last_name'));
                    $('#modal-package_phone').text($(this).data('package_phone'));
                    $('#modal-package_email').text($(this).data('package_email'));
                    $('#modal-package_dob').text($(this).data('package_dob'));
                    $('#modal-package_note').text($(this).data('package_note'));
                    $('#modal-package_number_of_guest').text($(this).data('package_number_of_guest'));
                    $('#modal-transportation_pickup_time').text($(this).data('transportation_pickup_time'));
                    $('#modal-transportation_address').text($(this).data('transportation_address'));
                    $('#modal-transportation_phone').text($(this).data('transportation_phone'));
                    $('#modal-transportation_guest').text($(this).data('transportation_guest'));
                    $('#modal-transportation_note').text($(this).data('transportation_note'));
                    $('#modal-payment_first_name').text($(this).data('payment_first_name'));
                    $('#modal-payment_last_name').text($(this).data('payment_last_name'));
                    $('#modal-payment_phone').text($(this).data('payment_phone'));
                    $('#modal-payment_email').text($(this).data('payment_email'));
                    $('#modal-payment_address').text($(this).data('payment_address'));
                    $('#modal-payment_city').text($(this).data('payment_city'));
                    $('#modal-payment_state').text($(this).data('payment_state'));
                    $('#modal-payment_country').text($(this).data('payment_country'));
                    $('#modal-payment_dob').text($(this).data('payment_dob'));
                    $('#modal-payment_zip_code').text($(this).data('payment_zip_code'));
                    $('#modal-type').text($(this).data('type'));
                    // Show status as badge
                    var status = $(this).data('status');
                    var badge = '';
                    if (status == 1 || status === 'Completed' || status === 'Approved') {
                        badge = '<span class="badge bg-success">Completed</span>';
                    } else if (status == 0 || status === 'Canceled' || status === '0') {
                        badge = '<span class="badge bg-danger">Canceled</span>';
                    } else if (status == 2 || status === 'Refunded') {
                        badge = '<span class="badge bg-warning text-dark">Refunded</span>';
                    } else {
                        badge = '<span class="badge bg-secondary">Unknown</span>';
                    }
                    $('#modal-status-badge').html(badge);
                    $('#modal-website_id').text($(this).data('website_id'));
                    $('#modal-ip_address').text($(this).data('ip_address'));
                    $('#modal-event_id').text($(this).data('event_id'));
                    $('#modal-addons').text($(this).data('addons'));
                    $('#modal-sub_total').text($(this).data('subtotal'));
                    $('#modal-business_company').text($(this).data('business_company'));
                    $('#modal-business_vat').text($(this).data('business_vat'));
                    $('#modal-business_address').text($(this).data('business_address'));
                    $('#modal-business_purpose').text($(this).data('business_purpose'));
                    $('#modal-refundable').text($(this).data('refundable'));
                    $('#modal-gratuity').text($(this).data('gratuity'));
                    $('#modal-total').text($(this).data('total'));
                    $('#modal-total_due').text($(this).data('due'));
                    $('#modal-date').text($(this).data('date'));
                });
                </script>

            <!-- jsPDF and autoTable -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
            <script>
                $(document).on('click', '#download-transaction-pdf', function() {
    var rows = [];
    $('#transaction-modal-content ul.list-group').each(function() {
        $(this).find('li').each(function() {
            var label = $(this).find('strong').text().replace(':', '').trim();
            var value = '';
            // Special handling for status badge
            if ($(this).find('span').attr('id') === 'modal-status-badge') {
                // Get only the text inside the badge, not the HTML
                value = $(this).find('span .badge').text().trim();
            } else {
                value = $(this).find('span').text().trim();
            }
            rows.push([label, value]);
        });
    });
    var { jsPDF } = window.jspdf;
    var doc = new jsPDF();
    doc.text('Transaction Details', 14, 14);
    doc.autoTable({
        head: [['Field', 'Value']],
        body: rows,
        startY: 20,
        styles: { fontSize: 10, cellPadding: 2 },
        headStyles: { fillColor: [41, 128, 185] }
    });
    doc.save('transaction-details.pdf');
});
            </script>
        @endsection

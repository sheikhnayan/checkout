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
                                            Promo Code
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
                                            Promo Code
                                        </li>

                                    </ol>

                                    <div class="btn-group" role="group" aria-label="Basic example" style="float: right">
                                        <a href="/admins/promo_code/create/{{ $website_id }}" class="btn btn-primary">Add Promo Code</a>
                                </nav>
                            </div>
                        </div>

                        <!-- Tabs for Active and Archived Promo Codes -->
                        <ul class="nav nav-tabs mb-3" id="promoTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#activePromos" type="button" role="tab" aria-controls="activePromos" aria-selected="true">
                                    Active Promo Codes
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archivedPromos" type="button" role="tab" aria-controls="archivedPromos" aria-selected="false">
                                    Archived Promo Codes
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="promoTabsContent">
                            <div class="tab-pane fade show active" id="activePromos" role="tabpanel" aria-labelledby="active-tab">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="card-shadow-primary card-border text-white mb-3 card bg-primary p-2" style="background: #fff !important;">
                                            <table class="table" id="activePromosTable">
                                                <thead>
                                                    <tr>
                                                        <th>SI</th>
                                                        <th>Name</th>
                                                        <th>Price</th>
                                                        <th>Type</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $activeIndex = 1; @endphp
                                                    @foreach ($data as $item)
                                                        @if (empty($item->is_archieved) || $item->is_archieved == 0)
                                                        <tr>
                                                            <td>{{ $activeIndex++ }}</td>
                                                            <td>{{ $item->name }}</td>
                                                            <td>{{ $item->percentage }}</td>
                                                            <td>{{ $item->type }}</td>
                                                            <td>
                                                                <a href="/admins/promo_code/edit/{{ $item->id }}" class="btn btn-primary">Edit</a>
                                                                <form action="/admins/promo_code/archive/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to archive this promo code?');">
                                                                        Archive
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="archivedPromos" role="tabpanel" aria-labelledby="archived-tab">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="card-shadow-primary card-border text-white mb-3 card bg-secondary p-2" style="background: #f8f9fa !important;">
                                            <table class="table" id="archivedPromosTable">
                                                <thead>
                                                    <tr>
                                                        <th>SI</th>
                                                        <th>Name</th>
                                                        <th>Price</th>
                                                        <th>Type</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $archivedIndex = 1; @endphp
                                                    @foreach ($data as $item)
                                                        @if (!empty($item->is_archieved) && $item->is_archieved == 1)
                                                        <tr>
                                                            <td>{{ $archivedIndex++ }}</td>
                                                            <td>{{ $item->name }}</td>
                                                            <td>{{ $item->percentage }}</td>
                                                            <td>{{ $item->type }}</td>
                                                            <td>
                                                                <form action="/admins/promo_code/unarchive/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Unarchive this promo code?');">
                                                                        Unarchive
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- / Content -->

            <!-- Include DataTables and jQuery CDN (jQuery first, then DataTables, then Bootstrap) -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                $(document).ready(function() {
                    let table1 = new DataTable('#activePromosTable');
                    let table2 = new DataTable('#archivedPromosTable');
                });
            </script>
        @endsection

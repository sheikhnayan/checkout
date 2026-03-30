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
                                            Packages
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

                                    <div class="btn-group" role="group" aria-label="Basic example" style="float: right">
                                        <a href="/admins/package/create/{{ $website_id }}" class="btn btn-primary">Add Package</a>
                                    </div>
                                </nav>
                            </div>
                        </div>

                        <!-- Tabs for Active / Archived Packages + Categories -->
                        <ul class="nav nav-tabs mb-3" id="packageTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#activePackages" type="button" role="tab" aria-controls="activePackages" aria-selected="true">
                                    Active Packages
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archivedPackages" type="button" role="tab" aria-controls="archivedPackages" aria-selected="false">
                                    Archived Packages
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categoriesPanel" type="button" role="tab" aria-controls="categoriesPanel" aria-selected="false">
                                    Categories
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="packageTabsContent">
                            <div class="tab-pane fade show active" id="activePackages" role="tabpanel" aria-labelledby="active-tab">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="card-shadow-primary card-border text-white mb-3 card bg-primary p-2">
                                            <table class="table" id="activePackagesTable">
                                                <thead>
                                                    <tr>
                                                        <th>SI</th>
                                                        <th>Name</th>
                                                        <th>Category</th>
                                                        <th>Price</th>
                                                        <th>Status</th>
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
                                                            <td>{{ optional($item->category)->name ?: 'Uncategorized' }}</td>
                                                            <td>{{ $item->price }}</td>
                                                            <td>
                                                                <form action="/admins/package/toggle-status/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @if ($item->status == 1)
                                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Deactivate this package?');">Active</button>
                                                                    @else
                                                                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Activate this package?');">Inactive</button>
                                                                    @endif
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <a href="/admins/package/edit/{{ $item->id }}" class="btn btn-primary">Edit</a>
                                                                <form action="/admins/package/archive/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to archive this package?');">
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
                            <div class="tab-pane fade" id="archivedPackages" role="tabpanel" aria-labelledby="archived-tab">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="card-shadow-primary card-border text-white mb-3 card bg-secondary p-2">
                                            <table class="table" id="archivedPackagesTable">
                                                <thead>
                                                    <tr>
                                                        <th>SI</th>
                                                        <th>Name</th>
                                                        <th>Category</th>
                                                        <th>Price</th>
                                                        <th>Status</th>
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
                                                            <td>{{ optional($item->category)->name ?: 'Uncategorized' }}</td>
                                                            <td>{{ $item->price }}</td>
                                                            <td>
                                                                <form action="/admins/package/toggle-status/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @if ($item->status == 1)
                                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Deactivate this package?');">Active</button>
                                                                    @else
                                                                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Activate this package?');">Inactive</button>
                                                                    @endif
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <form action="/admins/package/unarchive/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Unarchive this package?');">
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
                            {{-- ===== CATEGORIES TAB ===== --}}
                            <div class="tab-pane fade" id="categoriesPanel" role="tabpanel" aria-labelledby="categories-tab">
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                {{-- Add new category --}}
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold">Add New Category</h6>
                                        <form method="POST" action="{{ route('admin.package-category.store', $website_id) }}" class="d-flex gap-2">
                                            @csrf
                                            <input type="text" name="name" class="form-control" placeholder="Category name" required style="max-width:320px;">
                                            <button type="submit" class="btn btn-primary">Add</button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Existing categories --}}
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold">Existing Categories</h6>
                                        @if($categories->isEmpty())
                                            <p class="text-muted">No categories yet for this website.</p>
                                        @else
                                            <table class="table" id="categoriesTable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Packages</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($categories as $i => $cat)
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>
                                                        <td>
                                                            <form method="POST" action="{{ route('admin.package-category.update', $cat->id) }}" class="d-flex gap-2 align-items-center">
                                                                @csrf
                                                                <input type="text" name="name" value="{{ $cat->name }}" class="form-control form-control-sm" style="max-width:240px;" required>
                                                                <button type="submit" class="btn btn-sm btn-outline-primary">Rename</button>
                                                            </form>
                                                        </td>
                                                        <td>{{ $cat->packages()->count() }}</td>
                                                        <td>
                                                            <form method="POST" action="{{ route('admin.package-category.destroy', $cat->id) }}" onsubmit="return confirm('Delete this category? Packages will become Uncategorized.')">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- / Content -->
        </div>
    </div>

            <!-- Include DataTables and jQuery CDN (jQuery first, then DataTables, then Bootstrap) -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                $(document).ready(function() {
                    let table1 = new DataTable('#activePackagesTable');
                    let table2 = new DataTable('#archivedPackagesTable');
                });
            </script>
        @endsection

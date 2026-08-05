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
                                            Website
                                        </li>

                                    </ol>

                                    <div class="btn-group" role="group" aria-label="Basic example" style="float: right">
                                        <a href="/admins/website/create" class="btn btn-primary">Add Website</a>
                                </nav>
                            </div>
                        </div>

                        <!-- Tabs for Active and Archived Websites -->
                        <ul class="nav nav-tabs mb-3" id="websiteTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#activeWebsites" type="button" role="tab" aria-controls="activeWebsites" aria-selected="true">
                                    Active Websites
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archivedWebsites" type="button" role="tab" aria-controls="archivedWebsites" aria-selected="false">
                                    Archived Websites
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="websiteTabsContent">
                            <div class="tab-pane fade show active" id="activeWebsites" role="tabpanel" aria-labelledby="active-tab">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="card-shadow-primary card-border text-white mb-3 card bg-primary p-2">
                                            <table class="table" id="activeWebsitesTable">
                                                <thead>
                                                    <tr>
                                                        <th>SI</th>
                                                        <th>Name</th>
                                                        <th>Slug</th>
                                                        <th>Domain</th>
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
                                                            <td><code>{{ $item->slug }}</code></td>
                                                            <td>{{ $item->domain }}</td>
                                                            <td>
                                                                <form action="/admins/website/toggle-status/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @if ($item->status == 1)
                                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Deactivate this website?');">Active</button>
                                                                    @else
                                                                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Activate this website?');">Inactive</button>
                                                                    @endif
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <a href="/admins/website/edit/{{ $item->id }}" class="btn btn-primary btn-sm" title="Edit Website">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.website.payment-settings', $item->id) }}" class="btn btn-info btn-sm" title="Payment Settings">
                                                                        <i class="fas fa-credit-card"></i>
                                                                    </a>
                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-dark btn-sm js-copy-embed"
                                                                        title="Copy Embed Code"
                                                                        data-embed-url="{{ url('/' . $item->slug) }}?embed=1"
                                                                        onclick="copyWebsiteEmbedCode(this)">
                                                                        <i class="fas fa-code"></i>
                                                                    </button>
                                                                </div>
                                                                <form action="/admins/website/archive/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to archive this website?');" title="Archive Website">
                                                                        <i class="fas fa-archive"></i>
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
                            <div class="tab-pane fade" id="archivedWebsites" role="tabpanel" aria-labelledby="archived-tab">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="card-shadow-primary card-border text-white mb-3 card bg-secondary p-2">
                                            <table class="table" id="archivedWebsitesTable">
                                                <thead>
                                                    <tr>
                                                        <th>SI</th>
                                                        <th>Name</th>
                                                        <th>Slug</th>
                                                        <th>Domain</th>
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
                                                            <td><code>{{ $item->slug }}</code></td>
                                                            <td>{{ $item->domain }}</td>
                                                            <td>
                                                                <form action="/admins/website/toggle-status/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @if ($item->status == 1)
                                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Deactivate this website?');">Active</button>
                                                                    @else
                                                                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Activate this website?');">Inactive</button>
                                                                    @endif
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <form action="/admins/website/unarchive/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Unarchive this website?');">
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
                function copyWebsiteEmbedCode(button) {
                    const embedUrl = button.getAttribute('data-embed-url');
                    if (!embedUrl) {
                        return;
                    }

                    const iframeId = 'checkout-embed-' + Date.now();
                    const iframeCode = '<iframe id="' + iframeId + '" src="' + embedUrl + '" width="100%" height="2000" style="border:0;max-width:100%;display:block;" loading="lazy" scrolling="no" referrerpolicy="strict-origin-when-cross-origin" allow="payment"></iframe>' +
                        '<script>(function(){var iframe=document.getElementById("' + iframeId + '");if(!iframe){return;}var mobileQuery=window.matchMedia("(max-width: 991px)");function resetDesktopHeight(){if(!mobileQuery.matches){iframe.style.height="2000px";}}function applyMobileHeight(rawHeight){if(!mobileQuery.matches){return;}var parsed=parseInt(rawHeight,10);if(!parsed){return;}var next=Math.max(720,Math.min(parsed+56,12000));iframe.style.height=next+"px";}window.addEventListener("message",function(event){if(event.source!==iframe.contentWindow){return;}var data=event.data||{};if(data.type==="checkoutEmbedHeight"){applyMobileHeight(data.height);return;}if(data.type==="checkoutScrollToIframe"&&mobileQuery.matches&&iframe.scrollIntoView){iframe.scrollIntoView({block:"start",behavior:"auto"});}});if(mobileQuery.addEventListener){mobileQuery.addEventListener("change",resetDesktopHeight);}else if(mobileQuery.addListener){mobileQuery.addListener(resetDesktopHeight);}resetDesktopHeight();})();<\/script>';

                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(iframeCode).then(function() {
                            const originalTitle = button.title;
                            button.title = 'Copied';
                            button.classList.remove('btn-dark');
                            button.classList.add('btn-success');
                            setTimeout(function() {
                                button.title = originalTitle;
                                button.classList.remove('btn-success');
                                button.classList.add('btn-dark');
                            }, 1400);
                        }).catch(function() {
                            window.prompt('Copy embed code:', iframeCode);
                        });
                        return;
                    }

                    window.prompt('Copy embed code:', iframeCode);
                }

                $(document).ready(function() {
                    // Only initialize each table once
                    let table = new DataTable('#activeWebsitesTable');
                    let table2 = new DataTable('#archivedWebsitesTable');
                });
            </script>
        @endsection

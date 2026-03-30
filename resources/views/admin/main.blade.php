<!doctype html>

<html
  lang="en"
  class="layout-menu-fixed layout-compact"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>CartVIP by James</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{asset('user/assets/vendor/fonts/iconify-icons.css')}}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="{{asset('user/assets/vendor/css/core.css')}}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{asset('user/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />

    <!-- endbuild -->

    <link rel="stylesheet" href="{{asset('user/assets/vendor/libs/apex-charts/apex-charts.css')}}" />

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{asset('user/assets/vendor/js/helpers.js')}}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="{{asset('user/assets/js/config.js')}}"></script>

    <style>
      :root {
        --admin-bg: #0b0e1a;
        --admin-surface: #121726;
        --admin-surface-2: #171d2f;
        --admin-border: rgba(255, 255, 255, 0.1);
        --admin-text: #e8eaf6;
        --admin-text-muted: #b9bfd5;
        --admin-accent: #ffcc00;
      }

      body,
      .bg-menu-theme,
      .layout-page,
      .content-wrapper,
      .bg-footer-theme {
        background-color: var(--admin-bg) !important;
        color: var(--admin-text);
      }

      .layout-navbar,
      .layout-menu,
      .menu-inner,
      .card,
      .modal-content,
      .dropdown-menu {
        background: var(--admin-surface) !important;
        border-color: var(--admin-border) !important;
        color: var(--admin-text) !important;
      }

      .table,
      .table > :not(caption) > * > * {
        color: var(--admin-text) !important;
        border-color: var(--admin-border) !important;
        background: transparent !important;
      }

      .table thead th {
        color: #fff !important;
        font-weight: 700;
        letter-spacing: .02em;
      }

      .form-control,
      .form-select,
      textarea,
      input,
      select {
        background: var(--admin-surface-2) !important;
        color: var(--admin-text) !important;
        border-color: var(--admin-border) !important;
      }

      .form-control:focus,
      .form-select:focus,
      textarea:focus,
      input:focus,
      select:focus {
        border-color: var(--admin-accent) !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 204, 0, 0.16) !important;
      }

      .form-check-input {
        width: 1.15rem;
        height: 1.15rem;
        border: 2px solid rgba(255, 255, 255, 0.45) !important;
        border-radius: 0.3rem;
        background-color: #11172a !important;
        background-size: 0.8rem 0.8rem;
        cursor: pointer;
      }

      .form-check-input:hover {
        border-color: rgba(255, 204, 0, 0.75) !important;
      }

      .form-check-input:focus {
        border-color: var(--admin-accent) !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 204, 0, 0.24) !important;
      }

      .form-check-input:checked {
        border-color: var(--admin-accent) !important;
        background-color: var(--admin-accent) !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%230b0e1a' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M4 10l4 4 8-8'/%3e%3c/svg%3e") !important;
      }

      .form-check-input:indeterminate {
        border-color: var(--admin-accent) !important;
        background-color: var(--admin-accent) !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%230b0e1a' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M5 10h10'/%3e%3c/svg%3e") !important;
      }

      .form-check-label {
        color: var(--admin-text) !important;
      }

      .btn-primary,
      .btn-success,
      .btn-warning,
      .btn-info,
      .btn-outline-primary {
        background: var(--admin-accent) !important;
        border-color: var(--admin-accent) !important;
        color: #1f1400 !important;
        font-weight: 700;
      }

      .btn-secondary,
      .btn-light {
        background: #2a3148 !important;
        border-color: #2a3148 !important;
        color: var(--admin-text) !important;
      }

      .menu-vertical .menu-item.active > .menu-link,
      .menu-vertical .menu-item.open > .menu-link,
      .menu-vertical .menu-item .menu-link:hover {
        background: rgba(255, 204, 0, 0.15) !important;
        color: var(--admin-accent) !important;
      }

      .menu-vertical .menu-item .menu-link,
      .menu-header-text,
      .text-muted,
      .card-subtitle,
      .form-text,
      small {
        color: var(--admin-text-muted) !important;
      }

      .content-footer {
        border-top: 1px solid var(--admin-border);
      }

      .admin-feedback-stack {
        display: grid;
        gap: 10px;
      }

      .admin-feedback-stack .alert {
        border: 1px solid var(--admin-border);
        border-left-width: 4px;
        border-radius: 12px;
        margin-bottom: 0;
      }

      .admin-feedback-stack .alert-success {
        border-left-color: #37d67a;
        background: rgba(55, 214, 122, 0.14);
        color: #d9ffe9;
      }

      .admin-feedback-stack .alert-danger {
        border-left-color: #ff6b6b;
        background: rgba(255, 107, 107, 0.13);
        color: #ffe2e2;
      }

      .admin-feedback-stack .alert-warning {
        border-left-color: var(--admin-accent);
        background: rgba(255, 204, 0, 0.15);
        color: #fff1bf;
      }

      .admin-feedback-stack .alert-info {
        border-left-color: #6fa8ff;
        background: rgba(111, 168, 255, 0.14);
        color: #dfeeff;
      }

      .admin-feedback-stack ul {
        margin: 8px 0 0;
        padding-left: 18px;
      }

      .dataTables_wrapper .dataTables_filter input,
      .dataTables_wrapper .dataTables_length select {
        background: var(--admin-surface-2) !important;
        color: var(--admin-text) !important;
        border: 1px solid var(--admin-border) !important;
        border-radius: 8px;
      }

      .dataTables_wrapper .dataTables_filter label,
      .dataTables_wrapper .dataTables_length label,
      .dataTables_wrapper .dataTables_info,
      .dataTables_wrapper .dataTables_paginate {
        color: var(--admin-text-muted) !important;
      }

      .dataTables_wrapper .dt-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: .75rem;
      }

      .dataTables_wrapper .dt-button,
      .dataTables_wrapper button.dt-button,
      .dataTables_wrapper div.dt-button,
      .dataTables_wrapper a.dt-button {
        background: var(--admin-surface-2) !important;
        color: var(--admin-text) !important;
        border: 1px solid var(--admin-border) !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
        line-height: 1.2;
        text-shadow: none !important;
        box-shadow: none !important;
      }

      .dataTables_wrapper .dt-button:hover,
      .dataTables_wrapper button.dt-button:hover,
      .dataTables_wrapper div.dt-button:hover,
      .dataTables_wrapper a.dt-button:hover {
        background: rgba(255, 204, 0, 0.16) !important;
        color: #fff !important;
        border-color: rgba(255, 204, 0, 0.5) !important;
      }

      .dataTables_wrapper .dt-button:disabled,
      .dataTables_wrapper .dt-button.disabled {
        opacity: .6;
        cursor: not-allowed;
      }

      .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: var(--admin-text-muted) !important;
        border-radius: 6px;
        border: 1px solid transparent !important;
      }

      .dataTables_wrapper .dataTables_paginate .paginate_button.current,
      .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: rgba(255, 204, 0, 0.18) !important;
        color: #fff !important;
        border-color: rgba(255, 204, 0, 0.5) !important;
      }

      .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #fff !important;
        border-color: var(--admin-border) !important;
      }

      .card-header,
      .card-footer {
        background: rgba(255, 255, 255, 0.02) !important;
        border-color: var(--admin-border) !important;
      }

      .card-footer {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        justify-content: flex-end;
      }

      .card-footer .btn,
      .card .text-center .btn,
      .card form .btn[type='submit'] {
        border-radius: 10px;
        min-height: 38px;
        padding: 8px 14px;
      }

      .container-p-y > .row {
        row-gap: 1rem;
      }

      .admin-mobile-menu-toggle {
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1101;
        display: none !important;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border: 0;
        border-radius: 12px;
        background: #696cff;
        color: #fff;
        box-shadow: 0 10px 24px rgba(105, 108, 255, 0.28);
      }

      .layout-menu .layout-menu-toggle.menu-link {
        display: none !important;
      }

      .admin-mobile-menu-toggle i {
        font-size: 1.35rem;
        line-height: 1;
      }

      .admin-table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      .admin-table-responsive > .table {
        min-width: 720px;
        margin-bottom: 0;
      }

      .card .admin-table-responsive {
        border-radius: inherit;
      }

      @media (max-width: 1199.98px) {
        .admin-mobile-menu-toggle {
          display: inline-flex !important;
          left: auto;
          right: 1rem;
        }

        .layout-page {
          padding-top: 4.5rem;
        }

        .app-brand {
          padding-right: 3rem;
        }

        .menu-inner {
          padding-bottom: 5rem !important;
        }

        .menu-item[style*='position: absolute'] {
          position: static !important;
          margin-top: 1rem;
        }

        .content-wrapper,
        .container-xxl,
        .container-xl,
        .container-lg,
        .container-md,
        .container-sm,
        .container {
          max-width: 100%;
        }

        .nav-tabs {
          flex-wrap: nowrap;
          overflow-x: auto;
          overflow-y: hidden;
          white-space: nowrap;
          -webkit-overflow-scrolling: touch;
        }

        .nav-tabs .nav-link {
          flex: 0 0 auto;
        }
      }

      @media (max-width: 767.98px) {
        .layout-page {
          padding-top: 4.75rem;
        }

        .card-body,
        .card,
        .content-wrapper .row > [class*='col-'] {
          min-width: 0;
        }

        .table td,
        .table th {
          white-space: nowrap;
        }
      }
    </style>
  </head>

  <body>
    <button
      type="button"
      class="admin-mobile-menu-toggle"
      aria-label="Open sidebar"
      aria-controls="layout-menu"
      aria-expanded="false">
      <i class="bx bx-menu"></i>
    </button>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="#" class="app-brand-link">
              <span class="app-brand-logo demo">
                <span class="text-primary">
                  <svg
                    width="25"
                    viewBox="0 0 25 42"
                    version="1.1"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs>
                      <path
                        d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                        id="path-1"></path>
                      <path
                        d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
                        id="path-3"></path>
                      <path
                        d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
                        id="path-4"></path>
                      <path
                        d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
                        id="path-5"></path>
                    </defs>
                    <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                        <g id="Icon" transform="translate(27.000000, 15.000000)">
                          <g id="Mask" transform="translate(0.000000, 8.000000)">
                            <mask id="mask-2" fill="white">
                              <use xlink:href="#path-1"></use>
                            </mask>
                            <use fill="currentColor" xlink:href="#path-1"></use>
                            <g id="Path-3" mask="url(#mask-2)">
                              <use fill="currentColor" xlink:href="#path-3"></use>
                              <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                            </g>
                            <g id="Path-4" mask="url(#mask-2)">
                              <use fill="currentColor" xlink:href="#path-4"></use>
                              <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                            </g>
                          </g>
                          <g
                            id="Triangle"
                            transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                            <use fill="currentColor" xlink:href="#path-5"></use>
                            <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                          </g>
                        </g>
                      </g>
                    </g>
                  </svg>
                </span>
              </span>
              <span class="app-brand-text demo menu-text fw-bold ms-2" style="font-size: 1rem;">CartVIP by James</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
            </a>
          </div>

          <div class="menu-divider mt-0"></div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
  <li class="menu-header small text-uppercase">
    <span class="menu-header-text">Site Settings</span>
  </li>

  @if(auth()->check() && auth()->user()->isAdmin())
  <li class="menu-item {{ request()->is('admins/website') ? 'active' : '' }}">
    <a href="/admins/website" class="menu-link">
      <i class="menu-icon tf-icons bx bx-globe"></i>
      <div class="text-truncate">Websites</div>
    </a>
  </li>
  @endif

  @if(auth()->check() && auth()->user()->isAdmin())
  <li class="menu-item {{ request()->is('admins/website-users*') ? 'active' : '' }}">
    <a href="{{ route('admin.website-users.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-user"></i>
      <div class="text-truncate">Website Users</div>
    </a>
  </li>
  @endif

  @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isWebsiteUser()))
  <li class="menu-item {{ request()->is('admins/event') ? 'active' : '' }}">
    <a href="/admins/event" class="menu-link">
      <i class="menu-icon tf-icons bx bx-package"></i>
      <div class="text-truncate">Events</div>
    </a>
  </li>

  <li class="menu-item {{ request()->is('admins/package') ? 'active' : '' }}">
    <a href="/admins/package" class="menu-link">
      <i class="menu-icon tf-icons bx bx-package"></i>
      <div class="text-truncate">Packages</div>
    </a>
  </li>

  <li class="menu-item {{ request()->is('admins/addon') ? 'active' : '' }}">
    <a href="/admins/addon" class="menu-link">
      <i class="menu-icon tf-icons bx bx-package"></i>
      <div class="text-truncate">Add-ons</div>
    </a>
  </li>

  <li class="menu-item {{ request()->is('admins/promo_code') ? 'active' : '' }}">
    <a href="/admins/promo_code" class="menu-link">
      <i class="menu-icon tf-icons bx bx-package"></i>
      <div class="text-truncate">Promo Codes</div>
    </a>
  </li>

  <li class="menu-item {{ request()->is('admins/feed-model*') ? 'active' : '' }}">
    <a href="{{ route('admin.feed-model.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-user-circle"></i>
      <div class="text-truncate">Feed Entertainers</div>
    </a>
  </li>

  <li class="menu-item {{ request()->is('admins/feed-post*') ? 'active' : '' }}">
    <a href="{{ route('admin.feed-post.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-images"></i>
      <div class="text-truncate">Feed Posts</div>
    </a>
  </li>
  @endif

  @if(auth()->check() && auth()->user()->isAdmin())
  <li class="menu-item {{ request()->is('admins/setting/edit/1') ? 'active' : '' }}">
    <a href="/admins/setting/edit/1" class="menu-link">
      <i class="menu-icon tf-icons bx bx-package"></i>
      <div class="text-truncate">Platform Settings</div>
    </a>
  </li>
  @endif

  @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isWebsiteUser()))
  <li class="menu-item {{ request()->is('admins/transaction') ? 'active' : '' }}">
    <a href="/admins/transaction" class="menu-link">
      <i class="menu-icon tf-icons bx bx-package"></i>
      <div class="text-truncate">Transactions</div>
    </a>
  </li>
  @endif

  @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isWebsiteUser()))
  <li class="menu-item {{ request()->is('admins/custom-invoice') ? 'active' : '' }}">
    <a href="{{ route('admin.custom-invoice.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-file"></i>
      <div class="text-truncate">Custom Invoices</div>
    </a>
  </li>
  @endif

  @if(auth()->check() && auth()->user()->isAdmin())
  <li class="menu-item {{ request()->is('admins/affiliate*') ? 'active' : '' }}">
    <a href="{{ route('admin.affiliate.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-group"></i>
      <div class="text-truncate">Affiliates</div>
    </a>
  </li>
  @endif

  @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isWebsiteUser()))
  <li class="menu-item {{ request()->is('admins/entertainer*') ? 'active' : '' }}">
    <a href="{{ route('admin.entertainer.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-user-voice"></i>
      <div class="text-truncate">Entertainers</div>
    </a>
  </li>
  @endif

  @if(auth()->check() && auth()->user()->isAffiliate())
  <li class="menu-item {{ request()->is('affiliate-portal/dashboard') ? 'active' : '' }}">
    <a href="{{ route('affiliate.portal.dashboard') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-home-alt-2"></i>
      <div class="text-truncate">Affiliate Dashboard</div>
    </a>
  </li>

  <li class="menu-item {{ request()->is('affiliate-portal/packages') ? 'active' : '' }}">
    <a href="{{ route('affiliate.portal.packages') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-list-ul"></i>
      <div class="text-truncate">My Packages</div>
    </a>
  </li>

  <li class="menu-item {{ request()->is('affiliate-portal/settings') ? 'active' : '' }}">
    <a href="{{ route('affiliate.portal.settings') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-palette"></i>
      <div class="text-truncate">Page Customization</div>
    </a>
  </li>

  <li class="menu-item {{ request()->is('affiliate-portal/wallet') ? 'active' : '' }}">
    <a href="{{ route('affiliate.portal.wallet') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-wallet"></i>
      <div class="text-truncate">Wallet</div>
    </a>
  </li>

  @if(auth()->user()->affiliate && auth()->user()->affiliate->slug)
  <li class="menu-item">
    <a href="{{ route('affiliate.public', auth()->user()->affiliate->slug) }}" target="_blank" class="menu-link">
      <i class="menu-icon tf-icons bx bx-link-external"></i>
      <div class="text-truncate">My Affiliate Page</div>
    </a>
  </li>
  @endif
  @endif

  @if(auth()->check() && auth()->user()->isEntertainer())
  <li class="menu-item {{ request()->is('entertainer-portal/dashboard') ? 'active' : '' }}">
    <a href="{{ route('entertainer.portal.dashboard') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-home-alt-2"></i>
      <div class="text-truncate">Entertainer Dashboard</div>
    </a>
  </li>

  <li class="menu-item {{ request()->is('entertainer-portal/packages') ? 'active' : '' }}">
    <a href="{{ route('entertainer.portal.packages') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-list-ul"></i>
      <div class="text-truncate">My Packages</div>
    </a>
  </li>

  <li class="menu-item {{ request()->is('admins/feed-post*') ? 'active' : '' }}">
    <a href="{{ route('admin.feed-post.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-images"></i>
      <div class="text-truncate">My Feed Posts</div>
    </a>
  </li>

  <li class="menu-item {{ request()->is('entertainer-portal/settings') ? 'active' : '' }}">
    <a href="{{ route('entertainer.portal.settings') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-palette"></i>
      <div class="text-truncate">Page Customization</div>
    </a>
  </li>

  <li class="menu-item {{ request()->is('entertainer-portal/wallet') ? 'active' : '' }}">
    <a href="{{ route('entertainer.portal.wallet') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-wallet"></i>
      <div class="text-truncate">Wallet</div>
    </a>
  </li>

  @if(auth()->user()->entertainer && auth()->user()->entertainer->slug)
  <li class="menu-item">
    <a href="{{ route('entertainer.public', auth()->user()->entertainer->slug) }}" target="_blank" class="menu-link">
      <i class="menu-icon tf-icons bx bx-link-external"></i>
      <div class="text-truncate">My Entertainer Page</div>
    </a>
  </li>
  @endif
  @endif

  <li class="menu-item">
    <a href="{{ route('admin.profile.edit') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-user"></i>
      <div class="text-truncate">Profile</div>
    </a>
  </li>

  <li class="menu-item" style="position: absolute; bottom: 0px;">
    <a href="/logout" class="menu-link" style="background: red; color: #fff;">
      <i class="menu-icon tf-icons bx bx-power-off"></i>
      <div class="text-truncate">Logout</div>
    </a>
  </li>
</ul>

        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          @if(session('success') || session('error') || session('warning') || session('info') || $errors->any())
          <div class="container-xxl mt-3">
            <div class="admin-feedback-stack">
              @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success:</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              @endif

              @if(session('error'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Failure:</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              @endif

              @if(session('warning'))
              <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Warning:</strong> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              @endif

              @if(session('info'))
              <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>Info:</strong> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              @endif

              @if($errors->any())
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Validation failed:</strong> Please review the exact issues below.
                <ul>
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              @endif
            </div>
          </div>
          @endif



          @yield('content')

        <!-- Footer -->
    <footer class="content-footer footer bg-footer-theme">
        <div class="container-xxl">
        </div>
    </footer>
    <!-- / Footer -->

    <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
    </div>
    <!-- / Layout page -->
    </div>

          <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->

    <script src="{{asset('user/assets/vendor/libs/jquery/jquery.js')}}"></script>

    <script src="{{asset('user/assets/vendor/libs/popper/popper.js')}}"></script>
    <script src="{{asset('user/assets/vendor/js/bootstrap.js')}}"></script>

    <script src="{{asset('user/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>

    <script src="{{asset('user/assets/vendor/js/menu.js')}}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{asset('user/assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <!-- Main JS -->

    <script src="{{asset('user/assets/js/main.js')}}"></script>

    <!-- Page JS -->
    <script src="{{asset('user/assets/js/dashboards-analytics.js')}}"></script>

    <script>
      (function () {
        function buildExportColumnSelector(table) {
          const headers = Array.from(table.querySelectorAll('thead th'));
          const firstHeader = headers[0];
          const lastHeader = headers[headers.length - 1];
          const firstIsCheckbox = firstHeader ? !!firstHeader.querySelector('input[type="checkbox"]') : false;
          const lastText = (lastHeader ? lastHeader.textContent : '').trim().toLowerCase();
          const lastIsAction = ['action', 'actions', 'manage'].includes(lastText);

          if (firstIsCheckbox && lastIsAction) {
            return ':visible:not(:first-child):not(:last-child)';
          }

          if (firstIsCheckbox) {
            return ':visible:not(:first-child)';
          }

          if (lastIsAction) {
            return ':visible:not(:last-child)';
          }

          return ':visible';
        }

        function hasLocalDataTableInitializer(table) {
          const scriptText = Array.from(document.scripts)
            .map(function (script) { return script.textContent || ''; })
            .join('\n');

          if (/new\s+DataTable\(\s*['"]\.table['"]/.test(scriptText) || /\$\(\s*['"]\.table['"]\s*\)\.DataTable/.test(scriptText)) {
            return true;
          }

          if (!table.id) {
            return false;
          }

          const escapedId = table.id.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
          const idPatterns = [
            new RegExp("new\\s+DataTable\\(\\s*['\"]#" + escapedId + "['\"]"),
            new RegExp("\\$\\(\\s*['\"]#" + escapedId + "['\"]\\s*\\)\\.DataTable"),
            new RegExp("\\$\\(\\s*['\"]#" + escapedId + "['\"]\\s*\\)\\.dataTable")
          ];

          return idPatterns.some(function (pattern) {
            return pattern.test(scriptText);
          });
        }

        function initAdminDataTables() {
          if (!window.jQuery || !jQuery.fn || !jQuery.fn.DataTable) {
            return;
          }

          if (jQuery.fn.dataTable && jQuery.fn.dataTable.ext) {
            jQuery.fn.dataTable.ext.errMode = 'none';
          }

          jQuery('.layout-page table').each(function () {
            const table = this;

            if (table.classList.contains('no-datatable')) {
              return;
            }

            if (!table.querySelector('thead')) {
              return;
            }

            if (hasLocalDataTableInitializer(table)) {
              return;
            }

            if (jQuery.fn.dataTable.isDataTable(table)) {
              return;
            }

            const exportColumns = buildExportColumnSelector(table);

            jQuery(table).DataTable({
              dom: 'Bfrtip',
              pageLength: 25,
              order: [],
              autoWidth: false,
              buttons: [
                { extend: 'csv', text: 'Export CSV', exportOptions: { columns: exportColumns } },
                { extend: 'excel', text: 'Export Excel', exportOptions: { columns: exportColumns } },
                { extend: 'pdf', text: 'Export PDF', exportOptions: { columns: exportColumns } },
                { extend: 'print', text: 'Print', exportOptions: { columns: exportColumns } }
              ]
            });
          });
        }

        function wrapTablesForMobile() {
          document.querySelectorAll('.layout-page table').forEach(function (table) {
            if (table.closest('.table-responsive, .admin-table-responsive')) {
              return;
            }

            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive admin-table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
          });
        }

        function bindMobileMenuToggle() {
          const toggle = document.querySelector('.admin-mobile-menu-toggle');
          const wrapper = document.querySelector('.layout-wrapper');
          const overlay = document.querySelector('.layout-overlay');

          if (!toggle || !wrapper) {
            return;
          }

          function syncState() {
            const expanded = wrapper.classList.contains('layout-menu-expanded');
            toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            toggle.setAttribute('aria-label', expanded ? 'Close sidebar' : 'Open sidebar');
          }

          toggle.addEventListener('click', function (event) {
            event.preventDefault();

            if (window.innerWidth >= 1200) {
              return;
            }

            wrapper.classList.toggle('layout-menu-expanded');
            syncState();
          });

          if (overlay) {
            overlay.addEventListener('click', function () {
              wrapper.classList.remove('layout-menu-expanded');
              syncState();
            });
          }

          window.addEventListener('resize', function () {
            if (window.innerWidth >= 1200) {
              wrapper.classList.remove('layout-menu-expanded');
            }

            syncState();
          });

          syncState();
        }

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', function () {
            wrapTablesForMobile();
            initAdminDataTables();
            bindMobileMenuToggle();
          });
        } else {
          wrapTablesForMobile();
          initAdminDataTables();
          bindMobileMenuToggle();
        }
      })();

      (function () {
        const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        const MAX_BYTES = 4 * 1024 * 1024;
        const CRITERIA_TEXT = 'Allowed image types: JPG, PNG, WEBP, GIF, SVG. Max size: 4MB per image.';

        function isImageInput(input) {
          const key = ((input.name || '') + ' ' + (input.id || '') + ' ' + (input.className || '')).toLowerCase();
          return /image|logo|banner|gallery|photo|avatar|icon/.test(key);
        }

        function ensureCriteriaText(input) {
          if (input.dataset.criteriaBound === '1') {
            return;
          }

          if (!input.accept) {
            input.accept = 'image/jpeg,image/png,image/webp,image/gif,image/svg+xml';
          }

          const note = document.createElement('small');
          note.className = 'text-muted d-block mt-1 image-upload-criteria';
          note.textContent = CRITERIA_TEXT;
          input.insertAdjacentElement('afterend', note);
          input.dataset.criteriaBound = '1';
        }

        function showInputWarning(input, message) {
          clearInputWarning(input);
          const warning = document.createElement('div');
          warning.className = 'text-danger mt-1 image-upload-warning';
          warning.style.fontSize = '0.85rem';
          warning.textContent = message;
          input.insertAdjacentElement('afterend', warning);
        }

        function clearInputWarning(input) {
          const next = input.parentElement ? input.parentElement.querySelector('.image-upload-warning') : null;
          if (next) {
            next.remove();
          }
        }

        function validateSelectedFiles(input) {
          clearInputWarning(input);

          const files = Array.from(input.files || []);
          if (!files.length) {
            return;
          }

          for (const file of files) {
            const extension = (file.name.split('.').pop() || '').toLowerCase();
            const mime = (file.type || '').toLowerCase();
            const mimeAllowed = mime.startsWith('image/');
            const extensionAllowed = ALLOWED_EXTENSIONS.includes(extension);

            if (!mimeAllowed || !extensionAllowed) {
              input.value = '';
              showInputWarning(input, 'Invalid image type. Please upload JPG, PNG, WEBP, GIF, or SVG.');
              return;
            }

            if (file.size > MAX_BYTES) {
              input.value = '';
              showInputWarning(input, 'Image is too large. Maximum allowed size is 4MB per image.');
              return;
            }
          }
        }

        function bindUploadCriteria() {
          document.querySelectorAll('input[type="file"]').forEach(function (input) {
            if (!isImageInput(input)) {
              return;
            }

            ensureCriteriaText(input);

            input.addEventListener('change', function () {
              validateSelectedFiles(input);
            });
          });
        }

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', bindUploadCriteria);
        } else {
          bindUploadCriteria();
        }
      })();
    </script>

    <!-- Place this tag before closing body tag for github widget button. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <style id="admin-datatables-contrast-overrides">
      body .content-wrapper label,
      body .content-wrapper .form-label,
      body .content-wrapper .col-form-label,
      body .content-wrapper .dataTables_wrapper label {
        color: #f4f7ff !important;
        font-weight: 600;
      }

      body .content-wrapper .card,
      body .content-wrapper .card-shadow-primary,
      body .content-wrapper .card-shadow-primary.card-border,
      body .content-wrapper .card.bg-primary,
      body .content-wrapper .card.bg-secondary {
        background: #121726 !important;
        color: #e8eaf6 !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
      }

      body .content-wrapper .table,
      body .content-wrapper .table > :not(caption) > * > *,
      body .content-wrapper table.dataTable,
      body .content-wrapper table.dataTable > :not(caption) > * > * {
        background: transparent !important;
        color: #eef2ff !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
      }

      body .content-wrapper .table thead th,
      body .content-wrapper table.dataTable thead th {
        color: #ffffff !important;
      }

      body .content-wrapper .table-striped > tbody > tr:nth-of-type(odd) > * {
        --bs-table-accent-bg: rgba(255, 255, 255, 0.03) !important;
      }

      div.dataTables_wrapper .dataTables_length label,
      div.dataTables_wrapper .dataTables_filter label,
      div.dataTables_wrapper .dataTables_info,
      div.dataTables_wrapper .dataTables_paginate {
        color: #f7f9ff !important;
        font-weight: 600 !important;
      }

      div.dataTables_wrapper .dataTables_filter input,
      div.dataTables_wrapper .dataTables_length select {
        background: #0f1524 !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 204, 0, 0.7) !important;
      }

      div.dataTables_wrapper .dataTables_filter input::placeholder {
        color: #cfd6ee !important;
        opacity: 1 !important;
      }

      div.dataTables_wrapper .dt-buttons .dt-button,
      div.dataTables_wrapper .dt-buttons button.dt-button,
      div.dataTables_wrapper .dt-buttons a.dt-button {
        background: #ffcc00 !important;
        color: #1a1400 !important;
        border: 1px solid #ffcc00 !important;
        font-weight: 800 !important;
        text-shadow: none !important;
      }

      div.dataTables_wrapper .dt-buttons .dt-button:hover,
      div.dataTables_wrapper .dt-buttons button.dt-button:hover,
      div.dataTables_wrapper .dt-buttons a.dt-button:hover {
        background: #ffe37a !important;
        color: #140f00 !important;
        border-color: #ffe37a !important;
      }

      div.dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #f0f4ff !important;
      }

      div.dataTables_wrapper .dataTables_paginate .paginate_button.current,
      div.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #ffcc00 !important;
        border-color: #ffcc00 !important;
        color: #1a1400 !important;
        font-weight: 800 !important;
      }

      div.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(255, 255, 255, 0.14) !important;
        border-color: rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
      }

      /* ── Card titles and all headings inside cards ── */
      body .content-wrapper .card-title,
      body .content-wrapper .card-header .card-title,
      body .content-wrapper .card h1,
      body .content-wrapper .card h2,
      body .content-wrapper .card h3,
      body .content-wrapper .card h4,
      body .content-wrapper .card h5,
      body .content-wrapper .card h6,
      body .content-wrapper h1,
      body .content-wrapper h2,
      body .content-wrapper h3,
      body .content-wrapper h4,
      body .content-wrapper h5,
      body .content-wrapper h6 {
        color: #ffffff !important;
      }

      /* ── All selects / dropdowns everywhere in admin ── */
      body .content-wrapper select,
      body .content-wrapper .form-select,
      body .content-wrapper select option,
      body .content-wrapper .form-select option {
        background-color: #171d2f !important;
        color: #e8eaf6 !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
      }

      /* ── Text-dark class override (some cards still use it) ── */
      body .content-wrapper .text-dark {
        color: #e8eaf6 !important;
      }
    </style>

    <link rel="stylesheet" href="{{asset('user/assets/css/demo.css')}}" />

  </body>
</html>


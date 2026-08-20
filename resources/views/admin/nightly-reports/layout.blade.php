<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../../assets/">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>The Nightly Reports — Executive Operations & Analytics</title>

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="{{ asset('user/assets/img/favicon/favicon.svg') }}" />

  <!-- Google Fonts: DM Sans & Playfair Display -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="{{ asset('user/assets/vendor/fonts/iconify-icons.css') }}" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('user/assets/vendor/css/core.css') }}" />
  <link rel="stylesheet" href="{{ asset('user/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
  <link rel="stylesheet" href="{{ asset('user/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />

  <style>
    :root {
      --nr-bg: #07111f;
      --nr-surface: #0d1a2e;
      --nr-surface-2: #142238;
      --nr-surface-3: #1a2b47;
      --nr-border: rgba(255, 255, 255, 0.08);
      --nr-border-gold: rgba(201, 168, 76, 0.35);
      --nr-gold: #c9a84c;
      --nr-gold-bright: #e8be6a;
      --nr-gold-glow: rgba(201, 168, 76, 0.18);
      --nr-text: #e2e8f0;
      --nr-text-muted: #94a3b8;
      --nr-emerald: #10b981;
      --nr-rose: #f43f5e;
      --nr-amber: #f59e0b;
      --nr-blue: #38bdf8;
    }

    body {
      background-color: var(--nr-bg) !important;
      color: var(--nr-text) !important;
      font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
      overflow-x: hidden;
    }

    .layout-page, .content-wrapper, .bg-menu-theme {
      background-color: var(--nr-bg) !important;
    }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: var(--nr-bg); }
    ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--nr-gold); }

    /* Dedicated Nightly Reports Sidebar */
    #nr-sidebar {
      background: var(--nr-surface) !important;
      border-right: 1px solid var(--nr-border) !important;
      width: 270px;
      height: 100vh;
      display: flex;
      flex-direction: column;
      position: fixed;
      left: 0;
      top: 0;
      z-index: 1090;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nr-sidebar-brand {
      padding: 1.25rem 1.25rem 1rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--nr-border);
      background: rgba(13, 26, 46, 0.95);
    }

    .nr-brand-title {
      font-family: 'Playfair Display', serif;
      font-weight: 700;
      font-size: 1.15rem;
      color: #fff;
      display: flex;
      align-items: center;
      gap: 9px;
      letter-spacing: 0.02em;
    }

    .nr-brand-title i {
      color: var(--nr-gold);
      font-size: 1.1rem;
      filter: drop-shadow(0 0 6px var(--nr-gold-glow));
    }

    .nr-back-to-cartvip {
      display: flex;
      align-items: center;
      gap: 7px;
      padding: 0.45rem 0.85rem;
      margin: 0.85rem 0.85rem 0.35rem;
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.07);
      border-radius: 8px;
      color: var(--nr-text-muted);
      font-size: 0.78rem;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.2s;
    }

    .nr-back-to-cartvip:hover {
      background: rgba(201, 168, 76, 0.12);
      border-color: var(--nr-border-gold);
      color: var(--nr-gold-bright);
    }

    .nr-sidebar-menu {
      flex: 1 1 auto;
      overflow-y: auto;
      padding: 0.5rem 0.65rem 2rem;
      list-style: none;
      margin: 0;
    }

    .nr-menu-header {
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      color: var(--nr-gold);
      padding: 1rem 0.85rem 0.35rem;
      opacity: 0.85;
    }

    .nr-menu-item {
      margin-bottom: 2px;
    }

    .nr-menu-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 0.5rem 0.85rem;
      border-radius: 8px;
      color: var(--nr-text-muted);
      font-size: 0.83rem;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.18s ease-in-out;
    }

    .nr-menu-link i {
      font-size: 0.95rem;
      width: 18px;
      text-align: center;
      color: #64748b;
      transition: color 0.18s;
    }

    .nr-menu-link:hover {
      background: rgba(255, 255, 255, 0.04);
      color: #fff;
    }

    .nr-menu-link:hover i {
      color: var(--nr-gold);
    }

    .nr-menu-item.active .nr-menu-link {
      background: linear-gradient(90deg, rgba(201, 168, 76, 0.18) 0%, rgba(201, 168, 76, 0.05) 100%);
      color: #fff;
      font-weight: 600;
      border-left: 3px solid var(--nr-gold);
    }

    .nr-menu-item.active .nr-menu-link i {
      color: var(--nr-gold-bright);
    }

    /* Main Content Wrapper */
    .nr-main-wrapper {
      margin-left: 270px;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .nr-navbar {
      background: rgba(13, 26, 46, 0.85) !important;
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--nr-border);
      padding: 0.75rem 1.75rem;
      position: sticky;
      top: 0;
      z-index: 1050;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .nr-content {
      flex: 1 1 auto;
      padding: 1.75rem;
    }

    /* Luxury Card Aesthetics */
    .card, .nr-card {
      background: var(--nr-surface) !important;
      border: 1px solid var(--nr-border) !important;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }

    .card-header, .nr-card-header {
      background: rgba(255, 255, 255, 0.015) !important;
      border-bottom: 1px solid var(--nr-border) !important;
      padding: 1.1rem 1.4rem;
    }

    .card-title, .nr-card-title {
      font-size: 1rem;
      font-weight: 600;
      color: #fff;
      margin: 0;
    }

    /* Metric & KPI Cards */
    .nr-kpi-card {
      background: linear-gradient(145deg, var(--nr-surface) 0%, var(--nr-surface-2) 100%);
      border: 1px solid var(--nr-border);
      border-radius: 12px;
      padding: 1.25rem 1.4rem;
      position: relative;
      overflow: hidden;
      transition: transform 0.2s, border-color 0.2s;
    }

    .nr-kpi-card:hover {
      transform: translateY(-2px);
      border-color: var(--nr-border-gold);
    }

    .nr-kpi-card::after {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 60px;
      height: 60px;
      background: radial-gradient(circle, var(--nr-gold-glow) 0%, transparent 70%);
      pointer-events: none;
    }

    .nr-kpi-label {
      font-size: 0.76rem;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--nr-text-muted);
      font-weight: 600;
      margin-bottom: 0.4rem;
    }

    .nr-kpi-value {
      font-size: 1.65rem;
      font-weight: 700;
      color: #fff;
      line-height: 1.2;
    }

    .nr-kpi-sub {
      font-size: 0.78rem;
      margin-top: 0.35rem;
    }

    /* Badges */
    .badge-met {
      background: rgba(16, 185, 129, 0.15);
      color: #34d399;
      border: 1px solid rgba(16, 185, 129, 0.3);
      padding: 0.35rem 0.65rem;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 600;
    }

    .badge-below {
      background: rgba(244, 63, 94, 0.15);
      color: #fb7185;
      border: 1px solid rgba(244, 63, 94, 0.3);
      padding: 0.35rem 0.65rem;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 600;
    }

    .badge-gold {
      background: rgba(201, 168, 76, 0.15);
      color: var(--nr-gold-bright);
      border: 1px solid var(--nr-border-gold);
      padding: 0.35rem 0.65rem;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 600;
    }

    /* Forms & Inputs */
    .form-control, .form-select, select, input, textarea {
      background-color: var(--nr-surface-2) !important;
      border: 1px solid var(--nr-border) !important;
      color: var(--nr-text) !important;
      border-radius: 8px;
    }

    .form-control:focus, .form-select:focus, select:focus, input:focus, textarea:focus {
      border-color: var(--nr-gold) !important;
      box-shadow: 0 0 0 0.2rem var(--nr-gold-glow) !important;
    }

    /* Buttons */
    .btn-gold {
      background: linear-gradient(135deg, #c9a84c 0%, #b3923d 100%) !important;
      border: 1px solid #c9a84c !important;
      color: #0b0e1a !important;
      font-weight: 600;
      border-radius: 8px;
      transition: all 0.2s;
    }

    .btn-gold:hover {
      background: linear-gradient(135deg, #dfbc5e 0%, #c9a84c 100%) !important;
      box-shadow: 0 4px 14px var(--nr-gold-glow);
    }

    /* Tables */
    .table {
      color: var(--nr-text) !important;
      border-color: var(--nr-border) !important;
      vertical-align: middle;
    }

    .table thead th {
      background: rgba(255, 255, 255, 0.02) !important;
      color: #fff !important;
      font-size: 0.76rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      border-bottom: 1px solid var(--nr-border) !important;
      padding: 0.85rem 1rem;
    }

    .table tbody td {
      border-color: var(--nr-border) !important;
      padding: 0.85rem 1rem;
      font-size: 0.84rem;
    }

    .table tbody tr:hover {
      background-color: rgba(255, 255, 255, 0.02) !important;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
      #nr-sidebar { left: -280px; }
      .nr-main-wrapper { margin-left: 0; }
      body.nr-mobile-open #nr-sidebar { left: 0; }
    }
  </style>
  @stack('styles')
</head>
<body>

  <!-- 1. Dedicated 24-Item Nightly Reports Sidebar -->
  <aside id="nr-sidebar">
    <div class="nr-sidebar-brand">
      <div class="nr-brand-title">
        <i class="fas fa-moon"></i>
        <span>The Nightly Reports</span>
      </div>
    </div>

    <!-- Return to Main CartVIP Admin -->
    <a href="{{ route('admin.index') }}" class="nr-back-to-cartvip">
      <i class="fas fa-arrow-left"></i>
      <span>Return to CartVIP Admin</span>
    </a>

    <ul class="nr-sidebar-menu">
      <!-- Group 1: Overview & Daily -->
      <li class="nr-menu-header">Overview & Daily</li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.dashboard*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.dashboard') }}" class="nr-menu-link">
          <i class="fas fa-chart-pie"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.reports*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.reports.index') }}" class="nr-menu-link">
          <i class="fas fa-clipboard-list"></i>
          <span>Reports</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.trends*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.trends.index') }}" class="nr-menu-link">
          <i class="fas fa-chart-line"></i>
          <span>Trends</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.missing*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.missing.index') }}" class="nr-menu-link">
          <i class="fas fa-exclamation-triangle"></i>
          <span>Missing Reports</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.locations*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.locations.index') }}" class="nr-menu-link">
          <i class="fas fa-map-marker-alt"></i>
          <span>Locations</span>
        </a>
      </li>

      <!-- Group 2: Retail & Financial Audits -->
      <li class="nr-menu-header">Financials & Audits</li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.imports*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.imports.index') }}" class="nr-menu-link">
          <i class="fas fa-file-import"></i>
          <span>Import History</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.boutique-import*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.boutique-import.index') }}" class="nr-menu-link">
          <i class="fas fa-cash-register"></i>
          <span>Boutique Import</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.boutique.*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.boutique.index') }}" class="nr-menu-link">
          <i class="fas fa-store"></i>
          <span>Boutique Summary</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.fourweek*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.fourweek.index') }}" class="nr-menu-link">
          <i class="fas fa-calendar-alt"></i>
          <span>4-Week Reports</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.quarterly*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.quarterly.index') }}" class="nr-menu-link">
          <i class="fas fa-layer-group"></i>
          <span>Quarterly Reports</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.coh*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.coh.index') }}" class="nr-menu-link">
          <i class="fas fa-vault"></i>
          <span>COH Reports</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.high-transactions*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.high-transactions.index') }}" class="nr-menu-link">
          <i class="fas fa-money-check-alt"></i>
          <span>High Transactions</span>
        </a>
      </li>

      <!-- Group 3: Security, Legal & Compliance -->
      <li class="nr-menu-header">Risk & Legal Compliance</li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.incidents*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.incidents.index') }}" class="nr-menu-link">
          <i class="fas fa-shield-alt"></i>
          <span>Incident Reports</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.witness.*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.witness.index') }}" class="nr-menu-link">
          <i class="fas fa-file-signature"></i>
          <span>Witness Statements</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.witness-qr*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.witness-qr.index') }}" class="nr-menu-link">
          <i class="fas fa-qrcode"></i>
          <span>Witness QR Codes</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.model-releases*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.model-releases.index') }}" class="nr-menu-link">
          <i class="fas fa-id-card"></i>
          <span>Model Release Vault</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.legal*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.legal.index') }}" class="nr-menu-link">
          <i class="fas fa-balance-scale"></i>
          <span>Legal Access</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.document-requests*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.document-requests.index') }}" class="nr-menu-link">
          <i class="fas fa-folder-open"></i>
          <span>Document Requests</span>
        </a>
      </li>

      <!-- Group 4: Administration & Setup -->
      <li class="nr-menu-header">Administration & Setup</li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.ambassadors*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.ambassadors.index') }}" class="nr-menu-link">
          <i class="fas fa-users-cog"></i>
          <span>Ambassadors</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.benchmarks*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.benchmarks.index') }}" class="nr-menu-link">
          <i class="fas fa-bullseye"></i>
          <span>Benchmarks</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.form-builder*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.form-builder.index') }}" class="nr-menu-link">
          <i class="fas fa-sliders-h"></i>
          <span>Form Builder</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.quotes*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.quotes.index') }}" class="nr-menu-link">
          <i class="fas fa-quote-left"></i>
          <span>Daily Quotes</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.users*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.users.index') }}" class="nr-menu-link">
          <i class="fas fa-user-shield"></i>
          <span>Admin Users</span>
        </a>
      </li>
      <li class="nr-menu-item {{ request()->routeIs('admin.nightly-reports.backups*') ? 'active' : '' }}">
        <a href="{{ route('admin.nightly-reports.backups.index') }}" class="nr-menu-link">
          <i class="fas fa-database"></i>
          <span>Data Backup</span>
        </a>
      </li>
    </ul>
  </aside>

  <!-- 2. Main Content Wrapper -->
  <div class="nr-main-wrapper">
    <!-- Top Navbar -->
    <header class="nr-navbar">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm btn-outline-secondary d-lg-none" id="nr-toggle-btn" type="button">
          <i class="fas fa-bars"></i>
        </button>
        <span class="badge badge-gold"><i class="fas fa-building me-1"></i> Executive Portal</span>
      </div>

      <!-- Quick Action Buttons -->
      <div class="d-flex align-items-center gap-2">
        <div class="dropdown">
          <button class="btn btn-sm btn-gold dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="fas fa-plus me-1"></i> Submit Intake
          </button>
          <ul class="dropdown-menu dropdown-menu-end" style="background: var(--nr-surface-2); border-color: var(--nr-border);">
            <li><a class="dropdown-item text-white" href="{{ route('nightly.submit.nightly') }}" target="_blank"><i class="fas fa-moon me-2 text-warning"></i> Submit Nightly Report</a></li>
            <li><a class="dropdown-item text-white" href="{{ route('nightly.submit.boutique') }}" target="_blank"><i class="fas fa-store me-2 text-info"></i> Submit Boutique Report</a></li>
            <li><a class="dropdown-item text-white" href="{{ route('nightly.submit.coh') }}" target="_blank"><i class="fas fa-vault me-2 text-success"></i> Submit COH Audit</a></li>
            <li><a class="dropdown-item text-white" href="{{ route('nightly.submit.incident') }}" target="_blank"><i class="fas fa-shield-alt me-2 text-danger"></i> Submit Incident</a></li>
            <li><a class="dropdown-item text-white" href="{{ route('nightly.submit.witness') }}" target="_blank"><i class="fas fa-file-signature me-2 text-primary"></i> Witness Statement</a></li>
          </ul>
        </div>

        <div class="dropdown">
          <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name ?? 'Administrator' }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end" style="background: var(--nr-surface-2); border-color: var(--nr-border);">
            <li><a class="dropdown-item text-white" href="{{ route('admin.profile.edit') }}"><i class="fas fa-cog me-2"></i> Profile Settings</a></li>
            <li><hr class="dropdown-divider" style="border-color: var(--nr-border);"></li>
            <li><a class="dropdown-item text-danger" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt me-2"></i> Sign Out</a></li>
          </ul>
        </div>
      </div>
    </header>

    <!-- Feedback Alerts -->
    @if(session('success'))
      <div class="container-fluid px-4 pt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: rgba(16, 185, 129, 0.2); border-color: #10b981; color: #34d399;">
          <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      </div>
    @endif

    @if(session('error'))
      <div class="container-fluid px-4 pt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: rgba(244, 63, 94, 0.2); border-color: #f43f5e; color: #fb7185;">
          <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      </div>
    @endif

    <!-- Page Content -->
    <main class="nr-content">
      @yield('content')
    </main>
  </div>

  <script src="{{ asset('user/assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('user/assets/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('user/assets/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('user/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

  <script>
    $('#nr-toggle-btn').on('click', function() {
      $('body').toggleClass('nr-mobile-open');
    });
  </script>
  @stack('scripts')
</body>
</html>

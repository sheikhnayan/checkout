<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }} - Help Center | CartVIP</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('user/assets/img/favicon/favicon.svg') }}?v={{ time() }}" />
    <link rel="mask-icon" href="{{ asset('user/assets/img/favicon/safari-mask.svg') }}?v={{ time() }}" color="#ffcc00" />
    <link rel="shortcut icon" href="{{ asset('user/assets/img/favicon/favicon.ico') }}?v={{ time() }}" />
    
    <!-- Google Fonts & Boxicons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        :root {
            --hc-banner-color: {{ $page->banner_color ?: '#4f46e5' }};
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
        }
        .hc-header {
            background: linear-gradient(135deg, var(--hc-banner-color) 0%, #1e1b4b 100%);
            padding: 50px 0 40px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .hc-header-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .hc-search-box {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 14px;
            padding: 8px 18px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .hc-search-box i {
            color: #ffffff !important;
        }
        .hc-search-box input {
            background: transparent;
            border: none;
            color: #ffffff !important;
            font-size: 16px;
        }
        .hc-search-box input::placeholder {
            color: rgba(255, 255, 255, 0.8) !important;
            opacity: 1 !important;
        }
        .hc-search-box input:-ms-input-placeholder {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        .hc-search-box input::-ms-input-placeholder {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        .hc-search-box input:focus {
            outline: none;
            box-shadow: none;
            background: transparent;
            color: #ffffff !important;
        }
        footer.hc-footer {
            border-top: 1px solid #1e293b !important;
            color: #ffffff !important;
            padding: 1.5rem 0;
        }
        footer.hc-footer p,
        footer.hc-footer strong {
            color: #ffffff !important;
        }
        .hc-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .hc-card:hover {
            transform: translateY(-3px);
            border-color: #6366f1;
            box-shadow: 0 12px 28px rgba(99, 102, 241, 0.18);
        }
        .hc-item-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.25);
            color: #818cf8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }
        .hc-item-card {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 18px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
        }
        .hc-item-card:hover {
            background: #1e293b;
            border-color: #818cf8;
            color: inherit;
        }
        .hc-badge-form {
            background: rgba(14, 165, 233, 0.15);
            color: #38bdf8;
            border: 1px solid rgba(14, 165, 233, 0.3);
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 600;
        }
        .hc-badge-ext {
            background: rgba(148, 163, 184, 0.15);
            color: #cbd5e1;
            border: 1px solid rgba(148, 163, 184, 0.3);
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 600;
        }
        .hc-badge-file {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Top Header Navigation Bar -->
    <header class="hc-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="hc-header-badge">
                    <i class="bx bx-shield-quarter"></i> CartVIP Authenticated Help Center
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white-50 fs-7">Logged in as: <strong class="text-white">{{ $user->name }}</strong></span>
                    <a href="{{ route('admin.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                        <i class="bx bx-dashboard me-1"></i> CartVIP Dashboard
                    </a>
                </div>
            </div>

            <h1 class="fw-bold text-white display-5 mb-2">{{ $page->title }}</h1>
            @if($page->description)
                <p class="text-white-50 fs-5 max-w-2xl mb-4">{{ $page->description }}</p>
            @endif

            <div class="d-flex align-items-center gap-3 text-white-50 fs-7 flex-wrap mt-3">
                <div><i class="bx bx-user me-1"></i> Owner: <strong class="text-white">{{ $page->owner->name ?? 'CartVIP User' }}</strong></div>
                @if($page->acceptedCollaborators->count() > 0)
                    <div>
                        <i class="bx bx-group me-1"></i> Collaborators: 
                        <strong class="text-white">
                            {{ $page->acceptedCollaborators->pluck('user.name')->filter()->implode(', ') }}
                        </strong>
                    </div>
                @endif
            </div>
        </div>
    </header>

    <!-- Search & Content Section -->
    <main class="container py-5">
        <!-- Search Bar -->
        <div class="mb-5">
            <div class="hc-search-box d-flex align-items-center max-w-xl mx-auto">
                <i class="bx bx-search text-white fs-4 me-2"></i>
                <input type="text" id="hcSearchInput" class="form-control text-white" placeholder="Search forms, guides, and resources..." onkeyup="filterHelpCenterItems()">
            </div>
        </div>

        @if($page->sections->count() === 0)
            <div class="text-center py-5">
                <i class="bx bx-folder-open text-muted display-1 mb-3"></i>
                <h4 class="text-muted fw-semibold">No Help Center sections have been published yet.</h4>
            </div>
        @else
            <div class="d-flex flex-column gap-5">
                @foreach($page->sections as $section)
                    <div class="hc-section-block" data-section-id="{{ $section->id }}">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bx bx-folder text-indigo-400 fs-3" style="color: #818cf8;"></i>
                            <h3 class="fw-bold mb-0 text-white fs-4">{{ $section->title }}</h3>
                        </div>
                        @if($section->description)
                            <p class="text-slate-400 fs-6 mb-4" style="color: #94a3b8;">{{ $section->description }}</p>
                        @endif

                        @if($section->items->count() === 0)
                            <p class="text-muted fs-7">No items added to this section.</p>
                        @else
                            <div class="row g-3">
                                @foreach($section->items as $item)
                                    <div class="col-md-6 col-lg-4 hc-item-col" data-title="{{ strtolower($item->resolved_title) }}" data-desc="{{ strtolower($item->description) }}">
                                        <a href="{{ $item->resolved_url }}" target="_blank" class="hc-item-card">
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div class="hc-item-icon">
                                                        <i class="bx {{ $item->icon ?: ($item->type === 'form' ? 'bx-file' : ($item->type === 'file' ? 'bx-cloud-download' : 'bx-link-external')) }}"></i>
                                                    </div>
                                                    @if($item->type === 'form')
                                                        <span class="hc-badge-form"><i class="bx bx-list-check me-1"></i> Form</span>
                                                    @elseif($item->type === 'file')
                                                        <span class="hc-badge-file"><i class="bx bx-download me-1"></i> Document</span>
                                                    @else
                                                        <span class="hc-badge-ext"><i class="bx bx-link-external me-1"></i> Link</span>
                                                    @endif
                                                </div>
                                                <h5 class="fw-bold text-white mb-2 fs-5">{{ $item->resolved_title }}</h5>
                                                @if($item->description)
                                                    <p class="text-slate-400 fs-7 mb-3" style="color: #94a3b8; line-height: 1.5;">{{ $item->description }}</p>
                                                @endif
                                            </div>
                                            <div class="pt-3 border-top border-slate-700/50 d-flex align-items-center justify-content-between text-indigo-400 fs-7 fw-semibold" style="border-color: #334155 !important; color: #818cf8;">
                                                <span>{{ $item->type === 'form' ? 'Open Form' : ($item->type === 'file' ? 'Download Document' : 'Visit Link') }}</span>
                                                <i class="bx {{ $item->type === 'file' ? 'bx-download' : 'bx-right-arrow-alt' }} fs-5"></i>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    <footer class="text-center py-4 hc-footer fs-7">
        <div class="container">
            <p class="mb-0 text-white">Powered by <strong class="text-white">CartVIP Help Center Portal</strong> &copy; {{ date('Y') }}. All rights reserved.</p>
        </div>
    </footer>

    <script>
    function filterHelpCenterItems() {
        var query = document.getElementById('hcSearchInput').value.toLowerCase().trim();
        var cols = document.querySelectorAll('.hc-item-col');
        var sectionBlocks = document.querySelectorAll('.hc-section-block');

        cols.forEach(function(col) {
            var title = col.getAttribute('data-title') || '';
            var desc = col.getAttribute('data-desc') || '';
            if (title.indexOf(query) > -1 || desc.indexOf(query) > -1) {
                col.style.display = 'block';
            } else {
                col.style.display = 'none';
            }
        });

        sectionBlocks.forEach(function(block) {
            var visibleCols = block.querySelectorAll('.hc-item-col[style="display: block;"], .hc-item-col:not([style*="display: none"])');
            if (query === '' || visibleCols.length > 0) {
                block.style.display = 'block';
            } else {
                block.style.display = 'none';
            }
        });
    }
    </script>
</body>
</html>

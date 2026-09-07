<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Search | Indeed-Style Marketplace</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('user/assets/img/favicon/favicon.svg') }}?v={{ time() }}" />
    <link rel="mask-icon" href="{{ asset('user/assets/img/favicon/safari-mask.svg') }}?v={{ time() }}" color="#2557a7" />
    <link rel="shortcut icon" href="{{ asset('user/assets/img/favicon/favicon.ico') }}?v={{ time() }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --indeed-blue: #2557a7;
            --indeed-blue-hover: #164081;
            --indeed-blue-light: #e8f0fe;
            --indeed-dark: #2d2d2d;
            --indeed-grey-text: #595959;
            --indeed-border: #d4d2d0;
            --indeed-bg: #f4f2f0;
            --indeed-card: #ffffff;
            --indeed-pill-bg: #f3f2f1;
            --indeed-radius: 8px;
            --indeed-font: 'Noto Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        * { box-sizing: border-box; }
        
        body {
            margin: 0;
            font-family: var(--indeed-font);
            background-color: var(--indeed-card);
            color: var(--indeed-dark);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Header Navigation Bar */
        .indeed-header {
            border-bottom: 1px solid var(--indeed-border);
            background: var(--indeed-card);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .indeed-header-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 32px;
        }
        .indeed-logo {
            font-size: 1.6rem;
            font-weight: 900;
            color: var(--indeed-blue);
            text-decoration: none;
            letter-spacing: -0.8px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .indeed-logo span.dot {
            color: #ff5a5f;
            font-size: 1.8rem;
            line-height: 0;
        }
        .header-nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-tab {
            padding: 20px 12px;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--indeed-dark);
            text-decoration: none;
            border-bottom: 3px solid transparent;
            transition: color 0.15s ease, border-color 0.15s ease;
        }
        .nav-tab:hover {
            color: var(--indeed-blue);
        }
        .nav-tab.active {
            color: var(--indeed-blue);
            border-bottom-color: var(--indeed-blue);
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .post-job-link {
            color: var(--indeed-blue);
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 6px;
        }
        .post-job-link:hover {
            background-color: var(--indeed-blue-light);
        }

        /* Search Section */
        .search-container-section {
            background: #ffffff;
            border-bottom: 1px solid var(--indeed-border);
            padding: 24px 20px 20px;
        }
        .search-shell {
            max-width: 1280px;
            margin: 0 auto;
        }
        .indeed-search-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .search-inputs-box {
            display: flex;
            background: #ffffff;
            border: 2px solid var(--indeed-dark);
            border-radius: var(--indeed-radius);
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .input-group-cell {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 8px 16px;
            background: #fff;
            position: relative;
        }
        .input-group-cell:not(:last-child) {
            border-right: 1px solid var(--indeed-border);
        }
        .input-group-cell svg {
            color: var(--indeed-grey-text);
            margin-right: 10px;
            flex-shrink: 0;
        }
        .cell-content {
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .input-label {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--indeed-dark);
            margin-bottom: 2px;
        }
        .cell-input, .cell-select, #searchQ {
            border: 0 !important;
            outline: 0 !important;
            box-shadow: none !important;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--indeed-dark);
            width: 100%;
            background: transparent;
            padding: 4px 0;
            margin: 0;
        }
        .cell-input:focus, .cell-select:focus, #searchQ:focus {
            border: 0 !important;
            outline: 0 !important;
            box-shadow: none !important;
        }
        .cell-select {
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
        }
        .search-submit-btn {
            background: var(--indeed-blue);
            color: #ffffff;
            border: 0;
            padding: 0 32px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            white-space: nowrap;
        }
        .search-submit-btn:hover {
            background: var(--indeed-blue-hover);
        }

        /* Filter Pills Row */
        .filter-pills-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filter-select-pill {
            background: var(--indeed-pill-bg);
            border: 1px solid var(--indeed-border);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--indeed-dark);
            cursor: pointer;
            outline: 0;
            transition: all 0.15s ease;
        }
        .filter-select-pill:hover, .filter-select-pill.active {
            border-color: var(--indeed-blue);
            background: var(--indeed-blue-light);
            color: var(--indeed-blue);
        }
        .clear-filters-btn {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--indeed-blue);
            text-decoration: none;
            padding: 4px 8px;
        }

        /* Split-Screen Main Layout */
        .indeed-main-layout {
            max-width: 1280px;
            margin: 0 auto;
            padding: 24px 20px 60px;
        }
        .split-grid {
            display: grid;
            grid-template-columns: 460px 1fr;
            gap: 24px;
            align-items: start;
        }
        
        /* Left Column: Job Cards List */
        .left-feed-column {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .job-list-container {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .feed-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--indeed-border);
        }
        .results-count-text {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--indeed-grey-text);
        }
        .results-count-text strong {
            color: var(--indeed-dark);
        }

        /* Indeed Job Card Styles */
        .indeed-job-card {
            background: var(--indeed-card);
            border: 1px solid var(--indeed-border);
            border-radius: var(--indeed-radius);
            padding: 20px;
            cursor: pointer;
            position: relative;
            margin-bottom: 2px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .indeed-job-card:hover {
            border-color: var(--indeed-blue);
            box-shadow: 0 4px 12px rgba(37, 87, 167, 0.08);
        }
        .indeed-job-card.active {
            border-color: var(--indeed-blue);
            border-left: 5px solid var(--indeed-blue);
            background-color: #fafbfc;
            box-shadow: 0 4px 14px rgba(37, 87, 167, 0.12);
        }
        .job-card-header {
            margin-bottom: 8px;
        }
        .job-badge-category {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--indeed-blue);
            background: var(--indeed-blue-light);
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 6px;
        }
        .job-card-title {
            margin: 0 0 4px;
            font-size: 1.15rem;
            font-weight: 700;
            line-height: 1.3;
        }
        .job-card-title a {
            color: var(--indeed-blue);
            text-decoration: none;
        }
        .job-card-title a:hover {
            text-decoration: underline;
        }
        .job-card-company {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--indeed-dark);
        }
        .job-card-location {
            font-size: 0.88rem;
            color: var(--indeed-grey-text);
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 2px;
        }

        .job-card-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin: 12px 0 10px;
        }
        .tag-pill {
            background: var(--indeed-pill-bg);
            color: var(--indeed-dark);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .tag-pay {
            background: #eef7ee;
            color: #1e7e34;
            font-weight: 700;
        }
        .easily-apply-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--indeed-blue);
            margin-bottom: 10px;
        }
        .job-card-snippet {
            font-size: 0.88rem;
            color: #475569;
            margin: 8px 0 12px;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .job-card-footer {
            font-size: 0.78rem;
            color: var(--indeed-grey-text);
            border-top: 1px solid #f1f5f9;
            padding-top: 8px;
        }

        /* Right Column: Sticky Job Detail Pane */
        .right-detail-column {
            position: sticky;
            top: 84px;
            background: var(--indeed-card);
            border: 1px solid var(--indeed-border);
            border-radius: var(--indeed-radius);
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            overflow: hidden;
            max-height: calc(100vh - 104px);
            display: flex;
            flex-direction: column;
        }
        .detail-pane-scroll {
            overflow-y: auto;
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .detail-header {
            border-bottom: 1px solid var(--indeed-border);
            padding-bottom: 20px;
        }
        .detail-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--indeed-dark);
            margin: 0 0 6px;
            line-height: 1.25;
        }
        .detail-company {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--indeed-blue);
            margin-bottom: 4px;
        }
        .detail-location {
            font-size: 0.95rem;
            color: var(--indeed-grey-text);
            margin-bottom: 16px;
        }
        .detail-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 16px;
        }
        .btn-indeed-apply {
            background: var(--indeed-blue);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.15s ease;
            box-shadow: 0 2px 6px rgba(37, 87, 167, 0.2);
        }
        .btn-indeed-apply:hover {
            background: var(--indeed-blue-hover);
        }
        .btn-indeed-secondary {
            background: var(--indeed-pill-bg);
            color: var(--indeed-dark);
            font-size: 0.95rem;
            font-weight: 700;
            padding: 12px 18px;
            border-radius: 8px;
            border: 1px solid var(--indeed-border);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }
        .btn-indeed-secondary:hover {
            background: #e5e5e5;
        }

        .detail-section-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--indeed-dark);
            margin: 0 0 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid var(--indeed-blue-light);
        }
        .detail-body-text {
            font-size: 0.95rem;
            color: #334155;
            line-height: 1.6;
        }
        
        .skills-tag-cloud {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .skill-badge {
            background: var(--indeed-blue-light);
            color: var(--indeed-blue);
            font-size: 0.85rem;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .empty-state-card {
            background: #ffffff;
            border: 1px dashed var(--indeed-border);
            border-radius: var(--indeed-radius);
            padding: 40px 20px;
            text-align: center;
        }

        .loading-state {
            opacity: 0.5;
            pointer-events: none;
        }

        /* Mobile Responsiveness */
        @media (max-width: 992px) {
            .split-grid {
                grid-template-columns: 1fr;
            }
            .right-detail-column {
                display: none; /* Mobile view defaults to tapping job card to open full page */
            }
            .right-detail-column.mobile-open {
                display: flex;
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                max-height: 100vh;
                z-index: 1000;
                border-radius: 0;
            }
            .search-inputs-box {
                flex-direction: column;
            }
            .input-group-cell:not(:last-child) {
                border-right: 0;
                border-bottom: 1px solid var(--indeed-border);
            }
            .search-submit-btn {
                padding: 14px;
            }
        }
    </style>
</head>
<body>

<!-- Header Navigation -->
<header class="indeed-header">
    <div class="indeed-header-inner">
        <div class="header-left">
            <a href="{{ route('jobs.marketplace') }}" class="indeed-logo">
                checkout<span class="dot">•</span>jobs
            </a>
            <nav class="header-nav">
                <a href="{{ route('jobs.marketplace') }}" class="nav-tab active">Find jobs</a>
                <a href="{{ route('jobs.pre-apply') }}" class="nav-tab">General Application</a>
            </nav>
        </div>
        <div class="header-right">
            <a href="{{ route('jobs.pre-apply') }}" class="post-job-link">Submit Profile / Resume &rarr;</a>
        </div>
    </div>
</header>

<!-- Indeed Search Bar Section -->
<section class="search-container-section">
    <div class="search-shell">
        <form id="jobSearchForm" class="indeed-search-form" method="GET" action="{{ route('jobs.marketplace') }}">
            
            <div class="search-inputs-box">
                <!-- What Field -->
                <div class="input-group-cell">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <div class="cell-content">
                        <label class="input-label" for="searchQ">What</label>
                        <input type="text" name="q" id="searchQ" class="cell-input" value="{{ $filters['q'] }}" placeholder="Job title, keywords, or venue">
                    </div>
                </div>

                <!-- Where State Field -->
                <div class="input-group-cell">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <div class="cell-content">
                        <label class="input-label" for="searchState">State</label>
                        <select name="state" id="searchState" class="cell-select">
                            <option value="">All States</option>
                            @foreach($states as $st)
                                <option value="{{ $st }}" {{ $filters['state'] === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Where City Field -->
                <div class="input-group-cell">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M3 7v14M21 7v14M6 10h4M6 14h4M14 10h4M14 14h4M9 3h6v4H9z"/></svg>
                    <div class="cell-content">
                        <label class="input-label" for="searchCity">City</label>
                        <select name="city" id="searchCity" class="cell-select">
                            <option value="">All Cities</option>
                            @foreach($cities as $ct)
                                <option value="{{ $ct }}" {{ $filters['city'] === $ct ? 'selected' : '' }}>{{ $ct }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Search Button -->
                <button type="submit" class="search-submit-btn">
                    Find jobs
                </button>
            </div>

            <!-- Filter Pills Row -->
            <div class="filter-pills-row">
                <select name="employment_type" id="searchEmploymentType" class="filter-select-pill {{ $filters['employment_type'] ? 'active' : '' }}">
                    <option value="">Hours Type: All</option>
                    <option value="Full-time" {{ $filters['employment_type'] === 'Full-time' ? 'selected' : '' }}>Full-time</option>
                    <option value="Part-time" {{ $filters['employment_type'] === 'Part-time' ? 'selected' : '' }}>Part-time</option>
                    <option value="Freelance" {{ $filters['employment_type'] === 'Freelance' ? 'selected' : '' }}>Freelance</option>
                    <option value="Contract" {{ $filters['employment_type'] === 'Contract' ? 'selected' : '' }}>Contract</option>
                </select>

                <select name="pay_frequency" id="searchPayFrequency" class="filter-select-pill {{ $filters['pay_frequency'] ? 'active' : '' }}">
                    <option value="">Pay Frequency: All</option>
                    <option value="per_hour" {{ $filters['pay_frequency'] === 'per_hour' ? 'selected' : '' }}>Per Hour</option>
                    <option value="per_year" {{ $filters['pay_frequency'] === 'per_year' ? 'selected' : '' }}>Per Year</option>
                    <option value="other" {{ $filters['pay_frequency'] === 'other' ? 'selected' : '' }}>Other</option>
                </select>

                <select name="job_type" id="searchType" class="filter-select-pill {{ $filters['job_type'] ? 'active' : '' }}">
                    <option value="">Category: All</option>
                    <option value="entertainer" {{ $filters['job_type'] === 'entertainer' ? 'selected' : '' }}>Entertainer</option>
                    <option value="employee" {{ $filters['job_type'] === 'employee' ? 'selected' : '' }}>Employee / Staff</option>
                </select>

                @if(array_filter($filters))
                    <a href="{{ route('jobs.marketplace') }}" class="clear-filters-btn">Reset Filters</a>
                @endif
            </div>

        </form>
    </div>
</section>

<!-- Split-Screen Main Layout -->
<main class="indeed-main-layout">
    <div class="split-grid">
        
        <!-- Left Feed Column: Job Listings -->
        <div class="left-feed-column">
            <div class="feed-header">
                <div class="results-count-text">
                    Showing <strong id="resultCount">{{ $jobs->total() }}</strong> jobs
                </div>
            </div>

            <div id="jobList" class="job-list-container">
                @include('jobs.partials.listings', ['jobs' => $jobs])
            </div>
        </div>

        <!-- Right Detail Column: Sticky Job Preview Pane -->
        <div class="right-detail-column" id="rightDetailPane">
            <div class="detail-pane-scroll" id="detailPaneScroll">
                
                <div class="detail-header">
                    <h1 class="detail-title" id="paneJobTitle">Select a job to view details</h1>
                    <div class="detail-company" id="paneCompany">--</div>
                    <div class="detail-location" id="paneLocation">--</div>
                    
                    <div class="job-card-tags" id="paneTags">
                        <!-- Dynamic tags inserted here -->
                    </div>

                    <div class="detail-actions">
                        <a href="#" id="paneApplyBtn" class="btn-indeed-apply">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                            Apply now
                        </a>
                        <a href="{{ route('jobs.pre-apply') }}" class="btn-indeed-secondary">
                            General Application
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="detail-section-title">Job details</h3>
                    <div id="paneJobMeta" class="detail-body-text">
                        <!-- Meta details -->
                    </div>
                </div>

                <div>
                    <h3 class="detail-section-title">Full Job Description</h3>
                    <div id="paneDescription" class="detail-body-text">
                        Please select a job post from the left column to read the complete description and application guidelines.
                    </div>
                </div>

                <div id="paneSkillsSection" style="display:none;">
                    <h3 class="detail-section-title">Required Skills & Traits</h3>
                    <div class="skills-tag-cloud" id="paneSkillsList">
                        <!-- Skills pills -->
                    </div>
                </div>

            </div>
        </div>

    </div>
</main>

<script>
    (function() {
        const statesAndCities = {!! json_encode($statesAndCities) !!};
        const form = document.getElementById('jobSearchForm');
        const list = document.getElementById('jobList');
        const resultCount = document.getElementById('resultCount');
        const q = document.getElementById('searchQ');
        const state = document.getElementById('searchState');
        const city = document.getElementById('searchCity');
        const empType = document.getElementById('searchEmploymentType');
        const payFreq = document.getElementById('searchPayFrequency');
        const type = document.getElementById('searchType');

        let timer;

        function updateCityDropdown(selectedState) {
            const currentSelectedCity = city.value;
            city.innerHTML = '<option value="">All Cities</option>';
            if (selectedState && statesAndCities[selectedState]) {
                statesAndCities[selectedState].forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c;
                    opt.textContent = c;
                    if (c === currentSelectedCity) opt.selected = true;
                    city.appendChild(opt);
                });
            }
        }

        window.selectJobCard = function(cardEl) {
            if (!cardEl) return;

            document.querySelectorAll('.indeed-job-card').forEach(c => c.classList.remove('active'));
            cardEl.classList.add('active');

            const title = cardEl.getAttribute('data-job-title') || '';
            const company = cardEl.getAttribute('data-job-company') || '';
            const location = cardEl.getAttribute('data-job-location') || '';
            const compensation = cardEl.getAttribute('data-compensation') || '';
            const empType = cardEl.getAttribute('data-employment-type') || '';
            const typeLabel = cardEl.getAttribute('data-job-type-label') || '';
            const applyUrl = cardEl.getAttribute('data-apply-url') || '#';
            const posted = cardEl.getAttribute('data-posted') || '';

            const descEl = cardEl.querySelector('.hidden-full-description');
            const skillsJson = cardEl.querySelector('.hidden-skills-json');
            const traitsJson = cardEl.querySelector('.hidden-traits-json');

            document.getElementById('paneJobTitle').textContent = title;
            document.getElementById('paneCompany').textContent = company;
            document.getElementById('paneLocation').textContent = location;
            document.getElementById('paneApplyBtn').setAttribute('href', applyUrl);

            // Tags
            let tagsHtml = '';
            if (compensation) {
                tagsHtml += `<span class="tag-pill tag-pay">⚡ ${compensation}</span>`;
            }
            if (empType) {
                tagsHtml += `<span class="tag-pill">${empType}</span>`;
            }
            if (typeLabel) {
                tagsHtml += `<span class="tag-pill">${typeLabel}</span>`;
            }
            document.getElementById('paneTags').innerHTML = tagsHtml;

            // Description
            document.getElementById('paneDescription').innerHTML = descEl ? descEl.innerHTML : '';

            // Meta
            let metaHtml = `<p><strong>Posted:</strong> ${posted}</p>`;
            if (compensation) metaHtml += `<p><strong>Pay / Compensation:</strong> ${compensation}</p>`;
            if (empType) metaHtml += `<p><strong>Employment Hours:</strong> ${empType}</p>`;
            document.getElementById('paneJobMeta').innerHTML = metaHtml;

            // Skills & Traits
            let skillsArr = [];
            let traitsArr = [];
            try { if (skillsJson) skillsArr = JSON.parse(skillsJson.textContent); } catch(e){}
            try { if (traitsJson) traitsArr = JSON.parse(traitsJson.textContent); } catch(e){}
            const combined = [...skillsArr, ...traitsArr];

            const skillsSection = document.getElementById('paneSkillsSection');
            const skillsList = document.getElementById('paneSkillsList');
            if (combined.length > 0) {
                skillsList.innerHTML = combined.map(s => `<span class="skill-badge">${s}</span>`).join('');
                skillsSection.style.display = 'block';
            } else {
                skillsSection.style.display = 'none';
            }

            // Scroll detail pane to top
            document.getElementById('detailPaneScroll').scrollTop = 0;
        };

        function bindFirstJobCard() {
            const firstCard = document.querySelector('.indeed-job-card');
            if (firstCard) {
                selectJobCard(firstCard);
            }
        }

        async function fetchJobs() {
            const params = new URLSearchParams({
                q: q ? q.value || '' : '',
                state: state ? state.value || '' : '',
                city: city ? city.value || '' : '',
                employment_type: empType ? empType.value || '' : '',
                pay_frequency: payFreq ? payFreq.value || '' : '',
                job_type: type ? type.value || '' : ''
            });

            list.classList.add('loading-state');
            try {
                const response = await fetch(`{{ route('jobs.listings') }}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) return;

                const data = await response.json();
                list.innerHTML = data.html;
                resultCount.textContent = data.total;
                bindFirstJobCard();
            } catch (error) {
                console.error(error);
            } finally {
                list.classList.remove('loading-state');
            }
        }

        function debouncedFetch() {
            clearTimeout(timer);
            timer = setTimeout(fetchJobs, 260);
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchJobs();
        });

        if (q) q.addEventListener('input', debouncedFetch);
        if (state) {
            state.addEventListener('change', function() {
                updateCityDropdown(this.value);
                fetchJobs();
            });
        }
        if (city) city.addEventListener('change', fetchJobs);
        if (empType) empType.addEventListener('change', fetchJobs);
        if (payFreq) payFreq.addEventListener('change', fetchJobs);
        if (type) type.addEventListener('change', fetchJobs);

        if (list) {
            list.addEventListener('click', function(e) {
                const card = e.target.closest('.indeed-job-card');
                if (card) {
                    const applyBtn = e.target.closest('.apply-btn');
                    if (!applyBtn) {
                        e.preventDefault();
                        selectJobCard(card);
                    }
                }
            });
        }

        // Auto-select first job on initial load
        bindFirstJobCard();
    })();
</script>
</body>
</html>

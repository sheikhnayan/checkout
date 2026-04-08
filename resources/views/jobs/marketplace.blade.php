<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobs Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --indeed-blue: #2557a7;
            --indeed-dark: #2d2d2d;
            --indeed-bg: #f3f2f1;
            --indeed-card: #ffffff;
            --indeed-border: #d4d2d0;
            --indeed-muted: #595959;
            --indeed-cta: #164081;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background: radial-gradient(circle at 15% 0%, #e8effd 0%, #f3f2f1 42%, #f3f2f1 100%);
            color: var(--indeed-dark);
        }
        .hero {
            background: linear-gradient(145deg, #2557a7 0%, #1f4a91 45%, #143872 100%);
            color: #fff;
            padding: 48px 16px 56px;
        }
        .hero-inner {
            max-width: 1100px;
            margin: 0 auto;
        }
        .hero h1 {
            margin: 0;
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            line-height: 1.15;
            font-weight: 900;
        }
        .hero p {
            margin-top: 10px;
            max-width: 700px;
            opacity: .92;
        }
        .shell {
            max-width: 1100px;
            margin: -28px auto 30px;
            padding: 0 16px;
        }
        .search-bar {
            background: #fff;
            border: 1px solid var(--indeed-border);
            border-radius: 14px;
            padding: 10px;
            box-shadow: 0 18px 40px rgba(22, 64, 129, .16);
            display: grid;
            gap: 10px;
            grid-template-columns: 1fr 1fr auto auto;
        }
        .search-bar input,
        .search-bar select {
            width: 100%;
            border: 1px solid var(--indeed-border);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 15px;
        }
        .search-bar button,
        .ghost-btn {
            border: 0;
            border-radius: 10px;
            padding: 12px 16px;
            font-weight: 700;
            cursor: pointer;
        }
        .search-btn {
            background: var(--indeed-blue);
            color: #fff;
        }
        .ghost-btn {
            background: #eef3fc;
            color: var(--indeed-cta);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .layout {
            display: grid;
            gap: 18px;
            margin-top: 18px;
        }
        .results-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .results-count {
            color: var(--indeed-muted);
            font-weight: 500;
        }
        .job-list {
            display: grid;
            gap: 12px;
        }
        .job-card {
            background: var(--indeed-card);
            border: 1px solid var(--indeed-border);
            border-radius: 12px;
            padding: 16px;
            transition: transform .12s ease, box-shadow .12s ease;
        }
        .job-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(37, 87, 167, .1);
        }
        .job-card-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        .job-title {
            margin: 0;
            color: var(--indeed-blue);
            font-size: 1.15rem;
        }
        .job-company, .job-location {
            margin: 4px 0 0;
            color: #3d3d3d;
        }
        .job-type {
            background: #ebf2ff;
            color: #174389;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: .82rem;
            height: fit-content;
            font-weight: 700;
        }
        .job-snippet { color: var(--indeed-muted); margin: 12px 0; }
        .job-meta { display: flex; gap: 10px; flex-wrap: wrap; font-size: .9rem; color: #4f4f4f; }
        .job-card-actions { margin-top: 12px; }
        .apply-btn {
            display: inline-block;
            background: var(--indeed-blue);
            color: #fff;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 8px;
            font-weight: 700;
        }
        .empty-state {
            background: #fff;
            border: 1px dashed var(--indeed-border);
            border-radius: 12px;
            padding: 24px;
            text-align: center;
        }
        .loading {
            opacity: .6;
            pointer-events: none;
        }
        @media (max-width: 900px) {
            .search-bar { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<section class="hero">
    <div class="hero-inner">
        <h1>Find your next nightclub role</h1>
        <p>Universal marketplace across all clubs. Search, filter by location, and apply in minutes.</p>
    </div>
</section>

<div class="shell">
    <form id="jobSearchForm" class="search-bar" method="GET" action="{{ route('jobs.marketplace') }}">
        <input type="text" name="q" id="searchQ" value="{{ $filters['q'] }}" placeholder="Job title, club, or keyword">
        <input type="text" name="location" id="searchLocation" value="{{ $filters['location'] }}" placeholder="City or state" list="marketplaceLocations">
        <datalist id="marketplaceLocations">
            @foreach($locations as $location)
                <option value="{{ $location }}"></option>
            @endforeach
        </datalist>
        <select name="job_type" id="searchType">
            <option value="">All job types</option>
            <option value="entertainer" {{ $filters['job_type'] === 'entertainer' ? 'selected' : '' }}>Entertainer</option>
            <option value="employee" {{ $filters['job_type'] === 'employee' ? 'selected' : '' }}>Employee</option>
        </select>
        <button type="submit" class="search-btn">Find jobs</button>
        <a href="{{ route('jobs.pre-apply') }}" class="ghost-btn">Preferred Club Form</a>
    </form>

    <div class="layout">
        <div class="results-head">
            <div class="results-count"><span id="resultCount">{{ $jobs->total() }}</span> jobs found</div>
        </div>

        <div id="jobList" class="job-list">
            @include('jobs.partials.listings', ['jobs' => $jobs])
        </div>
    </div>
</div>

<script>
    (function() {
        const form = document.getElementById('jobSearchForm');
        const list = document.getElementById('jobList');
        const resultCount = document.getElementById('resultCount');
        const q = document.getElementById('searchQ');
        const location = document.getElementById('searchLocation');
        const type = document.getElementById('searchType');

        let timer;

        async function fetchJobs() {
            const params = new URLSearchParams({
                q: q.value || '',
                location: location.value || '',
                job_type: type.value || ''
            });

            list.classList.add('loading');
            try {
                const response = await fetch(`{{ route('jobs.listings') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                list.innerHTML = data.html;
                resultCount.textContent = data.total;
            } catch (error) {
                console.error(error);
            } finally {
                list.classList.remove('loading');
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

        q.addEventListener('input', debouncedFetch);
        location.addEventListener('input', debouncedFetch);
        type.addEventListener('change', fetchJobs);
    })();
</script>
</body>
</html>

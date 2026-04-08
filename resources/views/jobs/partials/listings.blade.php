@forelse($jobs as $job)
    <article class="job-card">
        <div class="job-card-top">
            <div>
                <h3 class="job-title">{{ $job->title }}</h3>
                <p class="job-company">{{ $job->website->name ?? 'Club' }}</p>
                <p class="job-location">{{ $job->location ?: 'Location not listed' }}</p>
            </div>
            <span class="job-type">{{ ucfirst($job->job_type) }}</span>
        </div>
        <p class="job-snippet">{{ $job->short_description ?: \Illuminate\Support\Str::limit(strip_tags($job->description), 170) }}</p>
        <div class="job-meta">
            @if($job->employment_type)
                <span>{{ $job->employment_type }}</span>
            @endif
            @if($job->compensation)
                <span>{{ $job->compensation }}</span>
            @endif
            <span>Posted {{ optional($job->created_at)->diffForHumans() }}</span>
        </div>
        <div class="job-card-actions">
            <a href="{{ route('jobs.apply', $job) }}" class="apply-btn">Apply now</a>
        </div>
    </article>
@empty
    <div class="empty-state">
        <h3>No jobs matched your search.</h3>
        <p>Try changing keywords, location, or job type filter.</p>
    </div>
@endforelse

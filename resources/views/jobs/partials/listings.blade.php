@forelse($jobs as $index => $job)
    @php
        $payFreqText = '';
        if ($job->pay_frequency) {
            if ($job->pay_frequency === 'per_hour') $payFreqText = 'an hour';
            elseif ($job->pay_frequency === 'per_year') $payFreqText = 'a year';
            else $payFreqText = ucfirst($job->pay_frequency);
        }
        $compText = trim(($job->compensation ? $job->compensation : '') . ($payFreqText ? ' ' . $payFreqText : ''));
        $skillsArr = is_array($job->skills) ? $job->skills : (json_decode($job->skills, true) ?: []);
        $traitsArr = is_array($job->traits) ? $job->traits : (json_decode($job->traits, true) ?: []);
    @endphp

    <article class="indeed-job-card {{ $index === 0 ? 'active' : '' }}" 
             data-job-id="{{ $job->id }}"
             data-job-slug="{{ $job->slug }}"
             data-job-title="{{ e($job->title) }}"
             data-job-company="{{ e($job->website->name ?? 'Venue / Club') }}"
             data-job-location="{{ e($job->location ?: 'Location not listed') }}"
             data-job-type-label="{{ e(ucfirst($job->job_type)) }}"
             data-employment-type="{{ e($job->employment_type ?? '') }}"
             data-compensation="{{ e($compText) }}"
             data-posted="{{ e(optional($job->created_at)->diffForHumans() ?? '') }}"
             data-apply-url="{{ route('jobs.apply', $job) }}">
        
        <div class="job-card-header">
            <div class="job-card-title-group">
                <span class="job-badge-category">{{ ucfirst($job->job_type) }}</span>
                <h2 class="job-card-title">
                    <a href="{{ route('jobs.apply', $job) }}" onclick="event.preventDefault(); selectJobCard(this.closest('.indeed-job-card'));">
                        {{ $job->title }}
                    </a>
                </h2>
                <div class="job-card-company">{{ $job->website->name ?? 'Venue / Club' }}</div>
                <div class="job-card-location">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    {{ $job->location ?: 'Location not listed' }}
                </div>
            </div>
        </div>

        <div class="job-card-tags">
            @if($compText)
                <span class="tag-pill tag-pay">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    {{ $compText }}
                </span>
            @endif
            @if($job->employment_type)
                <span class="tag-pill tag-type">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    {{ $job->employment_type }}
                </span>
            @endif
        </div>

        <div class="job-card-apply-badge">
            <span class="easily-apply-pill">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                Easily apply
            </span>
        </div>

        <p class="job-card-snippet">
            {{ $job->short_description ?: \Illuminate\Support\Str::limit(strip_tags($job->description), 160) }}
        </p>

        <div class="job-card-footer">
            <span class="posted-date">Posted {{ optional($job->created_at)->diffForHumans() }}</span>
        </div>

        <!-- Hidden container for full details preview -->
        <div class="hidden-full-description" style="display:none;">
            {!! nl2br(e($job->description)) !!}
        </div>
        <div class="hidden-skills-json" style="display:none;">
            {!! json_encode($skillsArr) !!}
        </div>
        <div class="hidden-traits-json" style="display:none;">
            {!! json_encode($traitsArr) !!}
        </div>
    </article>
@empty
    <div class="empty-state-card">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <h3>No jobs match your search criteria</h3>
        <p>Try modifying your search keywords, location filters, or employment category.</p>
    </div>
@endforelse

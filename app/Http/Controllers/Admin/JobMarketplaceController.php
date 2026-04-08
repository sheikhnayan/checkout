<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\JobPreferenceRequest;
use App\Models\Website;
use Illuminate\Http\Request;

class JobMarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $jobs = JobPost::with('website')
            ->withCount('applications')
            ->whereIn('website_id', $this->accessibleWebsiteIds())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.jobs.index', compact('jobs'));
    }

    public function create()
    {
        $websites = $this->accessibleWebsites();

        return view('admin.jobs.create', compact('websites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'website_id' => ['required', 'integer'],
            'job_type' => ['required', 'in:entertainer,employee'],
            'title' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'employment_type' => ['nullable', 'string', 'max:255'],
            'compensation' => ['nullable', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:1000'],
            'description' => ['required', 'string'],
            'skills_text' => ['nullable', 'string', 'max:5000'],
            'traits_text' => ['nullable', 'string', 'max:5000'],
            'status' => ['nullable', 'boolean'],
        ]);

        $this->ensureWebsiteAccess((int) $validated['website_id']);

        JobPost::create([
            'website_id' => $validated['website_id'],
            'posted_by_user_id' => auth()->id(),
            'job_type' => $validated['job_type'],
            'title' => $validated['title'],
            'location' => $validated['location'],
            'employment_type' => $validated['employment_type'] ?? null,
            'compensation' => $validated['compensation'] ?? null,
            'short_description' => $validated['short_description'],
            'description' => $validated['description'],
            'skills' => $this->parseLines($validated['skills_text'] ?? null),
            'traits' => $this->parseLines($validated['traits_text'] ?? null),
            'status' => (bool) ($validated['status'] ?? true),
            'is_archived' => false,
        ]);

        return redirect()->route('admin.jobs.index')->with('success', 'Job post created successfully.');
    }

    public function edit(JobPost $job)
    {
        $this->ensureWebsiteAccess((int) $job->website_id);
        $websites = $this->accessibleWebsites();

        return view('admin.jobs.edit', compact('job', 'websites'));
    }

    public function update(Request $request, JobPost $job)
    {
        $this->ensureWebsiteAccess((int) $job->website_id);

        $validated = $request->validate([
            'website_id' => ['required', 'integer'],
            'job_type' => ['required', 'in:entertainer,employee'],
            'title' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'employment_type' => ['nullable', 'string', 'max:255'],
            'compensation' => ['nullable', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:1000'],
            'description' => ['required', 'string'],
            'skills_text' => ['nullable', 'string', 'max:5000'],
            'traits_text' => ['nullable', 'string', 'max:5000'],
            'status' => ['nullable', 'boolean'],
            'is_archived' => ['nullable', 'boolean'],
        ]);

        $this->ensureWebsiteAccess((int) $validated['website_id']);

        $job->update([
            'website_id' => $validated['website_id'],
            'job_type' => $validated['job_type'],
            'title' => $validated['title'],
            'location' => $validated['location'],
            'employment_type' => $validated['employment_type'] ?? null,
            'compensation' => $validated['compensation'] ?? null,
            'short_description' => $validated['short_description'],
            'description' => $validated['description'],
            'skills' => $this->parseLines($validated['skills_text'] ?? null),
            'traits' => $this->parseLines($validated['traits_text'] ?? null),
            'status' => (bool) ($validated['status'] ?? true),
            'is_archived' => (bool) ($validated['is_archived'] ?? false),
        ]);

        return redirect()->route('admin.jobs.index')->with('success', 'Job post updated successfully.');
    }

    public function applications(Request $request)
    {
        $applications = JobApplication::with(['website', 'jobPost'])
            ->whereIn('website_id', $this->accessibleWebsiteIds())
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('application_type', $request->type);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderByDesc('submitted_at')
            ->paginate(20);

        return view('admin.jobs.applications', compact('applications'));
    }

    public function showApplication(JobApplication $application)
    {
        $this->ensureWebsiteAccess((int) $application->website_id);
        $application->load(['website', 'jobPost']);

        return view('admin.jobs.application-show', compact('application'));
    }

    public function updateApplicationStatus(Request $request, JobApplication $application)
    {
        $this->ensureWebsiteAccess((int) $application->website_id);

        $validated = $request->validate([
            'status' => ['required', 'in:new,reviewed,shortlisted,rejected,hired'],
        ]);

        $application->update(['status' => $validated['status']]);

        return back()->with('success', 'Application status updated.');
    }

    public function preferenceRequests(Request $request)
    {
        $requests = JobPreferenceRequest::with('website')
            ->whereIn('website_id', $this->accessibleWebsiteIds())
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderByDesc('submitted_at')
            ->paginate(20);

        return view('admin.jobs.preference-requests', compact('requests'));
    }

    public function updatePreferenceStatus(Request $request, JobPreferenceRequest $preferenceRequest)
    {
        $this->ensureWebsiteAccess((int) $preferenceRequest->website_id);

        $validated = $request->validate([
            'status' => ['required', 'in:new,reviewed,contacted,closed'],
        ]);

        $preferenceRequest->update(['status' => $validated['status']]);

        return back()->with('success', 'Preferred-work request status updated.');
    }

    private function accessibleWebsiteIds(): array
    {
        $user = auth()->user();

        if ($user && $user->isAdmin()) {
            return Website::pluck('id')->all();
        }

        if ($user && $user->isWebsiteUser() && $user->website_id) {
            return [(int) $user->website_id];
        }

        return [];
    }

    private function accessibleWebsites()
    {
        $ids = $this->accessibleWebsiteIds();

        return Website::whereIn('id', $ids)->orderBy('name')->get();
    }

    private function ensureWebsiteAccess(int $websiteId): void
    {
        if (!in_array($websiteId, $this->accessibleWebsiteIds(), true)) {
            abort(403, 'Access denied for this website.');
        }
    }

    private function parseLines(?string $value): array
    {
        if (!$value) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $value) ?: [];

        return array_values(array_filter(array_map('trim', $lines)));
    }
}

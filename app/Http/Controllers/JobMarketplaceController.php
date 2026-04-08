<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\JobPreferenceRequest;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class JobMarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $jobs = $this->marketplaceQuery($request)->paginate(12);
        $locations = JobPost::where('status', true)
            ->where('is_archived', false)
            ->whereNotNull('location')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        return view('jobs.marketplace', [
            'jobs' => $jobs,
            'locations' => $locations,
            'filters' => [
                'q' => (string) $request->get('q', ''),
                'location' => (string) $request->get('location', ''),
                'job_type' => (string) $request->get('job_type', ''),
            ],
        ]);
    }

    public function listings(Request $request)
    {
        $jobs = $this->marketplaceQuery($request)->paginate(12);

        $html = view('jobs.partials.listings', ['jobs' => $jobs])->render();

        return response()->json([
            'html' => $html,
            'total' => $jobs->total(),
        ]);
    }

    public function applyForm(JobPost $job)
    {
        if (!$job->status || $job->is_archived) {
            abort(404);
        }

        $job->load('website');

        return view('jobs.apply', compact('job'));
    }

    public function submitApplication(Request $request, JobPost $job)
    {
        if (!$job->status || $job->is_archived) {
            abort(404);
        }

        if ($job->job_type === 'entertainer') {
            $data = $this->validateEntertainerApplication($request);
            $application = $this->storeEntertainerApplication($job, $data, $request);
        } else {
            $data = $this->validateEmployeeApplication($request);
            $this->validateEmployeeExperienceRequirement($request, $data);
            $application = $this->storeEmployeeApplication($job, $data, $request);
        }

        return redirect()
            ->route('jobs.apply', $job)
            ->with('success', 'Application submitted successfully. We will contact you soon. Ref #' . $application->id);
    }

    public function preApplyForm()
    {
        $websites = Website::where('status', 1)->where('is_archieved', 0)->orderBy('name')->get();

        return view('jobs.pre-apply', compact('websites'));
    }

    public function submitPreApply(Request $request)
    {
        $data = $request->validate([
            'website_id' => ['required', 'integer', 'exists:websites,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'confirmed'],
            'email_confirmation' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'preferred_role' => ['required', 'string', 'max:255'],
            'availability' => ['nullable', 'array'],
            'availability.*' => ['nullable', 'string', 'max:80'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'x_handle' => ['nullable', 'string', 'max:255'],
            'experience_summary' => ['required', 'string', 'max:3000'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:4096'],
            'headshot' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'message' => ['nullable', 'string', 'max:3000'],
            'terms' => ['accepted'],
            'age_confirm' => ['accepted'],
        ]);

        $attachments = [];
        if ($request->hasFile('resume')) {
            $attachments['resume'] = $this->storeUploadedFile($request->file('resume'), 'resume');
        }
        if ($request->hasFile('headshot')) {
            $attachments['headshot'] = $this->storeUploadedFile($request->file('headshot'), 'headshot');
        }

        JobPreferenceRequest::create([
            'website_id' => $data['website_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'city' => $data['city'],
            'state' => $data['state'],
            'preferred_role' => $data['preferred_role'],
            'availability' => array_values(array_filter($data['availability'] ?? [])),
            'social_handles' => [
                'instagram' => $data['instagram'] ?? null,
                'facebook' => $data['facebook'] ?? null,
                'tiktok' => $data['tiktok'] ?? null,
                'x' => $data['x_handle'] ?? null,
            ],
            'experience' => [
                'summary' => $data['experience_summary'],
            ],
            'attachments' => $attachments,
            'message' => $data['message'] ?? null,
            'status' => 'new',
            'submitted_at' => Carbon::now(),
        ]);

        return redirect()->route('jobs.pre-apply')->with('success', 'Your preferred-work profile has been sent.');
    }

    private function marketplaceQuery(Request $request)
    {
        return JobPost::with('website')
            ->where('status', true)
            ->where('is_archived', false)
            ->when($request->filled('job_type'), function ($query) use ($request) {
                $query->where('job_type', $request->job_type);
            })
            ->when($request->filled('location'), function ($query) use ($request) {
                $query->where('location', 'like', '%' . $request->location . '%');
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->q);
                $query->where(function ($subQuery) use ($term) {
                    $subQuery->where('title', 'like', '%' . $term . '%')
                        ->orWhere('short_description', 'like', '%' . $term . '%')
                        ->orWhere('description', 'like', '%' . $term . '%')
                        ->orWhereHas('website', function ($websiteQuery) use ($term) {
                            $websiteQuery->where('name', 'like', '%' . $term . '%');
                        });
                });
            })
            ->orderByDesc('created_at');
    }

    private function validateEntertainerApplication(Request $request): array
    {
        return $request->validate([
            'legal_first_name' => ['required', 'string', 'max:120'],
            'legal_last_name' => ['required', 'string', 'max:120'],
            'display_first_name' => ['required', 'string', 'max:120'],
            'display_last_name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'confirmed'],
            'email_confirmation' => ['required', 'email', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'preferred_contact_method' => ['required', 'in:phone,text,email'],
            'previous_employment' => ['nullable', 'string', 'max:3000'],
            'entertainer_resume' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:4096'],
            'personality_video' => ['required', 'file', 'mimes:mp4,mov,webm,avi', 'max:4096'],
            'portfolio_photos' => ['required', 'array', 'min:3', 'max:3'],
            'portfolio_photos.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'traits' => ['required', 'array', 'min:1'],
            'traits.*' => ['required', 'string', 'max:80'],
            'skills' => ['required', 'array', 'min:1'],
            'skills.*' => ['required', 'string', 'max:80'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'x_handle' => ['nullable', 'string', 'max:255'],
            'age_confirm' => ['accepted'],
            'terms' => ['accepted'],
        ]);
    }

    private function validateEmployeeApplication(Request $request): array
    {
        return $request->validate([
            'legal_first_name' => ['required', 'string', 'max:120'],
            'legal_last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'confirmed'],
            'email_confirmation' => ['required', 'email', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'positions' => ['required', 'array', 'min:1'],
            'positions.*' => ['required', 'string', 'max:150'],
            'picture_upload' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'video_upload' => ['required', 'file', 'mimes:mp4,mov,webm,avi', 'max:4096'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'x_handle' => ['nullable', 'string', 'max:255'],
            'skills' => ['required', 'array', 'min:1'],
            'skills.*' => ['required', 'string', 'max:120'],
            'heard_about' => ['required', 'string', 'max:255'],
            'availability' => ['required', 'array', 'min:1'],
            'availability.*' => ['required', 'string', 'max:120'],
            'employment_history' => ['nullable', 'array'],
            'employment_history.*.employer' => ['nullable', 'string', 'max:255'],
            'employment_history.*.dates' => ['nullable', 'string', 'max:255'],
            'employment_history.*.position' => ['nullable', 'string', 'max:255'],
            'employment_history.*.phone' => ['nullable', 'string', 'max:80'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:4096'],
            'contact_previous_employer' => ['required', 'in:yes,no'],
            'start_date' => ['required', 'date'],
            'education' => ['nullable', 'array'],
            'education.*' => ['nullable', 'string', 'max:255'],
            'extra_notes' => ['nullable', 'string', 'max:4000'],
            'terms' => ['accepted'],
            'age_confirm' => ['accepted'],
        ]);
    }

    private function storeEntertainerApplication(JobPost $job, array $data, Request $request): JobApplication
    {
        $attachments = [
            'resume' => $this->storeUploadedFile($request->file('entertainer_resume'), 'entertainer-resume'),
            'personality_video' => $this->storeUploadedFile($request->file('personality_video'), 'entertainer-video'),
            'portfolio_photos' => [],
        ];

        foreach ((array) $request->file('portfolio_photos', []) as $photo) {
            $attachments['portfolio_photos'][] = $this->storeUploadedFile($photo, 'entertainer-photo');
        }

        return JobApplication::create([
            'job_post_id' => $job->id,
            'website_id' => $job->website_id,
            'application_type' => 'entertainer',
            'legal_first_name' => $data['legal_first_name'],
            'legal_last_name' => $data['legal_last_name'],
            'display_first_name' => $data['display_first_name'],
            'display_last_name' => $data['display_last_name'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'],
            'city' => $data['city'],
            'state' => $data['state'],
            'preferred_contact_method' => $data['preferred_contact_method'],
            'status' => 'new',
            'social_handles' => [
                'instagram' => $data['instagram'] ?? null,
                'facebook' => $data['facebook'] ?? null,
                'tiktok' => $data['tiktok'] ?? null,
                'x' => $data['x_handle'] ?? null,
            ],
            'traits' => $data['traits'],
            'skills' => $data['skills'],
            'attachments' => $attachments,
            'additional_notes' => $data['previous_employment'] ?? null,
            'submitted_at' => Carbon::now(),
        ]);
    }

    private function storeEmployeeApplication(JobPost $job, array $data, Request $request): JobApplication
    {
        $attachments = [
            'picture_upload' => $this->storeUploadedFile($request->file('picture_upload'), 'employee-picture'),
            'video_upload' => $this->storeUploadedFile($request->file('video_upload'), 'employee-video'),
        ];

        if ($request->hasFile('resume')) {
            $attachments['resume'] = $this->storeUploadedFile($request->file('resume'), 'employee-resume');
        }

        return JobApplication::create([
            'job_post_id' => $job->id,
            'website_id' => $job->website_id,
            'application_type' => 'employee',
            'legal_first_name' => $data['legal_first_name'],
            'legal_last_name' => $data['legal_last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'city' => $data['city'],
            'state' => $data['state'],
            'status' => 'new',
            'social_handles' => [
                'instagram' => $data['instagram'] ?? null,
                'facebook' => $data['facebook'] ?? null,
                'tiktok' => $data['tiktok'] ?? null,
                'x' => $data['x_handle'] ?? null,
            ],
            'skills' => $data['skills'],
            'availability' => $data['availability'],
            'positions' => $data['positions'],
            'employment_history' => $data['employment_history'] ?? [],
            'education' => $data['education'] ?? [],
            'attachments' => $attachments,
            'additional_notes' => trim(
                'Heard about us: ' . $data['heard_about']
                . "\nCan contact previous employer: " . $data['contact_previous_employer']
                . "\nStart date: " . $data['start_date']
                . "\n\n" . ($data['extra_notes'] ?? '')
            ),
            'submitted_at' => Carbon::now(),
        ]);
    }

    private function validateEmployeeExperienceRequirement(Request $request, array $data): void
    {
        $history = collect($data['employment_history'] ?? [])->filter(function ($row) {
            if (!is_array($row)) {
                return false;
            }

            return filled($row['employer'] ?? null)
                || filled($row['dates'] ?? null)
                || filled($row['position'] ?? null)
                || filled($row['phone'] ?? null);
        });

        if ($history->isEmpty() && !$request->hasFile('resume')) {
            throw ValidationException::withMessages([
                'resume' => 'Please provide either previous work experience or upload a resume.',
            ]);
        }
    }

    private function storeUploadedFile($file, string $prefix): array
    {
        $directory = public_path('uploads/job-marketplace');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $name = $prefix . '_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $name);

        return [
            'name' => $file->getClientOriginalName(),
            'path' => 'uploads/job-marketplace/' . $name,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
        ];
    }
}

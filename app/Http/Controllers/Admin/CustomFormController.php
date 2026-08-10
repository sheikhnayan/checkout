<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomForm;
use App\Models\CustomFormActivityLog;
use App\Models\CustomFormSubmission;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CustomFormController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $accessibleWebsiteIds = collect($user->accessibleWebsiteIds())
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $websites = Website::query()
            ->when(!$user->isAdmin(), fn ($q) => $q->whereIn('id', $accessibleWebsiteIds))
            ->where('is_archieved', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        $query = CustomForm::query()->with(['creator', 'updater'])->withCount('submissions');

        if (!$user->isAdmin()) {
            $query->where(function ($q) use ($accessibleWebsiteIds, $user) {
                $q->where('created_by_user_id', $user->id);
                foreach ($accessibleWebsiteIds as $wId) {
                    $q->orWhereJsonContains('website_ids', (int) $wId)
                      ->orWhereJsonContains('website_ids', (string) $wId);
                }
            });
        }

        if ($request->filled('website')) {
            $selectedWebId = (int) $request->input('website');
            $query->where(function ($q) use ($selectedWebId) {
                $q->whereJsonContains('website_ids', $selectedWebId)
                  ->orWhereJsonContains('website_ids', (string) $selectedWebId);
            });
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
        }

        $forms = $query->orderByDesc('id')->paginate(15);

        return view('admin.forms.index', [
            'forms' => $forms,
            'websites' => $websites,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $accessibleWebsiteIds = collect($user->accessibleWebsiteIds())
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $websites = Website::query()
            ->when(!$user->isAdmin(), fn ($q) => $q->whereIn('id', $accessibleWebsiteIds))
            ->where('is_archieved', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.forms.builder', [
            'form' => null,
            'websites' => $websites,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website_ids' => 'nullable|array',
            'website_ids.*' => 'integer',
            'fields_schema' => 'required|json',
            'settings' => 'nullable|json',
        ]);

        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $count = 1;
        while (CustomForm::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-" . $count++;
        }

        $fieldsSchema = json_decode($validated['fields_schema'], true) ?: [];
        $settings = !empty($validated['settings']) ? (json_decode($validated['settings'], true) ?: []) : [];

        $form = CustomForm::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'website_ids' => array_map('intval', $validated['website_ids'] ?? []),
            'is_active' => true,
            'fields_schema' => $fieldsSchema,
            'settings' => $settings,
        ]);

        try {
            CustomFormActivityLog::create([
                'custom_form_id' => $form->id,
                'user_id' => $user->id,
                'action' => 'created',
                'changes_summary' => "Created form with " . count($fieldsSchema) . " fields.",
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {
            // Silence if activity log table is pending migration
        }

        return redirect()->route('admin.forms.index')->with('success', 'Form created successfully.');
    }

    public function edit(CustomForm $form)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $this->authorizeFormAccess($form, $user);

        $accessibleWebsiteIds = collect($user->accessibleWebsiteIds())
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $websites = Website::query()
            ->when(!$user->isAdmin(), fn ($q) => $q->whereIn('id', $accessibleWebsiteIds))
            ->where('is_archieved', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        $form->load(['activityLogs.user', 'creator', 'updater']);

        return view('admin.forms.builder', [
            'form' => $form,
            'websites' => $websites,
        ]);
    }

    public function update(Request $request, CustomForm $form)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $this->authorizeFormAccess($form, $user);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website_ids' => 'nullable|array',
            'website_ids.*' => 'integer',
            'fields_schema' => 'required|json',
            'settings' => 'nullable|json',
        ]);

        $oldSchema = $form->fields_schema ?: [];
        $newSchema = json_decode($validated['fields_schema'], true) ?: [];
        $newSettings = !empty($validated['settings']) ? (json_decode($validated['settings'], true) ?: []) : [];

        $oldCount = count($oldSchema);
        $newCount = count($newSchema);
        $changeDesc = "Updated form structure (fields: {$oldCount} → {$newCount}).";
        if ($form->title !== $validated['title']) {
            $changeDesc .= " Title changed from '{$form->title}' to '{$validated['title']}'.";
        }

        $form->update([
            'updated_by_user_id' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'website_ids' => array_map('intval', $validated['website_ids'] ?? []),
            'fields_schema' => $newSchema,
            'settings' => $newSettings,
        ]);

        try {
            CustomFormActivityLog::create([
                'custom_form_id' => $form->id,
                'user_id' => $user->id,
                'action' => 'updated',
                'changes_summary' => $changeDesc,
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        return redirect()->route('admin.forms.index')->with('success', 'Form updated successfully.');
    }

    public function toggleStatus(CustomForm $form, Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $this->authorizeFormAccess($form, $user);

        $form->is_active = !$form->is_active;
        $form->updated_by_user_id = $user->id;
        $form->save();

        $statusText = $form->is_active ? 'Activated' : 'Deactivated';

        try {
            CustomFormActivityLog::create([
                'custom_form_id' => $form->id,
                'user_id' => $user->id,
                'action' => 'toggled',
                'changes_summary' => "{$statusText} form status.",
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        return back()->with('success', "Form status updated to {$statusText}.");
    }

    public function destroy(CustomForm $form, Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $this->authorizeFormAccess($form, $user);

        $formTitle = $form->title;
        $form->delete();

        return back()->with('success', "Form '{$formTitle}' deleted successfully.");
    }

    public function submissions(CustomForm $form, Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $this->authorizeFormAccess($form, $user);

        $query = CustomFormSubmission::query()
            ->where('custom_form_id', $form->id)
            ->with('website');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where('submission_data', 'like', "%{$search}%");
        }

        $submissions = $query->orderByDesc('id')->paginate(25);

        return view('admin.forms.submissions', [
            'form' => $form,
            'submissions' => $submissions,
        ]);
    }

    public function exportSubmissions(CustomForm $form, Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $this->authorizeFormAccess($form, $user);

        $submissions = CustomFormSubmission::query()
            ->where('custom_form_id', $form->id)
            ->orderByDesc('id')
            ->get();

        $fieldsSchema = $form->fields_schema ?: [];
        $headers = ['Submission ID', 'Submitted At', 'Club / Website', 'IP Address'];
        $fieldKeys = [];

        foreach ($fieldsSchema as $f) {
            if (($f['type'] ?? '') === 'heading' || ($f['type'] ?? '') === 'paragraph') {
                continue;
            }
            $key = $f['name'] ?? $f['id'] ?? null;
            if ($key) {
                $fieldKeys[] = $key;
                $headers[] = $f['label'] ?? $key;
            }
        }

        $lines = [];
        $lines[] = implode(',', array_map(fn($h) => '"' . str_replace('"', '""', $h) . '"', $headers));

        foreach ($submissions as $sub) {
            $data = $sub->submission_data ?: [];
            $row = [
                $sub->id,
                $sub->created_at ? $sub->created_at->format('Y-m-d H:i:s') : '',
                $sub->website ? $sub->website->name : 'N/A',
                $sub->submitter_ip ?? '',
            ];

            foreach ($fieldKeys as $fk) {
                $val = $data[$fk] ?? '';
                if (is_array($val)) {
                    $val = implode(', ', $val);
                }
                $row[] = (string) $val;
            }

            $lines[] = implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row));
        }

        $filename = 'form_submissions_' . Str::slug($form->title) . '_' . date('Y-m-d') . '.csv';

        return response(implode("\n", $lines))
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // Public Form Methods
    public function showPublic(string $slug)
    {
        $form = CustomForm::where('slug', $slug)->firstOrFail();

        if (!$form->is_active) {
            return response()->view('errors.404', [], 404);
        }

        $targetWebsites = [];
        if (!empty($form->website_ids)) {
            $targetWebsites = Website::whereIn('id', $form->website_ids)->get(['id', 'name']);
        }

        // Dynamic CAPTCHA Generation
        $num1 = rand(2, 18);
        $num2 = rand(2, 18);
        $captchaQuestion = "{$num1} + {$num2}";
        session(['captcha_answer_' . $form->id => ($num1 + $num2)]);

        return view('forms.public_show', [
            'form' => $form,
            'targetWebsites' => $targetWebsites,
            'captchaQuestion' => $captchaQuestion,
        ]);
    }

    public function submitPublic(string $slug, Request $request)
    {
        $form = CustomForm::where('slug', $slug)->firstOrFail();

        if (!$form->is_active) {
            return response()->json(['error' => 'Form is not active.'], 400);
        }

        $fieldsSchema = $form->fields_schema ?: [];

        // Validate Dynamic CAPTCHA
        foreach ($fieldsSchema as $f) {
            if (($f['type'] ?? '') === 'captcha') {
                $key = $f['name'] ?? $f['id'] ?? null;
                if ($key) {
                    $userAnswer = trim((string) $request->input($key));
                    $expectedAnswer = session('captcha_answer_' . $form->id);
                    if ($expectedAnswer === null || (int)$userAnswer !== (int)$expectedAnswer) {
                        return back()->withErrors([$key => 'CAPTCHA math answer is incorrect. Please try again.'])->withInput();
                    }
                }
            }
        }

        $rules = [];
        $customAttributes = [];
        $submissionData = [];

        foreach ($fieldsSchema as $f) {
            $type = $f['type'] ?? 'text';
            if ($type === 'heading' || $type === 'paragraph') {
                continue;
            }

            $key = $f['name'] ?? $f['id'] ?? null;
            if (!$key) {
                continue;
            }

            $label = $f['label'] ?? $key;
            $customAttributes[$key] = $label;

            $fieldRules = [];
            if (!empty($f['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            if ($type === 'email') {
                $fieldRules[] = 'email';
            } elseif ($type === 'number') {
                $fieldRules[] = 'numeric';
            } elseif ($type === 'file') {
                $allowedExt = !empty($f['allowed_extensions']) ? array_filter(array_map('trim', explode(',', strtolower($f['allowed_extensions'])))) : ['pdf', 'doc', 'docx', 'png', 'jpg'];
                $allowedExtStr = implode(',', $allowedExt);
                $maxMb = !empty($f['max_file_size']) ? (int) $f['max_file_size'] : 5;
                $maxKb = $maxMb * 1024;
                $maxCount = !empty($f['max_file_uploads']) ? (int) $f['max_file_uploads'] : 1;

                if ($maxCount > 1 || ($request->hasFile($key) && is_array($request->file($key)))) {
                    if (!empty($f['required'])) {
                        $rules[$key] = 'required';
                    } else {
                        $rules[$key] = 'nullable';
                    }
                    $itemRules = ['file', 'max:' . $maxKb];
                    if (!empty($allowedExtStr)) {
                        $itemRules[] = 'mimes:' . $allowedExtStr;
                    }
                    $rules["{$key}.*"] = implode('|', $itemRules);
                    $customAttributes["{$key}.*"] = $label;
                } else {
                    $fileRules = [];
                    if (!empty($f['required'])) {
                        $fileRules[] = 'required';
                    } else {
                        $fileRules[] = 'nullable';
                    }
                    $fileRules[] = 'file';
                    if (!empty($allowedExtStr)) {
                        $fileRules[] = 'mimes:' . $allowedExtStr;
                    }
                    $fileRules[] = 'max:' . $maxKb;
                    $rules[$key] = implode('|', $fileRules);
                }
            } else {
                if (!empty($fieldRules)) {
                    $rules[$key] = implode('|', $fieldRules);
                }
            }

            if ($request->hasFile($key)) {
                $files = $request->file($key);
                if (is_array($files)) {
                    $urls = [];
                    foreach ($files as $file) {
                        if ($file && $file->isValid()) {
                            $path = $file->store('form_uploads', 'public');
                            $urls[] = Storage::url($path);
                        }
                    }
                    $submissionData[$key] = implode(', ', $urls);
                } else {
                    if ($files && $files->isValid()) {
                        $path = $files->store('form_uploads', 'public');
                        $submissionData[$key] = Storage::url($path);
                    }
                }
            } else {
                $submissionData[$key] = $request->input($key);
            }
        }

        if (!empty($rules)) {
            $request->validate($rules, [], $customAttributes);
        }

        $submission = CustomFormSubmission::create([
            'custom_form_id' => $form->id,
            'website_id' => $request->input('website_id') ? (int) $request->input('website_id') : null,
            'submitter_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'submission_data' => $submissionData,
        ]);

        $settings = $form->settings ?: [];
        $confirmationSettings = $settings['confirmation'] ?? [];
        $confType = $confirmationSettings['type'] ?? 'message';
        $redirectUrl = $confirmationSettings['redirect_url'] ?? null;
        $successMessage = !empty($confirmationSettings['message']) ? $confirmationSettings['message'] : ($settings['success_message'] ?? 'Thanks for contacting us! We will be in touch with you shortly.');

        if ($confType === 'redirect' && !empty($redirectUrl)) {
            return redirect()->away($redirectUrl);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
            ]);
        }

        return back()->with('form_success', $successMessage);
    }

    private function authorizeFormAccess(CustomForm $form, $user): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ((int) $form->created_by_user_id === (int) $user->id) {
            return;
        }

        $accessibleWebsiteIds = collect($user->accessibleWebsiteIds())->map(fn ($id) => (int) $id)->all();
        $formWebsites = array_map('intval', $form->website_ids ?: []);

        if (array_intersect($accessibleWebsiteIds, $formWebsites)) {
            return;
        }

        abort(403, 'Unauthorized form access.');
    }
}

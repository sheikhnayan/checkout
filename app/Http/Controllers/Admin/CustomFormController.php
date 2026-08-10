<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomForm;
use App\Models\CustomFormActivityLog;
use App\Models\CustomFormSubmission;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
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
            if (($f['type'] ?? '') === 'heading' || ($f['type'] ?? '') === 'paragraph' || ($f['type'] ?? '') === 'captcha') {
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

        $settings = $form->settings ?: [];
        $spamSettings = $settings['spam'] ?? [];

        // 1. Honeypot Anti-Spam Check
        if (!empty($spamSettings['enable_antispam'])) {
            if (!empty($request->input('_hp_security_check'))) {
                Log::warning("Honeypot caught spam submission on form #{$form->id} IP: {$request->ip()}");
                return back()->withErrors(['form' => 'Spam detected. Submission rejected.'])->withInput();
            }
        }

        // 2. Minimum Time to Submit Check
        if (!empty($spamSettings['min_time'])) {
            $renderTime = (int) $request->input('_form_render_timestamp');
            $minSeconds = !empty($spamSettings['min_time_seconds']) ? (int) $spamSettings['min_time_seconds'] : 3;
            if ($renderTime > 0 && (time() - $renderTime) < $minSeconds) {
                return back()->withErrors(['form' => "Form submitted too quickly (minimum {$minSeconds} seconds required). Please take a moment and try again."])->withInput();
            }
        }

        // 3. Country Filter Check
        if (!empty($spamSettings['country_filter']) && !empty($spamSettings['restricted_countries'])) {
            $restrictedCountries = array_filter(array_map('trim', explode(',', strtoupper($spamSettings['restricted_countries']))));
            if (!empty($restrictedCountries)) {
                $userCountry = strtoupper(
                    $request->header('CF-IPCountry') ?: 
                    $request->header('X-Country') ?: 
                    $request->header('GEOIP_COUNTRY_CODE') ?: ''
                );
                if (!empty($userCountry) && in_array($userCountry, $restrictedCountries)) {
                    return back()->withErrors(['form' => "Submissions from your location ({$userCountry}) are restricted for this form."])->withInput();
                }
            }
        }

        $fieldsSchema = $form->fields_schema ?: [];

        // 4. Validate Dynamic CAPTCHA
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
            if ($type === 'heading' || $type === 'paragraph' || $type === 'captcha') {
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
                $inputVal = $request->input($key);
                if (is_array($inputVal)) {
                    $submissionData[$key] = implode(' ', array_filter(array_map('trim', $inputVal)));
                } else {
                    $submissionData[$key] = $inputVal;
                }
            }
        }

        if (!empty($rules)) {
            $request->validate($rules, [], $customAttributes);
        }

        // 5. Keyword Filter Check (Scan submitted data for blacklisted words/links)
        if (!empty($spamSettings['keyword_filter']) && !empty($spamSettings['restricted_keywords'])) {
            $keywords = array_filter(array_map('trim', explode(',', strtolower($spamSettings['restricted_keywords']))));
            if (!empty($keywords)) {
                foreach ($submissionData as $k => $val) {
                    $valStr = strtolower(is_array($val) ? json_encode($val) : (string) $val);
                    foreach ($keywords as $kw) {
                        if (!empty($kw) && str_contains($valStr, $kw)) {
                            $fieldLabel = $customAttributes[$k] ?? $k;
                            return back()->withErrors([$k => "The {$fieldLabel} field contains a restricted word or link: \"{$kw}\"."])->withInput();
                        }
                    }
                }
            }
        }

        $submission = CustomFormSubmission::create([
            'custom_form_id' => $form->id,
            'website_id' => $request->input('website_id') ? (int) $request->input('website_id') : null,
            'submitter_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'submission_data' => $submissionData,
        ]);

        // 6. Send Email Notification
        $notifySettings = $settings['notifications'] ?? [];
        if (!empty($notifySettings['enabled']) && !empty($notifySettings['send_to'])) {
            try {
                $toEmail = $notifySettings['send_to'];
                $subject = !empty($notifySettings['subject']) ? str_replace('{form_title}', $form->title, $notifySettings['subject']) : "New Form Submission: {$form->title}";
                $fromName = !empty($notifySettings['from_name']) ? $notifySettings['from_name'] : "CartVIP Forms";
                $fromEmail = !empty($notifySettings['from_email']) ? $notifySettings['from_email'] : (config('mail.from.address') ?: 'no-reply@cartvip.com');

                $tableHtml = "<h3 style='font-family:sans-serif;color:#0f172a;'>New Submission Received: " . htmlspecialchars($form->title) . "</h3>";
                $tableHtml .= "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse:collapse;width:100%;font-family:sans-serif;border-color:#e2e8f0;'>";
                $tableHtml .= "<tr style='background:#f8fafc;color:#0f172a;'><th>Field</th><th>Value</th></tr>";

                foreach ($fieldsSchema as $f) {
                    $key = $f['name'] ?? $f['id'] ?? null;
                    if (!$key || ($f['type'] ?? '') === 'heading') continue;
                    $label = $f['label'] ?? $key;
                    $val = $submissionData[$key] ?? '-';
                    if (is_array($val)) $val = implode(', ', $val);
                    $tableHtml .= "<tr><td style='width:35%;'><strong>" . htmlspecialchars($label) . "</strong></td><td>" . nl2br(htmlspecialchars((string)$val)) . "</td></tr>";
                }
                $tableHtml .= "</table>";
                $tableHtml .= "<p style='font-size:12px;color:#64748b;margin-top:20px;'>Submitted on " . now()->format('M d, Y h:i A') . " | IP: {$request->ip()}</p>";

                $customTemplate = !empty($notifySettings['message']) ? $notifySettings['message'] : '{all_fields}';
                $finalBody = str_replace('{all_fields}', $tableHtml, $customTemplate);

                Mail::html($finalBody, function ($message) use ($toEmail, $subject, $fromName, $fromEmail) {
                    $message->to($toEmail)
                            ->subject($subject)
                            ->from($fromEmail, $fromName);
                });
            } catch (\Exception $e) {
                Log::error("Form Notification Email Exception: " . $e->getMessage());
            }
        }

        // 7. Confirmation Response
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

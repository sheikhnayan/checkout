<?php

namespace App\Http\Controllers;

use App\Mail\EntertainerApplicationReceivedMail;
use App\Models\Entertainer;
use App\Models\SMTP;
use App\Models\User;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EntertainerRegistrationController extends Controller
{
    public function showForm(Request $request)
    {
        $clubSlug = trim((string) $request->query('club', ''));
        $selectedClub = $clubSlug !== ''
            ? Website::where('slug', $clubSlug)->where('status', 1)->where('is_archieved', 0)->first()
            : null;

        $clubs = Website::where('status', 1)
            ->where('is_archieved', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('entertainer.apply', compact('clubs', 'selectedClub'));
    }

    public function submit(Request $request)
    {
        // ========== BOT PREVENTION - LAYER 1: reCAPTCHA v3 (REQUIRED) ==========
        $recaptchaToken = $request->input('recaptcha_token');
        $hasRecaptchaConfig = config('services.recaptcha.secret_key') && config('services.recaptcha.secret_key') !== 'YOUR_RECAPTCHA_SECRET_KEY_HERE';

        if ($hasRecaptchaConfig) {
            // reCAPTCHA is configured - token is REQUIRED
            if (!$recaptchaToken) {
                \Log::warning('Entertainer registration rejected - no reCAPTCHA token provided', [
                    'ip' => $request->ip(),
                    'email' => $request->input('email')
                ]);
                return redirect()->back()
                    ->with('error', 'Bot verification failed. Please try again.')
                    ->withInput();
            }

            $recaptchaService = new \App\Services\RecaptchaService();
            $recaptchaResult = $recaptchaService->verify($recaptchaToken);

            if (!$recaptchaResult['success']) {
                \Log::warning('Entertainer registration blocked by reCAPTCHA', [
                    'score' => $recaptchaResult['score'],
                    'ip' => $request->ip(),
                    'email' => $request->input('email'),
                    'threshold' => config('services.recaptcha.threshold')
                ]);
                // Block the submission if reCAPTCHA score is too low (bot detected)
                return redirect()->back()
                    ->with('error', 'Bot verification failed. Please try again.')
                    ->withInput();
            }
        }

        // ========== BOT PREVENTION - LAYER 3: Server-Side Validation ==========
        $validationData = [
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'name' => $request->input('name'),
            'form_load_time' => $request->input('form_load_time'),
        ];

        $validationResult = \App\Services\FormValidationService::validateEntertainerRegistration($validationData, $request->ip());
        if (!$validationResult['valid']) {
            \Log::warning('Entertainer registration rejected by server validation', [
                'errors' => $validationResult['errors'],
                'ip' => $request->ip(),
                'email' => $request->input('email')
            ]);
            return redirect()->back()
                ->with('error', 'Registration validation failed: ' . implode(', ', $validationResult['errors']))
                ->withInput();
        }

        $clubSlug = trim((string) $request->input('club_slug', $request->query('club', '')));
        $selectedClub = $clubSlug !== ''
            ? Website::where('slug', $clubSlug)->where('status', 1)->where('is_archieved', 0)->first()
            : null;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:30',
            'experience' => 'nullable|string|max:1000',
        ];

        if (!$selectedClub) {
            $rules['website_id'] = 'required|integer|exists:websites,id';
        }

        $validated = $request->validate($rules);

        $clubId = $selectedClub
            ? (int) $selectedClub->id
            : (int) $validated['website_id'];

        $club = Website::where('id', $clubId)
            ->where('status', 1)
            ->where('is_archieved', 0)
            ->firstOrFail();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'website_id' => $club->id,
            'user_type' => 'entertainer',
        ]);

        $entertainer = Entertainer::create([
            'user_id' => $user->id,
            'website_id' => $club->id,
            'status' => 'pending',
            'slug' => Entertainer::generateUniqueSlug($validated['name']),
            'display_name' => $validated['name'],
            'description' => $validated['experience'] ?? null,
        ]);

        // Create W9Form record
        $w9Form = \App\Models\W9Form::create([
            'entertainer_id' => $entertainer->id,
            'type' => 'entertainer',
            'full_name' => $user->name,
            'status' => 'pending',
        ]);

        // Generate W-9 form link token
        $token = base64_encode(json_encode(['type' => 'entertainer', 'id' => $entertainer->id]));
        $formUrl = route('w9.show', $token, true);

        try {
            $this->applyGlobalSmtp();
            Mail::to($user->email)->send(new EntertainerApplicationReceivedMail($entertainer));

            // Send W-9 form email
            Mail::to($user->email)->send(new \App\Mail\W9FormLink($formUrl, $user->name, 'entertainer'));

            $submissionRecipients = collect((array) ($club->entertainer_submission_emails ?? []))
                ->map(function ($entry) {
                    if (!is_array($entry)) {
                        return null;
                    }

                    $email = strtolower(trim((string) ($entry['email'] ?? '')));
                    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
                })
                ->filter()
                ->unique()
                ->values();

            if ($submissionRecipients->isNotEmpty()) {
                foreach ($submissionRecipients as $recipientEmail) {
                    Mail::to($recipientEmail)->send(new \App\Mail\AdminApplicationNotificationMail(
                        'Entertainer',
                        'Club Application',
                        $user->name,
                        $user->email,
                        $club->name,
                        $request->input('phone'),
                        "Experience: " . ($request->input('experience') ? substr($request->input('experience'), 0, 100) . '...' : 'Not provided')
                    ));
                }
            } else {
                $adminEmails = User::where('user_type', 'admin')->pluck('email')->filter()->toArray();
                $adminEmails[] = 'hello@cartvip.com';
                $adminEmails = array_unique($adminEmails);

                foreach ($adminEmails as $adminEmail) {
                    Mail::to($adminEmail)->send(new \App\Mail\AdminApplicationNotificationMail(
                        'Entertainer',
                        'Public Registration',
                        $user->name,
                        $user->email,
                        $club->name,
                        $request->input('phone'),
                        "Experience: " . ($request->input('experience') ? substr($request->input('experience'), 0, 100) . '...' : 'Not provided')
                    ));
                }

                $websiteUserEmails = User::where('user_type', 'website_user')
                    ->where('website_id', $club->id)
                    ->pluck('email')
                    ->filter()
                    ->toArray();

                foreach ($websiteUserEmails as $websiteUserEmail) {
                    Mail::to($websiteUserEmail)->send(new \App\Mail\AdminApplicationNotificationMail(
                        'Entertainer',
                        'Club Application',
                        $user->name,
                        $user->email,
                        $club->name,
                        $request->input('phone'),
                        "Experience: " . ($request->input('experience') ? substr($request->input('experience'), 0, 100) . '...' : 'Not provided')
                    ));
                }
            }
        } catch (\Throwable $th) {
            // Keep registration successful even if mail fails.
        }

        return redirect()->route('login')->with('success', 'Your entertainer application has been submitted successfully. A confirmation email with instructions to complete your W-9 form has been sent to your email address. Please complete the W-9 form as soon as possible to expedite your account activation. We will review your application and notify you once approved.');
    }

    private function applyGlobalSmtp(): void
    {
        $smtp = SMTP::latest()->first();
        if (!$smtp || empty($smtp->host) || empty($smtp->port) || empty($smtp->username) || empty($smtp->password)) {
            return;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $smtp->host,
            'mail.mailers.smtp.port' => $smtp->port,
            'mail.mailers.smtp.username' => $smtp->username,
            'mail.mailers.smtp.password' => $smtp->password,
            'mail.mailers.smtp.encryption' => in_array($smtp->encryption, ['tls', 'ssl'], true) ? $smtp->encryption : ((string) $smtp->encryption === '1' ? 'tls' : null),
            'mail.from.address' => $smtp->from_email ?: config('mail.from.address'),
            'mail.from.name' => $smtp->from_name ?: config('mail.from.name'),
        ]);
    }
}

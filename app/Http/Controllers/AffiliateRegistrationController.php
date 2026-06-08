<?php

namespace App\Http\Controllers;

use App\Mail\AffiliateApplicationReceivedMail;
use App\Models\Affiliate;
use App\Models\SMTP;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AffiliateRegistrationController extends Controller
{
    public function showForm()
    {
        return view('affiliate.apply');
    }

    public function submit(Request $request)
    {
        // ========== BOT PREVENTION - LAYER 1: reCAPTCHA v3 (OPTIONAL) ==========
        $recaptchaToken = $request->input('recaptcha_token');
        if ($recaptchaToken && config('services.recaptcha.secret_key') && config('services.recaptcha.secret_key') !== 'YOUR_RECAPTCHA_SECRET_KEY_HERE') {
            $recaptchaService = new \App\Services\RecaptchaService();
            $recaptchaResult = $recaptchaService->verify($recaptchaToken);

            if (!$recaptchaResult['success']) {
                \Log::warning('Affiliate registration blocked by reCAPTCHA', [
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

        $validationResult = \App\Services\FormValidationService::validateAffiliateRegistration($validationData, $request->ip());
        if (!$validationResult['valid']) {
            \Log::warning('Affiliate registration rejected by server validation', [
                'errors' => $validationResult['errors'],
                'ip' => $request->ip(),
                'email' => $request->input('email')
            ]);
            return redirect()->back()
                ->with('error', 'Registration validation failed: ' . implode(', ', $validationResult['errors']))
                ->withInput();
        }

        // Standard Laravel validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:30',
            'experience' => 'nullable|string|max:1000',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'affiliate',
        ]);

        $affiliate = Affiliate::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'slug' => Affiliate::generateUniqueSlug($request->name),
            'display_name' => $request->name,
            'description' => $request->experience,
        ]);

        // Create W9Form record
        $w9Form = \App\Models\W9Form::create([
            'affiliate_id' => $affiliate->id,
            'type' => 'affiliate',
            'full_name' => $user->name,
            'status' => 'pending',
        ]);

        // Generate W-9 form link token
        $token = base64_encode(json_encode(['type' => 'affiliate', 'id' => $affiliate->id]));
        $formUrl = route('w9.show', $token, true);

        try {
            $this->applyGlobalSmtp();
            Mail::to($user->email)->send(new AffiliateApplicationReceivedMail($affiliate));

            // Send W-9 form email
            Mail::to($user->email)->send(new \App\Mail\W9FormLink($formUrl, $user->name, 'affiliate'));

            $adminEmails = User::where('user_type', 'admin')->pluck('email')->filter()->toArray();
            foreach ($adminEmails as $adminEmail) {
                Mail::raw(
                    'New affiliate application received from ' . $user->name . ' (' . $user->email . ').',
                    function ($message) use ($adminEmail) {
                        $message->to($adminEmail)->subject('New affiliate Application - CartVIP');
                    }
                );
            }
        } catch (\Throwable $th) {
            Log::error('Affiliate registration email failed: ' . $th->getMessage(), [
                'email' => $user->email,
                'exception' => (string) $th,
            ]);
        }

        return redirect()->route('login')->with('success', 'Your affiliate application has been submitted successfully. A confirmation email with instructions to complete your W-9 form has been sent to your email address. Please complete the W-9 form as soon as possible to expedite your account activation. We will review your application and notify you once approved.');
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

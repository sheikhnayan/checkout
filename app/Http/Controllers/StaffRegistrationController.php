<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Affiliate;
use App\Models\Entertainer;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StaffRegistrationController extends Controller
{
    public function showForm()
    {
        $websites = Website::where('status', 1)->where('is_archieved', 0)->get();
        return view('auth.staff-apply', ['websites' => $websites]);
    }

    public function submit(Request $request)
    {
        // ========== BOT PREVENTION - LAYER 1: reCAPTCHA v3 (OPTIONAL) ==========
        $recaptchaToken = $request->input('recaptcha_token');
        if ($recaptchaToken && config('services.recaptcha.secret_key') && config('services.recaptcha.secret_key') !== 'YOUR_RECAPTCHA_SECRET_KEY_HERE') {
            $recaptchaService = new \App\Services\RecaptchaService();
            $recaptchaResult = $recaptchaService->verify($recaptchaToken);

            if (!$recaptchaResult['success']) {
                \Log::info('Staff registration reCAPTCHA score low', [
                    'score' => $recaptchaResult['score'],
                    'ip' => $request->ip(),
                    'email' => $request->input('email')
                ]);
            }
        }

        // ========== BOT PREVENTION - LAYER 3: Server-Side Validation ==========
        $validationData = [
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'name' => $request->input('name'),
            'form_load_time' => $request->input('form_load_time'),
        ];

        $validationResult = \App\Services\FormValidationService::validateSubmission($validationData, $request->ip(), 'staff_registration');
        if (!$validationResult['valid']) {
            \Log::warning('Staff registration rejected by server validation', [
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
            'website_id' => 'required|integer|exists:websites,id',
            'staff_type' => 'required|in:affiliate,entertainer',
        ]);

        $website = Website::where('id', $request->website_id)
            ->where('status', 1)
            ->where('is_archieved', 0)
            ->firstOrFail();

        $staffType = $request->staff_type === 'affiliate' ? 'affiliate' : 'entertainer';
        $userType = $staffType === 'affiliate' ? 'affiliate' : 'entertainer';

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $userType,
        ]);

        // Create affiliate or entertainer record (marked as staff registration)
        if ($staffType === 'affiliate') {
            $staff = Affiliate::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'slug' => Affiliate::generateUniqueSlug($request->name),
                'display_name' => $request->name,
                'is_staff_registration' => true,
            ]);
        } else {
            $staff = Entertainer::create([
                'user_id' => $user->id,
                'website_id' => $website->id,
                'status' => 'pending',
                'slug' => Entertainer::generateUniqueSlug($request->name),
                'display_name' => $request->name,
                'is_staff_registration' => true,
            ]);
        }

        // Send registration confirmation email (NO W-9 form needed)
        try {
            $this->applyGlobalSmtp();

            if ($staffType === 'affiliate') {
                Mail::to($user->email)->send(new \App\Mail\AffiliateApplicationReceivedMail($staff));
            } else {
                Mail::to($user->email)->send(new \App\Mail\EntertainerApplicationReceivedMail($staff));
            }

            // Notify admins
            $adminEmails = User::where('user_type', 'admin')->pluck('email')->filter()->toArray();
            foreach ($adminEmails as $adminEmail) {
                $staffTypeLabel = $staffType === 'affiliate' ? 'Promoter' : 'Entertainer';
                Mail::raw(
                    "New {$staffTypeLabel} staff registration received from {$user->name} ({$user->email}) at {$website->name}.",
                    function ($message) use ($adminEmail) {
                        $message->to($adminEmail)->subject('New Staff Registration - CartVIP');
                    }
                );
            }
        } catch (\Throwable $th) {
            Log::error('Staff registration email failed: ' . $th->getMessage(), [
                'email' => $user->email,
                'exception' => (string) $th,
            ]);
        }

        // Success message (NO W-9 form mention)
        $staffTypeLabel = $staffType === 'affiliate' ? 'Promoter' : 'Entertainer';
        return redirect()->route('login')
            ->with('success', "Your {$staffTypeLabel} staff registration has been submitted successfully. A confirmation email has been sent to {$user->email}. We will review your application and notify you once approved.");
    }

    private function applyGlobalSmtp()
    {
        $setting = \App\Models\Setting::where('id', 1)->first();
        if ($setting) {
            config([
                'mail.mailers.smtp.host' => $setting->smtp_host ?? config('mail.mailers.smtp.host'),
                'mail.mailers.smtp.port' => $setting->smtp_port ?? config('mail.mailers.smtp.port'),
                'mail.mailers.smtp.username' => $setting->smtp_username ?? config('mail.mailers.smtp.username'),
                'mail.mailers.smtp.password' => $setting->smtp_password ?? config('mail.mailers.smtp.password'),
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Mail\AffiliateApplicationReceivedMail;
use App\Models\Affiliate;
use App\Models\SMTP;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AffiliateRegistrationController extends Controller
{
    public function showForm()
    {
        return view('affiliate.apply');
    }

    public function submit(Request $request)
    {
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

        try {
            $this->applyGlobalSmtp();
            Mail::to($user->email)->send(new AffiliateApplicationReceivedMail($affiliate));

            $adminEmails = User::where('user_type', 'admin')->pluck('email')->filter()->toArray();
            foreach ($adminEmails as $adminEmail) {
                Mail::raw(
                    'New affiliate application received from ' . $user->name . ' (' . $user->email . ').',
                    function ($message) use ($adminEmail) {
                        $message->to($adminEmail)->subject('New Affiliate Application - CartVIP');
                    }
                );
            }
        } catch (\Throwable $th) {
            // Keep registration successful even if mail fails.
        }

        return redirect()->route('login')->with('success', 'Your affiliate application has been submitted successfully. We will review your application and notify you once approved.');
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

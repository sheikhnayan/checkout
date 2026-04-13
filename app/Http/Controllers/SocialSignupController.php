<?php

namespace App\Http\Controllers;

use App\Mail\AffiliateApplicationReceivedMail;
use App\Mail\EntertainerApplicationReceivedMail;
use App\Models\Affiliate;
use App\Models\Entertainer;
use App\Models\SMTP;
use App\Models\User;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialSignupController extends Controller
{
    private const ALLOWED_ROLES = ['affiliate', 'entertainer'];
    private const ALLOWED_PROVIDERS = ['google', 'facebook'];

    public function redirect(Request $request, string $role, string $provider)
    {
        $role = strtolower(trim($role));
        $provider = strtolower(trim($provider));

        if (!in_array($role, self::ALLOWED_ROLES, true) || !in_array($provider, self::ALLOWED_PROVIDERS, true)) {
            abort(404);
        }

        if ($role === 'entertainer') {
            $clubSlug = trim((string) $request->query('club', ''));
            $websiteId = (int) $request->query('website_id', 0);

            $club = null;
            if ($clubSlug !== '') {
                $club = Website::where('slug', $clubSlug)->where('status', 1)->where('is_archieved', 0)->first();
            } elseif ($websiteId > 0) {
                $club = Website::where('id', $websiteId)->where('status', 1)->where('is_archieved', 0)->first();
            }

            if (!$club) {
                return redirect()->route('entertainer.apply')->withErrors([
                    'website_id' => 'Please select a club before using social signup.',
                ])->withInput();
            }

            session([
                'social_signup.website_id' => (int) $club->id,
                'social_signup.club_slug' => (string) $club->slug,
            ]);
        }

        session([
            'social_signup.role' => $role,
            'social_signup.provider' => $provider,
        ]);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $role, string $provider)
    {
        $role = strtolower(trim($role));
        $provider = strtolower(trim($provider));

        if (!in_array($role, self::ALLOWED_ROLES, true) || !in_array($provider, self::ALLOWED_PROVIDERS, true)) {
            abort(404);
        }

        $sessionRole = (string) session('social_signup.role');
        $sessionProvider = (string) session('social_signup.provider');

        if ($sessionRole !== $role || $sessionProvider !== $provider) {
            return redirect()->route($role === 'affiliate' ? 'affiliate.apply' : 'entertainer.apply')
                ->withErrors(['email' => 'Social signup session expired. Please try again.']);
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Throwable $e) {
            return redirect()->route($role === 'affiliate' ? 'affiliate.apply' : 'entertainer.apply')
                ->withErrors(['email' => 'Unable to authenticate with ' . ucfirst($provider) . '. Please try again.']);
        }

        $email = strtolower(trim((string) $socialUser->getEmail()));
        if ($email === '') {
            return redirect()->route($role === 'affiliate' ? 'affiliate.apply' : 'entertainer.apply')
                ->withErrors(['email' => 'Your social account does not provide an email address.']);
        }

        $name = trim((string) ($socialUser->getName() ?: $socialUser->getNickname() ?: 'User'));
        if ($name === '') {
            $name = 'User';
        }

        $providerId = trim((string) $socialUser->getId());
        $avatarUrl = trim((string) ($socialUser->getAvatar() ?: ''));

        $user = User::where('oauth_provider', $provider)
            ->where('oauth_provider_id', $providerId)
            ->first();

        if (!$user) {
            $user = User::where('email', $email)->first();
        }

        if ($user && $user->user_type !== $role) {
            return redirect()->route('login')->withErrors([
                'email' => 'This email is already registered for a different account type.',
            ]);
        }

        $selectedWebsiteId = null;
        if ($role === 'entertainer') {
            $selectedWebsiteId = (int) session('social_signup.website_id', 0);
            if ($selectedWebsiteId <= 0) {
                return redirect()->route('entertainer.apply')->withErrors([
                    'website_id' => 'Please select a club before using social signup.',
                ]);
            }
        }

        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
                'user_type' => $role,
                'website_id' => $selectedWebsiteId,
                'oauth_provider' => $provider,
                'oauth_provider_id' => $providerId,
                'avatar_url' => $avatarUrl !== '' ? $avatarUrl : null,
                'email_verified_at' => now(),
            ]);
        } else {
            $dirty = false;

            if ($user->name !== $name && $user->name === 'User') {
                $user->name = $name;
                $dirty = true;
            }

            if (empty($user->oauth_provider) || empty($user->oauth_provider_id)) {
                $user->oauth_provider = $provider;
                $user->oauth_provider_id = $providerId;
                $dirty = true;
            }

            if ($avatarUrl !== '' && empty($user->avatar_url)) {
                $user->avatar_url = $avatarUrl;
                $dirty = true;
            }

            if ($role === 'entertainer' && !$user->website_id && $selectedWebsiteId) {
                $user->website_id = $selectedWebsiteId;
                $dirty = true;
            }

            if ($dirty) {
                $user->save();
            }
        }

        if ($role === 'affiliate') {
            $affiliate = Affiliate::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'status' => 'pending',
                    'slug' => Affiliate::generateUniqueSlug($user->name),
                    'display_name' => $user->name,
                ]
            );

            if ($affiliate->wasRecentlyCreated) {
                $this->notifyAffiliateApplication($affiliate, $user);
            }

            $this->clearSession();

            return redirect()->route('login')->with('success', 'Affiliate application submitted via ' . ucfirst($provider) . '. We will notify you once approved.');
        }

        $club = Website::where('id', $selectedWebsiteId)->where('status', 1)->where('is_archieved', 0)->first();
        if (!$club) {
            $this->clearSession();

            return redirect()->route('entertainer.apply')->withErrors([
                'website_id' => 'Selected club is not available right now.',
            ]);
        }

        $entertainer = Entertainer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'website_id' => $club->id,
                'status' => 'pending',
                'slug' => Entertainer::generateUniqueSlug($user->name),
                'display_name' => $user->name,
            ]
        );

        if ($entertainer->wasRecentlyCreated) {
            $this->notifyEntertainerApplication($entertainer, $user, $club);
        }

        $this->clearSession();

        return redirect()->route('login')->with('success', 'Entertainer application submitted via ' . ucfirst($provider) . '. We will notify you once approved.');
    }

    private function notifyAffiliateApplication(Affiliate $affiliate, User $user): void
    {
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
        } catch (\Throwable $e) {
            // Keep signup successful even if mail fails.
        }
    }

    private function notifyEntertainerApplication(Entertainer $entertainer, User $user, Website $club): void
    {
        try {
            $this->applyGlobalSmtp();
            Mail::to($user->email)->send(new EntertainerApplicationReceivedMail($entertainer));

            $adminEmails = User::where('user_type', 'admin')->pluck('email')->filter()->toArray();
            foreach ($adminEmails as $adminEmail) {
                Mail::raw(
                    'New entertainer application received from ' . $user->name . ' (' . $user->email . ') for club ' . $club->name . '.',
                    function ($message) use ($adminEmail) {
                        $message->to($adminEmail)->subject('New Entertainer Application - CartVIP');
                    }
                );
            }

            $websiteUserEmails = User::where('user_type', 'website_user')
                ->where('website_id', $club->id)
                ->pluck('email')
                ->filter()
                ->toArray();

            foreach ($websiteUserEmails as $websiteUserEmail) {
                Mail::raw(
                    'A new entertainer application was submitted for your club: ' . $club->name . '.',
                    function ($message) use ($websiteUserEmail) {
                        $message->to($websiteUserEmail)->subject('New Entertainer Application - Club Notification');
                    }
                );
            }
        } catch (\Throwable $e) {
            // Keep signup successful even if mail fails.
        }
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

    private function clearSession(): void
    {
        session()->forget([
            'social_signup.role',
            'social_signup.provider',
            'social_signup.website_id',
            'social_signup.club_slug',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByUserType(Auth::user());
        }
        
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        $remember = $request->filled('remember');

        // An email may now belong to more than one website-admin account.
        $users = User::where('email', $email)->get();
        $matching = $users->filter(function ($u) use ($password) {
            return !empty($u->password) && Hash::check($password, $u->password);
        })->values();

        if ($matching->isEmpty()) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // If this email administers multiple websites, let them choose which one to manage.
        $websiteAdmins = $matching->where('user_type', 'website_user')->values();
        if ($websiteAdmins->count() > 1) {
            $request->session()->put('login_select', [
                'ids' => $websiteAdmins->pluck('id')->map(fn ($v) => (int) $v)->all(),
                'remember' => $remember,
            ]);
            return redirect()->route('login.select-website');
        }

        return $this->completeLogin($request, $matching->first(), $remember);
    }

    /**
     * Finish logging in a chosen user (shared by the direct and website-selection paths).
     */
    private function completeLogin(Request $request, User $user, bool $remember)
    {
        Auth::login($user, $remember);
        $request->session()->regenerate();

        if ($user->isAffiliate()) {
            $affiliate = $user->affiliate;
            if (!$affiliate || $affiliate->status !== 'approved' || !$affiliate->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Your promoter application is still under review. We will notify you once approved.',
                ])->onlyInput('email');
            }
        }

        if ($user->isEntertainer()) {
            $entertainer = $user->entertainer;
            if (!$entertainer || $entertainer->status !== 'approved' || !$entertainer->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Your entertainer application is still under review. We will notify you once approved.',
                ])->onlyInput('email');
            }
        }

        return $this->redirectByUserType($user);
    }

    /**
     * Show the "which website do you want to manage?" selection after a verified login.
     */
    public function showWebsiteSelect(Request $request)
    {
        $select = $request->session()->get('login_select');
        if (empty($select['ids'])) {
            return redirect()->route('login');
        }

        $accounts = User::whereIn('id', $select['ids'])->with('website')->get();
        if ($accounts->count() < 2) {
            $request->session()->forget('login_select');
            return redirect()->route('login');
        }

        return view('auth.select-website', ['accounts' => $accounts]);
    }

    /**
     * Log the user in as the chosen website-admin account.
     */
    public function selectWebsite(Request $request)
    {
        $request->validate(['user_id' => 'required|integer']);

        $select = $request->session()->get('login_select');
        $allowed = array_map('intval', $select['ids'] ?? []);

        if (empty($allowed) || !in_array((int) $request->input('user_id'), $allowed, true)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Your session expired. Please log in again.',
            ]);
        }

        $user = User::find($request->input('user_id'));
        if (!$user) {
            return redirect()->route('login');
        }

        $request->session()->forget('login_select');
        return $this->completeLogin($request, $user, !empty($select['remember']));
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a password reset link to the provided email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show the reset password form.
     */
    public function showResetPasswordForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Handle an incoming new password update.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $hash = Hash::make($password);
                $user->forceFill([
                    'password' => $hash,
                    'remember_token' => Str::random(60),
                ])->save();
                // Keep every website-admin row that shares this email in sync.
                User::where('email', $user->email)->where('id', '!=', $user->id)->update(['password' => $hash]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', __($status));
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => __($status),
        ]);
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

    private function redirectByUserType(User $user)
    {
        if ($user->isAffiliate()) {
            return redirect()->route('affiliate.portal.dashboard');
        }

        if ($user->isEntertainer()) {
            return redirect()->route('entertainer.portal.dashboard');
        }

        if ($user->isWebsiteUser() || $user->isBouncer() || $user->isManager()) {
            // Always land on the dashboard — it's always accessible regardless of permissions
            return redirect()->route('admin.index');
        }

        return redirect()->route('admin.transaction.index');
    }
}

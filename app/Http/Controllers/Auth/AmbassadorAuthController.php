<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NightlyReportAmbassador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AmbassadorAuthController extends Controller
{
    public function showSetupForm($token)
    {
        $ambassador = NightlyReportAmbassador::where('setup_token', $token)->firstOrFail();
        return view('auth.ambassador-setup', compact('ambassador', 'token'));
    }

    public function setupPassword(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $ambassador = NightlyReportAmbassador::where('setup_token', $token)->firstOrFail();
        
        $ambassador->password = Hash::make($request->password);
        $ambassador->setup_token = null; // Clear the token so it can't be used again
        $ambassador->save();

        Auth::guard('ambassador')->login($ambassador);

        return redirect()->route('ambassador.dashboard')->with('success', 'Password set successfully! Welcome to your dashboard.');
    }

    public function showLoginForm()
    {
        return view('auth.ambassador-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('ambassador')->attempt(['email' => $request->email, 'password' => $request->password, 'is_active' => true], $request->filled('remember'))) {
            return redirect()->intended(route('ambassador.dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials or your account is disabled.'])->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::guard('ambassador')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('ambassador.login');
    }
}

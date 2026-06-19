<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     */
    public function edit()
    {
        return view('admin.profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();
        $hash = Hash::make($request->password);
        $user->password = $hash;
        $user->save();
        // Keep every website-admin row that shares this email in sync (one email may manage several websites).
        \App\Models\User::where('email', $user->email)->where('id', '!=', $user->id)->update(['password' => $hash]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }
}

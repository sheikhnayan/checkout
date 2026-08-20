<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\NightlyReports\NrLocation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NightlyAdminUserController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $users = User::with('nrLocations')
            ->orderBy('name')
            ->paginate(20);

        $locations = NrLocation::where('active', true)->orderBy('name')->get();

        return view('admin.nightly-reports.users.index', compact('users', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'user_type' => 'required|in:admin,manager,website_user',
            'location_ids' => 'nullable|array',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_type' => $validated['user_type'],
        ]);

        if (!empty($validated['location_ids'])) {
            $user->nrLocations()->sync($validated['location_ids']);
        }

        return back()->with('success', "User {$user->name} created successfully.");
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => "required|email|unique:users,email,{$user->id}",
            'user_type' => 'required|in:admin,manager,website_user',
            'location_ids' => 'nullable|array',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_type' => $validated['user_type'],
        ]);

        if ($request->has('location_ids')) {
            $user->nrLocations()->sync($validated['location_ids']);
        }

        return back()->with('success', "User {$user->name} updated successfully.");
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $newPassword = $request->input('password') ?: Str::random(12);

        $user->password = Hash::make($newPassword);
        $user->save();

        return back()->with('success', "Password reset for {$user->name}. New password: {$newPassword}");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account.');
        }

        $user->delete();
        return back()->with('success', 'User removed.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Hash;

class WebsiteUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('user_type', 'website_user')
                    ->withTrashed()
                    ->with('website')
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return view('admin.website-users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $websites = Website::all();
        return view('admin.website-users.create', compact('websites'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'website_id' => 'required|exists:websites,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'website_id' => $request->website_id,
            'user_type' => 'website_user',
        ]);

        return redirect()->route('admin.website-users.index')
                        ->with('success', 'Website user created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::where('user_type', 'website_user')->findOrFail($id);
        return view('admin.website-users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::where('user_type', 'website_user')->findOrFail($id);
        $websites = Website::all();
        return view('admin.website-users.edit', compact('user', 'websites'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::where('user_type', 'website_user')->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'website_id' => 'required|exists:websites,id',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'website_id' => $request->website_id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.website-users.index')
                        ->with('success', 'Website user updated successfully.');
    }

    /**
     * Archive/Unarchive the specified resource.
     */
    public function archive(string $id)
    {
        $user = User::where('user_type', 'website_user')->withTrashed()->findOrFail($id);
        
        if ($user->trashed()) {
            $user->restore();
            $message = 'Website user unarchived successfully.';
        } else {
            $user->delete();
            $message = 'Website user archived successfully.';
        }

        return redirect()->route('admin.website-users.index')
                        ->with('success', $message);
    }

    /**
     * Remove the specified resource from storage (kept for compatibility).
     */
    public function destroy(string $id)
    {
        return $this->archive($id);
    }
}

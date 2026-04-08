<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\User;
use App\Models\Website;
use App\Models\WebsiteRole;
use Illuminate\Support\Facades\Hash;

class WebsiteUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorizeUserManagement();

        $users = User::whereIn('user_type', ['website_user', 'bouncer'])
                    ->withTrashed()
                    ->with(['website', 'websiteRole'])
                    ->whereIn('website_id', $this->accessibleWebsiteIds())
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return view('admin.website-users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeUserManagement();
        Permission::syncFromAdminRoutes();

        $websites = Website::whereIn('id', $this->accessibleWebsiteIds())->orderBy('name')->get();
        $roles = WebsiteRole::with('website')
            ->whereIn('website_id', $this->accessibleWebsiteIds())
            ->orderBy('website_id')
            ->orderBy('name')
            ->get();

        return view('admin.website-users.create', compact('websites', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeUserManagement();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'website_id' => 'required|exists:websites,id',
            'user_type' => 'required|in:website_user,bouncer',
            'website_role_id' => 'required|exists:website_roles,id',
        ]);

        $this->ensureWebsiteAccess((int) $request->website_id);

        $role = WebsiteRole::findOrFail((int) $request->website_role_id);
        if ((int) $role->website_id !== (int) $request->website_id) {
            return redirect()->back()->withInput()->withErrors([
                'website_role_id' => 'Selected role does not belong to the selected website.',
            ]);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'website_id' => $request->website_id,
            'website_role_id' => $request->website_role_id,
            'user_type' => $request->user_type,
        ]);

        return redirect()->route('admin.website-users.index')
                        ->with('success', 'Website user created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorizeUserManagement();
        $user = User::whereIn('user_type', ['website_user', 'bouncer'])->findOrFail($id);
        $this->ensureWebsiteAccess((int) $user->website_id);
        return view('admin.website-users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorizeUserManagement();
        Permission::syncFromAdminRoutes();

        $user = User::whereIn('user_type', ['website_user', 'bouncer'])->findOrFail($id);
        $this->ensureWebsiteAccess((int) $user->website_id);

        $websites = Website::whereIn('id', $this->accessibleWebsiteIds())->orderBy('name')->get();
        $roles = WebsiteRole::with('website')
            ->whereIn('website_id', $this->accessibleWebsiteIds())
            ->orderBy('website_id')
            ->orderBy('name')
            ->get();

        return view('admin.website-users.edit', compact('user', 'websites', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorizeUserManagement();

        $user = User::whereIn('user_type', ['website_user', 'bouncer'])->findOrFail($id);
        $this->ensureWebsiteAccess((int) $user->website_id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'website_id' => 'required|exists:websites,id',
            'user_type' => 'required|in:website_user,bouncer',
            'website_role_id' => 'required|exists:website_roles,id',
        ]);

        $this->ensureWebsiteAccess((int) $request->website_id);

        $role = WebsiteRole::findOrFail((int) $request->website_role_id);
        if ((int) $role->website_id !== (int) $request->website_id) {
            return redirect()->back()->withInput()->withErrors([
                'website_role_id' => 'Selected role does not belong to the selected website.',
            ]);
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'website_id' => $request->website_id,
            'website_role_id' => $request->website_role_id,
            'user_type' => $request->user_type,
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
        $this->authorizeUserManagement();

        $user = User::whereIn('user_type', ['website_user', 'bouncer'])->withTrashed()->findOrFail($id);
        $this->ensureWebsiteAccess((int) $user->website_id);
        
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

    private function authorizeUserManagement(): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        if ($user->isAdmin()) {
            return;
        }

        if (($user->isWebsiteUser() || $user->isBouncer()) && $user->isWebsiteAdmin()) {
            return;
        }

        abort(403, 'You do not have permission to manage website users.');
    }

    private function accessibleWebsiteIds(): array
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return Website::pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        return $user->website_id ? [(int) $user->website_id] : [];
    }

    private function ensureWebsiteAccess(int $websiteId): void
    {
        if (!in_array($websiteId, $this->accessibleWebsiteIds(), true)) {
            abort(403, 'You do not have access to this website.');
        }
    }
}

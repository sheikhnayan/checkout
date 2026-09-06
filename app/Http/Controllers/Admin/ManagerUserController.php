<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminCreatedUserMail;
use App\Models\Permission;
use App\Models\User;
use App\Models\Website;
use App\Models\WebsiteRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ManagerUserController extends Controller
{
    /**
     * Only the true super admin (user_type = 'admin') may access this controller.
     * Website admins and manager users are explicitly blocked.
     */
    private function authorizeAccess(): void
    {
        $user = auth()->user();

        if (!$user || !$user->isAdmin()) {
            abort(403, 'Only super admins can manage manager users.');
        }
    }

    // ------------------------------------------------------------------ //
    //  Index
    // ------------------------------------------------------------------ //

    public function index()
    {
        $this->authorizeAccess();

        $managers = User::where('user_type', 'manager')
            ->withTrashed()
            ->with(['websiteRole', 'managedWebsites'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.manager-users.index', compact('managers'));
    }

    // ------------------------------------------------------------------ //
    //  Create
    // ------------------------------------------------------------------ //

    public function create()
    {
        $this->authorizeAccess();
        Permission::syncFromAdminRoutes();

        $websites = Website::where('is_archieved', 0)->orderBy('name')->get();
        $roles = WebsiteRole::with('website')->orderBy('website_id')->orderBy('name')->get();

        return view('admin.manager-users.create', compact('websites', 'roles'));
    }

    // ------------------------------------------------------------------ //
    //  Store
    // ------------------------------------------------------------------ //

    public function store(Request $request)
    {
        $this->authorizeAccess();

        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users',
            'password'        => 'required|string|min:8|confirmed',
            'website_role_id' => 'required|exists:website_roles,id',
            'website_ids'     => 'required|array|min:1',
            'website_ids.*'   => 'integer|exists:websites,id',
        ]);

        $manager = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'user_type'       => 'manager',
            'website_id'      => null,
            'website_role_id' => $request->website_role_id,
        ]);

        $manager->managedWebsites()->sync(
            collect($request->website_ids)->map(fn ($id) => (int) $id)->unique()->all()
        );

        Mail::to($manager->email)->send(new AdminCreatedUserMail($manager, $request->password));

        return redirect()->route('admin.manager-users.index')
            ->with('success', 'Manager user created successfully.');
    }

    // ------------------------------------------------------------------ //
    //  Show
    // ------------------------------------------------------------------ //

    public function show(string $id)
    {
        $this->authorizeAccess();

        $manager = User::where('user_type', 'manager')
            ->with(['websiteRole', 'managedWebsites'])
            ->findOrFail($id);

        return view('admin.manager-users.show', compact('manager'));
    }

    // ------------------------------------------------------------------ //
    //  Edit
    // ------------------------------------------------------------------ //

    public function edit(string $id)
    {
        $this->authorizeAccess();
        Permission::syncFromAdminRoutes();

        $manager = User::where('user_type', 'manager')
            ->with(['websiteRole', 'managedWebsites'])
            ->findOrFail($id);

        $websites = Website::where('is_archieved', 0)->orderBy('name')->get();
        $roles = WebsiteRole::with('website')->orderBy('website_id')->orderBy('name')->get();
        $assignedWebsiteIds = $manager->managedWebsites->pluck('id')->map(fn ($id) => (int) $id)->all();

        return view('admin.manager-users.edit', compact('manager', 'websites', 'roles', 'assignedWebsiteIds'));
    }

    // ------------------------------------------------------------------ //
    //  Update
    // ------------------------------------------------------------------ //

    public function update(Request $request, string $id)
    {
        $this->authorizeAccess();

        $manager = User::where('user_type', 'manager')->findOrFail($id);

        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users,email,' . $manager->id,
            'password'        => 'nullable|string|min:8|confirmed',
            'website_role_id' => 'required|exists:website_roles,id',
            'website_ids'     => 'required|array|min:1',
            'website_ids.*'   => 'integer|exists:websites,id',
        ]);

        $updateData = [
            'name'            => $request->name,
            'email'           => $request->email,
            'website_role_id' => $request->website_role_id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $manager->update($updateData);

        $manager->managedWebsites()->sync(
            collect($request->website_ids)->map(fn ($id) => (int) $id)->unique()->all()
        );

        return redirect()->route('admin.manager-users.index')
            ->with('success', 'Manager user updated successfully.');
    }

    // ------------------------------------------------------------------ //
    //  Archive / Restore
    // ------------------------------------------------------------------ //

    public function archive(string $id)
    {
        $this->authorizeAccess();

        $manager = User::where('user_type', 'manager')->withTrashed()->findOrFail($id);

        if ($manager->trashed()) {
            $manager->restore();
            $message = 'Manager user restored successfully.';
        } else {
            $manager->delete();
            $message = 'Manager user archived successfully.';
        }

        return redirect()->route('admin.manager-users.index')->with('success', $message);
    }

    public function destroy(string $id)
    {
        return $this->archive($id);
    }
}

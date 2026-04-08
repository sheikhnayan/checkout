<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Website;
use App\Models\WebsiteRole;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebsiteRoleController extends Controller
{
    public function index()
    {
        $this->authorizeRoleManagement();
        Permission::syncFromAdminRoutes();

        $roles = WebsiteRole::query()
            ->with(['website', 'permissions', 'users'])
            ->whereIn('website_id', $this->accessibleWebsiteIds())
            ->orderBy('website_id')
            ->orderBy('name')
            ->get();

        return view('admin.website-roles.index', compact('roles'));
    }

    public function create()
    {
        $this->authorizeRoleManagement();
        Permission::syncFromAdminRoutes();

        return view('admin.website-roles.create', [
            'websites' => $this->accessibleWebsites(),
            'permissionsByModule' => $this->permissionsByModule(),
            'selectedWebsiteId' => $this->defaultWebsiteId(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeRoleManagement();
        Permission::syncFromAdminRoutes();

        $websiteId = $this->resolveWebsiteIdFromRequest($request);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'integer|exists:permissions,id',
        ]);

        $slug = Str::slug((string) $request->name) ?: 'role';
        $baseSlug = $slug;
        $counter = 1;
        while (WebsiteRole::where('website_id', $websiteId)->where('slug', $slug)->exists()) {
            $counter++;
            $slug = $baseSlug . '-' . $counter;
        }

        $role = WebsiteRole::create([
            'website_id' => $websiteId,
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_website_admin' => false,
            'is_system' => false,
        ]);

        $permissionIds = Permission::whereIn('id', $request->input('permission_ids', []))
            ->where('is_super_admin_only', false)
            ->pluck('id')
            ->all();

        $role->permissions()->sync($permissionIds);

        return redirect()->route('admin.website-roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(WebsiteRole $website_role)
    {
        $this->authorizeRoleManagement();
        Permission::syncFromAdminRoutes();
        $this->ensureWebsiteAccess((int) $website_role->website_id);

        return view('admin.website-roles.edit', [
            'role' => $website_role->load('permissions', 'website'),
            'websites' => $this->accessibleWebsites(),
            'permissionsByModule' => $this->permissionsByModule(),
            'selectedWebsiteId' => (int) $website_role->website_id,
        ]);
    }

    public function update(Request $request, WebsiteRole $website_role)
    {
        $this->authorizeRoleManagement();
        Permission::syncFromAdminRoutes();
        $this->ensureWebsiteAccess((int) $website_role->website_id);

        if ($website_role->is_website_admin) {
            return redirect()->back()->with('error', 'System website admin role cannot be edited.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'integer|exists:permissions,id',
        ]);

        $website_role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $permissionIds = Permission::whereIn('id', $request->input('permission_ids', []))
            ->where('is_super_admin_only', false)
            ->pluck('id')
            ->all();

        $website_role->permissions()->sync($permissionIds);

        return redirect()->route('admin.website-roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(WebsiteRole $website_role)
    {
        return $this->archive($website_role);
    }

    public function archive(WebsiteRole $website_role)
    {
        $this->authorizeRoleManagement();
        $this->ensureWebsiteAccess((int) $website_role->website_id);

        if ($website_role->is_system || $website_role->is_website_admin) {
            return redirect()->back()->with('error', 'System roles cannot be deleted.');
        }

        if ($website_role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete role assigned to users.');
        }

        $website_role->permissions()->detach();
        $website_role->delete();

        return redirect()->route('admin.website-roles.index')->with('success', 'Role deleted successfully.');
    }

    private function authorizeRoleManagement(): void
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

        abort(403, 'You do not have permission to manage roles.');
    }

    private function accessibleWebsiteIds(): array
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return Website::query()->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        return $user->website_id ? [(int) $user->website_id] : [];
    }

    private function accessibleWebsites()
    {
        return Website::query()
            ->whereIn('id', $this->accessibleWebsiteIds())
            ->orderBy('name')
            ->get();
    }

    private function ensureWebsiteAccess(int $websiteId): void
    {
        if (!in_array($websiteId, $this->accessibleWebsiteIds(), true)) {
            abort(403, 'You do not have access to this website role.');
        }
    }

    private function resolveWebsiteIdFromRequest(Request $request): int
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $request->validate([
                'website_id' => 'required|integer|exists:websites,id',
            ]);

            $websiteId = (int) $request->input('website_id');
            $this->ensureWebsiteAccess($websiteId);
            return $websiteId;
        }

        return (int) $user->website_id;
    }

    private function permissionsByModule()
    {
        return Permission::query()
            ->where('is_super_admin_only', false)
            ->where(function ($query) {
                $query->where('module', '!=', 'website')
                    ->orWhere('key', 'like', 'admin.website.payment-settings%');
            })
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');
    }

    private function defaultWebsiteId(): ?int
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return null;
        }

        return $user->website_id ? (int) $user->website_id : null;
    }
}

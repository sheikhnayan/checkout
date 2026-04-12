<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoutePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $routeName = $request->route()?->getName();
        if (!$routeName) {
            return $next($request);
        }

        // Profile is available for any authenticated backoffice user.
        if (in_array($routeName, ['admin.profile.edit', 'admin.profile.update-password'], true)) {
            return $next($request);
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if (!$user->isWebsiteUser() && !$user->isBouncer()) {
            abort(403, 'Access denied.');
        }

        if (in_array($routeName, ['admin.index'], true)) {
            return $next($request);
        }

        $permission = Permission::where('key', $routeName)->first();
        if ($permission && $permission->is_super_admin_only) {
            abort(403, 'This feature is available only to super admins.');
        }

        if (!$user->hasRoutePermission($routeName)) {
            if (!$user->website_role_id && str_starts_with($routeName, 'admin.transaction.')) {
                return $next($request);
            }

            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}

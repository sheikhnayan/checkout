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
        $routeName = (string) $request->route()?->getName();
        if (auth()->guard('ambassador')->check()) {
            $ambassadorRoutes = [
                'admin.nightly-reports.dashboard',
                'admin.nightly-reports.reports.',
                'admin.nightly-reports.trends.',
                'admin.nightly-reports.missing.',
                'admin.nightly-reports.boutique.',
                'admin.nightly-reports.fourweek.',
                'admin.nightly-reports.quarterly.',
                'admin.nightly-reports.coh.',
                'admin.nightly-reports.incidents.',
                'admin.nightly-reports.witness.',
                'admin.nightly-reports.witness-qr.',
                'admin.nightly-reports.high-transactions.',
                'admin.nightly-reports.model-releases.',
            ];

            foreach ($ambassadorRoutes as $allowedRoute) {
                if ($routeName === rtrim($allowedRoute, '.') || str_starts_with($routeName, $allowedRoute)) {
                    return $next($request);
                }
            }

            abort(403, 'This administration area is not available to ambassadors.');
        }

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $routeName = $request->route()?->getName();
        if (!$routeName) {
            return $next($request);
        }

        // Profile & Help Center invitations/portal routes are available for authenticated users.
        if (in_array($routeName, ['admin.profile.edit', 'admin.profile.update-password', 'admin.help-center.invitation.accept', 'admin.help-center.invitation.decline', 'help-center.invitation.accept', 'help-center.invitation.decline'], true) || str_contains($routeName, 'help-center.')) {
            return $next($request);
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        // Entertainers are allowed to manage only feed post routes.
        if ($user->isEntertainer() && str_starts_with($routeName, 'admin.feed-post.')) {
            return $next($request);
        }

        if (!$user->isWebsiteUser() && !$user->isBouncer() && !$user->isManager()) {
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

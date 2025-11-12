<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebsiteUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Allow admin users full access
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if user is website user
        if (!$user->isWebsiteUser() || !$user->website_id) {
            abort(403, 'Access denied. You do not have permission to access this resource.');
        }

        // Check specific permissions for website users
        if ($permission) {
            switch ($permission) {
                case 'transactions-only':
                    // Allow access only to transaction-related routes
                    $allowedRoutes = [
                        'admin.transaction.index',
                        'admin.transaction.show',
                    ];
                    
                    if (!in_array($request->route()->getName(), $allowedRoutes)) {
                        abort(403, 'Access denied. Website users can only access transactions.');
                    }
                    break;
                    
                case 'admin-only':
                    // Deny access to admin-only features
                    abort(403, 'Access denied. This feature is only available to administrators.');
                    break;
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictMrRollCallDomain
{
    /**
     * Restrict mrrollcall.com to specific public/auth/admin routes only.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower((string) $request->getHost());

        if (!in_array($host, ['mrrollcall.com', 'www.mrrollcall.com'], true)) {
            return $next($request);
        }

        $path = ltrim($request->path(), '/');

        $isAllowed = false;

        // Allow full admin area for this domain.
        if ($path === 'admins' || str_starts_with($path, 'admins/')) {
            $isAllowed = true;
        }

        // Allow login/logout endpoints.
        if (in_array($path, ['login', 'logout'], true)) {
            $isAllowed = true;
        }

        // Allow entertainer registration.
        if ($path === 'entertainer/apply') {
            $isAllowed = true;
        }

        // Allow feed index.
        if ($path === 'feed') {
            $isAllowed = true;
        }

        // Allow club feed page.
        if (preg_match('#^[^/]+/feed$#', $path) === 1) {
            $isAllowed = true;
        }

        // Allow feed profile pages.
        if (preg_match('#^[^/]+/feed/profile$#', $path) === 1) {
            $isAllowed = true;
        }

        // Allow feed model profile pages.
        if (preg_match('#^[^/]+/feed/models/[^/]+$#', $path) === 1) {
            $isAllowed = true;
        }

        if (!$isAllowed) {
            abort(404);
        }

        return $next($request);
    }
}

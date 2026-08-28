<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdminOrAmbassador
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return $next($request);
        }

        if (Auth::guard('ambassador')->check() && $request->routeIs('admin.nightly-reports.*')) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}

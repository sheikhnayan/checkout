<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleThrottleResponse
{
    /**
     * Handle throttle response for form submissions
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);

            // If response is a 429 (Too Many Requests), convert to friendly redirect
            if ($response->status() === 429) {
                return redirect()->back()
                    ->with('error', 'Too many submission attempts. Please wait 60 seconds before trying again.')
                    ->withInput();
            }

            return $response;
        } catch (\Exception $e) {
            // If throttle exception thrown, catch it
            if (strpos($e->getMessage(), 'throttle') !== false || strpos(get_class($e), 'Throttle') !== false) {
                return redirect()->back()
                    ->with('error', 'Too many submission attempts. Please wait 60 seconds before trying again.')
                    ->withInput();
            }

            throw $e;
        }
    }
}

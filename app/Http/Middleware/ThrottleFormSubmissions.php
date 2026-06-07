<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThrottleFormSubmissions
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $limit = 5, $decay = 60): Response
    {
        // Only throttle POST requests to form submissions
        if (!$request->isMethod('post')) {
            return $next($request);
        }

        // Get client identifier (IP address)
        $clientId = $request->ip();

        // Create unique key for this form type
        $key = 'form_submission_' . $clientId . '_' . $request->path();

        // Check rate limit
        if ($this->limiter->tooManyAttempts($key, $limit, $decay)) {
            // Get remaining seconds
            $retryAfter = $this->limiter->availableIn($key);

            return response()->json([
                'success' => false,
                'message' => "Too many submission attempts. Please try again in {$retryAfter} seconds.",
                'retry_after' => $retryAfter,
            ], 429);
        }

        // Increment the counter
        $this->limiter->hit($key, $decay);

        return $next($request);
    }
}

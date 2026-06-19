<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * For AJAX checkout submissions, reshape the controller's normal response into JSON
 * so the page can stay where it is on error and only navigate on success.
 *
 * The controller is left completely untouched: regular (non-AJAX) submissions keep
 * their exact existing behaviour (redirect to thank-you on success, redirect back
 * with a flashed 'error' on failure).
 */
class CheckoutAjaxResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only transform for AJAX/JSON requests; everything else is left exactly as-is.
        if (! ($request->ajax() || $request->wantsJson())) {
            return $response;
        }

        // Success / error are returned by the controller as redirects.
        if ($response instanceof RedirectResponse) {
            $target = $response->getTargetUrl();
            $thankYou = route('thank-you');

            if ($target === $thankYou || str_starts_with($target, $thankYou)) {
                // Success — keep the session flash (transaction/website/etc.) and let the
                // browser navigate to the thank-you page, exactly like the normal flow.
                return new JsonResponse(['success' => true, 'redirect' => $target]);
            }

            // Error redirect (back) — surface the same message the page would have shown.
            return new JsonResponse([
                'success' => false,
                'error' => $this->resolveError($request),
            ], 422);
        }

        return $response;
    }

    private function resolveError(Request $request): string
    {
        $session = $request->session();

        $error = $session->get('error');
        if (is_string($error) && $error !== '') {
            return $error;
        }

        if ($session->has('errors')) {
            $bag = $session->get('errors');
            if ($bag && method_exists($bag, 'first')) {
                $first = $bag->first();
                if (is_string($first) && $first !== '') {
                    return $first;
                }
            }
        }

        return 'Something went wrong. Please try again.';
    }
}

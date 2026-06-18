<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin client for the ClubLifter CartVIP Integration API (v1).
 *
 * Every method is defensive: it never throws. On any problem (disabled,
 * missing key, network/HTTP error) it logs and returns null, so callers
 * can safely fire-and-forget without affecting the checkout flow.
 */
class ClubLifterService
{
    /** Schedule a customer with transport. POST /api/v1/schedule */
    public function schedule(array $payload): ?array
    {
        return $this->post('/api/v1/schedule', $payload);
    }

    /** Register a walk-in (no transport). POST /api/v1/walkin */
    public function walkin(array $payload): ?array
    {
        return $this->post('/api/v1/walkin', $payload);
    }

    /** Get a booking's status. GET /api/v1/customer/{id} */
    public function getCustomer(int|string $customerId): ?array
    {
        return $this->get('/api/v1/customer/' . $customerId);
    }

    /** Cancel a booking. POST /api/v1/customer/{id}/cancel */
    public function cancel(int|string $customerId): ?array
    {
        return $this->post('/api/v1/customer/' . $customerId . '/cancel', []);
    }

    protected function get(string $path): ?array
    {
        return $this->request('get', $path, null);
    }

    protected function post(string $path, array $payload): ?array
    {
        return $this->request('post', $path, $payload);
    }

    protected function request(string $method, string $path, ?array $payload): ?array
    {
        if (! config('services.clublifter.enabled', false)) {
            return null;
        }

        $key = config('services.clublifter.key');
        if (empty($key)) {
            Log::warning('ClubLifter: API key not configured; skipping request', ['path' => $path]);
            return null;
        }

        $base = rtrim((string) config('services.clublifter.base_url', 'https://www.clublifter.com'), '/');

        try {
            $http = Http::withHeaders(['X-API-Key' => $key])
                ->acceptJson()
                ->connectTimeout(5)
                ->timeout(12);

            $response = $method === 'post'
                ? $http->post($base . $path, $payload ?? [])
                : $http->get($base . $path);

            $data = $response->json();

            if (! $response->successful()) {
                Log::warning('ClubLifter: API returned an error', [
                    'path' => $path,
                    'status' => $response->status(),
                    'body' => $data,
                ]);
                return null;
            }

            return is_array($data) ? $data : null;
        } catch (\Throwable $e) {
            Log::warning('ClubLifter: API request failed', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}

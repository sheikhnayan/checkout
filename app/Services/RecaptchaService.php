<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    private $secretKey;
    private $threshold;
    private $client;

    public function __construct()
    {
        $this->secretKey = config('services.recaptcha.secret_key');
        $this->threshold = config('services.recaptcha.threshold', 0.5);
        $this->client = new Client();
    }

    /**
     * Verify reCAPTCHA v3 token
     * Returns: ['success' => bool, 'score' => float, 'message' => string]
     */
    public function verify($token)
    {
        try {
            // Skip verification if keys not configured
            if (!$this->secretKey || $this->secretKey === 'YOUR_RECAPTCHA_SECRET_KEY_HERE') {
                Log::warning('reCAPTCHA not configured - skipping verification');
                return ['success' => true, 'score' => 1.0, 'message' => 'Verification skipped'];
            }

            $response = $this->client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => $this->secretKey,
                    'response' => $token,
                ]
            ]);

            $body = json_decode((string)$response->getBody());

            // Check if Google returned success
            if (!isset($body->success) || !$body->success) {
                Log::warning('reCAPTCHA verification failed', ['body' => $body]);
                return ['success' => false, 'score' => 0.0, 'message' => 'Verification failed'];
            }

            $score = $body->score ?? 0;

            // Log the score for monitoring
            Log::info('reCAPTCHA verification', [
                'score' => $score,
                'action' => $body->action ?? 'unknown',
                'challenge_ts' => $body->challenge_ts ?? null,
            ]);

            // Check if score meets threshold
            if ($score < $this->threshold) {
                return [
                    'success' => false,
                    'score' => $score,
                    'message' => 'Bot behavior detected'
                ];
            }

            return [
                'success' => true,
                'score' => $score,
                'message' => 'Verification passed'
            ];

        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error', ['error' => $e->getMessage()]);
            // On error, allow submission (better UX than blocking all users)
            return ['success' => true, 'score' => 1.0, 'message' => 'Error - verification skipped'];
        }
    }
}

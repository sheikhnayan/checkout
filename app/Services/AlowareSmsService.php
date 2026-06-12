<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class AlowareSmsService
{
    private $apiToken;
    private $apiUrl = 'https://app.aloware.io/api/v1/webhook/sms-gateway/send';
    private $fromNumber; // Default sending number
    private $client;

    public function __construct()
    {
        $this->apiToken = config('services.aloware.api_key');
        $this->fromNumber = config('services.aloware.from_number'); // e.g., +18552562001
        $this->client = new Client();
    }

    /**
     * Send SMS notification for transaction
     *
     * @param string $phoneNumber - Recipient phone number (with country code, e.g., +1234567890)
     * @param array $transactionData - Transaction details
     * @param string $type - 'package' or 'reservation'
     * @return array - Response from Aloware API
     */
    public function sendTransactionNotification($phoneNumber, $transactionData, $type = 'package')
    {
        if (!$this->apiToken) {
            Log::warning('Aloware API token not configured');
            return ['success' => false, 'message' => 'SMS service not configured'];
        }

        if (!$this->fromNumber) {
            Log::warning('Aloware "from" number (ALOWARE_FROM_NUMBER) not configured');
            return ['success' => false, 'message' => 'SMS service not properly configured - no sending number'];
        }

        try {
            $message = $this->formatTransactionMessage($transactionData, $type);
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);

            return $this->sendSms($phoneNumber, $message);
        } catch (\Exception $e) {
            Log::error('SMS notification error: ' . $e->getMessage(), [
                'phone' => $phoneNumber,
                'type' => $type,
                'transaction_id' => $transactionData['transaction_id'] ?? 'unknown'
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send SMS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format transaction data into professional SMS message
     */
    private function formatTransactionMessage($data, $type)
    {
        $clubName = $data['club_name'] ?? $data['website_name'] ?? 'Your Venue';
        $confirmationId = $data['transaction_id'] ?? 'Pending';
        $totalAmount = $data['total_amount'] ?? $data['total'] ?? '0.00';

        if ($type === 'reservation') {
            $reservationDate = $data['reservation_date'] ?? $data['package_use_date'] ?? 'N/A';

            // Format date to "Month Day, Year" format (e.g., June 25, 2026)
            try {
                $dateObj = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $reservationDate);
                $formattedDate = $dateObj->format('F j, Y');
            } catch (\Exception $e) {
                $formattedDate = $reservationDate;
            }

            $message = "RESERVATION CONFIRMED\n\n";
            $message .= "Venue: {$clubName}\n";
            $message .= "Confirmation: #{$confirmationId}\n";
            $message .= "Date: {$formattedDate}\n\n";
            $message .= "Your reservation details and QR code have been emailed to you. Please check your inbox and spam folder if needed.\n\n";
            $message .= "Questions? Contact the venue directly.";
        } else {
            // Package type
            $packageDate = $data['reservation_date'] ?? $data['package_use_date'] ?? 'N/A';

            // Format date to "Month Day, Year" format (e.g., June 25, 2026)
            try {
                $dateObj = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $packageDate);
                $formattedDate = $dateObj->format('F j, Y');
            } catch (\Exception $e) {
                $formattedDate = $packageDate;
            }

            $message = "PURCHASE CONFIRMED\n\n";
            $message .= "Venue: {$clubName}\n";
            $message .= "Confirmation: #{$confirmationId}\n";
            $message .= "Date: {$formattedDate}\n\n";
            $message .= "Your purchase details and QR code have been emailed to you. Please check your inbox and spam folder if needed.\n\n";
            $message .= "Questions? Contact the venue directly.";
        }

        // Keep message under 160 characters if possible, but Aloware handles longer messages
        return substr($message, 0, 300); // Allow up to 300 chars, Aloware will handle multi-part
    }

    /**
     * Get club link for SMS
     */
    private function getClubLink($data)
    {
        if (!empty($data['club_slug'])) {
            return url('/' . $data['club_slug']);
        } elseif (!empty($data['website_slug'])) {
            return url('/' . $data['website_slug']);
        }
        return url('/');
    }

    /**
     * Send SMS via Aloware API (Official endpoint)
     */
    private function sendSms($phoneNumber, $message)
    {
        try {
            // Ensure phone number has country code
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);

            // Official Aloware API request
            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'api_token' => $this->apiToken,
                    'from' => $this->fromNumber, // The sending phone number
                    'to' => $phoneNumber, // The recipient phone number
                    'message' => $message, // The SMS text
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($statusCode === 202) { // Aloware returns 202 on success
                Log::info('SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'from' => $this->fromNumber,
                    'response' => $body
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'response' => $body
                ];
            } else {
                Log::warning('SMS send failed', [
                    'phone' => $phoneNumber,
                    'status' => $statusCode,
                    'response' => $body
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send SMS: ' . ($body['message'] ?? 'Unknown error'),
                    'status' => $statusCode
                ];
            }
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            // Network/DNS error
            Log::error('Aloware API connection failed (Network/DNS issue)', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
                'suggestion' => 'Server cannot reach app.aloware.io - check firewall, DNS, or internet connectivity'
            ]);

            return [
                'success' => false,
                'message' => 'Network error: Cannot reach SMS service. Contact hosting provider.',
                'network_error' => true
            ];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 'unknown';
            $body = $response ? json_decode($response->getBody(), true) : [];

            Log::error('Aloware API request failed', [
                'phone' => $phoneNumber,
                'status' => $statusCode,
                'error' => $e->getMessage(),
                'response' => $body
            ]);

            return [
                'success' => false,
                'message' => 'API request failed: ' . ($body['message'] ?? $e->getMessage()),
                'status' => $statusCode
            ];
        } catch (\Exception $e) {
            Log::error('SMS sending exception: ' . $e->getMessage(), [
                'exception_class' => get_class($e),
                'phone' => $phoneNumber
            ]);
            return [
                'success' => false,
                'message' => 'Unexpected error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number to international format
     * More lenient - accepts numbers without country code
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // If already has +, return as-is
        if (str_starts_with($cleaned, '+')) {
            return $cleaned;
        }

        // Get default country code from config
        $defaultCountryCode = config('services.aloware.default_country_code', '1');

        // If no +, we need to add a country code
        // Check if it starts with the country code already (without the +)
        if (str_starts_with($cleaned, $defaultCountryCode)) {
            // Already has country code as first digits, just add +
            return '+' . $cleaned;
        }

        // Remove any leading 1 if default country code is 1 (US) and length is 11
        if ($defaultCountryCode === '1' && strlen($cleaned) === 11 && str_starts_with($cleaned, '1')) {
            $cleaned = substr($cleaned, 1);
        }

        // Check phone length - must be at least 7 digits for most countries
        if (strlen($cleaned) >= 7) {
            // Add the default country code
            return '+' . $defaultCountryCode . $cleaned;
        } else {
            // Too short - log warning and try anyway
            Log::warning('Phone number appears incomplete', [
                'original' => $phoneNumber,
                'cleaned' => $cleaned,
                'length' => strlen($cleaned),
                'default_country_code' => $defaultCountryCode
            ]);
            return '+' . $defaultCountryCode . $cleaned;
        }
    }

    /**
     * Test SMS sending
     */
    public function sendTest($phoneNumber, $message = null)
    {
        $testMessage = $message ?? "Test message from CartVIP SMS Service. If you received this, SMS notifications are working!";

        return $this->sendSms($phoneNumber, $testMessage);
    }
}

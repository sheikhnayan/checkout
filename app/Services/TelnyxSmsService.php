<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class TelnyxSmsService
{
    private $apiKey;
    private $apiUrl = 'https://api.telnyx.com/v2/messages';
    private $fromNumber; // Telnyx phone number to send from (E.164 format)
    private $client;

    public function __construct()
    {
        $this->apiKey = config('services.telnyx.api_key');
        $this->fromNumber = config('services.telnyx.from_number'); // e.g., +15551234567
        $this->client = new Client();
    }

    /**
     * Send SMS notification for transaction
     *
     * @param string $phoneNumber - Recipient phone number (any format, will be converted to E.164)
     * @param array $transactionData - Transaction details
     * @param string $type - 'package' or 'reservation'
     * @return array - Response from Telnyx API
     */
    public function sendTransactionNotification($phoneNumber, $transactionData, $type = 'package')
    {
        if (!$this->apiKey) {
            Log::warning('Telnyx API key not configured');
            return ['success' => false, 'message' => 'SMS service not configured'];
        }

        if (!$this->fromNumber) {
            Log::warning('Telnyx "from" number (TELNYX_FROM_NUMBER) not configured');
            return ['success' => false, 'message' => 'SMS service not properly configured - no sending number'];
        }

        try {
            $message = $this->formatTransactionMessage($transactionData, $type);
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);

            // Validate phone number is in E.164 format
            if (!$this->isValidE164($phoneNumber)) {
                Log::error('Invalid phone number format for SMS', [
                    'phone' => $phoneNumber,
                    'type' => $type
                ]);
                return [
                    'success' => false,
                    'message' => 'Invalid phone number format'
                ];
            }

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
            $menCount = $data['men_count'] ?? 0;
            $womenCount = $data['women_count'] ?? 0;
            $totalGuests = $menCount + $womenCount;

            $message = "RESERVATION CONFIRMED\n\n";
            $message .= "Club: {$clubName}\n";
            $message .= "Confirmation: #{$confirmationId}\n";
            $message .= "Date: {$reservationDate}\n";
            $message .= "Guests: {$menCount} Men + {$womenCount} Women = {$totalGuests} Total\n";
            $message .= "Total: \${$totalAmount}\n\n";

            if (!empty($data['notes'])) {
                $message .= "Notes: {$data['notes']}\n\n";
            }

            $message .= "Your reservation is confirmed!\n";
            $message .= "View Details: {$this->getClubLink($data)}\n";
            $message .= "Questions? Contact {$clubName}";
        } else {
            // Package type
            $packageName = $data['package_name'] ?? 'Package';
            $quantity = $data['quantity'] ?? 1;

            $message = "BOOKING CONFIRMED\n\n";
            $message .= "Club: {$clubName}\n";
            $message .= "Confirmation: #{$confirmationId}\n";
            $message .= "Package: {$packageName}\n";
            $message .= "Quantity: {$quantity}\n";
            $message .= "Total: \${$totalAmount}\n\n";

            if (!empty($data['package_date']) || !empty($data['event_date'])) {
                $eventDate = $data['package_date'] ?? $data['event_date'] ?? 'N/A';
                $message .= "Event Date: {$eventDate}\n\n";
            }

            $message .= "Your booking is confirmed!\n";
            $message .= "View Details: {$this->getClubLink($data)}\n";
            $message .= "Questions? Contact {$clubName}";
        }

        // Telnyx supports up to 1,600 characters (SMS will be concatenated if longer)
        return substr($message, 0, 1600);
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
     * Send SMS via Telnyx API
     */
    private function sendSms($phoneNumber, $message)
    {
        try {
            // Official Telnyx API request
            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'from' => $this->fromNumber, // Telnyx phone number in E.164 format
                    'to' => $phoneNumber, // Recipient phone number in E.164 format
                    'text' => $message, // The SMS text (max 1,600 chars)
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($statusCode === 201 || $statusCode === 200) {
                Log::info('SMS sent successfully via Telnyx', [
                    'phone' => $phoneNumber,
                    'from' => $this->fromNumber,
                    'message_id' => $body['data']['id'] ?? 'unknown',
                    'status' => $body['data']['status'] ?? 'sent'
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'message_id' => $body['data']['id'] ?? null,
                    'status' => $body['data']['status'] ?? 'sent'
                ];
            } else {
                Log::warning('Telnyx SMS send failed', [
                    'phone' => $phoneNumber,
                    'status' => $statusCode,
                    'response' => $body
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send SMS: ' . ($body['errors'][0]['detail'] ?? 'Unknown error'),
                    'status' => $statusCode
                ];
            }
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            // Network/DNS error
            Log::error('Telnyx API connection failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
                'suggestion' => 'Server cannot reach api.telnyx.com - check firewall/DNS'
            ]);

            return [
                'success' => false,
                'message' => 'Network error: Cannot reach SMS service.',
                'network_error' => true
            ];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // 4xx error (client error)
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            Log::error('Telnyx API client error', [
                'phone' => $phoneNumber,
                'status' => $statusCode,
                'error_code' => $body['errors'][0]['code'] ?? 'unknown',
                'error_detail' => $body['errors'][0]['detail'] ?? 'Unknown error'
            ]);

            return [
                'success' => false,
                'message' => 'API error: ' . ($body['errors'][0]['detail'] ?? 'Client error'),
                'status' => $statusCode,
                'error_code' => $body['errors'][0]['code'] ?? null
            ];
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // 5xx error (server error)
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();

            Log::error('Telnyx API server error', [
                'phone' => $phoneNumber,
                'status' => $statusCode
            ]);

            // Retryable server error
            return [
                'success' => false,
                'message' => 'Telnyx service temporarily unavailable. Please try again.',
                'status' => $statusCode,
                'retryable' => true
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
     * Format phone number to E.164 format (+[country][number])
     * Handles various input formats
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // If already in E.164 format (starts with +), return as-is
        if (str_starts_with($cleaned, '+')) {
            return $cleaned;
        }

        // Get default country code from config
        $defaultCountryCode = config('services.telnyx.default_country_code', '1');

        // Remove any leading 1 if default country code is 1 (US) and length is 11
        if ($defaultCountryCode === '1' && strlen($cleaned) === 11 && str_starts_with($cleaned, '1')) {
            $cleaned = substr($cleaned, 1);
        }

        // Add + and country code to convert to E.164 format
        return '+' . $defaultCountryCode . $cleaned;
    }

    /**
     * Validate phone number is in E.164 format
     * Format: +[1-3 digits country code][number]
     */
    private function isValidE164($phoneNumber)
    {
        // E.164 format: +[1-3 digits country code][7-14 digits number]
        // Total: +[1-3][7-14] = 9-18 characters
        return preg_match('/^\+\d{1,3}\d{7,14}$/', $phoneNumber) === 1;
    }

    /**
     * Test SMS sending
     */
    public function sendTest($phoneNumber, $message = null)
    {
        $testMessage = $message ?? "Test message from CartVIP SMS Service. If you received this, SMS notifications are working!";

        return $this->sendSms($this->formatPhoneNumber($phoneNumber), $testMessage);
    }
}

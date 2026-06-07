<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class AlowareSmsService
{
    private $apiKey;
    private $apiUrl = 'https://api.aloware.io/v1/send-message';
    private $client;

    public function __construct()
    {
        $this->apiKey = config('services.aloware.api_key');
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
        if (!$this->apiKey) {
            Log::warning('Aloware API key not configured');
            return ['success' => false, 'message' => 'SMS service not configured'];
        }

        try {
            $message = $this->formatTransactionMessage($transactionData, $type);

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

            $message = "🎉 *RESERVATION CONFIRMED* 🎉\n\n";
            $message .= "📍 *{$clubName}*\n";
            $message .= "Confirmation: #{$confirmationId}\n\n";
            $message .= "📅 Reservation Date: {$reservationDate}\n";
            $message .= "👥 Guests: {$menCount} Men + {$womenCount} Women = {$totalGuests} Total\n";
            $message .= "💰 Total: \${$totalAmount}\n\n";

            if (!empty($data['notes'])) {
                $message .= "📝 Notes: {$data['notes']}\n\n";
            }

            $message .= "✅ Your reservation is confirmed and ready!\n";
        } else {
            // Package type
            $packageName = $data['package_name'] ?? 'Package';
            $quantity = $data['quantity'] ?? 1;

            $message = "🎊 *BOOKING CONFIRMED* 🎊\n\n";
            $message .= "📍 *{$clubName}*\n";
            $message .= "Confirmation: #{$confirmationId}\n\n";
            $message .= "📦 Package: {$packageName}\n";
            $message .= "Qty: {$quantity}\n";
            $message .= "💰 Total: \${$totalAmount}\n\n";

            if (!empty($data['package_date']) || !empty($data['event_date'])) {
                $eventDate = $data['package_date'] ?? $data['event_date'] ?? 'N/A';
                $message .= "📅 Event Date: {$eventDate}\n\n";
            }

            $message .= "✅ Your booking is confirmed!\n";
        }

        // Add common footer info
        $message .= "🔗 View Details: {$this->getClubLink($data)}\n";
        $message .= "📞 Questions? Contact {$clubName}\n\n";
        $message .= "Thank you for your business! 🙏";

        return $message;
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
     * Send SMS via Aloware API
     */
    private function sendSms($phoneNumber, $message)
    {
        try {
            // Ensure phone number has country code
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);

            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'to' => $phoneNumber,
                    'body' => $message,
                    'type' => 'text', // or 'whatsapp' if supported
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($statusCode >= 200 && $statusCode < 300) {
                Log::info('SMS sent successfully', [
                    'phone' => $phoneNumber,
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
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 'unknown';

            Log::error('Aloware API request failed', [
                'phone' => $phoneNumber,
                'status' => $statusCode,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'API request failed: ' . $e->getMessage(),
                'status' => $statusCode
            ];
        } catch (\Exception $e) {
            Log::error('SMS sending exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Unexpected error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // If doesn't start with +, assume US and add +1
        if (!str_starts_with($cleaned, '+')) {
            // Remove leading 1 if present (for US)
            if (strlen($cleaned) === 11 && str_starts_with($cleaned, '1')) {
                $cleaned = substr($cleaned, 1);
            }
            $cleaned = '+1' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Test SMS sending
     */
    public function sendTest($phoneNumber, $message = null)
    {
        $testMessage = $message ?? "🧪 Test message from CartVIP SMS Service. If you received this, SMS notifications are working! ✅";

        return $this->sendSms($phoneNumber, $testMessage);
    }
}

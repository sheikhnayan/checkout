<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelnyxWebhookController extends Controller
{
    /**
     * Handle Telnyx SMS webhook notifications
     *
     * Receives delivery status updates for sent SMS messages
     */
    public function handleSmsWebhook(Request $request)
    {
        try {
            $payload = $request->json()->all();

            // Log the webhook
            Log::info('Telnyx webhook received', [
                'type' => $payload['type'] ?? 'unknown',
                'event_type' => $payload['data']['event_type'] ?? 'unknown',
                'message_id' => $payload['data']['id'] ?? 'unknown',
            ]);

            // Handle message.sent event
            if (($payload['data']['event_type'] ?? null) === 'message.sent') {
                $this->handleMessageSent($payload['data']);
            }

            // Handle message.finalized event
            if (($payload['data']['event_type'] ?? null) === 'message.finalized') {
                $this->handleMessageFinalized($payload['data']);
            }

            // Always return 200 OK to acknowledge receipt
            return response()->json(['status' => 'ok'], 200);

        } catch (\Exception $e) {
            Log::error('Telnyx webhook error: ' . $e->getMessage(), [
                'payload' => $request->json()->all()
            ]);

            // Still return 200 to prevent Telnyx from retrying
            return response()->json(['status' => 'ok'], 200);
        }
    }

    /**
     * Handle message sent event
     */
    private function handleMessageSent($data)
    {
        $messageId = $data['id'] ?? null;
        $toPhone = $data['to'][0]['phone_number'] ?? null;
        $status = $data['to'][0]['status'] ?? null;

        Log::info('SMS message sent', [
            'message_id' => $messageId,
            'to' => $toPhone,
            'status' => $status,
        ]);
    }

    /**
     * Handle message finalized event (delivery confirmation)
     */
    private function handleMessageFinalized($data)
    {
        $messageId = $data['id'] ?? null;
        $toPhone = $data['to'][0]['phone_number'] ?? null;
        $status = $data['to'][0]['status'] ?? null;
        $errors = $data['errors'] ?? [];

        if ($status === 'delivered') {
            Log::info('SMS message delivered', [
                'message_id' => $messageId,
                'to' => $toPhone,
            ]);
        } else if ($status === 'delivery_failed') {
            Log::error('SMS message failed to deliver', [
                'message_id' => $messageId,
                'to' => $toPhone,
                'errors' => $errors,
            ]);
        } else {
            Log::warning('SMS message status unknown', [
                'message_id' => $messageId,
                'to' => $toPhone,
                'status' => $status,
            ]);
        }
    }
}

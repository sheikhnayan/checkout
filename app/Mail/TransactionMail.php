<?php

namespace App\Mail;

use App\Models\Transaction;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;
    public $transaction;
    public $cartItems;
    public $priceBreakdown;
    public $website;
    public $includeQrInPdf;
    public $recipientType;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData, $transaction = null, $cartItems = null, $priceBreakdown = null, $website = null, bool $includeQrInPdf = true, string $recipientType = 'guest')
    {
        $this->mailData = $mailData;
        $this->transaction = $transaction;
        $this->cartItems = $cartItems;
        $this->priceBreakdown = $priceBreakdown;
        $this->website = $website;
        $this->includeQrInPdf = $includeQrInPdf;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $clubName = $this->mailData['club_name']
            ?? $this->mailData['website_name']
            ?? optional($this->website)->name
            ?? 'Venue';

        $confirmationNumber = $this->mailData['transaction_id']
            ?? optional($this->transaction)->transaction_id
            ?? 'Pending';

        $subject = $this->recipientType === 'manager'
            ? $clubName . ' - BOOKING - Confirmation # ' . $confirmationNumber
            : 'Booking Confirmed - ' . $clubName . ' - Confirmation # ' . $confirmationNumber;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $clubName = $this->mailData['club_name']
            ?? $this->mailData['website_name']
            ?? optional($this->website)->name
            ?? null;

        $mailData = $this->mailData;
        // Always use the cartItems parameter if provided, as it has the freshest data with all addons
        if ($this->cartItems !== null) {
            $mailData['cart_items'] = $this->cartItems;
        } elseif (!array_key_exists('cart_items', $mailData)) {
            $mailData['cart_items'] = [];
        }
        if (!array_key_exists('price_breakdown', $mailData) && $this->priceBreakdown !== null) {
            $mailData['price_breakdown'] = $this->priceBreakdown;
        }
        if (!array_key_exists('transaction_id', $mailData) && $this->transaction?->transaction_id) {
            $mailData['transaction_id'] = $this->transaction->transaction_id;
        }

        return new Content(
            view: 'emails.transaction',
            with: [
                'mailData' => $mailData,
                'clubName' => $clubName,
                'transaction' => $this->transaction,
                'recipientType' => $this->recipientType,
                'isManagerCopy' => $this->recipientType === 'manager',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (!$this->transaction || !$this->cartItems) {
            return [];
        }

        try {
            // Pre-fetch QR code as base64 so DomPDF can embed it without external HTTP
            $qrCodeBase64 = null;
            if ($this->includeQrInPdf && !empty($this->transaction->ticket_qr_code)) {
                try {
                    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . urlencode($this->transaction->ticket_qr_code);
                    $qrImageData = @file_get_contents($qrUrl);
                    if ($qrImageData !== false) {
                        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrImageData);
                    }
                } catch (\Exception $qrEx) {
                    // QR fetch failed, will fall back to text
                }
            }

            $pdf = Pdf::loadView('invoice-pdf', [
                'transaction' => $this->transaction,
                'cartItems' => $this->cartItems,
                'priceBreakdown' => $this->priceBreakdown,
                'website' => $this->website,
                'mailData' => $this->mailData,
                'qrCodeBase64' => $qrCodeBase64,
                'showQrInPdf' => $this->includeQrInPdf,
            ]);

            return [
                Attachment::fromData(
                    fn() => $pdf->output(),
                    'invoice-' . $this->transaction->transaction_id . '.pdf'
                )->withMime('application/pdf'),
            ];
        } catch (\Exception $e) {
            // If PDF generation fails, return no attachments
            \Log::warning('Invoice PDF generation failed', [
                'transaction_id' => $this->transaction->transaction_id ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}

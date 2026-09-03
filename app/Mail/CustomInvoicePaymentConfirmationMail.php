<?php

namespace App\Mail;

use App\Models\CustomInvoice;
use App\Models\Transaction;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomInvoicePaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $transaction;
    public $paymentType;
    public $website;
    public $payerFirstName;
    public $payerLastName;
    public $includeQrInPdf;
    public $recipientType;

    public function __construct(
        CustomInvoice $invoice,
        Transaction $transaction,
        string $paymentType,
        Website $website,
        string $payerFirstName = '',
        string $payerLastName = '',
        bool $includeQrInPdf = true,
        string $recipientType = 'guest'
    ) {
        $this->invoice = $invoice;
        $this->transaction = $transaction;
        $this->paymentType = $paymentType;
        $this->website = $website;
        $this->payerFirstName = $payerFirstName;
        $this->payerLastName = $payerLastName;
        $this->includeQrInPdf = $includeQrInPdf;
        $this->recipientType = $recipientType;
    }

    public function envelope(): Envelope
    {
        $subject = $this->recipientType === 'manager'
            ? 'Custom Invoice Payment Notification - ' . $this->transaction->transaction_id
            : 'Custom Invoice Payment Confirmation - ' . $this->transaction->transaction_id;

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $showQrInEmail = $this->shouldIncludeQrInEmail();

        return new Content(
            view: 'emails.custom-invoice-payment-confirmation',
            with: [
                'invoice' => $this->invoice,
                'transaction' => $this->transaction,
                'paymentType' => $this->paymentType,
                'website' => $this->website,
                'payerFirstName' => $this->payerFirstName,
                'payerLastName' => $this->payerLastName,
                'recipientType' => $this->recipientType,
                'isManagerCopy' => $this->recipientType === 'manager',
                'showQrInEmail' => $showQrInEmail,
            ],
        );
    }

    public function attachments(): array
    {
        if (!$this->invoice || !$this->transaction) {
            return [];
        }

        try {
            $qrCodeBase64 = null;
            $showQrInPdf = $this->shouldIncludeQrInPdf();
            if ($showQrInPdf && !empty($this->transaction->ticket_qr_code)) {
                try {
                    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($this->transaction->ticket_qr_code);
                    $qrImageData = @file_get_contents($qrUrl);
                    if ($qrImageData !== false) {
                        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrImageData);
                    }
                } catch (\Throwable $qrEx) {
                    // Fallback
                }
            }

            $pdf = Pdf::loadView('custom-invoice-pdf', [
                'invoice' => $this->invoice,
                'transaction' => $this->transaction,
                'paymentType' => $this->paymentType,
                'website' => $this->website,
                'qrCodeBase64' => $qrCodeBase64,
                'showQrInPdf' => $showQrInPdf,
            ]);

            return [
                Attachment::fromData(
                    fn() => $pdf->output(),
                    'invoice-' . $this->invoice->id . '.pdf'
                )->withMime('application/pdf'),
            ];
        } catch (\Exception $e) {
            // If PDF generation fails, return no attachments
            \Log::warning('Custom Invoice PDF generation failed', [
                'invoice_id' => $this->invoice->id ?? 'unknown',
                'transaction_id' => $this->transaction->transaction_id ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    private function shouldIncludeQrInEmail(): bool
    {
        return $this->recipientType !== 'manager';
    }

    private function shouldIncludeQrInPdf(): bool
    {
        return $this->recipientType !== 'manager' && $this->includeQrInPdf;
    }
}

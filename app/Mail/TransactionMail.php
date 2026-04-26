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

    /**
     * Create a new message instance.
     */
    public function __construct($mailData, $transaction = null, $cartItems = null, $priceBreakdown = null, $website = null)
    {
        $this->mailData = $mailData;
        $this->transaction = $transaction;
        $this->cartItems = $cartItems;
        $this->priceBreakdown = $priceBreakdown;
        $this->website = $website;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your CartVIP Booking Confirmation',
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

        return new Content(
            view: 'emails.transaction',
            with: [
                'mailData' => $this->mailData,
                'clubName' => $clubName,
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
            $pdf = Pdf::loadView('invoice-pdf', [
                'transaction' => $this->transaction,
                'cartItems' => $this->cartItems,
                'priceBreakdown' => $this->priceBreakdown,
                'website' => $this->website,
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

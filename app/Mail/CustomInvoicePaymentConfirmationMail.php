<?php

namespace App\Mail;

use App\Models\CustomInvoice;
use App\Models\Transaction;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomInvoicePaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $transaction;
    public $paymentType;
    public $website;
    public $payerFirstName;
    public $payerLastName;

    public function __construct(
        CustomInvoice $invoice,
        Transaction $transaction,
        string $paymentType,
        Website $website,
        string $payerFirstName = '',
        string $payerLastName = ''
    ) {
        $this->invoice = $invoice;
        $this->transaction = $transaction;
        $this->paymentType = $paymentType;
        $this->website = $website;
        $this->payerFirstName = $payerFirstName;
        $this->payerLastName = $payerLastName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Custom Invoice Payment Confirmation - ' . $this->transaction->transaction_id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.custom-invoice-payment-confirmation',
            with: [
                'invoice' => $this->invoice,
                'transaction' => $this->transaction,
                'paymentType' => $this->paymentType,
                'website' => $this->website,
                'payerFirstName' => $this->payerFirstName,
                'payerLastName' => $this->payerLastName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

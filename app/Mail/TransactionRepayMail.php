<?php

namespace App\Mail;

use App\Models\Transaction;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransactionRepayMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;
    public $website;
    public $repayUrl;
    public $customMessage;
    public $timezone;

    public function __construct(
        Transaction $transaction,
        ?Website $website = null,
        ?string $customMessage = null
    ) {
        $this->transaction = $transaction;
        $this->website = $website ?: $transaction->website;
        $this->repayUrl = $transaction->getRepayUrlAttribute();
        $this->customMessage = $customMessage;
        $this->timezone = optional($this->website)->resolved_timezone ?? 'America/Los_Angeles';
    }

    public function envelope(): Envelope
    {
        $clubName = optional($this->website)->name ?? 'CartVIP';

        return new Envelope(
            subject: 'Complete Your Reservation Payment for CartVIP - ' . $clubName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transaction-repay',
            with: [
                'transaction' => $this->transaction,
                'website' => $this->website,
                'repayUrl' => $this->repayUrl,
                'customMessage' => $this->customMessage,
                'timezone' => $this->timezone,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

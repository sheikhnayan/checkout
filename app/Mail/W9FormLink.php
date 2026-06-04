<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class W9FormLink extends Mailable
{
    use Queueable, SerializesModels;

    public $formUrl;
    public $recipientName;
    public $type;

    public function __construct($formUrl, $recipientName, $type = 'affiliate')
    {
        $this->formUrl = $formUrl;
        $this->recipientName = $recipientName;
        $this->type = $type;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Complete Your Tax Information - W-9 Form Required',
            from: 'hello@cartvip.com',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.w9-form-link',
            with: [
                'formUrl' => $this->formUrl,
                'recipientName' => $this->recipientName,
                'type' => $this->type,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

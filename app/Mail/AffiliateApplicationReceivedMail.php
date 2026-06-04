<?php

namespace App\Mail;

use App\Models\Affiliate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AffiliateApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Promoter $promoter;

    /**
     * Create a new message instance.
     */
    public function __construct(Promoter $promoter)
    {
        $this->promoter = $promoter;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Promoter Application Received - CartVIP')
            ->view('emails.promoter-application-received');
    }
}

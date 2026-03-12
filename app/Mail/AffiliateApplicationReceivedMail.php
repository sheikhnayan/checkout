<?php

namespace App\Mail;

use App\Models\Affiliate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AffiliateApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Affiliate $affiliate;

    /**
     * Create a new message instance.
     */
    public function __construct(Affiliate $affiliate)
    {
        $this->affiliate = $affiliate;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Affiliate Application Received - CartVIP')
            ->view('emails.affiliate-application-received');
    }
}

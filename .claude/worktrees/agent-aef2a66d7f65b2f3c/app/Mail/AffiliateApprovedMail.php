<?php

namespace App\Mail;

use App\Models\Affiliate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AffiliateApprovedMail extends Mailable
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
        return $this->subject('Your Affiliate Account Has Been Approved - CartVIP')
            ->view('emails.affiliate-approved');
    }
}

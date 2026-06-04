<?php

namespace App\Mail;

use App\Models\Promoter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AffiliateApprovedMail extends Mailable
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
        return $this->subject('Your Promoter Account Has Been Approved - CartVIP')
            ->view('emails.promoter-approved');
    }
}

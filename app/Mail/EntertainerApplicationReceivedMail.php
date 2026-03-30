<?php

namespace App\Mail;

use App\Models\Entertainer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EntertainerApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Entertainer $entertainer;

    public function __construct(Entertainer $entertainer)
    {
        $this->entertainer = $entertainer;
    }

    public function build(): self
    {
        return $this->subject('Entertainer Application Received - CartVIP')
            ->view('emails.entertainer-application-received');
    }
}

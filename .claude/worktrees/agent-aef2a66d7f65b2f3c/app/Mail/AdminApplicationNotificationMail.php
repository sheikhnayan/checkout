<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminApplicationNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $applicantType;
    public string $status;
    public string $name;
    public string $email;
    public string $websiteName;
    public ?string $rejectionReason;

    public function __construct(string $applicantType, string $status, string $name, string $email, string $websiteName = '', ?string $rejectionReason = null)
    {
        $this->applicantType = $applicantType;
        $this->status = $status;
        $this->name = $name;
        $this->email = $email;
        $this->websiteName = $websiteName;
        $this->rejectionReason = $rejectionReason;
    }

    public function build(): self
    {
        $subject = sprintf('%s Application %s - CartVIP', $this->applicantType, ucfirst($this->status));

        return $this->subject($subject)
            ->view('emails.admin-application-notification');
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminApplicationNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $applicantType;
    public string $registrationType;
    public string $name;
    public string $email;
    public ?string $phone;
    public string $websiteName;
    public string $submittedAt;
    public ?string $additionalInfo;

    public function __construct(
        string $applicantType,
        string $registrationType,
        string $name,
        string $email,
        string $websiteName = '',
        ?string $phone = null,
        ?string $additionalInfo = null
    ) {
        $this->applicantType = $applicantType;
        $this->registrationType = $registrationType;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->websiteName = $websiteName;
        $this->submittedAt = now()->timezone('America/Los_Angeles')->format('M d, Y \a\t h:i A PT');
        $this->additionalInfo = $additionalInfo;
    }

    public function build(): self
    {
        // For affiliates, don't show clubLabel
        $clubLabel = '';
        if ($this->applicantType !== 'Affiliate' && !empty($this->websiteName)) {
            $clubLabel = " - {$this->websiteName}";
        }

        // Replace "Affiliate" with "Promoter" in subject
        $displayType = $this->applicantType === 'Affiliate' ? 'Promoter' : $this->applicantType;
        $subject = "New {$displayType} Registration Received{$clubLabel}";

        return $this->subject($subject)
            ->view('emails.admin-application-notification');
    }
}

<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminCreatedUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $password;
    public string $userTypeLabel;

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
        $this->userTypeLabel = match ($user->user_type) {
            'manager' => 'Website Manager',
            'website_user' => 'Website Staff',
            'bouncer' => 'Bouncer',
            default => 'Portal User',
        };
    }

    public function build(): self
    {
        return $this->subject('Your CartVIP Portal Account Is Ready')
            ->view('emails.admin-created-user');
    }
}

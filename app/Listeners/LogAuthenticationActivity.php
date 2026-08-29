<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;

class LogAuthenticationActivity
{
    /**
     * Handle user login event.
     */
    public function handleLogin(Login $event): void
    {
        $user = $event->user;
        if ($user) {
            $roleLabel = ucfirst($user->user_type ?? 'User');
            if (method_exists($user, 'isWebsiteAdmin') && $user->isWebsiteAdmin()) {
                $roleLabel = 'Website Admin';
            }
            ActivityLogger::logAuth(
                'login',
                "User {$user->name} ({$user->email}) logged into the system as {$roleLabel}.",
                $user
            );
        }
    }

    /**
     * Handle user logout event.
     */
    public function handleLogout(Logout $event): void
    {
        $user = $event->user;
        if ($user) {
            ActivityLogger::logAuth(
                'logout',
                "User {$user->name} ({$user->email}) logged out of the system.",
                $user
            );
        }
    }

    /**
     * Handle failed login attempt event.
     */
    public function handleFailed(Failed $event): void
    {
        $email = $event->credentials['email'] ?? 'Unknown Email';
        $user = $event->user;

        ActivityLogger::logAuth(
            'failed_login',
            "Failed login attempt for email: {$email}.",
            $user,
            ['credentials_email' => $email]
        );
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events): array
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailed',
        ];
    }
}

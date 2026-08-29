<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an activity record.
     *
     * @param string $action Verb/action identifier (e.g. 'login', 'logout', 'create', 'update', 'delete', 'check_in', 'export')
     * @param string $description Human-readable summary of what occurred
     * @param string $module Component or feature area (e.g. 'auth', 'transactions', 'events', 'packages', 'users', 'nightly_reports')
     * @param mixed|null $subject Target model or entity
     * @param int|null $websiteId Associated club/website ID
     * @param User|null $user Performing user (defaults to auth()->user())
     * @param array|null $changes Optional payload diff or metadata
     * @return ActivityLog|null
     */
    public static function log(
        string $action,
        string $description,
        string $module = 'general',
        $subject = null,
        ?int $websiteId = null,
        ?User $user = null,
        ?array $changes = null
    ): ?ActivityLog {
        try {
            $user = $user ?: auth()->user();

            $resolvedWebsiteId = $websiteId;
            if (!$resolvedWebsiteId && $user) {
                $resolvedWebsiteId = $user->website_id;
            }
            if (!$resolvedWebsiteId && $subject && isset($subject->website_id)) {
                $resolvedWebsiteId = $subject->website_id;
            }

            $ipAddress = Request::ip();
            $userAgent = Request::header('User-Agent');

            return ActivityLog::create([
                'user_id'      => $user?->id,
                'user_name'    => $user?->name ?? 'System/Guest',
                'user_email'   => $user?->email,
                'user_type'    => $user?->user_type,
                'website_id'   => $resolvedWebsiteId,
                'action'       => strtolower($action),
                'module'       => strtolower($module),
                'description'  => $description,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id'   => $subject ? ($subject->id ?? null) : null,
                'changes'      => $changes,
                'ip_address'   => $ipAddress,
                'user_agent'   => $userAgent,
            ]);
        } catch (\Throwable $e) {
            // Fail silently to avoid breaking primary user flows
            logger()->error('Failed to log activity: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Log authentication events (login, logout, failed attempt).
     */
    public static function logAuth(string $action, string $description, ?User $user = null, ?array $meta = null): ?ActivityLog
    {
        return self::log($action, $description, 'auth', null, $user?->website_id, $user, $meta);
    }
}

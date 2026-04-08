<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'key',
        'module',
        'description',
        'is_super_admin_only',
    ];

    protected $casts = [
        'is_super_admin_only' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(WebsiteRole::class, 'permission_website_role', 'permission_id', 'website_role_id');
    }

    public static function syncFromAdminRoutes(): void
    {
        $routeNames = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn ($name) => is_string($name) && str_starts_with($name, 'admin.'))
            ->unique()
            ->values();

        foreach ($routeNames as $routeName) {
            $parts = explode('.', $routeName);
            $module = $parts[1] ?? 'general';

            $superAdminOnly = str_starts_with($routeName, 'admin.website.')
                || str_starts_with($routeName, 'admin.setting.');

            // Payment settings is website-level and allowed for website admins.
            if (str_starts_with($routeName, 'admin.website.payment-settings')) {
                $superAdminOnly = false;
            }

            static::updateOrCreate(
                ['key' => $routeName],
                [
                    'name' => self::humanizeRouteName($routeName),
                    'module' => $module,
                    'description' => 'Access to ' . $routeName,
                    'is_super_admin_only' => $superAdminOnly,
                ]
            );
        }
    }

    private static function humanizeRouteName(string $routeName): string
    {
        $labels = [
            'admin' => 'Admin',
            'index' => 'View List',
            'show' => 'View Details',
            'create' => 'Open Create Form',
            'store' => 'Create',
            'edit' => 'Open Edit Form',
            'update' => 'Update',
            'archive' => 'Archive / Restore',
            'destroy' => 'Delete',
            'toggle-status' => 'Toggle Status',
            'scan' => 'Scanner Screen',
            'lookup' => 'Lookup Ticket',
            'check-in' => 'Confirm Check-In',
            'update-password' => 'Change Password',
            'payment-settings' => 'Payment Settings',
        ];

        return collect(explode('.', $routeName))
            ->map(function ($part) use ($labels) {
                if (isset($labels[$part])) {
                    return $labels[$part];
                }

                return ucfirst(str_replace(['-', '_'], ' ', $part));
            })
            ->implode(' - ');
    }
}

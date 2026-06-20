<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Website IDs the current user may act on.
     * Admin → all, manager → allocated sites, website user / bouncer → their single site.
     */
    protected function currentAccessibleWebsiteIds(): array
    {
        $user = auth()->user();
        return $user ? $user->accessibleWebsiteIds() : [];
    }

    /**
     * Abort with 403 unless the current user may act on the given website.
     * Admins always pass; everyone else must have the website in their accessible set.
     */
    protected function authorizeWebsiteAccess($websiteId, string $message = 'Access denied.'): void
    {
        $user = auth()->user();
        if ($user && $user->isAdmin()) {
            return;
        }
        if (!$user || $websiteId === null || !in_array((int) $websiteId, $this->currentAccessibleWebsiteIds(), true)) {
            abort(403, $message);
        }
    }
}

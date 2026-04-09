<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WebsiteRole;
use App\Models\Permission;

class GrantSidebarPermissionsToAffiliatesAndEntertainers extends Command
{
    protected $signature = 'rbac:grant-sidebar-permissions';
    protected $description = 'Grant all sidebar-linked permissions to affiliate and entertainer roles';

    public function handle()
    {
        $sidebarRouteKeys = [
            // Entertainer sidebar
            'admin.feed-post.index',
            'admin.profile.edit',
            'entertainer.portal.dashboard',
            'entertainer.portal.packages',
            'entertainer.portal.settings',
            'entertainer.portal.wallet',
            // Affiliate sidebar
            'affiliate.portal.dashboard',
            'affiliate.portal.packages',
            'affiliate.portal.settings',
            'affiliate.portal.wallet',
            // Profile page (common)
            'admin.profile.edit',
        ];

        $roles = WebsiteRole::whereIn('slug', ['affiliate', 'entertainer'])->get();
        $permissions = Permission::whereIn('key', $sidebarRouteKeys)->get();

        foreach ($roles as $role) {
            $role->permissions()->syncWithoutDetaching($permissions->pluck('id')->all());
            $this->info("Granted sidebar permissions to role: {$role->name}");
        }

        $this->info('Sidebar permissions granted to affiliate and entertainer roles.');
    }
}

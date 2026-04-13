<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $keys = [
        'admin.popup.index',
        'admin.popup.show',
        'admin.popup.create',
        'admin.popup.store',
        'admin.popup.edit',
        'admin.popup.update',
        'admin.popup.archive',
        'admin.popup.unarchive',
    ];

    public function up(): void
    {
        foreach ($this->keys as $key) {
            DB::table('permissions')->updateOrInsert(
                ['key' => $key],
                [
                    'name' => $this->humanize($key),
                    'module' => 'popup',
                    'description' => 'Access to ' . $key,
                    'is_super_admin_only' => false,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $permissionIds = DB::table('permissions')->whereIn('key', $this->keys)->pluck('id');
        $websiteAdminRoleIds = DB::table('website_roles')->where('is_website_admin', 1)->pluck('id');

        foreach ($websiteAdminRoleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                $exists = DB::table('permission_website_role')
                    ->where('website_role_id', $roleId)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (!$exists) {
                    DB::table('permission_website_role')->insert([
                        'website_role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $permissionIds = DB::table('permissions')->whereIn('key', $this->keys)->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            DB::table('permission_website_role')->whereIn('permission_id', $permissionIds)->delete();
        }

        DB::table('permissions')->whereIn('key', $this->keys)->delete();
    }

    private function humanize(string $routeKey): string
    {
        return collect(explode('.', $routeKey))
            ->map(function (string $part) {
                return ucfirst(str_replace(['-', '_'], ' ', $part));
            })
            ->implode(' - ');
    }
};

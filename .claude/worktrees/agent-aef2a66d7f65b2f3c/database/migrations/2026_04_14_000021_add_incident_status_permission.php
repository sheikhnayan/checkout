<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $key = 'admin.incident.status.update';

    public function up(): void
    {
        DB::table('permissions')->updateOrInsert(
            ['key' => $this->key],
            [
                'name' => 'Admin - Incident - Status - Update',
                'module' => 'incident',
                'description' => 'Access to ' . $this->key,
                'is_super_admin_only' => false,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $permissionId = DB::table('permissions')->where('key', $this->key)->value('id');
        $websiteAdminRoleIds = DB::table('website_roles')->where('is_website_admin', 1)->pluck('id');

        foreach ($websiteAdminRoleIds as $roleId) {
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

    public function down(): void
    {
        $permissionId = DB::table('permissions')->where('key', $this->key)->value('id');

        if ($permissionId) {
            DB::table('permission_website_role')->where('permission_id', $permissionId)->delete();
        }

        DB::table('permissions')->where('key', $this->key)->delete();
    }
};

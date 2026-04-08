<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use App\Models\Website;
use App\Models\WebsiteRole;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class RbacAccessAndMenuTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureMinimalSchema();
        $this->truncateCoreTables();

        $this->seedPermissionKeys([
            'admin.website-users.index' => false,
            'admin.website-roles.index' => false,
            'admin.transaction.index' => false,
            'admin.transaction.scan' => false,
            'admin.website.index' => true,
        ]);
    }

    public function test_super_admin_has_full_route_access_including_super_only_routes(): void
    {
        $admin = User::factory()->create([
            'user_type' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.transaction.scan'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.website.index'))
            ->assertOk();
    }

    public function test_website_user_is_limited_by_assigned_permissions_and_super_admin_guard(): void
    {
        $website = $this->createWebsite();

        $role = $this->createRole($website, [
            'admin.transaction.scan',
        ]);

        $websiteUser = User::factory()->create([
            'user_type' => 'website_user',
            'website_id' => $website->id,
            'website_role_id' => $role->id,
        ]);

        $this->actingAs($websiteUser)
            ->get(route('admin.transaction.scan'))
            ->assertOk();

        $this->actingAs($websiteUser)
            ->get(route('admin.website.index'))
            ->assertForbidden();

        $this->actingAs($websiteUser)
            ->get(route('admin.transaction.index'))
            ->assertForbidden();
    }

    public function test_website_admin_user_is_still_permission_bound_by_route_permission_middleware(): void
    {
        $website = $this->createWebsite();

        $allowedAdminRole = $this->createRole(
            $website,
            [
                'admin.website-users.index',
            ],
            true
        );

        $websiteAdminWithPermission = User::factory()->create([
            'user_type' => 'website_user',
            'website_id' => $website->id,
            'website_role_id' => $allowedAdminRole->id,
        ]);

        $this->actingAs($websiteAdminWithPermission)
            ->get(route('admin.website-users.index'))
            ->assertOk();

        $blockedAdminRole = $this->createRole($website, [], true);

        $websiteAdminWithoutPermission = User::factory()->create([
            'user_type' => 'website_user',
            'website_id' => $website->id,
            'website_role_id' => $blockedAdminRole->id,
        ]);

        $this->actingAs($websiteAdminWithoutPermission)
            ->get(route('admin.website-users.index'))
            ->assertForbidden();
    }

    public function test_sidebar_only_shows_menu_items_for_granted_permissions(): void
    {
        $website = $this->createWebsite();

        $limitedRole = $this->createRole($website, [
            'admin.transaction.index',
        ]);

        $limitedUser = User::factory()->create([
            'user_type' => 'website_user',
            'website_id' => $website->id,
            'website_role_id' => $limitedRole->id,
        ]);

        $this->actingAs($limitedUser)
            ->get(route('admin.profile.edit'))
            ->assertOk()
            ->assertSee('Transactions', false)
            ->assertDontSee('Ticket Scanner', false)
            ->assertDontSee('Website Users', false)
            ->assertDontSee('Website Roles', false)
            ->assertDontSee('Platform Settings', false);

        $managerRole = $this->createRole($website, [
            'admin.website-users.index',
            'admin.website-roles.index',
            'admin.transaction.index',
            'admin.transaction.scan',
        ]);

        $manager = User::factory()->create([
            'user_type' => 'website_user',
            'website_id' => $website->id,
            'website_role_id' => $managerRole->id,
        ]);

        $this->actingAs($manager)
            ->get(route('admin.profile.edit'))
            ->assertOk()
            ->assertSee('Website Users', false)
            ->assertSee('Website Roles', false)
            ->assertSee('Transactions', false)
            ->assertSee('Ticket Scanner', false)
            ->assertDontSee('Platform Settings', false)
            ->assertDontSee('Websites', false);
    }

    private function ensureMinimalSchema(): void
    {
        if (!Schema::hasTable('websites')) {
            Schema::create('websites', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('domain')->nullable();
                $table->string('status')->nullable();
                $table->integer('is_archieved')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->unsignedBigInteger('website_id')->nullable();
                $table->unsignedBigInteger('website_role_id')->nullable();
                $table->string('user_type')->default('admin');
                $table->rememberToken();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('key')->unique();
                $table->string('module')->index();
                $table->text('description')->nullable();
                $table->boolean('is_super_admin_only')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('website_roles')) {
            Schema::create('website_roles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('website_id')->nullable();
                $table->string('name');
                $table->string('slug');
                $table->text('description')->nullable();
                $table->boolean('is_website_admin')->default(false);
                $table->boolean('is_system')->default(false);
                $table->timestamps();
                $table->unique(['website_id', 'slug']);
            });
        }

        if (!Schema::hasTable('permission_website_role')) {
            Schema::create('permission_website_role', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('website_role_id');
                $table->timestamps();
                $table->unique(['permission_id', 'website_role_id']);
            });
        }
    }

    private function truncateCoreTables(): void
    {
        DB::table('permission_website_role')->delete();
        DB::table('permissions')->delete();
        DB::table('users')->delete();
        DB::table('website_roles')->delete();
        DB::table('websites')->delete();
    }

    private function seedPermissionKeys(array $permissionMap): void
    {
        foreach ($permissionMap as $key => $isSuperAdminOnly) {
            Permission::updateOrCreate(
                ['key' => $key],
                [
                    'name' => $key,
                    'module' => 'rbac-test',
                    'description' => $key,
                    'is_super_admin_only' => (bool) $isSuperAdminOnly,
                ]
            );
        }
    }

    private function createWebsite(): Website
    {
        return Website::create([
            'name' => 'Test Club ' . Str::random(6),
            'domain' => Str::random(8) . '.test',
            'status' => '1',
            'is_archieved' => 0,
        ]);
    }

    private function createRole(Website $website, array $permissionKeys, bool $isWebsiteAdmin = false): WebsiteRole
    {
        $role = WebsiteRole::create([
            'website_id' => $website->id,
            'name' => $isWebsiteAdmin ? 'Website Admin ' . Str::random(4) : 'Staff ' . Str::random(4),
            'slug' => ($isWebsiteAdmin ? 'website-admin-' : 'staff-') . Str::lower(Str::random(8)),
            'description' => 'Test role',
            'is_website_admin' => $isWebsiteAdmin,
            'is_system' => false,
        ]);

        $permissionIds = Permission::query()
            ->whereIn('key', $permissionKeys)
            ->pluck('id')
            ->all();

        $role->permissions()->sync($permissionIds);

        return $role;
    }
}

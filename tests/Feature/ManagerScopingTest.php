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

/**
 * Regression guard for multi-tenant "manager" scoping.
 *
 * These lock in the access-control fixes so they cannot silently break:
 *  - accessibleWebsiteIds() returns the correct website set per user type
 *  - a manager can reach the ticket scanner when permitted (the original bug)
 *  - the permission middleware still blocks a manager without the permission
 *  - opening the scanner is implied by the Lookup Ticket / Confirm Check-In permissions
 */
class ManagerScopingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureMinimalSchema();
        $this->truncateCoreTables();

        $this->seedPermissionKeys([
            'admin.transaction.index' => false,
            'admin.transaction.scan' => false,
            'admin.transaction.scan.lookup' => false,
            'admin.transaction.scan.check-in' => false,
        ]);
    }

    public function test_accessible_website_ids_are_scoped_per_user_type(): void
    {
        $a = $this->createWebsite();
        $b = $this->createWebsite();
        $c = $this->createWebsite();

        // Admin → every website.
        $admin = User::factory()->create(['user_type' => 'admin']);
        $this->assertEqualsCanonicalizing([$a->id, $b->id, $c->id], $admin->accessibleWebsiteIds());

        // Website user → only their single site.
        $websiteUser = User::factory()->create(['user_type' => 'website_user', 'website_id' => $b->id]);
        $this->assertSame([$b->id], $websiteUser->accessibleWebsiteIds());

        // Manager → only their allocated sites (a + c), never b.
        $manager = User::factory()->create(['user_type' => 'manager', 'website_id' => null]);
        $manager->managedWebsites()->attach([$a->id, $c->id]);
        $this->assertEqualsCanonicalizing([$a->id, $c->id], $manager->accessibleWebsiteIds());

        // Manager with no allocation → empty set (fail-closed, never "all").
        $orphan = User::factory()->create(['user_type' => 'manager', 'website_id' => null]);
        $this->assertSame([], $orphan->accessibleWebsiteIds());
    }

    public function test_manager_can_open_the_scanner_when_granted(): void
    {
        $website = $this->createWebsite();
        $role = $this->createRole($website, ['admin.transaction.scan']);

        $manager = User::factory()->create([
            'user_type' => 'manager',
            'website_id' => null,
            'website_role_id' => $role->id,
        ]);
        $manager->managedWebsites()->attach([$website->id]);

        $this->actingAs($manager)
            ->get(route('admin.transaction.scan'))
            ->assertOk();
    }

    public function test_manager_without_scanner_permission_is_forbidden(): void
    {
        $website = $this->createWebsite();
        $role = $this->createRole($website, ['admin.transaction.index']); // no scan permission

        $manager = User::factory()->create([
            'user_type' => 'manager',
            'website_id' => null,
            'website_role_id' => $role->id,
        ]);
        $manager->managedWebsites()->attach([$website->id]);

        $this->actingAs($manager)
            ->get(route('admin.transaction.scan'))
            ->assertForbidden();
    }

    public function test_scanner_page_is_implied_by_lookup_or_checkin_permission(): void
    {
        $website = $this->createWebsite();

        // Role granted only "Lookup Ticket" — should still imply the scanner page.
        $lookupOnly = $this->createRole($website, ['admin.transaction.scan.lookup']);
        $manager = User::factory()->create([
            'user_type' => 'manager',
            'website_id' => null,
            'website_role_id' => $lookupOnly->id,
        ]);
        $this->assertTrue($manager->hasRoutePermission('admin.transaction.scan'));

        // Role with none of the scanner permissions — no implied access.
        $noScan = $this->createRole($website, ['admin.transaction.index']);
        $other = User::factory()->create([
            'user_type' => 'manager',
            'website_id' => null,
            'website_role_id' => $noScan->id,
        ]);
        $this->assertFalse($other->hasRoutePermission('admin.transaction.scan'));
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

        if (!Schema::hasTable('manager_websites')) {
            Schema::create('manager_websites', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('website_id');
                $table->timestamps();
                $table->unique(['user_id', 'website_id']);
            });
        }

        // The admin sidebar renders a pending-feed-post badge, so the view needs this table.
        if (!Schema::hasTable('feed_posts')) {
            Schema::create('feed_posts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('website_id')->nullable();
                $table->boolean('review_required')->default(false);
                $table->timestamps();
            });
        }
    }

    private function truncateCoreTables(): void
    {
        DB::table('manager_websites')->delete();
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
                    'module' => 'manager-test',
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
            'name' => 'Manager Role ' . Str::random(4),
            'slug' => 'manager-role-' . Str::lower(Str::random(8)),
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

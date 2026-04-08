<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\User;
use App\Models\Website;
use App\Models\WebsiteRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EnsureWebsiteAdminUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ensure-website-admin-users {--dry-run : Show changes without writing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync admin route permissions and ensure every website has an assigned website admin user and role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Permission::syncFromAdminRoutes();

        $dryRun = (bool) $this->option('dry-run');
        $createdUsers = [];

        $assignablePermissionIds = Permission::query()
            ->where('is_super_admin_only', false)
            ->pluck('id')
            ->all();

        $transactionPermissionIds = Permission::query()
            ->whereIn('key', [
                'admin.transaction.index',
                'admin.transaction.show',
                'admin.transaction.update',
                'admin.transaction.scan',
                'admin.transaction.scan.lookup',
                'admin.transaction.scan.check-in',
            ])
            ->pluck('id')
            ->all();

        $scannerPermissionIds = Permission::query()
            ->whereIn('key', [
                'admin.transaction.scan',
                'admin.transaction.scan.lookup',
                'admin.transaction.scan.check-in',
            ])
            ->pluck('id')
            ->all();

        $websites = Website::query()->orderBy('id')->get();

        foreach ($websites as $website) {
            $adminRole = WebsiteRole::firstOrCreate(
                [
                    'website_id' => $website->id,
                    'slug' => 'website-admin',
                ],
                [
                    'name' => 'Website Admin',
                    'description' => 'Full access for this website except super-admin-only platform features.',
                    'is_website_admin' => true,
                    'is_system' => true,
                ]
            );

            if (!$dryRun) {
                $adminRole->permissions()->sync($assignablePermissionIds);
            }

            $transactionRole = WebsiteRole::firstOrCreate(
                [
                    'website_id' => $website->id,
                    'slug' => 'transaction-staff',
                ],
                [
                    'name' => 'Transaction Staff',
                    'description' => 'Access to transactions and scanner only.',
                    'is_website_admin' => false,
                    'is_system' => true,
                ]
            );

            $bouncerRole = WebsiteRole::firstOrCreate(
                [
                    'website_id' => $website->id,
                    'slug' => 'bouncer',
                ],
                [
                    'name' => 'Bouncer',
                    'description' => 'Access to ticket scanner only.',
                    'is_website_admin' => false,
                    'is_system' => true,
                ]
            );

            if (!$dryRun) {
                $transactionRole->permissions()->sync($transactionPermissionIds);
                $bouncerRole->permissions()->sync($scannerPermissionIds);
            }

            // Always backfill missing staff role assignments, regardless of whether
            // a website admin already exists for this website.
            $websiteUsersWithoutRole = User::query()
                ->where('website_id', $website->id)
                ->whereIn('user_type', ['website_user', 'bouncer'])
                ->whereNull('website_role_id')
                ->get();

            foreach ($websiteUsersWithoutRole as $staffUser) {
                if ($dryRun) {
                    continue;
                }

                $staffUser->website_role_id = $staffUser->user_type === 'bouncer'
                    ? $bouncerRole->id
                    : $transactionRole->id;
                $staffUser->save();
            }

            $existingAdmin = User::query()
                ->where('website_id', $website->id)
                ->whereIn('user_type', ['website_user', 'bouncer'])
                ->where('website_role_id', $adminRole->id)
                ->first();

            if ($existingAdmin) {
                $this->line("Website #{$website->id} already has website admin user: {$existingAdmin->email}");
                continue;
            }

            $candidate = User::query()
                ->where('website_id', $website->id)
                ->whereIn('user_type', ['website_user', 'bouncer'])
                ->orderBy('id')
                ->first();

            if ($candidate) {
                if (!$dryRun) {
                    $candidate->website_role_id = $adminRole->id;
                    if ($candidate->user_type === 'bouncer') {
                        $candidate->user_type = 'website_user';
                    }
                    $candidate->save();
                }

                $this->info("Assigned existing user as website admin for website #{$website->id}: {$candidate->email}");
                continue;
            }

            $generatedEmail = $this->resolveWebsiteAdminEmail($website);
            $plainPassword = Str::password(12);

            if (!$dryRun) {
                User::create([
                    'name' => trim(($website->name ?? 'Website') . ' Admin'),
                    'email' => $generatedEmail,
                    'password' => Hash::make($plainPassword),
                    'website_id' => $website->id,
                    'website_role_id' => $adminRole->id,
                    'user_type' => 'website_user',
                ]);
            }

            $createdUsers[] = [
                'website_id' => $website->id,
                'website_name' => (string) $website->name,
                'email' => $generatedEmail,
                'password' => $plainPassword,
            ];

            $this->warn("Created website admin user for website #{$website->id}: {$generatedEmail}");
        }

        if (!empty($createdUsers)) {
            $this->table(['Website ID', 'Website', 'Email', 'Temporary Password'], $createdUsers);
        }

        $this->info('Website admin and role sync complete.');
        return self::SUCCESS;
    }

    private function resolveWebsiteAdminEmail(Website $website): string
    {
        $base = filter_var($website->email, FILTER_VALIDATE_EMAIL)
            ? strtolower((string) $website->email)
            : 'website-admin-' . $website->id . '@local.cartvip';

        if (!User::where('email', $base)->exists()) {
            return $base;
        }

        $namePart = Str::slug((string) $website->name, '.');
        if ($namePart === '') {
            $namePart = 'website';
        }

        $counter = 1;
        do {
            $candidate = $namePart . '.admin' . $counter . '@local.cartvip';
            $counter++;
        } while (User::where('email', $candidate)->exists());

        return $candidate;
    }
}

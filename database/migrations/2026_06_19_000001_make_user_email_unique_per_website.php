<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website admins (user_type = 'website_user') may share one email across multiple
 * websites. So the email is no longer globally unique — it's unique PER website.
 * Uniqueness for every other role (affiliate/entertainer/admin/etc.) is still
 * enforced in application logic, so "other unique email" behaviour is preserved.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the global unique index on email.
            try { $table->dropUnique('users_email_unique'); } catch (\Throwable $e) {}
            // Same email cannot be used twice for the SAME website, but may repeat across websites.
            try { $table->unique(['email', 'website_id'], 'users_email_website_id_unique'); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            try { $table->dropUnique('users_email_website_id_unique'); } catch (\Throwable $e) {}
            try { $table->unique('email', 'users_email_unique'); } catch (\Throwable $e) {}
        });
    }
};

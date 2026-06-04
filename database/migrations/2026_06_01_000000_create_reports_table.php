<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Main reports table
        if (!Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('category'); // 'Sales', 'Orders', 'Acquisition', 'Entertainer', 'Package', 'Customer', 'Event', 'Financial'
                $table->string('type'); // 'chart', 'table', 'metric'
                $table->json('available_filters')->nullable(); // date_range, status, website, promoter, entertainer, event, package, etc.
                $table->json('default_date_range')->nullable(); // ['period' => 'last_30_days']
                $table->boolean('is_active')->default(true);
                $table->integer('display_order')->default(0);
                $table->timestamps();
            });
        }

        // User-saved reports and preferences
        if (!Schema::hasTable('user_report_preferences')) {
            Schema::create('user_report_preferences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->index();
                $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
                $table->string('name'); // Custom name for saved report
                $table->json('filters'); // Saved filter values
                $table->json('columns')->nullable(); // For table reports, which columns to display
                $table->boolean('is_favorite')->default(false);
                $table->dateTime('last_run_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'report_id', 'name']);
            });
        }

        // Report exports/scheduled exports
        if (!Schema::hasTable('report_exports')) {
            Schema::create('report_exports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->index();
                $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
                $table->string('format'); // 'csv', 'pdf', 'excel'
                $table->json('filters');
                $table->string('file_path')->nullable();
                $table->string('status')->default('pending'); // pending, completed, failed
                $table->string('email_recipient')->nullable();
                $table->text('error_message')->nullable();
                $table->dateTime('scheduled_at')->nullable();
                $table->dateTime('exported_at')->nullable();
                $table->timestamps();
            });
        }

        // Report permissions linking (tracks which roles can access which reports)
        if (!Schema::hasTable('report_permissions')) {
            Schema::create('report_permissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
                $table->string('user_type')->nullable(); // 'admin', 'bouncer', 'manager', null for all
                $table->foreignId('website_role_id')->nullable()->constrained('website_roles')->onDelete('cascade');
                $table->foreignId('affiliate_id')->nullable()->constrained('promoters')->onDelete('cascade');
                $table->foreignId('entertainer_id')->nullable()->constrained('entertainers')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['report_id', 'user_type', 'website_role_id', 'affiliate_id', 'entertainer_id'], 'rpt_perm_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_permissions');
        Schema::dropIfExists('report_exports');
        Schema::dropIfExists('user_report_preferences');
        Schema::dropIfExists('reports');
    }
};

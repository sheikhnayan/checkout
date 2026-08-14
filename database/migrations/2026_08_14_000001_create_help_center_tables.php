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
        // 1. Help Center Pages Table
        if (!Schema::hasTable('help_center_pages')) {
            Schema::create('help_center_pages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('banner_color')->default('#4f46e5');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Help Center Sections Table
        if (!Schema::hasTable('help_center_sections')) {
            Schema::create('help_center_sections', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('help_center_page_id')->index();
                $table->string('title');
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 3. Help Center Items Table (Form links or External URLs)
        if (!Schema::hasTable('help_center_items')) {
            Schema::create('help_center_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('help_center_section_id')->index();
                $table->enum('type', ['form', 'external'])->default('form');
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedBigInteger('custom_form_id')->nullable()->index();
                $table->string('url')->nullable();
                $table->string('icon')->default('bx-link');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 4. Help Center Collaborators Table
        if (!Schema::hasTable('help_center_collaborators')) {
            Schema::create('help_center_collaborators', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('help_center_page_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('invited_by_user_id')->index();
                $table->string('email');
                $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
                $table->string('invitation_token')->nullable()->unique();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('help_center_collaborators');
        Schema::dropIfExists('help_center_items');
        Schema::dropIfExists('help_center_sections');
        Schema::dropIfExists('help_center_pages');
    }
};

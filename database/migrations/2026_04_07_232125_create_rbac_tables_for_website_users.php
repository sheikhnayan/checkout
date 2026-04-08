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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->string('module')->index();
            $table->text('description')->nullable();
            $table->boolean('is_super_admin_only')->default(false);
            $table->timestamps();
        });

        Schema::create('website_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('website_id')->nullable()->index();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->boolean('is_website_admin')->default(false);
            $table->boolean('is_system')->default(false);
            $table->timestamps();

            $table->foreign('website_id')->references('id')->on('websites')->onDelete('cascade');
            $table->unique(['website_id', 'slug']);
        });

        Schema::create('permission_website_role', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('website_role_id');
            $table->timestamps();

            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->foreign('website_role_id')->references('id')->on('website_roles')->onDelete('cascade');
            $table->unique(['permission_id', 'website_role_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('website_role_id')->nullable()->after('website_id');
            $table->foreign('website_role_id')->references('id')->on('website_roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['website_role_id']);
            $table->dropColumn('website_role_id');
        });

        Schema::dropIfExists('permission_website_role');
        Schema::dropIfExists('website_roles');
        Schema::dropIfExists('permissions');
    }
};

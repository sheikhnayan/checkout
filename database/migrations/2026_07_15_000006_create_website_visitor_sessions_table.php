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
        if (!Schema::hasTable('website_visitor_sessions')) {
            Schema::create('website_visitor_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('website_id')->constrained('websites')->onDelete('cascade');
                $table->string('session_id', 120);
                $table->string('visitor_key', 64)->nullable();
                $table->string('landing_path', 500)->nullable();
                $table->string('referrer_host')->nullable();
                $table->string('utm_source')->nullable();
                $table->string('utm_medium')->nullable();
                $table->string('utm_campaign')->nullable();
                $table->string('utm_term')->nullable();
                $table->string('utm_content')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 1024)->nullable();
                $table->unsignedInteger('page_views')->default(1);
                $table->timestamp('first_seen_at')->nullable();
                $table->timestamp('last_seen_at')->nullable();
                $table->timestamps();

                $table->unique(['website_id', 'session_id'], 'website_visitor_sessions_unique');
                $table->index('visitor_key');
                $table->index('first_seen_at');
                $table->index('last_seen_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_visitor_sessions');
    }
};

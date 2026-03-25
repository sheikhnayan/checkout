<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_post_id')->constrained('feed_posts')->cascadeOnDelete();
            $table->string('commenter_name');
            $table->string('commenter_email')->nullable();
            $table->text('body');
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_comments');
    }
};
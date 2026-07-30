<?php

use App\Models\JobPost;
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
        Schema::table('job_posts', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('posted_by_user_id');
            $table->index('slug');
        });

        JobPost::query()->select(['id', 'title', 'slug'])->chunkById(100, function ($jobs): void {
            foreach ($jobs as $job) {
                if (!blank($job->slug)) {
                    continue;
                }

                $job->slug = JobPost::generateUniqueSlug((string) $job->title);
                $job->save();
            }
        });

        Schema::table('job_posts', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });
    }
};

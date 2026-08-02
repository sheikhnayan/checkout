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
            $table->string('pay_frequency', 30)->nullable()->after('compensation');
            $table->string('city', 120)->nullable()->after('location');
            $table->string('state', 120)->nullable()->after('city');
        });

        JobPost::query()->whereNotNull('location')->chunkById(100, function ($jobs) {
            foreach ($jobs as $job) {
                if (blank($job->location)) {
                    continue;
                }
                $parts = array_map('trim', explode(',', $job->location));
                if (count($parts) >= 2) {
                    $job->city = $parts[0];
                    $job->state = $parts[1];
                    $job->save();
                } elseif (count($parts) === 1) {
                    $job->city = $parts[0];
                    $job->save();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropColumn(['pay_frequency', 'city', 'state']);
        });
    }
};

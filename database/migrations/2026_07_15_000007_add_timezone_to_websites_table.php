<?php

use App\Support\WebsiteTimezone;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->string('timezone')->default(WebsiteTimezone::DEFAULT)->after('email');
        });

        DB::table('websites')
            ->whereNull('timezone')
            ->update(['timezone' => WebsiteTimezone::DEFAULT]);
    }

    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn('timezone');
        });
    }
};
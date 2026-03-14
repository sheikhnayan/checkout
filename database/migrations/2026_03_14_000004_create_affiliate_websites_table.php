<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('affiliate_websites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliate_id');
            $table->unsignedBigInteger('website_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['affiliate_id', 'website_id']);
        });

        // Backfill from existing affiliate package mappings.
        $existingPairs = DB::table('affiliate_packages')
            ->select('affiliate_id', 'website_id')
            ->distinct()
            ->whereNotNull('affiliate_id')
            ->whereNotNull('website_id')
            ->get();

        foreach ($existingPairs as $pair) {
            DB::table('affiliate_websites')->updateOrInsert(
                [
                    'affiliate_id' => $pair->affiliate_id,
                    'website_id' => $pair->website_id,
                ],
                [
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_websites');
    }
};

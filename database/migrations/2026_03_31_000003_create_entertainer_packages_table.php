<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entertainer_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entertainer_id')->index();
            $table->unsignedBigInteger('website_id')->index();
            $table->unsignedBigInteger('package_id')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['entertainer_id', 'package_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entertainer_packages');
    }
};

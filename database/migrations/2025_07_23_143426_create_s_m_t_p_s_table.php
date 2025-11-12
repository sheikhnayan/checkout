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
        Schema::create('s_m_t_p_s', function (Blueprint $table) {
            $table->id();
            $table->string('host')->nullable();
            $table->integer('port')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->boolean('encryption')->default(false);
            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();
            $table->string('website_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_m_t_p_s');
    }
};

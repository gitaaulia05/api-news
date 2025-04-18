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
        Schema::create('password_reset_token', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string("token");
            $table->string("auth" , 20);
            $table->morphs('resettable' , 255);
            $table->timestamp("created_at");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_token');
    }
};

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
        Schema::create('beritas', function (Blueprint $table) {
            $table->string('id_berita')->primary();
            $table->string('judul_berita');
            $table->string('slug')->unique();
            $table->string('id_administrator');
            $table->foreign('id_administrator')->references('id_administrator')->on('administrators')->onDelete('cascade');
            $table->text('deks_berita');
            $table->string('gambar');
            $table->datetime('delete_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beritas');
    }
};

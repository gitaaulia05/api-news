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
        Schema::create('simpan_beritas', function (Blueprint $table) {
            $table->string('id_simpan_berita')->primary();
            $table->string('id_pengguna');
            $table->string('id_berita');
            $table->string('slug');
            $table->foreign('id_berita')->references('id_berita')->on('berita')->onDelete('cascade');
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simpan_beritas');
    }
};

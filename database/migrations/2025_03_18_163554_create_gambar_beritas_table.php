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
        Schema::create('gambar_beritas', function (Blueprint $table) {
            $table->string('id_gambar')->primary();
            $table->string('id_berita');
            $table->foreign('id_berita')->references('id_berita')->on('beritas')->onDelete('cascade');
            $table->string('gambar_berita');
            $table->string('keterangan_gambar');
            $table->string('posisi_gambar' , 30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gambar_beritas');
    }
};

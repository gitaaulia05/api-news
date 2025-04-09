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
            $table->string('id_kategori_berita');
            $table->foreign('id_kategori_berita')->references('id_kategori_berita')->on('kategori_beritas')->onDelete('cascade');
            $table->text('deks_berita');
            $table->char('is_tayang')->default('1');
            $table->datetime('deleted_at')->nullable();
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

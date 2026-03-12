<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyimpan jenis tagihan yang DINONAKTIFKAN untuk tahun ajaran tertentu.
     * Default = semua aktif. Record di tabel ini = exclude dari tagihan.
     */
    public function up(): void
    {
        Schema::create('jenis_tagihan_disabled', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tahun_ajaran_id');
            $table->unsignedBigInteger('jenis_tagihan_id');
            $table->timestamps();

            $table->unique(['tahun_ajaran_id', 'jenis_tagihan_id']);
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onDelete('cascade');
            $table->foreign('jenis_tagihan_id')->references('id')->on('jenis_tagihan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_tagihan_disabled');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Recreate jenis_tagihan_disabled with user_id so that
 * the config is per-santri per-tahun-ajaran (not global).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('jenis_tagihan_disabled');

        Schema::create('jenis_tagihan_disabled', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tahun_ajaran_id');
            $table->unsignedBigInteger('jenis_tagihan_id');
            $table->timestamps();

            $table->unique(['user_id', 'tahun_ajaran_id', 'jenis_tagihan_id'], 'jtd_user_ta_jenis_unique');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onDelete('cascade');
            $table->foreign('jenis_tagihan_id')->references('id')->on('jenis_tagihan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_tagihan_disabled');
    }
};

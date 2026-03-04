<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKartuPembayaranTable extends Migration
{
    public function up(): void
    {
        Schema::create('kartu_pembayaran', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tahun_ajaran_id');
            $table->string('nomor_kartu', 50);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kartu_pembayaran');
    }
}
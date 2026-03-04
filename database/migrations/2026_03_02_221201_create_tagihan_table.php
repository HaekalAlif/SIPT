<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagihanTable extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kartu_id');
            $table->decimal('total', 15, 2);
            $table->string('status', 30); // enum: draft, belum_bayar, menunggu_verifikasi, lunas
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('kartu_id')->references('id')->on('kartu_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
}
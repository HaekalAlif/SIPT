<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembayaranTable extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tagihan_id');
            $table->date('tanggal_bayar');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->string('bukti_pembayaran', 255)->nullable();
            $table->string('status', 30); // enum: menunggu_verifikasi, diterima, ditolak
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tagihan_id')->references('id')->on('tagihan');
            $table->foreign('verified_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
}
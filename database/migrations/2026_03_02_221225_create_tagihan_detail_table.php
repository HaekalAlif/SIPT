<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagihanDetailTable extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tagihan_id');
            $table->unsignedBigInteger('jenis_tagihan_id');
            $table->string('bulan', 20)->nullable();
            $table->decimal('nominal', 15, 2);
            $table->string('status', 20); // enum: belum_bayar, lunas
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tagihan_id')->references('id')->on('tagihan');
            $table->foreign('jenis_tagihan_id')->references('id')->on('jenis_tagihan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_detail');
    }
}
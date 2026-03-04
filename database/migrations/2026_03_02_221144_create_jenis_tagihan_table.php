<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJenisTagihanTable extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_tagihan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kategori_id');
            $table->string('nama_tagihan', 150);
            $table->decimal('nominal', 15, 2);
            $table->boolean('is_bulanan')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('kategori_id')->references('id')->on('kategori_tagihan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_tagihan');
    }
}
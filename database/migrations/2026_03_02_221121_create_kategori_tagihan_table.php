<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKategoriTagihanTable extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_tagihan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama', 50);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_tagihan');
    }
}
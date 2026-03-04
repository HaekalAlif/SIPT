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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('tahun_ajaran_masuk_id')->nullable()->after('kelas');
            $table->foreign('tahun_ajaran_masuk_id')->references('id')->on('tahun_ajaran')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_masuk_id']);
            $table->dropColumn('tahun_ajaran_masuk_id');
        });
    }
};

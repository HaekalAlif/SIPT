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
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('metode_pembayaran', 50)->nullable()->after('jumlah_bayar');
            $table->text('catatan')->nullable()->after('bukti_pembayaran');
            $table->text('catatan_verifikator')->nullable()->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['metode_pembayaran', 'catatan', 'catatan_verifikator']);
        });
    }
};

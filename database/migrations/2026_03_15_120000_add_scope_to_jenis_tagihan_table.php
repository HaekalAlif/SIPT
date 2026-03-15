<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('jenis_tagihan', function (Blueprint $table) {
            $table->string('target_scope', 20)->default('all')->after('is_bulanan');
            $table->string('target_value', 100)->nullable()->after('target_scope');
            $table->index(['target_scope', 'target_value'], 'jenis_tagihan_scope_idx');
        });

        DB::table('jenis_tagihan')
            ->whereNull('target_scope')
            ->update([
                'target_scope' => 'all',
                'target_value' => null,
            ]);
    }

    public function down(): void
    {
        Schema::table('jenis_tagihan', function (Blueprint $table) {
            $table->dropIndex('jenis_tagihan_scope_idx');
            $table->dropColumn(['target_scope', 'target_value']);
        });
    }
};

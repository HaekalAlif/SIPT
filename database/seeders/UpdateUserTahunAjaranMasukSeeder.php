<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TahunAjaran;

class UpdateUserTahunAjaranMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get tahun ajaran aktif (2025/2026)
        $tahunAjaranAktif = TahunAjaran::where('nama', '2025/2026')->first();
        
        if ($tahunAjaranAktif) {
            // Update semua user santri yang belum memiliki tahun_ajaran_masuk_id
            User::where('role', User::ROLE_SANTRI)
                ->whereNull('tahun_ajaran_masuk_id')
                ->update(['tahun_ajaran_masuk_id' => $tahunAjaranAktif->id]);
                
            $this->command->info('Updated users with tahun ajaran masuk: ' . $tahunAjaranAktif->nama);
        } else {
            $this->command->error('Tahun ajaran 2025/2026 tidak ditemukan');
        }
    }
}

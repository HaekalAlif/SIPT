<?php

namespace Database\Seeders;

use App\Models\JenisTagihan;
use App\Models\KategoriTagihan;
use Illuminate\Database\Seeder;

class MahasiswaTagihanSeeder extends Seeder
{
    public function run(): void
    {
        $syariah = KategoriTagihan::firstOrCreate(['nama' => 'Syariah']);

        // Tarif khusus mahasiswa (Perguruan Tinggi)
        JenisTagihan::updateOrCreate(
            [
                'kategori_id' => $syariah->id,
                'nama_tagihan' => 'SPP Bulanan (Khusus Mahasiswa)',
            ],
            [
                'nominal' => 250000,
                'is_bulanan' => true,
                'target_scope' => JenisTagihan::TARGET_SCOPE_TINGKATAN,
                'target_value' => 'KULIAH',
            ]
        );
    }
}

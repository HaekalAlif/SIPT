<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriTagihan;

class KategoriTagihanSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            ['nama' => 'Registrasi'],
            ['nama' => 'Syariah'],
            ['nama' => 'Lainnya']
        ];

        foreach ($kategoris as $kategori) {
            KategoriTagihan::create($kategori);
        }
    }
}
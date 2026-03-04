<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunAjaran;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjarans = [
            [
                'nama' => '2024/2025',
                'is_active' => false
            ],
            [
                'nama' => '2025/2026',
                'is_active' => true
            ],
            [
                'nama' => '2026/2027',
                'is_active' => false
            ]
        ];

        foreach ($tahunAjarans as $tahun) {
            TahunAjaran::create($tahun);
        }
    }
}
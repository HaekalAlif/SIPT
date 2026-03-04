<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisTagihan;
use App\Models\KategoriTagihan;

class JenisTagihanSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil kategori
        $registrasi = KategoriTagihan::where('nama', 'Registrasi')->first();
        $syariah = KategoriTagihan::where('nama', 'Syariah')->first();
        $lainnya = KategoriTagihan::where('nama', 'Lainnya')->first();

        $jenisTagihans = [
            // Kategori Registrasi (sesuai gambar UI)
            [
                'kategori_id' => $registrasi->id,
                'nama_tagihan' => 'Pendaftaran',
                'nominal' => 90000,
                'is_bulanan' => false
            ],
            [
                'kategori_id' => $registrasi->id,
                'nama_tagihan' => 'Ktk,Spp dan Raprot',
                'nominal' => 90000,
                'is_bulanan' => false
            ],
            [
                'kategori_id' => $registrasi->id,
                'nama_tagihan' => 'Dpp.Pesantren',
                'nominal' => 90000,
                'is_bulanan' => false
            ],
            [
                'kategori_id' => $registrasi->id,
                'nama_tagihan' => 'Fasilitas Kamar',
                'nominal' => 90000,
                'is_bulanan' => false
            ],
            [
                'kategori_id' => $registrasi->id,
                'nama_tagihan' => 'Ta,aruf',
                'nominal' => 90000,
                'is_bulanan' => false
            ],
            [
                'kategori_id' => $registrasi->id,
                'nama_tagihan' => 'infaq',
                'nominal' => 90000,
                'is_bulanan' => false
            ],

            // Kategori Syariah (pembayaran bulanan)
            [
                'kategori_id' => $syariah->id,
                'nama_tagihan' => 'SPP Bulanan',
                'nominal' => 150000,
                'is_bulanan' => true
            ],
            [
                'kategori_id' => $syariah->id,
                'nama_tagihan' => 'Makan Bulanan',
                'nominal' => 200000,
                'is_bulanan' => true
            ],

            // Kategori Lainnya
            [
                'kategori_id' => $lainnya->id,
                'nama_tagihan' => 'Seragam',
                'nominal' => 150000,
                'is_bulanan' => false
            ],
            [
                'kategori_id' => $lainnya->id,
                'nama_tagihan' => 'Buku Paket',
                'nominal' => 100000,
                'is_bulanan' => false
            ]
        ];

        foreach ($jenisTagihans as $jenis) {
            JenisTagihan::create($jenis);
        }
    }
}
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

        $khatamanLevels = [
            'I Tsanawiyyah',
            'III Tsanawiyyah',
            "VI Ibtida'iyah",
            'PTQ',
        ];

        $nonKhatamanLevels = [
            'II Tsanawiyyah',
            "V Ibtida'iyah",
            "IV Ibtida'iyah",
            "III I'dad",
        ];

        $jenisTagihans = [
            // Umum semua jenjang
            ['kategori_id' => $registrasi->id, 'nama_tagihan' => 'Pendaftaran', 'nominal' => 90000, 'is_bulanan' => false, 'target_scope' => 'all', 'target_value' => null],
            ['kategori_id' => $registrasi->id, 'nama_tagihan' => 'Kartu, SPP, Raport', 'nominal' => 90000, 'is_bulanan' => false, 'target_scope' => 'all', 'target_value' => null],
            ['kategori_id' => $registrasi->id, 'nama_tagihan' => 'DPP Pesantren', 'nominal' => 90000, 'is_bulanan' => false, 'target_scope' => 'all', 'target_value' => null],
            ['kategori_id' => $registrasi->id, 'nama_tagihan' => 'Fasilitas Kamar', 'nominal' => 90000, 'is_bulanan' => false, 'target_scope' => 'all', 'target_value' => null],
            ['kategori_id' => $registrasi->id, 'nama_tagihan' => "Ta'aruf", 'nominal' => 90000, 'is_bulanan' => false, 'target_scope' => 'all', 'target_value' => null],
            ['kategori_id' => $registrasi->id, 'nama_tagihan' => 'Infaq', 'nominal' => 90000, 'is_bulanan' => false, 'target_scope' => 'all', 'target_value' => null],

            // SPP bulanan umum (SD/SMP/SMA)
            ['kategori_id' => $syariah->id, 'nama_tagihan' => 'SPP Bulanan (SD/SMP/SMA)', 'nominal' => 150000, 'is_bulanan' => true, 'target_scope' => 'all', 'target_value' => null],

            // SPP khusus mahasiswa
            ['kategori_id' => $syariah->id, 'nama_tagihan' => 'SPP Bulanan (Khusus Mahasiswa)', 'nominal' => 250000, 'is_bulanan' => true, 'target_scope' => 'tingkatan', 'target_value' => 'KULIAH'],

            // Lainnya umum
            ['kategori_id' => $lainnya->id, 'nama_tagihan' => 'Seragam', 'nominal' => 150000, 'is_bulanan' => false, 'target_scope' => 'all', 'target_value' => null],
            ['kategori_id' => $lainnya->id, 'nama_tagihan' => 'Ramadhan', 'nominal' => 50000, 'is_bulanan' => false, 'target_scope' => 'all', 'target_value' => null],
            ['kategori_id' => $lainnya->id, 'nama_tagihan' => 'Imtihan Awal', 'nominal' => 30000, 'is_bulanan' => false, 'target_scope' => 'all', 'target_value' => null],
            ['kategori_id' => $lainnya->id, 'nama_tagihan' => 'Imtihan Tsani', 'nominal' => 30000, 'is_bulanan' => false, 'target_scope' => 'all', 'target_value' => null],
        ];

        foreach ($khatamanLevels as $level) {
            $jenisTagihans[] = [
                'kategori_id' => $lainnya->id,
                'nama_tagihan' => 'Haflah Akhir Sanah Khataman',
                'nominal' => 550000,
                'is_bulanan' => false,
                'target_scope' => 'ngaji',
                'target_value' => $level,
            ];
        }

        foreach ($nonKhatamanLevels as $level) {
            $jenisTagihans[] = [
                'kategori_id' => $lainnya->id,
                'nama_tagihan' => 'Haflah Akhir Sanah Non Khataman',
                'nominal' => 450000,
                'is_bulanan' => false,
                'target_scope' => 'ngaji',
                'target_value' => $level,
            ];
        }

        // Cleanup data lama agar konsisten dengan format terbaru
        JenisTagihan::where('kategori_id', $lainnya->id)
            ->where(function ($q) {
                $q->where('nama_tagihan', 'Buku Paket')
                    ->orWhere('nama_tagihan', 'like', 'Haflah Akhirusanah -%');
            })
            ->delete();

        foreach ($jenisTagihans as $jenis) {
            JenisTagihan::updateOrCreate(
                [
                    'kategori_id' => $jenis['kategori_id'],
                    'nama_tagihan' => $jenis['nama_tagihan'],
                    'target_scope' => $jenis['target_scope'],
                    'target_value' => $jenis['target_value'],
                ],
                $jenis
            );
        }
    }
}
<?php

namespace Database\Seeders;

use App\Models\MetodePembayaran;
use Illuminate\Database\Seeder;

class MetodePembayaranSeeder extends Seeder
{
    public function run(): void
    {
        MetodePembayaran::updateOrCreate(
            ['nama_metode' => 'Transfer Bank BRI'],
            [
                'nama_bank' => 'Bank BRI',
                'nomor_rekening' => '1234-5678-9012-3456',
                'atas_nama' => 'Siti Mutoharoh, S.E',
                'logo_path' => 'images/bri.png',
                'keterangan' => 'Transfer sesuai nominal tagihan, lalu upload bukti pembayaran.',
                'is_active' => true,
                'urutan' => 1,
            ]
        );
    }
}

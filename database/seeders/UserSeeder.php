<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\TahunAjaran;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAktifId = TahunAjaran::where('is_active', true)->value('id')
            ?? TahunAjaran::query()->value('id');

        // Admin
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
            'nama_santri' => 'Admin Pondok',
            'nama_orang_tua' => 'Ortu Admin',
            'no_telp' => '081234567890',
            'tingkatan' => 'Pengurus',
            'kelas' => 'A',
            'tempat_lahir' => 'Kota Admin',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => User::JENIS_KELAMIN_L,
            'alamat' => 'Alamat Admin',
            'tingkatan_ngaji' => 'PTQ',
            'tahun_ajaran_masuk_id' => $tahunAktifId,
        ]);

        $santriSeeds = [
            [
                'email' => 'santri.sd.vibtidaiyah@example.com',
                'nama_santri' => 'Santri MI VI Ibtidaiyah',
                'no_telp' => '081234567891',
                'tingkatan' => 'MI',
                'kelas' => '1 MI',
                'tingkatan_ngaji' => "VI Ibtida'iyah",
                'tanggal_lahir' => '2012-01-10',
                'jenis_kelamin' => User::JENIS_KELAMIN_L,
            ],
            [
                'email' => 'santri.smp.itsanawiyyah@example.com',
                'nama_santri' => 'Santri SMP/MTs I Tsanawiyyah',
                'no_telp' => '081234567892',
                'tingkatan' => 'SMP/MTs',
                'kelas' => '1 SMP/MTs',
                'tingkatan_ngaji' => 'I Tsanawiyyah',
                'tanggal_lahir' => '2010-02-11',
                'jenis_kelamin' => User::JENIS_KELAMIN_P,
            ],
            [
                'email' => 'santri.sma.iitsanawiyyah@example.com',
                'nama_santri' => 'Santri SMK/MA II Tsanawiyyah',
                'no_telp' => '081234567893',
                'tingkatan' => 'SMK/MA',
                'kelas' => '1 SMK/MA',
                'tingkatan_ngaji' => 'II Tsanawiyyah',
                'tanggal_lahir' => '2009-03-12',
                'jenis_kelamin' => User::JENIS_KELAMIN_L,
            ],
            [
                'email' => 'santri.kuliah.iiitsanawiyyah@example.com',
                'nama_santri' => 'Santri Perguruan Tinggi III Tsanawiyyah',
                'no_telp' => '081234567894',
                'tingkatan' => 'Perguruan Tinggi',
                'kelas' => 'Semester 1 dan 2',
                'tingkatan_ngaji' => 'III Tsanawiyyah',
                'tanggal_lahir' => '2007-04-13',
                'jenis_kelamin' => User::JENIS_KELAMIN_P,
            ],
            [
                'email' => 'santri.sd.vibtidaiyah2@example.com',
                'nama_santri' => 'Santri MI V Ibtidaiyah',
                'no_telp' => '081234567895',
                'tingkatan' => 'MI',
                'kelas' => '2 MI',
                'tingkatan_ngaji' => "V Ibtida'iyah",
                'tanggal_lahir' => '2011-05-14',
                'jenis_kelamin' => User::JENIS_KELAMIN_L,
            ],
            [
                'email' => 'santri.smp.ivibtidaiyah@example.com',
                'nama_santri' => 'Santri SMP/MTs IV Ibtidaiyah',
                'no_telp' => '081234567896',
                'tingkatan' => 'SMP/MTs',
                'kelas' => '2 SMP/MTs',
                'tingkatan_ngaji' => "IV Ibtida'iyah",
                'tanggal_lahir' => '2010-06-15',
                'jenis_kelamin' => User::JENIS_KELAMIN_P,
            ],
            [
                'email' => 'santri.sma.iiidad@example.com',
                'nama_santri' => "Santri SMK/MA III I'dad",
                'no_telp' => '081234567897',
                'tingkatan' => 'SMK/MA',
                'kelas' => '2 SMK/MA',
                'tingkatan_ngaji' => "III I'dad",
                'tanggal_lahir' => '2008-07-16',
                'jenis_kelamin' => User::JENIS_KELAMIN_L,
            ],
            [
                'email' => 'santri.kuliah.ptq@example.com',
                'nama_santri' => 'Santri Perguruan Tinggi PTQ',
                'no_telp' => '081234567898',
                'tingkatan' => 'Perguruan Tinggi',
                'kelas' => 'Semester 3 dan 4',
                'tingkatan_ngaji' => 'PTQ',
                'tanggal_lahir' => '2006-08-17',
                'jenis_kelamin' => User::JENIS_KELAMIN_P,
            ],
        ];

        foreach ($santriSeeds as $seed) {
            User::updateOrCreate(['email' => $seed['email']], [
                'email' => $seed['email'],
                'password' => Hash::make('password'),
                'role' => User::ROLE_SANTRI,
                'status' => User::STATUS_ACTIVE,
                'nama_santri' => $seed['nama_santri'],
                'nama_orang_tua' => 'Orang Tua ' . $seed['nama_santri'],
                'no_telp' => $seed['no_telp'],
                'tingkatan' => $seed['tingkatan'],
                'kelas' => $seed['kelas'],
                'tempat_lahir' => 'Kota Santri',
                'tanggal_lahir' => $seed['tanggal_lahir'],
                'jenis_kelamin' => $seed['jenis_kelamin'],
                'alamat' => 'Alamat ' . $seed['nama_santri'],
                'tingkatan_ngaji' => $seed['tingkatan_ngaji'],
                'tahun_ajaran_masuk_id' => $tahunAktifId,
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAktifId = \App\Models\TahunAjaran::where('is_active', true)->value('id');

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

        // Santri MI (Bayar Khataman: VI Ibtida'iyah)
        User::updateOrCreate(['email' => 'santri.mi@example.com'], [
            'email' => 'santri.mi@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SANTRI,
            'status' => User::STATUS_ACTIVE,
            'nama_santri' => 'Santri MI',
            'nama_orang_tua' => 'Ortu Santri',
            'no_telp' => '081234567891',
            'tingkatan' => 'SD',
            'kelas' => '6 SD/MI',
            'tempat_lahir' => 'Kota Santri',
            'tanggal_lahir' => '2008-05-10',
            'jenis_kelamin' => User::JENIS_KELAMIN_L,
            'alamat' => 'Alamat Santri',
            'tingkatan_ngaji' => "VI Ibtida'iyah",
            'tahun_ajaran_masuk_id' => $tahunAktifId,
        ]);

        // Santri MA (Tidak Bayar Khataman: II Tsanawiyyah)
        User::updateOrCreate(['email' => 'santri.ma@example.com'], [
            'email' => 'santri.ma@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SANTRI,
            'status' => User::STATUS_ACTIVE,
            'nama_santri' => 'Santri MA',
            'nama_orang_tua' => 'Ortu Santri MA',
            'no_telp' => '081234567892',
            'tingkatan' => 'SMA',
            'kelas' => '2 SMA/MA',
            'tempat_lahir' => 'Kota Santri MA',
            'tanggal_lahir' => '2007-03-14',
            'jenis_kelamin' => User::JENIS_KELAMIN_P,
            'alamat' => 'Alamat Santri MA',
            'tingkatan_ngaji' => 'II Tsanawiyyah',
            'tahun_ajaran_masuk_id' => $tahunAktifId,
        ]);

        // Mahasiswa (tarif khusus KULIAH)
        User::updateOrCreate(['email' => 'mahasiswa@example.com'], [
            'email' => 'mahasiswa@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SANTRI,
            'status' => User::STATUS_ACTIVE,
            'nama_santri' => 'Santri Mahasiswa',
            'nama_orang_tua' => 'Ortu Mahasiswa',
            'no_telp' => '081234567893',
            'tingkatan' => 'KULIAH',
            'kelas' => 'Semester 3',
            'tempat_lahir' => 'Kota Mahasiswa',
            'tanggal_lahir' => '2004-01-20',
            'jenis_kelamin' => User::JENIS_KELAMIN_L,
            'alamat' => 'Alamat Mahasiswa',
            'tingkatan_ngaji' => 'PTQ',
            'tahun_ajaran_masuk_id' => $tahunAktifId,
        ]);
    }
}
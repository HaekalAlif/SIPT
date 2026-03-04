<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'nis' => '00001',
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
            'tingkatan_ngaji' => 'Tingkat 1',
        ]);

        // Santri
        User::create([
            'nis' => '10001',
            'email' => 'santri@example.com',
            'password' => Hash::make('password'), 
            'role' => User::ROLE_SANTRI,
            'status' => User::STATUS_ACTIVE,
            'nama_santri' => 'Santri Pertama',
            'nama_orang_tua' => 'Ortu Santri',
            'no_telp' => '081234567891',
            'tingkatan' => 'MTs',
            'kelas' => '7A',
            'tempat_lahir' => 'Kota Santri',
            'tanggal_lahir' => '2008-05-10',
            'jenis_kelamin' => User::JENIS_KELAMIN_L,
            'alamat' => 'Alamat Santri',
            'tingkatan_ngaji' => 'Tingkat 2',
        ]);
    }
}
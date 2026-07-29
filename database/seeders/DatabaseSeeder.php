<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pegawai;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Membuat Akun Admin
        User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'), // Hash::make wajib untuk mengenkripsi password
            'role'     => 'admin',
        ]);

        // 2. Membuat Akun Pegawai
        $userPegawai = User::create([
            'username' => 'pegawai1',
            'password' => Hash::make('pegawai123'),
            'role'     => 'pegawai',
        ]);

        // 3. Membuat Profil Biodata Pegawai yang terhubung ke Akun Pegawai
        Pegawai::create([
            'user_id'       => $userPegawai->id, // Mengambil ID dari akun pegawai yang baru dibuat di atas
            'nik'           => '3274012345678901',
            'nama'          => 'Budi Santoso',
            'jenis_kelamin' => 'Laki-laki',
            'no_hp'         => '081234567890',
            'alamat'        => 'Jl. Siliwangi, Kota Cirebon',
            'status'        => 'aktif',
        ]);
    }
}
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
            'password' => Hash::make('admin123'), 
            'role'     => 'admin',
        ]);

        // 2. Daftar Pegawai Baru Sesuai Permintaan
        $daftarPegawai = [
            [
                'nik'           => '3209142808010005',
                'nama'          => 'Muhamad Shadam Azriel',
                'jenis_kelamin' => 'L',
                'alamat'        => 'DUSUN II BLOK KAVLING GG. SALAM 7',
                'no_hp'         => null,
            ],
            [
                'nik'           => '3209200205960010',
                'nama'          => 'Dany Ryanto',
                'jenis_kelamin' => 'L',
                'alamat'        => 'JL. SETIA NO.49',
                'no_hp'         => null,
            ],
            [
                'nik'           => '3274036305030005',
                'nama'          => 'Risya Nazhira Rahma',
                'jenis_kelamin' => 'P',
                'alamat'        => null,
                'no_hp'         => '0895334827810',
            ]
        ];

        // 3. Looping untuk mengeksekusi pembuatan User dan Biodatanya
        foreach ($daftarPegawai as $data) {
            // Buat akun login untuk pegawai
            $userPegawai = User::create([
                'username' => $data['nik'], 
                'password' => Hash::make('123456'), // Semua password diset 123456
                'role'     => 'pegawai',
            ]);

            // Buat profil data pegawai dan hubungkan dengan akun login
            Pegawai::create([
                'user_id'       => $userPegawai->id, 
                'nik'           => $data['nik'],
                'nama'          => $data['nama'],
                'jenis_kelamin' => $data['jenis_kelamin'], 
                'no_hp'         => $data['no_hp'],
                'alamat'        => $data['alamat'],
                'status'        => 'Aktif',
            ]);
        }
    }
}
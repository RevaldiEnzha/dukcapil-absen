<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    // Mengizinkan semua kolom diisi data, kecuali kolom 'id'
    protected $guarded = ['id'];

    // Relasi balik: 1 data Absensi ini adalah milik 1 Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
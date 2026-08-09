<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    // Mengizinkan semua kolom diisi
    protected $guarded = [];

    // Mengenalkan relasi ke tabel Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class)->withTrashed();
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    // Mengizinkan semua kolom diisi data, kecuali kolom 'id'
    protected $guarded = ['id'];

    // Relasi balik: 1 data Izin ini adalah milik 1 Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    // Tambahan relasi: Mengambil data Admin (User) yang menyetujui izin ini
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
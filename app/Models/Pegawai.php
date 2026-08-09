<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function izins()
    {
        return $this->hasMany(Izin::class);
    }

    // ================= FUNGSI RADAR OTOMATIS =================
    public function syncStatusCuti()
    {
        if ($this->status === 'Tidak Aktif') {
            return;
        }

        $hariIni = Carbon::today()->format('Y-m-d');

        // PERBAIKAN: Tambahkan filter jenis_izin = 'Cuti'
        $sedangCuti = $this->izins()
                           ->where('status', 'disetujui')
                           ->where('jenis_izin', 'Cuti') 
                           ->whereDate('tanggal_mulai', '<=', $hariIni)
                           ->whereDate('tanggal_selesai', '>=', $hariIni)
                           ->exists();

        if ($sedangCuti && $this->status !== 'Cuti') {
            $this->update(['status' => 'Cuti']);
        } elseif (!$sedangCuti && $this->status === 'Cuti') {
            $this->update(['status' => 'Aktif']);
        }
    }
}
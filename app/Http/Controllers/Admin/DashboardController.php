<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Absensi;
use App\Models\Izin;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil data statistik hari ini
        $hariIni = today();

        $totalPegawai = Pegawai::count();
        $hadirHariIni = Absensi::whereDate('tanggal', $hariIni)->count();
        $terlambat = Absensi::whereDate('tanggal', $hariIni)->where('status', 'Terlambat')->count();
        
        // Menghitung pegawai yang izin/sakit pada hari ini dan sudah disetujui
        $izinSakit = Izin::whereDate('tanggal_mulai', '<=', $hariIni)
                         ->whereDate('tanggal_selesai', '>=', $hariIni)
                         ->where('status', 'disetujui')
                         ->count();

        // Mengambil 5 data absensi terbaru hari ini beserta data pegawainya (Relasi)
        $absensiTerbaru = Absensi::with('pegawai')
                                 ->whereDate('tanggal', $hariIni)
                                 ->orderBy('check_in', 'desc')
                                 ->take(5)
                                 ->get();

        return view('admin.dashboard', compact(
            'totalPegawai', 'hadirHariIni', 'terlambat', 'izinSakit', 'absensiTerbaru'
        ));
    }
}
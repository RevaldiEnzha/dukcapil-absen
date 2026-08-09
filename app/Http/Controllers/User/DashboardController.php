<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil user yang sedang login beserta data pegawainya
        $user = Auth::user();
        $pegawai = $user->pegawai;

        // Ambil data absensi khusus untuk hari ini
        $hariIni = Carbon::today()->format('Y-m-d');
        $absenHariIni = null;

        if ($pegawai) {
            $absenHariIni = Absensi::where('pegawai_id', $pegawai->id)
                                   ->where('tanggal', $hariIni)
                                   ->first();
        }

        return view('user.dashboard', compact('pegawai', 'absenHariIni'));
    }
}
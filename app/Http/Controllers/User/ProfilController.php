<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Absensi;
use App\Models\Izin;
use Carbon\Carbon;

class ProfilController extends Controller
{
    public function index()
    {
        $pegawai = Auth::user()->pegawai;
        return view('user.profil', compact('pegawai'));
    }

    public function updateAkun(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        // 1. Validasi Input
        $request->validate([
            'no_hp'    => 'nullable|string|max:20',
            'alamat'   => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed', // 'confirmed' butuh input 'password_confirmation'
        ]);

        // 2. Update Data Pegawai (No HP & Alamat)
        if ($pegawai) {
            $pegawai->update([
                'no_hp'  => $request->no_hp,
                'alamat' => $request->alamat,
            ]);
        }

        // 3. Update Password JIKA diisi
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return back()->with('success', 'Pengaturan akun berhasil diperbarui!');
    }

    public function riwayat(Request $request)
    {
        $pegawai = Auth::user()->pegawai;

        // Ambil filter bulan dan tahun dari request (default: bulan & tahun ini)
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // 1. Ambil data absensi sesuai filter
        $absensis = Absensi::where('pegawai_id', $pegawai->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        // 2. Ambil data izin yang disetujui di bulan/tahun tersebut untuk mengambil keterangan
        $izins = Izin::where('pegawai_id', $pegawai->id)
            ->where('status', 'disetujui')
            ->where(function($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal_mulai', $bulan)->whereYear('tanggal_mulai', $tahun)
                  ->orWhereMonth('tanggal_selesai', $bulan)->whereYear('tanggal_selesai', $tahun);
            })->get();

        // 3. Cocokkan data absensi dengan keterangan izin (jika statusnya izin/sakit)
        foreach ($absensis as $absen) {
            $absen->keterangan_izin = null;
            
            if (in_array(strtolower($absen->status), ['izin', 'sakit', 'cuti'])) {
                // Cari izin yang mencakup tanggal absen ini
                $izinTerkait = $izins->first(function ($izin) use ($absen) {
                    $tglAbsen = Carbon::parse($absen->tanggal);
                    $mulai = Carbon::parse($izin->tanggal_mulai)->startOfDay();
                    $selesai = Carbon::parse($izin->tanggal_selesai)->endOfDay();
                    return $tglAbsen->between($mulai, $selesai);
                });

                if ($izinTerkait) {
                    $absen->keterangan_izin = $izinTerkait->jenis_izin . ' (' . $izinTerkait->alasan . ')';
                }
            }
        }

        return view('user.riwayat', compact('absensis', 'bulan', 'tahun'));
    }
}
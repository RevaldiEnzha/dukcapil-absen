<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;

class AbsenController extends Controller
{
    public function index()
    {
        $pegawai = Auth::user()->pegawai;
        $absenHariIni = null;
        $statusIzinHariIni = null; // Tambahan untuk dilempar ke UI
        $isWeekend = Carbon::today()->isWeekend();

        if ($pegawai) {
            $pegawai->syncStatusCuti(); 

            $absenHariIni = Absensi::where('pegawai_id', $pegawai->id)
                                   ->where('tanggal', Carbon::today()->format('Y-m-d'))
                                   ->first();
                                   
            // CEK: Apakah dia sedang Sakit, Izin Pribadi, atau Cuti hari ini?
            if ($absenHariIni && in_array($absenHariIni->status, ['Sakit', 'Izin Pribadi', 'Cuti'])) {
                $statusIzinHariIni = $absenHariIni->status;
            }
        }

        // Lempar variabel baru
        return view('user.absen', compact('pegawai', 'absenHariIni', 'isWeekend', 'statusIzinHariIni'));
    }   

    public function processScan(Request $request)
    {
        // 1. KEAMANAN HARI LIBUR
        if (Carbon::today()->isWeekend()) {
            return back()->with('error', 'Absensi ditutup pada hari libur (Sabtu & Minggu).');
        }
        
        // TAMBAHAN: KEAMANAN HARI LIBUR NASIONAL / CUTI BERSAMA
        $hariIni = Carbon::today()->format('Y-m-d');
        $cekLibur = \App\Models\HariLibur::where('tanggal', $hariIni)->first();
        if ($cekLibur) {
            return back()->with('error', 'Absensi ditutup. Hari ini adalah hari libur: ' . $cekLibur->keterangan);
        }

        // 2. Validasi Teks Rahasia QR Code
        $kodeRahasia = 'absendukcapilnonasn'; 

        if ($request->qr_code !== $kodeRahasia) {
            return back()->with('error', 'QR Code tidak valid atau bukan milik instansi!');
        }

        $pegawai = Auth::user()->pegawai;
        
        if (!$pegawai) {
            return back()->with('error', 'Data pegawai tidak ditemukan.');
        }

        $waktuSekarang = Carbon::now()->format('H:i:s');
        $jamSekarang   = Carbon::now()->format('H:i');

        // 3. KEAMANAN BARU: Cegah scan jika hari ini sedang Sakit/Izin/Cuti
        $cekIzin = Absensi::where('pegawai_id', $pegawai->id)
                          ->where('tanggal', $hariIni)
                          ->whereIn('status', ['Sakit', 'Izin Pribadi', 'Cuti'])
                          ->first();
                          
        if ($cekIzin) {
            return back()->with('error', 'Gagal: Anda tidak dapat melakukan absen karena hari ini Anda berstatus: ' . $cekIzin->status);
        }

        // 4. Cari data absensi hari ini
        $absen = Absensi::where('pegawai_id', $pegawai->id)
                        ->where('tanggal', $hariIni)
                        ->first();

        // 5. Logika Check-In / Check-Out dengan Batasan Waktu
        if (!$absen) {
            // SKENARIO A: Jika belum ada data -> Lakukan Check-In
            
            // Tembok Keamanan 1: Tolak jika Check-In sebelum jam 06:00
            if ($jamSekarang < '06:00') {
                return back()->with('error', 'Gagal: Waktu presensi masuk (Check-In) baru dibuka pukul 06:00.');
            }

            $status = ($jamSekarang > '07:30') ? 'Terlambat' : 'Hadir'; 
            
            Absensi::create([
                'pegawai_id' => $pegawai->id,
                'tanggal'    => $hariIni,
                'check_in'   => $waktuSekarang,
                'status'     => $status,
            ]);

            return back()->with('success', 'Berhasil Check-In pada pukul ' . $jamSekarang);

        } elseif ($absen && !$absen->check_out) {
            // Jika sudah Check-In tapi belum Check-Out -> Lakukan Check-Out
            // Tolak jika Check-Out sebelum jam 16:00
            if ($jamSekarang < '16:00') {
                return back()->with('error', 'Gagal: Waktu presensi pulang (Check-Out) baru dibuka pukul 16:00.');
            }

            $absen->update([
                'check_out' => $waktuSekarang,
            ]);

            return back()->with('success', 'Berhasil Check-Out pada pukul ' . $jamSekarang);

        } else {
            // SKENARIO C: Jika Check-In dan Check-Out sudah terisi penuh
            return back()->with('info', 'Anda sudah menyelesaikan absensi (Masuk & Keluar) hari ini.');
        }
    }
}
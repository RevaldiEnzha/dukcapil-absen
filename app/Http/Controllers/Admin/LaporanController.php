<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tangkap Filter Bulan & Tahun
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // 2. Buat penanda awal dan akhir bulan
        $awalBulanIni = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
        // Ambil tanggal terakhir di bulan laporan pada jam 23:59
        $akhirBulanIni = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        // 3. Query Pegawai
        $pegawais = Pegawai::withTrashed()
            //  Sembunyikan pegawai jika dia baru didaftarkan SETELAH bulan laporan ini
            ->where('created_at', '<=', $akhirBulanIni)
            // Sembunyikan pegawai jika dia di soft delete SEBELUM bulan laporan ini
            ->where(function($q) use ($awalBulanIni) {
                $q->whereNull('deleted_at') 
                  ->orWhere('deleted_at', '>=', $awalBulanIni);
            })
            ->withCount([
            'absensis as hadir_count' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)
                      ->whereYear('tanggal', $tahun)
                      ->whereNotNull('check_in')
                      ->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)');
            },
            'absensis as terlambat_count' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)
                      ->whereYear('tanggal', $tahun)
                      ->whereNotNull('check_in')
                      ->whereTime('check_in', '>', '07:30:00')
                      ->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)');
            },
            'absensis as cuti_count' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)
                      ->whereYear('tanggal', $tahun)
                      ->whereIn('status', ['Cuti', 'Sakit', 'Izin Pribadi'])
                      ->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)');
            }
        ])->paginate(10)->withQueryString();

        // 4. Daftar Bulan
        $daftarBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        // 5. Hitung dinamis jumlah hari kerja (Memperhitungkan Hari Libur Nasional)
        $hariKerja = 0;
        $tanggalMulai = Carbon::createFromDate($tahun, $bulan, 1);
        $jumlahHariDiBulan = $tanggalMulai->daysInMonth;

        $hariLiburBulanIni = \App\Models\HariLibur::whereMonth('tanggal', $bulan)
                                ->whereYear('tanggal', $tahun)
                                ->pluck('tanggal')
                                ->toArray();

        for ($i = 1; $i <= $jumlahHariDiBulan; $i++) {
            $cekTanggal = Carbon::createFromDate($tahun, $bulan, $i);
            $tanggalFormat = $cekTanggal->format('Y-m-d');

            if (!$cekTanggal->isWeekend() && !in_array($tanggalFormat, $hariLiburBulanIni)) {
                $hariKerja++;
            }
        }
        
        $totalHariKerja = $hariKerja;

        return view('admin.laporan.index', compact('pegawais', 'bulan', 'tahun', 'daftarBulan', 'totalHariKerja'));
    }

    public function exportPdf(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        
        $awalBulanIni = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
        $akhirBulanIni = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $pegawais = Pegawai::withTrashed()
            ->where('created_at', '<=', $akhirBulanIni)
            ->where(function($q) use ($awalBulanIni) {
                $q->whereNull('deleted_at')
                  ->orWhere('deleted_at', '>=', $awalBulanIni);
            })
            ->withCount([
            'absensis as hadir_count' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)
                      ->whereYear('tanggal', $tahun)
                      ->whereNotNull('check_in')
                      ->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)');
            },
            'absensis as terlambat_count' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)
                      ->whereYear('tanggal', $tahun)
                      ->whereNotNull('check_in')
                      ->whereTime('check_in', '>', '07:30:00')
                      ->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)');
            },
            'absensis as cuti_count' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)
                      ->whereYear('tanggal', $tahun)
                      ->whereIn('status', ['Cuti', 'Sakit', 'Izin Pribadi'])
                      ->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)');
            }
        ])->get(); 

        $daftarBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        $namaBulan = $daftarBulan[$bulan];

        // Perhitungan Hari Kerja (termasuk Hari Libur Nasional)
        $hariKerja = 0;
        $tanggalMulai = Carbon::createFromDate($tahun, $bulan, 1);
        $jumlahHariDiBulan = $tanggalMulai->daysInMonth;

        $hariLiburBulanIni = \App\Models\HariLibur::whereMonth('tanggal', $bulan)
                                ->whereYear('tanggal', $tahun)
                                ->pluck('tanggal')
                                ->toArray();

        for ($i = 1; $i <= $jumlahHariDiBulan; $i++) {
            $cekTanggal = Carbon::createFromDate($tahun, $bulan, $i);
            $tanggalFormat = $cekTanggal->format('Y-m-d');

            if (!$cekTanggal->isWeekend() && !in_array($tanggalFormat, $hariLiburBulanIni)) {
                $hariKerja++;
            }
        }
        $totalHariKerja = $hariKerja;

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('pegawais', 'namaBulan', 'tahun', 'totalHariKerja'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Laporan_Kehadiran_{$namaBulan}_{$tahun}.pdf");
    }
}
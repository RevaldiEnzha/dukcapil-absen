<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Asumsi relasi 'pegawai' sudah didefinisikan di model Absensi
        $query = Absensi::with('pegawai');

        // 1. Fitur Pencarian (Nama / NIK Pegawai)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // 2. Filter Waktu
        $waktu = $request->input('waktu', 'hari_ini'); // Default hari ini agar relevan
        
        if ($waktu === 'hari_ini') {
            $query->whereDate('tanggal', Carbon::today());
        } elseif ($waktu === 'minggu_ini') {
            $query->whereBetween('tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($waktu === 'bulan_ini') {
            $query->whereMonth('tanggal', Carbon::now()->month)
                  ->whereYear('tanggal', Carbon::now()->year);
        } elseif ($waktu === 'custom' && $request->filled('tanggal_spesifik')) {
            $query->whereDate('tanggal', $request->tanggal_spesifik);
        }

        // 3. Filter Urutan
        $sort = $request->input('sort', 'terbaru');
        $direction = ($sort === 'terlama') ? 'asc' : 'desc';
        
        $query->orderBy('tanggal', $direction)
              ->orderBy('check_in', $direction);

        // Paginasi
        $absensis = $query->paginate(10)->withQueryString();

        return view('admin.absensi.index', compact('absensis'));
    }

    public function exportPdf(Request $request)
    {
        $query = Absensi::with('pegawai');

        // 1. Terapkan Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // 2. Terapkan Filter Waktu
        $waktu = $request->input('waktu', 'hari_ini'); 
        $labelWaktu = '';
        
        if ($waktu === 'hari_ini') {
            $query->whereDate('tanggal', Carbon::today());
            $labelWaktu = 'Hari Ini (' . Carbon::today()->translatedFormat('d M Y') . ')';
        } elseif ($waktu === 'minggu_ini') {
            $query->whereBetween('tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            $labelWaktu = 'Minggu Ini (' . Carbon::now()->startOfWeek()->translatedFormat('d M') . ' - ' . Carbon::now()->endOfWeek()->translatedFormat('d M Y') . ')';
        } elseif ($waktu === 'bulan_ini') {
            $query->whereMonth('tanggal', Carbon::now()->month)->whereYear('tanggal', Carbon::now()->year);
            $labelWaktu = 'Bulan ' . Carbon::now()->translatedFormat('F Y');
        } elseif ($waktu === 'custom' && $request->filled('tanggal_spesifik')) {
            $query->whereDate('tanggal', $request->tanggal_spesifik);
            $labelWaktu = 'Tanggal: ' . Carbon::parse($request->tanggal_spesifik)->translatedFormat('d M Y');
        } else {
            $labelWaktu = 'Semua Waktu';
        }

        // 3. Terapkan Filter Urutan
        $sort = $request->input('sort', 'terbaru');
        $direction = ($sort === 'terlama') ? 'asc' : 'desc';
        $query->orderBy('tanggal', $direction)->orderBy('check_in', $direction);

        // Eksekusi Query menggunakan get() agar semua data terambil (tanpa paginasi)
        $absensis = $query->get();

        // Cetak ke PDF
        $pdf = Pdf::loadView('admin.absensi.pdf', compact('absensis', 'labelWaktu'));
        
        // Ukuran kertas A4, mode Portrait
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Data_Absensi_Pegawai.pdf");
    }
}
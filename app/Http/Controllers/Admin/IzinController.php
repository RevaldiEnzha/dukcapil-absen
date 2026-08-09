<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\Absensi; // Wajib dipanggil untuk injeksi data otomatis
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod; // Untuk melooping rentang tanggal

class IzinController extends Controller
{
    public function index(Request $request)
    {
        $query = Izin::with('pegawai');

        $sort = $request->input('sort', 'terbaru');
        if ($sort === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $izins = $query->paginate(10)->withQueryString();

        return view('admin.izin.index', compact('izins'));
    }

    // Fungsi memproses persetujuan, penolakan, dan pembatalan
    public function updateStatus(Request $request, $id)
    {
        $izin = Izin::findOrFail($id);

        // Validasi input mengizinkan 'pending' untuk fitur batal, dan menerima alasan penolakan
        $request->validate([
            'status'           => 'required|in:disetujui,ditolak,pending',
            'alasan_penolakan' => 'nullable|string' // TAMBAHKAN INI
        ]);

        // LOGIKA PEMBATALAN (Kembali ke Menunggu)
        if ($request->status === 'pending') {
            $izin->update([
                'status'           => 'pending',
                'approved_by'      => null,
                'approved_at'      => null,
                'alasan_penolakan' => null, // Kosongkan kembali alasan jika dibatalkan
            ]);

            // PERBAIKAN: Hapus absensi yang berstatus Cuti, Sakit, atau Izin Pribadi
            Absensi::where('pegawai_id', $izin->pegawai_id)
                   ->whereBetween('tanggal', [$izin->tanggal_mulai, $izin->tanggal_selesai])
                   ->whereIn('status', ['Cuti', 'Sakit', 'Izin Pribadi'])
                   ->delete();

            $izin->pegawai->syncStatusCuti();
            return back()->with('status', 'Keputusan berhasil dibatalkan...');
        }

        
        // LOGIKA PERSETUJUAN / PENOLAKAN
        $izin->update([
            'status'           => $request->status,
            'approved_by'      => auth()->id(), 
            'approved_at'      => \Carbon\Carbon::now(),
            // Simpan alasan penolakan hanya jika statusnya ditolak
            'alasan_penolakan' => $request->status === 'ditolak' ? $request->alasan_penolakan : null,
        ]);

        if ($request->status === 'disetujui') {
            $period = CarbonPeriod::create($izin->tanggal_mulai, $izin->tanggal_selesai);
            foreach ($period as $date) {
                Absensi::updateOrCreate(
                    [
                        'pegawai_id' => $izin->pegawai_id, 
                        'tanggal'    => $date->format('Y-m-d')
                    ],
                    [
                        // PERBAIKAN: Gunakan jenis izin aslinya
                        'status' => $izin->jenis_izin 
                    ]
                );
            }
        }

        // Sinkronkan status sesudah disetujui/ditolak
        $izin->pegawai->syncStatusCuti();
        
        $pesan = $request->status === 'disetujui' ? 'Permohonan izin berhasil disetujui!' : 'Permohonan izin telah ditolak.';
        return back()->with('status', $pesan);
    }
}
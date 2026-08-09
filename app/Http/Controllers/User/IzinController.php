<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Izin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IzinController extends Controller
{
    public function index()
    {
        $pegawai = Auth::user()->pegawai;
        
        // Ambil riwayat izin dari yang paling baru
        $izins = Izin::where('pegawai_id', $pegawai->id)
                     ->orderBy('created_at', 'desc')
                     ->get();

        return view('user.izin.index', compact('pegawai', 'izins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_izin'      => 'required|in:Sakit,Izin Pribadi,Cuti',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string',
            'lampiran'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', 
        ]);

        $lampiranPath = null;
        
        // --- PERBAIKAN: Pindahkan file ke folder public/uploads ---
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            // Buat nama file unik agar tidak bentrok
            $namaFile = time() . '_' . $file->getClientOriginalName();
            // Pindahkan langsung ke folder public/uploads/lampiran_izin
            $file->move(public_path('uploads/lampiran_izin'), $namaFile);
            // Simpan path-nya ke database
            $lampiranPath = 'uploads/lampiran_izin/' . $namaFile;
        }

        Izin::create([
            'pegawai_id'      => Auth::user()->pegawai->id,
            'jenis_izin'      => $request->jenis_izin,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan'          => $request->alasan,
            'lampiran'        => $lampiranPath,
            'status'          => 'pending',
        ]);

        return back()->with('success', 'Pengajuan izin berhasil dikirim dan menunggu persetujuan.');
    }

    public function destroy($id)
    {
        $izin = Izin::where('id', $id)->where('pegawai_id', Auth::user()->pegawai->id)->firstOrFail();

        // --- PERBAIKAN: Hapus file fisik dari folder public ---
        if ($izin->lampiran && file_exists(public_path($izin->lampiran))) {
            unlink(public_path($izin->lampiran));
        }

        $izin->delete();

        return back()->with('success', 'Pengajuan izin berhasil dihapus.');
    }
}
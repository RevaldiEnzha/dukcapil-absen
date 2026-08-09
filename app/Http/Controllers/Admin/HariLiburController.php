<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
    public function index()
    {
        // Menampilkan daftar hari libur dari yang paling baru
        $liburs = HariLibur::orderBy('tanggal', 'desc')->paginate(10);
        return view('admin.hari_libur.index', compact('liburs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'     => 'required|date|unique:hari_liburs,tanggal',
            'jenis_libur' => 'required|in:Tanggal Merah,Cuti Bersama',
            'keterangan'  => 'required|string|max:255',
        ], [
            'tanggal.unique' => 'Gagal: Tanggal ini sudah didaftarkan sebagai hari libur sebelumnya.'
        ]);

        HariLibur::create([
            'tanggal'     => $request->tanggal,
            'jenis_libur' => $request->jenis_libur,
            'keterangan'  => $request->keterangan,
        ]);

        return back()->with('status', 'Hari libur berhasil ditambahkan ke kalender!');
    }

    public function destroy($id)
    {
        HariLibur::findOrFail($id)->delete();
        return back()->with('status', 'Hari libur berhasil dihapus dari kalender.');
    }
}
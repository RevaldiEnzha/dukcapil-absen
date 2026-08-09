<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    // Menampilkan daftar pegawai (termasuk Pencarian, Filter, & Paginasi)
    public function index(Request $request)
    {
        // ========================================================
        // RADAR MASSAL: Sinkronkan status semua pegawai sebelum tabel dimuat
        // Kita hanya mengecek yang statusnya Aktif atau Cuti (Tidak Aktif diabaikan)
        $semuaPegawai = Pegawai::whereIn('status', ['Aktif', 'Cuti'])->get();
        foreach ($semuaPegawai as $p) {
            $p->syncStatusCuti();
        }
        // ========================================================

        $query = Pegawai::query();

        // 1. Fitur Pencarian (Berdasarkan Nama atau NIK)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
        }

        // 2. Fitur Filter (Pengurutan Terbaru/Terlama)
        $sort = $request->input('sort', 'terbaru'); // Default 'terbaru'
        if ($sort === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // 3. Paginasi (10 data per halaman) + Bawa parameter query string agar tidak hilang saat pindah halaman
        $pegawais = $query->paginate(10)->withQueryString();

        return view('admin.pegawai.index', compact('pegawais'));
    }

    // --- KERANGKA FUNGSI AKSI (Kosong untuk saat ini) ---
    
    public function store(Request $request)
    {
        // 1. Validasi Input (Tambahkan validasi password)
        $validated = $request->validate([
            'nik'           => 'required|string|size:16|unique:pegawais,nik',
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'status'        => 'required|in:Aktif,Tidak Aktif,Cuti',
            'password'      => 'required|string|min:6', // <--- VALIDASI PASSWORD BARU
        ], [
            'nik.required'  => 'NIK wajib diisi.',
            'nik.size'      => 'NIK harus tepat 16 digit.',
            'nik.unique'    => 'NIK ini sudah terdaftar di sistem.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
            'password.required' => 'Password akun wajib diisi.',
            'password.min'  => 'Password minimal 6 karakter.',
        ]);

        // 2. Buat Akun Login (User)
        $user = User::create([
            'username' => $validated['nik'],  
            'password' => Hash::make($validated['password']), // <--- GUNAKAN PASSWORD DARI FORM
            'role'     => 'pegawai', 
        ]);

        // 3. Simpan ke Tabel Pegawai (Dipetakan manual agar 'password' tidak ikut masuk)
        Pegawai::create([
            'user_id'       => $user->id,
            'nik'           => $validated['nik'],
            'nama'          => $validated['nama'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'no_hp'         => $validated['no_hp'],
            'alamat'        => $validated['alamat'],
            'status'        => $validated['status'],
        ]);

        // 4. Kembalikan notifikasi sukses (Hapus teks sandi default)
        return redirect()->route('admin.pegawai.index')
                         ->with('status', 'Pegawai berhasil ditambahkan! Username: ' . $validated['nik']);
    }

    public function edit($id)
    {
        // Logika ambil data edit nanti di sini
    }

    // Memproses update data pegawai
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        // 1. Validasi Input
        $validated = $request->validate([
            'nik'           => 'required|string|size:16|unique:pegawais,nik,' . $pegawai->id,
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'status'        => 'required|in:Aktif,Tidak Aktif,Cuti',
            'password'      => 'nullable|string|min:6', // Password opsional saat edit
        ], [
            'nik.required'  => 'NIK wajib diisi.',
            'nik.size'      => 'NIK harus tepat 16 digit.',
            'nik.unique'    => 'NIK ini sudah terdaftar di sistem.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'password.min'  => 'Password minimal 6 karakter.',
        ]);

        // 2. Update Akun Login (User)
        $user = $pegawai->user;
        $user->username = $validated['nik']; // NIK yang di-update juga mengubah username login
        
        // Hanya update password jika form password diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        // 3. Update Data Pegawai (Nama, Alamat, dll disimpan di tabel pegawais)
        $pegawai->update([
            'nik'           => $validated['nik'],
            'nama'          => $validated['nama'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'no_hp'         => $validated['no_hp'],
            'alamat'        => $validated['alamat'],
            'status'        => $validated['status'],
        ]);

        return redirect()->route('admin.pegawai.index')->with('status', 'Data pegawai berhasil diperbarui!');
    }

    // Memproses hapus data pegawai
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        
        // 1. Soft Delete akun User (agar tidak bisa login lagi)
        if ($pegawai->user) {
            $pegawai->user->delete();
        }

        // 2. Soft Delete profil Pegawai (agar hilang dari daftar tabel Admin)
        $pegawai->delete();
        
        return redirect()->route('admin.pegawai.index')->with('status', 'Data pegawai berhasil dihapus!');
    }
}
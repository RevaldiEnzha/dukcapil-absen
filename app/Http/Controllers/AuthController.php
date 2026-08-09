<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Jika sudah login, cegah masuk ke halaman login
        if (Auth::check()) {
            return Auth::user()->role === 'admin' 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('pegawai.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // 1. CEK STATUS PEGAWAI SEBELUM DILOLOSKAN
            if ($user->role === 'pegawai' && $user->pegawai) {
                // Sinkronkan dulu statusnya sebelum dibaca sistem!
                $user->pegawai->syncStatusCuti();

                $status = strtolower($user->pegawai->status);
                
                // PERBAIKAN: Hanya "Tidak Aktif" yang diblokir login
                if ($status === 'tidak aktif') {
                    Auth::logout(); // Batalkan proses masuk secara paksa
                    
                    return back()->withErrors([
                        'username' => 'Gagal masuk. Akun Anda saat ini Tidak Aktif.',
                    ])->onlyInput('username');
                }
            }

            // 2. JIKA STATUS AMAN (Aktif / Cuti) ATAU ADMIN, LANJUTKAN LOGIN
            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('pegawai.dashboard');
        }

        // Jika salah username atau password
        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
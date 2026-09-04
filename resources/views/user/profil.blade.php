@extends('layouts.user')

@section('title', 'Profil Saya - Pegawai')

@section('content')
<div class="flex flex-col gap-6 max-w-md mx-auto relative pb-10">
    
    <!-- Header Halaman -->
    <div class="text-center mb-2">
        <h1 class="text-2xl font-bold text-primary">Profil Saya</h1>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="mb-2 p-4 bg-green-100 text-green-700 rounded-xl font-bold text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-2 p-4 bg-error-container text-error rounded-xl font-bold text-sm border border-red-200">
            Pastikan password minimal 6 karakter dan konfirmasi password cocok.
        </div>
    @endif

    <!-- Profile Avatar & Info Singkat -->
    <div class="flex flex-col items-center justify-center gap-3">
        <div class="w-24 h-24 rounded-full bg-surface-container-highest border-2 border-primary/20 flex items-center justify-center overflow-hidden shadow-sm text-primary">
            <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1;">account_circle</span>
        </div>
        <div class="text-center">
            <h2 class="text-xl font-bold text-on-surface">{{ $pegawai->nama ?? 'Nama Pegawai' }}</h2>
            <p class="text-sm text-on-surface-variant font-medium">{{ $pegawai->jabatan ?? 'Pegawai Disdukcapil' }}</p>
        </div>
    </div>

    <!-- Kartu Data Lengkap Pegawai -->
    <section class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm border border-outline-variant/30 flex flex-col relative overflow-hidden">
        <!-- Aksen Latar -->
        <div class="absolute inset-0 bg-primary/5 pointer-events-none"></div>
        
        <!-- NIK -->
        <div class="flex items-center gap-4 py-4 border-b border-outline-variant/30 relative z-10">
            <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">badge</span>
            <div class="flex flex-col">
                <span class="font-bold text-on-surface-variant uppercase text-[10px] tracking-wider mb-0.5">NIK</span>
                <span class="font-bold text-on-surface text-sm">{{ $pegawai->nik ?? '-' }}</span>
            </div>
        </div>

        <!-- NAMA -->
        <div class="flex items-center gap-4 py-4 border-b border-outline-variant/30 relative z-10">
            <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">person</span>
            <div class="flex flex-col">
                <span class="font-bold text-on-surface-variant uppercase text-[10px] tracking-wider mb-0.5">Nama Lengkap</span>
                <span class="font-bold text-on-surface text-sm">{{ $pegawai->nama ?? '-' }}</span>
            </div>
        </div>

        <!-- JENIS KELAMIN -->
        <div class="flex items-center gap-4 py-4 border-b border-outline-variant/30 relative z-10">
            <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">wc</span>
            <div class="flex flex-col">
                <span class="font-bold text-on-surface-variant uppercase text-[10px] tracking-wider mb-0.5">Jenis Kelamin</span>
                <span class="font-bold text-on-surface text-sm">
                    @if(in_array(strtolower($pegawai->jenis_kelamin ?? ''), ['l', 'laki-laki', 'pria']))
                        Laki-laki
                    @elseif(in_array(strtolower($pegawai->jenis_kelamin ?? ''), ['p', 'perempuan', 'wanita']))
                        Perempuan
                    @else
                        {{ $pegawai->jenis_kelamin ?? '-' }}
                    @endif
                </span>
            </div>
        </div>

        <!-- STATUS -->
        <div class="flex items-center gap-4 pt-4 relative z-10">
            <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">verified</span>
            <div class="flex flex-col w-full">
                <span class="font-bold text-on-surface-variant uppercase text-[10px] tracking-wider mb-1">Status Pegawai</span>
                @if(strtolower($pegawai->status ?? '') == 'aktif')
                    <span class="inline-flex items-center justify-center bg-primary/10 text-primary px-3 py-1 rounded-full font-bold text-xs w-fit border border-primary/20">
                        Aktif
                    </span>
                @else
                    <span class="inline-flex items-center justify-center bg-surface-variant text-on-surface-variant px-3 py-1 rounded-full font-bold text-xs w-fit border border-outline-variant/50">
                        {{ ucwords($pegawai->status ?? 'Tidak Aktif') }}
                    </span>
                @endif
            </div>
        </div>
    </section>

    <!-- Tombol Aksi -->
    <div class="flex flex-col gap-3 mt-2">
        
        <a href="{{ route('pegawai.riwayat') }}" class="w-full h-14 bg-surface-container-highest text-on-surface-variant rounded-xl font-bold text-sm flex items-center justify-center gap-2 hover:bg-surface-variant transition-colors active:scale-[0.98]">
            <span class="material-symbols-outlined">history</span>
            Lihat Riwayat Absen
        </a>
        
        <!-- PERBAIKAN: Tombol Pengaturan kini memicu Modal -->
        <button type="button" onclick="bukaModalPengaturan()" class="w-full h-14 bg-surface-container-highest text-on-surface-variant rounded-xl font-bold text-sm flex items-center justify-center gap-2 hover:bg-surface-variant transition-colors active:scale-[0.98]">
            <span class="material-symbols-outlined">settings</span>
            Pengaturan Akun
        </button>
        
        <!-- Tombol Keluar (Memanggil Modal) -->
        <form id="form-logout-user" action="{{ url('/logout') }}" method="POST" class="w-full mt-2">
            @csrf
            <button type="button" onclick="bukaModalLogoutUser()" class="w-full h-14 bg-[#ba1a1a] text-white rounded-xl font-bold text-sm flex items-center justify-center gap-2 hover:bg-[#93000a] transition-colors shadow-sm active:scale-[0.98]">
                <span class="material-symbols-outlined">logout</span>
                Keluar
            </button>
        </form>

    </div>

    <!-- ================= MODAL PENGATURAN AKUN ================= -->
    <div id="modal-pengaturan" class="fixed inset-0 bg-on-surface/50 z-[100] transition-opacity opacity-0 hidden items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-surface w-full max-w-md rounded-2xl p-6 pb-10 shadow-xl transform scale-95 transition-transform max-h-[80vh] overflow-y-auto mb-8" id="sheet-pengaturan">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-on-surface">Pengaturan Akun</h2>
                <button onclick="tutupModalPengaturan()" class="text-on-surface-variant p-2 rounded-full hover:bg-surface-container-highest transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form action="{{ route('pegawai.profil.update') }}" method="POST" class="flex flex-col gap-4">
                @csrf
                @method('PUT')
                
                <!-- Akun Info (Read Only) -->
                <div class="bg-surface-container-lowest border border-outline-variant/30 p-3 rounded-lg flex flex-col gap-1 mb-2">
                    <span class="text-[10px] font-bold text-on-surface-variant uppercase">Username Login</span>
                    <span class="font-bold text-on-surface text-sm">{{ Auth::user()->username }}</span>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="font-bold text-sm text-on-surface-variant">Nomor HP</label>
                    <input type="text" name="no_hp" value="{{ $pegawai->no_hp ?? '' }}" class="h-12 rounded-lg border border-outline-variant px-4 bg-surface text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none text-sm">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="font-bold text-sm text-on-surface-variant">Alamat</label>
                    <textarea name="alamat" rows="2" class="rounded-lg border border-outline-variant p-4 bg-surface text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none resize-none text-sm">{{ $pegawai->alamat ?? '' }}</textarea>
                </div>
                
                <!-- Batas Ubah Password -->
                <div class="border-t border-outline-variant/50 pt-4 mt-2">
                    <p class="text-xs font-bold text-[#ba1a1a] mb-3">*Kosongkan form di bawah ini jika tidak ingin mengubah password.</p>
                    
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1.5 w-full">
                            <label class="font-bold text-sm text-on-surface-variant">Password Baru</label>
                            <input type="password" name="password" placeholder="Minimal 6 karakter" class="h-12 rounded-lg border border-outline-variant px-4 bg-surface text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none text-sm">
                        </div>
                        <div class="flex flex-col gap-1.5 w-full">
                            <label class="font-bold text-sm text-on-surface-variant">Konfirmasi Password Baru</label>
                            <!-- 'password_confirmation' adalah penamaan standar Laravel agar validasi berjalan -->
                            <input type="password" name="password_confirmation" placeholder="Ketik ulang password baru" class="h-12 rounded-lg border border-outline-variant px-4 bg-surface text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none text-sm">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-primary text-white font-bold h-14 rounded-xl mt-4 hover:bg-primary/90 active:scale-[0.98] transition-all shadow-md">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <!-- ================= MODAL KONFIRMASI LOGOUT USER ================= -->
    <div id="modal-logout-user" class="fixed inset-0 bg-on-surface/50 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-surface w-full max-w-sm rounded-2xl p-6 shadow-xl transform scale-95 transition-transform" id="sheet-logout-user">
            <div class="w-16 h-16 bg-error-container text-[#ba1a1a] rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[32px]">logout</span>
            </div>
            <h3 class="text-lg font-bold text-on-surface text-center mb-2">Keluar dari Akun?</h3>
            <p class="text-sm text-on-surface-variant text-center mb-6">Pastikan Anda telah menyelesaikan semua proses absensi hari ini sebelum keluar.</p>
            
            <div class="flex gap-3">
                <button type="button" onclick="tutupModalLogoutUser()" class="flex-1 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-bold text-sm hover:bg-surface-container-highest transition-colors">Batal</button>
                <button type="button" onclick="document.getElementById('form-logout-user').submit()" class="flex-1 py-2.5 rounded-lg bg-[#ba1a1a] text-white font-bold text-sm hover:bg-[#93000a] transition-colors shadow-sm">Ya, Keluar</button>
            </div>
        </div>
    </div>

    <script>
        const mPengaturan = document.getElementById('modal-pengaturan');
        const sPengaturan = document.getElementById('sheet-pengaturan');

        function bukaModalPengaturan() {
            mPengaturan.classList.remove('hidden');
            mPengaturan.classList.add('flex');
            setTimeout(() => {
                mPengaturan.classList.remove('opacity-0');
                sPengaturan.classList.remove('scale-95');
            }, 10);
        }

        function tutupModalPengaturan() {
            mPengaturan.classList.add('opacity-0');
            sPengaturan.classList.add('scale-95');
            setTimeout(() => {
                mPengaturan.classList.add('hidden');
                mPengaturan.classList.remove('flex');
            }, 300);
        }

        /* Script Modal Konfirmasi Logout User */
        const mLogoutUser = document.getElementById('modal-logout-user');
        const sLogoutUser = document.getElementById('sheet-logout-user');

        function bukaModalLogoutUser() {
            mLogoutUser.classList.remove('hidden');
            mLogoutUser.classList.add('flex');
            setTimeout(() => {
                mLogoutUser.classList.remove('opacity-0');
                sLogoutUser.classList.remove('scale-95');
            }, 10);
        }

        function tutupModalLogoutUser() {
            mLogoutUser.classList.add('opacity-0');
            sLogoutUser.classList.add('scale-95');
            setTimeout(() => {
                mLogoutUser.classList.add('hidden');
                mLogoutUser.classList.remove('flex');
            }, 300);
        }
    </script>

</div>
@endsection
@extends('layouts.user')

@section('title', 'Dashboard - Pegawai')

@section('content')
<div class="flex flex-col gap-6">
    
    <!-- Greeting Section -->
    <div>
        <h2 class="text-3xl font-bold text-on-surface">Halo,</h2>
        <p class="text-lg text-on-surface-variant">{{ $pegawai->nama ?? 'Pegawai' }}</p>
    </div>

    <!-- Attendance Summary Card -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant p-5 flex flex-col gap-4">
        <div class="flex justify-between items-center border-b border-surface-variant pb-3">
            <h3 class="font-bold text-on-surface text-sm">Kehadiran Hari Ini</h3>
            <span class="text-xs font-medium text-on-surface-variant">{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</span>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <!-- Bagian Check In -->
            <div class="flex flex-col gap-1">
                <span class="text-xs text-on-surface-variant flex items-center gap-1 font-medium">
                    <span class="material-symbols-outlined text-[16px]">login</span> Check In
                </span>
                
                @if($absenHariIni && $absenHariIni->check_in)
                    <span class="text-2xl font-bold text-on-surface">{{ \Carbon\Carbon::parse($absenHariIni->check_in)->format('H:i') }}</span>
                    
                    @if(\Carbon\Carbon::parse($absenHariIni->check_in)->format('H:i') > '07:30')
                        <div class="mt-1 inline-flex w-max items-center px-2.5 py-1 rounded-md bg-error-container text-[#ba1a1a] font-bold text-[10px]">
                            Terlambat
                        </div>
                    @else
                        <div class="mt-1 inline-flex w-max items-center px-2.5 py-1 rounded-md bg-green-100 text-green-700 font-bold text-[10px]">
                            Tepat Waktu
                        </div>
                    @endif
                @else
                    <span class="text-2xl font-bold text-on-surface-variant">-</span>
                    <div class="mt-1 inline-flex w-max items-center px-2.5 py-1 rounded-md bg-surface-variant text-on-surface-variant font-bold text-[10px]">
                        Belum Absen
                    </div>
                @endif
            </div>
            
            <!-- Bagian Check Out -->
            <div class="flex flex-col gap-1 border-l border-outline-variant pl-4">
                <span class="text-xs text-on-surface-variant flex items-center gap-1 font-medium">
                    <span class="material-symbols-outlined text-[16px]">logout</span> Check Out
                </span>
                
                @if($absenHariIni && $absenHariIni->check_out)
                    <span class="text-2xl font-bold text-on-surface">{{ \Carbon\Carbon::parse($absenHariIni->check_out)->format('H:i') }}</span>
                    <div class="mt-1 inline-flex w-max items-center px-2.5 py-1 rounded-md bg-surface-variant text-on-surface-variant font-bold text-[10px]">
                        Selesai
                    </div>
                @else
                    <span class="text-2xl font-bold text-on-surface-variant">-</span>
                    <div class="mt-1 inline-flex w-max items-center px-2.5 py-1 rounded-md bg-surface-variant text-on-surface-variant font-bold text-[10px]">
                        Menunggu
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Action Bento -->
    <div class="grid grid-cols-2 gap-4">
        
        <!-- Tombol Absen Sekarang (Diaktifkan mengarah ke halaman absen) -->
        <a href="{{ route('pegawai.absen') }}" class="col-span-1 bg-primary text-white rounded-2xl p-5 flex flex-col justify-between aspect-square relative overflow-hidden active:scale-95 hover:bg-primary/90 transition-all shadow-sm group">
            <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-[120px]">fingerprint</span>
            </div>
            <div class="z-10 mt-auto">
                <span class="material-symbols-outlined text-[32px] mb-2" style="font-variation-settings: 'FILL' 1;">fingerprint</span>
                <h4 class="font-bold text-lg leading-tight">Absen<br>Sekarang</h4>
            </div>
        </a>
        
        <!-- Tombol Riwayat (Diaktifkan mengarah ke halaman riwayat absensi) -->
        <a href="{{ route('pegawai.riwayat') }}" class="col-span-1 bg-surface-container-lowest text-on-surface rounded-2xl p-5 border border-outline-variant flex flex-col justify-between aspect-square active:scale-95 hover:bg-surface-variant transition-all shadow-sm">
            <div class="bg-primary/10 text-primary p-3 rounded-xl w-max mb-2">
                <span class="material-symbols-outlined text-[24px]">history</span>
            </div>
            <div>
                <h4 class="font-bold text-base leading-tight mb-1">Riwayat Kehadiran</h4>
                <p class="text-xs text-on-surface-variant font-medium">Lihat detail absensi bulan ini</p>
            </div>
        </a>

    </div>

</div>
@endsection
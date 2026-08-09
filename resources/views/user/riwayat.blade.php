@extends('layouts.user')

@section('title', 'Riwayat Absensi - Pegawai')

@section('content')
<div class="flex flex-col h-full max-w-md mx-auto relative pb-10">
    
    <!-- Header Khusus dengan Tombol Kembali -->
    <div class="flex items-center gap-3 mb-6 pt-2">
        <a href="{{ route('pegawai.profil') }}" class="p-2 -ml-2 rounded-full hover:bg-surface-variant active:scale-95 transition-all flex items-center justify-center text-primary">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold text-on-surface">Riwayat Absensi</h1>
    </div>

    <!-- Bagian Filter -->
    <section class="mb-6">
        <form method="GET" action="{{ route('pegawai.riwayat') }}" class="grid grid-cols-2 gap-4">
            <!-- Filter Bulan -->
            <div class="relative">
                <select name="bulan" onchange="this.form.submit()" class="w-full h-14 px-4 pt-5 pb-1 border border-outline-variant rounded-xl bg-surface-container-lowest text-on-surface appearance-none focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-bold text-sm shadow-sm cursor-pointer">
                    @foreach(['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'] as $num => $name)
                        <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <label class="absolute left-4 top-2 text-[10px] text-on-surface-variant font-bold uppercase tracking-wider">Pilih Bulan</label>
                <span class="material-symbols-outlined absolute right-3 top-4 text-on-surface-variant pointer-events-none">expand_more</span>
            </div>

            <!-- Filter Tahun -->
            <div class="relative">
                <select name="tahun" onchange="this.form.submit()" class="w-full h-14 px-4 pt-5 pb-1 border border-outline-variant rounded-xl bg-surface-container-lowest text-on-surface appearance-none focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-bold text-sm shadow-sm cursor-pointer">
                    @php $currentYear = date('Y'); @endphp
                    @for($i = $currentYear; $i >= $currentYear - 3; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                <label class="absolute left-4 top-2 text-[10px] text-on-surface-variant font-bold uppercase tracking-wider">Pilih Tahun</label>
                <span class="material-symbols-outlined absolute right-3 top-4 text-on-surface-variant pointer-events-none">expand_more</span>
            </div>
        </form>
    </section>

    <!-- Daftar Riwayat Absensi -->
    <section class="flex flex-col gap-4 flex-1">
        
        @forelse($absensis as $absen)
            @php
                $statusL = strtolower($absen->status);
                
                // Konfigurasi Warna Status Default (Misal: Alpha/Kosong)
                $bgClass = 'bg-error-container/50';
                $textClass = 'text-[#ba1a1a]';
                $badgeText = strtoupper($absen->status);

                if (in_array($statusL, ['tepat waktu', 'hadir'])) {
                    $bgClass = 'bg-[#e8f5e9]';
                    $textClass = 'text-[#2e7d32]';
                    $badgeText = 'HADIR';
                } elseif ($statusL == 'terlambat') {
                    $bgClass = 'bg-[#fff3e0]';
                    $textClass = 'text-[#e65100]';
                } elseif (in_array($statusL, ['izin', 'sakit', 'cuti'])) {
                    $bgClass = 'bg-[#e3f2fd]';
                    $textClass = 'text-[#1565c0]';
                }
            @endphp

            <div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm border border-outline-variant/30 flex flex-col gap-3 transition-all hover:shadow-md">
                
                <!-- Tanggal & Badge Status -->
                <div class="flex justify-between items-start border-b border-outline-variant/30 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">calendar_today</span>
                        <span class="font-bold text-on-surface">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-md {{ $bgClass }} {{ $textClass }} font-bold text-[10px] uppercase tracking-wide">
                        {{ $badgeText }}
                    </span>
                </div>

                @if(in_array($statusL, ['izin', 'sakit', 'cuti']))
                    <!-- Tampilan Jika Izin / Sakit -->
                    <div class="flex justify-center items-center py-2">
                        <span class="text-sm font-medium text-on-surface-variant italic text-center">
                            {{ $absen->keterangan_izin ?? 'Keterangan tidak tersedia' }}
                        </span>
                    </div>
                @else
                    <!-- Tampilan Waktu Jika Hadir / Terlambat -->
                    <div class="flex justify-between items-center px-1">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-0.5">Check-in</span>
                            <span class="text-lg font-bold text-on-surface">{{ $absen->check_in ? \Carbon\Carbon::parse($absen->check_in)->format('H:i') : '--:--' }}</span>
                        </div>
                        <span class="material-symbols-outlined text-outline-variant text-[20px]">arrow_forward</span>
                        <div class="flex flex-col text-right">
                            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-0.5">Check-out</span>
                            <span class="text-lg font-bold text-on-surface">{{ $absen->check_out ? \Carbon\Carbon::parse($absen->check_out)->format('H:i') : '--:--' }}</span>
                        </div>
                    </div>
                @endif
                
            </div>
        @empty
            <!-- State Jika Data Kosong -->
            <div class="text-center py-12 bg-surface-container-lowest border border-outline-variant/30 rounded-xl mt-4">
                <span class="material-symbols-outlined text-outline text-5xl mb-3">history_toggle_off</span>
                <p class="text-base font-bold text-on-surface">Belum ada riwayat</p>
                <p class="text-xs font-medium text-on-surface-variant mt-1">Tidak ada data absensi untuk bulan dan tahun ini.</p>
            </div>
        @endforelse

    </section>
</div>
@endsection
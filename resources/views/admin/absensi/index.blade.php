@extends('layouts.admin')

@section('title', 'Kelola Absensi - Disdukcapil')

@section('content')
<!-- Page Header & Action -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 w-full gap-4">
    <div class="text-left">
        <h1 class="font-headline-lg text-3xl font-bold text-on-surface mb-1">Kelola Absensi</h1>
        <p class="font-body-sm text-on-surface-variant">Pantau riwayat kehadiran dan status harian pegawai.</p>
    </div>
</div>

<!-- Data Table Card -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
    
    <!-- Filter Bar -->
    <!-- Filter Bar (DIPERBAIKI: Ditambah ID) -->
    <form id="form-filter-absensi" method="GET" action="{{ route('admin.absensi.index') }}" class="p-5 border-b border-outline-variant bg-surface-container-low/30 flex flex-col lg:flex-row gap-4 justify-between items-center w-full">
        
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
            <!-- Search -->
            <div class="relative w-full sm:w-64">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
                <input name="search" value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 bg-white border border-outline-variant rounded-lg text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-shadow" placeholder="Cari pegawai..." type="text">
            </div>

            <!-- Container Dropdown Waktu -->
            <div id="container_dropdown" class="relative w-full sm:w-auto {{ request('waktu') == 'custom' ? 'hidden' : 'block' }}">
                <select name="waktu" id="filter_waktu" onchange="toggleCustomDate(this.value)" class="appearance-none pl-10 pr-10 py-2 border border-outline-variant rounded-lg text-on-surface text-sm font-bold focus:border-primary focus:ring-1 focus:ring-primary outline-none cursor-pointer bg-white w-full">
                    <option value="hari_ini" {{ request('waktu') == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="minggu_ini" {{ request('waktu') == 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan_ini" {{ request('waktu') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="semua" {{ request('waktu') == 'semua' ? 'selected' : '' }}>Semua Waktu</option>
                    <option value="custom" {{ request('waktu') == 'custom' ? 'selected' : '' }}>Pilih Tanggal...</option>
                </select>
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] pointer-events-none text-outline">calendar_today</span>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[20px] pointer-events-none text-outline">expand_more</span>
            </div>

            <!-- Container Input Tanggal Spesifik -->
            <div id="container_custom_date" class="relative w-full sm:w-auto {{ request('waktu') == 'custom' ? 'flex' : 'hidden' }} items-center gap-2">
                <input type="date" name="tanggal_spesifik" id="tanggal_spesifik" value="{{ request('tanggal_spesifik') }}" class="px-4 py-2 bg-white border border-outline-variant rounded-lg text-sm font-bold focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-shadow w-full sm:w-auto">
                
                <!-- PERBAIKAN: Tambahkan type="button" dan ubah fungsi onclick menjadi resetCustomDate() -->
                <button type="button" onclick="resetCustomDate()" title="Batal Pilih Tanggal" class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-lg bg-error-container text-on-error-container hover:bg-[#ba1a1a] hover:text-white transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Filter Urutan -->
            <div class="relative w-full sm:w-auto">
                <select name="sort" class="appearance-none pl-10 pr-10 py-2 border border-outline-variant rounded-lg text-on-surface text-sm font-bold focus:border-primary focus:ring-1 focus:ring-primary outline-none cursor-pointer bg-white w-full">
                    <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                </select>
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] pointer-events-none text-outline">sort</span>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[20px] pointer-events-none text-outline">expand_more</span>
            </div>

            <button type="submit" class="px-4 py-2 bg-surface-container-highest hover:bg-surface-variant text-on-surface rounded-lg font-bold text-sm transition-colors w-full sm:w-auto">
                Terapkan
            </button>
        </div>

        <!-- Tombol Export (Dummy) -->
        <button type="button" class="inline-flex items-center justify-center gap-2 bg-primary text-white font-bold px-6 py-2.5 rounded-lg hover:bg-primary-container hover:shadow-md transition-all active:scale-95 whitespace-nowrap w-full lg:w-auto">
            <span class="material-symbols-outlined text-[20px]">download</span>
            Export Data
        </button>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant whitespace-nowrap">Nama Pegawai</th>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant">Tanggal</th>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant">Check In</th>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant">Check Out</th>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant bg-white">
                @forelse($absensis as $absen)
                
                <!-- Logika Pemeriksaan Waktu & Status -->
                @php
                    $isLate = false;
                    $checkInDisplay = '--:--';
                    $checkOutDisplay = '--:--';
                    
                    // Memeriksa status khusus (Cuti / Tidak Aktif)
                    $isSpecialStatus = in_array(strtolower($absen->status), ['cuti', 'tidak aktif']);

                    if (!$isSpecialStatus) {
                        if ($absen->check_in) {
                            $checkInDisplay = \Carbon\Carbon::parse($absen->check_in)->format('H:i');
                            if ($checkInDisplay > '07:30') {
                                $isLate = true;
                            }
                        }
                        if ($absen->check_out) {
                            $checkOutDisplay = \Carbon\Carbon::parse($absen->check_out)->format('H:i');
                        }
                    } else {
                        $checkInDisplay = '-';
                        $checkOutDisplay = '-';
                    }
                @endphp

                <tr class="hover:bg-surface-container-lowest transition-colors">
                    <!-- Kolom Nama -->
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary-fixed text-primary flex items-center justify-center font-bold text-[12px]">
                                {{ strtoupper(substr($absen->pegawai->nama, 0, 2)) }}
                            </div>
                            <div>
                                <span class="text-sm text-on-surface font-medium block">{{ $absen->pegawai->nama }}</span>
                                <span class="text-[12px] text-on-surface-variant">NIK: {{ substr($absen->pegawai->nik, 0, 8) }}...</span>
                            </div>
                        </div>
                    </td>
                    
                    <!-- Kolom Tanggal -->
                    <td class="py-4 px-6 text-sm text-on-surface whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}
                    </td>
                    
                    <!-- Kolom Check In -->
                    <td class="py-4 px-6 text-sm font-bold {{ $isLate ? 'text-[#ba1a1a]' : 'text-on-surface' }}">
                        {{ $checkInDisplay }}
                    </td>
                    
                    <!-- Kolom Check Out -->
                    <td class="py-4 px-6 text-sm text-on-surface-variant">
                        {{ $checkOutDisplay }}
                    </td>
                    
                    <!-- Kolom Status (Badge) -->
                    <td class="py-4 px-6 text-center">
                        @if(strtolower($absen->status) == 'cuti')
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-md text-[12px] font-bold bg-secondary-container/30 text-secondary border border-secondary/20">Cuti</span>
                        @elseif(strtolower($absen->status) == 'tidak aktif')
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-md text-[12px] font-bold bg-surface-variant text-on-surface-variant border border-outline-variant">Tidak Aktif</span>
                        @elseif($absen->check_in && !$absen->check_out)
                            @if($isLate)
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-md text-[12px] font-bold bg-error-container text-on-error-container border border-red-200">Terlambat</span>
                            @else
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-md text-[12px] font-bold bg-primary-fixed text-primary border border-primary/20">Tepat Waktu</span>
                            @endif
                        @elseif($absen->check_in && $absen->check_out)
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-md text-[12px] font-bold bg-surface-container-high text-on-surface-variant border border-outline-variant">Selesai</span>
                        @else
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-md text-[12px] font-bold bg-surface-variant text-on-surface-variant">Belum Absen</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-on-surface-variant">Tidak ada data absensi ditemukan untuk filter ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginasi -->
    <div class="p-4 border-t border-outline-variant bg-white">
        {{ $absensis->links() }}
    </div>
</div>

<!-- Paginasi (Kode tabel dan paginasi sebelumnya biarkan di atas ini) -->

<script>
    // Fungsi untuk mengubah dropdown menjadi kotak tanggal secara instan
    function toggleCustomDate(value) {
        if (value === 'custom') {
            document.getElementById('container_dropdown').classList.add('hidden');
            document.getElementById('container_dropdown').classList.remove('block');
            
            document.getElementById('container_custom_date').classList.remove('hidden');
            document.getElementById('container_custom_date').classList.add('flex');
            
            document.getElementById('tanggal_spesifik').focus();
        }
    }

    // Fungsi untuk mereset dan langsung submit form kembali ke 'hari_ini'
    function resetCustomDate() {
        // Kembalikan nilai dropdown ke 'hari_ini'
        document.getElementById('filter_waktu').value = 'hari_ini';
        document.getElementById('tanggal_spesifik').value = '';
        
        // Sembunyikan kotak tanggal, tampilkan kembali dropdown
        document.getElementById('container_custom_date').classList.remove('flex');
        document.getElementById('container_custom_date').classList.add('hidden');
        
        document.getElementById('container_dropdown').classList.remove('hidden');
        document.getElementById('container_dropdown').classList.add('block');
        
        // Eksekusi submit form berdasarkan ID yang baru kita buat
        document.getElementById('form-filter-absensi').submit();
    }
</script>
@endsection
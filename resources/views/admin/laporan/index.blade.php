@extends('layouts.admin')

@section('title', 'Laporan Rekapitulasi - Disdukcapil')

@section('content')
<!-- Page Header & Export Action -->
<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6 w-full">
    <div>
        <h2 class="font-headline-lg text-3xl font-bold text-on-background mb-1">Laporan Rekapitulasi Kehadiran</h2>
        <p class="font-body-lg text-sm text-on-surface-variant">Ringkasan data kehadiran seluruh pegawai Disdukcapil.</p>
    </div>
    
    <!-- Tombol Export Data (AKTIF) -->
    <a href="{{ route('admin.laporan.export-pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-surface text-primary border border-primary/30 rounded-lg font-bold text-sm hover:bg-primary-fixed hover:border-primary transition-all shadow-sm self-start sm:self-auto">
        <span class="material-symbols-outlined text-[20px]">download</span>
        Ekspor Data PDF
    </a>
</div>

<!-- Filter Section -->
<div class="bg-surface rounded-xl shadow-sm border border-outline-variant/50 p-5 mb-8">
    <div class="flex items-center gap-2 mb-4 text-on-surface font-bold text-sm">
        <span class="material-symbols-outlined text-primary text-[20px]">filter_list</span>
        Filter Berdasarkan Waktu
    </div>
    
    <!-- PERBAIKAN: Menggunakan Flexbox agar lebarnya menyesuaikan konten, tidak memanjang penuh -->
    <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-col sm:flex-row items-end gap-4">
        
        <!-- Pilih Bulan -->
        <div class="w-full sm:w-48">
            <label class="block font-bold text-xs text-on-surface-variant mb-1.5">Pilih Bulan</label>
            <div class="relative">
                <select name="bulan" class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface text-on-surface text-sm focus:ring-1 focus:ring-primary focus:border-primary appearance-none cursor-pointer outline-none transition-shadow">
                    @foreach($daftarBulan as $key => $namaBulan)
                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $namaBulan }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
            </div>
        </div>

        <!-- Pilih Tahun -->
        <div class="w-full sm:w-32">
            <label class="block font-bold text-xs text-on-surface-variant mb-1.5">Pilih Tahun</label>
            <div class="relative">
                <select name="tahun" class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface text-on-surface text-sm focus:ring-1 focus:ring-primary focus:border-primary appearance-none cursor-pointer outline-none transition-shadow">
                    @php $tahunSekarang = date('Y'); @endphp
                    @for($i = $tahunSekarang; $i >= $tahunSekarang - 3; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
            </div>
        </div>

        <!-- Tombol Tampilkan (PERBAIKAN: Warna diubah menjadi Biru Primary) -->
        <div class="w-full sm:w-auto">
            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-primary text-white rounded-lg font-bold text-sm hover:bg-primary/90 transition-all shadow-sm flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[20px]">search</span>
                Tampilkan
            </button>
        </div>
        
    </form>
</div>

<!-- Data Table Card -->
<div class="bg-surface rounded-xl shadow-sm border border-outline-variant/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant text-on-surface-variant font-bold text-sm">
                    <th class="py-4 px-6 font-semibold w-24">NIK</th>
                    <th class="py-4 px-6 font-semibold">Nama Pegawai</th>
                    <th class="py-4 px-4 font-semibold text-center w-24">Hadir</th>
                    <th class="py-4 px-4 font-semibold text-center w-24">Terlambat</th>
                    <th class="py-4 px-4 font-semibold text-center w-24">Izin/Sakit</th>
                    <th class="py-4 px-4 font-semibold text-center w-24">Alpha</th>
                    <th class="py-4 px-6 font-semibold text-right w-32">% Hadir</th>
                </tr>
            </thead>
            <tbody class="text-on-surface text-sm divide-y divide-outline-variant/30">
                
                @forelse($pegawais as $pegawai)
                
                <!-- Logika Perhitungan Matematis per Pegawai -->
                @php
                    $hadir = $pegawai->hadir_count;
                    $terlambat = $pegawai->terlambat_count;
                    $izin = $pegawai->cuti_count;
                    
                    // Alpha = Total hari kerja (22) - (Hadir + Izin). Jika minus, jadikan 0.
                    $alpha = max(0, $totalHariKerja - ($hadir + $izin));
                    
                    // Persentase = (Hadir / Total hari kerja) * 100
                    $persentase = ($hadir / $totalHariKerja) * 100;
                @endphp

                <tr class="hover:bg-surface-container-lowest transition-colors">
                    <td class="py-4 px-6 font-medium text-primary">{{ substr($pegawai->nik, 0, 8) }}...</td>
                    <td class="py-4 px-6">{{ $pegawai->nama }}</td>
                    
                    <td class="py-4 px-4 text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $hadir > 0 ? 'bg-primary-fixed text-primary' : 'bg-surface-variant text-on-surface-variant' }} font-bold text-xs">{{ $hadir }}</span>
                    </td>
                    
                    <td class="py-4 px-4 text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $terlambat > 0 ? 'bg-error-container text-[#ba1a1a]' : 'bg-surface-variant text-on-surface-variant' }} font-bold text-xs">{{ $terlambat }}</span>
                    </td>
                    
                    <td class="py-4 px-4 text-center font-bold text-on-surface-variant">{{ $izin }}</td>
                    <td class="py-4 px-4 text-center font-bold text-on-surface-variant">{{ $alpha }}</td>
                    
                    <!-- Warna Persentase berubah merah jika di bawah 80% -->
                    <td class="py-4 px-6 text-right font-bold {{ $persentase < 80 ? 'text-[#ba1a1a]' : 'text-primary' }}">
                        {{ number_format($persentase, 1) }}%
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-on-surface-variant">Tidak ada data pegawai.</td>
                </tr>
                @endforelse
                
            </tbody>
        </table>
    </div>

    <!-- Paginasi -->
    <div class="p-4 border-t border-outline-variant bg-surface-container-lowest">
        {{ $pegawais->links() }}
    </div>
</div>
@endsection
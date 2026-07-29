@extends('layouts.admin')

@section('title', 'Dashboard Admin - Sistem Absensi')

@section('content')
<div class="mb-8">
    <h3 class="font-headline-lg text-[28px] md:text-2xl font-bold text-on-surface mb-2">Overview Hari Ini</h3>
    <p class="font-body-lg text-on-surface-variant">Ringkasan kehadiran pegawai Disdukcapil Kota Cirebon.</p>
</div>

<!-- Stats Bento Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Stat Card 1 -->
    <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm border border-surface-variant hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary/5 rounded-full group-hover:scale-110 transition-transform"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div class="p-3 bg-primary-container text-on-primary-container rounded-lg">
                <span class="material-symbols-outlined text-3xl">groups</span>
            </div>
        </div>
        <div class="relative z-10">
            <p class="font-body-lg text-sm text-on-surface-variant mb-1">Total Pegawai</p>
            <p class="font-headline-lg text-2xl text-primary font-bold">{{ $totalPegawai }}</p>
        </div>
    </div>

    <!-- Stat Card 2 -->
    <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm border border-surface-variant hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div class="p-3 bg-green-100 text-green-700 rounded-lg">
                <span class="material-symbols-outlined text-3xl">check_circle</span>
            </div>
        </div>
        <div class="relative z-10">
            <p class="font-body-lg text-sm text-on-surface-variant mb-1">Hadir Hari Ini</p>
            <p class="font-headline-lg text-2xl text-on-surface font-bold">{{ $hadirHariIni }}</p>
        </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm border border-surface-variant hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div class="p-3 bg-amber-100 text-amber-700 rounded-lg">
                <span class="material-symbols-outlined text-3xl">schedule</span>
            </div>
        </div>
        <div class="relative z-10">
            <p class="font-body-lg text-sm text-on-surface-variant mb-1">Terlambat</p>
            <p class="font-headline-lg text-2xl text-on-surface font-bold">{{ $terlambat }}</p>
        </div>
    </div>

    <!-- Stat Card 4 -->
    <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm border border-surface-variant hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div class="p-3 bg-error-container text-on-error-container rounded-lg">
                <span class="material-symbols-outlined text-3xl">event_busy</span>
            </div>
        </div>
        <div class="relative z-10">
            <p class="font-body-lg text-sm text-on-surface-variant mb-1">Izin/Sakit</p>
            <p class="font-headline-lg text-2xl text-on-surface font-bold">{{ $izinSakit }}</p>
        </div>
    </div>

</div>

<!-- Tabel Kehadiran Terbaru -->
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-surface-variant">
    <div class="flex justify-between items-center mb-6">
        <h4 class="font-headline-md text-lg font-bold text-on-surface">Kehadiran Terbaru Hari Ini</h4>
        <button class="text-primary font-label-bold hover:underline text-sm">Lihat Semua</button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-surface-container-highest">
                    <th class="py-3 font-label-bold text-on-surface-variant text-sm">Nama Pegawai</th>
                    <th class="py-3 font-label-bold text-on-surface-variant text-sm">Waktu</th>
                    <th class="py-3 font-label-bold text-on-surface-variant text-sm">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-highest">
                @forelse($absensiTerbaru as $absen)
                <tr>
                    <td class="py-4 font-body-lg text-on-surface">{{ $absen->pegawai->nama ?? 'Unknown' }}</td>
                    <td class="py-4 font-body-lg text-on-surface-variant">{{ \Carbon\Carbon::parse($absen->check_in)->format('H:i') }}</td>
                    <td class="py-4">
                        @if($absen->status == 'Tepat Waktu' || $absen->status == 'Hadir')
                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">Hadir</span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-error-container text-on-error-container text-xs font-bold">{{ $absen->status }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-4 text-center text-sm text-on-surface-variant">Belum ada data absensi hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
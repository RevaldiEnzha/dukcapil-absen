@extends('layouts.admin')

@section('title', 'Kelola Izin - Disdukcapil')

@section('content')
<!-- Page Header & Action -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 w-full gap-4">
    <div class="text-left">
        <h1 class="font-headline-lg text-3xl font-bold text-on-surface mb-1">Kelola Izin</h1>
        <p class="font-body-sm text-on-surface-variant">Persetujuan permohonan cuti dan izin pegawai.</p>
    </div>
    
    <!-- Filter Bar (Kanan Atas) -->
    <form id="form-filter-izin" method="GET" action="{{ route('admin.izin.index') }}" class="flex gap-2">
        <div class="relative w-full sm:w-auto">
            <select name="sort" onchange="document.getElementById('form-filter-izin').submit()" class="appearance-none pl-10 pr-10 py-2 border border-outline-variant rounded-lg text-on-surface text-sm font-bold focus:border-primary focus:ring-1 focus:ring-primary outline-none cursor-pointer bg-surface-container-lowest shadow-sm hover:bg-surface-container-low transition-colors w-full">
                <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
            </select>
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] pointer-events-none text-outline">filter_list</span>
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[20px] pointer-events-none text-outline">expand_more</span>
        </div>
    </form>
</div>

<!-- Alert Notifikasi -->
@if(session('status'))
<div class="mb-4 p-4 bg-primary-fixed text-primary rounded-lg border border-primary/20 font-bold flex justify-between items-center" id="alert-notifikasi">
    <span>{{ session('status') }}</span>
    <button type="button" onclick="document.getElementById('alert-notifikasi').remove()" class="text-primary hover:bg-primary/20 p-1 rounded-full flex items-center transition-colors">
        <span class="material-symbols-outlined text-[20px]">close</span>
    </button>
</div>
@endif

<!-- Data Table Card -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant whitespace-nowrap">Pegawai</th>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant whitespace-nowrap">Jenis</th>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant whitespace-nowrap">Tanggal</th>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant whitespace-nowrap">Status</th>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant text-right whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant bg-white">
                @forelse($izins as $izin)
                
                <!-- Logika Format Tanggal -->
                @php
                    $start = \Carbon\Carbon::parse($izin->tanggal_mulai);
                    $end = \Carbon\Carbon::parse($izin->tanggal_selesai);
                    
                    if ($start->isSameDay($end)) {
                        $tanggalDisplay = $start->translatedFormat('d M Y');
                    } elseif ($start->isSameMonth($end)) {
                        $tanggalDisplay = $start->format('d') . ' - ' . $end->translatedFormat('d M Y');
                    } else {
                        $tanggalDisplay = $start->translatedFormat('d M') . ' - ' . $end->translatedFormat('d M Y');
                    }
                @endphp

                <tr class="hover:bg-surface-container-lowest transition-colors">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary-fixed text-primary flex items-center justify-center font-bold text-[12px]">
                                {{ strtoupper(substr($izin->pegawai->nama, 0, 2)) }}
                            </div>
                            <span class="text-sm text-on-surface font-medium">{{ $izin->pegawai->nama }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm text-on-surface-variant">{{ $izin->jenis_izin }}</td>
                    <td class="py-4 px-6 text-sm text-on-surface-variant">{{ $tanggalDisplay }}</td>
                    <!-- ... kode tanggalDisplay sebelumnya ... -->
                    <td class="py-4 px-6">
                        <!-- PERBAIKAN: Cek 'pending' sesuai migration -->
                        @if(strtolower($izin->status) == 'pending' || strtolower($izin->status) == 'menunggu')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-secondary-container/30 text-secondary border border-secondary/20">Menunggu</span>
                        @elseif(strtolower($izin->status) == 'disetujui')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">Disetujui</span>
                        @elseif(strtolower($izin->status) == 'ditolak')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-error-container text-on-error-container border border-red-200">Ditolak</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-right">
                        <!-- PERBAIKAN: Kirim ID, alasan, dan lampiran ke JS -->
                        <button type="button" 
                                onclick="openDetailModal('{{ $izin->id }}', '{{ $izin->pegawai->nama }}', '{{ $izin->jenis_izin }}', '{{ $tanggalDisplay }}', '{{ addslashes($izin->alasan) }}', '{{ $izin->lampiran }}', '{{ $izin->status }}', '{{ addslashes($izin->alasan_penolakan ?? '') }}')" 
                                class="px-4 py-2 border border-primary text-primary font-bold text-xs rounded-lg hover:bg-primary/5 transition-colors">
                            Detail
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-on-surface-variant">Belum ada permohonan izin/cuti.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginasi -->
    <div class="p-4 border-t border-outline-variant bg-white">
        {{ $izins->links() }}
    </div>
</div>

<!-- ================= MODAL DETAIL IZIN ================= -->
<div class="fixed inset-0 z-[100] hidden" id="modal-detail-izin">
    <div class="absolute inset-0 bg-on-surface/40 backdrop-blur-sm transition-opacity" onclick="document.getElementById('modal-detail-izin').classList.add('hidden')"></div>
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4 pointer-events-none">
        <div class="bg-surface-container-lowest rounded-xl w-full max-w-lg shadow-xl pointer-events-auto flex flex-col max-h-[90vh] overflow-hidden">
            
            <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface">
                <h3 class="text-lg text-on-surface font-bold">Detail Permohonan Izin</h3>
                <button type="button" class="text-on-surface-variant hover:bg-surface-container-highest transition-colors rounded-full p-1" onclick="document.getElementById('modal-detail-izin').classList.add('hidden')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1 bg-surface-container-lowest flex flex-col gap-4 text-sm">
                
                <div class="grid grid-cols-3 gap-4 border-b border-outline-variant pb-4">
                    <div class="text-on-surface-variant">Pegawai</div>
                    <div class="col-span-2 font-bold text-on-surface" id="detail-nama"></div>
                </div>
                
                <div class="grid grid-cols-3 gap-4 border-b border-outline-variant pb-4">
                    <div class="text-on-surface-variant">Jenis Izin</div>
                    <div class="col-span-2 font-bold text-on-surface" id="detail-jenis"></div>
                </div>
                
                <div class="grid grid-cols-3 gap-4 border-b border-outline-variant pb-4">
                    <div class="text-on-surface-variant">Tanggal</div>
                    <div class="col-span-2 font-bold text-on-surface" id="detail-tanggal"></div>
                </div>

                <div class="flex flex-col gap-2 border-b border-outline-variant pb-4">
                    <div class="text-on-surface-variant">Alasan / Keterangan:</div>
                    <div class="p-3 bg-surface-container-low rounded-lg text-on-surface" id="detail-alasan"></div>
                </div>

                <!-- TAMBAHAN: Box Alasan Penolakan (Disembunyikan secara default) -->
                <div class="flex flex-col gap-2 border-b border-outline-variant pb-4 hidden" id="box-alasan-tolak-admin">
                    <div class="font-bold text-[#ba1a1a]">Alasan Penolakan:</div>
                    <div class="p-3 bg-error-container text-[#ba1a1a] rounded-lg border border-red-200" id="detail-alasan-tolak"></div>
                </div>

                <!-- Lampiran -->
                <div class="flex flex-col gap-2">
                    <div class="text-on-surface-variant">Lampiran Surat:</div>
                    <div id="area-lampiran" class="w-full h-auto min-h-[8rem] bg-surface-variant rounded-lg border border-outline-variant flex flex-col items-center justify-center text-outline overflow-hidden p-2">
                        <!-- Lampiran akan disuntik dari JS -->
                    </div>
                </div>

            </div>

            <!-- Area Tombol Aksi (Hanya muncul jika status Menunggu) -->
            <div class="px-6 py-4 border-t border-outline-variant bg-surface flex justify-end gap-3 hidden" id="area-tombol-aksi">
                <!-- PERBAIKAN: Tombol Tolak kini membuka Modal Alasan -->
                <button type="button" onclick="bukaModalTolak()" class="px-5 py-2 rounded-lg bg-[#ba1a1a] text-white font-bold text-sm hover:bg-[#93000a] shadow-sm transition-all">
                    Tolak
                </button>

                <form id="form-setuju" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="disetujui">
                    <button type="submit" class="px-5 py-2 rounded-lg bg-primary text-white font-bold text-sm hover:bg-primary-container shadow-sm transition-all">
                        Setujui Izin
                    </button>
                </form>
            </div>

            <!-- Area Status Terproses (Hanya muncul jika sudah Disetujui/Ditolak) -->
            <div class="px-6 py-4 border-t border-outline-variant bg-surface flex justify-between items-center hidden" id="area-status-terproses">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-on-surface-variant">Keputusan:</span>
                    <span id="badge-status-saat-ini" class="px-3 py-1 rounded-full text-xs font-bold"></span>
                </div>
                <button type="button" onclick="bukaModalBatal()" class="px-4 py-2 border border-outline-variant text-on-surface-variant rounded-lg font-bold text-sm hover:bg-surface-container-highest transition-colors">
                    Batalkan Keputusan
                </button>
            </div>
            
        </div>
    </div>
</div>

<!-- ================= MODAL KONFIRMASI BATAL KEPUTUSAN ================= -->
<div class="fixed inset-0 z-[110] hidden" id="modal-batal-izin">
    <div class="absolute inset-0 bg-on-surface/40 backdrop-blur-sm transition-opacity" onclick="tutupModalBatal()"></div>
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4 pointer-events-none">
        <div class="bg-surface-container-lowest rounded-xl w-full max-w-sm shadow-xl pointer-events-auto flex flex-col overflow-hidden text-center p-6">
            <div class="w-16 h-16 bg-error-container text-on-error-container rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[32px]">warning</span>
            </div>
            <h3 class="text-xl font-bold text-on-surface mb-2">Batalkan Keputusan?</h3>
            <p class="text-on-surface-variant text-sm mb-6">Status izin ini akan dikembalikan menjadi <strong>Menunggu</strong>. Anda yakin?</p>
            
            <form id="form-batal-izin" method="POST" class="flex justify-center gap-3 w-full">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="pending">
                <button type="button" class="flex-1 py-2.5 rounded-lg border border-outline text-on-surface-variant font-bold text-sm hover:bg-surface-container-highest transition-colors" onclick="tutupModalBatal()">Tidak</button>
                <button type="submit" class="flex-1 py-2.5 rounded-lg bg-[#ba1a1a] text-white font-bold text-sm hover:bg-[#93000a] shadow-sm transition-all">Ya, Batalkan</button>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL KONFIRMASI TOLAK IZIN ================= -->
<div class="fixed inset-0 z-[110] hidden" id="modal-tolak-izin">
    <div class="absolute inset-0 bg-on-surface/40 backdrop-blur-sm transition-opacity" onclick="tutupModalTolak()"></div>
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4 pointer-events-none">
        <div class="bg-surface-container-lowest rounded-xl w-full max-w-sm shadow-xl pointer-events-auto flex flex-col overflow-hidden p-6">
            <h3 class="text-xl font-bold text-on-surface mb-2 text-center">Alasan Penolakan</h3>
            <p class="text-on-surface-variant text-sm mb-4 text-center">Berikan alasan mengapa permohonan izin ini ditolak agar pegawai dapat mengetahuinya.</p>
            
            <form id="form-konfirmasi-tolak" method="POST" class="flex flex-col gap-4 w-full">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="ditolak">
                
                <textarea name="alasan_penolakan" rows="3" required class="w-full px-4 py-3 bg-surface border border-outline-variant focus:border-[#ba1a1a] focus:ring-1 focus:ring-[#ba1a1a] rounded-lg outline-none text-sm transition-shadow resize-none" placeholder="Tuliskan alasan penolakan di sini..."></textarea>
                
                <div class="flex gap-3 mt-2">
                    <button type="button" class="flex-1 py-2.5 rounded-lg border border-outline text-on-surface-variant font-bold text-sm hover:bg-surface-container-highest transition-colors" onclick="tutupModalTolak()">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-lg bg-[#ba1a1a] text-white font-bold text-sm hover:bg-[#93000a] shadow-sm transition-all">Konfirmasi Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openDetailModal(id, nama, jenis, tanggal, alasan, lampiran, status, alasanTolak) {
        document.getElementById('detail-nama').innerText = nama;
        document.getElementById('detail-jenis').innerText = jenis;
        document.getElementById('detail-tanggal').innerText = tanggal;
        document.getElementById('detail-alasan').innerText = alasan || '- Tidak ada alasan tertulis -';
        
        // LOGIKA BARU: Tampilkan alasan tolak jika statusnya Ditolak
        const boxTolak = document.getElementById('box-alasan-tolak-admin');
        if (status.toLowerCase() === 'ditolak') {
            boxTolak.classList.remove('hidden');
            document.getElementById('detail-alasan-tolak').innerText = alasanTolak || 'Tidak ada keterangan tertulis.';
        } else {
            boxTolak.classList.add('hidden');
        }

        const areaLampiran = document.getElementById('area-lampiran');
        if (lampiran && lampiran !== '') {
            // disimpan di public agar bisa diakses langsung
            areaLampiran.innerHTML = `<a href="/${lampiran}" target="_blank" class="w-full text-center hover:opacity-80 transition-opacity"><img src="/${lampiran}" alt="Lampiran Izin" class="max-h-64 object-contain mx-auto rounded"><p class="text-xs text-primary mt-2 underline">Klik untuk perbesar</p></a>`;
        } else {
            areaLampiran.innerHTML = `<span class="material-symbols-outlined text-[32px] mb-2">image_not_supported</span><span class="font-bold text-sm">Tidak ada lampiran disertakan</span>`;
        }
        
        const areaTombol = document.getElementById('area-tombol-aksi');
        const areaTerproses = document.getElementById('area-status-terproses');
        const badgeStatus = document.getElementById('badge-status-saat-ini');

        if(status.toLowerCase() === 'pending' || status.toLowerCase() === 'menunggu') {
            areaTombol.classList.remove('hidden');
            areaTerproses.classList.add('hidden');
            document.getElementById('form-setuju').action = `/admin/izin/${id}/status`;
            document.getElementById('form-konfirmasi-tolak').action = `/admin/izin/${id}/status`;
        } else {
            areaTombol.classList.add('hidden');
            areaTerproses.classList.remove('hidden');
            document.getElementById('form-batal-izin').action = `/admin/izin/${id}/status`;
            
            if (status.toLowerCase() === 'disetujui') {
                badgeStatus.className = 'px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200';
                badgeStatus.innerText = 'Telah Disetujui';
            } else {
                badgeStatus.className = 'px-3 py-1 rounded-full text-xs font-bold bg-error-container text-on-error-container border border-red-200';
                badgeStatus.innerText = 'Telah Ditolak';
            }
        }

        document.getElementById('modal-detail-izin').classList.remove('hidden');
    }

    function bukaModalBatal() {
        document.getElementById('modal-detail-izin').classList.add('hidden');
        document.getElementById('modal-batal-izin').classList.remove('hidden');
    }

    function tutupModalBatal() {
        document.getElementById('modal-batal-izin').classList.add('hidden');
        document.getElementById('modal-detail-izin').classList.remove('hidden');
    }

    function bukaModalTolak() {
        document.getElementById('modal-detail-izin').classList.add('hidden');
        document.getElementById('modal-tolak-izin').classList.remove('hidden');
    }

    function tutupModalTolak() {
        document.getElementById('modal-tolak-izin').classList.add('hidden');
        document.getElementById('modal-detail-izin').classList.remove('hidden');
    }
</script>
@endsection
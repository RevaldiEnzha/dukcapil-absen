@extends('layouts.user')

@section('title', 'Izin - Pegawai')

@section('content')
<div class="flex flex-col h-full max-w-md mx-auto relative pb-10">

    <!-- Header & Tombol Ajukan -->
    <section class="pt-2 pb-6">
        <h1 class="text-2xl font-bold text-on-surface mb-2">Pengajuan Izin</h1>
        <p class="text-sm text-on-surface-variant mb-6">Kelola pengajuan cuti sakit dan izin keperluan pribadi Anda di sini.</p>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg font-bold text-sm border border-green-200">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-4 bg-error-container text-error rounded-lg font-bold text-sm border border-red-200">
                Data gagal dikirim. Pastikan ukuran file tidak lebih dari 5MB dan format sesuai.
            </div>
        @endif

        <button type="button" onclick="bukaModalForm()" class="w-full bg-primary text-white h-14 rounded-xl font-bold text-base flex items-center justify-center gap-2 shadow-md hover:bg-primary/90 active:scale-95 transition-all">
            <span class="material-symbols-outlined">add</span>
            Ajukan Izin
        </button>
    </section>

    <!-- Riwayat Pengajuan -->
    <section class="mt-2 flex-1">
        <h2 class="text-lg font-bold text-on-surface mb-3">Riwayat Pengajuan</h2>
        
        <div class="flex flex-col gap-4">
            @forelse($izins as $izin)
                @php
                    // Logika Format Tanggal
                    $start = \Carbon\Carbon::parse($izin->tanggal_mulai);
                    $end = \Carbon\Carbon::parse($izin->tanggal_selesai);
                    $tglDisplay = $start->isSameDay($end) ? $start->translatedFormat('d M Y') : $start->format('d M') . ' - ' . $end->translatedFormat('d M Y');
                    
                    // Logika Warna Status (Sesuai Permintaan)
                    $statusL = strtolower($izin->status);
                    $statusWarnaBg = 'bg-surface-variant text-on-surface-variant border-outline-variant'; // Default (Abu-abu / Menunggu)
                    $statusTeks = 'Menunggu';
                    
                    if ($statusL == 'disetujui') {
                        $statusWarnaBg = 'bg-green-100 text-green-700 border-green-200';
                        $statusTeks = 'Disetujui';
                    } elseif ($statusL == 'ditolak') {
                        $statusWarnaBg = 'bg-error-container text-[#ba1a1a] border-red-200';
                        $statusTeks = 'Ditolak';
                    }

                    // Ikon Jenis
                    $ikonIzin = strtolower($izin->jenis_izin) == 'sakit' ? 'sick' : 'work_off';
                @endphp

                <!-- Kartu Izin -->
                <div class="bg-surface-container-lowest border border-outline-variant/50 rounded-xl p-5 flex flex-col gap-3 shadow-sm hover:shadow-md transition-shadow relative">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div class="bg-surface-variant w-12 h-12 rounded-full flex items-center justify-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-[24px]">{{ $ikonIzin }}</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-on-surface">{{ $izin->jenis_izin }}</h3>
                                <p class="text-sm text-on-surface-variant font-medium">{{ $tglDisplay }}</p>
                            </div>
                        </div>
                        <span class="{{ $statusWarnaBg }} font-bold text-[11px] px-3 py-1 rounded-full border">{{ $statusTeks }}</span>
                    </div>
                    
                    <div class="border-t border-outline-variant/30 pt-3 mt-1 flex justify-between items-end gap-2">
                        <p class="text-sm text-on-surface-variant line-clamp-1 flex-1">
                            <span class="font-bold">Ket:</span> {{ $izin->alasan }}
                        </p>
                        <button type="button" 
                                onclick="bukaModalDetail('{{ $izin->id }}', '{{ $izin->jenis_izin }}', '{{ $tglDisplay }}', '{{ $statusTeks }}', '{{ addslashes($izin->alasan) }}', '{{ addslashes($izin->alasan_penolakan ?? '') }}', '{{ $izin->lampiran }}')" 
                                class="text-primary font-bold text-xs hover:underline whitespace-nowrap">
                            Detail
                        </button>
                    </div>

                    @if($statusL == 'ditolak')
                        <div class="mt-1 bg-error-container/30 p-2 rounded text-xs text-[#ba1a1a]">
                            <span class="font-bold">Alasan ditolak:</span> {{ $izin->alasan_penolakan ?? 'Tidak ada keterangan dari admin.' }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-10 bg-surface-container-lowest border border-outline-variant/50 rounded-xl">
                    <span class="material-symbols-outlined text-outline text-4xl mb-2">event_busy</span>
                    <p class="text-sm font-bold text-on-surface-variant">Belum ada riwayat pengajuan izin.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>

<!-- ================= MODAL AJUKAN IZIN ================= -->
<!-- PERBAIKAN: z-50 diubah menjadi z-[100] -->
<div id="modal-form-izin" class="fixed inset-0 bg-on-surface/50 z-[100] transition-opacity opacity-0 hidden items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-surface w-full max-w-md rounded-2xl p-6 pb-10 mb-8 shadow-xl transform scale-95 transition-transform max-h-[80vh] overflow-y-auto" id="sheet-form-izin">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-on-surface">Form Pengajuan Izin</h2>
            <button onclick="tutupModalForm()" class="text-on-surface-variant p-2 rounded-full hover:bg-surface-container-highest transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('pegawai.izin.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
            @csrf
            
            <div class="flex flex-col gap-1.5">
                <label class="font-bold text-sm text-on-surface-variant">Jenis Izin</label>
                <select name="jenis_izin" required class="h-12 rounded-lg border border-outline-variant px-4 bg-surface text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none text-sm">
                    <option value="Sakit">Sakit</option>
                    <option value="Izin Pribadi">Izin Pribadi</option>
                    <option value="Cuti">Cuti</option>
                </select>
            </div>
            
            <div class="flex gap-4">
                <div class="flex flex-col gap-1.5 w-full">
                    <label class="font-bold text-sm text-on-surface-variant">Dari Tanggal</label>
                    <input type="date" name="tanggal_mulai" required class="h-12 rounded-lg border border-outline-variant px-4 bg-surface text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none text-sm">
                </div>
                <div class="flex flex-col gap-1.5 w-full">
                    <label class="font-bold text-sm text-on-surface-variant">Sampai Tanggal</label>
                    <input type="date" name="tanggal_selesai" required class="h-12 rounded-lg border border-outline-variant px-4 bg-surface text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none text-sm">
                </div>
            </div>
            
            <div class="flex flex-col gap-1.5">
                <label class="font-bold text-sm text-on-surface-variant">Keterangan / Alasan</label>
                <textarea name="alasan" rows="3" required class="rounded-lg border border-outline-variant p-4 bg-surface text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none resize-none text-sm" placeholder="Jelaskan secara singkat alasan izin..."></textarea>
            </div>
            
            <div class="flex flex-col gap-1.5 mt-2">
                <label class="font-bold text-sm text-on-surface-variant">Lampiran Surat/Bukti (Opsional)</label>
                <p class="text-[10px] text-on-surface-variant mb-1">Maks 5MB. Format: JPG, PNG, PDF.</p>
                <input type="file" name="lampiran" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-sm text-on-surface-variant file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-colors border border-outline-variant rounded-lg p-1 bg-surface-container-lowest">
            </div>
            
            <button type="submit" class="w-full bg-primary text-white font-bold h-14 rounded-xl mt-4 hover:bg-primary/90 active:scale-[0.98] transition-all shadow-md">
                Kirim Pengajuan
            </button>
        </form>
    </div>
</div>

<!-- ================= MODAL DETAIL IZIN ================= -->
<div id="modal-detail" class="fixed inset-0 bg-on-surface/50 z-[60] hidden items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-surface w-full max-w-md rounded-2xl p-6 shadow-xl flex flex-col gap-4">
        <div class="flex justify-between items-center border-b border-outline-variant pb-4">
            <h2 class="text-lg font-bold text-on-surface">Detail Izin</h2>
            <button onclick="tutupModalDetail()" class="text-on-surface-variant p-1 rounded-full hover:bg-surface-container-highest transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="text-sm flex flex-col gap-3">
            <div class="grid grid-cols-3 gap-2">
                <span class="font-bold text-on-surface-variant">Status</span>
                <span id="dtl-status" class="col-span-2 font-bold">-</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="font-bold text-on-surface-variant">Jenis</span>
                <span id="dtl-jenis" class="col-span-2 font-medium text-on-surface">-</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="font-bold text-on-surface-variant">Tanggal</span>
                <span id="dtl-tanggal" class="col-span-2 font-medium text-on-surface">-</span>
            </div>
            <div class="flex flex-col gap-1 mt-2">
                <span class="font-bold text-on-surface-variant">Keterangan:</span>
                <div id="dtl-keterangan" class="p-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface">-</div>
            </div>
            
            <div id="box-alasan-tolak" class="flex flex-col gap-1 mt-2 hidden">
                <span class="font-bold text-[#ba1a1a]">Alasan Penolakan:</span>
                <div id="dtl-alasan-tolak" class="p-3 bg-error-container text-[#ba1a1a] rounded-lg border border-red-200 font-medium">-</div>
            </div>

            <div class="flex flex-col gap-1 mt-2">
                <span class="font-bold text-on-surface-variant">Lampiran:</span>
                <div id="dtl-lampiran" class="w-full flex justify-center p-3 bg-surface-variant rounded-lg border border-outline-variant">
                    <!-- Link lampiran akan diisi JS -->
                </div>
            </div>
        </div>

        <!-- Tombol Hapus memanggil Modal Konfirmasi Hapus -->
        <button type="button" onclick="bukaModalHapus()" class="w-full mt-4 bg-surface text-[#ba1a1a] border border-[#ba1a1a] hover:bg-error-container font-bold py-2.5 rounded-lg transition-colors">
            Hapus Pengajuan
        </button>
    </div>
</div>

<!-- ================= MODAL KONFIRMASI HAPUS ================= -->
<div id="modal-hapus" class="fixed inset-0 bg-on-surface/60 z-[70] hidden items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-surface w-full max-w-sm rounded-2xl p-6 shadow-xl text-center">
        <div class="w-16 h-16 bg-error-container text-[#ba1a1a] rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-[32px]">delete</span>
        </div>
        <h3 class="text-lg font-bold text-on-surface mb-2">Hapus Pengajuan?</h3>
        <p class="text-sm text-on-surface-variant mb-6">Tindakan ini tidak dapat dibatalkan. Pengajuan izin akan dihapus dari riwayat.</p>
        
        <form id="form-hapus-izin" method="POST" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="tutupModalHapus()" class="flex-1 py-2.5 rounded-lg border border-outline text-on-surface-variant font-bold text-sm hover:bg-surface-container-highest transition-colors">Batal</button>
            <button type="submit" class="flex-1 py-2.5 rounded-lg bg-[#ba1a1a] text-white font-bold text-sm hover:bg-[#93000a] transition-colors shadow-sm">Ya, Hapus</button>
        </form>
    </div>
</div>

<script>
    /* Script Modal Form Ajukan */
    const mForm = document.getElementById('modal-form-izin');
    const sForm = document.getElementById('sheet-form-izin');

    function bukaModalForm() {
        mForm.classList.remove('hidden');
        mForm.classList.add('flex');
        setTimeout(() => {
            mForm.classList.remove('opacity-0');
            sForm.classList.remove('scale-95');
        }, 10);
    }

    function tutupModalForm() {
        mForm.classList.add('opacity-0');
        sForm.classList.add('scale-95');
        setTimeout(() => {
            mForm.classList.add('hidden');
            mForm.classList.remove('flex');
        }, 300);
    }

    /* Script Modal Detail & Hapus */
    const mDetail = document.getElementById('modal-detail');
    const mHapus = document.getElementById('modal-hapus');

    function bukaModalDetail(id, jenis, tanggal, status, keterangan, alasanTolak, lampiran) {
        document.getElementById('dtl-jenis').innerText = jenis;
        document.getElementById('dtl-tanggal').innerText = tanggal;
        document.getElementById('dtl-keterangan').innerText = keterangan;
        
        const elStatus = document.getElementById('dtl-status');
        elStatus.innerText = status;
        if(status === 'Disetujui') elStatus.className = 'col-span-2 font-bold text-green-700';
        else if(status === 'Ditolak') elStatus.className = 'col-span-2 font-bold text-[#ba1a1a]';
        else elStatus.className = 'col-span-2 font-bold text-on-surface-variant';

        const boxTolak = document.getElementById('box-alasan-tolak');
        if (status === 'Ditolak') {
            boxTolak.classList.remove('hidden');
            document.getElementById('dtl-alasan-tolak').innerText = alasanTolak || 'Tidak ada keterangan.';
        } else {
            boxTolak.classList.add('hidden');
        }

        const boxLampiran = document.getElementById('dtl-lampiran');
        if(lampiran && lampiran !== '') {
            boxLampiran.innerHTML = `<a href="/storage/${lampiran}" target="_blank" class="flex items-center gap-2 text-primary font-bold hover:underline"><span class="material-symbols-outlined text-[20px]">description</span> Lihat / Unduh File</a>`;
        } else {
            boxLampiran.innerHTML = `<span class="text-sm font-medium text-on-surface-variant">Tidak ada lampiran.</span>`;
        }

        // Set action form hapus
        document.getElementById('form-hapus-izin').action = `/user/izin/${id}`;
        
        mDetail.classList.remove('hidden');
        mDetail.classList.add('flex');
    }

    function tutupModalDetail() {
        mDetail.classList.add('hidden');
        mDetail.classList.remove('flex');
    }

    function bukaModalHapus() {
        tutupModalDetail();
        mHapus.classList.remove('hidden');
        mHapus.classList.add('flex');
    }

    function tutupModalHapus() {
        mHapus.classList.add('hidden');
        mHapus.classList.remove('flex');
    }
</script>
@endsection
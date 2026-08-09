@extends('layouts.user')

@section('title', 'Absen - Pegawai')

@section('content')
<div class="flex flex-col h-full max-w-md mx-auto relative pb-10">
    
    <!-- Notifikasi Alert Berhasil / Gagal -->
    @if(session('success'))
        <div class="mt-4 p-4 bg-green-100 text-green-700 rounded-xl font-bold text-sm border border-green-200 flex items-center gap-2">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mt-4 p-4 bg-error-container text-[#ba1a1a] rounded-xl font-bold text-sm border border-red-200 flex items-center gap-2">
            <span class="material-symbols-outlined">warning</span>
            {{ session('error') }}
        </div>
    @endif
    @if(session('info'))
        <div class="mt-4 p-4 bg-blue-100 text-blue-700 rounded-xl font-bold text-sm border border-blue-200 flex items-center gap-2">
            <span class="material-symbols-outlined">info</span>
            {{ session('info') }}
        </div>
    @endif

    <!-- Bagian Atas: Header & Tombol Scan -->
    <section class="pt-6 pb-6">
        <h1 class="text-2xl font-bold text-on-surface mb-2">Presensi Pegawai</h1>
        <p class="text-sm text-on-surface-variant mb-6">Arahkan kamera ke QR Code untuk melakukan absensi kehadiran.</p>

        <!-- Tombol Buka Kamera (Trigger JS) -->
        <button type="button" id="btn-scan" class="w-full bg-primary text-white h-14 rounded-xl font-bold text-base flex items-center justify-center gap-3 shadow-md hover:bg-primary/90 active:scale-[0.98] transition-all">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">photo_camera</span>
            Scan Sekarang
        </button>
    </section>

    <!-- UI Scanner Kamera (Disembunyikan secara default) -->
    <section id="scanner-container" class="hidden flex-col items-center justify-center mb-6 bg-black rounded-3xl overflow-hidden shadow-lg relative h-[300px] w-full">
        <div id="reader" class="w-full h-full"></div>
        <button type="button" id="btn-cancel-scan" class="absolute bottom-4 bg-[#ba1a1a] text-white px-6 py-2 rounded-full font-bold text-sm shadow-md hover:bg-[#93000a] active:scale-95 transition-transform z-10 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">close</span> Batalkan
        </button>
    </section>

    <!-- Form Tersembunyi untuk mengirim hasil QR ke Backend -->
    <form id="form-scan" action="{{ route('pegawai.absen.scan') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="qr_code" id="input-qr-code">
    </form>

    <!-- Logika Waktu & Status -->
    @php
        $checkInTime = '--:--';
        $checkInStatus = 'Belum Absen';
        $isLate = false;
        
        if ($absenHariIni && $absenHariIni->check_in) {
            $checkInTime = \Carbon\Carbon::parse($absenHariIni->check_in)->format('H:i');
            if ($checkInTime > '07:30') {
                $isLate = true;
                $checkInStatus = 'Terlambat';
            } else {
                $checkInStatus = 'Tepat Waktu';
            }
        }

        $checkOutTime = '--:--';
        $checkOutStatus = 'Belum Waktunya';
        
        if ($absenHariIni && $absenHariIni->check_out) {
            $checkOutTime = \Carbon\Carbon::parse($absenHariIni->check_out)->format('H:i');
            $checkOutStatus = 'Selesai';
        }
    @endphp

    <!-- Bagian Bawah: Riwayat Hari Ini -->
    <section class="bg-surface-container-lowest rounded-3xl p-6 border border-outline-variant/30 shadow-sm flex-1">
        <div class="flex justify-between items-end mb-6">
            <h2 class="text-xl font-bold text-on-surface">Riwayat Hari Ini</h2>
            <span class="text-sm font-bold text-primary">{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</span>
        </div>

        <div class="flex flex-col gap-4">
            
            <!-- Kartu Check In -->
            <div class="bg-surface p-5 rounded-xl border border-outline-variant/30 flex justify-between items-center relative overflow-hidden shadow-sm">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-primary"></div>
                <div>
                    <div class="text-sm font-bold text-on-surface mb-1">Check In</div>
                    <div class="text-2xl font-bold text-on-surface">{{ $checkInTime }}</div>
                </div>
                
                @if($checkInTime !== '--:--')
                    @if($isLate)
                        <div class="bg-error-container text-[#ba1a1a] px-3 py-1.5 rounded-full font-bold text-xs flex items-center gap-1 border border-[#ba1a1a]/20">
                            <span class="material-symbols-outlined text-[14px]">warning</span> Terlambat
                        </div>
                    @else
                        <div class="bg-green-100 text-green-700 px-3 py-1.5 rounded-full font-bold text-xs flex items-center gap-1 border border-green-200">
                            <span class="material-symbols-outlined text-[14px]">check_circle</span> Tepat Waktu
                        </div>
                    @endif
                @else
                    <div class="bg-surface-variant text-on-surface-variant px-3 py-1.5 rounded-full font-bold text-xs">
                        {{ $checkInStatus }}
                    </div>
                @endif
            </div>

            <!-- Kartu Check Out -->
            <div class="bg-surface p-5 rounded-xl border {{ $checkOutTime === '--:--' ? 'border-outline-variant/50 border-dashed opacity-70' : 'border-outline-variant/30 shadow-sm' }} flex justify-between items-center relative overflow-hidden">
                @if($checkOutTime !== '--:--')
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-secondary-container"></div>
                @endif
                <div>
                    <div class="text-sm font-bold text-on-surface-variant mb-1">Check Out</div>
                    <div class="text-2xl font-bold text-on-surface-variant">{{ $checkOutTime }}</div>
                </div>
                <div class="bg-surface-variant text-on-surface-variant px-3 py-1.5 rounded-full font-bold text-xs">
                    {{ $checkOutStatus }}
                </div>
            </div>

        </div>
    </section>
</div>

<!-- ================= MODAL PERINGATAN CUTI ================= -->
<div id="modal-peringatan-cuti" class="fixed inset-0 bg-on-surface/50 z-50 transition-opacity opacity-0 hidden items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-surface w-full max-w-sm rounded-2xl p-6 shadow-xl transform scale-95 transition-transform text-center" id="sheet-peringatan-cuti">
        <div class="w-16 h-16 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-[32px]">event_busy</span>
        </div>
        <h3 class="text-lg font-bold text-on-surface mb-2">Status Anda: {{ $statusIzinHariIni ?? 'Cuti' }}</h3>
        <p class="text-sm text-on-surface-variant mb-6">Anda sedang berhalangan hadir. Anda tidak diwajibkan (dan tidak dapat) melakukan absensi harian.</p>
        
        <button type="button" onclick="tutupModalCuti()" class="w-full py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary/90 transition-colors shadow-sm active:scale-[0.98]">
            Mengerti
        </button>
    </div>
</div>

<!-- ================= MODAL PERINGATAN HARI LIBUR ================= -->
<div id="modal-peringatan-libur" class="fixed inset-0 bg-on-surface/50 z-50 transition-opacity opacity-0 hidden items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-surface w-full max-w-sm rounded-2xl p-6 shadow-xl transform scale-95 transition-transform text-center" id="sheet-peringatan-libur">
        <div class="w-16 h-16 bg-surface-variant text-on-surface-variant rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-[32px]">weekend</span>
        </div>
        <h3 class="text-lg font-bold text-on-surface mb-2">Hari Libur</h3>
        <p class="text-sm text-on-surface-variant mb-6">Sistem presensi ditutup pada hari Sabtu dan Minggu. Selamat menikmati akhir pekan!</p>
        
        <button type="button" onclick="tutupModalLibur()" class="w-full py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary/90 transition-colors shadow-sm active:scale-[0.98]">
            Mengerti
        </button>
    </div>
</div>

<!-- Library Scanner HTML5-QRCode -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btnScan = document.getElementById('btn-scan');
        const btnCancel = document.getElementById('btn-cancel-scan');
        const scannerContainer = document.getElementById('scanner-container');
        const formScan = document.getElementById('form-scan');
        const inputQrCode = document.getElementById('input-qr-code');
        
        let html5QrcodeScanner = null;

        // Fungsi ketika QR berhasil terbaca
        function onScanSuccess(decodedText) {

            console.log("QR TERBACA:", decodedText);

            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop()
                .then(() => {
                    return html5QrcodeScanner.clear();
                })
                .then(() => {
                    inputQrCode.value = decodedText;
                    formScan.submit();
                })
                .catch(err => console.error(err));
            }
        }

        function onScanFailure(error) {
            console.log(error);
        }

        // Fungsi Tutup Modal Cuti
        window.tutupModalCuti = function() {
            const mCuti = document.getElementById('modal-peringatan-cuti');
            const sCuti = document.getElementById('sheet-peringatan-cuti');
            mCuti.classList.add('opacity-0');
            sCuti.classList.add('scale-95');
            setTimeout(() => {
                mCuti.classList.add('hidden');
                mCuti.classList.remove('flex');
            }, 300);
        }

        // Fungsi Tutup Modal Libur
        window.tutupModalLibur = function() {
            const mLibur = document.getElementById('modal-peringatan-libur');
            const sLibur = document.getElementById('sheet-peringatan-libur');
            mLibur.classList.add('opacity-0');
            sLibur.classList.add('scale-95');
            setTimeout(() => {
                mLibur.classList.add('hidden');
                mLibur.classList.remove('flex');
            }, 300);
        }

        // Jalankan Kamera saat tombol ditekan
        btnScan.addEventListener('click', function() {
            // CEK HARI LIBUR DARI LARAVEL
            const isWeekend = "{{ $isWeekend ? 'true' : 'false' }}";
            
            // JIKA HARI LIBUR, TAMPILKAN MODAL DAN HENTIKAN PROSES
            if (isWeekend === 'true') {
                const mLibur = document.getElementById('modal-peringatan-libur');
                const sLibur = document.getElementById('sheet-peringatan-libur');
                mLibur.classList.remove('hidden');
                mLibur.classList.add('flex');
                setTimeout(() => {
                    mLibur.classList.remove('opacity-0');
                    sLibur.classList.remove('scale-95');
                }, 10);
                return; // Berhenti di sini
            }
            
            // CEK STATUS IZIN HARI INI DARI LARAVEL
            const statusIzinHariIni = "{{ $statusIzinHariIni ?? '' }}";
            
            // JIKA ADA STATUS IZIN, TAMPILKAN MODAL DAN HENTIKAN KAMERA
            if (statusIzinHariIni !== '') {
                const mCuti = document.getElementById('modal-peringatan-cuti');
                const sCuti = document.getElementById('sheet-peringatan-cuti');
                mCuti.classList.remove('hidden');
                mCuti.classList.add('flex');
                setTimeout(() => {
                    mCuti.classList.remove('opacity-0');
                    sCuti.classList.remove('scale-95');
                }, 10);
                return; // Berhenti di sini
            }

            // Tampilkan kotak kamera dan sembunyikan tombol scan utama
            scannerContainer.classList.remove('hidden');
            scannerContainer.classList.add('flex');
            btnScan.classList.add('hidden');

            html5QrcodeScanner = new Html5Qrcode("reader");
            const config = {
                fps: 15,
                qrbox: {
                    width: 300,
                    height: 300
                }
            };
            
            // Nyalakan kamera belakang
            html5QrcodeScanner.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
            .catch((err) => {
                alert("Gagal mengakses kamera. Pastikan izin kamera telah diberikan di browser Anda.");
                membatalkanScan();
            });
        });

        // Batalkan Scan
        btnCancel.addEventListener('click', function() {
            membatalkanScan();
        });

        function membatalkanScan() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    html5QrcodeScanner.clear();
                }).catch(err => console.log(err));
            }
            scannerContainer.classList.add('hidden');
            scannerContainer.classList.remove('flex');
            btnScan.classList.remove('hidden');
        }
    });
</script>
@endsection
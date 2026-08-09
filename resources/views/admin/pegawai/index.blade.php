@extends('layouts.admin')

@section('title', 'Manajemen Pegawai - Disdukcapil')

@section('content')
<!-- Page Header & Action (DIPERBAIKI: Kiri - Kanan) -->
<div class="flex flex-row items-center justify-between mb-8 w-full">
    <div class="text-left">
        <h1 class="font-headline-lg text-3xl font-bold text-on-surface mb-1">Daftar Pegawai</h1>
        <p class="font-body-sm text-on-surface-variant">Kelola data pegawai dan status kepegawaian.</p>
    </div>
    <button onclick="document.getElementById('modal-tambah-pegawai').classList.remove('hidden')" class="inline-flex items-center justify-center gap-2 bg-primary text-white font-bold px-6 py-2.5 rounded-lg hover:bg-primary-container hover:shadow-md transition-all active:scale-95 whitespace-nowrap">
        <span class="material-symbols-outlined text-[20px]">add</span>
        Tambah Pegawai
    </button>
</div>

<!-- Alert Notifikasi -->
@if(session('status'))
<div id="alert-notifikasi" class="mb-4 p-4 bg-primary-fixed text-primary rounded-lg border border-primary/20 font-bold flex justify-between items-center">
    <span>{{ session('status') }}</span>
    <button type="button" onclick="document.getElementById('alert-notifikasi').remove()" class="text-primary hover:bg-primary/20 p-1 rounded-full flex items-center transition-colors">
        <span class="material-symbols-outlined text-[20px]">close</span>
    </button>
</div>
@endif

<!-- Data Table Card -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
    
    <!-- Form Pencarian & Filter (DIPERBAIKI: Pendek & Bersebelahan) -->
    <form method="GET" action="{{ route('admin.pegawai.index') }}" class="p-5 border-b border-outline-variant bg-surface-container-low/30 flex flex-row items-center gap-4 w-full">
        
        <!-- Search (Lebar dibatasi ke w-72 atau sekitar 288px) -->
        <div class="relative w-72">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
            <input name="search" value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 bg-white border border-outline-variant rounded-lg text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-shadow" placeholder="Cari NIK atau Nama..." type="text">
        </div>
        
        <!-- Filter Urutan (Langsung bersebelahan dengan Search) -->
        <div class="relative">
            <select name="sort" onchange="this.form.submit()" class="appearance-none pl-10 pr-10 py-2 border border-outline-variant rounded-lg text-on-surface text-sm font-bold focus:border-primary focus:ring-1 focus:ring-primary outline-none cursor-pointer bg-white">
                <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
            </select>
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] pointer-events-none text-outline">sort</span>
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[20px] pointer-events-none text-outline">expand_more</span>
        </div>
        
    </form>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant whitespace-nowrap">NIK</th>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant">Nama Lengkap</th>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant">Jenis Kelamin</th>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant text-center">Status</th>
                    <th class="py-4 px-6 font-bold text-sm text-on-surface-variant text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant bg-white">
                @forelse($pegawais as $pegawai)
                <tr class="hover:bg-surface-container-lowest transition-colors">
                    <td class="py-4 px-6 text-sm text-on-surface whitespace-nowrap">{{ $pegawai->nik }}</td>
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary-fixed text-primary flex items-center justify-center font-bold text-[12px]">
                                {{ strtoupper(substr($pegawai->nama, 0, 2)) }}
                            </div>
                            <span class="text-sm text-on-surface font-medium">{{ $pegawai->nama }}</span>
                        </div>
                    </td>
                    <!-- Kolom Jenis Kelamin (DIPERBAIKI) -->
                    <td class="py-4 px-6 text-sm text-on-surface-variant">
                        @if($pegawai->jenis_kelamin == 'L')
                            Laki-laki
                        @elseif($pegawai->jenis_kelamin == 'P')
                            Perempuan
                        @else
                            {{ ucfirst($pegawai->jenis_kelamin) }}
                        @endif
                    </td>
                    
                    <!-- Kolom Status (DIPERBAIKI) -->
                    <td class="py-4 px-6 text-center">
                        @if(strtolower($pegawai->status) == 'aktif')
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold border border-green-200">Aktif</span>
                        @elseif(strtolower($pegawai->status) == 'tidak aktif')
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-error-container text-on-error-container text-xs font-bold border border-red-200">Tidak Aktif</span>
                        @else
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-surface-variant text-on-surface-variant text-xs font-bold border border-outline-variant">{{ ucfirst($pegawai->status) }}</span>
                        @endif
                    </td>
                    <!-- Kolom Aksi (DIPERBAIKI UNTUK JS) -->
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <!-- Tombol Edit (Kirim data via JS) -->
                            <button type="button" 
                                    onclick="openEditModal('{{ $pegawai->id }}', '{{ $pegawai->nik }}', '{{ $pegawai->nama }}', '{{ $pegawai->jenis_kelamin }}', '{{ $pegawai->no_hp }}', '{{ $pegawai->alamat }}', '{{ $pegawai->status }}')" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-secondary-container/50 text-secondary hover:bg-secondary-container rounded text-xs font-bold transition-colors">
                                <span class="material-symbols-outlined text-[16px]">edit</span>Edit
                            </button>
                            
                            <!-- Tombol Hapus (Kirim data via JS) -->
                            <button type="button" 
                                    onclick="openDeleteModal('{{ $pegawai->id }}', '{{ $pegawai->nama }}')" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-error-container text-on-error-container hover:bg-red-200 rounded text-xs font-bold transition-colors">
                                <span class="material-symbols-outlined text-[16px]">delete</span>Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-on-surface-variant">Tidak ada data pegawai yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginasi Bawaan Laravel -->
    <div class="p-4 border-t border-outline-variant bg-white">
        {{ $pegawais->links() }}
    </div>
</div>

<!-- Modal Form Tambah Pegawai -->
<div class="fixed inset-0 z-[100] {{ $errors->any() ? '' : 'hidden' }}" id="modal-tambah-pegawai">
    
    <!-- Backdrop Blur (Diberi z-0 agar ada di lapisan bawah) -->
    <div class="absolute inset-0 bg-on-surface/40 backdrop-blur-sm transition-opacity z-0" onclick="document.getElementById('modal-tambah-pegawai').classList.add('hidden')"></div>
    
    <!-- Konten Modal (Diberi relative dan z-10 agar melayang DI ATAS blur) -->
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4 pointer-events-none">
        <div class="bg-surface-container-lowest rounded-xl w-full max-w-lg shadow-xl pointer-events-auto flex flex-col max-h-[90vh] overflow-hidden">
            
            <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface">
                <h3 class="text-lg text-on-surface font-bold">Tambah Pegawai Baru</h3>
                <button type="button" class="text-on-surface-variant hover:bg-surface-container-highest transition-colors rounded-full p-1" onclick="document.getElementById('modal-tambah-pegawai').classList.add('hidden')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1 bg-surface-container-lowest">
                <form action="{{ route('admin.pegawai.store') }}" method="POST" class="flex flex-col gap-5">
                    @csrf
                    
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-sm text-on-surface" for="nik">NIK <span class="text-error">*</span></label>
                        <input name="nik" value="{{ old('nik') }}" id="nik" type="text" placeholder="Masukkan 16 digit NIK" required maxlength="16" minlength="16"
                               class="w-full px-4 py-2 bg-surface border {{ $errors->has('nik') ? 'border-error ring-1 ring-error' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} rounded-lg outline-none text-sm transition-shadow">
                        @error('nik') <span class="text-xs text-error font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-sm text-on-surface" for="nama">Nama Lengkap <span class="text-error">*</span></label>
                        <input name="nama" value="{{ old('nama') }}" id="nama" type="text" placeholder="Nama sesuai KTP" required
                               class="w-full px-4 py-2 bg-surface border {{ $errors->has('nama') ? 'border-error ring-1 ring-error' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} rounded-lg outline-none text-sm transition-shadow">
                        @error('nama') <span class="text-xs text-error font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-sm text-on-surface" for="jenis_kelamin">Jenis Kelamin <span class="text-error">*</span></label>
                            <div class="relative">
                                <select name="jenis_kelamin" id="jenis_kelamin" required
                                        class="w-full px-4 py-2 bg-surface border {{ $errors->has('jenis_kelamin') ? 'border-error ring-1 ring-error' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} rounded-lg outline-none text-sm appearance-none cursor-pointer">
                                    <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>Pilih...</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant text-[20px]">expand_more</span>
                            </div>
                            @error('jenis_kelamin') <span class="text-xs text-error font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-sm text-on-surface" for="no_hp">No. HP</label>
                            <input name="no_hp" value="{{ old('no_hp') }}" id="no_hp" type="tel" placeholder="08..."
                                   class="w-full px-4 py-2 bg-surface border border-outline-variant rounded-lg focus:border-primary focus:ring-1 focus:ring-primary outline-none text-sm transition-shadow">
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-sm text-on-surface" for="alamat">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="3" placeholder="Alamat lengkap"
                                  class="w-full px-4 py-2 bg-surface border border-outline-variant rounded-lg focus:border-primary focus:ring-1 focus:ring-primary outline-none text-sm transition-shadow">{{ old('alamat') }}</textarea>
                    </div>

                    <!-- Status & Password Akun (Dijadikan 2 Kolom) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Status -->
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-sm text-on-surface" for="status">Status <span class="text-error">*</span></label>
                            <div class="relative">
                                <select name="status" id="status" required
                                        class="w-full px-4 py-2 bg-surface border {{ $errors->has('status') ? 'border-error ring-1 ring-error' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} rounded-lg outline-none text-sm appearance-none cursor-pointer">
                                    <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Tidak Aktif" {{ old('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                    <option value="Cuti" {{ old('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant text-[20px]">expand_more</span>
                            </div>
                            @error('status') <span class="text-xs text-error font-bold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password Akun -->
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-sm text-on-surface" for="password">Password Akun <span class="text-error">*</span></label>
                            <!-- Aku menggunakan type="text" agar admin bisa melihat langsung password yang sedang mereka ketik untuk pegawai -->
                            <input name="password" id="password" autocomplete="new-password" type="text" placeholder="Minimal 6 karakter" required minlength="6"
                                   class="w-full px-4 py-2 bg-surface border {{ $errors->has('password') ? 'border-error ring-1 ring-error' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} rounded-lg outline-none text-sm transition-shadow">
                            @error('password') <span class="text-xs text-error font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-4 border-t border-outline-variant pt-5">
                        <button type="button" class="px-5 py-2.5 rounded-lg border border-outline text-primary font-bold text-sm hover:bg-surface-container-highest transition-colors" onclick="document.getElementById('modal-tambah-pegawai').classList.add('hidden')">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white font-bold text-sm hover:bg-primary-container shadow-sm hover:shadow-md transition-all active:scale-95">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
<!-- ================= MODAL EDIT PEGAWAI ================= -->
<div class="fixed inset-0 z-[100] hidden" id="modal-edit-pegawai">
    <div class="absolute inset-0 bg-on-surface/40 backdrop-blur-sm transition-opacity" onclick="document.getElementById('modal-edit-pegawai').classList.add('hidden')"></div>
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4 pointer-events-none">
        <div class="bg-surface-container-lowest rounded-xl w-full max-w-lg shadow-xl pointer-events-auto flex flex-col max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface">
                <h3 class="text-lg text-on-surface font-bold">Edit Data Pegawai</h3>
                <button type="button" class="text-on-surface-variant hover:bg-surface-container-highest transition-colors rounded-full p-1" onclick="document.getElementById('modal-edit-pegawai').classList.add('hidden')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1 bg-surface-container-lowest">
                <form id="form-edit-pegawai" method="POST" class="flex flex-col gap-5">
                    @csrf
                    @method('PUT') <!-- Wajib untuk edit data di Laravel -->
                    
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-sm text-on-surface">NIK <span class="text-error">*</span></label>
                        <input name="nik" id="edit_nik" type="text" required maxlength="16" minlength="16" class="w-full px-4 py-2 bg-surface border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary rounded-lg outline-none text-sm transition-shadow">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-sm text-on-surface">Nama Lengkap <span class="text-error">*</span></label>
                        <input name="nama" id="edit_nama" type="text" required class="w-full px-4 py-2 bg-surface border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary rounded-lg outline-none text-sm transition-shadow">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-sm text-on-surface">Jenis Kelamin <span class="text-error">*</span></label>
                            <div class="relative">
                                <select name="jenis_kelamin" id="edit_jenis_kelamin" required class="w-full px-4 py-2 bg-surface border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary rounded-lg outline-none text-sm appearance-none cursor-pointer">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant text-[20px]">expand_more</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-sm text-on-surface">No. HP</label>
                            <input name="no_hp" id="edit_no_hp" type="tel" class="w-full px-4 py-2 bg-surface border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary rounded-lg outline-none text-sm transition-shadow">
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-sm text-on-surface">Alamat</label>
                        <textarea name="alamat" id="edit_alamat" rows="3" class="w-full px-4 py-2 bg-surface border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary rounded-lg outline-none text-sm transition-shadow"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-sm text-on-surface">Status <span class="text-error">*</span></label>
                            <div class="relative">
                                <select name="status" id="edit_status" required class="w-full px-4 py-2 bg-surface border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary rounded-lg outline-none text-sm appearance-none cursor-pointer">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                    <option value="Cuti">Cuti</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant text-[20px]">expand_more</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-sm text-on-surface">Password Akun</label>
                            <input name="password" autocomplete="new-password" type="text" placeholder="Kosongkan jika tidak diubah" minlength="6" class="w-full px-4 py-2 bg-surface border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary rounded-lg outline-none text-sm transition-shadow">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-4 border-t border-outline-variant pt-5">
                        <button type="button" class="px-5 py-2.5 rounded-lg border border-outline text-primary font-bold text-sm hover:bg-surface-container-highest transition-colors" onclick="document.getElementById('modal-edit-pegawai').classList.add('hidden')">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white font-bold text-sm hover:bg-primary-container shadow-sm transition-all">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL KONFIRMASI HAPUS ================= -->
<div class="fixed inset-0 z-[100] hidden" id="modal-delete-pegawai">
    <div class="absolute inset-0 bg-on-surface/40 backdrop-blur-sm transition-opacity" onclick="document.getElementById('modal-delete-pegawai').classList.add('hidden')"></div>
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4 pointer-events-none">
        <div class="bg-surface-container-lowest rounded-xl w-full max-w-sm shadow-xl pointer-events-auto flex flex-col overflow-hidden text-center p-6">
            
            <!-- Ikon Peringatan -->
            <div class="w-16 h-16 bg-error-container text-on-error-container rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[32px]">warning</span>
            </div>
            
            <h3 class="text-xl font-bold text-on-surface mb-2">Hapus Pegawai?</h3>
            <p class="text-on-surface-variant text-sm mb-6">Anda yakin ingin menghapus data <strong id="delete-nama-pegawai" class="text-[#ba1a1a]"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
            
            <!-- Form Delete dengan Pencegah Klik Ganda (onsubmit) -->
            <form id="form-delete-pegawai" method="POST" class="flex justify-center gap-3 w-full" onsubmit="document.getElementById('btn-hapus-konfirm').disabled = true; document.getElementById('btn-hapus-konfirm').innerText = 'Menghapus...';">
                @csrf
                @method('DELETE')
                
                <button type="button" class="flex-1 py-2.5 rounded-lg border border-outline text-on-surface-variant font-bold text-sm hover:bg-surface-container-highest transition-colors" onclick="document.getElementById('modal-delete-pegawai').classList.add('hidden')">
                    Batal
                </button>
                
                <!-- PERBAIKAN: Warna tombol eksplisit jadi Merah, dan diberi ID -->
                <button type="submit" id="btn-hapus-konfirm" class="flex-1 py-2.5 rounded-lg bg-[#ba1a1a] text-white font-bold text-sm hover:bg-[#93000a] shadow-sm transition-all disabled:opacity-50 disabled:cursor-wait">
                    Ya, Hapus
                </button>
            </form>

        </div>
    </div>
</div>
@endsection

<script>
    // Fungsi melempar data dari tabel ke Modal Edit
    function openEditModal(id, nik, nama, jk, hp, alamat, status) {
        document.getElementById('form-edit-pegawai').action = `/admin/pegawai/${id}`;
        document.getElementById('edit_nik').value = nik;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_jenis_kelamin').value = jk;
        document.getElementById('edit_no_hp').value = hp || '';
        document.getElementById('edit_alamat').value = alamat || '';
        
        // Memilih dropdown status secara spesifik karena case-sensitive
        let statusSelect = document.getElementById('edit_status');
        for (let i = 0; i < statusSelect.options.length; i++) {
            if (statusSelect.options[i].value.toLowerCase() === status.toLowerCase()) {
                statusSelect.selectedIndex = i;
                break;
            }
        }
        
        document.getElementById('modal-edit-pegawai').classList.remove('hidden');
    }

    // Fungsi melempar data dari tabel ke Modal Hapus
    function openDeleteModal(id, nama) {
        document.getElementById('form-delete-pegawai').action = `/admin/pegawai/${id}`;
        document.getElementById('delete-nama-pegawai').innerText = nama;
        document.getElementById('modal-delete-pegawai').classList.remove('hidden');
    }
</script>
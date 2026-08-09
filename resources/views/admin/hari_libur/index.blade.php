@extends('layouts.admin')

@section('title', 'Kelola Hari Libur - Disdukcapil')

@section('content')
<div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
    <div>
        <h1 class="font-headline-lg text-3xl font-bold text-on-surface mb-1">Hari Libur Nasional</h1>
        <p class="font-body-sm text-on-surface-variant">Atur tanggal merah dan cuti bersama untuk kalkulasi absensi.</p>
    </div>
</div>

@if(session('status'))
<div class="mb-6 p-4 bg-primary-fixed text-primary rounded-lg border border-primary/20 font-bold flex justify-between items-center" id="alert-notifikasi">
    <span>{{ session('status') }}</span>
    <button type="button" onclick="document.getElementById('alert-notifikasi').remove()" class="text-primary hover:bg-primary/20 p-1 rounded-full flex items-center transition-colors">
        <span class="material-symbols-outlined text-[20px]">close</span>
    </button>
</div>
@endif

@if($errors->any())
<div class="mb-6 p-4 bg-error-container text-[#ba1a1a] rounded-lg border border-red-200 font-bold">
    @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
    @endforeach
</div>
@endif

<div class="flex flex-col lg:flex-row gap-6">
    <!-- Form Tambah Libur (Kiri) -->
    <div class="w-full lg:w-1/3">
        <div class="bg-surface rounded-xl shadow-sm border border-outline-variant p-6 sticky top-24">
            <h3 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">calendar_add_on</span>
                Tambah Libur
            </h3>
            
            <form action="{{ route('admin.hari-libur.store') }}" method="POST" class="flex flex-col gap-4">
                @csrf
                <div class="flex flex-col gap-1.5">
                    <label class="font-bold text-sm text-on-surface-variant">Tanggal Libur</label>
                    <input type="date" name="tanggal" required class="h-12 rounded-lg border border-outline-variant px-4 bg-surface-container-lowest text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none text-sm">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="font-bold text-sm text-on-surface-variant">Jenis Libur</label>
                    <select name="jenis_libur" required class="h-12 rounded-lg border border-outline-variant px-4 bg-surface-container-lowest text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none text-sm appearance-none cursor-pointer">
                        <option value="Tanggal Merah">Tanggal Merah</option>
                        <option value="Cuti Bersama">Cuti Bersama</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="font-bold text-sm text-on-surface-variant">Keterangan / Nama Libur</label>
                    <textarea name="keterangan" rows="2" required placeholder="Misal: Idul Fitri 1447 H" class="rounded-lg border border-outline-variant p-4 bg-surface-container-lowest text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none text-sm resize-none"></textarea>
                </div>

                <button type="submit" class="w-full h-12 bg-primary text-white font-bold rounded-lg mt-2 hover:bg-primary/90 transition-all shadow-sm">
                    Simpan Tanggal
                </button>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar Libur (Kanan) -->
    <div class="w-full lg:w-2/3 bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[500px]">
                <thead class="bg-surface-container-low border-b border-outline-variant">
                    <tr>
                        <th class="py-4 px-6 font-bold text-sm text-on-surface-variant">Tanggal</th>
                        <th class="py-4 px-6 font-bold text-sm text-on-surface-variant">Jenis</th>
                        <th class="py-4 px-6 font-bold text-sm text-on-surface-variant">Keterangan</th>
                        <th class="py-4 px-6 font-bold text-sm text-on-surface-variant text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant bg-white">
                    @forelse($liburs as $libur)
                    <tr class="hover:bg-surface-container-lowest transition-colors">
                        <td class="py-4 px-6 text-sm font-bold text-on-surface whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($libur->tanggal)->translatedFormat('d F Y') }}
                        </td>
                        <td class="py-4 px-6 text-sm">
                            @if($libur->jenis_libur == 'Tanggal Merah')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-error-container text-[#ba1a1a] border border-red-200">Tanggal Merah</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-secondary-container/30 text-secondary border border-secondary/20">Cuti Bersama</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-sm text-on-surface-variant">{{ $libur->keterangan }}</td>
                        <td class="py-4 px-6 text-right">
                            <form action="{{ route('admin.hari-libur.destroy', $libur->id) }}" method="POST" onsubmit="return confirm('Hapus tanggal libur ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-[#ba1a1a] bg-error-container/40 hover:bg-error-container rounded-full transition-colors inline-flex" title="Hapus">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-on-surface-variant">Belum ada hari libur yang didaftarkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-outline-variant">
            {{ $liburs->links() }}
        </div>
    </div>
</div>
@endsection
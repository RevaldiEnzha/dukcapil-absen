<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - Disdukcapil')</title>
    
    <!-- Google Fonts & Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface font-body-lg flex">

    <!-- ================= OVERLAY MOBILE ================= -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-on-surface/50 z-40 hidden md:hidden backdrop-blur-sm transition-opacity opacity-0"></div>

    <!-- SideNavBar (Desktop & Mobile) -->
    <aside id="admin-sidebar" class="w-[280px] h-screen fixed left-0 top-0 bg-surface-container-lowest shadow-sm z-50 flex flex-col transition-transform duration-300 ease-in-out -translate-x-full md:translate-x-0">

        <!-- Tombol Close (X) Khusus Mobile -->
        <button type="button" onclick="toggleSidebar()" class="md:hidden absolute top-4 right-4 p-2 bg-surface-variant/50 hover:bg-surface-variant rounded-full text-on-surface transition-colors flex items-center justify-center z-50" aria-label="Tutup Menu">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>

        <div class="p-6 flex flex-col items-center justify-center border-b border-surface-container-highest">
            <img alt="Disdukcapil Logo" class="w-32 h-auto object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCANGO0mrRx1BYfvqPHS7kvheSXOy0vM6sA7eUTRXOC1h92YwkR32_8ru0tiYVtwLWSUIOde_EQQiH73QQm41UVMlUcml5PqeyM1swl8Y0E27t1ZQr9cDDWXFJAVQHTDiEnK58cYUll0-mIFMBmsWZUw8dtM1OONChMRn7lHFmH5mk10X2TuxVLtMpI8DjGvietW1LNl8wHP8OXk59dD0MRQ8D-OFURNw3WGQY5QrVnHA0aqgAb1AvjzZe9ECm2i2ctAnw">
            <div class="text-center mt-3 mb-2 flex flex-col items-center">
                <h2 class="text-lg font-black text-primary tracking-tight leading-tight">Presensi Non-ASN</h2>
                <p class="text-[10px] text-on-surface-variant uppercase tracking-widest font-bold mt-0.5">Disdukcapil Kota Cirebon</p>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto flex flex-col gap-2 py-6">
            <!-- Sidebar Menu: Dashboard -->
            <a class="{{ request()->routeIs('admin.dashboard') ? 'bg-secondary-container text-on-secondary-container font-label-bold' : 'text-on-surface-variant hover:bg-surface-container-highest' }} rounded-full mx-3 px-4 py-3 flex items-center gap-4 transition-colors" href="{{ route('admin.dashboard') }}">
                <span class="material-symbols-outlined" {!! request()->routeIs('admin.dashboard') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>dashboard</span>
                <span>Dashboard</span>
            </a>

            <!-- Sidebar Menu: Pegawai -->
            <a class="{{ request()->routeIs('admin.pegawai.*') ? 'bg-secondary-container text-on-secondary-container font-label-bold' : 'text-on-surface-variant hover:bg-surface-container-highest' }} rounded-full mx-3 px-4 py-3 flex items-center gap-4 transition-colors" href="{{ route('admin.pegawai.index') }}">
                <span class="material-symbols-outlined" {!! request()->routeIs('admin.pegawai.*') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>group</span>
                <span>Pegawai</span>
            </a>
            
            <!-- Sidebar Menu: Absensi -->
            <a class="{{ request()->routeIs('admin.absensi.*') ? 'bg-secondary-container text-on-secondary-container font-label-bold' : 'text-on-surface-variant hover:bg-surface-container-highest' }} rounded-full mx-3 px-4 py-3 flex items-center gap-4 transition-colors" href="{{ route('admin.absensi.index') }}">
                <span class="material-symbols-outlined" {!! request()->routeIs('admin.absensi.*') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>fact_check</span>
                <span>Absensi</span>
            </a>

            <!-- Sidebar Menu: Izin -->
            <a class="{{ request()->routeIs('admin.izin.*') ? 'bg-secondary-container text-on-secondary-container font-label-bold' : 'text-on-surface-variant hover:bg-surface-container-highest' }} rounded-full mx-3 px-4 py-3 flex items-center gap-4 transition-colors" href="{{ route('admin.izin.index') }}">
                <span class="material-symbols-outlined" {!! request()->routeIs('admin.izin.*') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>event_busy</span>
                <span>Izin</span>
            </a>

            <!-- Sidebar Menu: Hari Libur -->
            <a class="{{ request()->routeIs('admin.hari-libur.*') ? 'bg-secondary-container text-on-secondary-container font-label-bold' : 'text-on-surface-variant hover:bg-surface-container-highest' }} rounded-full mx-3 px-4 py-3 flex items-center gap-4 transition-colors" href="{{ route('admin.hari-libur.index') }}">
                <span class="material-symbols-outlined" {!! request()->routeIs('admin.hari-libur.*') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>event</span>
                <span>Hari Libur</span>
            </a>

            <!-- Sidebar Menu: Laporan -->
            <a class="{{ request()->routeIs('admin.laporan.*') ? 'bg-secondary-container text-on-secondary-container font-label-bold' : 'text-on-surface-variant hover:bg-surface-container-highest' }} rounded-full mx-3 px-4 py-3 flex items-center gap-4 transition-colors" href="{{ route('admin.laporan.index') }}">
                <span class="material-symbols-outlined" {!! request()->routeIs('admin.laporan.*') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>assessment</span>
                <span>Laporan</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 ml-0 md:ml-[280px] flex flex-col min-h-screen">
        
        <!-- TopNavBar -->
        <header class="w-full h-16 sticky top-0 z-30 bg-surface border-b border-outline-variant flex items-center justify-between px-6">
            <!-- Tambahkan aksi onclick -->
            <div class="md:hidden" onclick="toggleSidebar()">
                <span class="material-symbols-outlined text-on-surface-variant cursor-pointer text-3xl">menu</span>
            </div>
            <h2 class="font-headline-md font-bold text-primary hidden md:block">Disdukcapil Kota Cirebon</h2>
            
            <div class="flex items-center gap-4">
                <div class="relative hidden sm:block">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 transform -translate-y-1/2 text-on-surface-variant">search</span>
                    <input class="pl-10 pr-4 py-2 bg-surface-container border border-outline-variant rounded-full focus:outline-none focus:border-primary transition-colors text-sm" placeholder="Search..." type="text">
                </div>
                
                <!-- Tombol Logout -->
                <form id="form-logout-admin" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <!-- Warna dasar diset menjadi teks merah dan background transparan merah -->
                    <button type="button" onclick="bukaModalLogoutAdmin()" class="text-[#ba1a1a] bg-error-container/40 hover:bg-error-container hover:text-[#93000a] transition-colors p-2 rounded-full flex items-center justify-center" title="Keluar">
                        <span class="material-symbols-outlined">logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Area Konten Dinamis -->
        <main class="flex-1 p-6 md:p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    <!-- ================= MODAL KONFIRMASI LOGOUT ADMIN ================= -->
    <div id="modal-logout-admin" class="fixed inset-0 bg-on-surface/50 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-surface w-full max-w-sm rounded-2xl p-6 shadow-xl transform scale-95 transition-transform" id="sheet-logout-admin">
            <div class="w-16 h-16 bg-error-container text-[#ba1a1a] rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[32px]">logout</span>
            </div>
            <h3 class="text-lg font-bold text-on-surface text-center mb-2">Yakin Ingin Keluar?</h3>
            <p class="text-sm text-on-surface-variant text-center mb-6">Sesi Anda akan diakhiri dan Anda harus login kembali untuk masuk ke dasbor admin.</p>
            
            <div class="flex gap-3">
                <button type="button" onclick="tutupModalLogoutAdmin()" class="flex-1 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-bold text-sm hover:bg-surface-container-highest transition-colors">Batal</button>
                <button type="button" onclick="document.getElementById('form-logout-admin').submit()" class="flex-1 py-2.5 rounded-lg bg-[#ba1a1a] text-white font-bold text-sm hover:bg-[#93000a] transition-colors shadow-sm">Ya, Keluar</button>
            </div>
        </div>
    </div>

    <script>
        const mLogoutAdmin = document.getElementById('modal-logout-admin');
        const sLogoutAdmin = document.getElementById('sheet-logout-admin');

        function bukaModalLogoutAdmin() {
            mLogoutAdmin.classList.remove('hidden');
            mLogoutAdmin.classList.add('flex');
            setTimeout(() => {
                mLogoutAdmin.classList.remove('opacity-0');
                sLogoutAdmin.classList.remove('scale-95');
            }, 10);
        }

        function tutupModalLogoutAdmin() {
            mLogoutAdmin.classList.add('opacity-0');
            sLogoutAdmin.classList.add('scale-95');
            setTimeout(() => {
                mLogoutAdmin.classList.add('hidden');
                mLogoutAdmin.classList.remove('flex');
            }, 300);
        }

        // Fungsi untuk Buka/Tutup Menu Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            // Geser sidebar masuk/keluar layar
            sidebar.classList.toggle('-translate-x-full');
            
            // Atur visibilitas latar belakang gelap (overlay)
            if (sidebar.classList.contains('-translate-x-full')) {
                // Proses menyembunyikan overlay
                overlay.classList.add('opacity-0');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
            } else {
                // Proses memunculkan overlay
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.remove('opacity-0');
                }, 10);
            }
        }
    </script>
</body>
</html>
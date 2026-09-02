<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Absensi Disdukcapil')</title>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#003f83",
                        "primary-container": "#1a56a6",
                        "on-primary-container": "#b7ceff",
                        "secondary-container": "#0070ea",
                        "on-secondary-container": "#fefcff",
                        "surface": "#f9f9ff",
                        "on-surface": "#191c21",
                        "surface-variant": "#e2e2e9",
                        "on-surface-variant": "#424751",
                        "surface-container-highest": "#e2e2e9",
                        "surface-container-lowest": "#ffffff",
                        "outline": "#737783",
                        "outline-variant": "#c2c6d3",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                        "on-error-container": "#93000a",
                    },
                    fontFamily: {
                        "sans": ["Hanken Grotesk", "sans-serif"],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-surface text-on-surface h-screen w-full flex overflow-hidden antialiased">
    <!-- DESKTOP SIDEBAR (Sembunyi di Mobile) -->
    <aside class="hidden md:flex flex-col w-[280px] h-screen bg-surface-container-lowest border-r border-outline-variant shadow-sm z-20">
        
        <!-- PERBAIKAN: Logo Gambar disamakan dengan Admin -->
        <div class="p-6 flex flex-col items-center justify-center border-b border-outline-variant/30">
            <img alt="Disdukcapil Logo" class="w-32 h-auto object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCANGO0mrRx1BYfvqPHS7kvheSXOy0vM6sA7eUTRXOC1h92YwkR32_8ru0tiYVtwLWSUIOde_EQQiH73QQm41UVMlUcml5PqeyM1swl8Y0E27t1ZQr9cDDWXFJAVQHTDiEnK58cYUll0-mIFMBmsWZUw8dtM1OONChMRn7lHFmH5mk10X2TuxVLtMpI8DjGvietW1LNl8wHP8OXk59dD0MRQ8D-OFURNw3WGQY5QrVnHA0aqgAb1AvjzZe9ECm2i2ctAnw">
            <div class="text-center mt-3 mb-2 flex flex-col items-center">
                <h2 class="text-lg font-black text-primary tracking-tight leading-tight">Presensi Non-ASN</h2>
                <p class="text-[10px] text-on-surface-variant uppercase tracking-widest font-bold mt-0.5">Disdukcapil Kota Cirebon</p>
            </div>
        </div>
        
        <nav class="flex-1 px-4 py-4 space-y-2">
            <a href="{{ route('pegawai.dashboard') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('pegawai.dashboard') ? 'bg-primary/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant font-medium' }} rounded-xl transition-colors">
                <span class="material-symbols-outlined" {!! request()->routeIs('pegawai.dashboard') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>home</span>
                Dashboard
            </a>
            <a href="{{ route('pegawai.absen') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('pegawai.absen') ? 'bg-primary/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant font-medium' }} rounded-xl transition-colors">
                <span class="material-symbols-outlined" {!! request()->routeIs('pegawai.absen') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>fingerprint</span>
                Absen
            </a>
            <a href="{{ route('pegawai.izin') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('pegawai.izin') ? 'bg-primary/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant font-medium' }} rounded-xl transition-colors">
                <span class="material-symbols-outlined" {!! request()->routeIs('pegawai.izin') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>event_note</span>
                Izin
            </a>
            <a href="{{ route('pegawai.profil') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('pegawai.profil') ? 'bg-primary/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant font-medium' }} rounded-xl transition-colors">
                <span class="material-symbols-outlined" {!! request()->routeIs('pegawai.profil') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>person</span>
                Profil
            </a>
        </nav>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        
        <!-- HEADER (Mobile Only) -->
        <header class="md:hidden w-full flex items-center justify-between px-6 py-4 bg-surface sticky top-0 z-40">
            <!-- PERBAIKAN: Logo Gambar untuk tampilan Mobile -->
            <div class="flex items-center">
                <img alt="Disdukcapil Logo" class="h-10 w-auto object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCANGO0mrRx1BYfvqPHS7kvheSXOy0vM6sA7eUTRXOC1h92YwkR32_8ru0tiYVtwLWSUIOde_EQQiH73QQm41UVMlUcml5PqeyM1swl8Y0E27t1ZQr9cDDWXFJAVQHTDiEnK58cYUll0-mIFMBmsWZUw8dtM1OONChMRn7lHFmH5mk10X2TuxVLtMpI8DjGvietW1LNl8wHP8OXk59dD0MRQ8D-OFURNw3WGQY5QrVnHA0aqgAb1AvjzZe9ECm2i2ctAnw">
                <div class="text-center mt-3 mb-2 flex flex-col items-center">
                    <h2 class="text-lg font-black text-primary tracking-tight leading-tight">Presensi Non-ASN</h2>
                    <p class="text-[10px] text-on-surface-variant uppercase tracking-widest font-bold mt-0.5">Disdukcapil Kota Cirebon</p>
                </div>
            </div>
        </header>

        <!-- KONTEN HALAMAN -->
        <main class="flex-1 overflow-y-auto px-6 pt-4 pb-28 md:pb-6">
            <div class="max-w-2xl mx-auto w-full">
                @yield('content')
            </div>
        </main>

        <!-- BOTTOM NAVBAR (Mobile Only) -->
        <nav class="md:hidden fixed bottom-0 left-0 w-full z-50 h-20 bg-surface-container-lowest border-t border-outline-variant shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] flex justify-around items-center px-2">
            
            <!-- Tombol Dashboard -->
            <a href="{{ route('pegawai.dashboard') }}" class="flex flex-col items-center justify-center w-16 group active:scale-95 transition-transform">
                <div class="{{ request()->routeIs('pegawai.dashboard') ? 'bg-primary-container text-white shadow-sm' : 'text-on-surface-variant group-hover:text-primary' }} rounded-full px-5 py-1 mb-1 transition-colors">
                    <span class="material-symbols-outlined" {!! request()->routeIs('pegawai.dashboard') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>home</span>
                </div>
                <span class="font-bold text-[11px] {{ request()->routeIs('pegawai.dashboard') ? 'text-primary' : 'text-on-surface-variant group-hover:text-primary' }}">Dashboard</span>
            </a>

            <!-- Tombol Absen (Baru Ditambahkan) -->
            <a href="{{ route('pegawai.absen') }}" class="flex flex-col items-center justify-center w-16 group active:scale-95 transition-transform">
                <div class="{{ request()->routeIs('pegawai.absen') ? 'bg-primary-container text-white shadow-sm' : 'text-on-surface-variant group-hover:text-primary' }} rounded-full px-5 py-1 mb-1 transition-colors">
                    <span class="material-symbols-outlined" {!! request()->routeIs('pegawai.absen') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>fingerprint</span>
                </div>
                <span class="font-bold text-[11px] {{ request()->routeIs('pegawai.absen') ? 'text-primary' : 'text-on-surface-variant group-hover:text-primary' }}">Absen</span>
            </a>
            
            <!-- Tombol Izin -->
            <a href="{{ route('pegawai.izin') }}" class="flex flex-col items-center justify-center w-16 group active:scale-95 transition-transform">
                <div class="{{ request()->routeIs('pegawai.izin') ? 'bg-primary-container text-white shadow-sm' : 'text-on-surface-variant group-hover:text-primary' }} rounded-full px-5 py-1 mb-1 transition-colors">
                    <span class="material-symbols-outlined" {!! request()->routeIs('pegawai.izin') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>event_note</span>
                </div>
                <span class="font-bold text-[11px] {{ request()->routeIs('pegawai.izin') ? 'text-primary' : 'text-on-surface-variant group-hover:text-primary' }}">Izin</span>
            </a>
            
            <!-- Tombol Profil -->
            <a href="{{ route('pegawai.profil') }}" class="flex flex-col items-center justify-center w-16 group active:scale-95 transition-transform">
                <div class="{{ request()->routeIs('pegawai.profil') ? 'bg-primary-container text-white shadow-sm' : 'text-on-surface-variant group-hover:text-primary' }} rounded-full px-5 py-1 mb-1 transition-colors">
                    <span class="material-symbols-outlined" {!! request()->routeIs('pegawai.profil') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>person</span>
                </div>
                <span class="font-bold text-[11px] {{ request()->routeIs('pegawai.profil') ? 'text-primary' : 'text-on-surface-variant group-hover:text-primary' }}">Profil</span>
            </a>
            
        </nav>

    </div>

</body>
</html>
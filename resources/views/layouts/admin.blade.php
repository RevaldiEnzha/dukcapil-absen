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

    <!-- SideNavBar (Desktop) -->
    <aside class="w-[280px] h-screen fixed left-0 top-0 bg-surface-container-lowest shadow-sm z-50 flex flex-col hidden md:flex">
        <div class="p-6 flex flex-col items-center border-b border-surface-container-highest">
            <img alt="Disdukcapil Logo" class="w-32 h-auto mb-4 object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCANGO0mrRx1BYfvqPHS7kvheSXOy0vM6sA7eUTRXOC1h92YwkR32_8ru0tiYVtwLWSUIOde_EQQiH73QQm41UVMlUcml5PqeyM1swl8Y0E27t1ZQr9cDDWXFJAVQHTDiEnK58cYUll0-mIFMBmsWZUw8dtM1OONChMRn7lHFmH5mk10X2TuxVLtMpI8DjGvietW1LNl8wHP8OXk59dD0MRQ8D-OFURNw3WGQY5QrVnHA0aqgAb1AvjzZe9ECm2i2ctAnw">
            <h1 class="font-headline-md text-primary text-center font-bold leading-tight">Disdukcapil</h1>
            <p class="font-body-sm text-on-surface-variant">Kota Cirebon</p>
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
        <header class="w-full h-16 sticky top-0 z-40 bg-surface border-b border-outline-variant flex items-center justify-between px-6">
            <div class="md:hidden">
                <span class="material-symbols-outlined text-on-surface-variant cursor-pointer text-3xl">menu</span>
            </div>
            <h2 class="font-headline-md font-bold text-primary hidden md:block">Disdukcapil Kota Cirebon</h2>
            
            <div class="flex items-center gap-4">
                <div class="relative hidden sm:block">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 transform -translate-y-1/2 text-on-surface-variant">search</span>
                    <input class="pl-10 pr-4 py-2 bg-surface-container border border-outline-variant rounded-full focus:outline-none focus:border-primary transition-colors text-sm" placeholder="Search..." type="text">
                </div>
                
                <!-- Tombol Logout -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-on-surface-variant hover:bg-error-container hover:text-on-error-container transition-colors p-2 rounded-full flex items-center justify-center" title="Keluar">
                        <span class="material-symbols-outlined">logout</span>
                    </button>
                </form>

                <div class="w-10 h-10 rounded-full bg-surface-container-highest overflow-hidden border border-outline-variant">
                    <img alt="Admin Profile" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCQKLr1eiuaVJm99iQ69gY4gT2RQRCfATnnuUYHoq2wNgZGywvEl7PVTw8x0QQjj1lo0dbpJXUpGjI1JEPR6n_GfifiLGTZ8OHrIZHtlgCzYu9AdVniqPPbuS3FZ0-AFYXdVgZCPe5EKCwOKpEtUvUbV3CE4otjGgoXC_OzFYvtCRek4m6OMIQYGepNO8P7Kkszb-_aSx1yiLxDAhz9B3FBEtHcs6jvu3JSwoEncb3bSq9HsFg3Z_seng">
                </div>
            </div>
        </header>

        <!-- Area Konten Dinamis -->
        <main class="flex-1 p-6 md:p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</body>
</html>
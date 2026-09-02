<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Absensi')</title>
    
    <!-- Google Fonts & Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <!-- WAJIB ADA: Memanggil Tailwind CSS lewat Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-container-low text-on-surface antialiased min-h-screen flex items-center justify-center p-4" style="font-family: 'Hanken Grotesk', sans-serif;">
    
    <!-- Pembungkus Utama (Card) -->
    <div class="w-full max-w-md bg-surface-container-lowest rounded-2xl shadow-lg border border-surface-variant p-8 relative overflow-hidden">
        <!-- Sedikit dekorasi visual di pojok atas -->
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-primary/5 rounded-full pointer-events-none"></div>
        
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
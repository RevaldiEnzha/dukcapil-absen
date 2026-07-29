@extends('layouts.guest')

@section('title', 'Masuk - Sistem Absensi Pegawai Non-ASN')

@section('content')
<div class="flex flex-col items-center w-full relative z-10">
    <!-- Logo Disdukcapil -->
    <div class="mb-6 w-40 flex justify-center">
        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuB_WYfy6X4LdWjJfb726VyhzMivQ34HWHSsypSJyu390HG5nUQCoS3oqJZ5E8J1hAZY114uo8ZxqtWAqo-9DEit60R6m5f-RhvIl7RYVP_XmiaiGoyjUnhs6EEaNZgBBUItvQs5ROqJ4ERZ-jDdnuGUArA4rJElIVgQOeQvebo-ugjCbJkE1Up4muh5n64WEseiucoxGs5cWiNHIhGExBFdX9k5T1qZdfhGDHnzYyIEGGjy3YpCdIVsxXTVUtcZF9p496M" 
             alt="Logo Disdukcapil" 
             class="w-full h-auto object-contain">
    </div>

    <!-- Judul -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-primary mb-1">Sistem Absensi</h1>
        <h2 class="text-sm font-semibold text-on-surface-variant">Pegawai Non-ASN</h2>
    </div>

    <!-- Menampilkan Error jika salah password -->
    @if ($errors->any())
        <div class="w-full mb-6 p-4 bg-error-container text-on-error-container rounded-lg text-sm border border-red-200">
            <span class="font-bold flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-[18px]">error</span>
                Gagal Masuk!
            </span>
            <ul class="list-disc list-inside text-xs space-y-1 ml-6">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('login.process') }}" class="w-full flex flex-col gap-5">
        @csrf

        <!-- Input Username -->
        <div class="relative w-full">
            <input type="text" id="username" name="username" value="{{ old('username') }}"
                   class="peer w-full h-[56px] bg-transparent border-2 border-outline-variant focus:border-primary rounded-lg outline-none px-4 pt-5 pb-1 text-on-surface transition-colors placeholder-transparent"
                   placeholder="Username" required autofocus>
            <label for="username" 
                   class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm transition-all pointer-events-none peer-focus:top-2 peer-focus:text-xs peer-focus:text-primary peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:text-xs">
                Username
            </label>
        </div>

        <!-- Input Password -->
        <div class="relative w-full">
            <input type="password" id="password" name="password"
                   class="peer w-full h-[56px] bg-transparent border-2 border-outline-variant focus:border-primary rounded-lg outline-none px-4 pt-5 pb-1 pr-12 text-on-surface transition-colors placeholder-transparent"
                   placeholder="Password" required>
            <label for="password" 
                   class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm transition-all pointer-events-none peer-focus:top-2 peer-focus:text-xs peer-focus:text-primary peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:text-xs">
                Password
            </label>
            <!-- Tombol Toggle Mata -->
            <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition-colors flex items-center justify-center focus:outline-none" onclick="togglePassword()">
                <span class="material-symbols-outlined text-[20px]" id="visibility-icon">visibility_off</span>
            </button>
        </div>

        <!-- Lupa Sandi -->
        <div class="flex justify-end w-full">
            <a href="#" class="text-sm font-bold text-primary hover:underline hover:text-primary-container transition-colors">Lupa Kata Sandi?</a>
        </div>

        <!-- Tombol Masuk -->
        <button type="submit" class="w-full h-[56px] mt-2 bg-primary text-white rounded-full font-bold shadow-md hover:bg-primary-container hover:shadow-lg transition-all active:scale-[0.98]">
            Masuk
        </button>
    </form>
</div>

@push('scripts')
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const visibilityIcon = document.getElementById('visibility-icon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            visibilityIcon.textContent = 'visibility';
        } else {
            passwordInput.type = 'password';
            visibilityIcon.textContent = 'visibility_off';
        }
    }
</script>
@endpush
@endsection
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LabRPL AdminPanel</title>
    <!-- Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script> --}}
    <link rel="preconnect" href="https://challenges.cloudflare.com" />
    <!-- PWA & Apple Mobile Web Support -->
    <meta name="theme-color" content="#001f3f">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('image/logo-RPL.jpg') }}">
    <link rel="apple-touch-startup-image" href="{{ asset('image/logo-RPL.jpg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Safer fix for standalone mode links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link &&
                link.href &&
                link.href.indexOf('http') === 0 &&
                link.href.indexOf(window.location.host) !== -1 &&
                link.target !== '_blank') {
                e.preventDefault();
                window.location.href = link.href;
            }
        }, false);
    </script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>
</head>

<body class="bg-zinc-50 font-sans text-zinc-900 antialiased min-h-screen flex flex-col items-center justify-center p-4">

    <!-- Header Section -->
    <div class="text-center mb-8">
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('image/logo-RPL.jpg') }}" class="h-24 w-auto object-contain" alt="Logo">
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900">LabRPL AdminPanel</h1>
        <p class="text-sm text-zinc-500 mt-2 max-w-sm mx-auto">Sistem Informasi Laboratorium Rekayasa Perangkat Lunak -
            ITATS</p>
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-md bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="p-8">
            <h2 class="text-lg font-bold text-zinc-900 mb-8 tracking-tight text-center">Login Admin</h2>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                <div class="space-y-2">
                    <label for="username" class="block text-sm font-medium text-zinc-900">Username</label>
                    <div class="relative group">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-zinc-900 transition-colors">
                            <i class="fas fa-user text-sm"></i>
                        </span>
                        <input type="text" name="username" id="username" value="{{ old('username') }}"
                            class="block w-full rounded-lg border border-zinc-300 pl-10 pr-3 py-2.5 text-sm transition-all focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 placeholder:text-zinc-400 font-medium"
                            placeholder="Masukkan username anda" required autofocus>
                    </div>
                    @error('username')
                        <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-zinc-900">Password</label>
                    <div class="relative group">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-zinc-900 transition-colors">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password" id="password"
                            class="block w-full rounded-lg border border-zinc-300 pl-10 pr-3 py-2.5 text-sm transition-all focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 placeholder:text-zinc-400 font-medium"
                            placeholder="••••••••" required>
                    </div>
                </div>

                <div class="flex items-center justify-between py-2">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="remember"
                            class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 transition-all cursor-pointer">
                        <span class="text-sm text-zinc-600 group-hover:text-zinc-900 transition-colors">Ingat
                            saya</span>
                    </label>
                    <a href="{{ route('password.request') }}"
                        class="text-sm font-medium text-zinc-500 hover:text-zinc-900 transition-colors">Lupa
                        Password?</a>
                </div>

                <div class="flex items-center justify-center py-2">
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.key') }}"></div>
                </div>

                <button type="submit"
                    class="w-full flex justify-center items-center gap-2 rounded-lg bg-zinc-900 px-4 py-3 text-sm font-bold text-white hover:bg-zinc-800 transition-all active:scale-[0.98] shadow-sm mt-2">
                    <span>Masuk Sekarang</span>
                    <i class="fas fa-arrow-right-to-bracket text-xs"></i>
                </button>
            </form>
        </div>

        <!-- Action Tip Bottom -->
        <div class="px-8 py-5 bg-zinc-50 border-t border-zinc-100 space-y-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-lg bg-white border border-zinc-200 flex items-center justify-center text-zinc-400 flex-shrink-0 shadow-sm">
                    <i class="fas fa-info text-[10px]"></i>
                </div>
                <p class="text-xs text-zinc-500 font-medium leading-tight italic">Gunakan username yang telah terdaftar
                    oleh Administrator Lab.</p>
            </div>

            <div class="h-[1px] bg-zinc-200/50 w-full"></div>


        </div>
    </div>

    <!-- Back to Public -->
    <a href="/"
        class="mt-8 inline-flex items-center gap-2 text-sm font-medium text-zinc-500 hover:text-zinc-900 transition-colors group">
        <i class="fas fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
        <span>Kembali ke Halaman Utama</span>
    </a>

    <p class="text-center text-[11px] font-bold text-zinc-300 uppercase tracking-[0.2em] mt-12 mb-4">
        &copy; {{ date('Y') }} LabRPL TEKNIK INFORMATIKA ITATS
    </p>

    @if (session('logout_success') || session('success'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: 'success',
                title: '{{ session('logout_success') ?? session('success') }}'
            })
        </script>
    @endif
</body>

</html>

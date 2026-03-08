<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Praktikan - LabRPL</title>
    <!-- Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('image/logo-RPL.jpg') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('image/logo-RPL.jpg') }}" type="image/x-icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-zinc-50 font-sans text-zinc-900 antialiased min-h-screen flex flex-col items-center justify-center p-4 py-12">

    <!-- Header Section -->
    <div class="text-center mb-8">
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('image/logo-RPL.jpg') }}" class="h-24 w-auto object-contain" alt="Logo">
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900">LabRPL AdminPanel</h1>
        <p class="text-sm text-zinc-500 mt-2 max-w-sm mx-auto">Sistem Informasi Laboratorium Rekayasa Perangkat Lunak -
            ITATS</p>
    </div>

    <!-- Register Card -->
    <div class="w-full max-w-lg bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="p-8">
            <h2 class="text-lg font-bold text-zinc-900 mb-8 tracking-tight text-center">Pendaftaran Praktikan</h2>

            @if ($errors->has('error'))
                <div
                    class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 text-xs font-bold uppercase tracking-widest text-center">
                    {{ $errors->first('error') }}
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label for="npm" class="block text-sm font-medium text-zinc-900">NPM</label>
                        <div class="relative group">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-zinc-900 transition-colors">
                                <i class="fas fa-id-card text-sm"></i>
                            </span>
                            <input type="text" name="npm" id="npm" value="{{ old('npm') }}"
                                class="block w-full rounded-lg border border-zinc-300 pl-10 pr-3 py-2.5 text-sm transition-all focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 placeholder:text-zinc-400 font-medium"
                                placeholder="Contoh: 06.2024.1.XXXXX" required autofocus>
                        </div>
                        @error('npm')
                            <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium text-zinc-900">Nama Lengkap</label>
                        <div class="relative group">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-zinc-900 transition-colors">
                                <i class="fas fa-user-tag text-sm"></i>
                            </span>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="block w-full rounded-lg border border-zinc-300 pl-10 pr-3 py-2.5 text-sm transition-all focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 placeholder:text-zinc-400 font-medium"
                                placeholder="Masukkan nama lengkap" required>
                        </div>
                        @error('name')
                            <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-zinc-900">Email Kampus</label>
                    <div class="relative group">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-zinc-900 transition-colors">
                            <i class="fas fa-envelope text-sm"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="block w-full rounded-lg border border-zinc-300 pl-10 pr-3 py-2.5 text-sm transition-all focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 placeholder:text-zinc-400 font-medium"
                            placeholder="user@itatsu.student.ac.id" required>
                    </div>
                    @error('email')
                        <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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
                        @error('password')
                            <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-zinc-900">Konfirmasi</label>
                        <div class="relative group">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-zinc-900 transition-colors">
                                <i class="fas fa-check-double text-sm"></i>
                            </span>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="block w-full rounded-lg border border-zinc-300 pl-10 pr-3 py-2.5 text-sm transition-all focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 placeholder:text-zinc-400 font-medium"
                                placeholder="••••••••" required>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full flex justify-center items-center gap-2 rounded-lg bg-zinc-900 px-4 py-3 text-sm font-bold text-white hover:bg-zinc-800 transition-all active:scale-[0.98] shadow-sm mt-4">
                    <span>Daftar Sekarang</span>
                    <i class="fas fa-paper-plane text-xs"></i>
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-zinc-100 text-center">
                <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Sudah punya akun?</p>
                <a href="{{ route('login') }}"
                    class="inline-block mt-3 text-sm font-bold text-zinc-900 hover:text-zinc-700 transition-all uppercase tracking-widest">MASUK
                    KEMBALI</a>
            </div>
        </div>
    </div>

    <!-- Back to Public -->
    <a href="/"
        class="mt-8 inline-flex items-center gap-2 text-sm font-medium text-zinc-500 hover:text-zinc-900 transition-colors group">
        <i class="fas fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
        <span>Halaman Utama</span>
    </a>

    <p class="text-center text-[11px] font-bold text-zinc-300 uppercase tracking-[0.2em] mt-12 mb-4">
        &copy; {{ date('Y') }} LabRPL TEKNIK INFORMATIKA ITATS
    </p>
</body>

</html>

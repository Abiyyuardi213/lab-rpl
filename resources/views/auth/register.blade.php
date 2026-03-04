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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-zinc-50 font-sans text-zinc-900 antialiased min-h-screen flex flex-col items-center justify-center p-4 py-12">

    <!-- Header Section -->
    <div class="text-center mb-8">
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('image/rplmini.png') }}" class="h-24 w-auto object-contain" alt="Logo">
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 leading-none">LabRPL ITATS</h1>
        <p class="text-[10px] font-black tracking-[0.3em] uppercase text-zinc-400 mt-3">Sistem Informasi Laboratorium
        </p>
    </div>

    <!-- Register Card -->
    <div class="w-full max-w-lg bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="p-8">
            <h2 class="text-xl font-black text-zinc-900 mb-8 uppercase tracking-tight text-center">Pendaftaran Praktikan
            </h2>

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
                        <label for="username"
                            class="block text-xs font-black uppercase tracking-widest text-zinc-400">Username
                            (NPM)</label>
                        <div class="relative group">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-zinc-900 transition-colors">
                                <i class="fas fa-id-card text-xs"></i>
                            </span>
                            <input type="text" name="username" id="username" value="{{ old('username') }}"
                                class="block w-full rounded-xl border border-zinc-300 pl-10 pr-3 py-3 text-sm transition-all focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 placeholder:text-zinc-400 font-bold"
                                placeholder="Contoh: 06.2024.1.XXXXX" required>
                        </div>
                        @error('username')
                            <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="name"
                            class="block text-xs font-black uppercase tracking-widest text-zinc-400">Nama
                            Lengkap</label>
                        <div class="relative group">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-zinc-900 transition-colors">
                                <i class="fas fa-user-tag text-xs"></i>
                            </span>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="block w-full rounded-xl border border-zinc-300 pl-10 pr-3 py-3 text-sm transition-all focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 placeholder:text-zinc-400 font-bold"
                                placeholder="Masukkan nama lengkap" required>
                        </div>
                        @error('name')
                            <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="email" class="block text-xs font-black uppercase tracking-widest text-zinc-400">Email
                        Kampus</label>
                    <div class="relative group">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-zinc-900 transition-colors">
                            <i class="fas fa-envelope text-xs"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="block w-full rounded-xl border border-zinc-300 pl-10 pr-3 py-3 text-sm transition-all focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 placeholder:text-zinc-400 font-bold"
                            placeholder="user@itatsu.student.ac.id" required>
                    </div>
                    @error('email')
                        <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label for="password"
                            class="block text-xs font-black uppercase tracking-widest text-zinc-400">Password</label>
                        <div class="relative group">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-zinc-900 transition-colors">
                                <i class="fas fa-lock text-xs"></i>
                            </span>
                            <input type="password" name="password" id="password"
                                class="block w-full rounded-xl border border-zinc-300 pl-10 pr-3 py-3 text-sm transition-all focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 placeholder:text-zinc-400 font-bold"
                                placeholder="••••••••" required>
                        </div>
                        @error('password')
                            <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="password_confirmation"
                            class="block text-xs font-black uppercase tracking-widest text-zinc-400">Konfirmasi</label>
                        <div class="relative group">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-zinc-900 transition-colors">
                                <i class="fas fa-check-double text-xs"></i>
                            </span>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="block w-full rounded-xl border border-zinc-300 pl-10 pr-3 py-3 text-sm transition-all focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 placeholder:text-zinc-400 font-bold"
                                placeholder="••••••••" required>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full flex justify-center items-center gap-3 rounded-xl bg-zinc-900 px-4 py-4 text-xs font-black text-white hover:bg-black transition-all active:scale-[0.98] shadow-lg shadow-zinc-200 mt-4 uppercase tracking-[0.2em]">
                    <span>Daftar Sekarang</span>
                    <i class="fas fa-paper-plane text-[10px]"></i>
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-zinc-100 text-center">
                <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Sudah punya akun?</p>
                <a href="{{ route('login') }}"
                    class="inline-block mt-3 text-sm font-black text-zinc-900 hover:tracking-widest transition-all">MASUK
                    KEMBALI</a>
            </div>
        </div>
    </div>

    <!-- Back to Public -->
    <a href="/"
        class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-zinc-500 hover:text-zinc-900 transition-colors group">
        <i class="fas fa-arrow-left text-[10px] group-hover:-translate-x-1 transition-transform"></i>
        <span>Halaman Utama</span>
    </a>

    <p class="text-center text-[10px] font-black text-zinc-300 uppercase tracking-[0.3em] mt-12 mb-4">
        &copy; {{ date('Y') }} LabRPL TEKNIK INFORMATIKA ITATS
    </p>
</body>

</html>

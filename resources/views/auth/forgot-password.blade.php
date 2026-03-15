<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - LabRPL ITATS</title>
    <!-- Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-zinc-50 font-sans text-zinc-900 antialiased min-h-screen flex flex-col items-center justify-center p-4">

    <!-- Header Section -->
    <div class="text-center mb-8">
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('image/logo-RPL.jpg') }}" class="h-24 w-auto object-contain" alt="Logo">
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Lupa Password?</h1>
        <p class="text-sm text-zinc-500 mt-2 max-w-sm mx-auto">Masukkan email yang terdaftar untuk menerima link reset
            password.</p>
    </div>

    <!-- Forgot Password Card -->
    <div class="w-full max-w-md bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="p-8">
            <div class="flex items-center justify-center gap-3 mb-8">
                <div
                    class="h-10 w-10 rounded-xl bg-[#001f3f] flex items-center justify-center text-white shadow-lg shadow-[#001f3f]/20">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <h2 class="text-lg font-bold text-zinc-900 tracking-tight text-center">Reset Password</h2>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-600 font-medium flex gap-3">
                    <i class="fas fa-check-circle mt-0.5"></i>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                @csrf

                <div class="space-y-2">
                    <label for="email" class="block text-sm font-bold text-zinc-700 uppercase tracking-tight">Alamat
                        Email</label>
                    <div class="relative group">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-[#001f3f] transition-colors">
                            <i class="fas fa-at text-sm"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="block w-full rounded-lg border border-zinc-300 pl-10 pr-3 py-2.5 text-sm transition-all focus:border-[#001f3f] focus:outline-none focus:ring-1 focus:ring-[#001f3f] placeholder:text-zinc-400 font-medium bg-zinc-50/30 focus:bg-white"
                            placeholder="nama@email.com" required autofocus>
                    </div>
                    @error('email')
                        <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full flex justify-center items-center gap-2 rounded-lg bg-[#001f3f] px-4 py-3 text-sm font-bold text-white hover:bg-[#002d5a] transition-all active:scale-[0.98] shadow-lg shadow-[#001f3f]/10 mt-6 tracking-wide">
                    <span>Kirim Link Reset</span>
                    <i class="fas fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>
        
        <!-- Action Tip Bottom -->
        <div class="px-8 py-5 bg-zinc-50 border-t border-zinc-100 text-center">
            <a href="{{ route('login.praktikan') }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-zinc-500 hover:text-zinc-900 transition-colors group">
                <i class="fas fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                <span>Kembali ke Halaman Login</span>
            </a>
        </div>
    </div>

    <p class="text-center text-[11px] font-bold text-zinc-300 uppercase tracking-[0.2em] mt-12 mb-4">
        &copy; {{ date('Y') }} LabRPL TEKNIK INFORMATIKA ITATS
    </p>

</body>

</html>

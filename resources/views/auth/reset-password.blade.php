<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - LabRPL ITATS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-zinc-50 font-sans text-zinc-900 antialiased min-h-screen flex flex-col items-center justify-center p-4">

    <!-- Header Section -->
    <div class="text-center mb-8">
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('image/logo-RPL.jpg') }}" class="h-24 w-auto object-contain" alt="Logo">
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Buat Password Baru</h1>
        <p class="text-sm text-zinc-500 mt-2 max-w-sm mx-auto">Silakan buat password kuat yang berisi kombinasi angka dan huruf.</p>
    </div>

    <!-- Reset Password Card -->
    <div class="w-full max-w-md bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="p-8">
            <div class="flex items-center justify-center gap-3 mb-8">
                <div
                    class="h-10 w-10 rounded-xl bg-[#001f3f] flex items-center justify-center text-white shadow-lg shadow-[#001f3f]/20">
                    <i class="fas fa-key"></i>
                </div>
                <h2 class="text-lg font-bold text-zinc-900 tracking-tight text-center">Ganti Password</h2>
            </div>

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="space-y-2">
                    <label for="email" class="block text-sm font-bold text-zinc-700 uppercase tracking-tight">Alamat
                        Email</label>
                    <div class="relative group">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-[#001f3f] transition-colors">
                            <i class="fas fa-at text-sm"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ $email ?? old('email') }}" readonly
                            class="block w-full rounded-lg border border-zinc-300 pl-10 pr-3 py-2.5 text-sm transition-all focus:border-[#001f3f] focus:outline-none focus:ring-1 focus:ring-[#001f3f] placeholder:text-zinc-400 font-medium bg-zinc-100 cursor-not-allowed">
                    </div>
                    @error('email')
                        <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password"
                        class="block text-sm font-bold text-zinc-700 uppercase tracking-tight">Password Baru</label>
                    <div class="relative group">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-[#001f3f] transition-colors">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password" id="password"
                            class="block w-full rounded-lg border border-zinc-300 pl-10 pr-10 py-2.5 text-sm transition-all focus:border-[#001f3f] focus:outline-none focus:ring-1 focus:ring-[#001f3f] placeholder:text-zinc-400 font-medium bg-zinc-50/30 focus:bg-white"
                            placeholder="••••••••" required autofocus>
                        <button type="button" onclick="togglePassword('password', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-[#001f3f] transition-colors">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation"
                        class="block text-sm font-bold text-zinc-700 uppercase tracking-tight">Konfirmasi Password</label>
                    <div class="relative group">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-[#001f3f] transition-colors">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="block w-full rounded-lg border border-zinc-300 pl-10 pr-10 py-2.5 text-sm transition-all focus:border-[#001f3f] focus:outline-none focus:ring-1 focus:ring-[#001f3f] placeholder:text-zinc-400 font-medium bg-zinc-50/30 focus:bg-white"
                            placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword('password_confirmation', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-[#001f3f] transition-colors">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full flex justify-center items-center gap-2 rounded-lg bg-[#001f3f] px-4 py-3 text-sm font-bold text-white hover:bg-[#002d5a] transition-all active:scale-[0.98] shadow-lg shadow-[#001f3f]/10 mt-6 tracking-wide">
                    <span>Simpan Password Baru</span>
                    <i class="fas fa-save text-xs"></i>
                </button>
            </form>
        </div>
        
    </div>

    <p class="text-center text-[11px] font-bold text-zinc-300 uppercase tracking-[0.2em] mt-12 mb-4">
        &copy; {{ date('Y') }} LabRPL TEKNIK INFORMATIKA ITATS
    </p>

</body>

<script>
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>

</html>

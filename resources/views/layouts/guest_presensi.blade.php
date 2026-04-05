<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Presensi') - Lab RPL ITATS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50/50 min-h-screen antialiased text-slate-800">
    <!-- Simple Header -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-center items-center h-16 sm:h-20">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('image/rplmini.png') }}" alt="LabRPL Logo" class="w-8 h-8 sm:w-10 sm:h-10 object-contain">
                    <div class="flex flex-col">
                        <span class="font-bold text-base sm:text-lg leading-tight text-slate-900 tracking-tight text-center sm:text-left">LAB RPL ITATS</span>
                        <span class="text-[10px] sm:text-xs font-medium text-slate-500 uppercase tracking-widest">Attendance System</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="py-8 text-center bg-white border-t border-slate-100">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">© {{ date('Y') }} Laboratorium Rekayasa Perangkat Lunak - ITATS</p>
    </footer>

    @if (session('success'))
        <script>
            Swal.fire({ icon: 'success', title: 'Berhasil', text: '{{ session('success') }}', confirmButtonColor: '#001f3f' });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({ icon: 'error', title: 'Gagal', text: '{{ session('error') }}', confirmButtonColor: '#001f3f' });
        </script>
    @endif

    @stack('scripts')
</body>
</html>

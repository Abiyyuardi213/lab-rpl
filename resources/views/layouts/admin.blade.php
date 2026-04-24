<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Lab RPL ITATS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('image/logo-RPL.jpg') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('image/logo-RPL.jpg') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <!-- PWA & Apple Mobile Web Support -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#001f3f">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('image/logo-RPL.jpg') }}">
    <link rel="apple-touch-startup-image" href="{{ asset('image/logo-RPL.jpg') }}">

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
    @stack('styles')
</head>

<body class="bg-gray-50/50 min-h-screen font-sans antialiased text-slate-800 flex flex-col">

    <!-- Floating Navbar -->
    @include('admin.components.admin-navbar')

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-20 sm:py-24 flex-grow w-full">
        @yield('content')
    </main>

    <div class="mt-auto">
        @include('admin.components.admin-footer')
    </div>

    <!-- Floating Return to Admin Button (Impersonate Mode) -->
    @if(session()->has('impersonated_by'))
        <div class="fixed bottom-6 right-6 z-[100] animate-bounce hover:animate-none">
            <form action="{{ route('impersonation.leave') }}" method="POST">
                @csrf
                <button type="submit" 
                    class="group flex items-center gap-3 bg-[#001f3f] hover:bg-rose-600 text-white px-5 py-3.5 rounded-full shadow-2xl shadow-blue-900/30 transition-all duration-300 hover:scale-105 hover:pr-6 border-2 border-white">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20 group-hover:bg-white text-white group-hover:text-rose-600 transition-colors">
                        <i class="fas fa-sign-out-alt text-sm"></i>
                    </div>
                    <span class="font-bold tracking-wide text-sm whitespace-nowrap">Kembali ke Admin</span>
                </button>
            </form>
        </div>
    @endif

    @if (session('login_success') || session('success'))
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
                title: '{{ session('login_success') ?? session('success') }}'
            })
        </script>
    @endif

    @if (session('error') || $errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: '{{ session('error') ?? $errors->first() }}',
                confirmButtonColor: '#001f3f'
            });
        </script>
    @endif

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then((registration) => {
                    console.log('ServiceWorker registered with scope:', registration.scope);
                }).catch((error) => {
                    console.error('ServiceWorker registration failed:', error);
                });
            });
        }
    </script>
    @stack('scripts')
</body>

</html>

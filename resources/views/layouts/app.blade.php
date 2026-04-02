<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lab RPL ITATS — Pusat Riset & Praktikum Teknik Informatika')</title>
    <meta name="description"
        content="@yield('meta_description', 'Sistem Informasi Laboratorium Rekayasa Perangkat Lunak (Lab RPL) ITATS. Pusat riset, pengembangan, dan praktikum mahasiswa Teknik Informatika ITATS.')">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Lab RPL ITATS — Pusat Riset & Praktikum Teknik Informatika')">
    <meta property="og:description"
        content="@yield('meta_description', 'Sistem Informasi Laboratorium Rekayasa Perangkat Lunak (Lab RPL) ITATS. Pusat riset, pengembangan, dan praktikum mahasiswa Teknik Informatika ITATS.')">
    <meta property="og:image" content="{{ asset('image/logo-RPL.jpg') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title"
        content="@yield('title', 'Lab RPL ITATS — Pusat Riset & Praktikum Teknik Informatika')">
    <meta property="twitter:description"
        content="@yield('meta_description', 'Sistem Informasi Laboratorium Rekayasa Perangkat Lunak (Lab RPL) ITATS. Pusat riset, pengembangan, dan praktikum mahasiswa Teknik Informatika ITATS.')">
    <meta property="twitter:image" content="{{ asset('image/logo-RPL.jpg') }}">

    @yield('meta')

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('image/logo-RPL.jpg') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('image/logo-RPL.jpg') }}" type="image/x-icon">

    <!-- PWA & Apple Mobile Web Support -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#001f3f">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Lab RPL">
    <link rel="apple-touch-icon" href="{{ asset('image/logo-RPL.jpg') }}">
    <link rel="apple-touch-startup-image" href="{{ asset('image/logo-RPL.jpg') }}">

    <script>
        // Safer fix for standalone mode links
        document.addEventListener('click', function (e) {
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
    <!-- Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom Utilities mimicking the Next.js globals.css variables */
        :root {
            --background: 0 0% 100%;
            --foreground: 222.2 84% 4.9%;
            --card: 0 0% 100%;
            --card-foreground: 222.2 84% 4.9%;
            --popover: 0 0% 100%;
            --popover-foreground: 222.2 84% 4.9%;
            --primary: 221.2 83.2% 53.3%;
            --primary-foreground: 210 40% 98%;
            --secondary: 210 40% 96.1%;
            --secondary-foreground: 222.2 47.4% 11.2%;
            --muted: 210 40% 96.1%;
            --muted-foreground: 215.4 16.3% 46.9%;
            --accent: 210 40% 96.1%;
            --accent-foreground: 222.2 47.4% 11.2%;
            --destructive: 0 84.2% 60.2%;
            --destructive-foreground: 210 40% 98%;
            --border: 214.3 31.8% 91.4%;
            --input: 214.3 31.8% 91.4%;
            --ring: 221.2 83.2% 53.3%;
            --radius: 0.5rem;
        }
    </style>
</head>

<body class="font-sans antialiased bg-background text-foreground">

    {{-- Skip content --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] bg-primary text-primary-foreground px-3 py-2 rounded-md">
        Loncat ke konten utama
    </a>

    {{-- Header --}}
    <header
        class="sticky top-0 z-50 border-b border-slate-200 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60">
        <div class="max-w-screen-2xl mx-auto px-6">
            <div class="flex h-24 items-center justify-between">
                {{-- Brand --}}
                <div class="flex items-center gap-3">
                    <a href="/" class="inline-flex items-center gap-2" aria-label="Beranda Lab RPL">
                        <img src="{{ asset('image/logo-RPL.png') }}" alt="Logo Lab RPL" width="48" height="48"
                            class="h-12 w-auto object-contain">
                        <div class="leading-tight">
                            <span class="block text-base font-semibold text-slate-900">Lab. Rekayasa Perangkat
                                Lunak</span>
                            <span class="block text-sm text-slate-500">Teknik Informatika ITATS</span>
                        </div>
                    </a>
                </div>

                {{-- Desktop Nav --}}
                <nav class="hidden md:flex items-center gap-1" aria-label="Navigasi utama">
                    @php
                        $navItems = [
                            ['label' => 'Beranda', 'href' => '/'],
                            ['label' => 'Tentang', 'href' => '/tentang'],
                            ['label' => 'Struktur Organisasi', 'href' => '/struktur-organisasi'],
                            ['label' => 'Praktikum', 'href' => '/praktikum'],
                            ['label' => 'Kegiatan', 'href' => '/kegiatan'],
                            ['label' => 'Pengumuman & Berita', 'href' => '/pengumuman'],
                        ];
                    @endphp

                    @foreach ($navItems as $item)
                        <a href="{{ $item['href'] }}"
                            class="group relative px-4 py-3 rounded-md text-sm font-semibold transition-colors duration-200 hover:text-slate-950 focus-visible:outline-none {{ ($item['href'] === '/' ? request()->path() === '/' : request()->is(trim($item['href'], '/') . '*')) ? 'text-slate-950' : 'text-slate-600' }}">
                            {{ $item['label'] }}
                            {{-- Line Animation: Expand from center --}}
                            <span aria-hidden="true"
                                class="absolute left-1/2 bottom-1 h-0.5 w-0 bg-[#1a4fa0] transition-all duration-300 origin-center -translate-x-1/2 group-hover:w-[70%] {{ ($item['href'] === '/' ? request()->path() === '/' : request()->is(trim($item['href'], '/') . '*')) ? 'w-[70%]' : '' }}"></span>
                        </a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-2">
                    <a href="{{ route('login.praktikan') }}"
                        class="hidden sm:inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-bold ring-offset-background transition-all hover:scale-105 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#1a4fa0] text-white hover:bg-[#1a4fa0]/90 h-10 px-6 py-2 shadow-sm shadow-blue-900/10">
                        Portal Login
                    </a>
                    <button type="button" id="mobile-menu-btn"
                        class="bg-[#1a4fa0] md:hidden inline-flex items-center justify-center rounded-md px-3 py-2 text-white hover:bg-[#1a4fa0]/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                        aria-label="Buka menu">
                        {{-- Icon Menu --}}
                        <svg id="icon-menu" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <line x1="4" x2="20" y1="12" y2="12" />
                            <line x1="4" x2="20" y1="6" y2="6" />
                            <line x1="4" x2="20" y1="18" y2="18" />
                        </svg>
                        {{-- Icon X (Hidden by default) --}}
                        <svg id="icon-close" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="hidden h-5 w-5">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Panel --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200 bg-white">
            <nav class="max-w-7xl mx-auto px-4 py-2" aria-label="Navigasi mobile">
                <ul class="flex flex-col py-2">
                    @foreach ($navItems as $item)
                        <li>
                            <a href="{{ $item['href'] }}"
                                class="block w-full px-3 py-2 rounded-md text-sm font-medium hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary {{ ($item['href'] === '/' ? request()->path() === '/' : request()->is(trim($item['href'], '/') . '*')) ? 'text-primary bg-slate-50' : 'text-slate-700' }}">
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                    <li class="mt-2">
                        <a href="{{ route('login.praktikan') }}"
                            class="w-full inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-bold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#1a4fa0] text-white hover:bg-[#1a4fa0]/90 h-10 px-4 py-2">
                            Masuk Portal
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    {{-- Main Content --}}
    <main id="main-content" class="min-h-[60vh]">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 py-10">
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {{-- Logo Lab RPL --}}
                    <div class="mb-4">
                        <img src="{{ asset('image/logo-RPL.jpg') }}" alt="Logo Lab RPL ITATS" width="80" height="80"
                            class="rounded-lg">
                    </div>

                    <h3 class="text-sm font-semibold mb-3">Lab. Rekayasa Perangkat Lunak</h3>
                    <p class="text-sm text-slate-500">
                        Pusat riset dan pembelajaran Rekayasa Perangkat Lunak - Institut Teknologi Adhi Tama Surabaya.
                        Menciptakan inovasi untuk masa depan.
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold mb-3">Informasi</h3>
                    <ul class="space-y-2">
                        <li><a href="/praktikum" class="text-sm hover:underline">Info Praktikum</a>
                        </li>
                        <li><a href="/pengumuman" class="text-sm hover:underline">Pengumuman</a></li>
                        <li><a href="/kegiatan" class="text-sm hover:underline">Kegiatan Lab</a></li>
                        <li><a href="/tentang" class="text-sm hover:underline">Tentang Kami</a></li>
                    </ul>
                </div>
                <div id="kontak">
                    <h3 class="text-sm font-semibold mb-3">Kontak Lab</h3>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li>Email: lab.basprog@itats.ac.id</li>
                        <li>Alamat: Lab G2 LCSE, ITATS. Surabaya.</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold mb-3">Ikuti Kami</h3>
                    <div class="flex items-center gap-3">
                        <a href="https://www.instagram.com/hmif_itats/" target="_blank" aria-label="Instagram HMIF"
                            class="hover:underline text-sm">Instagram</a>
                        <a href="#" aria-label="LinkedIn HMIF" class="hover:underline text-sm">LinkedIn</a>
                        <a href="#" aria-label="YouTube HMIF" class="hover:underline text-sm">YouTube</a>
                    </div>
                </div>
            </div>

            <div
                class="mt-8 border-t border-slate-200 pt-6 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p>&copy; {{ date('Y') }} Lab. Rekayasa Perangkat Lunak ITATS.</p>
                <p>Laboratorium Teknik Informatika ITATS.</p>
            </div>
        </div>
    </footer>

    {{-- Script for Mobile Menu --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const iconMenu = document.getElementById('icon-menu');
            const iconClose = document.getElementById('icon-close');
            let isOpen = false;

            btn.addEventListener('click', () => {
                isOpen = !isOpen;
                if (isOpen) {
                    menu.classList.remove('hidden');
                    iconMenu.classList.add('hidden');
                    iconClose.classList.remove('hidden');
                } else {
                    menu.classList.add('hidden');
                    iconMenu.classList.remove('hidden');
                    iconClose.classList.add('hidden');
                }
            });

        });
    </script>

    {{-- Notification Handler --}}
    @if (session('logout_success'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: "{{ session('logout_success') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-emerald-100',
                    title: 'text-sm font-bold text-slate-800'
                }
            });
        </script>
    @endif

    @yield('scripts')
</body>

</html>
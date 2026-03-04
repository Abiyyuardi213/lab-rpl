<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMIF ITATS — Himpunan Mahasiswa Informatika</title>
    <meta name="description"
        content="Website resmi Himpunan Mahasiswa Informatika ITATS. Informasi struktur organisasi, divisi, program kerja, kegiatan, dan pengumuman/berita.">

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
                    <a href="/" class="inline-flex items-center gap-2" aria-label="Beranda HMIF">
                        <img src="{{ asset('image/hima-infor.png') }}" alt="Logo HMIF" width="48" height="48"
                            class="h-12 w-18">
                        <div class="leading-tight">
                            <span class="block text-base font-semibold text-slate-900">Himpunan Mahasiswa</span>
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
                            ['label' => 'Program Kerja', 'href' => '/program-kerja'],
                            ['label' => 'Kegiatan', 'href' => '/kegiatan'],
                            ['label' => 'Pengumuman & Berita', 'href' => '/pengumuman'],
                            ['label' => 'Merchandise', 'href' => '/merchandise'],
                        ];
                    @endphp

                    @foreach ($navItems as $item)
                        <a href="{{ $item['href'] }}"
                            class="group relative px-4 py-3 rounded-md text-sm font-medium hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary {{ ($item['href'] === '/' ? request()->path() === '/' : request()->is(trim($item['href'], '/') . '*')) ? 'text-primary' : 'text-slate-700' }}">
                            {{ $item['label'] }}
                            <span aria-hidden="true"
                                class="pointer-events-none absolute inset-x-3 -bottom-0.5 h-0.5 scale-x-0 bg-primary transition-transform duration-200 group-hover:scale-x-100 group-focus-visible:scale-x-100 {{ ($item['href'] === '/' ? request()->path() === '/' : request()->is(trim($item['href'], '/') . '*')) ? 'scale-x-100' : '' }}"></span>
                        </a>
                    @endforeach
                </nav>

                {{-- CTA + Mobile toggle --}}
                <div class="flex items-center gap-2 b-primary">
                    <a href="https://www.instagram.com/hmif_itats/" target="_blank"
                        class="hidden sm:inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-white hover:bg-primary/90 h-10 px-4 py-2 shadow-sm">
                        Kontak HMIF
                    </a>
                    <button type="button" id="mobile-menu-btn"
                        class="bg-primary md:hidden inline-flex items-center justify-center rounded-md px-3 py-2 text-white hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                        aria-label="Buka menu">
                        {{-- Icon Menu --}}
                        <svg id="icon-menu" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="h-5 w-5">
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
                        <a href="https://www.instagram.com/hmif_itats/" target="_blank"
                            class="w-full inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-white hover:bg-primary/90 h-10 px-4 py-2">
                            Hubungi Kami
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
                    {{-- Logo HMIF --}}
                    <div class="mb-4">
                        <img src="{{ asset('image/icon-hmif.png') }}" alt="Logo HMIF ITATS" width="80"
                            height="80" class="rounded-full">
                    </div>

                    <h3 class="text-sm font-semibold mb-3">Tentang HMIF ITATS</h3>
                    <p class="text-sm text-slate-500">
                        Wadah kolaborasi dan pengembangan mahasiswa Informatika. Berkarya, berdampak, dan bertumbuh
                        bersama.
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold mb-3">Navigasi</h3>
                    <ul class="space-y-2">
                        <li><a href="/struktur-organisasi" class="text-sm hover:underline">Struktur Organisasi</a></li>
                        <li><a href="/program-kerja" class="text-sm hover:underline">Program Kerja</a></li>
                        <li><a href="/kegiatan" class="text-sm hover:underline">Kegiatan</a></li>
                        <li><a href="/pengumuman" class="text-sm hover:underline">Pengumuman & Berita</a></li>
                        <li><a href="/merchandise" class="text-sm hover:underline">Merchandise</a></li>
                    </ul>
                </div>
                <div id="kontak">
                    <h3 class="text-sm font-semibold mb-3">Kontak</h3>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li>Email: hmifitats1991@gmail.com</li>
                        <li>Alamat: Jl. Arief Rahman Hakim No.100, Klampis Ngasem, Kec. Sukolilo, Surabaya, Jawa Timur
                            60117</li>
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
                <p>&copy; {{ date('Y') }} HMIF. Semua hak dilindungi.</p>
                <p>UI berbasis design tokens: primary biru, accent amber, netral yang bersih.</p>
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
</body>

</html>

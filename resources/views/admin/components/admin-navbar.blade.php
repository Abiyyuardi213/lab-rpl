<nav class="fixed w-full z-50 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-slate-100">
    <div class="max-w-[1600px] mx-auto px-3 sm:px-6 lg:px-12">
        <div class="flex justify-between items-center h-16 sm:h-20">
            <!-- Left: Logo -->
            <div class="flex-1 flex items-center">
                @php
                    $logoUrl = url('/admin/dashboard');
                    if (Auth::check() && Auth::user()->role) {
                        if (Auth::user()->role->name === 'Praktikan') {
                            $logoUrl = route('praktikan.dashboard');
                        } elseif (Auth::user()->role->name === 'Aslab') {
                            $logoUrl = route('aslab.dashboard');
                        }
                    }
                @endphp
                <a href="{{ $logoUrl }}" class="flex items-center gap-2 sm:gap-3">
                    <img src="{{ asset('image/rplmini.png') }}" alt="LabRPL Logo"
                        class="w-7 h-7 sm:w-9 sm:h-9 object-contain">
                    <div class="flex flex-col">
                        <span class="font-bold text-base sm:text-lg leading-tight text-slate-900 tracking-tight">Lab
                            RPL</span>
                        <span class="text-[10px] sm:text-xs font-medium text-slate-500 uppercase tracking-widest">
                            @if (Auth::user()->role && Auth::user()->role->name === 'Praktikan')
                                Praktikan
                            @elseif(Auth::user()->role && Auth::user()->role->name === 'Aslab')
                                Lab Assistant
                            @elseif(Auth::user()->role && Auth::user()->role->name === 'Super Admin')
                                Super Admin
                            @else
                                Admin Panel
                            @endif
                        </span>
                    </div>
                </a>
            </div>

            <!-- Middle: Nav Links -->
            <div class="hidden lg:flex items-center space-x-6 flex-shrink-0">
                @if (Auth::user()->role && Auth::user()->role->name === 'Praktikan')
                    <a href="{{ route('praktikan.dashboard') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('praktikan/dashboard') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Dashboard
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('praktikan/dashboard') ? 'w-full' : '' }}"></span>
                    </a>

                    <a href="{{ route('praktikan.pendaftaran.index') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('praktikan/riwayat-pendaftaran*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Riwayat Daftar
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('praktikan/riwayat-pendaftaran*') ? 'w-full' : '' }}"></span>
                    </a>

                    <a href="{{ route('praktikan.penugasan.index') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('praktikan/penugasan*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Soal Praktikum
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('praktikan/penugasan*') ? 'w-full' : '' }}"></span>
                    </a>

                    <a href="{{ route('praktikan.recruitment.index') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('praktikan/recruitment*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Rekrutmen Aslab
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('praktikan/recruitment*') ? 'w-full' : '' }}"></span>
                    </a>
                @elseif (Auth::user()->role && Auth::user()->role->name === 'Aslab')
                    <a href="{{ route('aslab.dashboard') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('aslab/dashboard') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Dashboard
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('aslab/dashboard') ? 'w-full' : '' }}"></span>
                    </a>

                    <div class="h-4 w-px bg-slate-200"></div>

                    <!-- Akademik Dropdown -->
                    <div class="relative group">
                        <button
                            class="relative flex items-center gap-1.5 text-sm font-semibold transition-colors hover:text-[#001f3f] py-4 -my-4 {{ request()->is('aslab/pendaftaran*') || request()->is('aslab/tugas*') || request()->is('aslab/penugasan*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                            Akademik
                            <i
                                class="fas fa-chevron-down text-[10px] transition-transform duration-300 group-hover:rotate-180"></i>
                            <span
                                class="absolute bottom-3 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('aslab/pendaftaran*') || request()->is('aslab/tugas*') || request()->is('aslab/penugasan*') ? 'w-full' : '' }}"></span>
                        </button>
                        <div
                            class="absolute left-0 top-full mt-1 w-48 bg-white border border-slate-100 rounded-xl shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                            <a href="{{ route('aslab.pendaftaran.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('aslab/pendaftaran*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Daftar
                                Bimbingan</a>
                            <a href="{{ route('aslab.tugas.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('aslab/tugas*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Tugas
                                Asistensi</a>
                            <a href="{{ route('aslab.penugasan.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('aslab/penugasan*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Penugasan
                                Sesi</a>
                        </div>
                    </div>

                    <!-- Kegiatan Dropdown -->
                    <div class="relative group">
                        <button
                            class="relative flex items-center gap-1.5 text-sm font-semibold transition-colors hover:text-[#001f3f] py-4 -my-4 {{ request()->is('aslab/penilaian*') || request()->is('aslab/presensi*') || request()->is('aslab/ratings*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                            Kegiatan
                            <i
                                class="fas fa-chevron-down text-[10px] transition-transform duration-300 group-hover:rotate-180"></i>
                            <span
                                class="absolute bottom-3 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('aslab/penilaian*') || request()->is('aslab/presensi*') || request()->is('aslab/ratings*') ? 'w-full' : '' }}"></span>
                        </button>
                        <div
                            class="absolute left-0 top-full mt-1 w-48 bg-white border border-slate-100 rounded-xl shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                            <a href="{{ route('aslab.penilaian.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('aslab/penilaian*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Penilaian
                                Live</a>
                            <a href="{{ route('aslab.presensi.scan') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('aslab/presensi*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Scanner
                                Presensi</a>
                            <a href="{{ route('aslab.ratings.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('aslab/ratings*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Rating Praktikan</a>
                        </div>
                    </div>

                    <div class="h-4 w-px bg-slate-200"></div>

                    <!-- Portfolio -->
                    <a href="{{ route('aslab.portfolio.edit') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->routeIs('aslab.portfolio.*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Portfolio Saya
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->routeIs('aslab.portfolio.*') ? 'w-full' : '' }}"></span>
                    </a>
                @else
                    <a href="{{ url('/admin/dashboard') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('admin/dashboard') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Dashboard
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('admin/dashboard') ? 'w-full' : '' }}"></span>
                    </a>

                    <div class="h-4 w-px bg-slate-200"></div>

                    <!-- Manajemen Pengguna Dropdown -->
                    <div class="relative group">
                        <button
                            class="relative flex items-center gap-1.5 text-sm font-semibold transition-colors hover:text-[#001f3f] py-4 -my-4 {{ request()->is('admin/aslab*') || request()->is('admin/praktikan*') || request()->is('admin/user*') || request()->is('admin/role*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                            Data Pengguna
                            <i
                                class="fas fa-chevron-down text-[10px] transition-transform duration-300 group-hover:rotate-180"></i>
                            <span
                                class="absolute bottom-3 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('admin/aslab*') || request()->is('admin/praktikan*') || request()->is('admin/user*') || request()->is('admin/role*') ? 'w-full' : '' }}"></span>
                        </button>
                        <div
                            class="absolute left-0 top-full mt-1 w-48 bg-white border border-slate-100 rounded-xl shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                            @if (Auth::user()->role->name === 'Super Admin')
                                <a href="{{ route('admin.role.index') }}"
                                    class="block px-4 py-2 text-sm {{ request()->is('admin/role*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Manajemen
                                    Role</a>
                                <a href="{{ route('admin.user.index') }}"
                                    class="block px-4 py-2 text-sm {{ request()->is('admin/user*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Manajemen
                                    User</a>
                            @endif
                            <a href="{{ route('admin.aslab.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/aslab*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Data
                                Asisten Lab</a>
                            <a href="{{ route('admin.praktikan.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/praktikan*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Data
                                Praktikan</a>
                        </div>
                    </div>

                    <!-- Akademik Dropdown -->
                    <div class="relative group">
                        <button
                            class="relative flex items-center gap-1.5 text-sm font-semibold transition-colors hover:text-[#001f3f] py-4 -my-4 {{ request()->is('admin/praktikum*') || request()->is('admin/jadwal-praktikum*') || request()->is('admin/pendaftaran*') || request()->is('admin/presensi*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                            Akademik
                            <i
                                class="fas fa-chevron-down text-[10px] transition-transform duration-300 group-hover:rotate-180"></i>
                            <span
                                class="absolute bottom-3 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('admin/praktikum*') || request()->is('admin/jadwal-praktikum*') || request()->is('admin/pendaftaran*') || request()->is('admin/presensi*') ? 'w-full' : '' }}"></span>
                        </button>
                        <div
                            class="absolute left-0 top-full mt-1 w-48 bg-white border border-slate-100 rounded-xl shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                            <a href="{{ route('admin.praktikum.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/praktikum*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Mata
                                Praktikum</a>
                            <a href="{{ route('admin.jadwal-praktikum.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/jadwal-praktikum*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Jadwal
                                & Sesi</a>
                            <a href="{{ route('admin.pendaftaran.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/pendaftaran*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Pendaftaran</a>
                            <a href="{{ route('admin.presensi.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/presensi*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Presensi</a>
                            <a href="{{ route('admin.penilaian.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/penilaian*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Penilaian</a>
                        </div>
                    </div>

                    <!-- Kegiatan & Tugas Dropdown -->
                    <div class="relative group">
                        <button
                            class="relative flex items-center gap-1.5 text-sm font-semibold transition-colors hover:text-[#001f3f] py-4 -my-4 {{ request()->is('admin/penugasan*') || request()->is('admin/kegiatan*') || request()->is('admin/digit-npm*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                            Kegiatan
                            <i
                                class="fas fa-chevron-down text-[10px] transition-transform duration-300 group-hover:rotate-180"></i>
                            <span
                                class="absolute bottom-3 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('admin/penugasan*') || request()->is('admin/kegiatan*') || request()->is('admin/digit-npm*') ? 'w-full' : '' }}"></span>
                        </button>
                        <div
                            class="absolute left-0 top-full mt-1 w-48 bg-white border border-slate-100 rounded-xl shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                            <a href="{{ route('admin.kegiatan.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/kegiatan*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Daftar
                                Kegiatan</a>
                            <a href="{{ route('admin.penugasan.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/penugasan*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Penugasan</a>
                            <a href="{{ route('admin.digit-npm.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/digit-npm*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Manage
                                NPM</a>
                            <a href="{{ route('admin.recruitment.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/recruitment*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Rekrutmen
                                Aslab</a>
                        </div>
                    </div>

                    <!-- Informasi Dropdown -->
                    <div class="relative group">
                        <button
                            class="relative flex items-center gap-1.5 text-sm font-semibold transition-colors hover:text-[#001f3f] py-4 -my-4 {{ request()->is('admin/pengumuman*') || request()->is('admin/notifications*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                            Informasi
                            <i
                                class="fas fa-chevron-down text-[10px] transition-transform duration-300 group-hover:rotate-180"></i>
                            <span
                                class="absolute bottom-3 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('admin/pengumuman*') || request()->is('admin/notifications*') ? 'w-full' : '' }}"></span>
                        </button>
                        <div
                            class="absolute right-0 top-full mt-1 w-48 bg-white border border-slate-100 rounded-xl shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                            <a href="{{ route('admin.logs.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/logs*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Log Aktivitas</a>
                            <a href="{{ route('admin.ratings.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/ratings*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Rating Praktikan</a>
                            <a href="{{ route('admin.pengumuman.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/pengumuman*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Pengumuman</a>
                            <a href="{{ route('admin.notifications.create') }}"
                                class="block px-4 py-2 text-sm {{ request()->is('admin/notifications*') ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#001f3f]' }}">Kirim
                                Notifikasi</a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right: Profile & Action -->
            <div class="flex-1 flex items-center justify-end gap-2 sm:gap-4">
                {{-- Notification Bell --}}
                <div class="relative group">
                    <button
                        class="relative p-2 text-slate-400 hover:text-[#001f3f] transition-colors focus:outline-none"
                        id="notification-button">
                        <i class="fas fa-bell text-lg"></i>
                        @if (Auth::user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1 right-1 flex h-2.5 w-2.5">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                            </span>
                        @endif
                    </button>

                    {{-- Dropdown Notification --}}
                    <div class="absolute right-0 mt-2 w-[320px] bg-white rounded-xl shadow-xl border border-slate-100 hidden group-hover:block z-50 animate-in fade-in slide-in-from-top-2"
                        id="notification-dropdown">
                        <div
                            class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-xl">
                            <h3 class="text-xs font-black text-slate-900 tracking-wider uppercase">Notifikasi</h3>
                            @if (Auth::user()->unreadNotifications->count() > 0)
                                <a href="{{ route('notifications.markAllAsRead') }}"
                                    class="text-[10px] font-bold text-[#001f3f] hover:underline">Tandai semua dibaca</a>
                            @endif
                        </div>
                        <div class="max-h-80 overflow-y-auto w-full scrollbar-thin scrollbar-thumb-slate-200">
                            @forelse(Auth::user()->unreadNotifications->take(5) as $notification)
                                <div
                                    class="px-4 py-3 border-b border-slate-50 hover:bg-slate-50 transition w-full group/notif relative">
                                    <h4 class="text-sm font-bold text-slate-900 pr-6">
                                        {{ $notification->data['title'] ?? 'Info' }}</h4>
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">
                                        {{ $notification->data['message'] ?? '' }}</p>
                                    <span
                                        class="text-[10px] text-slate-400 mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
                                    <a href="{{ route('notifications.markAsRead', $notification->id) }}"
                                        class="absolute right-3 top-3 text-slate-300 hover:text-[#001f3f] opacity-0 group-hover/notif:opacity-100 transition"
                                        title="Tandai dibaca">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-slate-400">
                                    <i class="far fa-bell-slash text-2xl mb-2 opacity-50"></i>
                                    <p class="text-xs font-medium">Belum ada notifikasi baru</p>
                                </div>
                            @endforelse
                        </div>
                        @if (Auth::user()->notifications->count() > 0)
                            <div class="px-4 py-2 border-t border-slate-100 text-center bg-slate-50/50 rounded-b-xl">
                                <a href="{{ route('notifications.index') }}"
                                    class="text-[10px] font-bold text-slate-500 hover:text-[#001f3f]">Lihat semua
                                    notifikasi ({{ Auth::user()->unreadNotifications->count() }})</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="hidden md:flex flex-col items-end mr-2">
                    <span class="text-xs font-bold text-slate-900 leading-none">{{ Auth::user()->name }}</span>
                    <span
                        class="text-[10px] font-medium text-slate-400 uppercase tracking-tighter">{{ Auth::user()->role ? Auth::user()->role->display_name : 'No Role' }}</span>
                </div>

                <div class="relative group">
                    <button id="profile-dropdown-button"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border border-slate-200 bg-slate-100 flex items-center justify-center overflow-hidden focus:outline-none focus:ring-2 focus:ring-slate-200 focus:ring-offset-2 transition-all">
                        @if (Auth::user()->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                                class="w-full h-full object-cover flex-shrink-0" alt="Profile Photo"
                                onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=f8fafc&color=0f172a&bold=true';">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=f8fafc&color=0f172a&bold=true"
                                class="w-full h-full object-cover flex-shrink-0" alt="Avatar">
                        @endif
                    </button>
                    <!-- Dropdown -->
                    <div id="profile-dropdown-menu"
                        class="absolute top-full right-0 pt-3 w-56 hidden group-hover:block lg:group-hover:block transition-all animate-in fade-in slide-in-from-top-2">
                        <div class="bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden">
                            <div class="px-4 py-3 mb-2 rounded-xl bg-slate-50/50">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Signed
                                    in as</p>
                                <p class="text-sm font-black text-slate-900 truncate tracking-tight lowercase">
                                    {{ Auth::user()->email }}</p>
                            </div>

                            <div class="space-y-1">
                                @php
                                    $dashboardUrl = url('/admin/dashboard');
                                    $profileEditRoute = 'admin.profile.edit';
                                    if (Auth::user()->role) {
                                        if (Auth::user()->role->name === 'Praktikan') {
                                            $dashboardUrl = route('praktikan.dashboard');
                                            $profileEditRoute = 'praktikan.profile.edit';
                                        } elseif (Auth::user()->role->name === 'Aslab') {
                                            $dashboardUrl = route('aslab.dashboard');
                                            $profileEditRoute = 'aslab.profile.edit';
                                        }
                                    }
                                @endphp
                                <a href="{{ $dashboardUrl }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-zinc-500 rounded-xl hover:bg-zinc-50 hover:text-zinc-900 transition-all uppercase tracking-widest">
                                    <i class="fas fa-tachometer-alt w-4"></i> Dashboard
                                </a>
                                <a href="{{ route($profileEditRoute) }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-zinc-500 rounded-xl hover:bg-zinc-50 hover:text-zinc-900 transition-all uppercase tracking-widest">
                                    <i class="fas fa-user-circle w-4"></i> Pengaturan Profil
                                </a>
                                <button type="button" onclick="confirmLogout()"
                                    class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-rose-600 rounded-xl hover:bg-rose-50 transition-all uppercase tracking-widest">
                                    <i class="fas fa-sign-out-alt w-4"></i> Logout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-6 w-px bg-slate-200 mx-2 hidden lg:block"></div>

                <button onclick="confirmLogout()"
                    class="hidden lg:flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/10">
                    <i class="fas fa-power-off"></i>
                    <span class="hidden xl:inline">Keluar</span>
                </button>

                <!-- Mobile Trigger -->
                <button id="admin-mobile-button" class="lg:hidden text-slate-600 p-2">
                    <i class="fas fa-bars text-lg sm:text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <!-- Mobile Menu -->
    <div id="admin-mobile-menu" class="hidden lg:hidden bg-white border-t border-slate-100 shadow-2xl">
        <div class="px-3 py-3 sm:p-4 space-y-1 max-h-[calc(100vh-4rem)] overflow-y-auto">
            @if (Auth::user()->role && Auth::user()->role->name === 'Praktikan')
                <a href="{{ route('praktikan.dashboard') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('praktikan/dashboard') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Dashboard</a>
                <a href="{{ route('praktikan.pendaftaran.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('praktikan/riwayat-pendaftaran*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Riwayat
                    Pendaftaran</a>
                <a href="{{ route('praktikan.penugasan.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('praktikan/penugasan*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Soal
                    Praktikum</a>
                <a href="{{ route('praktikan.recruitment.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('praktikan/recruitment*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Rekrutmen
                    Aslab</a>
            @elseif(Auth::user()->role && Auth::user()->role->name === 'Aslab')
                <a href="{{ route('aslab.dashboard') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('aslab/dashboard') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Dashboard</a>
                <a href="{{ route('aslab.pendaftaran.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('aslab/pendaftaran*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Daftar
                    Bimbingan</a>
                <a href="{{ route('aslab.tugas.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('aslab/tugas*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Tugas
                    Asistensi</a>
                <a href="{{ route('aslab.penugasan.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('aslab/penugasan*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Penugasan
                    Sesi</a>
                <a href="{{ route('aslab.penilaian.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('aslab/penilaian*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Penilaian
                    Live</a>
                <a href="{{ route('aslab.presensi.scan') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('aslab/presensi*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Scanner
                    Presensi</a>
                <a href="{{ route('aslab.ratings.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('aslab/ratings*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Rating Praktikan</a>
                <a href="{{ route('aslab.portfolio.edit') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->routeIs('aslab.portfolio.*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Portfolio Saya</a>
            @else
                <a href="{{ url('/admin/dashboard') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/dashboard') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Dashboard</a>
                @if (Auth::user()->role->name === 'Super Admin')
                    <a href="{{ route('admin.role.index') }}"
                        class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/role*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manajemen
                        Role</a>
                @endif
                <a href="{{ route('admin.aslab.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/aslab*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manajemen
                    Aslab</a>
                @if (Auth::user()->role->name === 'Super Admin')
                    <a href="{{ route('admin.user.index') }}"
                        class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/user*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manajemen
                        User</a>
                @endif
                <a href="{{ route('admin.praktikum.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/praktikum*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manajemen
                    Praktikum</a>
                <a href="{{ route('admin.jadwal-praktikum.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/jadwal-praktikum*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manajemen
                    Jadwal</a>
                <a href="{{ route('admin.presensi.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/presensi*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Riwayat
                    Presensi</a>
                <a href="{{ route('admin.penilaian.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/penilaian*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manajemen
                    Penilaian</a>
                <a href="{{ route('admin.pendaftaran.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/pendaftaran*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manajemen
                    Pendaftaran</a>
                <a href="{{ route('admin.praktikan.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/praktikan*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manajemen
                    Praktikan</a>
                <a href="{{ route('admin.pengumuman.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/pengumuman*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manajemen
                    Pengumuman</a>
                <a href="{{ route('admin.logs.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/logs*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Log Aktivitas</a>
                <a href="{{ route('admin.ratings.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/ratings*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Rating Praktikan</a>
                <a href="{{ route('admin.penugasan.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/penugasan*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manajemen
                    Penugasan</a>
                <a href="{{ route('admin.recruitment.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/recruitment*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manajemen
                    Rekrutmen</a>
                <a href="{{ route('admin.kegiatan.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/kegiatan*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manajemen
                    Kegiatan</a>
                <a href="{{ route('admin.digit-npm.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/digit-npm*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manage
                    NPM</a>
                <a href="{{ route('admin.notifications.create') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/notifications*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Kirim
                    Notifikasi</a>
            @endif

            {{-- Profile Link for Mobile --}}
            <div class="pt-3 sm:pt-4 mt-3 sm:mt-4 border-t border-slate-100">
                @php
                    $profileEditRoute = 'admin.profile.edit';
                    if (Auth::user()->role) {
                        if (Auth::user()->role->name === 'Praktikan') {
                            $profileEditRoute = 'praktikan.profile.edit';
                        } elseif (Auth::user()->role->name === 'Aslab') {
                            $profileEditRoute = 'aslab.profile.edit';
                        }
                    }
                @endphp
                <a href="{{ route($profileEditRoute) }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('*/profile/edit') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">
                    <i class="fas fa-user-circle mr-2"></i> Pengaturan Profil
                </a>
            </div>

            <div class="pt-2">
                <button onclick="confirmLogout()"
                    class="w-full py-2.5 sm:py-3 bg-rose-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-rose-600/20">LOGOUT</button>
            </div>
        </div>
    </div>
</nav>


<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

<script>
    document.getElementById('admin-mobile-button').addEventListener('click', function() {
        const menu = document.getElementById('admin-mobile-menu');
        menu.classList.toggle('hidden');
    });

    // Profile Dropdown Toggle for Mobile
    document.getElementById('profile-dropdown-button').addEventListener('click', function(e) {
        e.stopPropagation();
        const dropdown = document.getElementById('profile-dropdown-menu');
        dropdown.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('profile-dropdown-menu');
        const button = document.getElementById('profile-dropdown-button');
        if (!button.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    function confirmLogout() {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: "Apakah Anda yakin ingin mengakhiri sesi admin?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7367f0',
            cancelButtonColor: '#82868b',
            confirmButtonText: 'Ya, Logout Sekarang',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-xl shadow-xl',
                title: 'text-2xl font-bold text-slate-700',
                htmlContainer: 'text-slate-500 text-sm mt-2',
                confirmButton: 'px-6 py-2.5 rounded-lg font-medium text-sm',
                cancelButton: 'px-6 py-2.5 rounded-lg font-medium text-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }

    // Polling Notification System
    let latestNotificationId =
        '{{ Auth::user()->unreadNotifications->first() ? Auth::user()->unreadNotifications->first()->id : '' }}';

    function fetchNotifications() {
        fetch("{{ route('notifications.fetch') }}", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    // Check if there is a new notification
                    if (data.latest_id && data.latest_id !== latestNotificationId) {
                        // Show SweetAlert Toast
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: 'Notifikasi Baru',
                            text: data.notifications[0].title,
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                    }
                    latestNotificationId = data.latest_id;

                    // Update badge
                    const button = document.getElementById('notification-button');
                    if (!button.querySelector('.animate-ping')) {
                        const badge = document.createElement('span');
                        badge.className = 'absolute top-1 right-1 flex h-2.5 w-2.5';
                        badge.innerHTML = `<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                     <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>`;
                        button.appendChild(badge);
                    }

                    // Update dropdown list
                    const container = document.querySelector('#notification-dropdown .max-h-80');
                    if (container) {
                        let html = '';
                        data.notifications.forEach(notif => {
                            html += `
                        <div class="px-4 py-3 border-b border-slate-50 hover:bg-slate-50 transition w-full group/notif relative">
                            <h4 class="text-sm font-bold text-slate-900 pr-6">${notif.title}</h4>
                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">${notif.message}</p>
                            <span class="text-[10px] text-slate-400 mt-1 block">${notif.time}</span>
                            <a href="/notifications/${notif.id}/read" class="absolute right-3 top-3 text-slate-300 hover:text-[#001f3f] opacity-0 group-hover/notif:opacity-100 transition" title="Tandai dibaca">
                                <i class="fas fa-check-circle"></i>
                            </a>
                        </div>`;
                        });
                        container.innerHTML = html;
                    }

                    // Update link count
                    const viewAllLink = document.querySelector('#notification-dropdown .border-t a');
                    if (viewAllLink) {
                        viewAllLink.innerHTML = 'Lihat semua notifikasi (' + data.count + ')';
                    }
                } else {
                    latestNotificationId = null;
                    // Remove badge
                    const ping = document.querySelector('#notification-button .animate-ping');
                    if (ping && ping.parentElement) {
                        ping.parentElement.remove();
                    }

                    // Empty state
                    const container = document.querySelector('#notification-dropdown .max-h-80');
                    if (container) {
                        container.innerHTML = `
                    <div class="px-4 py-6 text-center text-slate-400">
                        <i class="far fa-bell-slash text-2xl mb-2 opacity-50"></i>
                        <p class="text-xs font-medium">Belum ada notifikasi baru</p>
                    </div>`;
                    }
                }
            })
            .catch(console.error);
    }

    // Call fetchNotifications every 10 seconds (10000ms)
    setInterval(fetchNotifications, 10000);
</script>

@php
    $isDarkDashboard = request()->routeIs('admin.dashboard2');
@endphp
<header id="admin-sticky-header" class="sticky top-0 z-30 w-full {{ $isDarkDashboard ? 'bg-[#0b0e14]/85 border-b border-slate-800/80 text-white' : 'bg-gray-50/70 border-transparent text-slate-800' }} backdrop-blur-md pt-4 pb-2 transition-all duration-300">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            
            <!-- Left: Mobile Toggle & Page Title / Breadcrumb Context -->
            <div class="flex items-center gap-3">
                <button id="toggle-sidebar-btn"
                    class="lg:hidden flex items-center justify-center w-10 h-10 rounded-xl {{ $isDarkDashboard ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-white border-slate-200/80 text-slate-600' }} hover:text-slate-900 shadow-sm transition-all">
                    <i class="fas fa-bars text-base"></i>
                </button>

                <div class="hidden sm:flex flex-col">
                    <h2 class="text-sm font-bold {{ $isDarkDashboard ? 'text-white' : 'text-slate-800' }} tracking-tight leading-none">
                        @yield('title', 'Dashboard')
                    </h2>
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mt-1">
                        Lab RPL ITATS
                    </span>
                </div>
            </div>

            <!-- Right: System Online, Notification Brief & Profile Dropdown -->
            <div class="flex items-center gap-3">
                
                <!-- System Online Badge (Elegant Glowing Pulse Animation) -->
                <span class="hidden md:inline-flex items-center px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 text-xs font-extrabold shadow-sm tracking-tight transition-all hover:bg-emerald-500/20">
                    <span class="relative flex h-2 w-2 mr-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500 shadow-[0_0_8px_#10b981]"></span>
                    </span>
                    System Online
                </span>
                
                @php
                    $notifIndexRoute = 'praktikan.notifications.index';
                    $markAllRoute = 'praktikan.notifications.markAllAsRead';
                    $markReadRoute = 'praktikan.notifications.markAsRead';
                    if (Auth::check() && Auth::user()->role) {
                        if (Auth::user()->role->name === 'Super Admin' || Auth::user()->role->name === 'Admin') {
                            $notifIndexRoute = 'admin.notifications.index';
                            $markAllRoute = 'admin.notifications.markAllAsRead';
                            $markReadRoute = 'admin.notifications.markAsRead';
                        } elseif (Auth::user()->role->name === 'Aslab') {
                            $notifIndexRoute = 'aslab.notifications.index';
                            $markAllRoute = 'aslab.notifications.markAllAsRead';
                            $markReadRoute = 'aslab.notifications.markAsRead';
                        }
                    }
                    $recentNotifs = Auth::check() ? Auth::user()->notifications()->take(5)->get() : collect();
                    $unreadCount = Auth::check() ? Auth::user()->unreadNotifications->count() : 0;
                @endphp

                <!-- Notifications Dropdown (Hover Trigger) -->
                <div class="relative group">
                    <button class="relative flex items-center justify-center w-9 h-9 rounded-xl bg-white/80 hover:bg-white border border-slate-200/60 text-slate-600 hover:text-[#001f3f] shadow-sm transition-all">
                        <i class="fas fa-bell text-xs"></i>
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 text-white text-[9px] font-black rounded-full flex items-center justify-center shadow-sm">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Hover Notification Modal Dropdown -->
                    <div class="fixed sm:absolute left-4 right-4 sm:left-auto sm:right-0 top-16 sm:top-full mt-2 w-auto sm:w-96 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="p-3.5 bg-slate-900 text-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-bell text-xs text-blue-400"></i>
                                <span class="text-xs font-bold tracking-tight">Notifikasi</span>
                            </div>
                            @if($unreadCount > 0)
                                <a href="{{ route($markAllRoute) }}" class="text-[10px] text-blue-300 hover:text-white font-semibold transition-colors">
                                    Tandai semua dibaca
                                </a>
                            @endif
                        </div>

                        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                            @forelse($recentNotifs as $notif)
                                <div class="p-3 hover:bg-slate-50 transition-colors {{ $notif->read_at ? 'opacity-70' : 'bg-blue-50/30' }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 mb-0.5">
                                                @if(!$notif->read_at)
                                                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                                                @endif
                                                <p class="text-xs font-bold text-slate-800 truncate">{{ $notif->data['title'] ?? 'Notifikasi' }}</p>
                                            </div>
                                            <p class="text-[11px] text-slate-600 line-clamp-2 leading-relaxed">{{ $notif->data['message'] ?? '' }}</p>
                                            <span class="text-[9px] font-semibold text-slate-400 mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if(!$notif->read_at)
                                            <a href="{{ route($markReadRoute, $notif->id) }}" class="text-slate-300 hover:text-emerald-600 p-1 shrink-0" title="Tandai dibaca">
                                                <i class="fas fa-check text-xs"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 text-center">
                                    <i class="far fa-bell-slash text-2xl text-slate-300 mb-2 block"></i>
                                    <p class="text-xs text-slate-500 font-medium">Tidak ada notifikasi baru</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="p-2.5 bg-slate-50 border-t border-slate-100 text-center">
                            <a href="{{ route($notifIndexRoute) }}"
                                class="inline-flex items-center justify-center gap-2 w-full py-2 bg-[#001f3f] text-white hover:bg-slate-800 text-xs font-bold rounded-lg transition-all shadow-sm">
                                <span>Lihat Seluruh Notifikasi</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative group">
                    <button class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/80 hover:bg-white border border-slate-200/60 shadow-sm transition-all">
                        <div class="w-7 h-7 rounded-lg bg-[#001f3f] text-white flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="text-xs font-bold text-slate-700 max-w-[120px] truncate hidden md:inline-block">
                            {{ Auth::user()->name ?? 'User' }}
                        </span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400 group-hover:rotate-180 transition-transform duration-300"></i>
                    </button>

                    <!-- Profile Dropdown Menu -->
                    <div class="absolute right-0 top-full mt-2 w-52 bg-white rounded-xl shadow-xl border border-slate-200 p-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="px-3 py-2 border-b border-slate-100 mb-1">
                            <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name ?? 'User' }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                        </div>
                        
                        @php
                            $profileEditRoute = 'admin.profile.edit';
                            if (Auth::check() && Auth::user()->role) {
                                if (Auth::user()->role->name === 'Praktikan') {
                                    $profileEditRoute = 'praktikan.profile.edit';
                                } elseif (Auth::user()->role->name === 'Aslab') {
                                    $profileEditRoute = 'aslab.profile.edit';
                                }
                            }
                        @endphp

                        <a href="{{ route($profileEditRoute) }}"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-[#001f3f] transition-all">
                            <i class="fas fa-user-circle w-4 text-center text-slate-400"></i> Pengaturan Profil
                        </a>
                        <button type="button" onclick="confirmLogout()"
                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-bold text-rose-600 hover:bg-rose-50 transition-all">
                            <i class="fas fa-sign-out-alt w-4 text-center"></i> Keluar / Logout
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Floating Mobile Navbar Menu (Absolute Overlay Top-Down) -->
        <div id="mobile-navbar-menu" class="hidden lg:hidden absolute top-full left-4 right-4 z-50 mt-2 bg-[#001f3f] text-slate-200 rounded-xl shadow-2xl border border-slate-800 p-4 max-h-[75vh] overflow-y-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden transition-all duration-300 transform opacity-0 -translate-y-4 pointer-events-none">
            @if (Auth::check() && Auth::user()->role && Auth::user()->role->name === 'Praktikan')
                <div class="space-y-1">
                    <a href="{{ route('praktikan.dashboard') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->is('praktikan/dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-tachometer-alt w-4 text-center"></i> Dashboard
                    </a>
                    <a href="{{ route('praktikan.pendaftaran.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->is('praktikan/riwayat-pendaftaran*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-file-signature w-4 text-center"></i> Riwayat Pendaftaran
                    </a>
                    <a href="{{ route('praktikan.penugasan.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->is('praktikan/penugasan*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-tasks w-4 text-center"></i> Soal Praktikum
                    </a>
                    <a href="{{ route('praktikan.recruitment.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->is('praktikan/recruitment*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-user-plus w-4 text-center"></i> Rekrutmen Aslab
                    </a>
                </div>
            @elseif (Auth::check() && Auth::user()->role && Auth::user()->role->name === 'Aslab')
                <div class="space-y-1">
                    <a href="{{ route('aslab.dashboard') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->is('aslab/dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-tachometer-alt w-4 text-center"></i> Dashboard
                    </a>
                    <a href="{{ route('aslab.pendaftaran.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->is('aslab/pendaftaran*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-users w-4 text-center"></i> Daftar Bimbingan
                    </a>
                    <a href="{{ route('aslab.tugas.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->is('aslab/tugas*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-book w-4 text-center"></i> Tugas Asistensi
                    </a>
                    <a href="{{ route('aslab.penugasan.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->is('aslab/penugasan*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-clipboard-list w-4 text-center"></i> Penugasan Sesi
                    </a>
                    <a href="{{ route('aslab.penilaian.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->is('aslab/penilaian*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-star w-4 text-center"></i> Penilaian Live
                    </a>
                    <a href="{{ route('aslab.presensi.scan') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->is('aslab/presensi*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-qrcode w-4 text-center"></i> Scanner Presensi
                    </a>
                    <a href="{{ route('aslab.ratings.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->is('aslab/ratings*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-star-half-alt w-4 text-center"></i> Rating Praktikan
                    </a>
                    <a href="{{ route('aslab.portfolio.edit') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('aslab.portfolio.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-briefcase w-4 text-center"></i> Portfolio Saya
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    <div class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-chart-line w-4 text-center"></i> Dashboard Utama
                        </a>
                        <a href="{{ route('admin.dashboard2') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard2') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-chart-area w-4 text-center text-emerald-400"></i> Live Monitoring (Dark)
                        </a>
                    </div>

                    <!-- Group 1: Data Pengguna -->
                    @php
                        $isMobGroup1 = request()->is('administrator/role*') || request()->is('administrator/user*') || request()->is('administrator/aslab*') || request()->is('administrator/praktikan*');
                    @endphp
                    <div class="space-y-1">
                        <button type="button" onclick="toggleSubmenu('mob-submenu-pengguna', 'mob-arrow-pengguna')"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ $isMobGroup1 ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-users-cog w-4 text-center text-blue-300"></i>
                                <span>Data Pengguna</span>
                            </div>
                            <i id="mob-arrow-pengguna" class="fas fa-chevron-down text-[10px] transition-transform duration-300 {{ $isMobGroup1 ? 'rotate-180' : '' }}"></i>
                        </button>
                        <div id="mob-submenu-pengguna" class="{{ $isMobGroup1 ? '' : 'hidden' }} pl-4 pr-1 py-1 space-y-1 border-l-2 border-white/10 ml-5">
                            @if (Auth::check() && Auth::user()->role && Auth::user()->role->name === 'Super Admin')
                                <a href="{{ route('admin.role.index') }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/role*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                    <i class="fas fa-user-shield text-[10px] text-blue-300"></i> Manajemen Role
                                </a>
                                <a href="{{ route('admin.user.index') }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/user*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                    <i class="fas fa-user text-[10px] text-blue-300"></i> Manajemen User
                                </a>
                            @endif
                            <a href="{{ route('admin.aslab.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/aslab*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-user-tie text-[10px] text-blue-300"></i> Data Asisten Lab
                            </a>
                            <a href="{{ route('admin.praktikan.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/praktikan*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-user-graduate text-[10px] text-blue-300"></i> Data Praktikan
                            </a>
                        </div>
                    </div>

                    <!-- Group 2: Akademik -->
                    @php
                        $isMobGroup2 = request()->is('administrator/praktikum*') || request()->is('administrator/jadwal-praktikum*') || request()->is('administrator/pendaftaran*') || request()->is('administrator/presensi*') || request()->is('administrator/penilaian') || request()->is('administrator/penilaian/praktikum*') || request()->is('administrator/penilaian/jadwal*') || request()->is('administrator/penilaian-akhir*') || request()->is('administrator/dosen*') || request()->is('administrator/kelas*');
                    @endphp
                    <div class="space-y-1">
                        <button type="button" onclick="toggleSubmenu('mob-submenu-akademik', 'mob-arrow-akademik')"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ $isMobGroup2 ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-graduation-cap w-4 text-center text-blue-300"></i>
                                <span>Akademik</span>
                            </div>
                            <i id="mob-arrow-akademik" class="fas fa-chevron-down text-[10px] transition-transform duration-300 {{ $isMobGroup2 ? 'rotate-180' : '' }}"></i>
                        </button>
                        <div id="mob-submenu-akademik" class="{{ $isMobGroup2 ? '' : 'hidden' }} pl-4 pr-1 py-1 space-y-1 border-l-2 border-white/10 ml-5">
                            <a href="{{ route('admin.praktikum.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/praktikum*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-laptop-code text-[10px] text-blue-300"></i> Mata Praktikum
                            </a>
                            <a href="{{ route('admin.jadwal-praktikum.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/jadwal-praktikum*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-calendar-alt text-[10px] text-blue-300"></i> Jadwal & Sesi
                            </a>
                            <a href="{{ route('admin.pendaftaran.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/pendaftaran*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-file-signature text-[10px] text-blue-300"></i> Pendaftaran
                            </a>
                            <a href="{{ route('admin.presensi.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/presensi*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-clipboard-check text-[10px] text-blue-300"></i> Presensi
                            </a>
                            <a href="{{ route('admin.penilaian.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/penilaian') || request()->is('administrator/penilaian/praktikum*') || request()->is('administrator/penilaian/jadwal*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-edit text-[10px] text-blue-300"></i> Penilaian
                            </a>
                            <a href="{{ route('admin.penilaian-akhir.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/penilaian-akhir*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-award text-[10px] text-blue-300"></i> Penilaian Akhir
                            </a>
                            <a href="{{ route('admin.dosen.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/dosen*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-chalkboard-teacher text-[10px] text-blue-300"></i> Master Dosen
                            </a>
                            <a href="{{ route('admin.kelas.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/kelas*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-door-open text-[10px] text-blue-300"></i> Master Kelas
                            </a>
                        </div>
                    </div>

                    <!-- Group 3: Kegiatan & Operasional -->
                    @php
                        $isMobGroup3 = request()->is('administrator/kegiatan*') || request()->is('administrator/daftar-tamu*') || request()->is('administrator/penugasan*') || request()->is('administrator/digit-npm*') || request()->is('administrator/recruitment*');
                    @endphp
                    <div class="space-y-1">
                        <button type="button" onclick="toggleSubmenu('mob-submenu-kegiatan', 'mob-arrow-kegiatan')"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ $isMobGroup3 ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-tasks w-4 text-center text-blue-300"></i>
                                <span>Kegiatan & Tugas</span>
                            </div>
                            <i id="mob-arrow-kegiatan" class="fas fa-chevron-down text-[10px] transition-transform duration-300 {{ $isMobGroup3 ? 'rotate-180' : '' }}"></i>
                        </button>
                        <div id="mob-submenu-kegiatan" class="{{ $isMobGroup3 ? '' : 'hidden' }} pl-4 pr-1 py-1 space-y-1 border-l-2 border-white/10 ml-5">
                            <a href="{{ route('admin.kegiatan.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/kegiatan*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-calendar-check text-[10px] text-blue-300"></i> Daftar Kegiatan
                            </a>
                            <a href="{{ route('admin.guest-visits.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/daftar-tamu*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-id-badge text-[10px] text-blue-300"></i> Daftar Tamu
                            </a>
                            <a href="{{ route('admin.penugasan.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/penugasan*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-clipboard-list text-[10px] text-blue-300"></i> Penugasan Sesi
                            </a>
                            <a href="{{ route('admin.digit-npm.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/digit-npm*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-list-ol text-[10px] text-blue-300"></i> Manage NPM
                            </a>
                            <a href="{{ route('admin.recruitment.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/recruitment*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-user-plus text-[10px] text-blue-300"></i> Rekrutmen Aslab
                            </a>
                        </div>
                    </div>

                    <!-- Group 4: Informasi & System -->
                    @php
                        $isMobGroup4 = request()->is('administrator/logs*') || request()->is('administrator/ratings*') || request()->is('administrator/pengumuman*') || request()->is('administrator/notifications*');
                    @endphp
                    <div class="space-y-1">
                        <button type="button" onclick="toggleSubmenu('mob-submenu-informasi', 'mob-arrow-informasi')"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ $isMobGroup4 ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-info-circle w-4 text-center text-blue-300"></i>
                                <span>Informasi</span>
                            </div>
                            <i id="mob-arrow-informasi" class="fas fa-chevron-down text-[10px] transition-transform duration-300 {{ $isMobGroup4 ? 'rotate-180' : '' }}"></i>
                        </button>
                        <div id="mob-submenu-informasi" class="{{ $isMobGroup4 ? '' : 'hidden' }} pl-4 pr-1 py-1 space-y-1 border-l-2 border-white/10 ml-5">
                            <a href="{{ route('admin.logs.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/logs*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-history text-[10px] text-blue-300"></i> Log Aktivitas
                            </a>
                            <a href="{{ route('admin.ratings.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/ratings*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-star text-[10px] text-blue-300"></i> Rating Praktikan
                            </a>
                            <a href="{{ route('admin.pengumuman.index') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/pengumuman*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-bullhorn text-[10px] text-blue-300"></i> Pengumuman
                            </a>
                            <a href="{{ route('admin.notifications.create') }}"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->is('administrator/notifications*') ? 'text-white font-bold bg-white/15' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-bell text-[10px] text-blue-300"></i> Kirim Notifikasi
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const header = document.getElementById('admin-sticky-header');
        if (header) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 20) {
                    header.classList.remove('bg-gray-50/70', 'border-transparent');
                    header.classList.add('bg-gray-50/80', 'shadow-sm', 'border-b', 'border-slate-200/60');
                } else {
                    header.classList.remove('bg-gray-50/80', 'shadow-sm', 'border-b', 'border-slate-200/60');
                    header.classList.add('bg-gray-50/70', 'border-transparent');
                }
            });
        }

        const toggleBtn = document.getElementById('toggle-sidebar-btn');
        const mobileNavMenu = document.getElementById('mobile-navbar-menu');
        let isOpen = false;

        if (toggleBtn && mobileNavMenu) {
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                isOpen = !isOpen;
                if (isOpen) {
                    mobileNavMenu.classList.remove('hidden');
                    // Trigger reflow for transition
                    void mobileNavMenu.offsetWidth;
                    mobileNavMenu.classList.remove('opacity-0', '-translate-y-4', 'pointer-events-none');
                    mobileNavMenu.classList.add('opacity-100', 'translate-y-0');
                } else {
                    mobileNavMenu.classList.remove('opacity-100', 'translate-y-0');
                    mobileNavMenu.classList.add('opacity-0', '-translate-y-4', 'pointer-events-none');
                    setTimeout(() => {
                        if (!isOpen) {
                            mobileNavMenu.classList.add('hidden');
                        }
                    }, 300);
                }
            });

            document.addEventListener('click', function(e) {
                if (isOpen && !mobileNavMenu.contains(e.target) && !toggleBtn.contains(e.target)) {
                    isOpen = false;
                    mobileNavMenu.classList.remove('opacity-100', 'translate-y-0');
                    mobileNavMenu.classList.add('opacity-0', '-translate-y-4', 'pointer-events-none');
                    setTimeout(() => {
                        if (!isOpen) {
                            mobileNavMenu.classList.add('hidden');
                        }
                    }, 300);
                }
            });
        }
    });
</script>

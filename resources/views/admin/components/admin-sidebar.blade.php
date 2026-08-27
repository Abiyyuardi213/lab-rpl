<aside id="floating-sidebar"
    class="fixed top-4 bottom-4 left-4 z-40 w-72 bg-[#001f3f] text-slate-200 border border-slate-800 rounded-xl shadow-2xl flex flex-col transition-all duration-300 transform -translate-x-full lg:translate-x-0">

    <!-- Sidebar Header / Logo -->
    <div class="p-5 border-b border-white/10 flex items-center justify-between">
        @php
            $logoUrl = url('/administrator/dashboard');
            if (Auth::check() && Auth::user()->role) {
                if (Auth::user()->role->name === 'Praktikan') {
                    $logoUrl = route('praktikan.dashboard');
                } elseif (Auth::user()->role->name === 'Aslab') {
                    $logoUrl = route('aslab.dashboard');
                }
            }
        @endphp
        <a href="{{ $logoUrl }}" class="flex items-center gap-3">
            <img src="{{ asset('image/logo-RPL.jpg') }}" alt="LabRPL Logo"
                class="w-9 h-9 object-contain rounded-lg ring-2 ring-white/20">
            <div class="flex flex-col">
                <span class="font-bold text-base leading-tight text-white tracking-tight">Laboratorium RPL</span>
                <span class="text-[10px] font-semibold text-blue-200/70 uppercase tracking-wider">
                    @if (Auth::check() && Auth::user()->role)
                        {{ Auth::user()->role->name }}
                    @else
                        Administrator
                    @endif
                </span>
            </div>
        </a>
        <button id="close-sidebar-btn" class="lg:hidden text-slate-400 hover:text-white p-1">
            <i class="fas fa-times text-base"></i>
        </button>
    </div>

    <!-- Sidebar Navigation Items -->
    <div
        class="flex-1 overflow-y-auto p-4 space-y-3 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
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
            <!-- Dashboard Single Item -->
            <div>
                <a href="{{ url('/administrator/dashboard') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->is('administrator/dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-tachometer-alt w-4 text-center"></i> Dashboard
                </a>
            </div>

            <!-- Group 1: Data Pengguna -->
            @php
                $isGroup1Active =
                    request()->is('administrator/role*') ||
                    request()->is('administrator/user*') ||
                    request()->is('administrator/aslab*') ||
                    request()->is('administrator/praktikan*');
            @endphp
            <div class="space-y-1">
                <button type="button" onclick="toggleSubmenu('submenu-pengguna', 'arrow-pengguna')"
                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ $isGroup1Active ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-users-cog w-4 text-center text-blue-300"></i>
                        <span>Data Pengguna</span>
                    </div>
                    <i id="arrow-pengguna"
                        class="fas fa-chevron-down text-[10px] transition-transform duration-300 {{ $isGroup1Active ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="submenu-pengguna"
                    class="{{ $isGroup1Active ? '' : 'hidden' }} pl-4 pr-1 py-1 space-y-1 border-l-2 border-white/10 ml-5 transition-all">
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
                $isGroup2Active =
                    request()->is('administrator/praktikum*') ||
                    request()->is('administrator/jadwal-praktikum*') ||
                    request()->is('administrator/pendaftaran*') ||
                    request()->is('administrator/presensi*') ||
                    request()->is('administrator/penilaian') ||
                    request()->is('administrator/penilaian/praktikum*') ||
                    request()->is('administrator/penilaian/jadwal*') ||
                    request()->is('administrator/penilaian-akhir*') ||
                    request()->is('administrator/dosen*') ||
                    request()->is('administrator/kelas*');
            @endphp
            <div class="space-y-1">
                <button type="button" onclick="toggleSubmenu('submenu-akademik', 'arrow-akademik')"
                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ $isGroup2Active ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-graduation-cap w-4 text-center text-blue-300"></i>
                        <span>Akademik</span>
                    </div>
                    <i id="arrow-akademik"
                        class="fas fa-chevron-down text-[10px] transition-transform duration-300 {{ $isGroup2Active ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="submenu-akademik"
                    class="{{ $isGroup2Active ? '' : 'hidden' }} pl-4 pr-1 py-1 space-y-1 border-l-2 border-white/10 ml-5 transition-all">
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
                $isGroup3Active =
                    request()->is('administrator/kegiatan*') ||
                    request()->is('administrator/daftar-tamu*') ||
                    request()->is('administrator/penugasan*') ||
                    request()->is('administrator/digit-npm*') ||
                    request()->is('administrator/recruitment*');
            @endphp
            <div class="space-y-1">
                <button type="button" onclick="toggleSubmenu('submenu-kegiatan', 'arrow-kegiatan')"
                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ $isGroup3Active ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-tasks w-4 text-center text-blue-300"></i>
                        <span>Kegiatan & Tugas</span>
                    </div>
                    <i id="arrow-kegiatan"
                        class="fas fa-chevron-down text-[10px] transition-transform duration-300 {{ $isGroup3Active ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="submenu-kegiatan"
                    class="{{ $isGroup3Active ? '' : 'hidden' }} pl-4 pr-1 py-1 space-y-1 border-l-2 border-white/10 ml-5 transition-all">
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
                $isGroup4Active =
                    request()->is('administrator/logs*') ||
                    request()->is('administrator/ratings*') ||
                    request()->is('administrator/pengumuman*') ||
                    request()->is('administrator/notifications*');
            @endphp
            <div class="space-y-1">
                <button type="button" onclick="toggleSubmenu('submenu-informasi', 'arrow-informasi')"
                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all {{ $isGroup4Active ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-info-circle w-4 text-center text-blue-300"></i>
                        <span>Informasi</span>
                    </div>
                    <i id="arrow-informasi"
                        class="fas fa-chevron-down text-[10px] transition-transform duration-300 {{ $isGroup4Active ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="submenu-informasi"
                    class="{{ $isGroup4Active ? '' : 'hidden' }} pl-4 pr-1 py-1 space-y-1 border-l-2 border-white/10 ml-5 transition-all">
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
        @endif
    </div>

    <!-- Sidebar Footer / Profile Brief -->
    <div class="p-4 border-t border-white/10 bg-black/20 rounded-b-xl flex items-center justify-between">
        <div class="flex items-center gap-3 min-w-0">
            <div
                class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs shrink-0 ring-2 ring-white/20">
                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-white truncate leading-tight">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="text-[10px] font-medium text-slate-300 truncate leading-tight">
                    {{ Auth::user()->email ?? '' }}</p>
            </div>
        </div>
        <button onclick="confirmLogout()"
            class="text-slate-400 hover:text-rose-400 transition-colors p-1.5 rounded-lg hover:bg-white/10"
            title="Logout">
            <i class="fas fa-sign-out-alt text-sm"></i>
        </button>
    </div>
</aside>

<!-- Overlay for Mobile Sidebar -->
<div id="sidebar-backdrop"
    class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 hidden lg:hidden transition-opacity duration-300"></div>

<script>
    function toggleSubmenu(id, arrowId) {
        const submenu = document.getElementById(id);
        const arrow = document.getElementById(arrowId);
        if (submenu) {
            submenu.classList.toggle('hidden');
        }
        if (arrow) {
            arrow.classList.toggle('rotate-180');
        }
    }
</script>

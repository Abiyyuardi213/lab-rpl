<nav class="fixed w-full z-50 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-slate-100">
    <div class="max-w-[1600px] mx-auto px-3 sm:px-6 lg:px-12">
        <div class="flex justify-between items-center h-16 sm:h-20">
            <!-- Left: Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-2 sm:gap-3">
                    <img src="{{ asset('image/rplmini.png') }}" alt="LabRPL Logo"
                        class="w-10 h-10 sm:w-12 sm:h-12 object-contain">
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
            <div class="hidden lg:flex items-center space-x-6">
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
                @elseif (Auth::user()->role && Auth::user()->role->name === 'Aslab')
                    <a href="{{ route('aslab.dashboard') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('aslab/dashboard') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Dashboard
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('aslab/dashboard') ? 'w-full' : '' }}"></span>
                    </a>

                    <a href="{{ route('aslab.pendaftaran.index') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('aslab/pendaftaran*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Daftar Bimbingan
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('aslab/pendaftaran*') ? 'w-full' : '' }}"></span>
                    </a>

                    <a href="{{ route('aslab.tugas.index') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('aslab/tugas*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Tugas Asistensi
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('aslab/tugas*') ? 'w-full' : '' }}"></span>
                    </a>
                @else
                    <a href="{{ url('/admin/dashboard') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('admin/dashboard') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Dashboard
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('admin/dashboard') ? 'w-full' : '' }}"></span>
                    </a>

                    <div class="h-4 w-px bg-slate-200"></div>

                    @if (Auth::user()->role->name === 'Super Admin')
                        <a href="{{ route('admin.role.index') }}"
                            class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('admin/role*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                            Role
                            <span
                                class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('admin/role*') ? 'w-full' : '' }}"></span>
                        </a>
                    @endif

                    <a href="{{ route('admin.aslab.index') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('admin/aslab*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Aslab
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('admin/aslab*') ? 'w-full' : '' }}"></span>
                    </a>

                    @if (Auth::user()->role->name === 'Super Admin')
                        <a href="{{ route('admin.user.index') }}"
                            class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('admin/user*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                            User
                            <span
                                class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('admin/user*') ? 'w-full' : '' }}"></span>
                        </a>
                    @endif

                    <a href="{{ route('admin.praktikum.index') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('admin/praktikum*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Praktikum
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('admin/praktikum*') ? 'w-full' : '' }}"></span>
                    </a>

                    <a href="{{ route('admin.jadwal-praktikum.index') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('admin/jadwal-praktikum*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Jadwal
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('admin/jadwal-praktikum*') ? 'w-full' : '' }}"></span>
                    </a>

                    <a href="{{ route('admin.pendaftaran.index') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('admin/pendaftaran*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Pendaftaran
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('admin/pendaftaran*') ? 'w-full' : '' }}"></span>
                    </a>

                    <a href="{{ route('admin.praktikan.index') }}"
                        class="relative group text-sm font-semibold transition-colors hover:text-[#001f3f] {{ request()->is('admin/praktikan*') ? 'text-[#001f3f]' : 'text-slate-600' }}">
                        Praktikan
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-0.5 bg-[#001f3f] transition-all duration-300 transform -translate-x-1/2 group-hover:w-full {{ request()->is('admin/praktikan*') ? 'w-full' : '' }}"></span>
                    </a>
                @endif
            </div>

            <!-- Right: Profile & Action -->
            <div class="flex items-center gap-2 sm:gap-4">
                <div class="hidden md:flex flex-col items-end mr-2">
                    <span class="text-xs font-bold text-slate-900 leading-none">{{ Auth::user()->name }}</span>
                    <span
                        class="text-[10px] font-medium text-slate-400 uppercase tracking-tighter">{{ Auth::user()->role ? Auth::user()->role->display_name : 'No Role' }}</span>
                </div>

                <div class="relative group">
                    <button
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-slate-100 p-0.5 border border-slate-200 focus:outline-none flex items-center justify-center overflow-hidden">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=f8fafc&color=0f172a&bold=true"
                            class="w-full h-full object-cover">
                    </button>
                    <!-- Dropdown -->
                    <div
                        class="absolute top-full right-0 pt-3 w-56 hidden group-hover:block transition-all animate-in fade-in slide-in-from-top-2">
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
            @elseif(Auth::user()->role && Auth::user()->role->name === 'Aslab')
                <a href="{{ route('aslab.dashboard') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('aslab/dashboard') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Dashboard</a>
                <a href="{{ route('aslab.pendaftaran.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('aslab/pendaftaran*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Daftar
                    Bimbingan</a>
                <a href="{{ route('aslab.tugas.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('aslab/tugas*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Tugas
                    Asistensi</a>
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
                <a href="{{ route('admin.pendaftaran.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/pendaftaran*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manajemen
                    Pendaftaran</a>
                <a href="{{ route('admin.praktikan.index') }}"
                    class="block px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-sm font-bold {{ request()->is('admin/praktikan*') ? 'bg-primary/5 text-primary' : 'text-slate-600' }}">Manajemen
                    Praktikan</a>
            @endif
            <div class="pt-3 sm:pt-4 mt-3 sm:mt-4 border-t border-slate-100">
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

    function confirmLogout() {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: "Apakah Anda yakin ingin mengakhiri sesi admin?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#f1f5f9',
            confirmButtonText: 'Ya, Logout Sekarang',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'px-6 py-3 rounded-xl font-bold text-sm',
                cancelButton: 'px-6 py-3 rounded-xl font-bold text-sm text-slate-600'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>

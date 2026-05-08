<aside
    class="w-64 bg-white border-r border-zinc-200 hidden md:flex flex-col h-full shrink-0 transition-all duration-300 relative z-20">
    <!-- Logo -->
    <div class="h-20 flex items-center px-6 border-b border-zinc-100 bg-white">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 group">
            <div class="flex-shrink-0">
                <img src="{{ asset('image/logo-RPL.jpg') }}" alt="Logo RPL"
                    class="h-10 w-auto object-contain transition-transform duration-500 group-hover:rotate-[5deg]">
            </div>
            <div class="flex items-center gap-3">
                <span class="font-black text-lg text-zinc-900 tracking-tighter uppercase leading-none">LabRPL</span>
                {{-- <div class="h-5 w-[1px] bg-zinc-200 rotate-[15deg]"></div> --}}
                <span class="text-sm font-bold text-zinc-400 tracking-tight leading-none">Admin<span
                        class="font-medium text-zinc-300">Panel</span></span>
            </div>
        </a>
    </div>

    <!-- User Info Section -->
    <div class="p-5 border-b border-zinc-50 bg-zinc-50/30">
        <div class="flex items-center gap-4">
            @php
                $currentUser = auth()->user();
                $profilePic =
                    $currentUser && $currentUser->profile_picture
                        ? asset('storage/' . $currentUser->profile_picture)
                        : null;
            @endphp
            <div class="relative flex-shrink-0">
                @if ($profilePic)
                    <img src="{{ $profilePic }}"
                        class="w-10 h-10 rounded-xl object-cover border border-zinc-200 shadow-sm" alt="User profile">
                @else
                    <div
                        class="w-10 h-10 rounded-xl bg-white border border-zinc-200 shadow-sm flex items-center justify-center text-zinc-400">
                        <i class="fas fa-user-shield text-lg"></i>
                    </div>
                @endif
                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full">
                </div>
            </div>
            <div class="overflow-hidden">
                <h6 class="text-sm font-black text-zinc-800 truncate leading-tight">
                    {{ $currentUser->username ?? 'Guest Account' }}</h6>
                <p class="text-[11px] font-black text-zinc-400 mt-1 uppercase tracking-wider leading-none">
                    {{ $currentUser->role->display_name ?? 'Administrator' }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
            <i class="fas fa-home w-4 text-center text-[10px]"></i>
            <span>Dashboard</span>
        </a>

        <!-- Manajemen Laboratorium -->
        <div class="pt-6 pb-2 px-3">
            <p class="text-xs font-bold text-zinc-400 uppercase tracking-[0.2em]">Manajemen Lab</p>
        </div>

        <div class="space-y-1">
            <a href="{{ route('admin.praktikum.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.praktikum.*') ? 'bg-zinc-100 text-zinc-900 shadow-sm' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
                <i class="fas fa-flask w-4 text-center text-[10px]"></i>
                <span>Data Praktikum</span>
            </a>

            <a href="{{ route('admin.pendaftaran.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.pendaftaran.*') ? 'bg-zinc-100 text-zinc-900 shadow-sm' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
                <i class="fas fa-clipboard-list w-4 text-center text-[10px]"></i>
                <span>Pendaftaran</span>
            </a>

            <a href="{{ route('admin.aslab.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.aslab.*') ? 'bg-zinc-100 text-zinc-900 shadow-sm' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
                <i class="fas fa-user-tie w-4 text-center text-[10px]"></i>
                <span>Data Aslab</span>
            </a>

            <a href="{{ route('admin.praktikan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.praktikan.*') ? 'bg-zinc-100 text-zinc-900 shadow-sm' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
                <i class="fas fa-user-graduate w-4 text-center text-[10px]"></i>
                <span>Data Praktikan</span>
            </a>

            <a href="{{ route('admin.ratings.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.ratings.*') ? 'bg-zinc-100 text-zinc-900 shadow-sm' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
                <i class="fas fa-star w-4 text-center text-[10px]"></i>
                <span>Rating & Ulasan</span>
            </a>

            <a href="{{ route('admin.notifications.create') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.notifications.*') ? 'bg-zinc-100 text-zinc-900 shadow-sm' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
                <i class="fas fa-paper-plane w-4 text-center text-[10px]"></i>
                <span>Kirim Notifikasi</span>
            </a>
        </div>

        @if ($currentUser && $currentUser->role && $currentUser->role->name === 'Super Admin')
            <!-- Manajemen Pengguna (Super Admin ONLY) -->
            <div class="pt-6 pb-2 px-3">
                <p class="text-xs font-bold text-zinc-400 uppercase tracking-[0.2em]">Manajemen User</p>
            </div>

            <div class="space-y-1">
                <a href="{{ route('admin.user.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.user.*') ? 'bg-zinc-100 text-zinc-900 shadow-sm' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
                    <i class="fas fa-users-gear w-4 text-center text-[10px]"></i>
                    <span>List Pengguna</span>
                </a>

                <a href="{{ route('admin.role.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.role.*') ? 'bg-zinc-100 text-zinc-900 shadow-sm' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
                    <i class="fas fa-shield-halved w-4 text-center text-[10px]"></i>
                    <span>Role & Hak Akses</span>
                </a>
            </div>
        @endif

        <div class="h-10"></div> <!-- Spacer -->
    </nav>
</aside>

<script>
    function toggleMenu(submenuId, arrowId) {
        const submenu = document.getElementById(submenuId);
        const arrow = document.getElementById(arrowId);

        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            arrow.classList.add('rotate-180');
        } else {
            submenu.classList.add('hidden');
            arrow.classList.remove('rotate-180');
        }
    }
</script>

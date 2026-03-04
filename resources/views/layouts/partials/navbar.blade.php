<header
    class="h-16 bg-white border-b border-zinc-100 flex items-center justify-between px-6 z-10 sticky top-0 backdrop-blur supports-[backdrop-filter]:bg-white/80">
    <!-- Mobile Sidebar Toggle -->
    <button class="md:hidden text-zinc-500 hover:text-zinc-900 focus:outline-none transition-colors">
        <i class="fas fa-bars text-xl"></i>
    </button>

    <!-- Left Side -->
    <div class="hidden md:flex items-center gap-2 text-sm text-zinc-500">
        <div class="flex items-center gap-2 bg-zinc-50 px-3 py-1.5 rounded-lg border border-zinc-100">
            <i class="far fa-calendar-alt text-zinc-400"></i>
            <span class="font-bold text-zinc-800 tracking-tight">{{ date('d F Y') }}</span>
        </div>
    </div>

    <!-- Right Side -->
    <div class="flex items-center gap-4">
        <!-- Notification -->
        <div class="relative group">
            @php
                $user = auth()->user();
                $unreadCount = $user ? $user->unreadNotifications->count() : 0;
            @endphp
            <button class="relative p-2 text-zinc-400 hover:text-zinc-900 transition-all focus:outline-none">
                <i class="far fa-bell text-lg"></i>
                @if ($unreadCount > 0)
                    <span class="absolute top-1.5 right-2 flex h-2.5 w-2.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span
                            class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500 border border-white shadow-sm"></span>
                    </span>
                @endif
            </button>

            <!-- Notification Dropdown -->
            <div
                class="absolute right-0 top-full pt-2 w-80 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                <div class="bg-white rounded-2xl shadow-xl shadow-zinc-200/50 border border-zinc-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-zinc-100 flex justify-between items-center bg-zinc-50/50">
                        <p class="text-xs font-black text-zinc-900 uppercase tracking-widest">Notifikasi
                            ({{ $unreadCount }})</p>
                        @if ($unreadCount > 0)
                            <form action="{{ route('notifications.readAll') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="text-[10px] font-black text-zinc-400 hover:text-zinc-900 uppercase tracking-widest transition-colors">MARK
                                    READ</button>
                            </form>
                        @endif
                    </div>

                    <div class="max-h-80 overflow-y-auto p-2 space-y-1">
                        @if ($user)
                            @forelse($user->unreadNotifications as $notification)
                                <a href="{{ route('notifications.go', $notification->id) }}"
                                    class="flex gap-4 p-3 rounded-xl hover:bg-zinc-50 transition-colors group/item relative">
                                    <div
                                        class="h-10 w-10 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 group-hover/item:bg-white group-hover/item:shadow-sm transition-all flex-shrink-0">
                                        <i class="fas fa-bolt text-xs"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-sm font-bold text-zinc-800 mb-0.5 line-clamp-1">
                                            {{ $notification->data['title'] ?? 'Notifikasi' }}</p>
                                        <p class="text-xs text-zinc-500 line-clamp-2 leading-relaxed">
                                            {{ $notification->data['message'] ?? '' }}</p>
                                        <p class="text-[10px] font-bold text-zinc-400 mt-2 uppercase tracking-wide">
                                            {{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="absolute top-4 right-4 h-1.5 w-1.5 rounded-full bg-rose-500"></div>
                                </a>
                            @empty
                                <div class="py-12 text-center">
                                    <div
                                        class="h-12 w-12 bg-zinc-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-zinc-200 border border-zinc-100">
                                        <i class="far fa-bell-slash text-xl"></i>
                                    </div>
                                    <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Tidak ada
                                        notifikasi</p>
                                </div>
                            @endforelse
                        @endif
                    </div>

                    <div class="p-4 bg-zinc-50/50 border-t border-zinc-100">
                        <a href="{{ route('notifications.index') }}"
                            class="block w-full py-2 bg-white border border-zinc-200 rounded-xl text-center text-xs font-black text-zinc-500 hover:text-zinc-900 transition-all uppercase tracking-widest">LIHAT
                            SEMUA</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="h-6 w-[1px] bg-zinc-100 mx-1"></div>

        <!-- User Dropdown -->
        <div class="relative group">
            <button
                class="flex items-center gap-3 p-1 rounded-xl hover:bg-zinc-50 transition-all group-hover:shadow-sm">
                <div class="text-right hidden sm:block pl-2">
                    <p class="text-sm font-black text-zinc-900 tracking-tight leading-none mb-1.5 uppercase">
                        {{ $user->username ?? 'GUEST' }}</p>
                    <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest leading-none">
                        {{ $user->role->role_name ?? 'NOT LOGGED' }}</p>
                </div>
                <div class="relative">
                    @if ($user && $user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}"
                            class="h-9 w-9 rounded-xl object-cover border border-zinc-200 shadow-sm" alt="Avatar">
                    @else
                        <div
                            class="h-9 w-9 rounded-xl bg-zinc-900 flex items-center justify-center text-white shadow-lg shadow-zinc-200 border border-zinc-800 transition-transform group-hover:scale-105">
                            <i class="fas fa-user-shield text-xs"></i>
                        </div>
                    @endif
                    <div
                        class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full shadow-sm">
                    </div>
                </div>
                <i
                    class="fas fa-chevron-down text-[10px] text-zinc-300 group-hover:text-zinc-500 transition-transform duration-300 group-hover:rotate-180 mr-1 pl-1"></i>
            </button>

            <!-- User Dropdown Menu -->
            <div
                class="absolute right-0 top-full pt-2 w-64 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                <div
                    class="bg-white rounded-2xl shadow-xl shadow-zinc-200/50 border border-zinc-200 py-2 overflow-hidden px-2">
                    <div class="px-4 py-3 mb-2 rounded-xl bg-zinc-50/50">
                        <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">AKUN SAYA</p>
                        <p class="text-sm font-black text-zinc-900 truncate tracking-tight lowercase">
                            {{ $user->email ?? 'unknown@labrpl.com' }}</p>
                    </div>

                    <div class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-zinc-500 rounded-xl hover:bg-zinc-50 hover:text-zinc-900 transition-all uppercase tracking-widest">
                            <i class="fas fa-tachometer-alt w-4"></i> Dashboard
                        </a>
                        <a href="{{ route('admin.profile.edit') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-zinc-500 rounded-xl hover:bg-zinc-50 hover:text-zinc-900 transition-all uppercase tracking-widest">
                            <i class="fas fa-user-circle w-4"></i> Profile
                        </a>
                    </div>

                    <div class="h-[1px] bg-zinc-100 my-2 mx-2"></div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-3 text-xs font-black text-rose-500 rounded-xl hover:bg-rose-50 transition-all uppercase tracking-widest text-left">
                            <i class="fas fa-sign-out-alt w-4"></i> Keluar System
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

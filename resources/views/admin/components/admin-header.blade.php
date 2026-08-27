<header id="admin-sticky-header" class="sticky top-0 z-30 w-full bg-gray-50/70 backdrop-blur-md pt-4 pb-2 transition-all duration-300">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            
            <!-- Left: Mobile Toggle & Page Title / Breadcrumb Context -->
            <div class="flex items-center gap-3">
                <button id="toggle-sidebar-btn"
                    class="lg:hidden flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200/80 text-slate-600 hover:text-slate-900 shadow-sm transition-all">
                    <i class="fas fa-bars text-base"></i>
                </button>

                <div class="hidden sm:flex flex-col">
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight leading-none">
                        @yield('title', 'Dashboard')
                    </h2>
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mt-1">
                        Lab RPL ITATS
                    </span>
                </div>
            </div>

            <!-- Right: System Online, Notification Brief & Profile Dropdown -->
            <div class="flex items-center gap-3">
                
                <!-- System Online Badge -->
                <span class="hidden md:inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200/80 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
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
                    <div class="absolute right-0 top-full mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
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
    });
</script>

@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Dashboard</h1>
                <p class="text-slate-500 mt-1 text-sm sm:text-base">Selamat datang kembali, {{ Auth::user()->name }}</p>
            </div>
            <div>
                <span
                    class="inline-flex items-center px-2.5 sm:px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                    System Online
                </span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Card 1 - Users -->
            <div class="bg-white p-4 sm:p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-xs sm:text-sm font-medium text-slate-500 uppercase tracking-widest font-bold">Total
                        Pengguna</h3>
                    <div class="p-1.5 sm:p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <i class="fas fa-users text-sm sm:text-base"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['users'] }}</span>
                    <span
                        class="text-[10px] sm:text-xs text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100 italic">{{ $stats['active_users'] }}
                        aktif</span>
                </div>
            </div>

            <!-- Card 2 - Praktikum -->
            <div
                class="bg-white p-4 sm:p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-xs sm:text-sm font-medium text-slate-500 uppercase tracking-widest font-bold">Praktikum
                    </h3>
                    <div
                        class="p-1.5 sm:p-2 bg-[#001f3f]/5 text-[#001f3f] rounded-lg group-hover:bg-[#001f3f] group-hover:text-white transition-colors">
                        <i class="fas fa-flask text-sm sm:text-base"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['praktikums'] }}</span>
                    <span
                        class="text-[10px] sm:text-xs text-amber-600 font-bold bg-amber-50 px-2 py-0.5 rounded-full border border-amber-100 italic">{{ $stats['active_praktikums'] }}
                        aktif</span>
                </div>
            </div>

            <!-- Card 3 - Praktikan -->
            <div class="bg-white p-4 sm:p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-xs sm:text-sm font-medium text-slate-500 uppercase tracking-widest font-bold">Praktikan
                    </h3>
                    <div class="p-1.5 sm:p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                        <i class="fas fa-user-graduate text-sm sm:text-base"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['praktikan'] }}</span>
                    <span
                        class="text-[10px] sm:text-xs text-slate-400 font-bold bg-slate-50 px-2 py-0.5 rounded-full border border-slate-100 italic">verified</span>
                </div>
            </div>

            <!-- Card 4 - Role Management -->
            <div class="bg-white p-4 sm:p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-xs sm:text-sm font-medium text-slate-500 uppercase tracking-widest font-bold">Hak Akses
                    </h3>
                    <div class="p-1.5 sm:p-2 bg-purple-50 text-purple-600 rounded-lg">
                        <i class="fas fa-shield-halved text-sm sm:text-base"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['roles'] }}</span>
                    <span
                        class="text-[10px] sm:text-xs text-slate-400 font-bold bg-slate-50 px-2 py-0.5 rounded-full border border-slate-100 italic">roles</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Activity Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mt-6">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm sm:text-base uppercase tracking-tight">Aktivitas Terbaru</h3>
            <span
                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-50 px-3 py-1 rounded-full border border-slate-100">Live
                Updates</span>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($activities as $activity)
                <!-- Activity Item -->
                <div
                    class="px-4 sm:px-6 py-4 flex items-start sm:items-center gap-4 hover:bg-slate-50 transition-all cursor-pointer group">
                    <div
                        class="w-10 h-10 rounded-xl {{ $activity->icon_bg }} flex items-center justify-center shrink-0 shadow-sm border border-white/50 group-hover:scale-110 transition-transform">
                        <i class="{{ $activity->icon }} text-sm"></i>
                    </div>
                    <div class="flex-grow min-w-0">
                        <p
                            class="text-sm font-bold text-slate-900 truncate group-hover:text-primary transition-colors uppercase tracking-tight">
                            {{ $activity->title }}</p>
                        <p class="text-[10px] text-slate-500 mt-1 font-medium">
                            Oleh <span class="font-bold text-slate-700">{{ $activity->user }}</span>
                            •
                            <span class="italic text-slate-400">{{ $activity->time->diffForHumans() }}</span>
                        </p>
                    </div>
                    <div class="shrink-0">
                        <span
                            class="px-3 py-1 rounded-full text-[9px] font-black {{ $activity->badge_color }} uppercase tracking-widest border">
                            {{ $activity->badge }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="py-12 flex flex-col items-center justify-center text-center">
                    <div
                        class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mb-4 border border-slate-100 italic">
                        <i class="fas fa-clock-rotate-left text-slate-200 text-2xl"></i>
                    </div>
                    <p class="text-slate-400 font-bold text-xs uppercase tracking-[0.2em]">Belum ada aktivitas terbaru hari
                        ini</p>
                </div>
            @endforelse
        </div>
    </div>
    </div>

    @if (session('success'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-lg border border-slate-100 shadow-lg'
                }
            });
        </script>
    @endif
@endsection

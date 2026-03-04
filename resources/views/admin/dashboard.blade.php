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
            <h3 class="font-bold text-slate-900 text-sm sm:text-base">Aktivitas Terbaru</h3>
            <button class="text-xs sm:text-sm text-primary font-medium hover:underline">Lihat Semua</button>
        </div>
        <div class="divide-y divide-slate-100">
            <!-- Item 1 -->
            <div
                class="px-4 sm:px-6 py-3 sm:py-4 flex items-start sm:items-center gap-3 sm:gap-4 hover:bg-slate-50 transition-colors cursor-pointer">
                <div
                    class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                </div>
                <div class="flex-grow min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-900 truncate">Postingan baru dibuat: "Open
                        Recruitment 2026"</p>
                    <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5">Oleh <span
                            class="font-medium text-slate-700">Dimas Admin</span>
                        •
                        2 jam yang lalu</p>
                </div>
                <div class="shrink-0">
                    <span
                        class="px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-[9px] sm:text-[10px] font-bold bg-green-100 text-green-700 border border-green-200 uppercase">Published</span>
                </div>
            </div>
            <!-- Item 2 -->
            <div
                class="px-4 sm:px-6 py-3 sm:py-4 flex items-start sm:items-center gap-3 sm:gap-4 hover:bg-slate-50 transition-colors cursor-pointer">
                <div
                    class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <div class="flex-grow min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-900 truncate">Percobaan login gagal terdeteksi
                    </p>
                    <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5">IP: 192.168.1.10 • 5 jam yang lalu</p>
                </div>
                <div class="shrink-0">
                    <span
                        class="px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-[9px] sm:text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200 uppercase">Security</span>
                </div>
            </div>
            <!-- Item 3 -->
            <div
                class="px-4 sm:px-6 py-3 sm:py-4 flex items-start sm:items-center gap-3 sm:gap-4 hover:bg-slate-50 transition-colors cursor-pointer">
                <div
                    class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-grow min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-900 truncate">Data anggota diperbarui</p>
                    <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5">Oleh <span
                            class="font-medium text-slate-700">Dimas Admin</span>
                        • 1 hari yang lalu</p>
                </div>
                <div class="shrink-0">
                    <span
                        class="px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-[9px] sm:text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200 uppercase">System</span>
                </div>
            </div>
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

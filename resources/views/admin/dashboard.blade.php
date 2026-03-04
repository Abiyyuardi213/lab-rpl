@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-black text-zinc-800 tracking-tight">Dashboard Utama</h1>
                <p class="text-sm font-medium text-zinc-400 mt-2">Selamat datang di Panel Admin Laboratorium RPL ITATS.</p>
            </div>
            <div class="flex flex-col items-end gap-2 text-xs font-semibold text-zinc-400">
                <div class="flex items-center gap-2">
                    <span>Home</span>
                    <i class="fas fa-chevron-right text-[8px]"></i>
                    <span class="text-zinc-600">Dashboard</span>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <i class="fas fa-users-gear text-xl"></i>
                    </div>
                    <span class="text-[10px] font-black text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg">ACTIVE</span>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-black text-zinc-800 tracking-tight">12</h3>
                    <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest mt-1">TOTAL PERAN</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <span class="text-[10px] font-black text-zinc-400 bg-zinc-50 px-2 py-1 rounded-lg">SYSTEM</span>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-black text-zinc-800 tracking-tight">48</h3>
                    <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest mt-1">TOTAL PENGGUNA</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Kirim Notifikasi Broadcast')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Kirim Notifikasi</h1>
            <p class="text-slate-500 mt-1 text-sm">Kirim pesan informasi ke Praktikan atau Asisten Laboratorium.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}"
            class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center">
        <i class="fas fa-check-circle mr-3"></i>
        <span class="text-sm font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center">
        <i class="fas fa-exclamation-triangle mr-3"></i>
        <span class="text-sm font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <form action="{{ route('admin.notifications.send') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Target Opsi -->
                <div>
                    <label for="target" class="block text-sm font-medium text-slate-700 mb-2">Target Penerima</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="relative flex cursor-pointer rounded-lg border border-slate-200 bg-white p-4 shadow-sm focus:outline-none hover:bg-slate-50">
                            <input type="radio" name="target" value="all" class="sr-only peer" checked>
                            <div class="peer-checked:ring-2 peer-checked:ring-[#1a4fa0] peer-checked:border-transparent absolute inset-0 rounded-lg pointer-events-none"></div>
                            <div class="flex-1 flex items-center">
                                <div class="w-10 h-10 bg-blue-50 text-[#1a4fa0] rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="flex-1 text-sm">
                                    <p class="font-bold text-slate-900">Semua</p>
                                    <p class="text-slate-500 text-xs">Praktikan & Aslab</p>
                                </div>
                            </div>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border border-slate-200 bg-white p-4 shadow-sm focus:outline-none hover:bg-slate-50">
                            <input type="radio" name="target" value="praktikan" class="sr-only peer">
                            <div class="peer-checked:ring-2 peer-checked:ring-[#1a4fa0] peer-checked:border-transparent absolute inset-0 rounded-lg pointer-events-none"></div>
                            <div class="flex-1 flex items-center">
                                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div class="flex-1 text-sm">
                                    <p class="font-bold text-slate-900">Praktikan</p>
                                    <p class="text-slate-500 text-xs">Hanya Mahasiswa</p>
                                </div>
                            </div>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border border-slate-200 bg-white p-4 shadow-sm focus:outline-none hover:bg-slate-50">
                            <input type="radio" name="target" value="aslab" class="sr-only peer">
                            <div class="peer-checked:ring-2 peer-checked:ring-[#1a4fa0] peer-checked:border-transparent absolute inset-0 rounded-lg pointer-events-none"></div>
                            <div class="flex-1 flex items-center">
                                <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-laptop-code"></i>
                                </div>
                                <div class="flex-1 text-sm">
                                    <p class="font-bold text-slate-900">Asisten Lab</p>
                                    <p class="text-slate-500 text-xs">Hanya Aslab</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('target')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Judul -->
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Judul Notifikasi</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Contoh: Pengingat Tugas Praktikum"
                        class="w-full rounded-lg border-slate-300 border px-4 py-2.5 text-sm focus:border-[#1a4fa0] focus:ring-[#1a4fa0] @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pesan -->
                <div>
                    <label for="message" class="block text-sm font-medium text-slate-700 mb-1">Isi Pesan</label>
                    <textarea name="message" id="message" rows="4" placeholder="Tulis pesan lengkap di sini..."
                        class="w-full rounded-lg border-slate-300 border px-4 py-2.5 text-sm focus:border-[#1a4fa0] focus:ring-[#1a4fa0] @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-[#1a4fa0] text-white rounded-lg font-semibold hover:bg-[#1a4fa0]/90 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Notifikasi Sekarang
                    </button>
                    <p class="text-center text-xs text-slate-400 mt-3"><i class="fas fa-info-circle mr-1"></i> Pesan akan langsung masuk ke Database (lonceng notifikasi user).</p>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

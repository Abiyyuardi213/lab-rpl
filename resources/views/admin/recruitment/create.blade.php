@extends('layouts.admin')

@section('title', 'Tambah Periode Rekrutmen')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Buat Periode Rekrutmen Baru</h1>
                <p class="text-sm text-zinc-500 mt-1">Buka pendaftaran asisten laboratorium baru untuk mahasiswa.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('admin.recruitment.index') }}" class="hover:text-zinc-900 transition-colors">Rekrutmen</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Tambah</span>
            </div>
        </div>

        <form action="{{ route('admin.recruitment.store') }}" method="POST"
            class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf

            <div class="lg:col-span-2 space-y-6">
                <!-- Main Form -->
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <div class="space-y-4">
                        <div class="grid gap-2">
                            <label for="title" class="text-sm font-bold text-zinc-900">Judul Periode</label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                class="flex h-10 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950 placeholder:text-zinc-400"
                                placeholder="Contoh: Open Recruitment Aslab Ganjil 2024">
                            @error('title')
                                <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-2">
                            <label for="description" class="text-sm font-bold text-zinc-900">Deskripsi (Opsional)</label>
                            <textarea name="description" id="description" rows="5"
                                class="flex w-full rounded-md border border-zinc-200 bg-transparent px-3 py-2 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950 placeholder:text-zinc-400"
                                placeholder="Jelaskan detail rekrutmen atau pengumuman di sini...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="grid gap-2">
                                <label for="start_date" class="text-sm font-bold text-zinc-900">Tanggal Mulai</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                                    class="flex h-10 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                            </div>
                            <div class="grid gap-2">
                                <label for="end_date" class="text-sm font-bold text-zinc-900">Tanggal Berakhir</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+14 days'))) }}" required
                                    class="flex h-10 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Sidebar Form -->
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-zinc-900 mb-4 border-b border-zinc-100 pb-2">Persyaratan Minimum</h3>

                    <div class="space-y-4">
                        <div class="grid gap-2">
                            <label for="min_ipk" class="text-xs font-bold text-zinc-500 uppercase">Minimum IPK</label>
                            <input type="number" step="0.01" id="min_ipk" name="min_ipk"
                                value="{{ old('min_ipk', '3.00') }}" required
                                class="flex h-10 w-full rounded-md border border-zinc-200 bg-white px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950 transition-all">
                            @error('min_ipk')
                                <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-2">
                            <label for="min_semester" class="text-xs font-bold text-zinc-500 uppercase">Minimum Semester</label>
                            <input type="number" id="min_semester" name="min_semester" value="{{ old('min_semester', '3') }}"
                                class="flex h-10 w-full rounded-md border border-zinc-200 bg-white px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950 transition-all">
                            @error('min_semester')
                                <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center space-x-2 py-2">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked
                                class="h-4 w-4 rounded border-zinc-300 text-[#1a4fa0] focus:ring-[#1a4fa0]">
                            <label for="is_active" class="text-sm font-medium text-zinc-700">Aktifkan Periode</label>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <button type="submit"
                        class="inline-flex h-11 items-center justify-center rounded-xl bg-[#1a4fa0] px-8 py-2 text-sm font-bold text-white shadow-lg shadow-[#1a4fa0]/25 hover:bg-[#1a4fa0]/90 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        Simpan Periode
                    </button>
                    <a href="{{ route('admin.recruitment.index') }}"
                        class="inline-flex h-11 items-center justify-center rounded-xl bg-zinc-100 px-8 py-2 text-sm font-bold text-zinc-600 hover:bg-zinc-200 transition-all">
                        Batalkan
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection


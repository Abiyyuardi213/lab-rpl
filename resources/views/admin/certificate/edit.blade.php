@extends('layouts.admin')

@section('title', 'Edit Sertifikat Praktikum')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Edit Sertifikat Praktikum</h1>
                <p class="text-sm text-zinc-500 mt-1">Perbarui arsip sertifikat untuk {{ $certificate->praktikum->nama_praktikum }} ({{ $certificate->praktikum->periode_praktikum }}).</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('admin.certificate.index') }}" class="hover:text-zinc-900 transition-colors">Sertifikat</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Edit</span>
            </div>
        </div>

        <!-- Form Card Container -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden p-6">
            <form action="{{ route('admin.certificate.update', $certificate->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700">Prefix Kode Nomor Sertifikat <span class="text-rose-500">*</span></label>
                        <input type="text" name="nomor_surat_prefix" value="{{ old('nomor_surat_prefix', $certificate->nomor_surat_prefix) }}" required
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-xs shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700">Tanggal Resmi Sertifikat <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_sertifikat" value="{{ old('tanggal_sertifikat', $certificate->tanggal_sertifikat->format('Y-m-d')) }}" required
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-xs shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                    </div>
                </div>

                <!-- Penandatangan 1 -->
                <div class="space-y-3 pt-4 border-t border-zinc-100">
                    <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Penandatangan 1: Kepala Laboratorium</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-zinc-600">Nama Kepala Lab</label>
                            <input type="text" name="nama_kepala_lab" value="{{ old('nama_kepala_lab', $certificate->nama_kepala_lab) }}" required class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-xs shadow-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-zinc-600">NIP Kepala Lab</label>
                            <input type="text" name="nip_kepala_lab" value="{{ old('nip_kepala_lab', $certificate->nip_kepala_lab) }}" class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-xs shadow-sm">
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-1">
                        @if($certificate->ttd_kepala_lab)
                            <div class="h-14 w-20 p-1 border border-zinc-200 rounded-md bg-zinc-50 flex items-center justify-center shrink-0">
                                <img src="{{ asset('storage/' . $certificate->ttd_kepala_lab) }}" class="max-h-full max-w-full object-contain">
                            </div>
                        @endif
                        <input type="file" name="ttd_kepala_lab" accept="image/png,image/jpeg" class="text-xs text-zinc-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border file:border-zinc-200 file:bg-zinc-50 file:text-xs">
                    </div>
                </div>

                <!-- Penandatangan 2 -->
                <div class="space-y-3 pt-4 border-t border-zinc-100">
                    <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Penandatangan 2: Kepala Program Studi (Kaprodi)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-zinc-600">Nama Kaprodi</label>
                            <input type="text" name="nama_kaprodi" value="{{ old('nama_kaprodi', $certificate->nama_kaprodi) }}" required class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-xs shadow-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-zinc-600">NIP Kaprodi</label>
                            <input type="text" name="nip_kaprodi" value="{{ old('nip_kaprodi', $certificate->nip_kaprodi) }}" class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-xs shadow-sm">
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-1">
                        @if($certificate->ttd_kaprodi)
                            <div class="h-14 w-20 p-1 border border-zinc-200 rounded-md bg-zinc-50 flex items-center justify-center shrink-0">
                                <img src="{{ asset('storage/' . $certificate->ttd_kaprodi) }}" class="max-h-full max-w-full object-contain">
                            </div>
                        @endif
                        <input type="file" name="ttd_kaprodi" accept="image/png,image/jpeg" class="text-xs text-zinc-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border file:border-zinc-200 file:bg-zinc-50 file:text-xs">
                    </div>
                </div>

                <!-- Template Background -->
                <div class="space-y-2 pt-4 border-t border-zinc-100">
                    <label class="text-xs font-semibold text-zinc-700">Template Background Gambar (PNG/JPG Kustom)</label>
                    <div class="flex items-center gap-4">
                        @if($certificate->bg_template)
                            <div class="h-16 w-24 p-1 border border-zinc-200 rounded-md bg-zinc-50 flex items-center justify-center shrink-0">
                                <img src="{{ asset('storage/' . $certificate->bg_template) }}" class="max-h-full max-w-full object-contain">
                            </div>
                        @endif
                        <input type="file" name="bg_template" accept="image/png,image/jpeg" class="text-xs text-zinc-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border file:border-zinc-200 file:bg-zinc-50 file:text-xs">
                    </div>
                </div>

                <div class="pt-4 border-t border-zinc-100 flex items-center justify-end gap-2">
                    <a href="{{ route('admin.certificate.index') }}"
                        class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 py-2 text-xs font-medium text-zinc-700 shadow-sm hover:bg-zinc-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-4 py-2 text-xs font-medium text-white shadow hover:bg-[#002d5a] transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

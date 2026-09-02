@extends('layouts.admin')

@section('title', 'Pengaturan Laboratorium & TTD Sertifikat')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Pengaturan Laboratorium</h1>
                <p class="text-sm text-zinc-500">Kelola informasi pejabat & tanda tangan (TTD) untuk sertifikat kelulusan praktikum.</p>
            </div>
        </div>

        <form action="{{ route('admin.laboratorium.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden p-6 space-y-8">
                <!-- Section 1: Kepala Laboratorium -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-zinc-100">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#001f3f] flex items-center justify-center font-bold text-xs">
                            <i class="fas fa-[#001f3f] fa-user-tie"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-zinc-900">Kepala Laboratorium</h2>
                            <p class="text-xs text-zinc-500">Penandatangan 1 pada sertifikat praktikum</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-zinc-700">Nama Kepala Lab <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_kepala_lab" value="{{ old('nama_kepala_lab', $setting->nama_kepala_lab) }}" required
                                class="w-full px-3.5 py-2 border border-zinc-200 rounded-xl text-xs font-medium focus:outline-none focus:border-[#001f3f]">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-zinc-700">NIP / NPT Kepala Lab</label>
                            <input type="text" name="nip_kepala_lab" value="{{ old('nip_kepala_lab', $setting->nip_kepala_lab) }}"
                                class="w-full px-3.5 py-2 border border-zinc-200 rounded-xl text-xs font-medium focus:outline-none focus:border-[#001f3f]">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-zinc-700">Upload TTD Kepala Lab (PNG Transparan recommended)</label>
                        <div class="flex items-center gap-4">
                            @if($setting->ttd_kepala_lab)
                                <div class="w-24 h-24 p-2 border border-zinc-200 rounded-xl bg-zinc-50 flex items-center justify-center shrink-0">
                                    <img src="{{ asset('storage/' . $setting->ttd_kepala_lab) }}" class="max-h-full max-w-full object-contain">
                                </div>
                            @endif
                            <input type="file" name="ttd_kepala_lab" accept="image/png,image/jpeg"
                                class="text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-[#001f3f] hover:file:bg-blue-100">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Ketua Program Studi (Kaprodi) -->
                <div class="space-y-4 pt-4 border-t border-zinc-100">
                    <div class="flex items-center gap-3 pb-3 border-b border-zinc-100">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-zinc-900">Ketua Program Studi (Kaprodi)</h2>
                            <p class="text-xs text-zinc-500">Penandatangan 2 pada sertifikat praktikum</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-zinc-700">Nama Kaprodi <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_kaprodi" value="{{ old('nama_kaprodi', $setting->nama_kaprodi) }}" required
                                class="w-full px-3.5 py-2 border border-zinc-200 rounded-xl text-xs font-medium focus:outline-none focus:border-[#001f3f]">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-zinc-700">NIP / NPT Kaprodi</label>
                            <input type="text" name="nip_kaprodi" value="{{ old('nip_kaprodi', $setting->nip_kaprodi) }}"
                                class="w-full px-3.5 py-2 border border-zinc-200 rounded-xl text-xs font-medium focus:outline-none focus:border-[#001f3f]">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-zinc-700">Upload TTD Kaprodi (PNG Transparan recommended)</label>
                        <div class="flex items-center gap-4">
                            @if($setting->ttd_kaprodi)
                                <div class="w-24 h-24 p-2 border border-zinc-200 rounded-xl bg-zinc-50 flex items-center justify-center shrink-0">
                                    <img src="{{ asset('storage/' . $setting->ttd_kaprodi) }}" class="max-h-full max-w-full object-contain">
                                </div>
                            @endif
                            <input type="file" name="ttd_kaprodi" accept="image/png,image/jpeg"
                                class="text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Desain Template Sertifikat & Format Nomor -->
                <div class="space-y-4 pt-4 border-t border-zinc-100">
                    <div class="flex items-center gap-3 pb-3 border-b border-zinc-100">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xs">
                            <i class="fas fa-file-image"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-zinc-900">Desain Template Sertifikat Kustom</h2>
                            <p class="text-xs text-zinc-500">Upload desain sertifikat mentah (format gambar .PNG / .JPG). Sistem akan memposisikan nama praktikan, judul praktikum, dan TTD secara presisi di atasnya.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-zinc-700">Upload Desain Sertifikat Background</label>
                            <div class="flex items-center gap-4">
                                @if($setting->bg_sertifikat_template)
                                    <div class="w-32 h-20 p-1 border border-zinc-200 rounded-xl bg-zinc-50 flex items-center justify-center shrink-0">
                                        <img src="{{ asset('storage/' . $setting->bg_sertifikat_template) }}" class="max-h-full max-w-full object-contain">
                                    </div>
                                @endif
                                <input type="file" name="bg_sertifikat_template" accept="image/png,image/jpeg"
                                    class="text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                            </div>
                            <p class="text-[10px] text-zinc-400 italic">Disarankan gambar resolusi tinggi orientasi Landscape (rasio 4:3 / A4).</p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-zinc-700">Prefix Kode Nomor Sertifikat</label>
                            <input type="text" name="nomor_surat_prefix" value="{{ old('nomor_surat_prefix', $setting->nomor_surat_prefix) }}" placeholder="e.g., LAB-RPL/SERT"
                                class="w-full px-3.5 py-2 border border-zinc-200 rounded-xl text-xs font-medium focus:outline-none focus:border-[#001f3f]">
                            <p class="text-[10px] text-zinc-400 italic">Format penomoran otomatis: [NOMOR]/[PREFIX]/[BULAN]/[TAHUN]</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-zinc-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-[#001f3f] text-white rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-[#002d5a] transition-all shadow-md">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

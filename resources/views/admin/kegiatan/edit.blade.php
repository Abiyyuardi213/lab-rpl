@extends('layouts.admin')

@section('title', 'Edit Kegiatan')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Ubah Laporan Kegiatan</h1>
                <p class="text-sm text-zinc-500 mt-1">Perbarui report kegiatan yang sudah ada.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('admin.kegiatan.index') }}" class="hover:text-zinc-900 transition-colors">Kegiatan</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Edit</span>
            </div>
        </div>

        <form action="{{ route('admin.kegiatan.update', $kegiatan->id) }}" method="POST" enctype="multipart/form-data"
            class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf
            @method('PUT')

            <div class="lg:col-span-2 space-y-6">
                <!-- Main Form -->
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <div class="space-y-4">
                        <div class="grid gap-2">
                            <label for="judul" class="text-sm font-bold text-zinc-900">Judul Kegiatan</label>
                            <input type="text" id="judul" name="judul" value="{{ old('judul', $kegiatan->judul) }}"
                                required
                                class="flex h-10 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950 placeholder:text-zinc-400"
                                placeholder="Masukkan judul kegiatan...">
                            @error('judul')
                                <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-2">
                            <label for="konten" class="text-sm font-bold text-zinc-900">Isi Laporan / Artikel</label>
                            <input id="konten" type="hidden" name="konten"
                                value="{{ old('konten', $kegiatan->konten) }}">
                            <trix-editor input="konten"
                                class="trix-content prose prose-sm max-w-none min-h-[400px] rounded-md border border-zinc-200 bg-transparent px-3 py-2 text-sm shadow-sm transition-colors focus:ring-1 focus:ring-zinc-950 placeholder:text-zinc-400"
                                placeholder="Tuliskan detail kegiatan di sini..."></trix-editor>
                            @error('konten')
                                <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Sidebar Form -->
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-zinc-900 mb-4 border-b border-zinc-100 pb-2">Informasi Tambahan</h3>

                    <div class="space-y-4">
                        <div class="grid gap-2">
                            <label for="tanggal_kegiatan" class="text-xs font-bold text-zinc-500 uppercase">Tanggal
                                Kegiatan</label>
                            <input type="date" id="tanggal_kegiatan" name="tanggal_kegiatan"
                                value="{{ old('tanggal_kegiatan', $kegiatan->tanggal_kegiatan->format('Y-m-d')) }}" required
                                class="flex h-10 w-full rounded-md border border-zinc-200 bg-white px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950 transition-all">
                            @error('tanggal_kegiatan')
                                <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-2">
                            <label for="lokasi" class="text-xs font-bold text-zinc-500 uppercase">Lokasi</label>
                            <input type="text" id="lokasi" name="lokasi"
                                value="{{ old('lokasi', $kegiatan->lokasi) }}"
                                class="flex h-10 w-full rounded-md border border-zinc-200 bg-white px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950 transition-all"
                                placeholder="Contoh: Lab RPL, Gedung B">
                            @error('lokasi')
                                <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center space-x-2 py-2">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $kegiatan->is_active) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-zinc-300 text-[#1a4fa0] focus:ring-[#1a4fa0]">
                            <label for="is_active" class="text-sm font-medium text-zinc-700">Aktif / Publikasikan</label>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-zinc-900 mb-4 border-b border-zinc-100 pb-2">Gambar Dokumentasi</h3>
                    <div class="grid gap-4">
                        <div class="flex flex-col items-center justify-center border-2 border-dashed border-zinc-200 rounded-xl p-4 transition-all hover:bg-zinc-50/50 group @if ($kegiatan->gambar) hidden @endif"
                            id="upload-container">
                            <i
                                class="fas fa-camera text-3xl text-zinc-300 mb-3 group-hover:text-zinc-400 transition-colors"></i>
                            <div class="text-center">
                                <label for="gambar"
                                    class="relative cursor-pointer rounded-md bg-white font-bold text-[#1a4fa0] focus-within:outline-none hover:text-[#1a4fa0]/80">
                                    <span>Ganti Gambar</span>
                                    <input id="gambar" name="gambar" type="file" class="sr-only"
                                        onchange="previewImage(event)">
                                </label>
                            </div>
                        </div>
                        <div id="image-preview"
                            class="@if (!$kegiatan->gambar) hidden @endif relative rounded-xl overflow-hidden aspect-video border border-zinc-200">
                            <img src="{{ $kegiatan->gambar ? asset('storage/' . $kegiatan->gambar) : '' }}"
                                class="w-full h-full object-cover">
                            <button type="button" onclick="removeImage()"
                                class="absolute top-2 right-2 bg-rose-500 text-white p-1.5 rounded-full hover:bg-rose-600 shadow-lg shadow-rose-500/20">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <button type="submit"
                        class="inline-flex h-11 items-center justify-center rounded-xl bg-[#1a4fa0] px-8 py-2 text-sm font-bold text-white shadow-lg shadow-[#1a4fa0]/25 hover:bg-[#1a4fa0]/90 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.kegiatan.index') }}"
                        class="inline-flex h-11 items-center justify-center rounded-xl bg-zinc-100 px-8 py-2 text-sm font-bold text-zinc-600 hover:bg-zinc-200 transition-all">
                        Batalkan
                    </a>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <style>
            trix-toolbar [data-trix-button-group="file-tools"] {
                display: none;
            }

            trix-editor {
                outline: none !important;
            }

            .trix-button--icon-attach {
                display: none !important;
            }
        </style>
        <script>
            function previewImage(event) {
                const reader = new FileReader();
                reader.onload = function() {
                    const output = document.querySelector('#image-preview img');
                    output.src = reader.result;
                    document.getElementById('image-preview').classList.remove('hidden');
                    document.getElementById('upload-container').classList.add('hidden');
                };
                reader.readAsDataURL(event.target.files[0]);
            }

            function removeImage() {
                document.getElementById('gambar').value = "";
                document.getElementById('image-preview').classList.add('hidden');
                document.getElementById('upload-container').classList.remove('hidden');
            }
        </script>
    @endpush
@endsection

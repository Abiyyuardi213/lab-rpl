@extends('layouts.admin')

@section('title', 'Tambah Pengumuman')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Buat Pengumuman Baru</h1>
                <p class="text-sm text-zinc-500 mt-1">Publikasikan informasi atau berita ke laman publik.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('admin.pengumuman.index') }}" class="hover:text-zinc-900 transition-colors">Pengumuman</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Tambah</span>
            </div>
        </div>

        <form action="{{ route('admin.pengumuman.store') }}" method="POST" enctype="multipart/form-data"
            class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf

            <div class="lg:col-span-2 space-y-6">
                <!-- Main Form -->
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <div class="space-y-4">
                        <div class="grid gap-2">
                            <label for="judul" class="text-sm font-bold text-zinc-900">Judul Pengumuman</label>
                            <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required
                                class="flex h-10 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950 placeholder:text-zinc-400"
                                placeholder="Masukkan judul yang menarik...">
                            @error('judul')
                                <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-2">
                            <label for="konten" class="text-sm font-bold text-zinc-900">Isi Pengumuman</label>
                            <input id="konten" type="hidden" name="konten" value="{{ old('konten') }}">
                            <trix-editor input="konten"
                                class="trix-content prose prose-sm max-w-none min-h-[400px] rounded-md border border-zinc-200 bg-transparent px-3 py-2 text-sm shadow-sm transition-colors focus:ring-1 focus:ring-zinc-950 placeholder:text-zinc-400"
                                placeholder="Tuliskan isi pengumuman secara detail di sini..."></trix-editor>
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
                    <h3 class="text-sm font-bold text-zinc-900 mb-4 border-b border-zinc-100 pb-2">Status & Kategori</h3>

                    <div class="space-y-4">
                        <div class="grid gap-2">
                            <label for="kategori" class="text-xs font-bold text-zinc-500 uppercase">Kategori</label>
                            <select id="kategori" name="kategori" required
                                class="flex h-10 w-full rounded-md border border-zinc-200 bg-white px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950 transition-all">
                                <option value="umum" {{ old('kategori') == 'umum' ? 'selected' : '' }}>Umum / Berita
                                </option>
                                <option value="praktikum" {{ old('kategori') == 'praktikum' ? 'selected' : '' }}>Informasi
                                    Praktikum</option>
                                <option value="kegiatan" {{ old('kategori') == 'kegiatan' ? 'selected' : '' }}>Kegiatan Lab
                                </option>
                            </select>
                            @error('kategori')
                                <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center space-x-2 py-2">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked
                                class="h-4 w-4 rounded border-zinc-300 text-[#1a4fa0] focus:ring-[#1a4fa0]">
                            <label for="is_active" class="text-sm font-medium text-zinc-700">Aktif / Publikasikan</label>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-zinc-900 mb-4 border-b border-zinc-100 pb-2">Gambar Sampul</h3>
                    <div class="grid gap-4">
                        <div class="flex flex-col items-center justify-center border-2 border-dashed border-zinc-200 rounded-xl p-4 transition-all hover:bg-zinc-50/50 group"
                            id="upload-container">
                            <i
                                class="fas fa-image text-3xl text-zinc-300 mb-3 group-hover:text-zinc-400 transition-colors"></i>
                            <div class="text-center">
                                <label for="gambar"
                                    class="relative cursor-pointer rounded-md bg-white font-bold text-[#1a4fa0] focus-within:outline-none hover:text-[#1a4fa0]/80">
                                    <span>Unggah Gambar</span>
                                    <input id="gambar" name="gambar" type="file" class="sr-only"
                                        onchange="previewImage(event)">
                                </label>
                                <p class="text-[10px] text-zinc-400 mt-1">PNG, JPG, JPEG sampai 2MB</p>
                            </div>
                        </div>
                        <div id="image-preview"
                            class="hidden relative rounded-xl overflow-hidden aspect-video border border-zinc-200">
                            <img src="" class="w-full h-full object-cover">
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
                        Simpan Pengumuman
                    </button>
                    <a href="{{ route('admin.pengumuman.index') }}"
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

@extends('layouts.admin', ['title' => 'Manajemen Portfolio'])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Portfolio Anda</h1>
            <p class="text-sm text-zinc-500 mt-1">Kelola informasi publik yang akan ditampilkan di halaman portfolio anda.</p>
        </div>
        <a href="{{ route('aslab.portfolio', $aslab->slug ?? 'not-set') }}" target="_blank" class="inline-flex h-9 items-center justify-center rounded-lg bg-zinc-900 px-4 text-xs font-bold text-white shadow-lg transition-all hover:bg-zinc-800 active:scale-95">
            <i class="fas fa-external-link-alt mr-2"></i> Lihat Portfolio
        </a>
    </div>

    <form action="{{ route('aslab.portfolio.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-zinc-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-user-edit text-primary text-sm"></i>
                        Biodata & Sosial Media
                    </h3>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label for="bio" class="text-sm font-bold text-zinc-700 uppercase tracking-tight">Bio Singkat</label>
                            <textarea name="bio" id="bio" rows="4" 
                                class="flex w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                                placeholder="Ceritakan sedikit tentang diri anda...">{{ old('bio', $aslab->bio) }}</textarea>
                            @error('bio')
                                <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="instagram_link" class="text-sm font-bold text-zinc-700 uppercase tracking-tight">Instagram URL</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                                        <i class="fab fa-instagram text-xs"></i>
                                    </div>
                                    <input type="url" name="instagram_link" id="instagram_link" value="{{ old('instagram_link', $aslab->instagram_link) }}"
                                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 pl-8 pr-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                                        placeholder="https://instagram.com/username">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label for="github_link" class="text-sm font-bold text-zinc-700 uppercase tracking-tight">GitHub URL</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                                        <i class="fab fa-github text-xs"></i>
                                    </div>
                                    <input type="url" name="github_link" id="github_link" value="{{ old('github_link', $aslab->github_link) }}"
                                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 pl-8 pr-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                                        placeholder="https://github.com/username">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="linkedin_link" class="text-sm font-bold text-zinc-700 uppercase tracking-tight">LinkedIn URL</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                                    <i class="fab fa-linkedin text-xs"></i>
                                </div>
                                <input type="url" name="linkedin_link" id="linkedin_link" value="{{ old('linkedin_link', $aslab->linkedin_link) }}"
                                    class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 pl-8 pr-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                                    placeholder="https://linkedin.com/in/username">
                            </div>
                        </div>

                        <div class="pt-6 border-t border-zinc-100 space-y-10">
                            {{-- Prestasi --}}
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-bold text-zinc-700 uppercase tracking-tight">Prestasi & Penghargaan</label>
                                    <button type="button" onclick="addRow('achievements-container', 'achievements')" class="text-[10px] font-bold text-[#1a4fa0] hover:underline uppercase">+ Tambah Baris</button>
                                </div>
                                <div id="achievements-container" class="space-y-3">
                                    @forelse($aslab->achievements as $item)
                                        <div class="flex gap-2 items-start group">
                                            <input type="text" name="achievements[][name]" value="{{ $item->name }}" placeholder="Nama Prestasi" class="flex-1 h-10 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm focus:bg-white focus:ring-2 focus:ring-[#1a4fa0]/10 outline-none">
                                            <input type="text" name="achievements[][year]" value="{{ $item->year }}" placeholder="Tahun" class="w-24 h-10 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm focus:bg-white focus:ring-2 focus:ring-[#1a4fa0]/10 outline-none">
                                            <button type="button" onclick="removeRow(this)" class="h-10 w-10 flex items-center justify-center text-zinc-300 hover:text-red-500 transition-colors"><i class="fas fa-times-circle"></i></button>
                                        </div>
                                    @empty
                                        <div class="flex gap-2 items-start group">
                                            <input type="text" name="achievements[][name]" placeholder="Nama Prestasi" class="flex-1 h-10 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm focus:bg-white focus:ring-2 focus:ring-[#1a4fa0]/10 outline-none">
                                            <input type="text" name="achievements[][year]" placeholder="Tahun" class="w-24 h-10 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm focus:bg-white focus:ring-2 focus:ring-[#1a4fa0]/10 outline-none">
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Pengalaman --}}
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-bold text-zinc-700 uppercase tracking-tight">Pengalaman Organisasi</label>
                                    <button type="button" onclick="addRow('experience-container', 'experience')" class="text-[10px] font-bold text-[#1a4fa0] hover:underline uppercase">+ Tambah Baris</button>
                                </div>
                                <div id="experience-container" class="space-y-3">
                                    @forelse($aslab->experiences as $item)
                                        <div class="flex gap-2 items-start group">
                                            <input type="text" name="experience[][name]" value="{{ $item->name }}" placeholder="Pengalaman/Organisasi" class="flex-1 h-10 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm focus:bg-white focus:ring-2 focus:ring-[#1a4fa0]/10 outline-none">
                                            <input type="text" name="experience[][year]" value="{{ $item->year }}" placeholder="Tahun" class="w-24 h-10 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm focus:bg-white focus:ring-2 focus:ring-[#1a4fa0]/10 outline-none">
                                            <button type="button" onclick="removeRow(this)" class="h-10 w-10 flex items-center justify-center text-zinc-300 hover:text-red-500 transition-colors"><i class="fas fa-times-circle"></i></button>
                                        </div>
                                    @empty
                                        <div class="flex gap-2 items-start group">
                                            <input type="text" name="experience[][name]" placeholder="Pengalaman/Organisasi" class="flex-1 h-10 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm focus:bg-white focus:ring-2 focus:ring-[#1a4fa0]/10 outline-none">
                                            <input type="text" name="experience[][year]" placeholder="Tahun" class="w-24 h-10 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm focus:bg-white focus:ring-2 focus:ring-[#1a4fa0]/10 outline-none">
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Kegiatan --}}
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-bold text-zinc-700 uppercase tracking-tight">Kegiatan Prodi/Kampus</label>
                                    <button type="button" onclick="addRow('activities-container', 'activities')" class="text-[10px] font-bold text-[#1a4fa0] hover:underline uppercase">+ Tambah Baris</button>
                                </div>
                                <div id="activities-container" class="space-y-3">
                                    @forelse($aslab->activities as $item)
                                        <div class="flex gap-2 items-start group">
                                            <input type="text" name="activities[][name]" value="{{ $item->name }}" placeholder="Nama Kegiatan" class="flex-1 h-10 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm focus:bg-white focus:ring-2 focus:ring-[#1a4fa0]/10 outline-none">
                                            <input type="text" name="activities[][year]" value="{{ $item->year }}" placeholder="Tahun" class="w-24 h-10 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm focus:bg-white focus:ring-2 focus:ring-[#1a4fa0]/10 outline-none">
                                            <button type="button" onclick="removeRow(this)" class="h-10 w-10 flex items-center justify-center text-zinc-300 hover:text-red-500 transition-colors"><i class="fas fa-times-circle"></i></button>
                                        </div>
                                    @empty
                                        <div class="flex gap-2 items-start group">
                                            <input type="text" name="activities[][name]" placeholder="Nama Kegiatan" class="flex-1 h-10 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm focus:bg-white focus:ring-2 focus:ring-[#1a4fa0]/10 outline-none">
                                            <input type="text" name="activities[][year]" placeholder="Tahun" class="w-24 h-10 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm focus:bg-white focus:ring-2 focus:ring-[#1a4fa0]/10 outline-none">
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-zinc-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-brain text-amber-500 text-sm"></i>
                        Keahlian (Skills)
                    </h3>
                    
                    <div id="skills-container" class="space-y-3">
                        @php
                            $skills = old('skills', $aslab->skills ?? []);
                        @endphp
                        @if(count($skills) > 0)
                            @foreach($skills as $skill)
                                <div class="flex gap-2 skill-item">
                                    <input type="text" name="skills[]" value="{{ $skill }}"
                                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                                        placeholder="Contoh: Laravel, React, UI/UX">
                                    <button type="button" onclick="this.parentElement.remove()" class="h-10 w-10 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="flex gap-2 skill-item">
                                <input type="text" name="skills[]"
                                    class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                                    placeholder="Contoh: Laravel, React, UI/UX">
                                <button type="button" onclick="this.parentElement.remove()" class="h-10 w-10 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" onclick="addSkill()" class="mt-4 inline-flex items-center gap-2 text-xs font-bold text-[#1a4fa0] hover:underline">
                        <i class="fas fa-plus"></i> Tambah Keahlian
                    </button>
                </div>

                <div class="flex items-center justify-end pt-4">
                    <button type="submit" class="inline-flex h-12 items-center justify-center rounded-xl bg-[#001f3f] px-10 text-sm font-bold text-white shadow-xl shadow-[#001f3f]/20 transition-all hover:bg-[#002d5a] active:scale-95">
                        Simpan Perubahan Portfolio
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Premium Card Image (Can also be updated by aslab) --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-zinc-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-star text-amber-500 text-sm"></i>
                        Card Image
                    </h3>
                    <p class="text-[10px] text-zinc-500 mb-6 italic leading-tight">Ini adalah gambar banner yang akan tampil di halaman utama (Rasio 4:5 portrait direkomendasikan).</p>

                    <div class="flex flex-col items-center gap-6">
                        <div id="preview-container-premium"
                            class="relative h-60 w-48 rounded-none border-2 {{ $aslab->profile_image ? 'border-solid border-[#001f3f]/10' : 'border-dashed border-zinc-200' }} bg-zinc-50/50 flex items-center justify-center overflow-hidden group transition-all hover:border-[#001f3f]/20 hover:bg-white">
                            <div id="placeholder-text-premium"
                                class="{{ $aslab->profile_image ? 'hidden' : '' }} text-center p-4">
                                <i class="fas fa-upload text-3xl text-zinc-300 mb-2 group-hover:scale-110 transition-transform"></i>
                                <p class="text-[10px] font-semibold text-zinc-400 uppercase tracking-tighter text-center">Premium Banner</p>
                            </div>
                            @if ($aslab->profile_image)
                                <img id="image-preview-premium" src="{{ asset('storage/' . $aslab->profile_image) }}"
                                    class="h-full w-full object-cover">
                            @else
                                <img id="image-preview-premium" class="hidden h-full w-full object-cover">
                            @endif

                            <button type="button" onclick="removeImagePremium()" id="remove-btn-premium"
                                class="{{ $aslab->profile_image ? 'flex scale-100' : 'hidden scale-0' }} absolute top-2 right-2 h-7 w-7 rounded-lg bg-rose-500 text-white items-center justify-center shadow-lg hover:bg-rose-600 transition-all active:scale-90 origin-center">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                        </div>

                        <div class="w-full space-y-3">
                            <input type="file" name="profile_image" id="profile_image" class="hidden"
                                accept="image/*" onchange="previewImagePremium(this)">
                            <button type="button" onclick="document.getElementById('profile_image').click()"
                                class="w-full inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-amber-200 bg-amber-50/30 px-4 text-sm font-bold text-amber-900 shadow-sm transition-all hover:bg-amber-50 active:scale-95">
                                <i class="fas fa-image text-xs"></i>
                                Ganti Gambar Card
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-blue-50 border border-blue-100 p-5">
                    <h4 class="text-sm font-bold text-blue-800 flex items-center gap-2 mb-2 uppercase tracking-wider">
                        <i class="fas fa-info-circle"></i>
                        Tips Portfolio
                    </h4>
                    <p class="text-xs text-blue-700 leading-relaxed">
                        Data ini akan dilihat oleh publik dan mahasiswa praktikan. Gunakan bahasa yang sopan dan profesional untuk mencerminkan identitas laboratorium kita.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function addSkill() {
        const container = document.getElementById('skills-container');
        const div = document.createElement('div');
        div.className = 'flex gap-2 skill-item animate-in slide-in-from-left-2';
        div.innerHTML = `
            <input type="text" name="skills[]"
                class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                placeholder="Contoh: Laravel, React, UI/UX">
            <button type="button" onclick="this.parentElement.remove()" class="h-10 w-10 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(div);
    }

    function previewImagePremium(input) {
        const preview = document.getElementById('image-preview-premium');
        const placeholder = document.getElementById('placeholder-text-premium');
        const container = document.getElementById('preview-container-premium');
        const removeBtn = document.getElementById('remove-btn-premium');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                container.classList.remove('border-dashed');
                container.classList.add('border-solid');

                removeBtn.classList.remove('hidden', 'scale-0');
                removeBtn.classList.add('flex', 'scale-100');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImagePremium() {
        const preview = document.getElementById('image-preview-premium');
        const placeholder = document.getElementById('placeholder-text-premium');
        const container = document.getElementById('preview-container-premium');
        const input = document.getElementById('profile_image');
        const removeBtn = document.getElementById('remove-btn-premium');

        input.value = '';
        preview.src = '';
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
        container.classList.add('border-dashed');
        container.classList.remove('border-solid');

        removeBtn.classList.add('scale-0');
        setTimeout(() => {
            removeBtn.classList.add('hidden');
            removeBtn.classList.remove('flex');
        }, 200);
    }
    function addRow(containerId, inputName) {
        const container = document.getElementById(containerId);
        const div = document.createElement('div');
        div.className = 'flex gap-2 items-start group animate-in slide-in-from-left-2';
        div.innerHTML = `
            <input type="text" name="${inputName}[][name]" placeholder="Nama/Keterangan" class="flex-1 h-10 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm focus:bg-white focus:ring-2 focus:ring-[#1a4fa0]/10 outline-none">
            <input type="text" name="${inputName}[][year]" placeholder="Tahun" class="w-24 h-10 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm focus:bg-white focus:ring-2 focus:ring-[#1a4fa0]/10 outline-none">
            <button type="button" onclick="removeRow(this)" class="h-10 w-10 flex items-center justify-center text-zinc-300 hover:text-red-500 transition-colors">
                <i class="fas fa-times-circle"></i>
            </button>
        `;
        container.appendChild(div);
    }

    function removeRow(btn) {
        btn.parentElement.remove();
        
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });

        Toast.fire({
            icon: 'success',
            title: 'Baris berhasil dihapus'
        });
    }
</script>
@endpush
@endsection

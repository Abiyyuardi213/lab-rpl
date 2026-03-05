@extends('layouts.admin', ['title' => 'Edit Aslab'])

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.aslab.index') }}" data-spa
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-500 shadow-sm hover:bg-zinc-100 hover:text-zinc-900 transition-colors">
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Edit Aslab</h1>
                    <p class="text-sm text-zinc-500 mt-1">Perbarui informasi asisten laboratorium yang sudah terdaftar.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.aslab.update', $aslab->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Form Details -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-zinc-900 mb-6 flex items-center gap-2">
                            <i class="fas fa-user-pen text-primary text-sm"></i>
                            Informasi Akun
                        </h3>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label for="username"
                                        class="text-sm font-bold text-zinc-700 uppercase tracking-tight flex items-center justify-between">
                                        Username
                                        <span
                                            class="text-[10px] font-medium text-zinc-400 normal-case italic">Opsional</span>
                                    </label>
                                    <input type="text" name="username" id="username"
                                        value="{{ old('username', $aslab->username) }}"
                                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                                        placeholder="Kosongkan jika sama dengan NPM">
                                    @error('username')
                                        <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="npm"
                                        class="text-sm font-bold text-zinc-700 uppercase tracking-tight">NPM</label>
                                    <input type="text" name="npm" id="npm"
                                        value="{{ old('npm', $aslab->npm) }}"
                                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                                        placeholder="06.2024.1.XXXXX" required>
                                    @error('npm')
                                        <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label for="name"
                                        class="text-sm font-bold text-zinc-700 uppercase tracking-tight">Nama
                                        Lengkap</label>
                                    <input type="text" name="name" id="name"
                                        value="{{ old('name', $aslab->name) }}"
                                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                                        placeholder="Masukan nama lengkap" required>
                                    @error('name')
                                        <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="email"
                                        class="text-sm font-bold text-zinc-700 uppercase tracking-tight">Email</label>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $aslab->email) }}"
                                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                                        placeholder="aslab@itats.ac.id" required>
                                    @error('email')
                                        <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label for="jurusan"
                                        class="text-sm font-bold text-zinc-700 uppercase tracking-tight">Jurusan</label>
                                    <input type="text" name="jurusan" id="jurusan"
                                        value="{{ old('jurusan', $aslab->jurusan) }}"
                                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                                        placeholder="Teknik Informatika">
                                    @error('jurusan')
                                        <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="angkatan"
                                        class="text-sm font-bold text-zinc-700 uppercase tracking-tight">Angkatan</label>
                                    <input type="text" name="angkatan" id="angkatan"
                                        value="{{ old('angkatan', $aslab->angkatan) }}"
                                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                                        placeholder="2024">
                                    @error('angkatan')
                                        <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label for="no_hp"
                                        class="text-sm font-bold text-zinc-700 uppercase tracking-tight">No. HP</label>
                                    <input type="text" name="no_hp" id="no_hp"
                                        value="{{ old('no_hp', $aslab->no_hp) }}"
                                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                                        placeholder="08123456789">
                                    @error('no_hp')
                                        <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="password"
                                        class="text-sm font-bold text-zinc-700 uppercase tracking-tight">Password <span
                                            class="lowercase font-medium text-zinc-400 font-sans tracking-normal">(Kosongkan
                                            jika tidak ingin diubah)</span></label>
                                    <div class="relative">
                                        <input type="password" name="password" id="password"
                                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                                            placeholder="••••••••">
                                        <button type="button" onclick="togglePassword()"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 transition-colors">
                                            <i class="fas fa-eye text-xs" id="eye-icon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <a href="{{ route('admin.aslab.index') }}" data-spa
                                class="inline-flex h-10 items-center justify-center rounded-lg border border-zinc-200 bg-white px-6 text-sm font-bold text-zinc-600 transition-all hover:bg-zinc-50 active:scale-95">
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex h-10 items-center justify-center rounded-lg bg-[#001f3f] px-8 text-sm font-bold text-white shadow-lg shadow-[#001f3f]/20 transition-all hover:bg-[#002d5a] active:scale-95">
                                Perbarui Data Aslab
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Profile Picture -->
                <div class="space-y-6">
                    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-zinc-900 mb-6 flex items-center gap-2">
                            <i class="fas fa-camera text-primary text-sm"></i>
                            Foto Profil
                        </h3>

                        <div class="flex flex-col items-center gap-6">
                            <div id="preview-container"
                                class="relative h-48 w-48 rounded-2xl border-2 {{ $aslab->profile_picture ? 'border-solid border-[#001f3f]/10' : 'border-dashed border-zinc-200' }} bg-zinc-50/50 flex items-center justify-center overflow-hidden group transition-all hover:border-[#001f3f]/20 hover:bg-white">
                                <div id="placeholder-text"
                                    class="{{ $aslab->profile_picture ? 'hidden' : '' }} text-center p-4">
                                    <i
                                        class="fas fa-cloud-arrow-up text-3xl text-zinc-300 mb-2 group-hover:scale-110 transition-transform"></i>
                                    <p class="text-xs font-semibold text-zinc-400">Klik untuk ganti gambar</p>
                                </div>
                                @if ($aslab->profile_picture)
                                    <img id="image-preview" src="{{ asset('storage/' . $aslab->profile_picture) }}"
                                        class="h-full w-full object-cover">
                                @else
                                    <img id="image-preview" class="hidden h-full w-full object-cover">
                                @endif

                                <button type="button" onclick="removeImage()" id="remove-btn"
                                    class="{{ $aslab->profile_picture ? 'flex scale-100' : 'hidden scale-0' }} absolute top-2 right-2 h-7 w-7 rounded-lg bg-rose-500 text-white items-center justify-center shadow-lg hover:bg-rose-600 transition-all active:scale-90 origin-center">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>

                            <div class="w-full space-y-3">
                                <input type="file" name="profile_picture" id="profile_picture" class="hidden"
                                    accept="image/*" onchange="previewImage(this)">
                                <button type="button" onclick="document.getElementById('profile_picture').click()"
                                    class="w-full inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-zinc-200 bg-white px-4 text-sm font-bold text-zinc-700 shadow-sm transition-all hover:bg-zinc-50 hover:text-zinc-900 active:scale-95">
                                    <i class="fas fa-image text-xs"></i>
                                    {{ $aslab->profile_picture ? 'Ganti Gambar' : 'Pilih Gambar' }}
                                </button>
                                <div class="p-3 rounded-lg bg-zinc-50 border border-zinc-100 italic">
                                    <p class="text-[10px] text-zinc-500 leading-relaxed text-center">
                                        Format yang didukung: JPG, JPEG, atau PNG.<br>Maksimal ukuran file adalah 2MB.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @error('profile_picture')
                            <p class="text-[10px] font-medium text-rose-500 mt-2 text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-5">
                        <h4
                            class="text-sm font-bold text-emerald-800 flex items-center gap-2 mb-2 uppercase tracking-wider">
                            <i class="fas fa-check-circle"></i>
                            Identitas Terverifikasi
                        </h4>
                        <p class="text-xs text-emerald-700 leading-relaxed">
                            Pastikan data NPM sesuai dengan kartu tanda mahasiswa untuk keperluan validasi jadwal jaga.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('placeholder-text');
            const container = document.getElementById('preview-container');
            const removeBtn = document.getElementById('remove-btn');

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

        function removeImage() {
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('placeholder-text');
            const container = document.getElementById('preview-container');
            const input = document.getElementById('profile_picture');
            const removeBtn = document.getElementById('remove-btn');

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

        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
@endsection

@extends('layouts.admin', ['title' => 'Tambah Pengguna'])

@section('content')
    <div class="max-w-2xl mx-auto space-y-8">
        <!-- Header Section -->
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.user.index') }}" data-spa
                class="w-10 h-10 flex items-center justify-center rounded-xl border border-zinc-200 text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-zinc-800 tracking-tight">Tambah Pengguna</h1>
                <p class="text-sm font-medium text-zinc-500 mt-1">Daftarkan admin atau asisten laboratorium baru.</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <form action="{{ route('admin.user.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="username"
                            class="text-xs font-bold uppercase tracking-widest text-zinc-400">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}"
                            class="w-full rounded-xl border border-zinc-200 bg-zinc-50/50 px-4 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-zinc-900/5 focus:border-zinc-900 transition-all"
                            placeholder="admin.lab" required>
                        @error('username')
                            <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="name" class="text-xs font-bold uppercase tracking-widest text-zinc-400">Nama
                            Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="w-full rounded-xl border border-zinc-200 bg-zinc-50/50 px-4 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-zinc-900/5 focus:border-zinc-900 transition-all"
                            placeholder="John Doe" required>
                        @error('name')
                            <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="email"
                            class="text-xs font-bold uppercase tracking-widest text-zinc-400">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="w-full rounded-xl border border-zinc-200 bg-zinc-50/50 px-4 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-zinc-900/5 focus:border-zinc-900 transition-all"
                            placeholder="admin@example.com" required>
                        @error('email')
                            <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="role_id"
                            class="text-xs font-bold uppercase tracking-widest text-zinc-400">Peran</label>
                        <select name="role_id" id="role_id" required
                            class="w-full rounded-xl border border-zinc-200 bg-zinc-50/50 px-4 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-zinc-900/5 focus:border-zinc-900 transition-all appearance-none cursor-pointer">
                            <option value="">Pilih Peran</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->role_name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-xs font-bold uppercase tracking-widest text-zinc-400">Password</label>
                    <input type="password" name="password" id="password"
                        class="w-full rounded-xl border border-zinc-200 bg-zinc-50/50 px-4 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-zinc-900/5 focus:border-zinc-900 transition-all"
                        placeholder="••••••••" required>
                    @error('password')
                        <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-4">
                    <label class="text-xs font-bold uppercase tracking-widest text-zinc-400">Foto Profil</label>
                    <div class="flex items-center gap-6">
                        <div id="preview-container"
                            class="w-20 h-20 rounded-2xl border-2 border-dashed border-zinc-200 flex items-center justify-center text-zinc-300 overflow-hidden bg-zinc-50/50">
                            <i class="fas fa-image text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="profile_picture" id="profile_picture" class="hidden"
                                accept="image/*" onchange="previewImage(this)">
                            <button type="button" onclick="document.getElementById('profile_picture').click()"
                                class="inline-flex items-center justify-center rounded-xl border border-zinc-200 bg-white px-4 py-2 text-xs font-bold text-zinc-600 hover:bg-zinc-50 transition-all active:scale-95 uppercase tracking-widest">
                                Pilih Gambar
                            </button>
                            <p class="text-[10px] font-medium text-zinc-400 mt-2 italic">Format: JPG, JPEG, PNG. Max: 2MB
                            </p>
                        </div>
                    </div>
                    @error('profile_picture')
                        <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-wide">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-6 border-t border-zinc-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.user.index') }}" data-spa
                        class="px-6 py-2.5 text-xs font-bold text-zinc-400 hover:text-zinc-600 uppercase tracking-widest transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-zinc-900 hover:bg-black text-white font-bold px-8 py-2.5 rounded-xl shadow-lg shadow-zinc-200 transition-all active:scale-95 uppercase text-xs tracking-widest">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview-container');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    preview.classList.remove('border-dashed');
                    preview.classList.add('border-solid');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection

@extends('layouts.admin', ['title' => 'Edit Pengguna'])

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header Section -->
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.user.index') }}" data-spa
                class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-500 shadow-sm hover:bg-zinc-100 hover:text-zinc-900 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Edit Pengguna</h1>
                <p class="text-sm text-zinc-500 mt-1">Perbarui informasi profil pengguna.</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <form action="{{ route('admin.user.update', $user->id) }}" method="POST" enctype="multipart/form-data"
                class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="username"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-zinc-900">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}"
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950"
                            placeholder="admin.lab" required>
                        @error('username')
                            <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="name"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-zinc-900">Nama
                            Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950"
                            placeholder="John Doe" required>
                        @error('name')
                            <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="email"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-zinc-900">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950"
                            placeholder="admin@example.com" required>
                        @error('email')
                            <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="role_id"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-zinc-900">Peran</label>
                        <select name="role_id" id="role_id" required
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950 appearance-none cursor-pointer">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->role_name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="password"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-zinc-900">Password
                            Baru</label>
                        <span class="text-[10px] font-medium text-zinc-500 italic">Kosongkan jika tidak ingin diubah</span>
                    </div>
                    <input type="password" name="password" id="password"
                        class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950"
                        placeholder="••••••••">
                    @error('password')
                        <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-4">
                    <label
                        class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-zinc-900">Foto
                        Profil</label>
                    <div class="flex items-center gap-4">
                        <div id="preview-container"
                            class="h-16 w-16 rounded-md border border-zinc-200 flex items-center justify-center text-zinc-400 overflow-hidden bg-zinc-50">
                            @if ($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                    class="h-full w-full object-cover">
                            @else
                                <i class="fas fa-image text-xl"></i>
                            @endif
                        </div>
                        <div class="flex flex-col gap-2">
                            <input type="file" name="profile_picture" id="profile_picture" class="hidden"
                                accept="image/*" onchange="previewImage(this)">
                            <button type="button" onclick="document.getElementById('profile_picture').click()"
                                class="inline-flex h-9 w-fit items-center justify-center rounded-md border border-zinc-200 bg-white px-4 py-2 text-sm font-medium shadow-sm hover:bg-zinc-100 hover:text-zinc-900 transition-colors">
                                Ganti Gambar
                            </button>
                            <p class="text-xs text-zinc-500">Format: JPG, JPEG, PNG. Max: 2MB</p>
                        </div>
                    </div>
                    @error('profile_picture')
                        <p class="text-[10px] font-medium text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-6 flex items-center justify-end gap-2">
                    <a href="{{ route('admin.user.index') }}" data-spa
                        class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 py-2 text-sm font-medium shadow-sm hover:bg-zinc-100 hover:text-zinc-900 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-zinc-50 shadow hover:bg-zinc-900/90 transition-colors">
                        Simpan Perubahan
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
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection

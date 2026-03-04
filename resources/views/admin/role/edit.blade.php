@extends('layouts.admin', ['title' => 'Edit Role'])

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header Section -->
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.role.index') }}" data-spa
                class="w-10 h-10 flex items-center justify-center rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 tracking-tight">Edit Role</h1>
                <p class="text-sm text-zinc-500">Perbarui informasi peran dalam sistem.</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
            <form action="{{ route('admin.role.update', $role->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-2">
                    <label for="role_name" class="text-sm font-medium text-zinc-900">Nama Role</label>
                    <input type="text" name="role_name" id="role_name" value="{{ old('role_name', $role->role_name) }}"
                        class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-900/5 focus:border-zinc-900 transition-all"
                        placeholder="Contoh: Administrator, Editor, User" required>
                    @error('role_name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="role_description" class="text-sm font-medium text-zinc-900">Deskripsi</label>
                    <textarea name="role_description" id="role_description" rows="3"
                        class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-900/5 focus:border-zinc-900 transition-all"
                        placeholder="Jelaskan fungsi dari role ini...">{{ old('role_description', $role->role_description) }}</textarea>
                    @error('role_description')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-zinc-900 block">Status Role</label>
                    <div class="flex items-center gap-4 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="role_status" value="1" class="sr-only peer"
                                {{ $role->role_status ? 'checked' : '' }}>
                            <div
                                class="w-4 h-4 rounded-full border border-zinc-300 peer-checked:border-zinc-900 peer-checked:bg-zinc-900 flex items-center justify-center transition-all">
                                <div
                                    class="w-1.5 h-1.5 rounded-full bg-white scale-0 peer-checked:scale-100 transition-transform">
                                </div>
                            </div>
                            <span class="text-sm text-zinc-600 group-hover:text-zinc-900">Aktif</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="role_status" value="0" class="sr-only peer"
                                {{ !$role->role_status ? 'checked' : '' }}>
                            <div
                                class="w-4 h-4 rounded-full border border-zinc-300 peer-checked:border-zinc-900 peer-checked:bg-zinc-900 flex items-center justify-center transition-all">
                                <div
                                    class="w-1.5 h-1.5 rounded-full bg-white scale-0 peer-checked:scale-100 transition-transform">
                                </div>
                            </div>
                            <span class="text-sm text-zinc-600 group-hover:text-zinc-900">Non-aktif</span>
                        </label>
                    </div>
                </div>

                <div class="pt-4 border-t border-zinc-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.role.index') }}" data-spa
                        class="px-4 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-6 py-2 text-sm font-medium text-white shadow hover:bg-zinc-800 transition-all active:scale-95">
                        Perbarui Role
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

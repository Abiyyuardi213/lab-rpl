<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Aslab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AslabController extends Controller
{
    public function index()
    {
        $aslabRole = Role::where('name', 'Aslab')->first();
        if (!$aslabRole) {
            return redirect()->back()->with('error', 'Role Aslab tidak ditemukan.');
        }
        $aslabs = User::where('role_id', $aslabRole->id)
            ->with('aslab')
            ->orderBy('created_at', 'asc')
            ->get();
        return view('admin.aslab.index', compact('aslabs'));
    }

    public function create()
    {
        return view('admin.aslab.create');
    }

    public function store(Request $request)
    {
        $aslabRole = Role::where('name', 'Aslab')->first();

        $request->validate([
            'npm' => 'required|string|unique:aslabs,npm',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'angkatan' => 'nullable|string',
            'jabatan' => 'required|string',
            'no_hp' => 'nullable|string',
        ]);

        $user = new User();
        $user->username = $request->npm;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role_id = $aslabRole->id;
        $user->status = true;

        if ($request->hasFile('profile_picture')) {
            $user->profile_picture = $request->file('profile_picture')->store('profile', 'public');
        }

        $user->save();

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('aslab-premium', 'public');
        }

        Aslab::create([
            'user_id' => $user->id,
            'npm' => $request->npm,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'jabatan' => $request->jabatan,
            'jurusan' => 'Teknik Informatika',
            'angkatan' => $request->angkatan,
            'no_hp' => $request->no_hp,
            'profile_image' => $profileImagePath,
        ]);

        return redirect()->route('admin.aslab.index')->with('success', 'Aslab berhasil ditambahkan.');
    }

    public function show($id)
    {
        $aslab = User::with('aslab')->findOrFail($id);
        return view('admin.aslab.show', compact('aslab'));
    }

    public function edit($id)
    {
        $aslab = User::with('aslab')->findOrFail($id);
        return view('admin.aslab.edit', compact('aslab'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'npm' => 'required|string|unique:aslabs,npm,' . ($user->aslab ? $user->aslab->id : 'NULL'),
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'angkatan' => 'nullable|string',
            'jabatan' => 'required|string',
            'no_hp' => 'nullable|string',
        ]);

        $userData = [
            'username' => $request->npm,
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::delete('public/' . $user->profile_picture);
            }
            $userData['profile_picture'] = $request->file('profile_picture')->store('profile', 'public');
        }

        $user->update($userData);

        $aslabData = [
            'npm' => $request->npm,
            'jabatan' => $request->jabatan,
            'jurusan' => 'Teknik Informatika',
            'angkatan' => $request->angkatan,
            'no_hp' => $request->no_hp,
        ];

        if (!$user->aslab || !$user->aslab->slug) {
            $aslabData['slug'] = \Illuminate\Support\Str::slug($request->name);
        }

        if ($request->hasFile('profile_image')) {
            if ($user->aslab && $user->aslab->profile_image) {
                Storage::delete('public/' . $user->aslab->profile_image);
            }
            $aslabData['profile_image'] = $request->file('profile_image')->store('aslab-premium', 'public');
        }

        $user->aslab()->updateOrCreate(
            ['user_id' => $user->id],
            $aslabData
        );

        return redirect()->route('admin.aslab.index')->with('success', 'Aslab berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $aslab = User::findOrFail($id);
        if ($aslab->profile_picture) {
            Storage::delete('public/' . $aslab->profile_picture);
        }
        $aslab->delete();

        return redirect()->route('admin.aslab.index')->with('success', 'Aslab berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Status aslab berhasil diperbarui.'
        ]);
    }
}

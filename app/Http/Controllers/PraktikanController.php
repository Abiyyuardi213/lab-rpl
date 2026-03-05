<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Praktikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PraktikanController extends Controller
{
    public function index()
    {
        $praktikanRole = Role::where('name', 'Praktikan')->first();
        if (!$praktikanRole) {
            return redirect()->back()->with('error', 'Role Praktikan tidak ditemukan.');
        }
        $praktikans = User::where('role_id', $praktikanRole->id)
            ->with('praktikan')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.praktikan.index', compact('praktikans'));
    }

    public function create()
    {
        return view('admin.praktikan.create');
    }

    public function store(Request $request)
    {
        $praktikanRole = Role::where('name', 'Praktikan')->first();

        $request->validate([
            'username' => 'nullable|string|unique:users,username',
            'npm' => 'required|string|unique:praktikans,npm',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'jurusan' => 'nullable|string',
            'angkatan' => 'nullable|string',
            'no_hp' => 'nullable|string',
        ]);

        $userData = [
            'username' => $request->username ?: $request->npm,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $praktikanRole->id,
            'status' => true,
        ];

        if ($request->hasFile('profile_picture')) {
            $userData['profile_picture'] = $request->file('profile_picture')->store('profile', 'public');
        }

        $user = User::create($userData);

        Praktikan::create([
            'user_id' => $user->id,
            'npm' => $request->npm,
            'jurusan' => $request->jurusan,
            'angkatan' => $request->angkatan,
            'no_hp' => $request->no_hp,
        ]);

        return redirect()->route('admin.praktikan.index')->with('success', 'Praktikan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $praktikan = User::with('praktikan')->findOrFail($id);
        return view('admin.praktikan.show', compact('praktikan'));
    }

    public function edit($id)
    {
        $praktikan = User::with('praktikan')->findOrFail($id);
        return view('admin.praktikan.edit', compact('praktikan'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'nullable|string|unique:users,username,' . $id,
            'npm' => 'required|string|unique:praktikans,npm,' . ($user->praktikan ? $user->praktikan->id : 'NULL'),
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'jurusan' => 'nullable|string',
            'angkatan' => 'nullable|string',
            'no_hp' => 'nullable|string',
        ]);

        $userData = [
            'username' => $request->username ?: $request->npm,
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

        $user->praktikan()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'npm' => $request->npm,
                'jurusan' => $request->jurusan,
                'angkatan' => $request->angkatan,
                'no_hp' => $request->no_hp,
            ]
        );

        return redirect()->route('admin.praktikan.index')->with('success', 'Praktikan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $praktikan = User::findOrFail($id);
        if ($praktikan->profile_picture) {
            Storage::delete('public/' . $praktikan->profile_picture);
        }
        $praktikan->delete();

        return redirect()->route('admin.praktikan.index')->with('success', 'Praktikan berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Status praktikan berhasil diperbarui.'
        ]);
    }
}

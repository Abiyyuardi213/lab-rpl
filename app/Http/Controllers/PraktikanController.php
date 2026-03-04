<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
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
        $praktikans = User::where('role_id', $praktikanRole->id)->orderBy('created_at', 'desc')->get();
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
            'npm' => 'required|string|unique:users,npm',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();
        $data['username'] = $request->username ?: $request->npm;
        $data['password'] = Hash::make($request->password);
        $data['role_id'] = $praktikanRole->id;

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('profile', 'public');
        }

        User::create($data);

        return redirect()->route('admin.praktikan.index')->with('success', 'Praktikan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $praktikan = User::findOrFail($id);
        return view('admin.praktikan.show', compact('praktikan'));
    }

    public function edit($id)
    {
        $praktikan = User::findOrFail($id);
        return view('admin.praktikan.edit', compact('praktikan'));
    }

    public function update(Request $request, $id)
    {
        $praktikan = User::findOrFail($id);

        $request->validate([
            'username' => 'nullable|string|unique:users,username,' . $id,
            'npm' => 'required|string|unique:users,npm,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('password');
        $data['username'] = $request->username ?: $request->npm;
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            if ($praktikan->profile_picture) {
                Storage::delete('public/' . $praktikan->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('profile', 'public');
        }

        $praktikan->update($data);

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

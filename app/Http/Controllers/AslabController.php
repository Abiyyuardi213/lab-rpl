<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
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
        $aslabs = User::where('role_id', $aslabRole->id)->orderBy('created_at', 'desc')->get();
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
        $data['role_id'] = $aslabRole->id;

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('profile', 'public');
        }

        User::create($data);

        return redirect()->route('admin.aslab.index')->with('success', 'Aslab berhasil ditambahkan.');
    }

    public function show($id)
    {
        $aslab = User::findOrFail($id);
        return view('admin.aslab.show', compact('aslab'));
    }

    public function edit($id)
    {
        $aslab = User::findOrFail($id);
        return view('admin.aslab.edit', compact('aslab'));
    }

    public function update(Request $request, $id)
    {
        $aslab = User::findOrFail($id);

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
            if ($aslab->profile_picture) {
                Storage::delete('public/' . $aslab->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('profile', 'public');
        }

        $aslab->update($data);

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

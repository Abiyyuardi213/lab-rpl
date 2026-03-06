<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        // Determine view based on role
        $view = 'admin.profile.edit';
        if ($user->role) {
            $roleName = strtolower($user->role->name);
            if (view()->exists("$roleName.profile.edit")) {
                $view = "$roleName.profile.edit";
            }
        }

        return view($view, compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|string|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        // Add role-specific rules
        if ($user->role) {
            if ($user->role->name === 'Praktikan' || $user->role->name === 'Aslab') {
                $rules['no_hp'] = 'nullable|string|max:20';
                $rules['jurusan'] = 'nullable|string|max:255';
                $rules['angkatan'] = 'nullable|string|max:4';
            }
        }

        $request->validate($rules);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::delete('public/' . $user->profile_picture);
            }
            $user->profile_picture = $request->file('profile_picture')->store('profile', 'public');
        }

        $user->save();

        // Update role-specific data
        if ($user->role) {
            if ($user->role->name === 'Praktikan' && $user->praktikan) {
                $user->praktikan->update([
                    'no_hp' => $request->no_hp,
                    'jurusan' => $request->jurusan,
                    'angkatan' => $request->angkatan,
                    // Note: NPM is usually not editable by students, 
                    // but we keep it in sync with username if it was originally NPM? 
                    // No, let's keep them separate as per user's request.
                ]);
            } elseif ($user->role->name === 'Aslab' && $user->aslab) {
                $user->aslab->update([
                    'no_hp' => $request->no_hp,
                    'jurusan' => $request->jurusan,
                    'angkatan' => $request->angkatan,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }
}

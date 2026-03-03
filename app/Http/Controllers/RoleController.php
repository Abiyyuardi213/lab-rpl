<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('created_at', 'asc')->get();
        return view('admin.role.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.role.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'role_description' => 'nullable|string',
            'role_status' => 'required|boolean',
        ]);

        Role::createRole($request->all());

        return redirect()->route('admin.role.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.role.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'role_description' => 'nullable|string',
            'role_status' => 'required|boolean',
        ]);

        $role = Role::findOrFail($id);
        $role->updateRole($request->all());

        return redirect()->route('admin.role.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->deleteRole();

        return redirect()->route('admin.role.index')->with('success', 'Role berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->toggleStatus();

            session()->flash('success', 'Status role berhasil diperbarui.');

            return response()->json([
                'success' => true,
                'message' => 'Status role berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status.'
            ], 500);
        }
    }
}

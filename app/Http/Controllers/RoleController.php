<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('admin.role.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'display_name' => 'required',
        ]);

        Role::create($request->all());

        return redirect()->back()->with('success', 'Role berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'display_name' => 'required',
        ]);

        $role->update($request->all());

        return redirect()->back()->with('success', 'Role berhasil diperbarui');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->back()->with('success', 'Role berhasil dihapus');
    }

    public function toggleStatus($id)
    {
        $role = Role::findOrFail($id);
        $role->status = !$role->status;
        $role->save();

        return response()->json([
            'success' => true,
            'message' => 'Status role berhasil diperbarui.'
        ]);
    }
}

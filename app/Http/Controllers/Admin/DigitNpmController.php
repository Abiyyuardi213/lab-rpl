<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitNpm;
use App\Models\Penugasan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DigitNpmController extends Controller
{
    public function index()
    {
        $digitNpms = DigitNpm::ordered()->get();

        return view('admin.digit-npm.index', compact('digitNpms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'digit' => 'required|string|max:20|unique:digit_npms,digit',
            'label' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? DigitNpm::max('sort_order') + 1;

        DigitNpm::create($data);

        return redirect()->route('admin.digit-npm.index')->with('success', 'Digit NPM berhasil ditambahkan.');
    }

    public function update(Request $request, DigitNpm $digitNpm)
    {
        $data = $request->validate([
            'digit' => [
                'required',
                'string',
                'max:20',
                Rule::unique('digit_npms', 'digit')->ignore($digitNpm->id),
            ],
            'label' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        if ($digitNpm->digit !== $data['digit'] && Penugasan::where('kode_akhir_npm', $digitNpm->digit)->exists()) {
            return redirect()->route('admin.digit-npm.index')
                ->with('error', 'Kode tidak dapat diubah karena sudah dipakai pada penugasan.');
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? $digitNpm->sort_order;

        $digitNpm->update($data);

        return redirect()->route('admin.digit-npm.index')->with('success', 'Digit NPM berhasil diperbarui.');
    }

    public function destroy(DigitNpm $digitNpm)
    {
        if (Penugasan::where('kode_akhir_npm', $digitNpm->digit)->exists()) {
            return redirect()->route('admin.digit-npm.index')
                ->with('error', 'Digit tidak dapat dihapus karena sudah dipakai pada penugasan.');
        }

        $digitNpm->delete();

        return redirect()->route('admin.digit-npm.index')->with('success', 'Digit NPM berhasil dihapus.');
    }

    public function toggleStatus(DigitNpm $digitNpm)
    {
        $digitNpm->update([
            'is_active' => !$digitNpm->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status digit NPM berhasil diperbarui.',
            'is_active' => $digitNpm->is_active,
        ]);
    }
}

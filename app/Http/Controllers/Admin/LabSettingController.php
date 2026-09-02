<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LabSettingController extends Controller
{
    public function index()
    {
        $setting = LabSetting::getSetting();
        return view('admin.setting.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_kepala_lab' => 'required|string|max:255',
            'nip_kepala_lab' => 'nullable|string|max:100',
            'ttd_kepala_lab' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'nama_kaprodi' => 'required|string|max:255',
            'nip_kaprodi' => 'nullable|string|max:100',
            'ttd_kaprodi' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'nomor_surat_prefix' => 'nullable|string|max:100',
            'bg_sertifikat_template' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
        ]);

        $setting = LabSetting::getSetting();

        $setting->nama_kepala_lab = $request->nama_kepala_lab;
        $setting->nip_kepala_lab = $request->nip_kepala_lab;
        $setting->nama_kaprodi = $request->nama_kaprodi;
        $setting->nip_kaprodi = $request->nip_kaprodi;
        $setting->nomor_surat_prefix = $request->nomor_surat_prefix ?: 'LAB-RPL/SERT';

        if ($request->hasFile('ttd_kepala_lab')) {
            if ($setting->ttd_kepala_lab && Storage::disk('public')->exists($setting->ttd_kepala_lab)) {
                Storage::disk('public')->delete($setting->ttd_kepala_lab);
            }
            $setting->ttd_kepala_lab = $request->file('ttd_kepala_lab')->store('setting/ttd', 'public');
        }

        if ($request->hasFile('ttd_kaprodi')) {
            if ($setting->ttd_kaprodi && Storage::disk('public')->exists($setting->ttd_kaprodi)) {
                Storage::disk('public')->delete($setting->ttd_kaprodi);
            }
            $setting->ttd_kaprodi = $request->file('ttd_kaprodi')->store('setting/ttd', 'public');
        }

        if ($request->hasFile('bg_sertifikat_template')) {
            if ($setting->bg_sertifikat_template && Storage::disk('public')->exists($setting->bg_sertifikat_template)) {
                Storage::disk('public')->delete($setting->bg_sertifikat_template);
            }
            $setting->bg_sertifikat_template = $request->file('bg_sertifikat_template')->store('setting/template', 'public');
        }

        $setting->save();

        return back()->with('success', 'Pengaturan laboratorium & TTD sertifikat berhasil diperbarui.');
    }
}

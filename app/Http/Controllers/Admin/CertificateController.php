<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Praktikum;
use App\Models\LabSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with(['praktikum' => function($q) {
            $q->withCount(['pendaftarans as total_lulus' => function($pq) {
                $pq->where('status', 'verified')->whereHas('penilaianAkhir', function($aq) {
                    $aq->where('status_kelulusan', 'LULUS');
                });
            }]);
        }])
        ->latest()
        ->get();

        $praktikums = Praktikum::doesntHave('certificate')
            ->orderBy('created_at', 'desc')
            ->get();

        $setting = LabSetting::getSetting();

        return view('admin.certificate.index', compact('certificates', 'praktikums', 'setting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|exists:praktikums,id|unique:certificates,praktikum_id',
            'nomor_surat_prefix' => 'required|string|max:255',
            'nama_kepala_lab' => 'required|string|max:255',
            'nip_kepala_lab' => 'nullable|string|max:100',
            'ttd_kepala_lab' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'nama_kaprodi' => 'required|string|max:255',
            'nip_kaprodi' => 'nullable|string|max:100',
            'ttd_kaprodi' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'bg_template' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
            'tanggal_sertifikat' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $setting = LabSetting::getSetting();

        $bgPath = null;
        if ($request->hasFile('bg_template')) {
            $bgPath = $request->file('bg_template')->store('certificates/template', 'public');
        } elseif ($setting->bg_sertifikat_template) {
            $bgPath = $setting->bg_sertifikat_template;
        }

        $ttdKepalaLabPath = null;
        if ($request->hasFile('ttd_kepala_lab')) {
            $ttdKepalaLabPath = $request->file('ttd_kepala_lab')->store('certificates/ttd', 'public');
        } elseif ($setting->ttd_kepala_lab) {
            $ttdKepalaLabPath = $setting->ttd_kepala_lab;
        }

        $ttdKaprodiPath = null;
        if ($request->hasFile('ttd_kaprodi')) {
            $ttdKaprodiPath = $request->file('ttd_kaprodi')->store('certificates/ttd', 'public');
        } elseif ($setting->ttd_kaprodi) {
            $ttdKaprodiPath = $setting->ttd_kaprodi;
        }

        Certificate::create([
            'praktikum_id' => $request->praktikum_id,
            'nomor_surat_prefix' => $request->nomor_surat_prefix,
            'bg_template' => $bgPath,
            'nama_kepala_lab' => $request->nama_kepala_lab,
            'nip_kepala_lab' => $request->nip_kepala_lab,
            'ttd_kepala_lab' => $ttdKepalaLabPath,
            'nama_kaprodi' => $request->nama_kaprodi,
            'nip_kaprodi' => $request->nip_kaprodi,
            'ttd_kaprodi' => $ttdKaprodiPath,
            'tanggal_sertifikat' => $request->tanggal_sertifikat,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('admin.certificate.index')->with('success', 'Konfigurasi sertifikat praktikum berhasil diterbitkan.');
    }

    public function edit($id)
    {
        $certificate = Certificate::with('praktikum')->findOrFail($id);
        return view('admin.certificate.edit', compact('certificate'));
    }

    public function update(Request $request, $id)
    {
        $certificate = Certificate::findOrFail($id);

        $request->validate([
            'nomor_surat_prefix' => 'required|string|max:255',
            'nama_kepala_lab' => 'required|string|max:255',
            'nip_kepala_lab' => 'nullable|string|max:100',
            'ttd_kepala_lab' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'nama_kaprodi' => 'required|string|max:255',
            'nip_kaprodi' => 'nullable|string|max:100',
            'ttd_kaprodi' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'bg_template' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
            'tanggal_sertifikat' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->except(['bg_template', 'ttd_kepala_lab', 'ttd_kaprodi']);

        if ($request->hasFile('bg_template')) {
            if ($certificate->bg_template && Storage::disk('public')->exists($certificate->bg_template)) {
                Storage::disk('public')->delete($certificate->bg_template);
            }
            $data['bg_template'] = $request->file('bg_template')->store('certificates/template', 'public');
        }

        if ($request->hasFile('ttd_kepala_lab')) {
            if ($certificate->ttd_kepala_lab && Storage::disk('public')->exists($certificate->ttd_kepala_lab)) {
                Storage::disk('public')->delete($certificate->ttd_kepala_lab);
            }
            $data['ttd_kepala_lab'] = $request->file('ttd_kepala_lab')->store('certificates/ttd', 'public');
        }

        if ($request->hasFile('ttd_kaprodi')) {
            if ($certificate->ttd_kaprodi && Storage::disk('public')->exists($certificate->ttd_kaprodi)) {
                Storage::disk('public')->delete($certificate->ttd_kaprodi);
            }
            $data['ttd_kaprodi'] = $request->file('ttd_kaprodi')->store('certificates/ttd', 'public');
        }

        $certificate->update($data);

        return redirect()->route('admin.certificate.index')->with('success', 'Data sertifikat praktikum berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
        $certificate->delete();

        return redirect()->route('admin.certificate.index')->with('success', 'Arsip sertifikat praktikum berhasil dihapus.');
    }
}

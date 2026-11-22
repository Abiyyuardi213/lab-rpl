<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranPraktikum;
use App\Traits\HasActivityLog;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    use HasActivityLog;

    public function index(Request $request)
    {
        $query = PendaftaranPraktikum::with(['praktikan.user', 'praktikum', 'sesi']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $pendaftarans = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pendaftaran.index', compact('pendaftarans'));
    }

    public function show($id)
    {
        $pendaftaran = PendaftaranPraktikum::with(['praktikan.user', 'praktikum', 'sesi'])->findOrFail($id);

        return view('admin.pendaftaran.show', compact('pendaftaran'));
    }

    public function updateStatus(Request $request, $id)
    {
        $pendaftaran = PendaftaranPraktikum::with('praktikan.user')->findOrFail($id);

        // Jika form mengirimkan status 2-step verification (Lembar Kuning & Berkas)
        if ($request->has('status_lembar_kuning') || $request->has('status_berkas')) {
            $request->validate([
                'status_lembar_kuning' => 'required|in:pending,verified,rejected',
                'catatan_lembar_kuning' => 'required_if:status_lembar_kuning,rejected|nullable|string',
                'status_berkas' => 'required|in:pending,verified,rejected',
                'catatan' => 'required_if:status_berkas,rejected|nullable|string',
            ]);

            $statusLembarKuning = $request->status_lembar_kuning;
            $statusBerkas = $request->status_berkas;

            // Logika Gatekeeper 2-Step:
            // 1. Jika salah satu rejected -> Master status rejected
            // 2. Jika KEDUA verifikasi verified -> Master status verified (praktikan aktif)
            // 3. Jika salah satu atau keduanya pending -> Master status pending (belum boleh praktikum)
            if ($statusLembarKuning === 'rejected' || $statusBerkas === 'rejected') {
                $computedStatus = 'rejected';
            } elseif ($statusLembarKuning === 'verified' && $statusBerkas === 'verified') {
                $computedStatus = 'verified';
            } else {
                $computedStatus = 'pending';
            }

            $pendaftaran->update([
                'status_lembar_kuning' => $statusLembarKuning,
                'catatan_lembar_kuning' => $request->catatan_lembar_kuning,
                'status_berkas' => $statusBerkas,
                'status' => $computedStatus,
                'catatan' => $request->catatan,
            ]);

            $this->logActivity(
                'Verifikasi Pendaftaran 2-Step',
                "Memperbarui verifikasi 2-step praktikan {$pendaftaran->praktikan?->user?->name}: Lembar Kuning [{$statusLembarKuning}], Berkas [{$statusBerkas}] => Status Akhir [{$computedStatus}]",
                ['pendaftaran_id' => $pendaftaran->id, 'praktikum_id' => $pendaftaran->praktikum_id]
            );

            return redirect()->route('admin.pendaftaran.index')->with('success', 'Hasil verifikasi 2-step pendaftaran berhasil diperbarui.');
        }

        // Jalur cepat / kompatibilitas legacy (misal tombol centang verified / tolak langsung dari index)
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'catatan' => 'required_if:status,rejected|nullable|string',
        ]);

        if ($request->status === 'verified') {
            $pendaftaran->update([
                'status_lembar_kuning' => 'verified',
                'status_berkas' => 'verified',
                'status' => 'verified',
                'catatan' => $request->catatan,
            ]);
        } else {
            $pendaftaran->update([
                'status_berkas' => 'rejected',
                'status' => 'rejected',
                'catatan' => $request->catatan,
            ]);
        }

        return redirect()->route('admin.pendaftaran.index')->with('success', 'Status pendaftaran berhasil diperbarui.');
    }

    public function reactivate(Request $request, $id)
    {
        $pendaftaran = PendaftaranPraktikum::with('praktikan.user')->findOrFail($id);

        if ($pendaftaran->status !== 'gugur') {
            return back()->with('error', 'Hanya praktikan berstatus gugur yang dapat dipulihkan.');
        }

        $request->validate([
            'alasan' => 'nullable|string|max:500',
        ]);

        $alasan = $request->alasan ?? 'Dispensasi resmi disetujui.';

        $pendaftaran->update([
            'status' => 'verified',
            'catatan' => 'Dipulihkan dari status gugur oleh Admin: '.$alasan,
        ]);

        $this->logActivity(
            'Pulihkan Praktikan',
            "Memulihkan praktikan {$pendaftaran->praktikan?->user?->name} dari status gugur: {$alasan}",
            ['pendaftaran_id' => $pendaftaran->id, 'praktikum_id' => $pendaftaran->praktikum_id]
        );

        return back()->with('success', "Status praktikan {$pendaftaran->praktikan?->user?->name} berhasil dipulihkan menjadi terverifikasi.");
    }

    public function destroy($id)
    {
        $pendaftaran = PendaftaranPraktikum::findOrFail($id);

        // Hapus file-file terkait
        $files = ['bukti_krs', 'bukti_pembayaran', 'foto_almamater'];
        foreach ($files as $file) {
            if ($pendaftaran->$file && \Illuminate\Support\Facades\Storage::disk('public')->exists($pendaftaran->$file)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pendaftaran->$file);
            }
        }

        $pendaftaran->delete();

        return redirect()->route('admin.pendaftaran.index')->with('success', 'Data pendaftaran berhasil dihapus secara permanen.');
    }
}

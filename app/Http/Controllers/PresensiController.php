<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\TugasAsistensi;
use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PresensiController extends Controller
{
    public function generateQR($jadwal_id)
    {
        $user = Auth::user();
        $praktikan = $user->praktikan;

        if (!$praktikan) {
            return back()->with('error', 'Hanya praktikan yang dapat generate QR.');
        }

        $jadwal = JadwalPraktikum::with('praktikum')->findOrFail($jadwal_id);

        // Find pendaftaran for this praktikum
        $pendaftaran = PendaftaranPraktikum::where('praktikan_id', $praktikan->id)
            ->where('praktikum_id', $jadwal->praktikum_id)
            ->where('status', 'verified')
            ->first();

        if (!$pendaftaran) {
            return back()->with('error', 'Anda tidak terdaftar di praktikum ini atau pendaftaran belum terverifikasi.');
        }

        // Check already present
        $alreadyPresent = Presensi::where('jadwal_id', $jadwal->id)
            ->where('pendaftaran_id', $pendaftaran->id)
            ->exists();

        if ($alreadyPresent) {
            return back()->with('info', 'Anda sudah melakukan presensi untuk jadwal ini.');
        }

        // --- RULE CHECK: Asistensi Above Modul 1 ---
        $modulNumber = $this->parseModulNumber($jadwal->judul_modul);

        if ($modulNumber > 1) {
            // Check if there are any unfinished tasks from previous modules
            // A task is considered "unfinished" if it has no grade AND status is not 'reviewed'
            $unfinishedTasks = TugasAsistensi::where('pendaftaran_id', $pendaftaran->id)
                ->where('status', '!=', 'reviewed')
                ->whereNull('nilai')
                ->get();

            foreach ($unfinishedTasks as $task) {
                $taskModulNumber = $this->parseModulNumber($task->judul);
                if ($taskModulNumber < $modulNumber) {
                    return back()->with('error', 'Anda belum menyelesaikan asistensi untuk modul sebelumnya (' . $task->judul . '). Silakan selesaikan asistensi terlebih dahulu.');
                }
            }
        }

        // Generate a short, unique token for the QR code
        $token = Str::random(32);
        
        // Store the attendance data in cache for 30 minutes
        Cache::put("presensi_token_{$token}", [
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_id' => $jadwal->id,
            'user_id' => $user->id,
        ], now()->addMinutes(30));

        // Use a full URL for the QR code so it's informative when scanned by generic apps
        $qrUrl = route('home') . '/p/' . $token;

        // Generate the SVG QR code using the new library
        // We use a lower correction level (L) and larger size to make it scan faster
        $qrCode = QrCode::size(300)
            ->gradient(15, 23, 42, 30, 64, 175, 'diagonal') // slate-900 to a blue-ish
            ->margin(1)
            ->errorCorrection('M')
            ->generate($qrUrl);

        return view('praktikan.presensi.qr', compact('qrCode', 'qrUrl', 'jadwal', 'pendaftaran', 'token'));
    }

    public function scan(Request $request)
    {
        $aslab = Auth::user()->aslab;
        if (!$aslab) {
            return redirect()->back()->with('error', 'Data aslab tidak ditemukan.');
        }

        // Get practicums where this user is an aslab
        $praktikumIds = $aslab->praktikums->pluck('id');

        // Get schedules for today related to these practicums
        $today = now()->toDateString();
        
        // Active schedules: Tanggal hari ini
        $schedules = JadwalPraktikum::with(['praktikum', 'presensis.pendaftaran.praktikan.user'])
            ->whereIn('praktikum_id', $praktikumIds)
            ->where('tanggal', $today)
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        // Separate active and finished (based on current time)
        $activeSchedules = $schedules->filter(function($j) {
            return now()->lt(\Carbon\Carbon::parse($j->tanggal . ' ' . $j->waktu_selesai));
        });

        $finishedSchedules = $schedules->filter(function($j) {
            return now()->gt(\Carbon\Carbon::parse($j->tanggal . ' ' . $j->waktu_selesai));
        });

        return view('aslab.presensi.scan', compact('activeSchedules', 'finishedSchedules'));
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        try {
            // Check if it's a full URL and extract the token
            $token = $request->token;
            if (filter_var($token, FILTER_VALIDATE_URL)) {
                $path = parse_url($token, PHP_URL_PATH);
                $segments = explode('/', trim($path, '/'));
                $token = end($segments);
            }

            // Retrieve data from cache
            $data = Cache::get("presensi_token_{$token}");
            
            if (!$data) {
                // Try fallback to encrypted token for backward compatibility during transition if needed
                // But since we are changing it now, we can just return error
                try {
                    $json = decrypt($request->token);
                    $data = json_decode($json, true);
                    if ($data['expires'] < now()->timestamp) {
                        return response()->json(['success' => false, 'message' => 'QR Code sudah kadaluarsa.'], 400);
                    }
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau sudah kadaluarsa.'], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat validasi QR.'], 400);
        }

        $pendaftaran = PendaftaranPraktikum::with('sesi')->findOrFail($data['pendaftaran_id']);
        $jadwal = JadwalPraktikum::findOrFail($data['jadwal_id']);

        // Final validation 
        $alreadyPresent = Presensi::where('jadwal_id', $jadwal->id)
            ->where('pendaftaran_id', $pendaftaran->id)
            ->exists();

        if ($alreadyPresent) {
            return response()->json(['success' => false, 'message' => 'Praktikan sudah melakukan presensi.'], 400);
        }

        // --- Enforcement: Session Match ---
        if ($jadwal->sesi_id && $pendaftaran->sesi_id !== $jadwal->sesi_id) {
            return response()->json([
                'success' => false, 
                'message' => 'Sesi Anda (' . ($pendaftaran->sesi->nama_sesi ?? 'N/A') . ') tidak sesuai dengan jadwal ini.'
            ], 400);
        }

        // Check-in logic
        $status = 'hadir';
        $waktuMulai = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . $jadwal->waktu_mulai);
        if (now()->gt($waktuMulai->addMinutes(15))) {
            $status = 'terlambat';
        }

        Presensi::create([
            'jadwal_id' => $jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'jam_masuk' => now(),
            'status' => $status
        ]);

        $sesiInfo = $pendaftaran->sesi ? 
            "{$pendaftaran->sesi->nama_sesi} ({$pendaftaran->sesi->hari}, {$pendaftaran->sesi->jam_mulai}-{$pendaftaran->sesi->jam_selesai})" : 
            "Tidak Terdaftar Sesi";

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil: ' . $pendaftaran->praktikan->user->name,
            'nama' => $pendaftaran->praktikan->user->name,
            'sesi' => $sesiInfo,
            'status' => $status
        ]);
    }

    public function checkStatus($jadwal_id)
    {
        $user = Auth::user();
        $praktikan = $user->praktikan;

        if (!$praktikan) {
            return response()->json(['present' => false]);
        }

        $jadwal = JadwalPraktikum::findOrFail($jadwal_id);

        $pendaftaran = PendaftaranPraktikum::where('praktikan_id', $praktikan->id)
            ->where('praktikum_id', $jadwal->praktikum_id)
            ->where('status', 'verified')
            ->first();

        if (!$pendaftaran) {
            return response()->json(['present' => false]);
        }

        $present = Presensi::where('jadwal_id', $jadwal->id)
            ->where('pendaftaran_id', $pendaftaran->id)
            ->exists();

        return response()->json(['present' => $present]);
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_praktikums,id',
            'pendaftaran_id' => 'required|exists:pendaftaran_praktikums,id',
            'status' => 'required|in:hadir,terlambat'
        ]);

        $user = Auth::user();
        $jadwal = JadwalPraktikum::findOrFail($request->jadwal_id);

        // Authorization check
        if ($user->role->name === 'Aslab') {
            $aslab = $user->aslab;
            $isAssigned = $aslab->praktikums()->where('praktikum_id', $jadwal->praktikum_id)->exists();
            if (!$isAssigned) {
                return response()->json(['success' => false, 'message' => 'Anda tidak ditugaskan di praktikum ini.'], 403);
            }
        } elseif ($user->role->name !== 'Admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // Check already present
        $alreadyPresent = Presensi::where('jadwal_id', $jadwal->id)
            ->where('pendaftaran_id', $request->pendaftaran_id)
            ->exists();

        if ($alreadyPresent) {
            return response()->json(['success' => false, 'message' => 'Praktikan sudah melakukan presensi.'], 400);
        }

        $presensi = Presensi::create([
            'jadwal_id' => $jadwal->id,
            'pendaftaran_id' => $request->pendaftaran_id,
            'jam_masuk' => now(),
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Presensi manual berhasil dicatat.'
        ]);
    }

    public function destroy($id)
    {
        $presensi = Presensi::with('jadwal')->findOrFail($id);
        $user = Auth::user();

        // Authorization check
        if ($user->role->name === 'Aslab') {
            $aslab = $user->aslab;
            $isAssigned = $aslab->praktikums()->where('praktikum_id', $presensi->jadwal->praktikum_id)->exists();
            if (!$isAssigned) {
                return response()->json(['success' => false, 'message' => 'Anda tidak ditugaskan di praktikum ini.'], 403);
            }
        } elseif ($user->role->name !== 'Admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $presensi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil dibatalkan.'
        ]);
    }

    public function generateJadwalQR($jadwal_id)
    {
        $user = Auth::user();
        
        $jadwal = JadwalPraktikum::with(['praktikum', 'presensis.pendaftaran.praktikan.user'])->findOrFail($jadwal_id);
        
        // Authorization check
        if ($user->role->name === 'Aslab') {
            $aslab = $user->aslab;
            $isAssigned = $aslab->praktikums()->where('praktikum_id', $jadwal->praktikum_id)->exists();
            if (!$isAssigned) {
                return back()->with('error', 'Anda tidak ditugaskan di praktikum ini.');
            }
        } elseif ($user->role->name !== 'Admin') {
            return back()->with('error', 'Akses ditolak.');
        }

        if (!$jadwal->token) {
            $jadwal->token = (string) Str::random(32);
            $jadwal->save();
        }

        // Pointing to a public landing page for better UX with native phone cameras
        $qrUrl = route('presensi.scan-landing', $jadwal->token);

        $qrCode = QrCode::size(400)
            ->gradient(0, 31, 63, 0, 102, 204, 'vertical') // navy to blue
            ->margin(2)
            ->errorCorrection('H')
            ->generate($qrUrl);

        $presentPendaftaranIds = $jadwal->presensis->pluck('pendaftaran_id');
        
        $notPresentStudents = PendaftaranPraktikum::with('praktikan.user')
            ->where('praktikum_id', $jadwal->praktikum_id)
            ->where('status', 'verified')
            ->whereNotIn('id', $presentPendaftaranIds)
            ->get()
            ->sortBy(function($p) {
                return $p->praktikan->user->name;
            });

        return view('aslab.presensi.jadwal-qr', compact('qrCode', 'qrUrl', 'jadwal', 'notPresentStudents'));
    }

    public function downloadJadwalPDF($jadwal_id)
    {
        $jadwal = JadwalPraktikum::with('praktikum')->findOrFail($jadwal_id);
        
        if (!$jadwal->token) {
            $jadwal->token = (string) Str::random(32);
            $jadwal->save();
        }

        $qrUrl = route('presensi.scan-landing', $jadwal->token);
        
        // Generate SVG QR code for the PDF
        $qrCode = base64_encode(QrCode::size(300)->margin(2)->generate($qrUrl));

        // Load logos and convert to base64
        $itatsLogoPath = public_path('image/logo-itats-biru.jpg');
        $rplLogoPath = public_path('image/rplmini.png');
        
        $itatsLogo = "";
        $rplLogo = "";
        
        if (file_exists($itatsLogoPath)) {
            $itatsLogo = base64_encode(file_get_contents($itatsLogoPath));
        }
        
        if (file_exists($rplLogoPath)) {
            $rplLogo = base64_encode(file_get_contents($rplLogoPath));
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('aslab.presensi.jadwal-pdf', compact('qrCode', 'jadwal', 'itatsLogo', 'rplLogo'));
        
        return $pdf->download("QR-Presensi-{$jadwal->judul_modul}.pdf");
    }

    public function scanLanding($token)
    {
        $jadwal = JadwalPraktikum::with('praktikum')->where('token', $token)->firstOrFail();
        
        // If already logged in, we can proceed to scanJadwal automatically or show a nice landing
        if (Auth::check() && Auth::user()->praktikan) {
            return redirect()->route('praktikan.presensi.scan-jadwal', $token);
        }

        return view('presensi.scan-landing', compact('jadwal'));
    }

    public function showScanner()
    {
        $user = Auth::user();
        if (!$user->praktikan) {
            return redirect()->route('login')->with('error', 'Hanya praktikan yang dapat mengakses pemindai.');
        }

        return view('praktikan.presensi.scan');
    }

    public function scanJadwal(Request $request, $token)
    {
        $user = Auth::user();
        if (!$user->praktikan) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai praktikan.');
        }

        $jadwal = JadwalPraktikum::with('praktikum')->where('token', $token)->firstOrFail();
        $praktikan = $user->praktikan;

        // Check if student is enrolled in this praktikum
        $pendaftaran = PendaftaranPraktikum::where('praktikan_id', $praktikan->id)
            ->where('praktikum_id', $jadwal->praktikum_id)
            ->where('status', 'verified')
            ->first();

        if (!$pendaftaran) {
             return redirect()->route('praktikan.dashboard')->with('error', 'Anda tidak terdaftar di praktikum ini.');
        }

        // Check already present
        $alreadyPresent = Presensi::where('jadwal_id', $jadwal->id)
            ->where('pendaftaran_id', $pendaftaran->id)
            ->exists();

        if ($alreadyPresent) {
            return redirect()->route('praktikan.dashboard')->with('info', 'Anda sudah melakukan presensi.');
        }

        // --- Enforcement: Session Match ---
        if ($jadwal->sesi_id && $pendaftaran->sesi_id !== $jadwal->sesi_id) {
            return redirect()->route('praktikan.dashboard')->with('error', 'Sesi Anda tidak sesuai dengan jadwal ini. Silakan presensi di sesi Anda masing-masing.');
        }

        // --- RULE CHECK: Asistensi Above Modul 1 ---
        $modulNumber = $this->parseModulNumber($jadwal->judul_modul);
        if ($modulNumber > 1) {
            $unfinishedTasks = TugasAsistensi::where('pendaftaran_id', $pendaftaran->id)
                ->where('status', '!=', 'reviewed')
                ->whereNull('nilai')
                ->get();

            foreach ($unfinishedTasks as $task) {
                if ($this->parseModulNumber($task->judul) < $modulNumber) {
                     return redirect()->route('praktikan.dashboard')->with('error', 'Anda belum menyelesaikan asistensi untuk modul sebelumnya (' . $task->judul . '). Silakan selesaikan asistensi terlebih dahulu.');
                }
            }
        }

        // Check-in logic
        $status = 'hadir';
        $waktuMulai = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . $jadwal->waktu_mulai);
        if (now()->gt($waktuMulai->addMinutes(15))) {
            $status = 'terlambat';
        }

        Presensi::create([
            'jadwal_id' => $jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'jam_masuk' => now(),
            'status' => $status
        ]);

        return redirect()->route('praktikan.dashboard')->with('success', 'Presensi Berhasil: ' . $jadwal->judul_modul);
    }

    public function publicVerify($token)
    {
        $data = Cache::get("presensi_token_{$token}");

        if (!$data) {
            return view('presensi.public-verify', [
                'status' => 'expired',
                'message' => 'QR Code ini sudah tidak berlaku atau sudah kadaluarsa.'
            ]);
        }

        $pendaftaran = PendaftaranPraktikum::with(['praktikan.user', 'praktikum'])->find($data['pendaftaran_id']);

        if (!$pendaftaran || !$pendaftaran->praktikan || !$pendaftaran->praktikan->user) {
            return view('presensi.public-verify', [
                'status' => 'expired',
                'message' => 'Data pendaftaran tidak ditemukan atau sudah dihapus.'
            ]);
        }

        return view('presensi.public-verify', [
            'status' => 'valid',
            'message' => 'QR Code Valid',
            'nama' => $pendaftaran->praktikan->user->name,
            'praktikum' => $pendaftaran->praktikum->nama_praktikum
        ]);
    }

    private function parseModulNumber($judul)
    {
        // Extract number from "Modul X"
        if (preg_match('/Modul\s+(\d+)/i', $judul, $matches)) {
            return (int)$matches[1];
        }
        return 1; // Default
    }
}

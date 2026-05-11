<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aslab;
use App\Models\AslabApplication;
use App\Models\Praktikan;
use App\Models\RecruitmentPeriod;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecruitmentController extends Controller
{
    public function index()
    {
        $periods = RecruitmentPeriod::withCount('applications')->latest()->get();
        return view('admin.recruitment.index', compact('periods'));
    }

    public function create()
    {
        return view('admin.recruitment.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'min_ipk' => 'required|numeric|between:0,4.00',
            'min_semester' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        RecruitmentPeriod::create($validated);

        return redirect()->route('admin.recruitment.index')->with('success', 'Periode rekrutmen berhasil dibuat.');
    }

    public function show(RecruitmentPeriod $recruitment)
    {
        $recruitment->load([
            'applications.user.praktikan',
            'applications.schedules',
            'schedules.applications.user.praktikan'
        ]);
        return view('admin.recruitment.show', compact('recruitment'));
    }

    public function updateApplicationStatus(Request $request, AslabApplication $application)
    {
        $request->validate([
            'status' => 'required|in:pending,shortlisted,rejected,accepted',
            'admin_notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $application) {
                $oldStatus = $application->status;
                $newStatus = $request->status;

                $application->update([
                    'status' => $newStatus,
                    'admin_notes' => $request->admin_notes,
                ]);

                // Core Logic: Move to Aslab if accepted
                if ($newStatus === 'accepted' && $oldStatus !== 'accepted') {
                    $user = $application->user;
                    $praktikan = $user->praktikan;

                    if (!$praktikan) {
                        throw new \Exception("Data praktikan tidak ditemukan untuk user ini.");
                    }

                    // 1. Create Aslab record
                    Aslab::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'npm' => $praktikan->npm,
                            'no_hp' => $praktikan->no_hp,
                            'jurusan' => $praktikan->jurusan,
                            'angkatan' => $praktikan->angkatan,
                        ]
                    );

                    // 2. Update User Role to Aslab
                    $aslabRole = Role::where('name', 'Aslab')->first();
                    $user->update(['role_id' => $aslabRole->id]);

                    // 3. Optional: Delete Praktikan record (or keep it but they are now Aslab)
                    // $praktikan->delete(); 
                    // Usually we delete it to avoid confusion since one user is either Aslab or Praktikan in the current schema
                    $praktikan->delete();

                    Log::info("User {$user->username} has been promoted to Aslab via recruitment.");
                }
            });

            return back()->with('success', 'Status pendaftaran berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error("Failed to update application status: " . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function validateIpk(RecruitmentPeriod $recruitment)
    {
        $minIpk = $recruitment->min_ipk;
        $applications = $recruitment->applications()->where('status', 'pending')->get();
        
        if ($applications->isEmpty()) {
            return back()->with('info', 'Tidak ada pendaftaran dengan status pending untuk divalidasi.');
        }

        $passedCount = 0;
        $failedCount = 0;

        foreach ($applications as $application) {
            if ($application->ipk >= $minIpk) {
                $application->update(['status' => 'shortlisted']);
                $passedCount++;
            } else {
                $application->update([
                    'status' => 'rejected', 
                    'admin_notes' => 'Otomatis: IPK (' . number_format($application->ipk, 2) . ') di bawah persyaratan minimum (' . number_format($minIpk, 2) . ').'
                ]);
                $failedCount++;
            }
        }

        return back()->with('success', "Validasi IPK selesai. $passedCount pelamar lolos verifikasi, $failedCount pelamar ditolak.");
    }

    public function storeSchedule(Request $request, RecruitmentPeriod $recruitment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'location' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $schedule = $recruitment->schedules()->create($validated);

        // Otomatis masukkan peserta yang lolos seleksi (shortlisted) ke jadwal baru ini
        $shortlistedIds = $recruitment->applications()
            ->where('status', 'shortlisted')
            ->pluck('id');

        if ($shortlistedIds->isNotEmpty()) {
            $schedule->applications()->sync($shortlistedIds);
        }

        return back()->with('success', 'Jadwal tes berhasil dibuat. ' . $shortlistedIds->count() . ' peserta shortlist otomatis didaftarkan.');
    }

    public function destroySchedule(\App\Models\RecruitmentSchedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal tes berhasil dihapus.');
    }

    public function assignApplicants(Request $request, \App\Models\RecruitmentSchedule $schedule)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:aslab_applications,id',
        ]);

        $schedule->applications()->syncWithoutDetaching($request->application_ids);

        return back()->with('success', 'Berhasil menetapkan peserta ke jadwal ini.');
    }

    public function edit(RecruitmentPeriod $recruitment)
    {
        return view('admin.recruitment.edit', compact('recruitment'));
    }

    public function update(Request $request, RecruitmentPeriod $recruitment)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'whatsapp_link' => 'nullable|url',
            'min_ipk' => 'required|numeric|between:0,4.00',
            'min_semester' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $recruitment->update($validated);

        return redirect()->route('admin.recruitment.index')->with('success', 'Periode rekrutmen berhasil diperbarui.');
    }

    public function destroy(RecruitmentPeriod $recruitment)
    {
        $recruitment->delete();
        return redirect()->route('admin.recruitment.index')->with('success', 'Periode rekrutmen berhasil dihapus.');
    }
}

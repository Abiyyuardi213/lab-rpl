<?php

namespace App\Http\Controllers\Praktikan;

use App\Http\Controllers\Controller;
use App\Models\AslabApplication;
use App\Models\RecruitmentPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RecruitmentController extends Controller
{
    public function index()
    {
        $activePeriods = RecruitmentPeriod::where('is_active', true)
            ->where('end_date', '>=', now())
            ->get();

        $myApplications = AslabApplication::with(['period', 'schedules'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('praktikan.recruitment.index', compact('activePeriods', 'myApplications'));
    }

    public function show($id)
    {
        $application = AslabApplication::with(['period', 'schedules'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        return view('praktikan.recruitment.show', compact('application'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recruitment_period_id' => 'required|exists:recruitment_periods,id',
            'cv' => 'required|file|mimes:pdf|max:2048',
            'khs' => 'required|file|mimes:pdf|max:2048',
            'transcript' => 'required|file|mimes:pdf|max:2048',
            'portfolio_url' => 'nullable|url',
            'motivation_letter' => 'nullable|string',
            'ipk' => 'required|numeric|between:0,4',
        ]);

        $user = Auth::user();

        $exists = AslabApplication::where('recruitment_period_id', $request->recruitment_period_id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah mendaftar pada periode ini.');
        }

        $period = RecruitmentPeriod::find($request->recruitment_period_id);


        $cvPath = $request->file('cv')->store('recruitment/cv', 'public');
        $khsPath = $request->file('khs')->store('recruitment/khs', 'public');
        $transcriptPath = $request->file('transcript')->store('recruitment/transcripts', 'public');

        AslabApplication::create([
            'recruitment_period_id' => $request->recruitment_period_id,
            'user_id' => $user->id,
            'cv_path' => $cvPath,
            'khs_path' => $khsPath,
            'transcript_path' => $transcriptPath,
            'portfolio_url' => $request->portfolio_url,
            'motivation_letter' => $request->motivation_letter,
            'ipk' => $request->ipk,
            'status' => 'pending',
        ]);

        return redirect()->route('praktikan.recruitment.index')->with('success', 'Pendaftaran Anda berhasil dikirim.');
    }
}

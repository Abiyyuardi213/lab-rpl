<?php

namespace App\Http\Controllers;

use App\Models\GuestVisit;
use Illuminate\Http\Request;

class GuestVisitController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $today = now()->toDateString();

        $activeVisits = GuestVisit::query()
            ->whereDate('visit_date', $today)
            ->whereNull('ended_at')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('guest_name', 'like', "%{$search}%")
                        ->orWhere('activity_purpose', 'like', "%{$search}%")
                        ->orWhereDate('visit_date', $search);
                });
            })
            ->latest('started_at')
            ->get();

        $completedVisits = GuestVisit::query()
            ->whereNotNull('ended_at')
            ->whereDate('ended_at', $today)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('guest_name', 'like', "%{$search}%")
                        ->orWhere('activity_purpose', 'like', "%{$search}%")
                        ->orWhereDate('visit_date', $search);
                });
            })
            ->latest('ended_at')
            ->take(24)
            ->get();

        return view('guest-visits.index', compact('activeVisits', 'completedVisits', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:500'],
            'activity_purpose' => ['required', 'string', 'max:2000'],
            'lab_condition' => ['required', 'string', 'max:255'],
            'additional_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $now = now();

        GuestVisit::create([
            ...$validated,
            'visit_date' => $now->toDateString(),
            'started_at' => $now,
        ]);

        return redirect()
            ->route('portal-tamu.index')
            ->with('success', 'Check-in tamu berhasil dicatat.');
    }

    public function checkout(GuestVisit $guestVisit)
    {
        if ($guestVisit->ended_at) {
            return redirect()
                ->route('portal-tamu.index', ['mode' => 'checkout'])
                ->with('info', 'Record tamu ini sudah checkout sebelumnya.');
        }

        $guestVisit->update([
            'ended_at' => now(),
        ]);

        return redirect()
            ->route('portal-tamu.index', ['mode' => 'checkout'])
            ->with('success', 'Checkout tamu berhasil dicatat.');
    }
}

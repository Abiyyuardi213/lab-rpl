<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $assignedCount = $user->assignedStudents()->count();
        $totalQuota = $user->aslab ? $user->aslab->aslabPraktikums()->sum('kuota') : 0;
        $myPraktikums = $user->aslab ? $user->aslab->praktikums : collect();

        return view('aslab.dashboard.index', compact('assignedCount', 'totalQuota', 'myPraktikums'));
    }
}

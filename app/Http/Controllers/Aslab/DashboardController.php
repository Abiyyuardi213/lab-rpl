<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $aslab = Auth::user();
        $assignedCount = $aslab->assignedStudents()->count();
        $totalQuota = $aslab->aslabPraktikums()->sum('kuota');
        $myPraktikums = $aslab->aslabPraktikums;

        return view('aslab.dashboard.index', compact('assignedCount', 'totalQuota', 'myPraktikums'));
    }
}

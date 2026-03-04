<?php

namespace App\Http\Controllers\Praktikan;

use App\Http\Controllers\Controller;
use App\Models\Praktikum;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $praktikums = Praktikum::withCount('pendaftarans')
            ->where('status_praktikum', '!=', 'finished')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('praktikan.dashboard', compact('praktikums'));
    }
}

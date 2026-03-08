<?php

namespace App\Http\Controllers;

use App\Models\Praktikum;
use App\Models\Aslab;
use App\Models\Praktikan;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        // Get stats by role or table count
        $stats = [
            'praktikum' => \App\Models\Praktikum::count(),
            'aslab' => \App\Models\Aslab::count(),
            'praktikan' => \App\Models\Praktikan::count() ?: \App\Models\User::whereHas('role', function ($q) {
                $q->where('name', 'Praktikan');
            })->count(),
        ];

        // Get latest active/open praktikum
        $latestPraktikum = \App\Models\Praktikum::whereIn('status_praktikum', ['open_registration', 'on_progress'])->latest()->first();

        return view('welcome', compact('stats', 'latestPraktikum'));
    }

    public function about()
    {
        return view('about');
    }
}

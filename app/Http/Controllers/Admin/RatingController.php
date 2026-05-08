<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index()
    {
        $totalRatings = Rating::count();
        $avgRatingAslab = Rating::avg('rating_asisten');
        $avgRatingPraktikum = Rating::avg('rating_praktikum');

        $ratings = Rating::with(['pendaftaran.praktikan', 'pendaftaran.praktikum', 'pendaftaran.aslab'])
            ->latest()
            ->paginate(20);

        return view('admin.rating.index', compact('ratings', 'totalRatings', 'avgRatingAslab', 'avgRatingPraktikum'));
    }
}

<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function index()
    {
        $aslabId = Auth::user()->aslab->id;

        $query = Rating::whereHas('pendaftaran', function ($q) use ($aslabId) {
            $q->where('aslab_id', $aslabId);
        });

        $totalRatings = $query->count();
        $avgRatingAslab = $query->avg('rating_asisten');
        $avgRatingPraktikum = Rating::avg('rating_praktikum');

        $ratings = $query->with(['pendaftaran.praktikan', 'pendaftaran.praktikum'])
            ->latest()
            ->paginate(20);

        return view('aslab.rating.index', compact('ratings', 'totalRatings', 'avgRatingAslab', 'avgRatingPraktikum'));
    }
}

<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    public function edit()
    {
        $aslab = Auth::user()->aslab;
        return view('aslab.portfolio.edit', compact('aslab'));
    }

    public function update(Request $request)
    {
        $aslab = Auth::user()->aslab;

        $request->validate([
            'bio' => 'nullable|string',
            'skills' => 'nullable|array',
            'instagram_link' => 'nullable|url',
            'github_link' => 'nullable|url',
            'linkedin_link' => 'nullable|url',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'achievements' => 'nullable|array',
            'experience' => 'nullable|array',
            'activities' => 'nullable|array',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $aslab) {
            $data = $request->only(['bio', 'instagram_link', 'github_link', 'linkedin_link']);
            
            // Handle skills
            $data['skills'] = $request->skills ?? [];

            if ($request->hasFile('profile_image')) {
                if ($aslab->profile_image) {
                    Storage::delete('public/' . $aslab->profile_image);
                }
                $data['profile_image'] = $request->file('profile_image')->store('aslab-premium', 'public');
            }

            // Generate slug if not exists
            if (!$aslab->slug) {
                $data['slug'] = Str::slug(Auth::user()->name);
            }

            $aslab->update($data);

            // Sync Achievements
            $aslab->achievements()->delete();
            if ($request->has('achievements')) {
                foreach ($request->achievements as $item) {
                    if (!empty($item['name'])) {
                        $aslab->achievements()->create([
                            'name' => $item['name'],
                            'start_year' => $item['start_year'] ?? null,
                            'end_year' => $item['end_year'] ?? null,
                        ]);
                    }
                }
            }

            // Sync Experiences
            $aslab->experiences()->delete();
            if ($request->has('experience')) {
                foreach ($request->experience as $item) {
                    if (!empty($item['name'])) {
                        $aslab->experiences()->create([
                            'name' => $item['name'],
                            'start_year' => $item['start_year'] ?? null,
                            'end_year' => $item['end_year'] ?? null,
                        ]);
                    }
                }
            }

            // Sync Activities
            $aslab->activities()->delete();
            if ($request->has('activities')) {
                foreach ($request->activities as $item) {
                    if (!empty($item['name'])) {
                        $aslab->activities()->create([
                            'name' => $item['name'],
                            'month' => $item['month'] ?? null,
                            'year' => $item['year'] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Portfolio berhasil diperbarui.');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Pengumuman extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pengumumans';

    protected $fillable = [
        'judul',
        'slug',
        'konten',
        'gambar',
        'kategori',
        'is_active',
        'user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($pengumuman) {
            if (empty($pengumuman->slug)) {
                $baseSlug = Str::slug($pengumuman->judul);
                $slug = $baseSlug;
                $count = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $count++;
                }
                $pengumuman->slug = $slug;
            }
        });
    }
}

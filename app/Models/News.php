<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    protected $fillable = [
        'title','slug','excerpt','body','status','published_at',
        'cover_path','meta_title','meta_description','category','tags','user_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'tags' => 'array',
    ];

    // Pakai slug untuk route model binding
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Buat slug unik
    public static function makeUniqueSlug(string $title): string
    {
        $base = Str::slug(Str::limit($title, 60, '')) ?: 'news';
        $slug = $base;
        $i = 0;
        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-" . (++$i);
        }
        return $slug;
    }

    // Relasi opsional (contoh)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope published (optional)
    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }
}

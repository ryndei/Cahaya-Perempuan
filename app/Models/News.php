<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title','slug','excerpt','body','cover_path',
        'status','published_at','meta_title','meta_description',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /* Route binding pakai slug */
    public function getRouteKeyName(): string { return 'slug'; }

    public function user() { return $this->belongsTo(User::class); }

    /* Scope publik */
    public function scopePublished($q) {
        return $q->where('status','published')
                 ->whereNotNull('published_at')
                 ->where('published_at','<=', now());
    }

    /* Helper: generate slug unik */
    public static function makeUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;
        while (static::withTrashed()->where('slug',$slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }

    /* Auto-clear cache homepage saat diubah */
    protected static function booted(): void
    {
        static::saved(fn() => Cache::forget('home_news_3'));
        static::deleted(fn() => Cache::forget('home_news_3'));
    }
}

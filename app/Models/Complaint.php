<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','code','title','category','description','attachment_path','status','admin_note','reporter_name',
        'reporter_address',
        'reporter_phone',
    ];

    protected static function booted(): void
    {
       static::creating(function (Complaint $c) {
            $c->code    ??= 'CP-'.now()->format('ymd').'-'.strtoupper(Str::random(5));
+            $c->user_id ??= Auth::id(); // pastikan user login saat submit
        });
    }

    public function user() { return $this->belongsTo(User::class); }
}

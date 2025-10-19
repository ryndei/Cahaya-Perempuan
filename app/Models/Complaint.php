<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
   protected $fillable = [
      'user_id','reference_code','title','description','visibility','priority','submitted_at'
    ];
    protected $casts = ['submitted_at'=>'datetime'];

    public function user(){ return $this->belongsTo(User::class); }
}

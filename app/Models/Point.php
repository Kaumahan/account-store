<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $fillable = [
        'amount',
        'source_type',
        'source_id',
        'description',
        // user_id is NOT here; we set it manually via Auth::id()
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
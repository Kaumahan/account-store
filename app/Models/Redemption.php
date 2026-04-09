<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Redemption extends Model
{
    //
    protected $casts = [
        'claimed_at' => 'datetime',
    ];
    protected $fillable = ['user_id', 'amount', 'phone_number', 'status', 'claimed_at', 'processed_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // THIS LINE IS REQUIRED
    protected $fillable = ['body', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}
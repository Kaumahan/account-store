<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Stock extends Model
{
    protected $fillable = ['payment_id','product_id', 'data', 'is_sold'];

    /**
     * Automatic Encryption/Decryption
     */
    protected function casts(): array
    {
        return [
            'data' => 'encrypted',
            'is_sold' => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Payment extends Model
{
    //
    protected $fillable = ['user_id', 'product_id', 'checkout_session_id', 'amount', 'status'];

    /**
     * Get the product associated with the payment.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who made the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }
    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Report;
class Payment extends Model
{
    //
    protected $fillable = ['user_id', 'product_id', 'checkout_session_id', 'amount', 'status'];

    /**
     * Get the product associated with the payment.
     */
    public function product()
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

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    // Helper to check if a report already exists
    public function isReported()
    {
        return $this->reports()->where('status', 'pending')->exists();
    }


    // Helper to check if there is an active report
    public function hasActiveReport()
    {
        return $this->reports()->where('status', 'pending')->exists();
    }

}

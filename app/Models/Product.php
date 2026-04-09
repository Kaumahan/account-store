<?php

namespace App\Models;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADD THIS
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory; // <--- ADD THIS
    use SoftDeletes;

    // Mass assignment protection
    protected $fillable = [
        'name',
        'user_id',
        'slug',
        'description',
        'price',
        'stock',
        'image_url',
        'is_active',
        'deleted_at'
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Get the user (seller) who created this product.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
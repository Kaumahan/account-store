<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADD THIS
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory; // <--- ADD THIS

    // Mass assignment protection
    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'price', 
        'stock', 
        'image_url', 
        'is_active'
    ];
}
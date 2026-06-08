<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'category',
        'price',
        'description',
        'image',
        'barcode',
        'add_ons',
        'is_available',
    ];

    protected $casts = [
        'price' => 'float',
        'add_ons' => 'array',
        'is_available' => 'boolean',
    ];
}

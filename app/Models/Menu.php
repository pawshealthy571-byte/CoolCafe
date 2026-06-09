<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'price',
        'description',
        'image',
        'barcode',
        'barcode_image',
        'add_ons',
        'is_available',
    ];

    protected $casts = [
        'price' => 'float',
        'add_ons' => 'array',
        'is_available' => 'boolean',
    ];
}

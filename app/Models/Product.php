<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'shopify_id',
        'title',
        'sku',
        'quantity',
        'location',
        'price',
        'description',
        'image'
    ];
}

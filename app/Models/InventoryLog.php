<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/InventoryLog.php
class InventoryLog extends Model
{
    protected $fillable = [
        'product_id', 'sku', 'action', 'details', 'performed_by', 'source',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

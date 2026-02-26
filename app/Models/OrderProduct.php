<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $fillable = [
        'order_id',
        'product_variation_id',
        'quantity',
        'price',
        'offer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }
}

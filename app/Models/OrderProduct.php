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
        'tax',
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'offer' => 'decimal:2',
        'tax' => 'decimal:2',
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

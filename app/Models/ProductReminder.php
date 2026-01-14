<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReminder extends Model
{
    protected $fillable = [
        'product_variation_id',
        'user_id',
        'is_notified',
    ];

    protected $casts = [
        'is_notified' => 'boolean',
    ];

   public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

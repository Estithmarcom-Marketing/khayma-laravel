<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'stock_quantity',
        'price',
        'sku',
        'is_active',
        'offer',
        'offer_expired_date',
        'offer_started_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function properties()
    {
        return $this->belongsToMany(
            Property::class,
            'variant_properties'
        )
            ->withPivot('value_ar', 'value_en')
            ->withTimestamps();
    }
}

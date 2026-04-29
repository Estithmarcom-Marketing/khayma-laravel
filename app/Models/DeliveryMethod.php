<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DeliveryMethod extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'name_ar',
        'name_en',
        'is_active',
        'has_shipping_cost',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

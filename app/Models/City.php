<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_ar',
        'is_active',
        'can_ship',
        'zip',
    ];

    public function shipments()
    {
        return $this->hasMany(CityShipment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('can_ship', true);
    }
}

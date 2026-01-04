<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'is_active',
        'can_ship',
    ];

    public function shipments()
    {
        return $this->hasMany(CityShipment::class);
    }
}

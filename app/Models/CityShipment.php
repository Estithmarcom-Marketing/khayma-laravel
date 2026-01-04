<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityShipment extends Model
{
    protected $fillable = [
        'city_id',
        'cost',
        'estimated_delivery_days', ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}

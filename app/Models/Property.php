<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = ['name_ar', 'name_en'];

    public function variations()
    {
        return $this->belongsToMany(ProductVariation::class, 'variant_properties')
            ->withPivot('value_ar', 'value_en')
            ->withTimestamps();
    }
}

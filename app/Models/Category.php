<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'slug_en',
        'slug_ar',
        'description_en',
        'description_ar',
        'parent_id',

    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

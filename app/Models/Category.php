<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
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

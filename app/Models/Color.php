<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name_en',
        'name_ar',
        'code',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
    
}

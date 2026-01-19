<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Size extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name_ar',
        'name_en',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
   

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use InteractsWithMedia , SoftDeletes;

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

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('category')
            ->singleFile();
    }
}

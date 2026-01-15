<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia , SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'slug_ar',
        'slug_en',
        'category_id',
        'brand_id',
        'is_published',
        'meta_title_ar',
        'meta_title_en',
        'meta_description_ar',
        'meta_description_en',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function productVariations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function properties()
    {
        return $this->hasManyThrough(
            Property::class,
            ProductVariation::class,
            'product_id',
            'id',
            'id',
            'id'
        )->withPivot('value_ar', 'value_en');
    }

    public function getAllPropertiesAttribute()
    {
        // Eager load variations + properties to avoid N+1
        return $this->productVariations
            ->load('properties')
            ->flatMap(fn ($variation) => $variation->properties)
            ->unique('id')
            ->values();
    }

    public function sizes(): HasManyThrough
    {
        return $this->hasManyThrough(Size::class, ProductVariation::class);

    }

    public function colors(): HasManyThrough
    {
        return $this->hasManyThrough(Color::class, ProductVariation::class);
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia , Searchable ,SoftDeletes;

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

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'description_ar'=>$this->description_ar,
            'description_en'=>$this->description_en,
            'slug_en' => $this->slug_en,
            'slug_ar' => $this->slug_ar,
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function cartItems(): HasManyThrough
    {
        return $this->hasManyThrough(
            CartProduct::class,
            ProductVariation::class,
            'product_id',
            'product_variation_id',
            'id',
            'id'
        );
    }

    public function reminders(): HasManyThrough
    {
        return $this->hasManyThrough(
            ProductReminder::class,
            ProductVariation::class,
            'product_id',
            'product_variation_id',
            'id',
            'id'
        );
    }

    public function orders()
    {
        return $this->hasManyThrough(
            Order::class,
            OrderProduct::class,
            'product_variation_id',
            'id',
            'id',
            'order_id'
        );
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

    public function getPriceAttribute()
    {
        if (! $this->relationLoaded('productVariations')) {
            return null;
        }

        return $this->getLowestPriceVariation()?->price;
    }

    public function getOfferAttribute()
    {
        if (! $this->relationLoaded('productVariations')) {
            return null;
        }

        return $this->getLowestPriceVariation()?->offer;
    }

    private function getLowestPriceVariation()
    {
        return $this->productVariations->sortBy('price')->first();
    }
}

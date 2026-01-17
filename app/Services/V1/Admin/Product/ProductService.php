<?php

namespace App\Services\V1\Admin\Product;

use App\Models\Product;

class ProductService
{
    public function list()
    {
        return Product::query()
            ->published()
            ->with('category', 'brand', 'media')
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(10);
    }

    public function show(Product $product)
    {
        return $product->load([
            'category:id,name_ar,name_en',
            'brand:id,name_ar,name_en',
            'productVariations' => function ($q) {
                $q->select('id', 'product_id', 'sku', 'price', 'stock_quantity', 'is_active', 'offer', 'offer_started_date', 'offer_expired_date', 'color_id', 'size_id');
            },
            'productVariations.color:id,name_ar,name_en,code',
            'productVariations.size:id,name_ar,name_en',
            'productVariations.properties:id,name_ar,name_en',
            'media',
        ]);
    }

    public function store(array $data)
    {
        $produxt = Product::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'description_ar' => $data['description_ar'],
            'description_en' => $data['description_en'],
            'category_id' => $data['category_id'],
            'brand_id' => $data['brand_id'],
            'is_published' => (bool) $data['is_published'],
            'slug_ar' => $data['slug_ar'],
            'slug_en' => $data['slug_en'],
            'meta_title_ar' => $data['meta_title_ar'],
            'meta_title_en' => $data['meta_title_en'],
            'meta_description_ar' => $data['meta_description_ar'],
            'meta_description_en' => $data['meta_description_en'],

        ]);

        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $image) {
                $produxt->addMedia($image)->toMediaCollection('images');
            }
        }

        return $produxt->load('category', 'brand', 'media')->refresh();
    }

    public function update(Product $product, array $data)
    {
        $product->update([
            'name_ar' => $data['name_ar'] ?? $product->name_ar,
            'name_en' => $data['name_en'] ?? $product->name_en,
            'description_ar' => $data['description_ar'] ?? $product->description_ar,
            'description_en' => $data['description_en'] ?? $product->description_en,
            'category_id' => $data['category_id'] ?? $product->category_id,
            'brand_id' => $data['brand_id'] ?? $product->brand_id,
            'is_published' => $data['is_published'] ?? $product->is_published,
            'slug_ar' => $data['slug_ar'] ?? $product->slug_ar,
            'slug_en' => $data['slug_en'] ?? $product->slug_en,
            'meta_title_ar' => $data['meta_title_ar'] ?? $product->meta_title_ar,
            'meta_title_en' => $data['meta_title_en'] ?? $product->meta_title_en,
            'meta_description_ar' => $data['meta_description_ar'] ?? $product->meta_description_ar,
            'meta_description_en' => $data['meta_description_en'] ?? $product->meta_description_en,
        ]);

        if (isset($data['images']) && is_array($data['images'])) {
            $product->clearMediaCollection('images');
            foreach ($data['images'] as $image) {
                $product->addMedia($image)->toMediaCollection('images');
            }
        }

        return $product->load('category', 'brand', 'media')->refresh();
    }

    public function delete(Product $product)
    {
        return $product->delete();
    }
}

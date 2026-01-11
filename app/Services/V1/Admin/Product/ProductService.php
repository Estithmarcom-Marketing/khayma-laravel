<?php

namespace App\Services\V1\Admin\Product;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Property;

class ProductService
{
    public function list()
    {
        return Product::query()
            ->published()
            ->with('category', 'brand')
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(10);
    }

    public function show(Product $product)
    {
        return $product->load('category', 'brand', 'variations', 'properties');
    }

    public function store(array $data)
    {
        return Product::create([
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

        return $product->refresh();
    }

    public function delete(Product $product)
    {
        return $product->delete();
    }

    public function storeVariations(Product $product, array $data)
    {
        $product->variations()->create([
            'color_id' => $data['color_id'],
            'size_id' => $data['size_id'],
            'sku' => $data['sku'],
            'price' => $data['price'],
            'stock_quantity' => $data['stock_quantity'],
            'is_active' => $data['is_active'],
            'offer' => $data['offer'],
            'offer_expired_date' => $data['offer_expired_date'],
            'offer_started_date' => $data['offer_started_date'],
        ]);

        return $product->refresh();
    }

    public function updateVariation(Product $product, ProductVariation $productVariation, array $data)
    {
        $productVariation->update([
            'color_id' => $data['color_id'] ?? $productVariation->color_id,
            'size_id' => $data['size_id'] ?? $productVariation->size_id,
            'sku' => $data['sku'] ?? $productVariation->sku,
            'price' => $data['price'] ?? $productVariation->price,
            'stock_quantity' => $data['stock_quantity'] ?? $productVariation->stock_quantity,
            'is_active' => $data['is_active'] ?? $productVariation->is_active,
            'offer' => $data['offer'] ?? $productVariation->offer,
            'offer_expired_date' => $data['offer_expired_date'] ?? $productVariation->offer_expired_date,
            'offer_started_date' => $data['offer_started_date'] ?? $productVariation->offer_started_date,
        ]);

        return $product->refresh();
    }

    public function deleteVariation(Product $product, ProductVariation $productVariation)
    {
        return $product->variations()->where('id', $productVariation->id)->delete();
    }

   
}

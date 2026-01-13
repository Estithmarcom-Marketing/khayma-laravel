<?php

namespace App\Services\V1\Admin\Product;

use App\Models\Product;
use App\Models\ProductVariation;

class ProductVariantService
{
    
    public function storeVariations(Product $product, array $data)
    {
        return $product->productVariations()->create([
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

        return $productVariation->load('color', 'size')->refresh();
    }

    public function deleteVariation(Product $product, ProductVariation $productVariation)
    {
        return $productVariation->delete();

    }

    public function showVariation(Product $product, ProductVariation $productVariation)
    {
        return $product->productVariations()->with(['color', 'size'])->where('id', $productVariation->id)->first();
    }

    public function listProductVariations(Product $product)
    {
        return $product->productVariations()->with(['color', 'size'])->paginate(10);
    }
}

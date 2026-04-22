<?php

namespace App\Services\V1\Admin\Product;

use App\Events\Product\ProductStockUpdated;
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
            'offer' => $data['offer'] ?? null,
            'offer_expired_date' => $data['offer_expired_date'] ?? null,
            'offer_started_date' => $data['offer_started_date'] ?? null,
        ]);
    }

    public function updateVariation(ProductVariation $productVariation, array $data)
    {
        $oldStock = $productVariation->stock_quantity;

        $newStock = $data['stock_quantity'] ?? $oldStock;

        $productVariation->update([
            'color_id' => $data['color_id'] ?? $productVariation->color_id,
            'size_id' => $data['size_id'] ?? $productVariation->size_id,
            'sku' => $data['sku'] ?? $productVariation->sku,
            'price' => $data['price'] ?? $productVariation->price,
            'stock_quantity' => $newStock,
            'is_active' => $data['is_active'] ?? $productVariation->is_active,
            'offer' => $data['offer'] ?? $productVariation->offer,
            'offer_expired_date' => $data['offer_expired_date'] ?? $productVariation->offer_expired_date,
            'offer_started_date' => $data['offer_started_date'] ?? $productVariation->offer_started_date,
        ]);

        if ($oldStock == 0 && $newStock > 0) {
            event(new ProductStockUpdated($productVariation->refresh()));
        }

        return $productVariation->load('color', 'size');
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
    public function listLowStock()
    {
        return ProductVariation::query()
            ->lowStock(5)
            ->with([
                'product:id,name_ar,name_en',
                'color:id,name_ar,name_en',
                'size:id,name_ar,name_en'
            ])
            ->orderByRaw('stock_quantity = 0 DESC')
            ->orderBy('stock_quantity')
            ->paginate(10);
    }
}

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
            'color_id' => $data['color_id'] ?? null,
            'size_id' => $data['size_id'] ?? null,
            'sku' => $data['sku'],
            'price' => $data['price'],
            'tax' => $data['tax'] ?? 0,
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

        $productVariation->fill($data)->save();

        if ($oldStock == 0 && $productVariation->stock_quantity > 0) {
            event(new ProductStockUpdated($productVariation->fresh()));
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
        return $product->productVariations()->with(['color', 'size'])->latest()->paginate(10);
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

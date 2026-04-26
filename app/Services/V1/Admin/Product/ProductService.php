<?php

namespace App\Services\V1\Admin\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function list()
    {
        return Product::query()
            ->published()
            ->with(['category:id,name_ar,name_en', 'brand:id,name_ar,name_en', 'media:id,model_id,name,file_name,collection_name,disk'])
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(10);
    }

    public function show(Product $product)
    {
        $product->load([
            'category:id,name_ar,name_en',
            'brand:id,name_ar,name_en',
            'productVariations' => function ($q) {
                $q->select('id', 'product_id', 'sku', 'price', 'stock_quantity', 'is_active', 'offer', 'offer_started_date', 'offer_expired_date', 'color_id', 'size_id');
            },
            'productVariations.color:id,name_ar,name_en,code',
            'productVariations.size:id,name_ar,name_en',
            'productVariations.properties:id,name_ar,name_en',
            'media:id,model_id,name,file_name,collection_name,disk',
        ]);

        return $product;
    }

    public function store(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = $this->createProduct($data);
            $this->createVariations($product, $data['variations']);
            $this->uploadImages($product, $data['images'] ?? []);

            return $product->refresh()->load([
                'category:id,name_ar,name_en',
                'brand:id,name_ar,name_en',
                'variations.color:id,name_ar,name_en',
                'variations.size:id,name_ar,name_en',
                'media:id,model_id,name,file_name,collection_name,disk',
            ]);
        });
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
            $product->clearMediaCollection('products');
            foreach ($data['images'] as $image) {
                $product->addMedia($image)->toMediaCollection('products');
            }
        }
        return $product->load(['category:id,parent_id,name_ar,name_en', 'brand:id,name_ar,name_en', 'media:id,model_id,name,file_name,collection_name,disk'])->refresh();
    }

    public function delete(Product $product)
    {
        return $product->delete();
    }

    private function createProduct(array $data): Product
    {
        return Product::create([
            'name_ar'             => $data['name_ar'],
            'name_en'             => $data['name_en'],
            'description_ar'      => $data['description_ar'] ?? null,
            'description_en'      => $data['description_en'] ?? null,
            'category_id'         => $data['category_id'],
            'brand_id'            => $data['brand_id'],
            'is_published'        => (bool) $data['is_published'],
            'slug_ar'             => $data['slug_ar'],
            'slug_en'             => $data['slug_en'],
            'meta_title_ar'       => $data['meta_title_ar'] ?? null,
            'meta_title_en'       => $data['meta_title_en'] ?? null,
            'meta_description_ar' => $data['meta_description_ar'] ?? null,
            'meta_description_en' => $data['meta_description_en'] ?? null,
        ]);
    }
    private function createVariations(Product $product, array $variations): void
    {
        $product->productVariations()->createMany(
            array_map(fn($variation) => $this->prepareVariation($variation), $variations)
        );
    }
    private function prepareVariation(array $variation): array
    {
        return [
            'color_id'           => $variation['color_id'],
            'size_id'            => $variation['size_id'],
            'sku'                => $variation['sku'],
            'price'              => $variation['price'],
            'stock_quantity'     => $variation['stock_quantity'],
            'is_active'          => (bool) $variation['is_active'],
            'offer'              => $variation['offer'] ?? null,
            'offer_started_date' => $variation['offer_started_date'] ?? null,
            'offer_expired_date' => $variation['offer_expired_date'] ?? null,
        ];
    }
    private function uploadImages(Product $product, array $images): void
    {
        if (empty($images)) {
            return;
        }

        foreach ($images as $image) {
            $product->addMedia($image)->toMediaCollection('products');
        }
    }
}

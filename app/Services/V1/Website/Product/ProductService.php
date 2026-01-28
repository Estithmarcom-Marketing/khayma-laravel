<?php

namespace App\Services\V1\Website\Product;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;

class ProductService
{
    public function filter(array $filters)
    {
        $limit = $filters['limit'] ?? 12;
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $orderBy = $filters['order_by'] ?? 'desc';

        $sortMap = [
            'created_at' => 'products.created_at',
            'min_price' => 'product_variations_min_price',
        ];

        $user = auth()->user();
        $userId = $user?->id;
        $cartId = $user?->cart?->id;

        $query = Product::published()
            ->withAvg('reviews', 'rating')
            ->withCount('orders')
            ->withCount('reviews');

        $this->filterByCategory($query, $filters);
        $this->filterByBrand($query, $filters);
        $this->filterByRating($query, $filters);
        $this->filterBySearch($query, $filters);
        $this->filterByBestSellers($query, $filters);

        $this->filterByPrice($query, $filters);
        $this->filterByColor($query, $filters);
        $this->filterByOffer($query, $filters);

        return $query
            ->withExists([
                'favourites as is_favourite' => fn ($q) => $userId ? $q->where('user_id', $userId) : $q->whereRaw('0 = 1'),
                'cartItems as is_in_cart' => fn ($q) => $cartId
            ? $q->where('cart_id', $cartId)
            : $q->whereRaw('0 = 1'),
            ])
            ->with([
                'brand:id,name_ar,name_en',
                'category:id,name_ar,name_en',
                'productVariations.color:id,code',
                'productVariations.size',
                'media:id,model_id,name,file_name,collection_name,disk',
            ])
            ->orderBy($sortMap[$sortBy] ?? 'created_at',
                $orderBy)
            ->paginate($limit);
    }

    private function filterByCategory($query, array $filters): void
    {
        if (empty($filters['categories']) && empty($filters['subcategories'])) {
            return;
        }

        $ids = collect($filters['subcategories'] ?? []);

        if (! empty($filters['categories'])) {
            $children = Category::whereIn('parent_id', $filters['categories'])
                ->pluck('id');

            $ids = $ids->merge($children)->merge($filters['categories']);
        }

        $query->whereIn('category_id', $ids->unique());

    }

    private function filterByPrice($query, array $filters): void
    {
        if (! isset($filters['min_price']) && ! isset($filters['max_price'])) {
            return;
        }

        if (! isset($filters['min_price'])) {
            $filters['min_price'] = 0;
        }
        if (! isset($filters['max_price'])) {
            $filters['max_price'] = null;
        }
        $query->whereHas('productVariations', function ($q) use ($filters) {
            $q->active()
                ->when($filters['min_price'], fn ($q) => $q->where('price', '>=', $filters['min_price'])
                )
                ->when($filters['max_price'], fn ($q) => $q->where('price', '<=', $filters['max_price'])
                );
        });
    }

    private function filterByBrand($query, array $filters): void
    {
        if (empty($filters['brands'])) {
            return;
        }

        $query->whereIn('brand_id', $filters['brands']);
    }

    private function filterByColor($query, array $filters): void
    {
        if (empty($filters['colors'])) {
            return;
        }

        $query->whereHas('productVariations', function ($q) use ($filters) {
            $q->whereIn('color_id', $filters['colors']);
        });
    }

    private function filterByRating($query, array $filters): void
    {
        if (! isset($filters['rating'])) {
            return;
        }
        $query->having('reviews_avg_rating', '>=', $filters['rating']);
    }

    private function filterByOffer($query, array $filters): void
    {
        if (empty($filters['has_offer'])) {
            return;
        }
        $query->whereHas('productVariations', function ($q) {
            $q->whereNotNull('offer')
                ->where('offer_started_date', '<=', now())
                ->where(function ($q) {
                    $q->whereNull('offer_expired_date')
                        ->orWhere('offer_expired_date', '>=', now());
                });
        });
    }

    private function filterBySearch($query, array $filters): void
    {
        if (empty($filters['search'])) {
            return;
        }

        $term = trim($filters['search']);

        $query->where(function ($q) use ($term) {
            $q->where('name_en', 'LIKE', "%{$term}%")
                ->orWhere('name_ar', 'LIKE', "%{$term}%")
                ->orWhere('slug_en', 'LIKE', "%{$term}%")
                ->orWhere('slug_ar', 'LIKE', "%{$term}%");
        });
    }

    private function filterByBestSellers($query, array $filters): void
    {
        if (empty($filters['best_sellers'])) {
            return;
        }
        $query->withCount('orders')
            ->orderByDesc('orders_count');
    }

    public function showVariations($identifier)
    {

        $variations = ProductVariation::where('product_id', $identifier)
            ->active()
            ->with([
                'color:id,name_ar,name_en,code',
                'size:id,name_ar,name_en',
                'properties:id,name_ar,name_en',
            ])->get();

        return $variations;
    }

    public function showProduct($identifier)
    {
        $user = auth()->user();
        $userId = $user?->id;
        $cartId = $user?->cart?->id;
        $product = Product::where('id', $identifier)
            ->orwhere('slug_en', $identifier)
            ->orwhere('slug_ar', $identifier)
            ->published()
            ->with([
                'category:id,name_ar,name_en',
                'brand:id,name_ar,name_en',
                'media:id,model_id,name,file_name,collection_name,disk',
            ])->withExists([
                'favourites as is_favourite' => fn ($q) => $q->where('user_id', $userId),
                'cartItems as is_in_cart' => fn ($q) => $cartId
                ? $q->where('cart_id', $cartId)
                : $q->whereRaw('0 = 1'),
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')

            ->firstOrFail();

        return $product;
    }

    public function getRelatedProducts($identifier)
    {
        $product = Product::where('id', $identifier)
            ->orWhere('slug_en', $identifier)
            ->orWhere('slug_ar', $identifier)
            ->first();

        if (! $product) {
            return collect();
        }
        $categoryIds = Category::where('id', $product->category_id)
            ->orWhere('parent_id', $product->category_id)
            ->pluck('id')
            ->toArray();

        $relatedProducts = Product::where(function ($query) use ($categoryIds) {
            $query->whereIn('category_id', $categoryIds);
        })
            ->where('id', '!=', $product->id)
            ->published()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withExists([
                'favourites as is_favourite' => function ($q) {
                    $q->where('user_id', auth()->user()->id);
                }, 'cartItems as is_in_cart' => fn ($q) => $q->where('cart_id', auth()->user()->cart?->id),
            ])
            ->with([
                'media:id,model_id,name,file_name,collection_name,disk',
                'productVariations' => function ($query) {
                    $query->select('id', 'product_id', 'price', 'offer')
                        ->orderBy('price', 'asc')
                        ->limit(1);
                },
            ])
            ->limit(5)
            ->get();

        return $relatedProducts;
    }
}

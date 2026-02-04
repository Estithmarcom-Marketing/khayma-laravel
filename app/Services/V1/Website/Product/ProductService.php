<?php

namespace App\Services\V1\Website\Product;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function filter(array $filters)
    {
        $limit   = $filters['limit'] ?? 12;
        $sortBy  = $filters['sort_by'] ?? 'created_at';
        $orderBy = $filters['order_by'] ?? 'desc';

        $sortMap = [
            'created_at' => 'products.created_at',
            'price'      => 'min_price',
        ];

        [$userId, $cartId] = $this->getAuthContext();

        $query = Product::published()
            ->withAvg('reviews', 'rating')
            ->withCount('orders')
            ->withCount('reviews')
            ->addSelect([
                'min_price' => $this->minPriceSubquery(),
            ]);

        $this->filterByCategory($query, $filters);
        $this->filterByBrand($query, $filters);
        $this->filterByRating($query, $filters);
        $this->filterBySearch($query, $filters);
        $this->filterByBestSellers($query, $filters);
        $this->filterByPrice($query, $filters);
        $this->filterByColor($query, $filters);
        $this->filterByOffer($query, $filters);

        return $query
            ->withExists($this->existsConditions($userId, $cartId))
            ->with($this->buildVariationEagerLoad($filters))
            ->with([
                'brand:id,name_ar,name_en',
                'category:id,name_ar,name_en',
                'media:id,model_id,name,file_name,collection_name,disk',
            ])
            ->orderBy($sortMap[$sortBy] ?? 'products.created_at', $orderBy)
            ->paginate($limit);
    }
    public function showProduct($identifier)
    {
        [$userId, $cartId] = $this->getAuthContext();

        return Product::where(function ($q) use ($identifier) {
                $q->where('id', $identifier)
                  ->orWhere('slug_en', $identifier)
                  ->orWhere('slug_ar', $identifier);
            })
            ->published()
            ->with([
                'category:id,name_ar,name_en',
                'brand:id,name_ar,name_en',
                'media:id,model_id,name,file_name,collection_name,disk',
            ])
            ->withExists($this->existsConditions($userId, $cartId))
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->firstOrFail();
    }

    public function showVariations($identifier)
    {
        return ProductVariation::where('product_id', $identifier)
            ->active()
            ->with([
                'color:id,name_ar,name_en,code',
                'size:id,name_ar,name_en',
                'properties:id,name_ar,name_en',
            ])->get();
    }

    public function getRelatedProducts($identifier)
    {
        [$userId, $cartId] = $this->getAuthContext();

        $product = Product::where(function ($q) use ($identifier) {
                $q->where('id', $identifier)
                  ->orWhere('slug_en', $identifier)
                  ->orWhere('slug_ar', $identifier);
            })->first();

        if (! $product) {
            return collect();
        }

        $categoryIds = Category::where('id', $product->category_id)
            ->orWhere('parent_id', $product->category_id)
            ->pluck('id');

        return Product::whereIn('category_id', $categoryIds)
            ->where('id', '!=', $product->id)
            ->published()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withExists($this->existsConditions($userId, $cartId))
            ->addSelect([
                'min_price'       => $this->minPriceSubquery(),
                'min_price_offer' => $this->minPriceOfferSubquery(),
            ])
            ->with('media:id,model_id,name,file_name,collection_name,disk')
            ->limit(5)
            ->get();
    }
    private function filterByCategory($query, array $filters): void
    {
        if (empty($filters['categories']) && empty($filters['subcategories'])) {
            return;
        }

        $ids = collect($filters['subcategories'] ?? []);

        if (! empty($filters['categories'])) {
            $children = Category::whereIn('parent_id', $filters['categories'])->pluck('id');
            $ids = $ids->merge($children)->merge($filters['categories']);
        }

        $query->whereIn('category_id', $ids->unique());
    }

  private function filterByPrice($query, array $filters): void
{
    $minPrice = $filters['min_price'] ?? null;
    $maxPrice = $filters['max_price'] ?? null;

    if ($minPrice === null && $maxPrice === null) {
        return;
    }

    $query->when($minPrice !== null, function ($q) use ($minPrice) {
        $q->whereHas('productVariations', function ($subQuery) use ($minPrice) {
            $subQuery->active()->where('price', '>=', $minPrice);
        });
    })
    ->when($maxPrice !== null, function ($q) use ($maxPrice) {
        $q->whereHas('productVariations', function ($subQuery) use ($maxPrice) {
            $subQuery->active()->where('price', '<=', $maxPrice);
        });
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

        $query->whereHas('productVariations', fn ($q) => $q->whereIn('color_id', $filters['colors']));
    }


    private function filterByRating($query, array $filters): void
    {
        if (! isset($filters['rating'])) {
            return;
        }

        $query->where(
            DB::raw('(SELECT AVG(rating) FROM reviews WHERE reviews.product_id = products.id)'),
            '>=',
            $filters['rating']
        );
    }

    private function filterByOffer($query, array $filters): void
    {
        if (empty($filters['has_offer'])) {
            return;
        }

        $query->whereHas('productVariations', fn ($q) => $this->applyOfferConditions($q));
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

        $query->orderByDesc('orders_count');
    }

   
    private function getAuthContext(): array
    {
        $user   = auth('sanctum')->user();
        return [$user?->id, $user?->cart?->id];
    }


    private function existsConditions(?int $userId, ?int $cartId): array
    {
        return [
            'favourites as is_favourite' => fn ($q) => $userId
                ? $q->where('user_id', $userId)
                : $q->whereRaw('0 = 1'),
            'cartItems as is_in_cart' => fn ($q) => $cartId
                ? $q->where('cart_id', $cartId)
                : $q->whereRaw('0 = 1'),
        ];
    }

  
    private function minPriceSubquery()
    {
        return ProductVariation::select('price')
            ->whereColumn('product_variations.product_id', 'products.id')
            ->active()
            ->orderBy('price')
            ->limit(1);
    }

    private function minPriceOfferSubquery(): ProductVariation
    {
        return ProductVariation::select('offer')
            ->whereColumn('product_variations.product_id', 'products.id')
            ->active()
            ->where('offer_started_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('offer_expired_date')
                    ->orWhere('offer_expired_date', '>=', now());
            })
            ->orderBy('price')
            ->limit(1);
    }

    
    private function applyOfferConditions($q): void
    {
        $q->whereNotNull('offer')
            ->where('offer_started_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('offer_expired_date')
                    ->orWhere('offer_expired_date', '>=', now());
            });
    }

    private function buildVariationEagerLoad(array $filters): array
    {
        return [
            'productVariations' => function ($q) use ($filters) {
                $q->active();

                if (! empty($filters['colors'])) {
                    $q->whereIn('color_id', $filters['colors']);
                }

                $minPrice = $filters['min_price'] ?? null;
                $maxPrice = $filters['max_price'] ?? null;

                if ($minPrice !== null) {
                    $q->where('price', '>=', $minPrice);
                }
                if ($maxPrice !== null) {
                    $q->where('price', '<=', $maxPrice);
                }

                if (! empty($filters['has_offer'])) {
                    $this->applyOfferConditions($q);
                }
            },
            'productVariations.color:id,code',
            'productVariations.size',
        ];
    }
}
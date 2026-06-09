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
        $limit = $filters['limit'] ?? 12;
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $orderBy = $filters['order_by'] ?? 'desc';

        $sortMap = [
            'created_at' => 'products.created_at',
            'price' => 'min_price',
        ];

        [$userId, $cartId] = $this->getAuthContext();
        $variationFilter = $this->variationFilterClosure($filters);

        $query = $this->getProductBaseQuery($variationFilter);

        $this->filterByCategory($query, $filters);
        $this->filterByBrand($query, $filters);
        $this->filterByRating($query, $filters);
        $this->filterBySearch($query, $filters);
        $this->filterByBestSellers($query, $filters);

        $query->whereHas('productVariations', $variationFilter);

        return $query
            ->withExists($this->existsConditions($userId, $cartId))

            ->withCount([
                'productVariations as filtered_variations_count' => $this->makeVariationCountClosure($filters),
            ])
            ->orderBy($sortMap[$sortBy] ?? 'products.created_at', $orderBy)
            ->paginate($limit);
    }

    private function getProductBaseQuery($variationFilter)
    {
        $query = Product::published()
            ->withAvg('reviews', 'rating')
            ->withCount('orders')
            ->withCount('reviews')
            ->addSelect([
                'min_price' => $this->minPriceSubquery(),
                'min_price_offer' => $this->minPriceOfferSubquery(),
            ])
            ->with([
                'productVariations' => $variationFilter,
                'productVariations.color:id,name_ar,name_en,code',
                'productVariations.size',
                'brand:id,name_ar,name_en',
                'category:id,name_ar,name_en',
                'media:id,model_id,name,file_name,collection_name,disk',
            ]);

        return $query;
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
                'productVariations' => function ($q) {
                    $q->selectWithActiveOffer()
                        ->withIsInReminder()
                        ->active();
                },
                'productVariations.color:id,name_ar,name_en,code',
                'productVariations.size',
                'productVariations.properties:id,name_ar,name_en',
            ])
            ->withExists($this->existsConditions($userId, $cartId))
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->firstOrFail();
    }

    public function showVariations($identifier)
    {
        return ProductVariation::selectWithActiveOffer()
            ->withIsInReminder()
            ->where('product_id', $identifier)
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
                'min_price' => $this->minPriceSubquery(),
                'min_price_offer' => $this->minPriceOfferSubquery(),
            ])
            ->with([
                'media:id,model_id,name,file_name,collection_name,disk',
                'productVariations' => function ($q) {
                    $q->selectWithActiveOffer()
                        ->withIsInReminder()
                        ->active();
                },
                'productVariations.color:id,name_ar,name_en,code',
                'productVariations.size',
                'brand:id,name_ar,name_en',
                'category:id,name_ar,name_en',
            ])
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

    private function filterByBrand($query, array $filters): void
    {
        if (empty($filters['brands'])) {
            return;
        }

        $query->whereIn('brand_id', $filters['brands']);
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

    // private function filterBySearch($query, array $filters): void
    // {
    //     if (empty($filters['search'])) {
    //         return;
    //     }

    //     $term = trim($filters['search']);

    //     $query->where(function ($q) use ($term) {
    //         $q->where('name_en', 'LIKE', "%{$term}%")
    //             ->orWhere('name_ar', 'LIKE', "%{$term}%")
    //             ->orWhere('slug_en', 'LIKE', "%{$term}%")
    //             ->orWhere('slug_ar', 'LIKE', "%{$term}%");
    //     });
    // }

    // search using algolia
    private function filterBySearch($query, array $filters): void
    {
        if (empty($filters['search'])) {
            return;
        }

        $term = trim($filters['search']);

        // Get matching IDs from Algolia
        $ids = Product::search($term)
            ->get()
            ->pluck('id');

        if ($ids->isEmpty()) {
            $query->whereRaw('0 = 1');

            return;
        }
        $query->whereIn('products.id', $ids);
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
        $user = auth('sanctum')->user();

        return [$user?->id, $user?->cart?->id];
    }

    private function existsConditions(?int $userId, ?int $cartId): array
    {
        return [
            'favourites as is_favourite' => fn($q) => $userId
                ? $q->where('user_id', $userId)
                : $q->whereRaw('0 = 1'),
            'cartItems as is_in_cart' => fn($q) => $cartId
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

    private function minPriceOfferSubquery()
    {
        $now = now()->format('Y-m-d H:i:s');

        return ProductVariation::select(
            DB::raw("CASE
                WHEN offer_started_date <= '{$now}'
                AND offer_expired_date >= '{$now}'
                THEN offer
                ELSE NULL
            END as offer")
        )
            ->whereColumn('product_variations.product_id', 'products.id')
            ->active()
            ->orderBy('price')
            ->limit(1);
    }

    private function variationFilterClosure(array $filters)
    {
        return function ($q) use ($filters) {
            $onlyActiveOffers = ! empty($filters['has_offer']);

            $q->selectWithActiveOffer([], $onlyActiveOffers)
                ->withIsInReminder()

                ->active();

            if (! empty($filters['colors'])) {
                $q->whereIn('color_id', $filters['colors']);
            }

            if (! empty($filters['has_offer'])) {
                $now = now();
                $q->whereNotNull('offer')
                    ->where('offer_started_date', '<=', $now)
                    ->where('offer_expired_date', '>=', $now);
            }

            if (isset($filters['min_price'])) {
                $q->where('price', '>=', $filters['min_price']);
            }

            if (isset($filters['max_price'])) {
                $q->where('price', '<=', $filters['max_price']);
            }
        };
    }

    private function makeVariationCountClosure($filters)
    {
        return function ($q) use ($filters) {
            $q->active();

            if (! empty($filters['colors'])) {
                $q->whereIn('color_id', $filters['colors']);
            }

            if (! empty($filters['has_offer'])) {
                $now = now();
                $q->whereNotNull('offer')
                    ->where('offer_started_date', '<=', $now)
                    ->where('offer_expired_date', '>=', $now);
            }

            if (isset($filters['min_price'])) {
                $q->where('price', '>=', $filters['min_price']);
            }

            if (isset($filters['max_price'])) {
                $q->where('price', '<=', $filters['max_price']);
            }
        };
    }
    public function getAllForSiteMap()
    {
        return Product::select([
            'id',
            'slug_en',
            'slug_ar',
            'meta_title_en',
            'meta_title_ar',
            'meta_description_en',
            'meta_description_ar'
        ])
            ->published()
            ->latest()
            ->get();
    }
}

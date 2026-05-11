<?php

namespace App\Services\V1\Website\Home;

use App\Models\Banner;
use App\Models\Category;
use App\Models\CommonQuestion;
use App\Models\Product;
use App\Models\Review;

class HomeService
{
    public function getHomeBanners()
    {
        return Banner::query()
            ->with('media')
            ->active()
            ->orderBy('created_at')
            ->get()
            ->groupBy('position');
    }

    public function getCategories()
    {
        return Category::select(['id', 'name_ar', 'name_en'])
            ->
        with(['media:id,model_id,name,file_name,collection_name,disk'])
            ->whereNull('parent_id')
            ->limit(10)
            ->get();
    }

    public function getCommonQuestions()
    {
        return CommonQuestion::query()
            ->latest()
            ->paginate(10);
    }

    public function getLatestProducts()
    {
        $user = auth('sanctum')->user();
        $userId = $user?->id;
        $cartId = $user?->cart?->id;

        return Product::query()
            ->published()
            ->withExists([
                'favourites as is_favourite' => fn ($q) => $userId
                ? $q->where('user_id', $userId)
                : $q->whereRaw('0 = 1'),
                'cartItems as is_in_cart' => fn ($q) => $cartId
                    ? $q->where('cart_id', $cartId)
                    : $q->whereRaw('0 = 1')])
            ->with(['category:id,name_ar,name_en',
                'brand:id,name_ar,name_en',
                'productVariations' => function ($q) {
                    $q->selectWithActiveOffer()
                        ->withIsInReminder()
                        ->active();
                },
                'productVariations.color:id,name_ar,name_en,code',
                'productVariations.size',
                'media:id,model_id,name,file_name,collection_name,disk'])
            ->withCount(['reviews', 'favourites', 'orders'])
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(10);
    }

    public function getCommonProducts()
    {
        $user = auth('sanctum')->user();
        $userId = $user?->id;
        $cartId = $user?->cart?->id;

        return Product::query()
            ->published()
            ->withCount(['reviews', 'favourites', 'orders'])
            ->withAvg('reviews', 'rating')

            ->where(function ($q) {
                $q->whereHas('reviews')
                    ->orWhereHas('favourites');
            })
            ->withExists([
                'favourites as is_favourite' => fn ($q) => $userId
                ? $q->where('user_id', $userId)
                : $q->whereRaw('0 = 1'),
                'cartItems as is_in_cart' => fn ($q) => $cartId
                    ? $q->where('cart_id', $cartId)
                    : $q->whereRaw('0 = 1')])
            ->with([
                'category:id,name_ar,name_en',
                'brand:id,name_ar,name_en',
                'productVariations' => function ($q) {
                    $q->selectWithActiveOffer()
                        ->withIsInReminder()
                        ->active();
                },
                'productVariations.color:id,name_ar,name_en,code',
                'productVariations.size',
                'media:id,model_id,name,file_name,collection_name,disk',
            ])
            ->orderByDesc('reviews_count')
            ->orderByDesc('favourites_count')
            ->paginate(10);
    }

    public function getMostOrderdProducts()
    {
        $user = auth('sanctum')->user();
        $userId = $user?->id;
        $cartId = $user?->cart?->id;

        return Product::query()
            ->published()
            ->withCount(['orders', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->withExists([
                'favourites as is_favourite' => fn ($q) => $userId
                ? $q->where('user_id', $userId)
                : $q->whereRaw('0 = 1'),
                'cartItems as is_in_cart' => fn ($q) => $cartId
                    ? $q->where('cart_id', $cartId)
                    : $q->whereRaw('0 = 1')])
            ->with([
                'category:id,name_ar,name_en',
                'brand:id,name_ar,name_en',
                'productVariations' => function ($q) {
                    $q->selectWithActiveOffer()
                        ->withIsInReminder()
                        ->active();
                },
                'productVariations.color:id,name_ar,name_en,code',
                'productVariations.size',
                'media:id,model_id,name,file_name,collection_name,disk',
            ])
            ->orderByDesc('orders_count')
            ->paginate(10);
    }

    public function getSuggestedProducts()
    {
        $user = auth('sanctum')->user();
        $userId = $user?->id;
        $cartId = $user?->cart?->id;

        return Product::query()
            ->published()
            ->withCount(['reviews', 'favourites', 'orders'])
            ->withAvg('reviews', 'rating')
            ->whereHas('category')
            ->withExists([
                'favourites as is_favourite' => fn ($q) => $userId
                ? $q->where('user_id', $userId)
                : $q->whereRaw('0 = 1'),
                'cartItems as is_in_cart' => fn ($q) => $cartId
                    ? $q->where('cart_id', $cartId)
                    : $q->whereRaw('0 = 1')])
            ->with([
                'category:id,name_ar,name_en',
                'brand:id,name_ar,name_en',
                'productVariations' => function ($q) {
                    $q->selectWithActiveOffer()
                        ->withIsInReminder()
                        ->active();
                },
                'productVariations.color:id,name_ar,name_en,code',
                'productVariations.size',
                'media:id,model_id,name,file_name,collection_name,disk',
            ])
            ->inRandomOrder()
            ->paginate(10);
    }

    public function getHomeReviews()
    {
        return Review::query()
            ->with(['user:id,name', 'user.media'])
            ->where(function ($q) {
                $q->where('rating', '>', 4);
            })
            ->latest()
            ->paginate(10);
    }
}

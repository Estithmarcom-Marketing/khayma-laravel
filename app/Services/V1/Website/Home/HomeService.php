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
        return Category::with(['media', 'subCategories.media'])->whereNull('parent_id')->get();
    }

    public function getCommonQuestions()
    {
        return CommonQuestion::query()
            ->latest()
            ->paginate(10);
    }

    public function getLatestProducts()
    {
        return Product::query()
            ->published()
            ->with(['category:id,name_ar,name_en', 'brand:id,name_ar,name_en', 'productVariations:id,product_id,sku,price,stock_quantity,offer,offer_started_date,offer_expired_date,color_id,size_id', 'media:id,model_id,name,file_name,collection_name,disk'])
            ->latest()
            ->paginate(10);
    }

    public function getCommonProducts()
    {
        return Product::query()
            ->published()
            ->withCount(['reviews', 'favourites'])
            ->where(function ($q) {
                $q->whereHas('reviews')
                    ->orWhereHas('favourites');
            })
            ->with([
                    'category:id,name_ar,name_en',
                    'brand:id,name_ar,name_en',
                    'productVariations:id,product_id,sku,price,stock_quantity,offer,offer_started_date,offer_expired_date,color_id,size_id', 
                    'media:id,model_id,name,file_name,collection_name,disk',
                ])
            ->orderByDesc('reviews_count')
            ->orderByDesc('favourites_count')
            ->paginate(10);
    }

    public function getMostOrderdProducts()
    {
        return Product::query()
            ->published()
            ->withCount(['orders'])
            ->with([
                'category:id,name_ar,name_en',
                'brand:id,name_ar,name_en',
                'productVariations:id,product_id,sku,price,stock_quantity,offer,offer_started_date,offer_expired_date,color_id,size_id', 
                'media:id,model_id,name,file_name,collection_name,disk',
            ])
            ->orderByDesc('orders_count')
            ->paginate(10);
    }
    public function getHomeReviews()
    {
        return Review::query()
            ->with(['product:id,name_ar,name_en,slug_ar,slug_en', 'user:id,name', 'user.media'])
            ->where(function ($q) {
                $q->where('rating', '>', 4);
            })
            ->latest()
            ->paginate(10);
    }
}

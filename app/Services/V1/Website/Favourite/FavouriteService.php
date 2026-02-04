<?php

namespace App\Services\V1\Website\Favourite;

use App\Models\Favourite;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class FavouriteService
{
    public function list()
    {
        $user = auth()->user();
        $favourites = $user->favourites()->with(['product:id,name_ar,name_en,slug_ar,slug_en,description_ar,description_en,category_id,brand_id',

            'product.media:id,model_id,name,file_name,collection_name,disk'])
            ->paginate(10);

        return $favourites;
    }

    public function show(Favourite $favourite)
    {
        return $favourite->load(['product:id,name_ar,name_en,slug_ar,slug_en,description_ar,description_en,category_id,brand_id',
            'product.media:id,model_id,name,file_name,collection_name,disk']);
    }

    public function store(Product $product)
    {
        $user = auth()->user();

        $favourite = $user->favourites()->where('product_id', $product->id)->first();
        if (! $favourite) {
            return $user->favourites()->create([
                'product_id' => $product->id,
            ]);
        }
        return $favourite;
    }

    public function deleteProductFromFavourite(Product $product): mixed
    {
        return DB::transaction(function () use ($product) {
            $user = auth()->user();
            $favourite = $user->favourites()->where('product_id', $product->id)->first();
            if (! $favourite) {
                return false;
            }

            return $favourite->delete();
        });
    }
}

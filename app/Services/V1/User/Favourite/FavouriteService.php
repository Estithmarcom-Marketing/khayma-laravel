<?php

namespace App\Services\V1\User\Favourite;

use App\Models\Favourite;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class FavouriteService
{
    public function list()
    {
        $user = auth('sanctum')->user();
        $favourites = $user->faviourites()->with(['product', 'product.productVariations', 'product.media'])->paginate(10);

        return $favourites;
    }

    public function show(Favourite $favourite)
    {
        return $favourite->load(['product', 'product.productVariations', 'product.media']);
    }

    public function store(Product $product)
    {
        $user = auth('sanctum')->user();

        $favourite = $user->faviourites()->create([
            'product_id' => $product->id, ]);

        return $favourite;
    }

    public function deleteProductFromFavourite(Favourite $favourite)
    {
        return DB::transaction(function () use ($favourite) {
            $user = auth('sanctum')->user();
            $favourite = $user->faviourites()->where('product_id', $favourite->product_id)->first();
            if (! $favourite) {
                return false;
            }

            return $favourite->delete();
        });
    }
}

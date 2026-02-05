<?php

namespace App\Services\V1\Website\Cart;

class CartService
{
    public function getCartItems()
    {
        $user = auth()->user();

        return $user->cart->items->load([
            'productVariation.product:id,name_en,name_ar,slug_en,slug_ar,brand_id,category_id,description_en,description_ar',
            'productVariation.color:id,name_en,name_ar,code',
            'productVariation.size:id,name_en,name_ar',
            'productVariation.product.media:id,model_id,name,file_name,collection_name,disk',
            'productVariation'=> function($q){
                $q->selectWithActiveOffer()
                ->active();
            },
        ]);
    }

    public function addItems(array $data)
    {
        $user = auth()->user();

        if (! $user->cart) {
            $user->cart()->create([]);
        }

        $cart = $user->cart;

        foreach ($data['items'] as $item) {
            $cartItem = $cart->items()->where('product_variation_id', $item['product_variation_id'])->first();

            if ($cartItem) {

                $cartItem->increment('quantity', $item['quantity']);
            } else {

                $cart->items()->create($item);
            }
        }

        return true;
    }

    public function updateItem($variationId, array $data)
    {
        $user = auth()->user();

        $item = $user->cart->items()->where('product_variation_id', $variationId)->first();
        $item->update([
            'quantity' => $data['quantity'],
        ]);

        return $item->load(['productVariation' => function ($q) {
            $q->selectWithActiveOffer()
                ->active();
        }]);
    }

    public function removeItem($variationId)
    {
        $user = auth()->user();

        return $user->cart->items()->where('product_variation_id', $variationId)->delete();
    }

    public function clearCart()
    {
        $user = auth()->user();

        return $user->cart->items()->delete();
    }
}

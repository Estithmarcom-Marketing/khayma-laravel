<?php

namespace App\Services\V1\Website\Cart;

use App\Models\CartProduct;

class CartService
{
    public function getCart()
    {
        $user = auth()->user();

        return $user->cart;
    }

    public function getCartItems()
    {
        $user = auth()->user();

        return $user->cart->items->load([
            'productVariation.product:id,name_en,name_ar,slug_en,slug_ar,brand_id,category_id,description_en,description_ar',
            'productVariation.product.media:id,model_id,name,file_name,collection_name,disk',
            'productVariation.color:id,name_en,name_ar,code',
            'productVariation.size:id,name_en,name_ar',
            'productVariation.properties:id,name_en,name_ar',
        ]);

    }

    public function addItems(array $data)
    {
        $user = auth()->user();

        return $user->cart->items()->createMany($data['items']);
    }

    public function updateItem(CartProduct $cartProduct, array $data)
    {
        $user = auth()->user();

        $item = $user->cart->items()->where('id', $cartProduct->id)->firstOrFail();
        $item->update([
            'quantity' => $data['quantity'],
        ]);

        return $item->load('productVariation')->refresh();
    }

    public function removeItem(CartProduct $cartProduct)
    {
        $user = auth()->user();

        return $user->cart->items()->where('id', $cartProduct->id)->delete();
    }

    public function clearCart()
    {
        $user = auth()->user();

        return $user->cart->items()->delete();
    }
}

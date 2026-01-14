<?php

namespace App\Services\V1\User\Cart;

use App\Models\CartProduct;
use App\Models\ProductVariation;

class CartService
{
    public function getCart()
    {
        $user = auth('sanctum')->user();

        return $user->cart;
    }

    public function getCartItems()
    {
        $user = auth('sanctum')->user();

        return $user->cart->items->load('productVariation');
    }

    public function addItem(ProductVariation $productVariation, array $data)
    {
        $user = auth('sanctum')->user();

        return $user->cart->items()->create([
            'product_variation_id' => $productVariation->id,
            'quantity' => $data['quantity'],
        ]);
    }

    public function updateItem(CartProduct $cartProduct, array $data)
    {
        $user = auth('sanctum')->user();

        $item = $user->cart->items()->where('id', $cartProduct->id)->firstOrFail();
        $item->update([
            'quantity' => $data['quantity'],
        ]);

        return $item->load('productVariation')->refresh();
    }

    public function removeItem(CartProduct $cartProduct)
    {
        $user = auth('sanctum')->user();

        return $user->cart->items()->where('id', $cartProduct->id)->delete();
    }

    public function clearCart()
    {
        $user = auth('sanctum')->user();

        return $user->cart->items()->delete();
    }
}

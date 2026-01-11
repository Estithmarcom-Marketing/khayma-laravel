<?php

namespace App\Services\V1\User\Cart;

class CartService
{
    public function getCart()
    {
        $user = auth('sanctum')->user();
       


        return $user->cart;
    }
}

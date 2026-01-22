<?php

namespace App\Services\V1\Website\Order;

use App\Enums\Orders\OrderStatusEnum;
use App\Models\Address;
use App\Models\CityShipment;
use App\Models\Order;
use App\Models\ProductVariation;
use App\Models\PromoCode;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {

            $subtotal = 0;

            foreach ($data['items'] as $item) {
                $variation = ProductVariation::lockForUpdate()->findOrFail(
                    $item['product_variation_id']
                );

                if ($variation->stock_quantity < $item['quantity']) {
                    throw new \Exception('Insufficient stock');
                }
                $price = $variation->price;

                if ($variation->offer > 0) {
                    $price -= $variation->offer;
                }

                $subtotal += $price * $item['quantity'];

                $variation->decrement('stock_quantity', $item['quantity']);
            }
            $shipping_cost = $this->getShippingCost($data['address_id']);
            $promo_code = $this->getPromoCode($data['promo_code']);
            $total = $this->calculateTotal($subtotal, $shipping_cost, $promo_code);
            $order = Order::create([
                'user_id' => auth()->id(),
                'address_id' => $data['address_id'],
                'delivery_method_id' => $data['delivery_method_id'],
                'paument_gateway_id' => null,
                'subtotal_price' => $subtotal,
                'shipping_cost' => $shipping_cost ?? 0,
                'discount_amount' => $total['discount'] ?? 0,
                'total_price' => $total['total'],
                'promo_code' => $data['promo_code'] ?? null,
                'status' => OrderStatusEnum::PENDING,
            ]);
            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'product_variation_id' => $item['product_variation_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            return $order->load([
                'items.productVariation.product',
                'address',
                'deliveryMethod:id,name_ar,name_en',
            ]);
        });
    }

    private function getShippingCost($address_id)
    {
        $address = Address::findOrFail($address_id);
        $cost = CityShipment::select('cost')->where('city_id', $address->city_id)->first();

        return $cost?->cost ?? 0;
    }

    private function getPromoCode($promo_code)
    {
        if ($promo_code) {
            $promo = PromoCode::active()
                ->where('code', $promo_code)
                ->first();

            return $promo;
        }

        return null;
    }

    private function calculateTotal($subtotal, $shipping_cost, $promo_code)
    {
        $discount = 0;
        if ($promo_code) {
            $promo_code->increment('times_used');
            if ($promo_code->is_percentage) {
                $discount = ($subtotal) * ($promo_code->value / 100);
            } else {
                $discount = $promo_code->value;
            }
        }
        $total = ($subtotal + $shipping_cost) - $discount;

        return ['total' => $total, 'discount' => $discount];

    }

    public function show($id)
    {
        return Order::with(['items.productVariation.product:id,name_ar,name_en,slug_ar,slug_en',
            'address:id,name,value,city_id,additional_info',
            'deliveryMethod:id,name_ar,name_en'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

    }

    public function list()
    {
        return auth()->user()->orders()->with([
            'items.productVariation.product:id,name_ar,name_en,slug_ar,slug_en',
            'address:id,name,value,city_id,additional_info',
            'deliveryMethod:id,name_ar,name_en',
        ])->paginate(10);
    }
}

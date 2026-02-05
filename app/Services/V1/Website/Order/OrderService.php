<?php

namespace App\Services\V1\Website\Order;

use App\Enums\Orders\OrderStatusEnum;
use App\Enums\Payments\PaymentStatusEnum;
use App\Events\Order\OrderPlacement;
use App\Models\Address;
use App\Models\CityShipment;
use App\Models\DeliveryMethod;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariation;
use App\Models\PromoCode;
use App\Services\V1\Website\Cart\CartService;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(public CartService $cartService) {}

    public function store(array $data)
    {
        $result = DB::transaction(function () use ($data) {

            $subtotal = 0;
            $user = auth()->user();
            $discountOfOffer = 0;
            $data['items'] = $user->cart->items()->get();
            if ($data['items']->isEmpty()) {
                throw new \LogicException(message: 'Cart is empty');
            }
            foreach ($data['items'] as $item) {
                $variation = ProductVariation::lockForUpdate()->findOrFail(
                    $item['product_variation_id']
                );

                if ($variation->stock_quantity < $item['quantity']) {
                    throw new \LogicException(message: 'Insufficient stock');
                }
                $price = $variation->price;

                if ($variation->offer > 0 && $variation->offer != null && $variation->offer < $variation->price && $variation->offer_started_date <= now() && $variation->offer_expired_date >= now()) {
                    $price -= $variation->offer;
                    $discountOfOffer += $variation->offer * $item['quantity'];
                }

                $subtotal += $price * $item['quantity'];

                $variation->decrement('stock_quantity', $item['quantity']);
            }

            $shipping_cost = $this->getShippingCost($data);

            if (isset($data['promo_code']) && $data['promo_code'] != null) {
                $promo_code = $this->getPromoCode($data['promo_code']);
            }
            $total = $this->calculateTotal($subtotal, $shipping_cost, $promo_code ?? null);

            $order = Order::create([
                'user_id' => auth()->id(),
                'address_id' => $data['address_id'] ?? null,
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'delivery_method_id' => $data['delivery_method_id'],
                'subtotal_price' => $subtotal,
                'shipping_cost' => $shipping_cost ?? 0,
                'discount_amount' => ($total['discount_of_promo_code'] + $discountOfOffer) ?? 0,
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

            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $total['total'],
                'payment_method_id' => $data['payment_method_id'],
                'payment_gateway_id' => null,
                'status' => PaymentStatusEnum::PENDING,
                'transaction_id' => null,
                'payment_response' => null,
                'meta_data' => null,
            ]);
            $user->cart->items()->delete();

            return $order->load([
                'items.productVariation.product',
                'address',
                'deliveryMethod:id,name_ar,name_en',
                'paymentMethod:id,name_ar,name_en',
            ]);
        });
        event(new OrderPlacement($result));

        return $result;
    }

    private function getShippingCost(array $data)
    {
        if (isset($data['delivery_method_id']) && $data['delivery_method_id'] != null) {
            $deliveryMethod = DeliveryMethod::where('id', $data['delivery_method_id'])->first();

            if ($deliveryMethod->has_shipping_cost == false) {
                return 0;
            }
            if (isset($data['address_id']) && $data['address_id'] != null) {
                $address = Address::findOrFail($data['address_id']);
                $cost = CityShipment::select('cost')->where('city_id', $address->city_id)->first();

                return $cost?->cost ?? 0;
            }else {
                throw new \LogicException(message: __('orders.error_address'));
            }
        }

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
        $discount_of_promo_code = 0;
        if ($promo_code) {
            $promo_code->increment('times_used');
            if ($promo_code->is_percentage) {
                $discount_of_promo_code = ($subtotal) * ($promo_code->value / 100);
            } else {
                $discount_of_promo_code = $promo_code->value;
            }
        }
        $total = ($subtotal + $shipping_cost) - $discount_of_promo_code;

        return ['total' => $total, 'discount_of_promo_code' => $discount_of_promo_code];
    }

    public function show($id)
    {
        return Order::with(['items.productVariation.product:id,name_ar,name_en,slug_ar,slug_en',
            'address:id,name,value,city_id,additional_info',
            'deliveryMethod:id,name_ar,name_en',
            'paymentMethod:id,name_ar,name_en',
        ])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

    }

    public function list()
    {
        return auth()->user()->orders()->with([
            'items.productVariation.product:id,name_ar,name_en,slug_ar,slug_en',
            'address:id,name,value,city_id,additional_info',
            'deliveryMethod:id,name_ar,name_en',
            'paymentMethod:id,name_ar,name_en',
        ])->latest()->paginate(10);
    }

    public function listCanceled()
    {
        return auth()->user()->orders()->where('status', OrderStatusEnum::CANCELED)->with([
            'items.productVariation.product:id,name_ar,name_en,slug_ar,slug_en',
            'address:id,name,value,city_id,additional_info',
            'deliveryMethod:id,name_ar,name_en',
            'paymentMethod:id,name_ar,name_en',
        ])->latest()->paginate(10);
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        if (($order->status == OrderStatusEnum::PENDING || $order->status == OrderStatusEnum::PROCESSING) && $order->user_id == auth()->id() && $order->created_at->diffInHours() < 72) {
            $order->update(['status' => OrderStatusEnum::CANCELED]);

            // call refund
            return $order;
        } else {
            throw new \Exception(__('orders.error_cancel'));
        }

    }

    public function reorder($id)
    {
        $order = Order::with('items:id,order_id,product_variation_id,quantity')->findOrFail($id);
        $items = $order->items->map(function ($item) {
            return [
                'product_variation_id' => $item->product_variation_id,
                'quantity' => $item->quantity,
            ];
        });

        return $this->cartService->addItems([
            'items' => $items->toArray(),
        ]);

    }

    public function calculateTotalAmountOfOrder(array $data)
    {
        $subtotal = 0;
        $discountOfOffer = 0;
        $user = auth()->user();
        $data['items'] = $user->cart->items()->get();
        if ($data['items']->isEmpty()) {
            throw new \LogicException(message: 'Cart is empty');
        }
        foreach ($data['items'] as $item) {
            $variation = ProductVariation::findOrFail(
                $item['product_variation_id']
            );

            if ($variation->stock_quantity < $item['quantity']) {
                throw new \LogicException(message: 'Insufficient stock');
            }
            $price = $variation->price;

            if ($variation->offer > 0 && $variation->offer < $variation->price && $variation->offer_started_date <= now() && $variation->offer_expired_date >= now()) {
                $price -= $variation->offer;
                $discountOfOffer += $variation->offer * $item['quantity'];
            }

            $subtotal += $price * $item['quantity'];
        }

        $shipping_cost = $this->getShippingCost($data);

        $promo_code = null;

        if (isset($data['promo_code']) && $data['promo_code'] != null) {
            $promo_code = $this->getPromoCode($data['promo_code']);
        }
        $discountOfPromo = 0;
        if ($promo_code) {

            if ($promo_code->is_percentage) {
                $discountOfPromo = ($subtotal) * ($promo_code->value / 100);
            } else {
                $discountOfPromo = $promo_code->value;
            }
        }
        $total = ($subtotal + $shipping_cost) - $discountOfPromo;

        return ['total' => $total, 'discount' => $discountOfOffer + $discountOfPromo, 'shipping' => $shipping_cost, 'subtotal' => $subtotal];

    }
}

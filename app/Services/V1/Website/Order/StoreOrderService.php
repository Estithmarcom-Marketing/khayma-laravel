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
use App\Models\User;
use App\Services\V1\Website\Cart\CartService;
use Illuminate\Support\Facades\DB;

class StoreOrderService
{

    public function __construct(public CartService $cartService) {}

    public function store(array $data)
    {
        $result = DB::transaction(function () use ($data) {

            $user = auth('sanctum')->user();
            $this->updateUserInfo($user, $data);
            $data['items'] = $this->getCartItems($user);
            if ($data['items']->isEmpty()) {
                throw new \LogicException(message: 'Cart is empty');
            }
            $validatedItems = $this->validateCartItems($data, true);
            $subtotal = $validatedItems['subtotal'];
            $tax = $validatedItems['taxTotal'];
            $discountOfOffer = $validatedItems['discountOfOffer'];
            $enrichedItems = $validatedItems['enrichedItems'];
            $address = $this->getAddress($data, $user);
            $shipping_cost = $this->getShippingCost($data['delivery_method_id'] ?? null, $address);
            $promo_code = $this->getPromoCode($data['promo_code'] ?? null);
            $total = $this->calculateTotal($subtotal, $shipping_cost, $promo_code, true, $tax);

            $order = Order::create([
                'user_id' => $user->id,
                'phone' => $data['phone'] ?? $user->phone,
                'address_details' => $address->value ?? null,
                'address_id' => $data['address_id'] ?? null,
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'delivery_method_id' => $data['delivery_method_id'],
                'subtotal_price' => $subtotal,
                'tax_amount' => $tax,
                'shipping_cost' => $shipping_cost,
                'discount_of_offer' => $discountOfOffer,
                'discount_of_promo_code' => $total['discount_of_promo_code'],
                'total_price' => $total['total'],
                'promo_code' => $data['promo_code'] ?? null,
                'status' => OrderStatusEnum::PENDING,
            ]);

            $order->items()->createMany($enrichedItems->map(function ($item) {
                return [
                    'product_variation_id' => $item->product_variation_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'offer' => $item->offer,
                    'tax' => $item->tax,
                ];
            })->toArray());

            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $total['total'],
                'payment_method_id' => $data['payment_method_id'],
                'payment_gateway_id' => null,
                'gateway' => null,
                'status' => PaymentStatusEnum::PENDING,
                'transaction_id' => null,
                'payment_response' => null,
                'meta_data' => null,
            ]);
            $user->cart->items()->delete();

            return $order->load([
                'items.productVariation.product.category:id,name_ar,name_en',
                'items.productVariation.product.media:id,model_id,name,file_name,collection_name,disk',
                'items.productVariation' => function ($q) {
                    $q->selectWithActiveOffer()
                        ->withIsInReminder()
                        ->active();
                },
                'address.city:id,name_ar,name_en,zip',
                'deliveryMethod:id,name_ar,name_en',
                'paymentMethod:id,name_ar,name_en,type',
                'payments:id,order_id,amount,status,gateway',
            ]);
        });
        event(new OrderPlacement($result));

        return $result;
    }

    private function updateUserInfo(User $user, array $data)
    {
        if ((isset($data['name']) && $data['name'] != null) || (isset($data['email']) && $data['email'] != null)) {
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
            ]);
        }
    }

    private function getCartItems(User $user)
    {
        if (! $user->cart) {
            throw new \LogicException(__('cart.cart_fetch_failed'));
        }

        return $user->cart
            ->items()
            ->lockForUpdate()
            ->get();
    }

    private function validateCartItems(array $data, bool $decrementStock): array
    {
        $calculator = $this->initializeCalculator();

        $variations = $this->loadVariations($data['items']);

        foreach ($data['items'] as $item) {

            $variation = $this->resolveVariation($variations, $item);

            $this->validateStock($variation, $item, $decrementStock);

            $pricing = $this->calculateItemPricing($variation, $item);

            $this->accumulateTotals($calculator, $pricing);

            $this->appendEnrichedItem($calculator, $variation, $item, $pricing);

            $this->decrementStockIfNeeded($variation, $item, $decrementStock);
        }

        return $this->buildResult($calculator);
    }

    private function getShippingCost($deliveryMethodId, $address)
    {
        if ($deliveryMethodId) {
            $deliveryMethod = DeliveryMethod::where('id', $deliveryMethodId)->firstOrFail();

            if (! $deliveryMethod->has_shipping_cost) {
                return 0;
            }
            if (! $address) {
                throw new \LogicException(__('orders.error_address'));
            }
            $cost = CityShipment::select('cost')->where('city_id', $address->city_id)->first();

            return $cost?->cost ?? 0;
        }

        return 0;
    }

    private function getPromoCode($promoCode)
    {
        if ($promoCode) {
            $promo = PromoCode::active()
                ->where('code', $promoCode)
                ->lockForUpdate()
                ->first();
            if (! $promo) {
                throw new \LogicException(__('orders.promo_code_exists'));
            }

            return $promo;
        }

        return null;
    }

    private function getAddress(array $data, User $user)
    {
        if (isset($data['address_id']) && $data['address_id'] != null) {
            $address = Address::findOrFail($data['address_id']);
            if ($address->user_id != $user->id) {
                throw new \LogicException(message: 'Invalid address');
            }

            return $address;
        }

        return null;
    }

    private function calculateTotal(
        $subtotal,
        $shipping_cost,
        $promo_code,
        bool $promoCodeIncrement,
        $tax = 0
    ) {
        $discount_of_promo_code = 0;

        if ($promo_code) {

            if ($promoCodeIncrement) {
                $promo_code->increment('times_used');
            }

            if ($promo_code->is_percentage) {
                $discount_of_promo_code = ($subtotal) * ($promo_code->value / 100);
            } else {
                $discount_of_promo_code = $promo_code->value;
            }
        }

        $total = max(
            0,
            ($subtotal + $tax + $shipping_cost) - $discount_of_promo_code
        );

        return [
            'total' => $total,
            'discount_of_promo_code' => $discount_of_promo_code,
            'tax' => $tax,
        ];
    }

    public function calculateTotalAmountOfOrder(array $data): array
    {

        $user = auth('sanctum')->user();

        $data['items'] = $this->getCartItems($user);

        if ($data['items']->isEmpty()) {
            throw new \LogicException(__('orders.cart_is_empty'));
        }
        $validatedItems = $this->validateCartItems($data, false);
        $subtotal = $validatedItems['subtotal'];
        $discountOfOffer = $validatedItems['discountOfOffer'];
        $tax = $validatedItems['taxTotal'];

        $address = $this->getAddress($data, $user);
        $shipping_cost = $this->getShippingCost($data['delivery_method_id'] ?? null, $address);
        $promo_code = $this->getPromoCode($data['promo_code'] ?? null);

        $total = $this->calculateTotal($subtotal, $shipping_cost, $promo_code, false, $tax);

        return [
            'price_before_offer' => (float) $validatedItems['priceBeforeOffer'],
            'discount_of_offer' => (float) $discountOfOffer,
            'discount_of_promo_code' => (float) $total['discount_of_promo_code'],
            'discount_total' => (float) ($discountOfOffer + $total['discount_of_promo_code']),
            'subtotal' => (float) $subtotal,
            'shipping' => (float) $shipping_cost,
            'tax' => (float) $tax,
            'total' => (float) $total['total'],
        ];
    }

    public function rollbackOrder(Order $order): void
    {
        $user = auth('sanctum')->user();
        foreach ($order->items as $item) {
            $item->productVariation->increment('stock_quantity', $item->quantity);

            $cartItem = $user->cart->items()->where('product_variation_id', $item->product_variation_id)->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $item->quantity);
            } else {
                $user->cart->items()->create([
                    'product_variation_id' => $item->product_variation_id,
                    'quantity' => $item->quantity,
                ]);
            }
        }
        $order->delete();
    }
    private function initializeCalculator(): object
    {
        return (object)[
            'subtotal' => 0,
            'discountOfOffer' => 0,
            'priceBeforeOffer' => 0,
            'taxTotal' => 0,
            'enrichedItems' => collect(),
        ];
    }
    private function loadVariations($items)
    {
        return ProductVariation::lockForUpdate()
            ->whereIn('id', $items->pluck('product_variation_id'))
            ->get()
            ->keyBy('id');
    }
    private function resolveVariation($variations, $item)
    {
        $variation = $variations->get($item->product_variation_id);

        if (!$variation) {
            throw new \LogicException(__('orders.error_product_not_found'));
        }

        return $variation;
    }
    private function validateStock($variation, $item, bool $decrementStock): void
    {
        if (!$decrementStock) {
            return;
        }

        if ($variation->stock_quantity < $item->quantity) {

            $locale = app()->getLocale();

            throw new \LogicException(
                __('orders.error_stock', [
                    'product' => $variation->product->{'name_' . $locale}
                ])
            );
        }
    }
    private function calculateItemPricing($variation, $item): object
    {
        $basePrice = $variation->price;

        $activeOffer = $this->resolveActiveOffer($variation);

        $finalPrice = $basePrice - $activeOffer;

        $taxPerItem = $variation->tax ?? 0;

        return (object)[
            'basePrice' => $basePrice,
            'activeOffer' => $activeOffer,
            'finalPrice' => $finalPrice,
            'taxPerItem' => $taxPerItem,
            'quantity' => $item->quantity,
        ];
    }
    private function resolveActiveOffer($variation): float
    {
        if (
            $variation->offer > 0 &&
            $variation->offer < $variation->price &&
            $variation->offer_started_date <= now() &&
            $variation->offer_expired_date >= now()
        ) {
            return $variation->offer;
        }

        return 0;
    }
    private function accumulateTotals($calc, $pricing): void
    {
        $calc->priceBeforeOffer +=
            $pricing->basePrice * $pricing->quantity;

        $calc->discountOfOffer +=
            $pricing->activeOffer * $pricing->quantity;

        $calc->taxTotal +=
            $pricing->taxPerItem * $pricing->quantity;

        $calc->subtotal +=
            $pricing->finalPrice * $pricing->quantity;
    }
    private function appendEnrichedItem($calc, $variation, $item, $pricing): void
    {
        $calc->enrichedItems->push((object)[
            'product_variation_id' => $variation->id,
            'quantity' => $item->quantity,
            'price' => $pricing->basePrice,
            'offer' => $pricing->activeOffer,
            'tax' => $pricing->taxPerItem,
        ]);
    }
    private function decrementStockIfNeeded($variation, $item, bool $decrementStock)
    {
        if ($decrementStock) {
            $variation->decrement('stock_quantity', $item->quantity);
        }
    }
    private function buildResult($calc): array
    {
        return [
            'subtotal' => $calc->subtotal,
            'discountOfOffer' => $calc->discountOfOffer,
            'priceBeforeOffer' => $calc->priceBeforeOffer,
            'taxTotal' => $calc->taxTotal,
            'enrichedItems' => $calc->enrichedItems,
        ];
    }
}

<?php

namespace App\Services\V1\Website\Order;

use App\Enums\Orders\OrderStatusEnum;
use App\Enums\Payments\PaymentMethodTypeEnum;
use App\Models\Order;
use App\Services\V1\Website\Cart\CartService;
use App\Services\V1\Website\Payment\PaymentService;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public $user;

    public function __construct(protected CartService $cartService, protected PaymentService $paymentService)
    {
        $this->user = auth('sanctum')->user();
    }

    public function show($id)
    {
        return Order::with([
            'items.productVariation.product:id,name_ar,name_en,slug_ar,slug_en',
            'items.productVariation.product.media:id,model_id,name,file_name,collection_name,disk',
            'items.productVariation' => function ($q) {
                $q->selectWithActiveOffer()
                    ->withIsInReminder()
                    ->active();
            },
            'items.productVariation.color:id,name_en,name_ar,code',
            'items.productVariation.size:id,name_en,name_ar',
            'address:id,name,value,city_id,additional_info',
            'address.city.shipments:id,city_id,cost,estimated_delivery_days',
            'deliveryMethod:id,name_ar,name_en',
            'paymentMethod:id,name_ar,name_en',
            'payments:id,order_id,amount,status,gateway',
        ])
            ->where('user_id', $this->user->id)
            ->findOrFail($id);
    }

    public function list()
    {
        return $this->user->orders()->with([
            'items.productVariation.product:id,name_ar,name_en,slug_ar,slug_en',
            'items.productVariation.product.media:id,model_id,name,file_name,collection_name,disk',
            'items.productVariation' => function ($q) {
                $q->selectWithActiveOffer()
                    ->withIsInReminder()
                    ->active();
            },
            'address:id,name,value,city_id,additional_info',
            'address.city.shipments:id,city_id,cost,estimated_delivery_days',
            'deliveryMethod:id,name_ar,name_en',
            'paymentMethod:id,name_ar,name_en',
            'payments:id,order_id,amount,status,gateway',
        ])->latest()->paginate(10);
    }

    public function filter(array $filters)
    {
        $query = $this->user->orders()->with([
            'items.productVariation.product:id,name_ar,name_en,slug_ar,slug_en',
            'items.productVariation.product.media:id,model_id,name,file_name,collection_name,disk',
            'items.productVariation' => function ($q) {
                $q->selectWithActiveOffer()
                    ->withIsInReminder()
                    ->active();
            },
            'address:id,name,value,city_id,additional_info',
            'address.city.shipments:id,city_id,cost,estimated_delivery_days',
            'deliveryMethod:id,name_ar,name_en',
            'paymentMethod:id,name_ar,name_en',
            'payments:id,order_id,amount,status,gateway',
        ]);

        $limit = $filters['limit'] ?? 4;
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $orderBy = $filters['order_by'] ?? 'desc';

        $sortMap = [
            'created_at' => 'created_at',
            'total' => 'total_price',
        ];
        $query->orderBy($sortMap[$sortBy], $orderBy);

        $query->when(isset($filters['status']), function ($q) use ($filters) {
            $q->where('status', OrderStatusEnum::from($filters['status']));
        });

        return $query->paginate($limit);
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status == OrderStatusEnum::PENDING && $order->user_id == $this->user->id && $order->created_at->diffInHours() < 72) {
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

    public function repay(Order $order, array $data)
    {

        if ($order->user_id !== $this->user->id) {
            throw new \LogicException('Unauthorized');
        }

        if ($order->status !== OrderStatusEnum::PENDING) {
            throw new \LogicException('Order is not pending, cannot repay');
        }

        return DB::transaction(function () use ($order, $data) {

            if (isset($data['payment_method_id'])) {
                $order->update([
                    'payment_method_id' => $data['payment_method_id'],
                ]);
            }

            $this->paymentService->resetPayment($order);

            if ($order->paymentMethod->type->value == PaymentMethodTypeEnum::CASH->value) {
                return [
                    'order' => $order->fresh(['payments']),
                    'redirect_url' => null,
                ];
            }

            $dto = $this->paymentService->initiateIfNeeded(
                $order,
                $data['gateway'] ?? null
            );

            return [
                'order' => $order->fresh(['payments']),
                'redirect_url' => $dto?->redirectUrl,
            ];
        });
    }
}

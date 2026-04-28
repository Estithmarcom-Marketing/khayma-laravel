<?php

namespace App\Services\V1\Admin\Order;

use App\Enums\Orders\OrderStatusEnum;
use App\Events\Order\OrderStatusUpdated;
use App\Models\Order;

class OrderService
{

    public function getOrders($filters)
    {
        $search = $filters['search'] ?? null;
        $order_status = $filters['order_status'] ?? null;
        $payment_status = $filters['payment_status'] ?? null;
        $is_delivered = $filters['is_delivered'] ?? null;
        $limit = $filters['per_page'] ?? 10;

        $orders = Order::with(['user', 'items.productVariation.product', 'paymentMethod', 'payments', 'deliveryMethod'])
            ->when($search, function ($query) use ($search) {
                $query->search($search);
            })
            ->when($order_status, function ($query) use ($order_status) {
                $query->where('status', $order_status);
            })
            ->when($payment_status, function ($query) use ($payment_status) {
                $query->paymentStatusFilter($payment_status);
            })
            ->when($is_delivered !== null, function ($query) use ($is_delivered) {
                $query->isDelivered($is_delivered);
            })
            ->latest()
            ->paginate($limit);
        return $orders;
    }

    public function getOrderById($id)
    {
        return Order::with([
            'user',
            'items.productVariation.product',
            'paymentMethod',
            'payments',
            'address',
            'deliveryMethod'
        ])->find($id);
    }

    public function updateOrder($id, $data)
    {
        $order = Order::find($id);
        if (!$order) {
            return null;
        }
        isset($data['order_status']) && $this->updateOrderStatus($order, $data['order_status']);
        isset($data['payment_status']) && $order->payments()->update(['status' => $data['payment_status']]);
        isset($data['is_delivered']) && $order->update(['delivered_at' => $data['is_delivered'] ? now() : null]);
        return $order->fresh();
    }

    private function updateOrderStatus($order, $status)
    {
        $order->update(['status' => $status]);
        if ($status == OrderStatusEnum::DELIVERED) {
            $order->update(['delivered_at' => now()]);
        }
        event(new OrderStatusUpdated($order));
    }
}

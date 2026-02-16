<?php

namespace App\Services\V1\Admin\Order;

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

        $orders = Order::with(['user', 'items.productVariation.product', 'paymentMethod', 'deliveryMethod'])
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
            ->paginate($limit); 
        return $orders;
    }
}

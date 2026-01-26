<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderPlacement;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SendOrderPlacedSms
{
    public function handle(OrderPlacement $event): void
    {
        try {
            $user_id = $event->order->user_id;
            $user = User::findOrFail($user_id);
            // Send SMS message
            Log::info(__('orders.created'), ['Phone' => $user->phone, 'order_id' => $event->order->id]);
        } catch (\Exception $e) {
            Log::error('Order Placed Error', ['error' => $e->getMessage()]);
        }
    }
}

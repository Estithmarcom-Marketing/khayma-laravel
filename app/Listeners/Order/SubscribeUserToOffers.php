<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderPlacement;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Kreait\Laravel\Firebase\Facades\Firebase;

class SubscribeUserToOffers
{

    public function handle(OrderPlacement $event): void
    {
        $order = $event->order;
        $user  = $order->user;

        $isFirstOrder = $user->orders()->count() === 1;

        if (! $isFirstOrder) {
            return;
        }

        $tokens = $user->fcmTokens()->pluck('token')->toArray();

        if (empty($tokens)) {
            return;
        }

        Firebase::messaging()->subscribeToTopic('offers', $tokens);
    }
}

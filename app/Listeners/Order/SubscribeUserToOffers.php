<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderPlacement;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Kreait\Laravel\Firebase\Facades\Firebase;

class SubscribeUserToOffers
{
    public function handle(OrderPlacement $event): void
    {
        $order = $event->order;
        $user  = $order->user;


        $isFirstOrder = $user->orders()->count() === 1;

        if (! $isFirstOrder) {
            Log::info('SubscribeUserToOffers: skipping, not first order', [
                'user_phone' => $user->phone,
                'user_id'    => $user->id
            ]);
            return;
        }

        $tokens = $user->fcmTokens()->pluck('token')->toArray();

        if (empty($tokens)) {
            Log::warning('SubscribeUserToOffers: first order but no FCM tokens found', [
                'user_id' => $user->phone,
            ]);
            return;
        }
        Firebase::messaging()->subscribeToTopic('offers', $tokens);
        Log::info('SubscribeUserToOffers: successfully subscribed to offers topic', [
            'user_id'     => $user->phone,
            'token_count' => count($tokens),
        ]);
    }
}

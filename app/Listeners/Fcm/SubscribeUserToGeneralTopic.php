<?php

namespace App\Listeners\Fcm;

use App\Events\Fcm\UserFcmTokenUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Kreait\Laravel\Firebase\Facades\Firebase;

class SubscribeUserToGeneralTopic
{

    public function handle(UserFcmTokenUpdated $event): void
    {
        $user   = $event->user->fresh();
        $tokens = $user->fcmTokens()->pluck('token')->toArray();
        $hasOrders = $user->orders()->exists();

        if (empty($tokens)) {
            Log::warning('UserFcmTokenUpdated: no FCM tokens found', [
                'user_phone' => $user->phone,
            ]);
            return;
        }
        Firebase::messaging()->subscribeToTopic('general', $tokens);

        if ($hasOrders) {
            Firebase::messaging()->subscribeToTopic('offers', $tokens);
        }

        Log::info('UserFcmTokenUpdated: topics resubscribed', [
            'user_phone'     => $user->phone,
            'subscribed_offers' => $hasOrders,
        ]);
    }
}

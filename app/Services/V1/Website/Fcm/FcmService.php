<?php

namespace App\Services\V1\Website\Fcm;

use App\Enums\Platform\PlatformEnum;
use App\Models\FcmToken;
use App\Services\V1\Admin\Firebase\FcmService as FirebaseFcmService;
use Illuminate\Support\Facades\Log;

class FcmService
{
    public function __construct(public FirebaseFcmService $service) {}
    public function updateFcmToken(array $data)
    {
        $user = auth('sanctum')->user();

        FcmToken::updateOrCreate(
            [
                'user_id' => $user->id,
                'platform' => PlatformEnum::from($data['platform'])
            ],
            [
                'token' => $data['token']
            ]
        );
    }
    public function sendTestNotification(array $data)
    {
        $user = auth('sanctum')->user();
        $tokens = FcmToken::where('user_id', $user->id)->pluck('token')->toArray();
        if (!empty($tokens)) {
            Log::info("Sending test FCM notification to {$user->phone}");
            $this->service->sendToMany($tokens, $data['title'], $data['body']);
        } else {
            Log::info("No FCM tokens found for {$user->phone} in test notification mood");
        }
    }
}

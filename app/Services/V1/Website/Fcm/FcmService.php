<?php

namespace App\Services\V1\Website\Fcm;

use App\Enums\Platform\PlatformEnum;
use App\Events\Fcm\UserFcmTokenUpdated;
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
        event(new UserFcmTokenUpdated($user));
    }
}

<?php

namespace App\Services\V1\Website\Fcm;

use App\Enums\Platform\PlatformEnum;
use App\Models\FcmToken;

class FcmService
{
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
}

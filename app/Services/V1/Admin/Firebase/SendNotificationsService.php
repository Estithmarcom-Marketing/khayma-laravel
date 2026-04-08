<?php

namespace App\Services\V1\Admin\Firebase;

use App\Models\FcmToken;
use App\Services\V1\Admin\Firebase\FcmService;

class SendNotificationsService
{
    public function __construct(public FcmService $service) {}
    public function sendToMany(array $data)
    {
        $userIds = $data['users'];
        $title   = $data['title'];
        $body    = $data['body'];
        $payload = $data['data'] ?? [];

        $tokens = FcmToken::whereIn('user_id', $userIds)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return false;
        }

        return $this->service->sendToMany($tokens, $title, $body, $payload);
    }
    public function sendToTopic(array $data)
    {
        $topic = $data['topic'];
        $title   = $data['title'];
        $body    = $data['body'];
        $payload = $data['data'] ?? [];
        return $this->service->sendToTopic($topic, $title, $body, $payload);
    }
}

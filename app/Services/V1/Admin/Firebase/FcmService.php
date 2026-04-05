<?php

namespace App\Services\Firebase;

use App\Enums\Platform\PlatformEnum;
use App\Models\FcmToken;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmService
{
    protected $messaging;

    public function __construct()
    {
        $this->messaging = Firebase::messaging();
    }

    public function sendToToken(
        string $token,
        string $title,
        string $body,
        array $data = []
    ) {
        $message = CloudMessage::new()
            ->withToken($token)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        return $this->messaging->send($message);
    }

    public function sendToMany(
        array $tokens,
        string $title,
        string $body,
        array $data = []
    ) {
        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        return $this->messaging->sendMulticast($message, $tokens);
    }

}

<?php

namespace App\Services\V1\Website\TqnyatSms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TqnyatSmsService
{
    public function send(string $phone, string $message)
    {
        $phone = str_replace(['+', ' ', '-', '_', '/'], '', $phone);
        $response = Http::withToken(config('services.tqnyat.api_token'))
            ->withHeaders(['Content-Type' => 'application/json'])
            ->retry(3, 100)
            ->timeout(10)
            ->post(config('services.tqnyat.api_url'), [
                'recipients' => [$phone],
                'body' => $message,
                'sender' => config('services.tqnyat.sender'),
            ])
            ->throw()
            ->json();
        Log::info('Tqnyat SMS sent', ['phone' => $phone, 'message' => $message, 'response' => $response]);
        return $response;
    }
}

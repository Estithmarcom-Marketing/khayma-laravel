<?php

namespace App\Services\V1\Website\Payment\Gateways;

use App\Models\Order;
use App\Services\V1\Website\Payment\Contracts\PaymentGatewayInterface;
use App\Services\V1\Website\Payment\DTOs\PaymentResponseDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MyFatoorahGateway implements PaymentGatewayInterface
{
    public function createPayment(Order $order): PaymentResponseDTO
    {
        $payload = $this->createPayload($order);
        Log::info('MyFatoorah payment payload', $payload);

        $response = $this->createCheckoutSession($payload);

        Log::info('MyFatoorah payment response', [
            'order_id' => $order->id,
            'response' => $response,
            'method' => __METHOD__,
        ]);

        $paymentUrl = data_get($response, 'Data.PaymentURL');

        return new PaymentResponseDTO(
            referenceId: data_get($response, 'Data.InvoiceId'),
            redirectUrl: $paymentUrl,
            status: data_get($response, 'IsSuccess') ? 'pending' : 'failed',
            payload: $response
        );
    }

    private function createPayload(Order $order): array
    {
        $user = $order->user;

        return [
            'PaymentMethod' => 'CARD',
            'Order' => [
                'Amount' => (float) $order->total_price,
                'Currency' => 'SAR',
                'Reference' => (string) $order->id,
            ],
            'Customer' => [
                'Name' => $user->name ?? 'Customer',
                'Mobile' => [
                    'CountryCode' => '966',
                    'Number' => substr($order->phone, 3),
                ],
            ],
            'IntegrationUrls' => [
                'Redirection' => config('services.payments_urls.success'),
                'Webhook' => config('services.payments_urls.success'), // TODO: add webhook and change this url
            ],
            'CustomerReference' => (string) $order->id,
            'Language' => app()->isLocale('ar') ? 'AR' : 'EN',
        ];
    }

    private function createCheckoutSession($payload)
    {
        $response = Http::withToken(config('services.myfatoorah.api_key'))
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post(config('services.myfatoorah.api_url'), $payload)
            ->throw()
            ->json();

        return $response;

    }

    public function handleWebhook(array $payload): void {}
}

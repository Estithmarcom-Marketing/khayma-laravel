<?php

namespace App\Services\V1\Website\Payment\Gateways;

use App\Enums\Orders\OrderStatusEnum;
use App\Enums\Payments\MyFatoorahStatusEnum;
use App\Enums\Payments\PaymentStatusEnum;
use App\Models\Order;
use App\Models\Payment;
use App\Services\V1\Website\Payment\Contracts\PaymentGatewayInterface;
use App\Services\V1\Website\Payment\DTOs\PaymentResponseDTO;
use Illuminate\Support\Facades\DB;
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
                'Webhook' => config('services.myfatoorah.webhook_url'),
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

    public function handleWebhook(array $payload): void
    {
        Log::info('MyFatoorah webhook received', $payload);

        if (data_get($payload, 'Event.Name') !== 'PAYMENT_STATUS_CHANGED') {
            return;
        }

        $transactionId = data_get($payload, 'Data.Transaction.Id');
        $paymentId = data_get($payload, 'Data.Transaction.PaymentId');
        $orderId = data_get($payload, 'Data.Invoice.ExternalIdentifier');

        $payment = $this->findPayment($transactionId, $paymentId, $orderId);

        if (! $payment) {
            Log::warning('MyFatoorah webhook payment not found', $payload);

            return;
        }

        $status = strtoupper(data_get($payload, 'Data.Transaction.Status', ''));

        match ($status) {
            MyFatoorahStatusEnum::SUCCESS->value => $this->markAsPaid($payment, $payload),
            MyFatoorahStatusEnum::FAILED->value , MyFatoorahStatusEnum::CANCELED->value => $this->markAsFailed($payment, $payload),
            default => Log::info('Unhandled MyFatoorah status', compact('status')),
        };
    }

    private function findPayment(?string $transactionId, ?string $paymentId, ?string $orderId): ?Payment
    {
        return Payment::query()
            ->when($transactionId, fn ($q) => $q->where('transaction_id', $transactionId))
            ->when(! $transactionId && $paymentId, fn ($q) => $q->where('id', $paymentId))
            ->when(! $transactionId && ! $paymentId && $orderId, fn ($q) => $q->where('order_id', $orderId))
            ->first();

    }

    private function markAsPaid(Payment $payment, array $payload): void
    {
        if ($payment->status === PaymentStatusEnum::COMPLETED) {
            return;
        }

        DB::transaction(function () use ($payment, $payload) {

            $payment->update([
                'status' => PaymentStatusEnum::COMPLETED,
                'transaction_id' => data_get($payload, 'Data.Transaction.Id'),
                'payment_response' => $payload,
            ]);

            if ($payment->order->status === OrderStatusEnum::PENDING) {
                $payment->order->update([
                    'status' => OrderStatusEnum::PROCESSING,
                ]);
            }
        });
    }

    private function markAsFailed(Payment $payment, array $payload): void
    {
        $payment->update([
            'status' => PaymentStatusEnum::FAILED,
            'payment_response' => $payload,
        ]);
    }
}

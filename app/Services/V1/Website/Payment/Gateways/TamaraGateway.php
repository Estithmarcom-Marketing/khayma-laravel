<?php

namespace App\Services\V1\Website\Payment\Gateways;

use App\Enums\Orders\OrderStatusEnum;
use App\Enums\Payments\PaymentStatusEnum;
use App\Enums\Payments\TamaraStatusEnum;
use App\Events\Order\OrderPaid;
use App\Models\Order;
use App\Models\Payment;
use App\Services\V1\Website\Payment\Contracts\PaymentGatewayInterface;
use App\Services\V1\Website\Payment\DTOs\PaymentResponseDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TamaraGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        logger(config('services.tamara'));
    }

    public function createPayment(Order $order): PaymentResponseDTO
    {
        $user = $order->user;

        $payload = $this->createPayload($order);

        $response = $this->createCheckoutSession($payload);

        Log::info('Tamara payment response',
            ['order' => $order,
                'user' => $user,
                'response' => $response,
                'method' => __METHOD__]);

        return new PaymentResponseDTO(
            referenceId: $response['checkout_id'] ?? null,
            redirectUrl: data_get($response, 'checkout_url'),
            status: $response['status'] ?? 'pending',
            payload: $response
        );
    }

    private function createPayload(Order $order)
    {
        $user = $order->user;

        return [
            'total_amount' => $this->money($order->total_price),

            'shipping_amount' => $this->money($order->shipping_cost),
            'tax_amount' => $this->money($order->tax_amount ?? 0),

            'order_reference_id' => (string) $order->id,

            'items' => $order->items->map(fn ($item) => $this->mapItem($item))->toArray(),

            'consumer' => [
                'first_name' => $user->name,
                'last_name' => '.',
                'phone_number' => $order->phone,
            ],

            'country_code' => 'SA',
            'description' => "Order #{$order->id}",

            'merchant_url' => [
                'cancel' => config('services.payments_urls.cancel'),
                'failure' => config('services.payments_urls.failure'),
                'success' => config('services.payments_urls.success'),
                'notification_url' => config('services.tamara.notification_url'),
            ],
            'shipping_address' => $this->mapAddress($order),
            'merchant_id' => (string) config('services.tamara.merchant_id'),
            'locale' => app()->isLocale('ar') ? 'ar_SA' : 'en_US',
        ];
    }

    private function createCheckoutSession($payload)
    {
        Log::info('Payload sent to tamara ', ['payload' => $payload, 'method' => __METHOD__]);

        return Http::withToken(config('services.tamara.token'))
            ->post(config('services.tamara.base_url'), $payload)
            ->throw()
            ->json();
    }

    private function mapItem($item): array
    {
        $price = $item->productVariation->price;
        $quantity = $item->quantity;
        $discount = $item->productVariation->offer;

        return [
            'name' => $item->productVariation->product->name_ar,
            'quantity' => $quantity,
            'reference_id' => (string) $item->product_variation_id,
            'type' => 'Physical',
            'sku' => $item->productVariation->sku ?? 'SKU',
            'total_amount' => $this->money(
                ($price - $discount) * $quantity
            ),

        ];
    }

    private function mapAddress(Order $order): array
    {
        return [
            'city' => $order->address?->city?->name_ar ?? 'City',
            'country_code' => 'SA',
            'first_name' => $order->user->name ?? 'Customer',
            'last_name' => '.',
            'line1' => $order->address_details ?? 'Address',
        ];
    }

    private function money($amount): array
    {
        return [
            'amount' => (float) $amount,
            'currency' => 'SAR',
        ];
    }

    public function handleWebhook(array $payload): void
    {
        Log::info('Tamara webhook received', $payload);

        $checkoutId = data_get($payload, 'checkout_id');
        $orderId = data_get($payload, 'order_reference_id');

        $payment = $this->findPayment($checkoutId, $orderId);

        if (! $payment) {
            Log::warning('Tamara payment not found', $payload);

            return;
        }

        $status = strtoupper(data_get($payload, 'event_type'));

        match ($status) {
            TamaraStatusEnum::CAPTURED->value => $this->markAsPaid($payment, $payload),
            TamaraStatusEnum::DECLINED->value ,TamaraStatusEnum::EXPIRED->value,TamaraStatusEnum::CANCELED->value => $this->markAsFailed($payment, $payload),
            default => Log::info('Unhandled Tamara status', compact('status')),
        };
    }

    private function findPayment(string $transactionId, string $orderId): ?Payment
    {
        return Payment::when($transactionId, fn ($q) => $q->where('transaction_id', $transactionId))
            ->when($orderId, fn ($q) => $q->orWhere('order_id', $orderId))
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
                'payment_response' => $payload,
            ]);

            if ($payment->order->status === OrderStatusEnum::PENDING) {
                $payment->order->update([
                    'status' => OrderStatusEnum::PROCESSING,
                ]);
            }
        });
        $order = $payment->order;
        event(new OrderPaid($order));
    }

    private function markAsFailed(Payment $payment, array $payload): void
    {
        $payment->update([
            'status' => PaymentStatusEnum::FAILED,
            'payment_response' => $payload,
        ]);
    }
}

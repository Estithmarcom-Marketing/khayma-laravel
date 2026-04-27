<?php

namespace App\Services\V1\Website\Payment\Gateways;

use App\Enums\Orders\OrderStatusEnum;
use App\Enums\Payments\PaymentStatusEnum;
use App\Enums\Payments\TabbyStatusEnum;
use App\Events\Order\OrderPaid;
use App\Events\Order\OrderStatusUpdated;
use App\Models\Order;
use App\Models\Payment;
use App\Services\V1\Website\Payment\Contracts\PaymentGatewayInterface;
use App\Services\V1\Website\Payment\DTOs\PaymentResponseDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TabbyGateway implements PaymentGatewayInterface
{
   

    public function createPayment(Order $order): PaymentResponseDTO
    {
        $user = $order->user;
        $payload = $this->createPayload($order);
        Log::info('Tabby payment payload', $payload);

        $response = $this->createCheckoutSession($payload);

        Log::info(
            'Tabby payment response',
            [
                'order' => $order,
                'user' => $user,
                'response' => $response,
                'method' => __METHOD__
            ]
        );
        $redirectUrl = data_get(
            $response,
            'configuration.available_products.installments.0.web_url'
        );

        return new PaymentResponseDTO(
            referenceId: $response['id'] ?? null,
            redirectUrl: $redirectUrl,
            status: $response['status'] ?? null,
            payload: $response
        );
    }

    private function createPayload(Order $order)
    {
        $user = $order->user;

        return [
            'payment' => [
                'amount' => (string) $order->total_price,
                'currency' => 'SAR',
                'description' => "Order #{$order->id}",

                'buyer' => [
                    'name' => $user->name ?? 'Customer',
                    'email' => $user->email,
                    'phone' => $order->phone,
                ],
                'shipping_address' => [
                    'city' => $order->address ? $order->address->city->name_ar : 'City',
                    'address' => $order->address_details ?? 'Address',
                    'zip' => $order->address ? $order->address->city->zip : '00000',
                ],
                'order' => [
                    'reference_id' => (string) $order->id,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'title' => $item->productVariation->product->name_ar ?? 'Product',
                            'quantity' => $item->quantity,
                            'unit_price' => (string) $item->price,
                            'discount_amount' => (string) $item->offer ?? '0',
                            'category' => $item->productVariation->product->category->name_ar ?? 'Category',
                            'reference_id' => (string) $item->product_variation_id,
                        ];
                    })->toArray(),
                ],
                'buyer_history' => [
                    'registered_since' => $user->created_at,
                    'loyalty_level' => 0,
                ],
                'order_history' => [
                    [
                        'purchased_at' => $order->created_at,
                        'amount' => (string) $order->total_price,
                        'status' => 'new',
                        'buyer' => [
                            'name' => $user->name ?? 'Customer',
                            'email' => $user->email,
                            'phone' => $order->phone,
                        ],
                        'shipping_address' => [
                            'city' => $order->address ? $order->address->city->name_ar : 'City',
                            'address' => $order->address_details ?? 'Address',
                            'zip' => $order->address ? $order->address->city->zip : '00000',
                        ],
                    ],
                ],

            ],
            'lang' => app()->isLocale('ar') ? 'ar' : 'en',

            'merchant_code' => config('services.tabby.merchant_code'),
            'merchant_urls' => [
                'success' => config('services.payments_urls.success'),
                'cancel' => config('services.payments_urls.cancel'),
                'failure' => config('services.payments_urls.failure'),
            ],
        ];
    }

    private function createCheckoutSession($payload)
    {
        $response = Http::withToken(config('services.tabby.secret_key'))
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post(config('services.tabby.api_url'), $payload)
            ->throw()
            ->json();

        return $response;
    }

    public function handleWebhook(array $payload): void
    {
        Log::info('Tabby webhook received', $payload);

        $transactionId = data_get($payload, 'id');
        $orderId = data_get($payload, 'order.reference_id');

        $payment = $this->findPayment($transactionId, $orderId);
        if (! $payment) {
            Log::warning('Webhook payment not found', $payload);

            return;
        }

        $status = strtoupper(data_get($payload, 'status', ''));

        match ($status) {
            TabbyStatusEnum::AUTHORIZED->value, TabbyStatusEnum::CLOSED->value => $this->markAsPaid($payment, $payload),
            TabbyStatusEnum::REJECTED->value, TabbyStatusEnum::EXPIRED->value, TabbyStatusEnum::CANCELLED->value => $this->markAsFailed($payment, $payload),
            default => Log::info('Unhandled Tabby webhook status', compact('status')),
        };
    }

    private function findPayment(string $transactionId, string $orderId): ?Payment
    {
        return Payment::when($transactionId, fn($q) => $q->where('transaction_id', $transactionId))
            ->when($orderId, fn($q) => $q->orWhere('order_id', $orderId))
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
        event(new OrderStatusUpdated($order));
    }

    private function markAsFailed(Payment $payment, array $payload): void
    {
        $payment->update([
            'status' => PaymentStatusEnum::FAILED,
            'payment_response' => $payload,
        ]);
    }
}

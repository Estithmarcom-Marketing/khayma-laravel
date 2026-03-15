<?php

namespace App\Http\Controllers\Api\V1\Application\PaymentWebhook;

use App\Enums\Payments\PaymentGatewayEnum;
use App\Http\Controllers\Controller;
use App\Services\V1\Website\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function tabby(Request $request)
    {
        return $this->handle($request, PaymentGatewayEnum::TABBY);
    }

    public function tamara(Request $request)
    {
        return $this->handle($request, PaymentGatewayEnum::TAMARA);
    }
    public function myfatoorah(Request $request)
    {
        return $this->handle($request, PaymentGatewayEnum::MYFATOORAH);
    }

    private function handle(Request $request, PaymentGatewayEnum $gateway)
    {
        try {
            Log::info('Webhook received', [
                'gateway' => $gateway->value,
                'payload' => $request->all(),
            ]);

            $this->paymentService->handleWebhook($gateway, $request->all());

            return response()->json(['status' => 'ok'], Response::HTTP_OK);

        } catch (\Throwable $e) {
            Log::error('Webhook failed', [
                'gateway' => $gateway->value,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error'], Response::HTTP_OK);
        }
    }
}


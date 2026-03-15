<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyMyFatoorahSignature
{
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('myfatoorah-signature');

        if (! $signature) {
            return response()->json([
                'message' => 'Missing MyFatoorah signature',
            ], 401);
        }

        $payload = $request->all();

        $dataString = sprintf(
            'Invoice.Id=%s,Invoice.Status=%s,Transaction.Status=%s,Transaction.PaymentId=%s,Invoice.ExternalIdentifier=%s',
            data_get($payload, 'Data.Invoice.Id', ''),
            data_get($payload, 'Data.Invoice.Status', ''),
            data_get($payload, 'Data.Transaction.Status', ''),
            data_get($payload, 'Data.Transaction.PaymentId', ''),
            data_get($payload, 'Data.Invoice.ExternalIdentifier', '')
        );

        $secret = config('services.myfatoorah.webhook_secret');

        $generatedSignature = base64_encode(
            hash_hmac('sha256', $dataString, $secret, true)
        );

        if (! hash_equals($generatedSignature, $signature)) {

            Log::warning('Invalid MyFatoorah signature', [
                'expected' => $generatedSignature,
                'received' => $signature,
                'string' => $dataString,
            ]);

            return response()->json([
                'message' => 'Invalid signature',
            ], 401);
        }

        return $next($request);
    }
}

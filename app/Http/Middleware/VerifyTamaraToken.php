<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyTamaraToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.tamara.notification_token');
        Log::info('Tamara Headers', $request->headers->all());
        $payload = $request->getContent();
        $incomingSignature = $request->header('X-Tamara-Signature');

        if (!$incomingSignature) {
            Log::warning('Tamara webhook missing signature');
            abort(403, 'Missing signature');
        }

        $generatedSignature = hash_hmac(
            'sha256',
            $payload,
            $secret
        );

        if (!hash_equals($generatedSignature, $incomingSignature)) {
            Log::warning('Invalid Tamara signature', [
                'payload' => $payload,
                'generated_signature' => $generatedSignature,
                'incoming_signature' => $incomingSignature,
            ]);

            abort(403, 'Invalid signature');
        }

        return $next($request);
    }
}

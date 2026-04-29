<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
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
        $notificationToken = config('services.tamara.notification_token');

        if (!$notificationToken) {
            Log::critical('Tamara notification token missing');
            abort(500);
        }
        $token =
            $request->bearerToken()
            ?? $request->input('tamaraToken')
            ?? $request->query('tamaraToken');
        Log::debug('Tamara webhook token', [
            'token' => $token,
            'request' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        if (!$token) {
            Log::warning('Tamara webhook missing token');
            abort(403, 'Missing Tamara token');
        }

        try {
            $decoded = JWT::decode(
                $token,
                new Key($notificationToken, 'HS256')
            );

            if (($decoded->iss ?? null) !== 'Tamara') {
                throw new \Exception('Invalid issuer');
            }

            Log::info('Tamara webhook verified', [
                'iss' => $decoded->iss,
                'exp' => $decoded->exp ?? null,
            ]);
        } catch (\Throwable $e) {

            Log::warning('Invalid Tamara webhook', [
                'error' => $e->getMessage(),
            ]);

            abort(403, 'Invalid Tamara webhook');
        }

        return $next($request);
    }
}

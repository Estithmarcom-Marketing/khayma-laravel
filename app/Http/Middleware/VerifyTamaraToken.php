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
        $secret = config('services.tamara.notification_token');

        $token = $request->bearerToken();

        if (!$token) {
            $token = $request->input('tamaraToken');
        }

        if (!$token) {
            Log::warning('Tamara webhook missing token');
            abort(403, 'Missing Tamara token');
        }

        try {
            $decoded = JWT::decode(
                $token,
                new Key($secret, 'HS256')
            );

            if (($decoded->iss ?? null) !== 'Tamara') {
                throw new \Exception('Invalid issuer');
            }
            Log::info('Tamara webhook verified', ['iss' => $decoded->iss]);
        } catch (\Throwable $e) {

            Log::warning('Invalid Tamara JWT', [
                'error' => $e->getMessage(),
            ]);

            abort(403, 'Invalid Tamara token');
        }

        return $next($request);
    }
}

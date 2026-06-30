<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyTabbyIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $allowedIps = config('services.tabby.allowed_ips');

        if (! in_array($request->ip(), $allowedIps)) {
            Log::warning('Unauthorized IP address attempted to access Tabby webhook', [
                'ip' => $request->ip(),
                'request' => $request->all()
            ]);
            abort(403, 'Unauthorized IP');
        }
        Log::info('Authorized IP address accessed Tabby webhook', [
            'ip' => $request->ip(),
            'request' => $request->all()
        ]);

        return $next($request);
    }
}

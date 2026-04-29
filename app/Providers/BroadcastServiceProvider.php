<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */

    public function boot(): void
    {
        Broadcast::routes([
            'middleware' => ['auth:admin'],
        ]);
        app('auth')->shouldUse('admin');
        require base_path('routes/channels.php');
    }
}

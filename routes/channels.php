<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
Broadcast::routes([
    'middleware' => ['auth:sanctum'],
    'prefix' => 'api',
]);

Broadcast::channel('admins.notifications', function ($admin) {
    Log::info('Broadcast auth hit', ['admin_id' => $admin?->id]);
    // return $admin->can('read-notifications');
    return true;
}, ['guards' => ['admin']]);

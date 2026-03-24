<?php

namespace App\Models;

use App\Enums\Platform\PlatformEnum;
use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'platform',
    ];

    protected $casts = [
        'timestamps' => 'datetime',
        'platform' =>PlatformEnum::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

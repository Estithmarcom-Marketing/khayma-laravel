<?php

namespace App\Models;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'type',
        'is_read',
        'notifiable_id',
        'notifiable_type',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'type' => NotificationTypeEnum::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}

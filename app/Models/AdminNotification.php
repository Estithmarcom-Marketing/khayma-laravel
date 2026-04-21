<?php

namespace App\Models;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'body_ar',
        'body_en',
        'type',
        'is_read',
        'notifiable_id',
        'notifiable_type',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'type' => NotificationTypeEnum::class,
    ];
    public function notifiable()
    {
        return $this->morphTo();
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}

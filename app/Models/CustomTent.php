<?php

namespace App\Models;

use App\Enums\CustomTent\CustomTentStatus;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CustomTent extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'user_name',
        'phone',
        'size',
        'description',
        'status'
    ];
    protected $casts = [
        'status' => CustomTentStatus::class,
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}

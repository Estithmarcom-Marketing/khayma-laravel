<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Setting extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'facebook',
        'instagram',
        'x',
        'snapchat',
        'tiktok',
        'linkedin',
        'address',
        'phone',
        'email',
        'whatsapp',
        'telegram',
    ];
}

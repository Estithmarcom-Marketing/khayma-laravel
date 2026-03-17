<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
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

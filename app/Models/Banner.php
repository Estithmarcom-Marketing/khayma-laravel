<?php

namespace App\Models;

use App\Enums\BannerPosition\BannerPositionEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Banner extends Model implements HasMedia
{
    use InteractsWithMedia,SoftDeletes;
    protected $fillable = [
        'title_ar',
        'title_en',
        'position',
        'redirect_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => BannerPositionEnum::class,
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

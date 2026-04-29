<?php

namespace App\Models;

use App\Enums\Payments\PaymentGatewayEnum;
use App\Enums\Payments\PaymentMethodTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PaymentMethod extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'name_ar',
        'name_en',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'type' => PaymentMethodTypeEnum::class,
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentGateways()
    {
        return $this->hasMany(PaymentGateway::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function supportsGateway(PaymentGatewayEnum $gateway): bool
    {
        if ($this->relationLoaded('paymentGateways')) {
            return $this->paymentGateways->contains('gateway', $gateway);
        }

        return $this->paymentGateways()
            ->where('gateway', $gateway)
            ->exists();
    }
}

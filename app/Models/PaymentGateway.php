<?php

namespace App\Models;

use App\Enums\Payments\PaymentGatewayEnum;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'gateway',
        'payment_method_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'gateway' => PaymentGatewayEnum::class,
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

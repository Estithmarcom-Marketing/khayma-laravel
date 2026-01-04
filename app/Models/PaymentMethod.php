<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
}

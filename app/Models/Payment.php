<?php

namespace App\Models;

use App\Enums\Payments\PaymentGatewayEnum;
use App\Enums\Payments\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method_id',
        'payment_gateway_id',
        'gateway',
        'amount',
        'status',
        'transaction_id',
        'payment_response',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'status' =>PaymentStatusEnum::class,
        'gateway' => PaymentGatewayEnum::class,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }
}

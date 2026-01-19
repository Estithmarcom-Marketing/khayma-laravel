<?php

namespace App\Models;

use App\Enums\Orders\OrderStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'address_id',
        'payment_gateway_id',
        'delivery_method_id',
        'total_price',
        'subtotal_price',
        'shipping_cost',
        'tax_amount',
        'discount_amount',
        'promo_code',
        'status',
    ];

    protected $casts = [
        'total_price' => 'decimal,2',
        'subtotal_price' => 'decimal,2',
        'shipping_cost' => 'decimal,2',
        'tax_amount' => 'decimal,2',
        'discount_amount' => 'decimal,2',
        'status' => OrderStatusEnum::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
  

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function deliveryMethod()
    {
        return $this->belongsTo(DeliveryMethod::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function items()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}

<?php

namespace App\Models;

use App\Enums\Orders\OrderStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model implements HasMedia
{
    use HasUlids ,InteractsWithMedia , SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'phone',
        'address_id',
        'address_details',
        'delivery_method_id',
        'payment_method_id',
        'total_price',
        'subtotal_price',
        'shipping_cost',
        'tax_amount',
        'discount_of_offer',
        'discount_of_promo_code',
        'promo_code',
        'status',
        'delivered_at',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'subtotal_price' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'status' => OrderStatusEnum::class,
        'delivered_at' => 'datetime',
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

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
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

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->search($search);
            })
                ->orWhere('id', 'like', '%'.$search.'%');
        }
    }

    public function scopeIsDelivered($query, bool $isDelivered)
    {
        if ($isDelivered) {
            $query->whereNotNull('delivered_at');
        } else {
            $query->whereNull('delivered_at');
        }
    }

    public function scopePaymentStatusFilter($query, $status)
    {
        if ($status) {
            $query->whereHas('payments', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }
    }
}

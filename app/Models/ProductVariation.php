<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'stock_quantity',
        'price',
        'tax',
        'sku',
        'is_active',
        'offer',
        'offer_expired_date',
        'offer_started_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'tax' => 'decimal:2',
        'offer' => 'decimal:2',
        'offer_started_date' => 'datetime',
        'offer_expired_date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function reminders()
    {
        return $this->hasMany(ProductReminder::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithIsInReminder($query, ?int $userId = null)
    {
        $userId = $userId ?? auth('sanctum')->id();

        if (! $userId) {
            return $query;
        }

        return $query->withExists([
            'reminders as is_in_reminder' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            },
        ]);
    }

    public function scopeSelectWithActiveOffer($query, array $additionalColumns = [], bool $onlyActiveOffers = false)
    {
        $now = now()->format('Y-m-d H:i:s');

        $baseColumns = [
            'id',
            'product_id',
            'color_id',
            'size_id',
            'stock_quantity',
            'price',
            'sku',
            'is_active',
            DB::raw("CASE
                    WHEN offer IS NOT NULL
                        AND (
                                (offer_started_date IS NULL AND offer_expired_date IS NULL)
                                OR (offer_started_date <= '{$now}' AND offer_expired_date >= '{$now}')
                            )
                        THEN offer
                        ELSE NULL
                    END as offer"),

        ];

        $query->select(array_merge($baseColumns, $additionalColumns));

        if ($onlyActiveOffers) {
            $query->whereNotNull('offer')
                ->where('offer_started_date', '<=', $now)
                ->where('offer_expired_date', '>=', $now);
        }

        return $query;
    }

    public function properties()
    {
        return $this->belongsToMany(
            Property::class,
            'variant_properties'
        )
            ->withPivot('value_ar', 'value_en')
            ->withTimestamps();
    }
    public function scopeLowStock($query, $limit = 5)
    {
        return $query->where('stock_quantity', '<', $limit);
    }
}

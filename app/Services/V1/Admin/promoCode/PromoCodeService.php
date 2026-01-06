<?php

namespace App\Services\V1\Admin\promoCode;

use App\Models\PromoCode;

class PromoCodeService
{
    public function list()
    {
        return PromoCode::paginate(10);
    }

    public function store(array $data)
    {
        return PromoCode::create([
            'code' => $data['code'],
            'value' => $data['value'],
            'is_percentage' => $data['is_percentage'],
            'is_active' => $data['is_active'],
            'expires_at' => $data['expires_at'] ?? null,
            'usage_limit' => $data['usage_limit'] ?? null,
        ]);
    }

    public function update(PromoCode $promoCode, array $data)
    {
        $promoCode->update([
            'code' => $data['code'] ?? $promoCode->code,
            'value' => $data['value'] ?? $promoCode->value,
            'is_percentage' => $data['is_percentage'] ?? $promoCode->is_percentage,
            'is_active' => $data['is_active'] ?? $promoCode->is_active,
            'expires_at' => $data['expires_at'] ?? $promoCode->expires_at,
            'usage_limit' => $data['usage_limit'] ?? $promoCode->usage_limit,
        ]);

        return $promoCode->refresh();
    }

    public function show(PromoCode $promoCode)
    {
        return $promoCode;
    }

    public function delete(PromoCode $promoCode)
    {
        return $promoCode->delete();
    }

    public function getActivePromoCodes()
    {
        return PromoCode::active()->latest()->paginate(10);
    }
}

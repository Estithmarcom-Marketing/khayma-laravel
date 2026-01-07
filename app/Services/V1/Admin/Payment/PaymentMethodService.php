<?php

namespace App\Services\V1\Admin\Payment;

use App\Models\PaymentMethod;

class PaymentMethodService
{
    public function list()
    {
        return PaymentMethod::all();
    }

    public function getActive()
    {
        return PaymentMethod::active()->get();
    }

    public function store(array $data)
    {
        return PaymentMethod::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(PaymentMethod $paymentMethod, array $data)
    {
        $paymentMethod->update([
            'name_ar' => $data['name_ar'] ?? $paymentMethod->name_ar,
            'name_en' => $data['name_en'] ?? $paymentMethod->name_en,
            'is_active' => $data['is_active'] ?? $paymentMethod->is_active,
        ]);

        return $paymentMethod->refresh();
    }

    public function show(PaymentMethod $paymentMethod)
    {
        return $paymentMethod;
    }

    public function delete(PaymentMethod $paymentMethod)
    {
        return $paymentMethod->delete();
    }
}

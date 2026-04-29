<?php

namespace App\Services\V1\Admin\Payment;

use App\Models\PaymentMethod;

class PaymentMethodService
{
    public function list()
    {
        return PaymentMethod::with('media')->get();
    }

    public function getActive()
    {
        return PaymentMethod::with('media')->active()->get();
    }

    public function store(array $data)
    {
        $paymentMethod = PaymentMethod::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'is_active' => $data['is_active'] ?? true,
        ]);
        if (isset($data['image'])) {
            $paymentMethod->addMedia($data['image'])->toMediaCollection('payment_methods');
        }

        return $paymentMethod;
    }

    public function update(PaymentMethod $paymentMethod, array $data)
    {
        $paymentMethod->update([
            'name_ar' => $data['name_ar'] ?? $paymentMethod->name_ar,
            'name_en' => $data['name_en'] ?? $paymentMethod->name_en,
            'is_active' => $data['is_active'] ?? $paymentMethod->is_active,
        ]);
        if (isset($data['image'])) {
            $paymentMethod->clearMediaCollection('payment_methods');
            $paymentMethod->addMedia($data['image'])->toMediaCollection('payment_methods');
        }
        return $paymentMethod->refresh();
    }

    public function show(PaymentMethod $paymentMethod)
    {
        return $paymentMethod->load('media');
    }

    public function delete(PaymentMethod $paymentMethod)
    {
        $paymentMethod->clearMediaCollection('payment_methods');
        return $paymentMethod->delete();
    }
}

<?php

namespace App\Services\V1\Admin\Payment;

use App\Models\PaymentGateway;

class PaymentGatewayService
{
    public function list()
    {
        return PaymentGateway::with(['paymentMethod', 'media'])->get();
    }

    public function getActive()
    {
        return PaymentGateway::active()->with(['paymentMethod', 'media'])->get();
    }

    public function show(PaymentGateway $paymentGateway)
    {
        return $paymentGateway->load(['paymentMethod', 'media']);
    }

    public function store(array $data)
    {
        $paymentGateway = PaymentGateway::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'gateway' => $data['gateway'],
            'payment_method_id' => $data['payment_method_id'],
            'is_active' => $data['is_active'] ?? true,
        ]);
        if (isset($data['image'])) {
            $paymentGateway->addMedia($data['image'])->toMediaCollection('payment_gateways');
        }
        return $paymentGateway;
    }

    public function update(PaymentGateway $paymentGateway, array $data)
    {
        $paymentGateway->update([
            'name_ar' => $data['name_ar'] ?? $paymentGateway->name_ar,
            'name_en' => $data['name_en'] ?? $paymentGateway->name_en,
            'gateway' => $data['gateway'] ?? $paymentGateway->gateway,
            'payment_method_id' => $data['payment_method_id'] ?? $paymentGateway->payment_method_id,
            'is_active' => $data['is_active'] ?? $paymentGateway->is_active,
        ]);
        if (isset($data['image'])) {
            $paymentGateway->clearMediaCollection('payment_gateways');
            $paymentGateway->addMedia($data['image'])->toMediaCollection('payment_gateways');
        }

        return $paymentGateway->refresh()->load('paymentMethod');
    }

    public function delete(PaymentGateway $paymentGateway)
    {
        $paymentGateway->clearMediaCollection('payment_gateways');
        $paymentGateway->delete();
    }
}

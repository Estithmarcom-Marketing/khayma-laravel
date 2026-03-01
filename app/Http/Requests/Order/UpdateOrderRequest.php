<?php

namespace App\Http\Requests\Order;

use App\Enums\Orders\OrderStatusEnum;
use App\Enums\Payments\PaymentStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $order_statuses = implode(',', array_map(fn ($status) => $status, OrderStatusEnum::all()));
        $payment_statuses = implode(',', array_map(fn ($status) => $status, PaymentStatusEnum::all()));
        return [
            'order_status' => "sometimes|in:{$order_statuses}",
            'payment_status' => "sometimes|in:{$payment_statuses}",
            'is_delivered' => 'sometimes|boolean',
        ];
    }
}

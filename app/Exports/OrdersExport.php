<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings, WithCustomCsvSettings
{
    public function collection()
    {
        return Order::with(['user', 'paymentMethod', 'deliveryMethod', 'items'])
            ->get()
            ->map(function (Order $order) {
                return [
                    $order->id,
                    optional($order->user)->name ?? '—',
                    $order->phone ?? optional($order->user)->phone ?? '—',
                    $order->status?->label()['ar'] ?? $order->getRawOriginal('status'),
                    $order->subtotal_price,
                    $order->shipping_cost,
                    $order->tax_amount,
                    $order->discount_of_offer,
                    $order->discount_of_promo_code ?? '—',
                    $order->promo_code ?? '—',
                    $order->total_price,
                    optional($order->paymentMethod)->name_ar ?? '—',
                    optional($order->deliveryMethod)->name_ar ?? '—',
                    $order->items->count(),
                    $order->created_at?->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'رقم الطلب',
            'اسم العميل',
            'رقم الهاتف',
            'الحالة',
            'المجموع الفرعي',
            'تكلفة الشحن',
            'الضريبة',
            'خصم العروض',
            'خصم كود الخصم',
            'كود الخصم',
            'المجموع الكلي',
            'طريقة الدفع',
            'طريقة التوصيل',
            'عدد المنتجات',
            'تاريخ الإنشاء',
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            'use_bom' => true,
        ];
    }
}

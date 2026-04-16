<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromQuery, WithMapping, WithHeadings, WithCustomCsvSettings, WithCustomChunkSize
{
    /**
     * Return a query builder — maatwebsite/excel will process it in chunks
     * (see chunkSize()) so only N rows + their relations live in RAM at once.
     */
    public function query(): Builder
    {
        return Order::with(['user', 'paymentMethod', 'deliveryMethod', 'items'])
            ->oldest('id'); // deterministic order required for chunked processing
    }

    /**
     * Map a single Order model to the flat array that becomes one spreadsheet row.
     * Called per-row; never holds the full result-set in memory.
     */
    public function map($order): array
    {
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
    }

    /**
     * Process 500 rows per chunk.
     * Each batch fetches 500 orders + their eager-loaded relations, writes them,
     * then frees that memory before loading the next batch.
     */
    public function chunkSize(): int
    {
        return 500;
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

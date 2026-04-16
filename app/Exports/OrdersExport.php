<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrdersExport implements FromQuery, WithMapping, WithHeadings, WithCustomCsvSettings, WithCustomChunkSize, ShouldAutoSize, WithColumnFormatting, WithEvents, WithTitle
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

    public function title(): string
    {
        return 'الطلبات';
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

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $lastRow  = $sheet->getHighestRow();
                $lastCol  = $sheet->getHighestColumn();
                $fullRange = 'A1:' . $lastCol . $lastRow;

                // RTL layout for Arabic content
                $sheet->setRightToLeft(true);

                // ── Header row ──────────────────────────────────────────────
                $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 12,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF3A94CC'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(32);

                // ── Data rows — alternating background + center alignment ───
                for ($row = 2; $row <= $lastRow; $row++) {
                    $bgColor = $row % 2 === 0 ? 'FFE8F4FC' : 'FFFFFFFF';

                    $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['argb' => $bgColor],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    $sheet->getRowDimension($row)->setRowHeight(22);
                }

                // ── Thin borders around every cell ──────────────────────────
                $sheet->getStyle($fullRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFB0C4DE'],
                        ],
                    ],
                ]);

                // ── Freeze the header so it stays visible while scrolling ───
                $sheet->freezePane('A2');
            },
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            'use_bom' => true,
        ];
    }
}

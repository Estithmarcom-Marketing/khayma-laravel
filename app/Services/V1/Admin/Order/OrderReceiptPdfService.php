<?php

namespace App\Services\V1\Admin\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class OrderReceiptPdfService
{
    public function generate(Order $order): string
    {
        $order->loadMissing([
            'items.productVariation.product:id,name_ar,name_en',
            'user',
            'deliveryMethod',
            'payments' => function ($query) {
                $query->latest();
            },
        ]);

        $locale = app()->getLocale();
        $hash = md5($order->updated_at);
        $fileName = "receipt-{$order->id}-{$hash}-{$locale}.pdf";
        $filePath = "receipts/{$fileName}";

        $disk = Storage::disk('public');

        if ($disk->exists($filePath)) {
            return $disk->url($filePath);
        }

        $pdfBinary = $this->generatePdfBinary($order);

        $disk->put($filePath, $pdfBinary);

        return $disk->url($filePath);
    }

    private function generatePdfBinary(Order $order): string
    {
        $locale = app()->getLocale();
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'default_font' => 'cairo',
            'directionality' => $locale === 'ar' ? 'rtl' : 'ltr',
        ]);

        $cairoFontPath = storage_path('fonts/Cairo-Regular.ttf');
        if (file_exists($cairoFontPath)) {
            $mpdf->fontdata['cairo'] = ['R' => $cairoFontPath];
        }

        $mpdf->SetFont('cairo');

        $html = view('pdf.order-receipt', compact('order'))->render();
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', 'S');
    }

    public function deleteCached(Order $order, ?string $locale = null): bool
    {
        $locale = $locale ?? app()->getLocale();
        $fileName = "receipt-{$order->id}-{$locale}.pdf";
        $filePath = "receipts/{$fileName}";

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }

        return false;
    }
}

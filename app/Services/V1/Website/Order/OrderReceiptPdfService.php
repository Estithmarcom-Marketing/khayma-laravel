<?php

namespace App\Services\V1\Website\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class OrderReceiptPdfService
{
    public function generate(Order $order): string
    {

        $order->loadMissing([
            'items.productVariation.product',
            'user',
            'address',
            'deliveryMethod',
            'payments',
        ]);

        $locale = app()->getLocale();
        $fileName = "receipt-{$order->id}-{$locale}.pdf";
        $filePath = "receipts/{$fileName}";

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->url($filePath);
        }

        $pdfBinary = $this->generatePdfBinary($order);

        Storage::disk('public')->put($filePath, $pdfBinary);

        return Storage::disk('public')->url($filePath);
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

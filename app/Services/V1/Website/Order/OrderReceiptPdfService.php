<?php

namespace App\Services\V1\Website\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class OrderReceiptPdfService
{
    public function generate(Order $order): string
    {
        $user = auth('sanctum')->user();
        if (! $user || $order->user_id !== $user->id) {
            throw new \LogicException(__('auth.unauthorized'));
        }

        $order->loadMissing([
            'items.productVariation.product:id,name_ar,name_en',
            'items.productVariation.size:id,name_ar',
            'user',
            'deliveryMethod',
            'payments' => function ($query) {
                $query->latest()->with('paymentMethod:id,name_ar,name_en');
            },
        ]);

        $hash = md5($order->updated_at);
        $fileName = "receipt-{$order->id}-{$hash}.pdf";
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
}

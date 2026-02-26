@php
    $isAr = app()->getLocale() === 'ar';
    $payment = $order->payments->first();
    $isPaid = $payment && $payment->status->value == 'completed';
    $logoPath = public_path('storage/alkhimah-logo-1.png');

@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: cairo, sans-serif;
            font-size: 12px;
            color: #1a2a35;
            background: #eaf3f8;
        }

        .page-wrap {
            padding: 20px;
        }

        .card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #b3d9f0;
            overflow: hidden;
        }

        /* == HEADER == */
        .header-inner {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #b3d9f0;
        }

        .header-logo-cell {
            padding: 18px 20px;
            vertical-align: middle;
            width: 50%;
        }

        .header-meta-cell {
            padding: 18px 20px;
            vertical-align: middle;
            width: 50%;
            text-align: {{ $isAr ? 'left' : 'right' }};
        }

        .logo-box {
            width: 80px;
            height: 80px;
            border: 2px dashed #aac8dc;
            border-radius: 8px;
            background: #f5fafd;
            display: inline-block;
            vertical-align: middle;
            text-align: center;
            line-height: 76px;
            font-size: 10px;
            color: #aac8dc;
            overflow: hidden;
        }

        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            line-height: 1;
        }

        .meta-label {
            font-size: 10px;
            color: #7a95a5;
        }

        .meta-value {
            font-size: 12px;
            /* font-weight: bold; */

            color: #1a2a35;
            margin-bottom: 4px;
        }

        .paid-badge {
            display: inline-block;
            padding: 2px 10px;
            background: #e6faf1;
            color: #27ae60;
            border: 1px solid #a8e6c8;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }

        .unpaid-badge {
            display: inline-block;
            padding: 2px 10px;
            background: #f0f0f0;
            color: #6b6b6b;
            border: 1px solid #c0c0c0;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }

        /* == INFO STRIP == */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #b3d9f0;
        }

        .info-cell {
            padding: 14px 20px;
            vertical-align: top;
            width: 33.33%;
            border-{{ $isAr ? 'left' : 'right' }}: 1px solid #b3d9f0;
        }

        .info-cell-last {
            padding: 14px 20px;
            vertical-align: top;
            width: 33.33%;
        }

        .info-title {
            font-weight: bold;
            color: #3a94cc;
            font-size: 11px;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #d6ecf8;
        }

        .info-row {
            color: #4a6070;
            font-size: 11px;
            margin-bottom: 3px;
            line-height: 1.5;
        }

        .info-row strong {
            color: #1a2a35;
        }

        /* == ITEMS TABLE == */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table thead tr {
            background: #1a2e3b;
            color: #ffffff;
        }

        .items-table th {
            padding: 10px 14px;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
        }

        .items-table th.th-product {
            text-align: {{ $isAr ? 'right' : 'left' }};

        }

        .items-table th.th-product {
            color: white !important;

        }

        .items-table td {
            padding: 11px 14px;
            font-size: 11px;
            color: #1a2a35;
            text-align: center;
            border-bottom: 1px solid #ddeef8;
            vertical-align: middle;
        }

        .items-table td.td-product {
            text-align: {{ $isAr ? 'right' : 'left' }};
            font-weight: bold;
        }

        .items-table td small {
            display: block;
            color: #7a95a5;
            font-size: 10px;
            font-weight: normal;
            margin-top: 2px;
        }

        .items-table td.td-total {
            font-weight: bold;
            color: #3a94cc;
        }

        .row-alt {
            background: #f5fafd;
        }

        /* == TOTALS == */
        .totals-wrap {
            border-top: 2px solid #b3d9f0;
            padding: 16px 20px;
            text-align: {{ $isAr ? 'left' : 'right' }};
        }

        .totals-inner {
            display: inline-block;
            width: 260px;
            text-align: {{ $isAr ? 'right' : 'left' }};
        }

        .total-line {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .total-line td {
            padding: 3px 0;
            font-size: 12px;
            color: #4a6070;
        }

        .tl-amount {
            text-align: {{ $isAr ? 'left' : 'right' }};
            font-weight: bold;
            color: #1a2a35;
        }

        .tl-discount {
            text-align: {{ $isAr ? 'left' : 'right' }};
            font-weight: bold;
            color: #27ae60;
        }

        .grand-row {
            width: 100%;
            border-collapse: collapse;
            border-top: 2px solid #3a94cc;
            margin-top: 8px;
        }

        .grand-row td {
            padding-top: 8px;
            font-size: 15px;
            font-weight: bold;
            color: #1a2a35;
        }

        .grand-amount {
            text-align: {{ $isAr ? 'left' : 'right' }};
            color: #3a94cc;
            font-size: 18px;
        }

        /* == FOOTER == */
        .footer {
            background: #1a2e3b;
            color: #ffffff;
            text-align: center;
            padding: 16px 20px;
        }

        .footer-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .footer-contacts {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-contacts td {
            text-align: center;
            font-size: 11px;
            color: #a0c8e0;
            padding: 0 10px;
        }

        .footer-copy {
            font-size: 10px;
            color: #a0c8e0;
            margin-top: 10px;
            border-top: 1px solid #2e4557;
            padding-top: 8px;
        }
    </style>
</head>


<body>
    <div class="page-wrap">
        <div class="card">

            {{-- == HEADER == --}}
            <table class="header-inner">
                <tr>
                    <td class="header-logo-cell">
                        <div class="logo-box">
                            @if (!empty($logoPath))
                                <img width="120px" src="{{ $logoPath }}" alt="Logo">
                            @else
                                Logo
                            @endif
                        </div>
                    </td>
                    <td class="header-meta-cell">
                        <div class="meta-label">{{ $isAr ? 'رقم الطلب' : 'Order ID' }}</div>
                        <div class="meta-value">{{ $order->id }}</div>
                        <div class="meta-label">{{ $isAr ? 'تاريخ الطلب' : 'Order Date' }}</div>
                        <div class="meta-value">{{ $order->created_at->format('Y-m-d H:i') }}</div>
                        @if ($isPaid)
                            <span class="paid-badge">{{ $isAr ? 'مدفوعة' : 'Paid' }}</span>
                        @else
                            <span class="unpaid-badge">{{ $isAr ? 'غير مدفوع' : 'Unpaid' }}</span>
                        @endif
                    </td>
                </tr>
            </table>

            {{-- == INFO STRIP == --}}
            <table class="info-table">
                <tr>
                    <td class="info-cell">
                        <div class="info-title">{{ $isAr ? 'بيانات العميل' : 'Customer' }}</div>
                        <div class="info-row"><strong>{{ $isAr ? 'الاسم' : 'Name' }}:</strong>
                            {{ $order->user->name }}</div>
                        @if ($order->user->phone)
                            <div class="info-row"><strong>{{ $isAr ? 'الجوال' : 'Phone' }}:</strong>
                                {{ $order->user->phone }}</div>
                        @endif
                    </td>
                    <td class="info-cell">
                        <div class="info-title">{{ $isAr ? 'عنوان التوصيل' : 'Delivery Address' }}</div>
                        <div class="info-row">{{ $order->address_details }}</div>
                    </td>
                    <td class="info-cell-last">
                        <div class="info-title">{{ $isAr ? 'طريقة التوصيل' : 'Delivery Method' }}</div>
                        <div class="info-row">{{ optional($order->deliveryMethod)->{'name_' . app()->getLocale()} }}
                        </div>
                    </td>
                </tr>
            </table>

            {{-- == ITEMS == --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="th-product">{{ $isAr ? 'المنتج' : 'Product' }}</th>
                        <th class="th-product">{{ $isAr ? 'الكمية' : 'Qty' }}</th>
                        <th class="th-product">{{ $isAr ? 'سعر الوحدة' : 'Unit Price' }}</th>
                        <th class="th-product">{{ $isAr ? 'خصم الوحدة' : 'Unit offer' }}</th>

                        <th class="th-product">{{ $isAr ? 'الإجمالي' : 'Total' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $i => $item)
                        @php
                            $price = (float) $item->price;
                            $offer = (float) $item->offer;
                            $qty = (int) $item->quantity;
                        @endphp
                        <tr class="{{ $i % 2 === 1 ? 'row-alt' : '' }}">
                            <td class="td-product">
                                {{ $item->productVariation->product->{'name_' . app()->getLocale()} }}
                                <small>SKU: {{ $item->productVariation->sku }}</small>
                            </td>
                            <td>{{ $qty }}</td>
                            <td>{{ number_format($price, 2) }}</td>
                            <td>{{ number_format($offer, 2) }}</td>
                            <td class="td-total">{{ number_format(($price - $offer) * $qty, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- == TOTALS == --}}
            <div class="totals-wrap">
                <div class="totals-inner">
                    <table class="total-line">
                        <tr>
                            <td>{{ $isAr ? 'المجموع الفرعي' : 'Subtotal' }}</td>
                            <td class="tl-amount">{{ number_format($order->subtotal_price, 2) }}</td>
                        </tr>
                    </table>
                    <table class="total-line">
                        <tr>
                            <td>{{ $isAr ? 'الشحن' : 'Shipping' }}</td>
                            <td class="tl-amount">{{ number_format($order->shipping_cost, 2) }}</td>
                        </tr>
                    </table>
                    <table class="total-line">
                        <tr>
                            <td>{{ $isAr ? 'الضريبة' : 'Tax' }}</td>
                            <td class="tl-amount">{{ number_format($order->tax_amount, 2) }}</td>
                        </tr>
                    </table>
                    @if ($order->discount_of_offer > 0 || $order->discount_of_promo_code > 0)
                        @if ($order->discount_of_offer > 0)
                            <table class="total-line">
                                <tr>
                                    <td>{{ $isAr ? 'خصم العروض' : 'Offers Discount' }}</td>
                                    <td class="tl-discount">
                                        {{ number_format($order->discount_of_offer, 2) }}
                                    </td>
                                </tr>
                            </table>
                        @endif
                        @if ($order->discount_of_promo_code > 0)
                            <table class="total-line">
                                <tr>
                                    <td>{{ $isAr ? ' كود الخصم' : 'Promo Code ' }}</td>
                                    <td class="tl-discount">
                                        {{ $order->promo_code }}
                                    </td>
                                </tr>
                            </table>
                            <table class="total-line">
                                <tr>
                                    <td>{{ $isAr ? 'خصم كود الخصم' : 'Promo Code Discount' }}</td>
                                    <td class="tl-discount">
                                        {{ number_format($order->discount_of_promo_code, 2) }}
                                    </td>
                                </tr>
                            </table>
                        @endif

                        <table class="total-line">
                            <tr>
                                <td>{{ $isAr ? 'الخصم الكلي' : 'Total Discount' }}</td>
                                <td class="tl-discount">
                                    {{ number_format($order->discount_of_offer + $order->discount_of_promo_code, 2) }}
                                </td>
                            </tr>
                        </table>
                    @endif
                    <table class="grand-row">
                        <tr>
                            <td>{{ $isAr ? 'الإجمالي الكلي' : 'Grand Total' }}</td>
                            <td class="grand-amount">{{ number_format($order->total_price, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- == FOOTER == --}}
            <div class="footer">
                <div class="footer-title">{{ $isAr ? 'شكرًا لتسوقك معنا' : 'Thank you for your order!' }}</div>
                <table class="footer-contacts">
                    <tr>
                        <td> 92000XXXX</td>
                        <td> support@alkhimah.com</td>
                        <td> www.alkhimah.com</td>
                    </tr>
                </table>
                <div class="footer-copy">
                    {{ $isAr ? 'جميع الحقوق محفوظة' : 'All rights reserved' }} &copy; {{ date('Y') }}
                </div>
            </div>

        </div>
    </div>
</body>

</html>

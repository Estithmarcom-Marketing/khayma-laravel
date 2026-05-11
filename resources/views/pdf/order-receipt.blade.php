@php
    use App\Models\Setting;
    $settings = Setting::first();
    $isAr = app()->getLocale() === 'ar';
    $payment = $order->payments->first();
    $isPaid = $payment && $payment->status->value == 'completed';
    $logoPath = public_path('Images/alkhimah-logo-1.png');
@endphp

<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>فاتورة ضريبية - Tax Invoice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            color: #111;
            font-size: 12px;
        }

        .invoice-wrapper {
            width: 595px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #ccc;
            padding: 20px 24px;
            font-size: 12px;
        }

        .header {
            margin-bottom: 14px;
        }

        .header table {
            width: 100%;
            border-collapse: collapse;
        }

        .header td {
            vertical-align: top;
            padding: 4px 0;
        }

        .header-left,
        .header-right {
            font-size: 11.5px;
            line-height: 1.7;
        }

        .header-left {
            text-align: left;
            direction: ltr;
        }

        .header-right {
            text-align: right;
            direction: rtl;
        }

        .logo-container {
            text-align: center;
            padding: 6px 0;
        }

        .logo-placeholder {
            width: 20px;
            height: 20px;
            display: inline-block;
            background: #fff;
            text-align: center;
            line-height: 20px;
        }

        .logo-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-placeholder .placeholder-text {
            font-size: 10px;
            color: #aaa;
        }

        .invoice-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 16px;
            letter-spacing: 1px;
            padding: 10px 0;
            /* border-bottom: 2px solid #3a94cc; */
            color: #000;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 11.5px;
        }

        .meta-table td {
            padding: 3px 6px;
            border-top: 1px solid white;
            border-bottom: 1px solid white;
        }

        .meta-table .label {
            font-weight: 600;
            background: #eaf4fb;
            white-space: nowrap;
            text-align: right;
            direction: rtl;
        }

        .meta-table .value {
            text-align: right;
            background: #eaf4fb;
            direction: rtl;
        }

        .special-td {
            width: 5px;
            background-color: white;
        }

        .meta-table .label-ltr {
            font-weight: 600;
            background: #eaf4fb;
            white-space: nowrap;
            text-align: left;
            direction: ltr;
        }

        .meta-table .value-ltr {
            text-align: center;
            background: #eaf4fb;
            direction: ltr;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            margin-bottom: 8px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #bbb;
            padding: 5px 6px;
            text-align: center;
        }

        .items-table thead tr {
            background: #3a94cc;
            color: #fff;
        }

        .items-table thead th {
            font-weight: 600;
            font-size: 11px;
            color: #fff;
        }

        .items-table .col-desc {
            text-align: right;
            direction: rtl;
        }

        .items-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .footer-section {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #3a94cc;
        }

        .footer-section table {
            width: 100%;
            border-collapse: collapse;
        }

        .notes-box {
            padding: 12px;
            font-size: 11px;
            direction: rtl;
            text-align: right;
            background: #fbfcff;
            /* border: 1px solid #e1e9f5; */
            /* border-radius: 8px; */
        }

        .notes-label {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 12px;
            color: #020303;
            padding-bottom: 4px;
            display: block;
            text-align: center;
            /* border-bottom: 1px solid #e1e9f5; */
        }

        .note-item {
            padding: 10px 8px;
            margin: 4px 0;
            font-size: 11px;
            line-height: 1.6;
            color: #222;
            text-align: right;
        }

        .note-item:first-child {
            padding-top: 0;
        }

        .qr-box {
            width: 80px;
            height: 80px;
            text-align: center;
            background: #fff;
            margin-top: 6px;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .qr-placeholder {
            font-size: 9px;
            color: #aaa;
            text-align: center;
            width: 80px;
            height: 80px;
            line-height: 80px;
            background: #fff;
        }

        .totals-table {
            width: 220px;
            border-collapse: collapse;
            font-size: 11.5px;
        }

        .totals-table td {
            padding: 5px 8px;
            border-top: 1px solid white;
            border-bottom: 4px solid white;
        }

        .totals-table .tot-label {
            text-align: center;
            direction: rtl;
            font-weight: 600;
            background: #eaf4fb;
            font-size: 10.5px;
        }

        .totals-table .tot-value {
            text-align: center;
            direction: ltr;
            font-weight: 600;
            background: #eaf4fb;
            min-width: 70px;
        }

        .totals-table .tot-final-label {
            text-align: center;
            font-size: 10.5px;
            font-weight: bold;
            background: #eaf4fb;
            color: #0f0f0f;
        }

        .totals-table .tot-final {
            text-align: center;
            direction: ltr;
            font-weight: bold;
            background: #eaf4fb;
            color: #060606;
        }
    </style>
</head>

<body>
    <div class="invoice-wrapper">
        <!-- HEADER -->
        <div class="header">
            <table>
                <tr>
                    <td width="30%" class="header-right">
                        <strong>شركة الخيمة المتكاملة التجارية</strong><br />
                        س.ت 4030273318<br />
                        الرقم الضريبي : 312700988800003<br />
                        جدة - حي البلد
                    <td width="40%"
                        style="
            text-align: center;
            vertical-align: middle;
            padding: 6px 0;
          ">
                        @if (!empty($logoPath))
                            <img src="{{ $logoPath }}" alt="Logo" width="100"
                                style="display: block; margin: 0 auto;" />
                        @else
                            <span style="font-size: 10px; color: #aaa;">Logo</span>
                        @endif
                    </td>

                    <td width="30%" class="header-left">
                        <strong style="font-size: 10px; display: block;">Al-Khaima Complete Trading
                            Company</strong><br />
                        C.T 4030273318<br />
                        Tax number: 312700988800003<br />
                        Jeddah - Albalad district
                    </td>
                </tr>
            </table>
        </div>

        <!-- TITLE -->
        <h2 class="invoice-title">فاتورة ضريبية &nbsp; TAX INVOICE</h2>

        <!-- META INFO -->
        <table class="meta-table">
            <tr>
                <td width="50%" valign="top">
                    <table width="100%" style="border-collapse: collapse;">
                        <tr>
                            <td class="label">رقم الطلب :</td>
                            <td class="value-ltr">{{ $order->id }}</td>
                            <td class="label-ltr">Order No :</td>
                        </tr>
                        <tr>
                            <td class="label">التاريخ :</td>
                            <td class="value-ltr">{{ $order->created_at->format('Y-m-d') }}</td>
                            <td class="label-ltr">Date :</td>
                        </tr>
                        <tr>
                            <td class="label">نوع الفاتورة :</td>
                            <td class="value-ltr">{{ $payment->paymentMethod->name_ar }}</td>
                            <td class="label-ltr">Type :</td>
                        </tr>
                    </table>
                </td>
                <td width="50%" valign="top">
                    <table width="100%" style="border-collapse: collapse;">
                        <tr>
                            <td class="label">اسم العميل :</td>
                            <td class="value">{{ optional($order->user)->name ?? '-' }}</td>
                            <td class="label-ltr">Customer Name :</td>
                        </tr>
                        <tr>
                            <td class="label">هاتف العميل :</td>
                            <td class="value">{{ $order->phone }}</td>
                            <td class="label-ltr">Customer Phone :</td>
                        </tr>
                        <tr>
                            <td class="label"> طريقة التوصيل:</td>
                            <td class="value">{{ $order->deliveryMethod->name_ar }}</td>
                            <td class="label-ltr"> Delivery Method :</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- ITEMS TABLE -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>م<br />NO</th>
                    <th class="col-desc">الصنف<br />Description</th>
                    <th>المقاس<br />Size</th>
                    <th>الكمية<br />Qty</th>
                    <th>السعر<br />Price</th>
                    <th>الخصم<br />Disc</th>
                    <th>المجموع<br />Total</th>
                    <th>الضريبة<br />VAT</th>
                    <th>الإجمالي<br />Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $index => $item)
                    @php
                        $price = (float) $item->price;
                        $offer = (float) $item->offer;
                        $qty = (int) $item->quantity;
                        $subtotal = ($price - $offer) * $qty;
                        $vat = (float) $item->tax;
                        $total = $subtotal + $vat;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="col-desc">{{ $item->productVariation->product->{'name_' . app()->getLocale()} }}</td>
                        <td>{{ $item->productVariation->size->{'name_' . app()->getLocale()} ?? '-' }}</td>
                        <td>{{ number_format($qty, 0) }}</td>
                        <td>{{ number_format($price, 2) }}</td>
                        <td>{{ number_format($offer, 2) }}</td>
                        <td>{{ number_format($subtotal, 2) }}</td>
                        <td>{{ number_format($vat, 2) }}</td>
                        <td>{{ number_format($total, 2) }}</td>
                    </tr>
                @endforeach
                @for ($i = count($order->items); $i < 10; $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <!-- FOOTER -->
        <div class="footer-section">
            <table>
                <tr>
                    <td width="60%" height="100%" style="padding: 0; vertical-align: top;">
                        <table width="100%" style="border-collapse: collapse;">
                            <tr>
                                <td width="100%" style="padding: 0; text-align: center; vertical-align: top;">
                                    <div class="notes-box">
                                        <h3 class="notes-label">ملاحظات على الفاتورة / Notes</h3>
                                        <div class="note-item">
                                            <br>
                                        </div>

                                        @if ($order->tax_amount == 0)
                                            <h4 class="note-item"
                                                style="color:#ff0000; font-weight:bold; font-size:12px; ">
                                                {{ '- جميع اﻷسعار شاملة الضريبة' }}
                                            </h4>
                                        @endif

                                        <div class="note-item">
                                            <br> <br> <br>
                                        </div>


                                        @if ($order->shipping_cost != 0)
                                            <div class="note-item" style="font-weight:bold; font-size:12px;">
                                                {{ '-  عنوان التوصيل: ' }}
                                            </div>
                                            <div class="note-item" style="padding: 4px 4px;">

                                            </div>
                                            <div class="note-item" style="font-size:12px;">
                                                {{ $order->address_details }}
                                            </div>
                                        @endif
                                        <div class="note-item">
                                            <br> <br> <br>
                                        </div>

                                        <div class="note-item" style="font-size:12px;  padding: 12px 8px;">
                                            {{ '- يمكن استرجاع المنتجات خلال 14 يومًا من تاريخ الاستلام بشرط أن تكون بحالتها الأصلية وغير مستخدمة وإرفاق الفاتورة.' }}
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="40%">
                        <table class="totals-table">
                            <tr>
                                <td class="tot-label">
                                    الإجمالي بدون الضريبة<br /><small>Subtotal</small>
                                </td>
                                <td class="tot-value">{{ number_format($order->subtotal_price, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="tot-label">
                                    إجمالي الخصم<br /><small>Total Discount</small>
                                </td>
                                <td class="tot-value">
                                    {{ number_format($order->discount_of_offer + $order->discount_of_promo_code, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="tot-label">
                                    ضريبة القيمة المضافة<br /><small>VAT 15%</small>
                                </td>
                                <td class="tot-value">{{ number_format($order->tax_amount, 2) }}</td>
                            </tr>
                            @if ($order->shipping_cost != 0)
                                <tr>
                                    <td class="tot-label">
                                        إجمالي الشحن<br /><small>Total Shipping</small>
                                    </td>
                                    <td class = "tot-value"> {{ number_format($order->shipping_cost, 2) }} </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="tot-final-label">
                                    الإجمالي النهائي<br /><small>Total</small>
                                </td>
                                <td class="tot-final">{{ number_format($order->total_price, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>

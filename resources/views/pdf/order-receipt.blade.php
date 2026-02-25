<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: cairo, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .rtl { direction: rtl; text-align: right; }
        .ltr { direction: ltr; text-align: left; }

        .number {
            direction: ltr;
            unicode-bidi: embed;
        }

        h1, h2, h3 {
            margin: 0 0 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th, td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        th {
            background: #f5f5f5;
            font-weight: bold;
        }

        .section {
            margin-top: 16px;
        }

        .totals td {
            border: none;
            padding: 4px 0;
        }

        .total-row {
            font-weight: bold;
            font-size: 13px;
        }
    </style>
</head>

<body class="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

{{-- ================= HEADER ================= --}}
<h2>
    {{ app()->getLocale() === 'ar' ? 'إيصال الطلب' : 'Order Receipt' }}
</h2>

<p>
    {{ app()->getLocale() === 'ar' ? 'رقم الطلب' : 'Order ID' }}:
    <span class="number">{{ $order->id }}</span>
</p>

<p>
    {{ app()->getLocale() === 'ar' ? 'تاريخ الطلب' : 'Order Date' }}:
    <span class="number">{{ $order->created_at->format('Y-m-d H:i') }}</span>
</p>

<hr>

{{-- ================= CUSTOMER INFO ================= --}}
<div class="section">
    <h3>{{ app()->getLocale() === 'ar' ? 'بيانات العميل' : 'Customer Information' }}</h3>

    <p>{{ $order->user->name }}</p>
   

    @if($order->user->phone)
        <p class="number">{{ $order->user->phone }}</p>
    @endif
</div>

{{-- ================= ADDRESS ================= --}}
<div class="section">
    <h3>{{ app()->getLocale() === 'ar' ? 'عنوان التوصيل' : 'Delivery Address' }}</h3>

    <p>
        {{ $order->address_details }} 
    </p>
</div>

{{-- ================= DELIVERY METHOD ================= --}}
<div class="section">
    <h3>{{ app()->getLocale() === 'ar' ? 'طريقة التوصيل' : 'Delivery Method' }}</h3>

    <p>
        {{ $order->deliveryMethod->{'name_'.app()->getLocale()} }}
    </p>
</div>

{{-- ================= ITEMS ================= --}}
<div class="section">
    <h3>{{ app()->getLocale() === 'ar' ? 'المنتجات' : 'Items' }}</h3>

    <table>
        <thead>
        <tr>
            <th>{{ app()->getLocale() === 'ar' ? 'المنتج' : 'Product' }}</th>
            <th>{{ app()->getLocale() === 'ar' ? 'الكمية' : 'Qty' }}</th>
            <th>{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}</th>
            <th>{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Total' }}</th>
        </tr>
        </thead>

        <tbody>
        @foreach($order->items as $item)
            @php
                $price = (float) $item->productVariation->price;
                $qty = (int) $item->quantity;
            @endphp
            <tr>
                <td>
                    {{ $item->productVariation->product->{'name_'.app()->getLocale()} }}
                    <br>
                    <small class="number">
                        SKU: {{ $item->productVariation->sku }}
                    </small>
                </td>
                <td class="number">{{ $qty }}</td>
                <td class="number">{{ number_format($price, 2) }}</td>
                <td class="number">{{ number_format($price * $qty, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

{{-- ================= TOTALS ================= --}}
<div class="section">
    <table class="totals">
        <tr>
            <td>{{ app()->getLocale() === 'ar' ? 'المجموع الفرعي' : 'Subtotal' }}</td>
            <td class="number">{{ number_format($order->subtotal_price, 2) }}</td>
        </tr>

        <tr>
            <td>{{ app()->getLocale() === 'ar' ? 'الشحن' : 'Shipping' }}</td>
            <td class="number">{{ number_format($order->shipping_cost, 2) }}</td>
        </tr>

        <tr>
            <td>{{ app()->getLocale() === 'ar' ? 'الضريبة' : 'Tax' }}</td>
            <td class="number">{{ number_format($order->tax_amount, 2) }}</td>
        </tr>

        @if($order->discount_amount > 0)
            <tr>
                <td>{{ app()->getLocale() === 'ar' ? 'الخصم' : 'Discount' }}</td>
                <td class="number">-{{ number_format($order->discount_amount, 2) }}</td>
            </tr>
        @endif

        <tr class="total-row">
            <td>{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Total' }}</td>
            <td class="number">{{ number_format($order->total_price, 2) }}</td>
        </tr>
    </table>
</div>

{{-- ================= FOOTER ================= --}}
<hr>

<p style="text-align:center;">
    {{ app()->getLocale() === 'ar'
        ? 'شكرًا لتسوقك معنا'
        : 'Thank you for your order' }}
</p>

</body>
</html>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة #{{ $sale->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            color: #1f1f1f;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            background: #f5f7fa;
            direction: rtl;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 48px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 5px 9px 0 rgba(0, 0, 0, 0.06);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 48px;
            border-bottom: 2px solid #f3f3f3;
            padding-bottom: 24px;
        }
        .store-info h1 {
            margin: 0 0 8px 0;
            color: #000000;
            font-weight: 300;
            font-size: 44px;
        }
        .store-info p {
            color: #6b6b6b;
            margin: 0;
        }
        .invoice-details {
            text-align: left; /* Because it's RTL, left is the opposite side */
        }
        .invoice-details h2 {
            margin: 0 0 8px 0;
            color: #0070cc; /* PlayStation Blue */
            font-weight: 300;
            font-size: 35px;
        }
        .customer-info {
            margin-bottom: 48px;
            background: rgba(0, 112, 204, 0.05);
            padding: 24px;
            border-radius: 12px;
            border-right: 4px solid #0070cc;
        }
        .customer-info h3 {
            margin: 0 0 8px 0;
            color: #0070cc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 48px;
        }
        th, td {
            padding: 16px 20px;
            text-align: right;
            border-bottom: 1px solid #f3f3f3;
        }
        th {
            color: #6b6b6b;
            font-weight: 600;
            font-size: 14px;
        }
        td {
            font-size: 18px;
        }
        .total-row td {
            font-weight: 700;
            font-size: 22px;
            border-top: 2px solid #000000;
            color: #000000;
        }
        .footer {
            text-align: center;
            color: #6b6b6b;
            font-size: 14px;
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px solid #f3f3f3;
        }
        @media print {
            body { background: #ffffff; }
            .invoice-box { box-shadow: none; padding: 0; border-radius: 0; }
            .no-print { display: none !important; }
        }
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background: #0070cc;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-back:hover {
            background: #005fa3;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="text-align: right; max-width: 800px; margin: 0 auto;">
        <a href="{{ route('pos.index') }}" class="btn-back">← العودة لشاشة الكاشير</a>
        <button onclick="window.print()" class="btn-back" style="background: #10b981; margin-right: 10px;">🖨️ طباعة الفاتورة</button>
    </div>
    <div class="invoice-box">
        <div class="header">
            <div class="store-info">
                <h1>نظام الإدارة</h1>
                <p>فلسطين - غزة<br>هاتف: 0590000000</p>
            </div>
            <div class="invoice-details">
                <h2>فاتورة مبيعات</h2>
                <p>رقم الفاتورة: {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}<br>
                التاريخ: {{ $sale->created_at->format('Y-m-d H:i') }}<br>
                طريقة الدفع: <strong>{{ $sale->payment_type == 'cash' ? 'نقدي 💰' : 'دين 📒' }}</strong></p>
            </div>
        </div>

        @if($sale->customer)
        <div class="customer-info">
            <h3>فاتورة للسيد/ة:</h3>
            <p style="font-size: 22px; font-weight: 700; margin: 0;">{{ $sale->customer->name }}</p>
            <p style="margin: 4px 0 0 0; color: #6b6b6b;">رقم الهاتف: {{ $sale->customer->phone ?? 'غير متوفر' }}</p>
        </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>وصف الصنف</th>
                    <th>الكمية</th>
                    <th>سعر الوحدة (₪)</th>
                    <th>المجموع (₪)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td style="font-weight: 500;">{{ $item->product ? $item->product->name : 'صنف غير معروف' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td style="font-weight: 700;">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="text-align: left;">الإجمالي المطلوب:</td>
                    <td style="color: #0070cc;">₪{{ number_format($sale->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>شكراً لتعاملكم معنا!</p>
            <p>تم إصدار هذه الفاتورة بواسطة StorePro (نظام إدارة المبيعات)</p>
        </div>
    </div>
</body>
</html>

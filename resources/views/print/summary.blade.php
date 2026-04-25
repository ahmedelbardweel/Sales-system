<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ملخص اليوم - {{ $today->format('Y-m-d') }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; padding: 20px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #0070cc; padding-bottom: 10px; margin-bottom: 20px; }
        .row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .label { font-weight: bold; }
        .value { font-weight: bold; color: #0070cc; }
        .total-box { background: #f0f7ff; padding: 15px; margin-top: 20px; text-align: center; border: 1px solid #0070cc; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>ملخص النشاط اليومي</h1>
        <p>التاريخ: {{ $today->format('Y-m-d') }}</p>
    </div>

    <div class="row">
        <span class="label">أرباح نقدية محققة:</span>
        <span class="value">₪{{ number_format($cashProfit, 2) }}</span>
    </div>

    <div class="row">
        <span class="label">أرباح ديون (غير محققة):</span>
        <span class="value" style="color: #d53b00;">₪{{ number_format($debtProfit, 2) }}</span>
    </div>

    <div class="row">
        <span class="label">دفعات ديون مستلمة:</span>
        <span class="value" style="color: #1eaedb;">₪{{ number_format($debtPayments, 2) }}</span>
    </div>

    <div class="total-box">
        <div style="font-size: 14px; color: #666;">إجمالي النقد الداخل للصندوق اليوم</div>
        <div style="font-size: 28px; font-weight: 900; color: #000; margin-top: 5px;">₪{{ number_format($totalCashIn, 2) }}</div>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #999;">
        تم استخراج هذا التقرير تلقائياً من نظام الإدارة
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #0070cc; color: #fff; border: none; cursor: pointer;">إعادة الطباعة</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #eee; color: #333; border: none; cursor: pointer;">إغلاق</button>
    </div>
</body>
</html>

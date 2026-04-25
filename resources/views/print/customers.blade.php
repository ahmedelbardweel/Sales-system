<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير ديون الزبائن</title>
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
            text-align: center;
            margin-bottom: 48px;
            border-bottom: 2px solid #f3f3f3;
            padding-bottom: 24px;
        }
        .header h1 {
            margin: 0 0 8px 0;
            color: #0070cc;
            font-weight: 700;
            font-size: 32px;
        }
        .header p {
            color: #6b6b6b;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 48px;
        }
        th, td {
            padding: 12px 16px;
            text-align: right;
            border-bottom: 1px solid #f3f3f3;
        }
        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 14px;
        }
        td {
            font-size: 16px;
        }
        .total-row td {
            font-weight: 700;
            font-size: 20px;
            border-top: 2px solid #000000;
            color: #000000;
            background-color: #f8fafc;
        }
        @media print {
            body { background: #ffffff; }
            .invoice-box { box-shadow: none; padding: 0; border-radius: 0; }
            .no-print { display: none !important; }
        }
        .btn-action {
            display: inline-block;
            padding: 10px 20px;
            background: #0070cc;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: none;
            cursor: pointer;
        }
        .btn-action:hover {
            background: #005fa3;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="text-align: right; max-width: 800px; margin: 0 auto;">
        <button onclick="window.print()" class="btn-action" style="background: #10b981; margin-right: 10px;">🖨️ طباعة التقرير / حفظ PDF</button>
        <button onclick="window.close()" class="btn-action" style="background: #ef4444; margin-right: 10px;">❌ إغلاق</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <h1>تقرير ديون الزبائن</h1>
            <p>تاريخ التقرير: {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>اسم الزبون</th>
                    <th>رقم الهاتف</th>
                    <th>إجمالي الدين (₪)</th>
                </tr>
            </thead>
            <tbody>
                @php $totalDebts = 0; @endphp
                @foreach($customers as $customer)
                    @if($customer->total_debt > 0)
                        @php $totalDebts += $customer->total_debt; @endphp
                        <tr>
                            <td style="font-weight: 700; color: #0070cc;">{{ $customer->name }}</td>
                            <td dir="ltr" style="text-align: right;">{{ $customer->phone ?? 'غير متوفر' }}</td>
                            <td style="font-weight: 700; color: #dc2626;">{{ number_format($customer->total_debt, 2) }}</td>
                        </tr>
                    @endif
                @endforeach
                @if($totalDebts == 0)
                    <tr>
                        <td colspan="3" style="text-align: center; color: #16a34a; font-weight: 700;">لا يوجد ديون على أي زبون! 🎉</td>
                    </tr>
                @endif
            </tbody>
            @if($totalDebts > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" style="text-align: left;">إجمالي الديون في السوق:</td>
                    <td style="color: #dc2626;">₪{{ number_format($totalDebts, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>

        <div style="text-align: center; color: #6b6b6b; font-size: 14px; margin-top: 48px; border-top: 1px solid #f3f3f3; padding-top: 24px;">
            تم إصدار هذا التقرير بواسطة StorePro (نظام إدارة المبيعات)
        </div>
    </div>
</body>
</html>

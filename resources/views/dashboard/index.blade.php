@extends('components.layouts.app')

@section('content')
<div>
    <div class="page-header">
        <h1 class="page-title">نظرة عامة على النظام</h1>
        <a href="/print/summary" target="_blank" class="btn btn-primary">
            <i data-lucide="printer"></i>
            طباعة ملخص اليوم
        </a>
    </div>

    @if($zeroProfitAlert)
        <div class="card" style="border-right: 4px solid var(--text-error); background: rgba(200, 27, 58, 0.05); display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
            <i data-lucide="alert-octagon" style="color: var(--text-error); width: 28px; height: 28px;"></i>
            <div>
                <h4 style="color: var(--text-error); font-weight: 700; margin-bottom: 2px; font-size: 15px;">تنبيه: لا يوجد أرباح نقدية اليوم</h4>
                <p style="color: var(--text-mute); font-size: 14px;">لقد قمت بعمليات بيع اليوم، ولكن لم تحقق أي ربح نقدي فعلي (إما أن البيع كان بسعر التكلفة أو أن المبيعات كلها ديون). يرجى مراجعة الأسعار!</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-4" style="margin-bottom: 24px;">
        <!-- Today's Cash Profit -->
        <div class="card" style="border-top: 4px solid var(--ps-blue);">
            <p class="form-label">أرباح نقدية (محققة)</p>
            <h2 class="display-m" style="font-weight: 700; color: var(--ps-blue);">
                ₪{{ number_format($cashProfit, 2) }}
            </h2>
            <p style="font-size: 14px; color: var(--text-mute); margin-top: 8px;">
                الربح الفعلي في الصندوق اليوم
            </p>
        </div>

        <!-- Today's Debt Profit -->
        <div class="card" style="border-top: 4px solid var(--ps-orange);">
            <p class="form-label">أرباح ديون (غير محققة)</p>
            <h2 class="display-m" style="font-weight: 700; color: var(--ps-orange);">
                ₪{{ number_format($debtProfit, 2) }}
            </h2>
            <p style="font-size: 14px; color: var(--text-mute); margin-top: 8px;">
                أرباح محتجزة في ديون اليوم
            </p>
        </div>

        <!-- Debt Payments Received -->
        <div class="card" style="border-top: 4px solid var(--ps-cyan);">
            <p class="form-label">دفعات ديون مستلمة اليوم</p>
            <h2 class="display-m" style="font-weight: 700; color: var(--ps-cyan);">
                ₪{{ number_format($debtPaymentsReceivedToday, 2) }}
            </h2>
            <p style="font-size: 14px; color: var(--text-mute); margin-top: 8px;">
                أموال ديون سابقة تم تحصيلها اليوم
            </p>
        </div>

        <!-- Total Outstanding Debt -->
        <div class="card" style="border-top: 4px solid var(--text-error);">
            <p class="form-label">إجمالي ديون السوق</p>
            <h2 class="display-m" style="font-weight: 700; color: var(--text-error);">
                ₪{{ number_format($totalDebt, 2) }}
            </h2>
            <p style="font-size: 14px; color: var(--text-mute); margin-top: 8px;">
                إجمالي الأموال المتبقية في السوق
            </p>
        </div>
    </div>

    <div class="grid grid-cols-2" style="gap: 24px;">
        <!-- Low Stock Alerts -->
        <div class="card" style="border-color: rgba(213, 59, 0, 0.2);">
            <h3 style="margin-bottom: 16px; font-size: 16px; font-weight: 600; color: var(--ps-orange); display: flex; align-items: center; gap: 6px;">
                <i data-lucide="package-minus"></i> نواقص المخزون
            </h3>
            
            @if($lowStockProducts->count() > 0)
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>الصنف</th>
                                <th>الكمية المتبقية</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $product)
                                <tr>
                                    <td style="font-weight: 500;">{{ $product->name }}</td>
                                    <td><span class="badge badge-warning">{{ $product->cartons_stock }} كراتين</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="text-align: center; color: var(--text-mute); padding: 24px;">المخزون ممتاز، لا توجد نواقص!</p>
            @endif
        </div>

        <!-- High Debt Customers Alert -->
        <div class="card" style="border-color: rgba(200, 27, 58, 0.2);">
            <h3 style="margin-bottom: 16px; font-size: 16px; font-weight: 600; color: var(--text-error); display: flex; align-items: center; gap: 6px;">
                <i data-lucide="users"></i> ديون متراكمة
            </h3>
            
            @if($highDebtCustomers->count() > 0)
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>الزبون</th>
                                <th>قيمة الدين</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($highDebtCustomers as $customer)
                                <tr>
                                    <td style="font-weight: 500;">{{ $customer->name }}</td>
                                    <td style="color: var(--text-error); font-weight: 700;">₪{{ number_format($customer->total_debt, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="text-align: center; color: var(--text-mute); padding: 24px;">لا يوجد زبائن عليهم ديون متراكمة!</p>
            @endif
        </div>
    </div>

    <!-- Activity Log Section -->
    <div class="card" style="margin-top: 24px; padding: 20px;">
        <h3 style="margin-bottom: 20px; font-size: 18px; font-weight: 700; color: var(--ps-blue); display: flex; align-items: center; gap: 8px;">
            <i data-lucide="bell"></i> سجل النشاطات الأخير (الإشعارات)
        </h3>

        <div style="display: flex; flex-direction: column; gap: 12px;">
            @forelse($activityLogs as $log)
                <div style="display: flex; align-items: flex-start; gap: 15px; padding: 12px; border-bottom: 1px solid #f1f5f9; background: #fff; transition: 0.2s;">
                    <div style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; 
                        background: {{ 
                            $log->type == 'sale' ? 'rgba(0, 112, 204, 0.1)' : 
                            ($log->type == 'purchase' ? 'rgba(213, 59, 0, 0.1)' : 
                            ($log->type == 'debt_payment' ? 'rgba(30, 174, 219, 0.1)' : 'rgba(100, 116, 139, 0.1)')) 
                        }};">
                        <i data-lucide="{{ 
                            $log->type == 'sale' ? 'shopping-cart' : 
                            ($log->type == 'purchase' ? 'truck' : 
                            ($log->type == 'debt_payment' ? 'coins' : 'user-plus')) 
                        }}" style="width: 18px; height: 18px; color: {{ 
                            $log->type == 'sale' ? 'var(--ps-blue)' : 
                            ($log->type == 'purchase' ? 'var(--ps-orange)' : 
                            ($log->type == 'debt_payment' ? 'var(--ps-cyan)' : '#64748b')) 
                        }};"></i>
                    </div>
                    
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <span style="font-weight: 700; font-size: 14px; color: #1e293b;">{{ $log->description }}</span>
                            <span style="font-size: 11px; color: #94a3b8;">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="display: flex; gap: 12px; align-items: center;">
                            @if($log->amount)
                                <span style="font-size: 13px; font-weight: 800; color: {{ $log->type == 'purchase' ? '#ef4444' : '#059669' }};">
                                    {{ $log->type == 'purchase' ? '-' : '+' }} ₪{{ number_format($log->amount, 2) }}
                                </span>
                            @endif
                            <span style="font-size: 11px; color: #64748b; background: #f8fafc; padding: 2px 8px; border-radius: 10px; border: 1px solid #e2e8f0;">
                                {{ 
                                    $log->type == 'sale' ? 'مبيعات' : 
                                    ($log->type == 'purchase' ? 'مشتريات' : 
                                    ($log->type == 'debt_payment' ? 'دفعة دين' : 'إدارة')) 
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 40px; color: #94a3b8;">
                    <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                    <p>لا توجد نشاطات مسجلة بعد</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@extends('components.layouts.app')

@section('content')
<div class="container">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h1 class="page-title" style="margin: 0;">إدارة الزبائن والديون</h1>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('print.customers') }}" target="_blank" class="btn btn-primary" style="background: #10b981; border-color: #10b981; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="printer"></i>
                طباعة ديون السوق
            </a>
            <button onclick="toggleModal('add-customer-modal')" class="btn btn-primary" style="display: flex; align-items: center; gap: 8px;">
                <i data-lucide="user-plus"></i>
                إضافة زبون جديد
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="badge badge-success" style="padding: 16px; width: 100%; margin-bottom: 24px; font-size: 16px;">
            {{ session('message') }}
        </div>
    @endif

    <!-- Add Customer Form -->
    <div id="add-customer-modal" class="card" style="display: none; margin-bottom: 32px; border-right: 4px solid var(--ps-blue);">
        <h3 style="margin-bottom: 24px; font-size: 22px; font-weight: 500;">بيانات الزبون الجديد</h3>
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">اسم الزبون</label>
                    <input type="text" name="name" class="form-input" required placeholder="مثال: أحمد، محمد...">
                </div>
                
                <div class="form-group">
                    <label class="form-label">رقم الهاتف (اختياري)</label>
                    <input type="text" name="phone" class="form-input" placeholder="مثال: 059...">
                </div>
            </div>
            
            <div style="display: flex; gap: 16px; margin-top: 16px;">
                <button type="submit" class="btn btn-primary">حفظ الزبون</button>
                <button type="button" onclick="toggleModal('add-customer-modal')" class="btn btn-outline">إلغاء</button>
            </div>
        </form>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 16px;">
        @forelse($customers as $customer)
            <div class="card" style="padding: 16px; border-top: 3px solid {{ $customer->total_debt > 0 ? '#ef4444' : 'var(--ps-blue)' }}; display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 36px; height: 36px; background: rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="user" style="color: var(--ps-blue); width: 18px; height: 18px;"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: #000;">{{ $customer->name }}</h3>
                            <div style="color: #64748b; font-size: 11px;">{{ $customer->phone ?? 'لا يوجد رقم' }}</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 4px;">
                        <button onclick="toggleModal('edit-customer-modal-{{ $customer->id }}')" style="background: rgba(0, 112, 204, 0.1); border: none; color: var(--ps-blue); cursor: pointer; padding: 8px; border-radius: 4px; display: flex; align-items: center; justify-content: center;" title="تعديل">
                            <i data-lucide="edit-3" style="width: 16px; height: 16px;"></i>
                        </button>
                        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الزبون؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: rgba(239, 68, 68, 0.1); border: none; color: #ef4444; cursor: pointer; padding: 8px; border-radius: 4px; display: flex; align-items: center; justify-content: center;" title="حذف">
                                <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Edit Customer Modal -->
                <div id="edit-customer-modal-{{ $customer->id }}" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
                    <div class="card" style="max-width: 500px; width: 100%;">
                        <h3 style="margin-bottom: 24px;">تعديل بيانات الزبون: {{ $customer->name }}</h3>
                        <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label class="form-label">اسم الزبون</label>
                                <input type="text" name="name" class="form-input" value="{{ $customer->name }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">رقم الهاتف</label>
                                <input type="text" name="phone" class="form-input" value="{{ $customer->phone }}">
                            </div>
                            <div style="display: flex; gap: 16px; margin-top: 16px;">
                                <button type="submit" class="btn btn-primary">تحديث</button>
                                <button type="button" onclick="toggleModal('edit-customer-modal-{{ $customer->id }}')" class="btn btn-outline">إلغاء</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div style="background: #f8fafc; padding: 10px; text-align: center;">
                    <div style="color: #64748b; font-size: 11px; margin-bottom: 2px;">إجمالي الدين المتبقي</div>
                    @if($customer->total_debt > 0)
                        <div style="font-weight: 900; font-size: 20px; color: #ef4444;">₪{{ number_format($customer->total_debt, 2) }}</div>
                    @else
                        <div style="font-weight: 700; font-size: 16px; color: #059669;">لا يوجد دين </div>
                    @endif
                </div>

                <div style="display: flex; justify-content: space-between; font-size: 12px; color: #64748b; border-bottom: 1px dashed #eee; padding-bottom: 8px;">
                    <span>المشتريات: {{ $customer->sales_count }}</span>
                    <span>الدفعات: {{ $customer->debt_payments_count }}</span>
                </div>

                @if($customer->total_debt > 0)
                    <button onclick="toggleModal('payment-modal-{{ $customer->id }}')" class="btn btn-primary" style="width: 100%; padding: 8px; font-size: 12px; background-color: var(--ps-cyan);">
                        <i data-lucide="coins" style="width: 14px; height: 14px; margin-left: 6px;"></i>
                        استلام دفعة من الدين
                    </button>

                    <!-- Payment Modal -->
                    <div id="payment-modal-{{ $customer->id }}" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
                        <div class="card" style="max-width: 500px; width: 100%;">
                            <h3 style="margin-bottom: 24px;">تسجيل دفعة: {{ $customer->name }}</h3>
                            <form action="{{ route('customers.pay', $customer->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label">المبلغ المدفوع ₪</label>
                                    <input type="number" step="0.01" name="amount" class="form-input" required max="{{ $customer->total_debt }}" placeholder="قيمة الدفعة...">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">ملاحظات (اختياري)</label>
                                    <input type="text" name="notes" class="form-input" placeholder="مثال: نقداً...">
                                </div>
                                <div style="display: flex; gap: 16px; margin-top: 16px;">
                                    <button type="submit" class="btn btn-primary" style="background-color: var(--ps-cyan);">تأكيد الدفعة</button>
                                    <button type="button" onclick="toggleModal('payment-modal-{{ $customer->id }}')" class="btn btn-outline">إلغاء</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div style="grid-column: span 3; text-align: center; padding: 48px; color: var(--text-mute); background: #fff; border: 1px dashed #eee;">
                لا يوجد زبائن مسجلين حتى الآن.
            </div>
        @endforelse
    </div>
</div>

<script>
    function toggleModal(id) {
        const el = document.getElementById(id);
        if (el.style.display === 'none' || el.style.display === '') {
            el.style.display = (id.includes('edit') || id.includes('payment')) ? 'flex' : 'block';
        } else {
            el.style.display = 'none';
        }
    }
</script>
@endsection

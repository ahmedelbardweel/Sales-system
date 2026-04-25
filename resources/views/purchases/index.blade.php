@extends('components.layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">سجل المشتريات (تزويد المخزون)</h1>
        <button onclick="toggleModal('add-purchase-modal')" class="btn btn-primary">
            <i data-lucide="shopping-bag"></i>
            تسجيل فاتورة شراء جديدة
        </button>
    </div>

    @if (session()->has('message'))
        <div class="badge badge-success" style="padding: 16px; width: 100%; margin-bottom: 24px; font-size: 16px;">
            {{ session('message') }}
        </div>
    @endif

    <!-- Add Purchase Form -->
    <div id="add-purchase-modal" class="card" style="display: none; margin-bottom: 32px; border-right: 4px solid var(--ps-blue);">
        <h3 style="margin-bottom: 24px; font-size: 22px; font-weight: 500;">تفاصيل فاتورة الشراء</h3>
        <form action="{{ route('purchases.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-3">
                <div class="form-group">
                    <label class="form-label">الصنف</label>
                    <select name="product_id" class="form-input" required>
                        <option value="">-- اختر الصنف --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">الكمية (كراتين)</label>
                    <input type="number" name="cartons_purchased" class="form-input" min="1" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">تكلفة الكرتونة ₪</label>
                    <input type="number" step="0.01" name="cost_per_carton" class="form-input" required placeholder="السعر المدفوع للمورد">
                </div>
            </div>
            
            <div style="display: flex; gap: 16px; margin-top: 16px;">
                <button type="submit" class="btn btn-primary">حفظ الفاتورة</button>
                <button type="button" onclick="toggleModal('add-purchase-modal')" class="btn btn-outline">إلغاء</button>
            </div>
        </form>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px;">
        @forelse($purchases as $purchase)
            <div class="card" style="padding: 16px; border-right: 4px solid var(--ps-blue); display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--ps-blue);">{{ $purchase->product->name }}</h3>
                    <div style="font-size: 11px; color: #64748b;">{{ $purchase->created_at->format('Y-m-d') }}</div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px;">
                    <div>
                        <div style="color: #64748b; font-size: 11px;">الكمية المشتراة</div>
                        <div style="font-weight: 700;">{{ $purchase->cartons_purchased }} كرتونة</div>
                    </div>
                    <div>
                        <div style="color: #64748b; font-size: 11px;">التكلفة الإجمالية</div>
                        <div style="font-weight: 700; color: #ef4444;">₪{{ number_format($purchase->total_cost, 2) }}</div>
                    </div>
                    <div>
                        <div style="color: #64748b; font-size: 11px;">تكلفة الكرتونة</div>
                        <div style="font-weight: 600;">₪{{ number_format($purchase->cost_per_carton, 2) }}</div>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: span 3; text-align: center; padding: 48px; color: var(--text-mute); background: #fff; border: 1px dashed #eee;">
                لا يوجد مشتريات مسجلة حتى الآن.
            </div>
        @endforelse
    </div>
</div>

<script>
    function toggleModal(id) {
        const el = document.getElementById(id);
        if (el.style.display === 'none' || el.style.display === '') {
            el.style.display = 'block';
        } else {
            el.style.display = 'none';
        }
    }
</script>
@endsection

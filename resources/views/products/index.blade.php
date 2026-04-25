@extends('components.layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">إدارة الأصناف والمخزون</h1>
        <button onclick="toggleModal('add-product-modal')" class="btn btn-primary">
            <i data-lucide="plus"></i>
            إضافة صنف جديد
        </button>
    </div>

    @if (session()->has('message'))
        <div class="badge badge-success" style="padding: 16px; width: 100%; margin-bottom: 24px; font-size: 16px;">
            {{ session('message') }}
        </div>
    @endif

    <!-- Add Product Form (Modal) -->
    <div id="add-product-modal" class="card" style="display: none; border-right: 4px solid var(--ps-blue); margin-bottom: 32px;">
        <h3 style="margin-bottom: 24px; font-size: 22px; font-weight: 500;">تفاصيل الصنف الجديد</h3>
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">اسم الصنف</label>
                    <input type="text" name="name" class="form-input" required placeholder="مثال: عصير برتقال، بسكويت...">
                </div>
                
                <div class="form-group">
                    <label class="form-label">الكمية الافتتاحية (كراتين)</label>
                    <input type="number" name="initial_cartons" class="form-input" required value="0">
                </div>

                <div class="form-group">
                    <label class="form-label">سعر شراء الكرتونة ₪</label>
                    <input type="number" step="0.01" name="purchase_price_carton" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">عدد الحبات داخل الكرتونة</label>
                    <input type="number" name="items_per_carton" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">سعر مبيع الحبة ₪</label>
                    <input type="number" step="0.01" name="selling_price_item" class="form-input" required>
                </div>
            </div>
            
            <div style="display: flex; gap: 16px; margin-top: 16px;">
                <button type="submit" class="btn btn-primary">حفظ الصنف</button>
                <button type="button" onclick="toggleModal('add-product-modal')" class="btn btn-outline">إلغاء</button>
            </div>
        </form>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px;">
        @forelse($products as $product)
            <div class="card" style="padding: 16px; position: relative; border-top: 3px solid var(--ps-blue); display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: var(--ps-blue);">{{ $product->name }}</h3>
                    <div style="display: flex; gap: 4px;">
                        <button onclick="toggleModal('edit-product-modal-{{ $product->id }}')" style="background: rgba(0, 112, 204, 0.1); border: none; color: var(--ps-blue); cursor: pointer; padding: 8px; border-radius: 4px; display: flex; align-items: center; justify-content: center;" title="تعديل">
                            <i data-lucide="edit-3" style="width: 16px; height: 16px;"></i>
                        </button>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الصنف؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: rgba(239, 68, 68, 0.1); border: none; color: #ef4444; cursor: pointer; padding: 8px; border-radius: 4px; display: flex; align-items: center; justify-content: center;" title="حذف">
                                <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Edit Product Form (Hidden per product) -->
                <div id="edit-product-modal-{{ $product->id }}" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
                    <div class="card" style="max-width: 600px; width: 100%; max-height: 90vh; overflow-y: auto;">
                        <h3 style="margin-bottom: 24px;">تعديل الصنف: {{ $product->name }}</h3>
                        <form action="{{ route('products.update', $product->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-2">
                                <div class="form-group">
                                    <label class="form-label">اسم الصنف</label>
                                    <input type="text" name="name" class="form-input" value="{{ $product->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">سعر شراء الكرتونة ₪</label>
                                    <input type="number" step="0.01" name="purchase_price_carton" class="form-input" value="{{ $product->purchase_price_carton }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">عدد الحبات داخل الكرتونة</label>
                                    <input type="number" name="items_per_carton" class="form-input" value="{{ $product->items_per_carton }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">سعر مبيع الحبة ₪</label>
                                    <input type="number" step="0.01" name="selling_price_item" class="form-input" value="{{ $product->selling_price_item }}" required>
                                </div>
                            </div>
                            <div style="display: flex; gap: 16px; margin-top: 16px;">
                                <button type="submit" class="btn btn-primary">تحديث</button>
                                <button type="button" onclick="toggleModal('edit-product-modal-{{ $product->id }}')" class="btn btn-outline">إلغاء</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px;">
                    <div>
                        <div style="color: #64748b; font-size: 11px;">المخزون المتوفر</div>
                        <div style="font-weight: 700;">
                            {{ $product->cartons_stock }} كرتونة 
                            @if($product->items_stock > 0)
                                <span style="color: #94a3b8; font-size: 11px;">(+ {{ $product->items_stock }} حبة)</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div style="color: #64748b; font-size: 11px;">سعر المبيع</div>
                        <div style="font-weight: 700; color: #059669;">₪{{ number_format($product->selling_price_item, 2) }}</div>
                    </div>
                    <div>
                        <div style="color: #64748b; font-size: 11px;">التكلفة (كرتونة)</div>
                        <div style="font-weight: 600;">₪{{ number_format($product->purchase_price_carton, 2) }}</div>
                    </div>
                    <div>
                        <div style="color: #64748b; font-size: 11px;">الربح المتوقع</div>
                        <div style="font-weight: 600; color: var(--ps-blue);">₪{{ number_format($product->profit_per_item, 2) }} <small>(للحبة)</small></div>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: span 3; text-align: center; padding: 48px; color: var(--text-mute); background: #fff; border: 1px dashed #eee;">
                لا يوجد أصناف في النظام حتى الآن. أضف صنفاً جديداً!
            </div>
        @endforelse
    </div>
</div>

<script>
    function toggleModal(id) {
        const el = document.getElementById(id);
        if (el.style.display === 'none' || el.style.display === '') {
            el.style.display = (id.includes('edit')) ? 'flex' : 'block';
        } else {
            el.style.display = 'none';
        }
    }
</script>
@endsection

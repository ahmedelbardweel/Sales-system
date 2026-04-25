@extends('components.layouts.app')

@section('content')
<div class="container" style="padding: 12px; max-width: 1400px; margin: 0 auto;">
    <div class="page-header" style="margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
        <h1 class="page-title" style="font-size: 18px; font-weight: 800;">نقطة البيع (نظام احترافي)</h1>
    </div>

    <div id="status-message" style="display: none; padding: 8px; margin-bottom: 12px; border-radius: 4px; font-size: 12px; text-align: center;"></div>

    <div class="pos-layout">
        <!-- Products Section -->
        <div class="products-section">
            <div class="card" style="background: #fff; border: 1px solid #eee; padding: 12px; height: 100%;">
                <div style="position: relative; margin-bottom: 12px;">
                    <input type="text" id="product-search" class="form-input" placeholder="ابحث عن صنف بالاسم..." style="padding-right: 32px; height: 38px; border-radius: 0; border: 2px solid #eee; font-size: 13px;">
                    <div style="position: absolute; right: 10px; top: 10px; color: #94a3b8;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                </div>

                <div id="product-grid" class="product-grid">
                    @foreach($products as $product)
                    <div class="product-card" data-name="{{ $product->name }}" style="border: 1px solid #eee; padding: 8px; text-align: center; transition: 0.2s; display: flex; flex-direction: column; background: #fff; height: 100%;">
                        <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center; min-height: 70px;">
                            <h4 style="font-size: 12px; font-weight: 700; margin-bottom: 3px; color: #000; line-height: 1.2;">{{ $product->name }}</h4>
                            <div style="color: var(--ps-blue); font-weight: 800; font-size: 14px; margin-bottom: 3px;">₪{{ number_format($product->selling_price_item, 2) }}</div>
                            <div style="font-size: 9px; color: #64748b;">المخزون: {{ $product->total_items_stock }}</div>
                        </div>
                        <button onclick="addToCart({{ $product->id }})" class="btn btn-primary" style="width: 100%; border-radius: 0; padding: 6px; font-weight: 700; font-size: 10px; margin-top: 8px;">إضافة</button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Cart Section -->
        <div class="cart-section">
            <div class="card cart-card">
                <div style="padding: 12px; border-bottom: 1px dashed #eee; background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; font-size: 14px; font-weight: 800; color: var(--ps-blue); display: flex; align-items: center; gap: 6px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                        سلة المشتريات
                    </h3>
                </div>
                
                <div id="cart-items" style="padding: 12px; overflow-y: auto; flex-grow: 1; min-height: 100px;">
                    <!-- Cart items loaded via JS -->
                    @include('pos.partials.cart_items', ['cart' => $cart])
                </div>

                <div style="padding: 12px; border-top: 1px solid #eee; background: #fff;">
                    <div style="background: var(--ps-ice); padding: 12px; border-radius: 0; margin-bottom: 12px; text-align: center;">
                        <div style="font-size: 10px; font-weight: 700; color: #64748b; margin-bottom: 2px;">المبلغ الإجمالي</div>
                        <div id="cart-total" style="font-size: 24px; font-weight: 900; color: var(--ps-blue);">₪{{ number_format(array_reduce($cart, function($c, $i){ return $c + ($i['quantity']*$i['unit_price']); }, 0), 2) }}</div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 12px;">
                        <div class="form-group" style="margin: 0;">
                            <label style="font-size: 10px; font-weight: 800; display: block; margin-bottom: 4px;">الدفع</label>
                            <select id="payment-type" class="form-input" style="height: 34px; font-size: 11px; padding: 4px;" onchange="toggleCustomerSelect()">
                                <option value="cash">نقدي 💵</option>
                                <option value="debt">دين 📒</option>
                            </select>
                        </div>
                        <div id="customer-select-group" class="form-group" style="display: none; margin: 0;">
                            <label style="font-size: 10px; font-weight: 800; display: block; margin-bottom: 4px;">الزبون</label>
                            <select id="customer-id" class="form-input" style="height: 34px; font-size: 11px; padding: 4px;">
                                <option value="">-- اختر --</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button onclick="checkout()" id="checkout-btn" class="btn btn-primary checkout-btn">
                        إتمام العملية والطباعة
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pos-layout {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 12px;
        align-items: start;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 10px;
        align-items: stretch;
    }

    .cart-card {
        background: #fff;
        border: 1px solid #eee;
        border-top: 4px solid var(--ps-blue);
        padding: 0;
        position: sticky;
        top: 80px;
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 100px);
    }

    .checkout-btn {
        width: 100%;
        padding: 14px;
        font-size: 14px;
        font-weight: 800;
        background: var(--ps-blue);
        border: none;
        transition: 0.2s;
    }

    /* Mobile Specific */
    @media (max-width: 768px) {
        .pos-layout {
            grid-template-columns: 1fr;
            display: flex;
            flex-direction: column-reverse;
            width: 100%;
        }

        .products-section, .cart-section {
            width: 100%;
        }
        
        .cart-card {
            position: relative;
            top: 0;
            max-height: none;
            margin-bottom: 20px;
            width: 100%;
        }

        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        }

        .container {
            padding: 5px !important;
            max-width: 100% !important;
            width: 100% !important;
        }
    }

    .product-card:hover {
        border-color: var(--ps-blue) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
</style>

<script>
    function addToCart(productId) {
        fetch('{{ route('pos.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateCartUI(data);
            } else {
                showStatus(data.error, 'danger');
            }
        });
    }

    function removeFromCart(productId) {
        fetch('{{ route('pos.remove') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateCartUI(data);
            }
        });
    }

    function updateCartUI(data) {
        const cartItems = document.getElementById('cart-items');
        const cartTotal = document.getElementById('cart-total');
        
        let html = '';
        if (Object.keys(data.cart).length === 0) {
            html = '<div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 12px;">السلة فارغة</div>';
        } else {
            Object.values(data.cart).forEach(item => {
                html += `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                        <div style="flex: 1; padding-left: 8px;">
                            <div style="font-weight: 700; font-size: 12px; color: #000;">${item.name}</div>
                            <small style="color: #64748b; font-size: 10px;">₪${item.unit_price.toFixed(2)}</small>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <button onclick="removeFromCart(${item.product_id})" style="width: 22px; height: 22px; border: none; background: #fee2e2; color: #ef4444; cursor: pointer; border-radius: 4px; font-weight: bold; font-size: 12px;">-</button>
                            <span style="font-weight: 800; font-size: 12px; min-width: 15px; text-align: center;">${item.quantity}</span>
                            <button onclick="addToCart(${item.product_id})" style="width: 22px; height: 22px; border: none; background: #e0f2fe; color: #0284c7; cursor: pointer; border-radius: 4px; font-weight: bold; font-size: 12px;">+</button>
                        </div>
                    </div>
                `;
            });
        }
        
        cartItems.innerHTML = html;
        cartTotal.innerText = '₪' + data.total.toFixed(2);
    }

    function toggleCustomerSelect() {
        const type = document.getElementById('payment-type').value;
        document.getElementById('customer-select-group').style.display = (type === 'debt') ? 'block' : 'none';
    }

    function checkout() {
        const btn = document.getElementById('checkout-btn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'جاري المعالجة...';

        fetch('{{ route('pos.checkout') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                payment_type: document.getElementById('payment-type').value,
                customer_id: document.getElementById('customer-id').value
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showStatus(data.message, 'success');
                if (data.sale_id) {
                    window.open('/print/invoice/' + data.sale_id, '_blank');
                }
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showStatus(data.error, 'danger');
                btn.disabled = false;
                btn.innerText = originalText;
            }
        });
    }

    function showStatus(msg, type) {
        const el = document.getElementById('status-message');
        el.innerText = msg;
        el.style.display = 'block';
        el.style.background = (type === 'success') ? '#dcfce7' : '#fee2e2';
        el.style.color = (type === 'success') ? '#166534' : '#991b1b';
        setTimeout(() => el.style.display = 'none', 3000);
    }

    // Product Search
    document.getElementById('product-search').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.getAttribute('data-name').toLowerCase();
            card.style.display = name.includes(term) ? 'block' : 'none';
        });
    });
</script>
@endsection

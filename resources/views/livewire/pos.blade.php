<div>
    <div class="page-header">
        <h1 class="page-title">نقطة البيع (الكاشير)</h1>
    </div>

    @if (session()->has('message'))
        <div class="badge badge-success"
            style="padding: 10px; width: 100%; margin-bottom: 16px; font-size: 13px; display: flex; justify-content: space-between; border-radius: 0;">
            <div>{{ session('message') }}</div>
            @if($lastSaleId)
                <a href="/print/invoice/{{ $lastSaleId }}" target="_blank" class="btn btn-primary btn-sm">
                    <i data-lucide="printer"></i> طباعة الفاتورة #{{ $lastSaleId }}
                </a>
            @endif
        </div>
    @endif
    @if (session()->has('error'))
        <div class="badge badge-danger"
            style="padding: 10px; width: 100%; margin-bottom: 16px; font-size: 13px; border-radius: 0;">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-3" style="gap: 16px;">
        <!-- Products Search & List (2/3 width) -->
        <div style="grid-column: span 2;">
            <div class="card">
                <div class="form-group" style="position: relative;">
                    <i data-lucide="search"
                        style="position: absolute; right: 12px; top: 8px; color: var(--text-mute); width: 16px;"></i>
                    <input type="text" wire:model.live="search" class="form-input" placeholder="ابحث عن صنف بالاسم..."
                        style="padding-right: 36px;">
                </div>

                <div class="grid grid-cols-3" style="gap: 12px;">
                    @forelse($products as $product)
                        <div wire:key="prod-{{ $product->id }}"
                            style="border: 1px solid var(--border-light); border-radius: 0; padding: 10px; text-align: center; transition: all 0.2s; position: relative;"
                            onmouseover="this.style.borderColor='var(--ps-blue)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)'"
                            onmouseout="this.style.borderColor='var(--border-light)'; this.style.boxShadow='none'">
                            <h4 style="font-weight: 700; margin-bottom: 4px; font-size: 14px;">{{ $product->name }}</h4>
                            <p style="color: var(--ps-blue); font-weight: 700; font-size: 16px; margin-bottom: 8px;">
                                ₪{{ number_format($product->selling_price_item, 2) }}</p>
                            <p style="font-size: 11px; color: var(--text-mute);">المخزون: {{ $product->total_items_stock }}
                                حبة</p>

                            <!-- Mini CTA style -->
                            <div style="margin-top: 10px;">
                                <button wire:click="addToCart({{ $product->id }})" wire:loading.attr="disabled"
                                    class="btn btn-primary btn-sm"
                                    style="width: 100%; font-size: 11px; justify-content: center;">
                                    إضافة بالسلة
                                </button>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column: span 3; text-align: center; padding: 48px; color: var(--text-mute);">
                            لم يتم العثور على نتائج.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Shopping Cart (1/3 width) -->
        <div style="grid-column: span 1;">
            <div class="card" style="position: sticky; top: 16px; border-top: 4px solid var(--ps-blue);">
                <h3
                    style="margin-bottom: 16px; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="shopping-cart" style="width: 18px;"></i> سلة المشتريات
                </h3>

                @if(!empty($cart))
                    <div style="margin-bottom: 12px; max-height: 250px; overflow-y: auto;">
                        @foreach($cart as $id => $item)
                            <div wire:key="cart-{{ $id }}"
                                style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--border-light);">
                                <div style="flex: 1;">
                                    <h5 style="font-weight: 700; font-size: 13px; margin: 0;">{{ $item['name'] }}</h5>
                                    <p style="color: var(--text-mute); font-size: 11px; margin: 0;">
                                        ₪{{ number_format($item['unit_price'], 2) }}</p>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <button wire:click="removeFromCart({{ $id }})" wire:loading.attr="disabled"
                                        wire:target="removeFromCart({{ $id }})" class="btn-icon"
                                        style="background: rgba(200, 27, 58, 0.1); color: var(--text-error); width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border: none;">
                                        <i data-lucide="minus" style="width: 14px; height: 14px;"></i>
                                    </button>
                                    <span
                                        style="font-weight: 700; font-size: 13px; width: 20px; text-align: center;">{{ $item['quantity'] }}</span>
                                    <button wire:click="addToCart({{ $item['product_id'] }})" wire:loading.attr="disabled"
                                        wire:target="addToCart({{ $item['product_id'] }})" class="btn-icon"
                                        style="background: rgba(0, 112, 204, 0.1); color: var(--ps-blue); width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border: none;">
                                        <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="background: var(--ps-ice); padding: 10px; margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; font-size: 15px; font-weight: 700;">
                            <span>الإجمالي:</span>
                            <span>₪{{ number_format($this->total, 2) }}</span>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 10px;">
                        <label class="form-label" style="font-size: 11px;">طريقة الدفع</label>
                        <select wire:model.live="paymentType" class="form-input" style="padding: 4px 8px; font-size: 12px;">
                            <option value="cash">نقدي 💰</option>
                            <option value="debt">دين 📒</option>
                        </select>
                    </div>

                    @if($paymentType === 'debt')
                        <div class="form-group" style="margin-bottom: 10px;" wire:key="customer-select-group">
                            <label class="form-label" style="font-size: 11px;">الزبون (الذي عليه دين)</label>
                            <select wire:model="customerId" class="form-input" style="padding: 4px 8px; font-size: 12px;"
                                required>
                                <option value="">-- اختر الزبون --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} (الدين:
                                        ₪{{ number_format($customer->total_debt, 2) }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <button wire:click="checkout" class="btn btn-primary"
                        style="width: 100%; font-size: 13px; padding: 10px;">
                        إتمام البيع
                    </button>
                @else
                    <div style="text-align: center; padding: 30px 0; color: var(--text-mute);">
                        <p style="font-size: 13px;">السلة فارغة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
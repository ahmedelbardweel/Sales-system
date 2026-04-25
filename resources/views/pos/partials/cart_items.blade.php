@if(empty($cart))
    <div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 12px;">السلة فارغة</div>
@else
    @foreach($cart as $id => $item)
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #eee;">
            <div>
                <div style="font-weight: 700; font-size: 13px;">{{ $item['name'] }}</div>
                <small style="color: #64748b; font-size: 10px;">₪{{ number_format($item['unit_price'], 2) }}</small>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <button onclick="removeFromCart({{ $id }})" style="width: 20px; height: 20px; border: none; background: #fee2e2; color: #ef4444; cursor: pointer; border-radius: 4px; font-weight: bold; font-size: 12px;">-</button>
                <span style="font-weight: 800; font-size: 13px;">{{ $item['quantity'] }}</span>
                <button onclick="addToCart({{ $id }})" style="width: 20px; height: 20px; border: none; background: #e0f2fe; color: #0284c7; cursor: pointer; border-radius: 4px; font-weight: bold; font-size: 12px;">+</button>
            </div>
        </div>
    @endforeach
@endif

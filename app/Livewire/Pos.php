<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Customer;
use App\Services\SaleService;
use Exception;

class Pos extends Component
{
    public $search = '';
    public $cart = [];
    public $paymentType = 'cash';
    public $customerId = null;
    public $lastSaleId = null;

    public function mount()
    {
        // جلب السلة من الجلسة عند تحميل الصفحة لأول مرة
        $this->cart = session()->get('cart', []);
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            session()->flash('error', 'الصنف غير موجود');
            return;
        }

        // تحديث السلة المحلية والجلسة
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'unit_price' => (float) $product->selling_price_item,
                'quantity' => 1,
            ];
        }

        session()->put('cart', $this->cart);
        session()->flash('message', 'تمت إضافة ' . $product->name . ' للسلة');
    }

    public function removeFromCart($productId)
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] > 1) {
                $this->cart[$productId]['quantity']--;
            } else {
                unset($this->cart[$productId]);
            }
        }

        session()->put('cart', $this->cart);
    }

    // خاصية محسوبة للإجمالي (Computed Property)
    public function getTotalProperty()
    {
        return array_reduce($this->cart, function ($carry, $item) {
            return $carry + ($item['quantity'] * $item['unit_price']);
        }, 0);
    }

    public function checkout(SaleService $saleService)
    {
        if (empty($this->cart)) {
            session()->flash('error', 'السلة فارغة!');
            return;
        }

        if ($this->paymentType === 'debt' && !$this->customerId) {
            session()->flash('error', 'يرجى اختيار زبون للمبيعات الآجلة (الدين).');
            return;
        }

        try {
            // تنفيذ عملية البيع عبر الـ Service
            $sale = $saleService->createSale($this->cart, $this->paymentType, $this->customerId);

            $this->lastSaleId = $sale->id;

            // تصفير البيانات بعد النجاح
            session()->forget('cart');
            $this->cart = [];
            $this->paymentType = 'cash';
            $this->customerId = null;

            session()->flash('message', 'تمت عملية البيع بنجاح!');
        } catch (Exception $e) {
            session()->flash('error', 'خطأ: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pos', [
            'products' => Product::where('name', 'like', '%' . $this->search . '%')
                ->limit(12) // لتحسين الأداء
                ->get(),
            'customers' => Customer::all(),
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Exception;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $customers = Customer::all();
        $cart = session()->get('cart', []);
        
        return view('pos.index', compact('products', 'customers', 'cart'));
    }

    public function addToCart(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::find($productId);
        
        if (!$product) {
            return response()->json(['error' => 'الصنف غير موجود'], 404);
        }

        $cart = session()->get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'unit_price' => (float)$product->selling_price_item,
                'quantity' => 1,
            ];
        }

        session()->put('cart', $cart);
        
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => $this->calculateTotal($cart)
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $productId = $request->product_id;
        $cart = session()->get('cart', []);
        
        if (isset($cart[$productId])) {
            if ($cart[$productId]['quantity'] > 1) {
                $cart[$productId]['quantity']--;
            } else {
                unset($cart[$productId]);
            }
        }

        session()->put('cart', $cart);
        
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => $this->calculateTotal($cart)
        ]);
    }

    public function checkout(Request $request, SaleService $saleService)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['error' => 'السلة فارغة'], 400);
        }

        $paymentType = $request->payment_type;
        $customerId = $request->customer_id;

        if ($paymentType === 'debt' && !$customerId) {
            return response()->json(['error' => 'يرجى اختيار زبون للدين'], 400);
        }

        try {
            $sale = $saleService->createSale($cart, $paymentType, $customerId);
            session()->forget('cart');
            
            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'message' => 'تمت عملية البيع بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function calculateTotal($cart)
    {
        return array_reduce($cart, function($carry, $item) {
            return $carry + ($item['quantity'] * $item['unit_price']);
        }, 0);
    }
}

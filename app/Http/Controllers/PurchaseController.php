<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('product')->latest()->get();
        $products = Product::all();
        return view('purchases.index', compact('purchases', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'cartons_purchased' => 'required|integer|min:1',
            'cost_per_carton' => 'required|numeric|min:0.01',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $totalCost = $validated['cartons_purchased'] * $validated['cost_per_carton'];

        Purchase::create([
            'product_id' => $validated['product_id'],
            'cartons_purchased' => $validated['cartons_purchased'],
            'cost_per_carton' => $validated['cost_per_carton'],
            'total_cost' => $totalCost,
        ]);

        $product->cartons_stock += $validated['cartons_purchased'];
        $product->purchase_price_carton = $validated['cost_per_carton'];
        $product->save();

        ActivityLog::create([
            'type' => 'purchase',
            'description' => "فاتورة شراء جديدة: " . $product->name . " (" . $validated['cartons_purchased'] . " كرتونة)",
            'amount' => $totalCost,
            'status' => 'success'
        ]);

        return redirect()->back()->with('message', 'تم تسجيل فاتورة الشراء وتحديث المخزون بنجاح.');
    }
}

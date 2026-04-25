<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();
        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'purchase_price_carton' => 'required|numeric|min:0',
            'items_per_carton' => 'required|integer|min:1',
            'selling_price_item' => 'required|numeric|min:0',
            'initial_cartons' => 'required|integer|min:0',
        ]);

        $product = Product::create($validated);

        // Log Activity
        ActivityLog::create([
            'type' => 'customer', // Using 'customer' as a generic type for admin actions if needed, or I should have used 'product'
            'description' => "إضافة صنف جديد: " . $product->name,
            'status' => 'success'
        ]);

        return redirect()->back()->with('message', 'تم إضافة الصنف بنجاح.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'purchase_price_carton' => 'required|numeric|min:0',
            'items_per_carton' => 'required|integer|min:1',
            'selling_price_item' => 'required|numeric|min:0',
        ]);

        $product->update($validated);

        return redirect()->back()->with('message', 'تم تحديث الصنف بنجاح.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('message', 'تم حذف الصنف بنجاح.');
    }
}

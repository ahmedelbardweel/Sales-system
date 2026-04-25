<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Products;
use App\Livewire\Pos;
use App\Livewire\Customers;
use App\Livewire\Purchases;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

// Purchases
Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');

// Customers
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
Route::post('/customers/{customer}/pay', [CustomerController::class, 'payDebt'])->name('customers.pay');

// Stable POS Routes
Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
Route::post('/pos/add', [PosController::class, 'addToCart'])->name('pos.add');
Route::post('/pos/remove', [PosController::class, 'removeFromCart'])->name('pos.remove');
Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

Route::get('/print/invoice/{id}', function ($id) {
    $sale = App\Models\Sale::with(['items.product', 'customer'])->findOrFail($id);
    return view('print.invoice', compact('sale'));
});

Route::get('/print/summary', function () {
    $today = Carbon\Carbon::today();
    
    $sales = App\Models\Sale::whereDate('created_at', $today)->get();
    $cashProfit = $sales->where('payment_type', 'cash')->sum('total_profit');
    $debtProfit = $sales->where('payment_type', 'debt')->sum('total_profit');
    
    $debtPayments = App\Models\DebtPayment::whereDate('created_at', $today)->sum('amount');
    
    $totalCashIn = $sales->where('payment_type', 'cash')->sum('total_amount') + $debtPayments;
    
    return view('print.summary', compact('cashProfit', 'debtProfit', 'debtPayments', 'totalCashIn', 'today'));
});


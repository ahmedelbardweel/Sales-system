<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\DebtPayment;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Basic Sales Data
        $todaySales = Sale::whereDate('created_at', $today)->sum('total_amount');
        $monthlySales = Sale::whereMonth('created_at', Carbon::now()->month)->sum('total_amount');
        $totalDebt = Customer::sum('total_debt');
        
        // Calculate Profit
        $todayCashSales = Sale::whereDate('created_at', $today)->where('payment_type', 'cash')->with('items.product')->get();
        $todayDebtSales = Sale::whereDate('created_at', $today)->where('payment_type', 'debt')->with('items.product')->get();
        
        $cashProfit = 0;
        foreach ($todayCashSales as $sale) {
            foreach ($sale->items as $item) {
                if ($item->product) {
                    $cashProfit += ($item->quantity * ($item->unit_price - $item->product->purchase_price_item));
                }
            }
        }

        $debtProfit = 0;
        foreach ($todayDebtSales as $sale) {
            foreach ($sale->items as $item) {
                if ($item->product) {
                    $debtProfit += ($item->quantity * ($item->unit_price - $item->product->purchase_price_item));
                }
            }
        }

        $debtPaymentsReceivedToday = DebtPayment::whereDate('payment_date', $today)->sum('amount');
        
        // Alerts
        $lowStockProducts = Product::where('cartons_stock', '<=', 2)->get();
        $highDebtCustomers = Customer::where('total_debt', '>', 500)->get();
        $zeroProfitAlert = ($cashProfit <= 0 && $todaySales > 0);

        $activityLogs = ActivityLog::latest()->take(15)->get();

        return view('dashboard.index', [
            'todaySales' => $todaySales,
            'monthlySales' => $monthlySales,
            'totalDebt' => $totalDebt,
            'cashProfit' => $cashProfit,
            'debtProfit' => $debtProfit,
            'debtPaymentsReceivedToday' => $debtPaymentsReceivedToday,
            'lowStockProducts' => $lowStockProducts,
            'highDebtCustomers' => $highDebtCustomers,
            'zeroProfitAlert' => $zeroProfitAlert,
            'activityLogs' => $activityLogs,
        ]);
    }
}

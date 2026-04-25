<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DebtPayment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount(['sales', 'debtPayments'])->latest()->get();
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer = Customer::create(array_merge($validated, ['total_debt' => 0]));

        ActivityLog::create([
            'type' => 'customer',
            'description' => "تسجيل زبون جديد: " . $customer->name,
            'status' => 'success'
        ]);

        return redirect()->back()->with('message', 'تم إضافة الزبون بنجاح.');
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer->update($validated);

        return redirect()->back()->with('message', 'تم تحديث بيانات الزبون بنجاح.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->back()->with('message', 'تم حذف الزبون بنجاح.');
    }

    public function payDebt(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        DebtPayment::create([
            'customer_id' => $customer->id,
            'amount' => $validated['amount'],
            'notes' => $validated['notes'],
            'payment_date' => now(),
        ]);

        $customer->total_debt -= $validated['amount'];
        $customer->save();

        ActivityLog::create([
            'type' => 'debt_payment',
            'description' => "استلام دفعة من الزبون: " . $customer->name,
            'amount' => $validated['amount'],
            'status' => 'success'
        ]);

        return redirect()->back()->with('message', 'تم تسجيل الدفعة وتحديث الدين بنجاح.');
    }
}

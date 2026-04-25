<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\DebtPayment;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Products (الأصناف)
        $productsData = [
            [
                'name' => 'بسكويت كراميل (Bamba)',
                'purchase_price_carton' => 50.00, // 50 ILS per carton
                'items_per_carton' => 100, // 100 items in a carton
                'selling_price_item' => 0.75, // 0.75 ILS per item (Profit = 25 ILS per carton)
                'cartons_stock' => 10,
                'items_stock' => 0,
            ],
            [
                'name' => 'عصير برتقال طبيعي (سبرينغ)',
                'purchase_price_carton' => 60.00,
                'items_per_carton' => 24,
                'selling_price_item' => 3.00, // 3 ILS per item (Profit = 12 ILS per carton)
                'cartons_stock' => 5,
                'items_stock' => 5, // 5 loose items left
            ],
            [
                'name' => 'شيبس دوريتوس حار',
                'purchase_price_carton' => 45.00,
                'items_per_carton' => 30,
                'selling_price_item' => 2.00, // Profit = 15 ILS per carton
                'cartons_stock' => 2, // Low stock alert!
                'items_stock' => 10,
            ],
            [
                'name' => 'كرتونة مياه معدنية (عين جدي)',
                'purchase_price_carton' => 12.00,
                'items_per_carton' => 12,
                'selling_price_item' => 1.50, // Profit = 6 ILS per carton
                'cartons_stock' => 20,
                'items_stock' => 0,
            ],
        ];

        $products = [];
        foreach ($productsData as $data) {
            $products[] = Product::create($data);
        }

        // 2. Create Purchases History (تاريخ شراء البضاعة)
        Purchase::create([
            'product_id' => $products[0]->id, // بسكويت
            'cartons_purchased' => 5,
            'cost_per_carton' => 50.00,
            'total_cost' => 250.00,
            'created_at' => Carbon::now()->subDays(5),
        ]);
        Purchase::create([
            'product_id' => $products[2]->id, // دوريتوس
            'cartons_purchased' => 2,
            'cost_per_carton' => 45.00,
            'total_cost' => 90.00,
            'created_at' => Carbon::now()->subDays(2),
        ]);

        // 3. Create Customers (الزبائن اللي عليهم ديون)
        $customer1 = Customer::create([
            'name' => 'أحمد حسن (أبو يوسف)',
            'phone' => '0591234567',
            'total_debt' => 650.00, // High debt alert! (> 500)
        ]);

        $customer2 = Customer::create([
            'name' => 'محمود الدكان المجاورة',
            'phone' => '0597654321',
            'total_debt' => 120.50,
        ]);

        $customer3 = Customer::create([
            'name' => 'زبون مجهول (بدون رقم)',
            'phone' => null,
            'total_debt' => 0.00,
        ]);

        // 4. Create Sales History (تاريخ المبيعات)
        
        // Sale 1: Cash Sale today (أرباح محققة)
        $sale1 = Sale::create([
            'customer_id' => null, // Cash customer
            'total_amount' => 12.00,
            'payment_type' => 'cash',
            'created_at' => Carbon::now(), // Today
        ]);
        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $products[1]->id, // عصير
            'quantity' => 4,
            'unit_price' => 3.00,
            'subtotal' => 12.00,
        ]);

        // Sale 2: Debt Sale today (أرباح غير محققة)
        $sale2 = Sale::create([
            'customer_id' => $customer2->id,
            'total_amount' => 20.00,
            'payment_type' => 'debt',
            'created_at' => Carbon::now(), // Today
        ]);
        SaleItem::create([
            'sale_id' => $sale2->id,
            'product_id' => $products[2]->id, // دوريتوس
            'quantity' => 10,
            'unit_price' => 2.00,
            'subtotal' => 20.00,
        ]);

        // Sale 3: Old Cash Sale (أمس)
        $sale3 = Sale::create([
            'customer_id' => null,
            'total_amount' => 7.50,
            'payment_type' => 'cash',
            'created_at' => Carbon::now()->subDay(),
        ]);
        SaleItem::create([
            'sale_id' => $sale3->id,
            'product_id' => $products[0]->id, // بسكويت
            'quantity' => 10,
            'unit_price' => 0.75,
            'subtotal' => 7.50,
        ]);

        // 5. Debt Payments Received Today (سداد ديون)
        DebtPayment::create([
            'customer_id' => $customer1->id,
            'amount' => 50.00,
            'payment_date' => Carbon::today(),
            'notes' => 'دفعة نقدية اليوم',
            'created_at' => Carbon::now(),
        ]);
        // Note: The total_debt of $customer1 was already set to 650 manually in seed.
        // In reality, it was 700 and they paid 50.
    }
}

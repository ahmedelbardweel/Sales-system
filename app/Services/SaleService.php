<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Exception;

class SaleService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Create a new sale.
     * $items = [['product_id' => 1, 'quantity' => 2, 'unit_price' => 0.70], ...]
     */
    public function createSale(array $items, string $paymentType, ?int $customerId = null)
    {
        if ($paymentType === 'debt' && !$customerId) {
            throw new Exception("Customer is required for debt sales.");
        }

        DB::beginTransaction();

        try {
            $totalAmount = 0;

            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $totalAmount += $subtotal;
            }

            $sale = Sale::create([
                'customer_id' => $customerId,
                'total_amount' => $totalAmount,
                'payment_type' => $paymentType,
            ]);

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $item['quantity'] * $item['unit_price'];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);

                // Deduct inventory
                $this->inventoryService->deductStock($product, $item['quantity']);
            }

            if ($paymentType === 'debt' && $customerId) {
                $customer = Customer::findOrFail($customerId);
                $customer->total_debt += $totalAmount;
                $customer->save();
            }

            DB::commit();

            // Log Activity
            $desc = ($paymentType === 'cash') ? "عملية بيع نقدي" : "عملية بيع دين للزبون: " . $customer->name;
            \App\Models\ActivityLog::create([
                'type' => 'sale',
                'description' => $desc,
                'amount' => $totalAmount,
                'status' => 'success'
            ]);

            return $sale;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

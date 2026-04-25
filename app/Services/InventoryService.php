<?php

namespace App\Services;

use App\Models\Product;
use Exception;

class InventoryService
{
    /**
     * Deduct items from stock. Automatically opens a new carton if needed.
     */
    public function deductStock(Product $product, int $quantity)
    {
        $totalAvailable = ($product->cartons_stock * $product->items_per_carton) + $product->items_stock;

        if ($quantity > $totalAvailable) {
            throw new Exception("Insufficient stock for {$product->name}.");
        }

        // Deduct from current items stock first
        if ($product->items_stock >= $quantity) {
            $product->items_stock -= $quantity;
        } else {
            // Need to open cartons
            $remainingToDeduct = $quantity - $product->items_stock;
            $product->items_stock = 0;

            $cartonsNeeded = ceil($remainingToDeduct / $product->items_per_carton);
            
            $product->cartons_stock -= $cartonsNeeded;
            $product->items_stock = ($cartonsNeeded * $product->items_per_carton) - $remainingToDeduct;
        }

        $product->save();
    }

    /**
     * Add stock (cartons)
     */
    public function addStock(Product $product, int $cartons)
    {
        $product->cartons_stock += $cartons;
        $product->save();
    }
}

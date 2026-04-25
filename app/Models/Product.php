<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'purchase_price_carton',
        'items_per_carton',
        'selling_price_item',
        'cartons_stock',
        'items_stock',
    ];

    public function getPurchasePriceItemAttribute()
    {
        return $this->purchase_price_carton / $this->items_per_carton;
    }

    public function getProfitPerItemAttribute()
    {
        return $this->selling_price_item - $this->purchase_price_item;
    }

    public function getProfitPerCartonAttribute()
    {
        return $this->profit_per_item * $this->items_per_carton;
    }

    public function getTotalItemsStockAttribute()
    {
        return ($this->cartons_stock * $this->items_per_carton) + $this->items_stock;
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}

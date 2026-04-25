<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'cartons_purchased',
        'cost_per_carton',
        'total_cost',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('purchase_price_carton', 10, 2);
            $table->integer('items_per_carton');
            $table->decimal('selling_price_item', 10, 2);
            $table->integer('cartons_stock')->default(0);
            $table->integer('items_stock')->default(0); // Items from the currently open carton
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

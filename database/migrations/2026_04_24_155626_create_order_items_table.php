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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->string('product_name_ar');
            $table->string('product_name_en');
            $table->string('sku');
            $table->integer('quantity');
            $table->decimal('unit_price_base', 18, 2);
            $table->decimal('unit_price_display', 18, 2);
            $table->decimal('subtotal_base', 18, 2);
            $table->decimal('subtotal_display', 18, 2);
            $table->decimal('discount_base', 18, 2)->default(0);
            $table->decimal('discount_display', 18, 2)->default(0);
            $table->decimal('total_base', 18, 2);
            $table->decimal('total_display', 18, 2);
            $table->json('product_snapshot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

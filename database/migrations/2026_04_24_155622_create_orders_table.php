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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('currency_code', 10);
            $table->decimal('exchange_rate', 18, 6)->default(1);
            $table->decimal('subtotal_base', 18, 2);
            $table->decimal('discount_base', 18, 2)->default(0);
            $table->decimal('shipping_base', 18, 2)->default(0);
            $table->decimal('tax_base', 18, 2)->default(0);
            $table->decimal('total_base', 18, 2);
            $table->decimal('subtotal_display', 18, 2);
            $table->decimal('discount_display', 18, 2)->default(0);
            $table->decimal('shipping_display', 18, 2)->default(0);
            $table->decimal('tax_display', 18, 2)->default(0);
            $table->decimal('total_display', 18, 2);
            $table->enum('payment_status', ['unpaid', 'initiated', 'paid', 'failed', 'cancelled', 'refunded', 'partially_refunded'])->default('unpaid');
            $table->enum('order_status', ['pending_payment', 'pending_bank_review', 'confirmed', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'cancelled', 'returned', 'refunded'])->default('pending_payment');
            $table->foreignId('shipping_method_id')->nullable()->constrained('shipping_methods')->onDelete('set null');
            $table->json('shipping_address_snapshot')->nullable();
            $table->json('billing_address_snapshot')->nullable();
            $table->string('coupon_code')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

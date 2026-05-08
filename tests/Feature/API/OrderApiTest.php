<?php

namespace Tests\Feature\API;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_customer_can_checkout_cart(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = $this->product();
        $cart = Cart::create(['user_id' => $user->id, 'currency_code' => 'SAR']);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => $product->effective_price,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_name' => 'NOVA Customer',
            'customer_email' => $user->email,
            'customer_phone' => '0500000000',
            'shipping_address' => [
                'city' => 'Riyadh',
                'street' => 'King Fahd Road',
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_base', '200.00')
            ->assertJsonPath('data.payment_status', 'initiated')
            ->assertJsonPath('data.order_status', 'pending_payment');

        $this->assertDatabaseHas('order_items', ['product_id' => $product->id, 'quantity' => 2]);
        $this->assertDatabaseHas('payments', ['amount_base' => 200, 'status' => 'pending']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 8]);
        $this->assertDatabaseMissing('cart_items', ['cart_id' => $cart->id]);
    }

    private function product(): Product
    {
        $group = ProductGroup::create([
            'name_ar' => 'مجموعة',
            'name_en' => 'Group',
            'slug' => 'checkout-group',
        ]);

        $category = Category::create([
            'product_group_id' => $group->id,
            'name_ar' => 'تصنيف',
            'name_en' => 'Category',
            'slug' => 'checkout-category',
        ]);

        return Product::create([
            'product_group_id' => $group->id,
            'category_id' => $category->id,
            'name_ar' => 'منتج',
            'name_en' => 'Product',
            'slug' => 'checkout-product',
            'sku' => 'CHECKOUT-SKU',
            'base_price' => 100,
            'stock_quantity' => 10,
            'status' => 'active',
            'stock_status' => 'in_stock',
        ]);
    }
}

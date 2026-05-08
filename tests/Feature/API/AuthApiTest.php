<?php

namespace Tests\Feature\API;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'NOVA Customer',
            'email' => 'customer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['user' => ['id', 'email'], 'token']]);
    }

    public function test_login_merges_guest_cart_into_user_cart(): void
    {
        $user = \App\Models\User::factory()->create(['password' => 'password123']);
        $product = $this->activeProduct();
        $guestCart = Cart::create(['session_id' => 'guest-123', 'currency_code' => 'SAR']);
        CartItem::create([
            'cart_id' => $guestCart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => $product->effective_price,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
            'cart_session' => 'guest-123',
        ]);

        $response->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseHas('carts', ['user_id' => $user->id]);
        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 2]);
        $this->assertDatabaseMissing('carts', ['session_id' => 'guest-123']);
    }

    private function activeProduct(): Product
    {
        $group = ProductGroup::create([
            'name_ar' => 'مجموعة',
            'name_en' => 'Group',
            'slug' => 'group',
        ]);

        $category = Category::create([
            'product_group_id' => $group->id,
            'name_ar' => 'تصنيف',
            'name_en' => 'Category',
            'slug' => 'category',
        ]);

        return Product::create([
            'product_group_id' => $group->id,
            'category_id' => $category->id,
            'name_ar' => 'منتج',
            'name_en' => 'Product',
            'slug' => 'product',
            'sku' => 'SKU-1',
            'base_price' => 100,
            'stock_quantity' => 10,
            'status' => 'active',
            'stock_status' => 'in_stock',
        ]);
    }
}

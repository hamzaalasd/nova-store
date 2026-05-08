<?php

namespace Tests\Feature\API;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogAndCartApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_endpoint_filters_visible_products(): void
    {
        $category = $this->category();
        $active = $this->product($category, ['name_en' => 'Solar Kit', 'slug' => 'solar-kit', 'sku' => 'SKU-A']);
        $this->product($category, ['name_en' => 'Hidden Product', 'slug' => 'hidden-product', 'sku' => 'SKU-B', 'status' => 'draft']);

        $response = $this->getJson('/api/v1/products?search=Solar');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $active->id);
    }

    public function test_guest_can_add_update_and_remove_cart_item(): void
    {
        $product = $this->product($this->category());

        $this->postJson('/api/v1/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ], ['X-Cart-Session' => 'session-1'])
            ->assertOk()
            ->assertJsonPath('data.items_count', 2)
            ->assertJsonPath('data.subtotal', '200.00');

        $itemId = \App\Models\CartItem::query()->value('id');

        $this->putJson("/api/v1/cart/{$itemId}", [
            'quantity' => 3,
        ], ['X-Cart-Session' => 'session-1'])
            ->assertOk()
            ->assertJsonPath('data.items_count', 3)
            ->assertJsonPath('data.subtotal', '300.00');

        $this->deleteJson("/api/v1/cart/{$itemId}", [], ['X-Cart-Session' => 'session-1'])
            ->assertOk()
            ->assertJsonPath('data.items_count', 0);
    }

    private function category(): Category
    {
        $group = ProductGroup::create([
            'name_ar' => 'مجموعة',
            'name_en' => 'Group',
            'slug' => 'group-'.uniqid(),
        ]);

        return Category::create([
            'product_group_id' => $group->id,
            'name_ar' => 'تصنيف',
            'name_en' => 'Category',
            'slug' => 'category-'.uniqid(),
        ]);
    }

    private function product(Category $category, array $overrides = []): Product
    {
        return Product::create(array_merge([
            'product_group_id' => $category->product_group_id,
            'category_id' => $category->id,
            'name_ar' => 'منتج',
            'name_en' => 'Product',
            'slug' => 'product-'.uniqid(),
            'sku' => 'SKU-'.uniqid(),
            'base_price' => 100,
            'stock_quantity' => 10,
            'status' => 'active',
            'stock_status' => 'in_stock',
        ], $overrides));
    }
}

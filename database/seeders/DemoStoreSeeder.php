<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoStoreSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@nova.test'],
            [
                'name' => 'NOVA Admin',
                'phone' => '0500000001',
                'password' => 'password',
                'type' => 'admin',
                'status' => 'active',
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@nova.test'],
            [
                'name' => 'عميل نوفا',
                'phone' => '0500000002',
                'password' => 'password',
                'type' => 'customer',
                'status' => 'active',
            ]
        );

        $groups = [
            'smart-devices' => ['name_ar' => 'الأجهزة الذكية', 'name_en' => 'Smart Devices'],
            'home-tech' => ['name_ar' => 'تقنية المنزل', 'name_en' => 'Home Tech'],
            'gaming' => ['name_ar' => 'الألعاب والترفيه', 'name_en' => 'Gaming'],
        ];

        foreach ($groups as $slug => $data) {
            ProductGroup::updateOrCreate(['slug' => $slug], $data + [
                'slug' => $slug,
                'is_active' => true,
                'sort_order' => 1,
            ]);
        }

        $categories = [
            ['group' => 'smart-devices', 'slug' => 'wearables', 'name_ar' => 'الساعات والسماعات', 'name_en' => 'Wearables'],
            ['group' => 'smart-devices', 'slug' => 'phones-accessories', 'name_ar' => 'إكسسوارات الجوال', 'name_en' => 'Phone Accessories'],
            ['group' => 'home-tech', 'slug' => 'smart-home', 'name_ar' => 'المنزل الذكي', 'name_en' => 'Smart Home'],
            ['group' => 'gaming', 'slug' => 'gaming-gear', 'name_ar' => 'معدات الألعاب', 'name_en' => 'Gaming Gear'],
        ];

        foreach ($categories as $index => $category) {
            $group = ProductGroup::where('slug', $category['group'])->firstOrFail();

            Category::updateOrCreate(['slug' => $category['slug']], [
                'product_group_id' => $group->id,
                'name_ar' => $category['name_ar'],
                'name_en' => $category['name_en'],
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }

        $products = [
            ['category' => 'wearables', 'sku' => 'NOVA-WATCH-PRO', 'slug' => 'nova-watch-pro', 'name_ar' => 'ساعة NOVA Pro الذكية', 'name_en' => 'NOVA Watch Pro', 'price' => 899, 'sale' => 749, 'stock' => 24, 'featured' => true],
            ['category' => 'wearables', 'sku' => 'NOVA-BUDS-AIR', 'slug' => 'nova-buds-air', 'name_ar' => 'سماعات NOVA Buds Air', 'name_en' => 'NOVA Buds Air', 'price' => 449, 'sale' => 399, 'stock' => 7, 'featured' => true],
            ['category' => 'phones-accessories', 'sku' => 'NOVA-POWER-20K', 'slug' => 'nova-power-20k', 'name_ar' => 'باور بنك سريع 20000mAh', 'name_en' => 'Fast Power Bank 20K', 'price' => 219, 'sale' => null, 'stock' => 42, 'featured' => false],
            ['category' => 'smart-home', 'sku' => 'NOVA-HUB-MINI', 'slug' => 'nova-hub-mini', 'name_ar' => 'مركز تحكم المنزل الذكي', 'name_en' => 'Smart Home Hub Mini', 'price' => 599, 'sale' => 529, 'stock' => 12, 'featured' => true],
            ['category' => 'smart-home', 'sku' => 'NOVA-CAM-2K', 'slug' => 'nova-cam-2k', 'name_ar' => 'كاميرا مراقبة 2K داخلية', 'name_en' => 'Indoor Security Cam 2K', 'price' => 349, 'sale' => null, 'stock' => 5, 'featured' => false],
            ['category' => 'gaming-gear', 'sku' => 'NOVA-GAMEPAD-X', 'slug' => 'nova-gamepad-x', 'name_ar' => 'يد تحكم NOVA X', 'name_en' => 'NOVA Gamepad X', 'price' => 299, 'sale' => 249, 'stock' => 18, 'featured' => true],
        ];

        foreach ($products as $product) {
            $category = Category::where('slug', $product['category'])->firstOrFail();

            Product::updateOrCreate(['sku' => $product['sku']], [
                'product_group_id' => $category->product_group_id,
                'category_id' => $category->id,
                'name_ar' => $product['name_ar'],
                'name_en' => $product['name_en'],
                'slug' => $product['slug'],
                'short_description_ar' => 'منتج مختار بعناية لتجربة متجر NOVA الاحترافية.',
                'description_ar' => 'وصف تسويقي منظم يوضح المزايا الأساسية، الاستخدامات، وما يجعل المنتج مناسباً للشراء من متجر NOVA.',
                'base_price' => $product['price'],
                'sale_price' => $product['sale'],
                'cost_price' => round($product['price'] * 0.62, 2),
                'stock_quantity' => $product['stock'],
                'manage_stock' => true,
                'low_stock_threshold' => 8,
                'stock_status' => $product['stock'] > 0 ? 'in_stock' : 'out_of_stock',
                'status' => 'active',
                'is_featured' => $product['featured'],
                'published_at' => now()->subDays(3),
            ]);
        }

        $this->seedOrders($customer, $admin);
    }

    private function seedOrders(User $customer, User $admin): void
    {
        $orderSpecs = [
            ['number' => 'NOVA-DEMO-1001', 'status' => 'delivered', 'payment' => 'paid', 'days' => 10],
            ['number' => 'NOVA-DEMO-1002', 'status' => 'processing', 'payment' => 'paid', 'days' => 3],
            ['number' => 'NOVA-DEMO-1003', 'status' => 'pending_payment', 'payment' => 'initiated', 'days' => 0],
        ];

        foreach ($orderSpecs as $index => $spec) {
            $product = Product::query()->skip($index)->first();

            if (! $product) {
                continue;
            }

            $quantity = $index + 1;
            $unitPrice = (float) $product->effective_price;
            $subtotal = $unitPrice * $quantity;

            $order = Order::updateOrCreate(['order_number' => $spec['number']], [
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'currency_code' => 'SAR',
                'exchange_rate' => 1,
                'subtotal_base' => $subtotal,
                'discount_base' => 0,
                'shipping_base' => 25,
                'tax_base' => 0,
                'total_base' => $subtotal + 25,
                'subtotal_display' => $subtotal,
                'discount_display' => 0,
                'shipping_display' => 25,
                'tax_display' => 0,
                'total_display' => $subtotal + 25,
                'payment_status' => $spec['payment'],
                'order_status' => $spec['status'],
                'shipping_address_snapshot' => ['city' => 'Riyadh', 'district' => 'Al Olaya', 'street' => 'King Fahd Road'],
                'billing_address_snapshot' => ['city' => 'Riyadh', 'district' => 'Al Olaya', 'street' => 'King Fahd Road'],
                'placed_at' => now()->subDays($spec['days']),
                'created_at' => now()->subDays($spec['days']),
                'updated_at' => now()->subDays($spec['days']),
            ]);

            OrderItem::updateOrCreate(['order_id' => $order->id, 'sku' => $product->sku], [
                'product_id' => $product->id,
                'product_name_ar' => $product->name_ar,
                'product_name_en' => $product->name_en,
                'quantity' => $quantity,
                'unit_price_base' => $unitPrice,
                'unit_price_display' => $unitPrice,
                'subtotal_base' => $subtotal,
                'subtotal_display' => $subtotal,
                'discount_base' => 0,
                'discount_display' => 0,
                'total_base' => $subtotal,
                'total_display' => $subtotal,
                'product_snapshot' => ['sku' => $product->sku, 'name_ar' => $product->name_ar],
            ]);

            Payment::updateOrCreate(['order_id' => $order->id], [
                'payment_number' => 'PAY-DEMO-'.$order->id,
                'gateway' => 'demo',
                'status' => $spec['payment'] === 'paid' ? 'paid' : 'pending',
                'amount_base' => $order->total_base,
                'amount_display' => $order->total_display,
                'currency_code' => 'SAR',
                'exchange_rate' => 1,
                'paid_at' => $spec['payment'] === 'paid' ? now()->subDays($spec['days']) : null,
            ]);
        }
    }
}

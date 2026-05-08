<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class NovaExperienceSeeder extends Seeder
{
    public function run(): void
    {
        Banner::updateOrCreate(
            ['position' => 'hero', 'sort_order' => 1],
            [
                'title_ar' => 'اكتشف عالماً من التقنية الراقية',
                'title_en' => 'Discover premium technology',
                'subtitle_ar' => 'أجهزة مختارة بعناية، عروض واضحة، وسلة شراء مرتبطة بالشحن والدفع والطلبات.',
                'subtitle_en' => 'Curated devices, clear offers, cart, shipping, payments, and orders.',
                'image' => 'nova-signature-hero',
                'link' => '/products?sort=featured',
                'button_text_ar' => 'تسوق الآن',
                'button_text_en' => 'Shop now',
                'is_active' => true,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'NOVA10'],
            [
                'name_ar' => 'خصم ترحيبي 10%',
                'name_en' => 'Welcome 10% off',
                'type' => 'percentage',
                'value' => 10,
                'minimum_order_amount' => 250,
                'maximum_discount_amount' => 150,
                'usage_limit' => 500,
                'usage_limit_per_user' => 1,
                'is_active' => true,
                'target_type' => 'all_store',
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'FREESHIP'],
            [
                'name_ar' => 'شحن مجاني للطلبات الكبيرة',
                'name_en' => 'Free shipping',
                'type' => 'free_shipping',
                'value' => 0,
                'minimum_order_amount' => 500,
                'usage_limit' => 1000,
                'usage_limit_per_user' => 3,
                'is_active' => true,
                'target_type' => 'all_store',
            ]
        );

        $customer = User::query()->where('type', 'customer')->first();

        Product::query()->visible()->take(8)->get()->each(function (Product $product, int $index) use ($customer): void {
            ProductSpecification::updateOrCreate(
                ['product_id' => $product->id, 'name_en' => 'Warranty'],
                [
                    'name_ar' => 'الضمان',
                    'value_ar' => 'سنتان',
                    'value_en' => '2 years',
                    'sort_order' => 1,
                ]
            );

            ProductSpecification::updateOrCreate(
                ['product_id' => $product->id, 'name_en' => 'Delivery'],
                [
                    'name_ar' => 'التوصيل',
                    'value_ar' => 'داخل السعودية',
                    'value_en' => 'Saudi Arabia',
                    'sort_order' => 2,
                ]
            );

            if ($customer) {
                Review::updateOrCreate(
                    ['product_id' => $product->id, 'user_id' => $customer->id],
                    [
                        'rating' => 5 - ($index % 2),
                        'title' => 'تجربة ممتازة',
                        'comment' => 'منتج مرتب وتجربة شراء واضحة من NOVA.',
                        'status' => 'approved',
                        'approved_at' => now()->subDays($index + 1),
                    ]
                );
            }
        });
    }
}

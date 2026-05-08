<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name_ar' => 'شحن سريع',
                'name_en' => 'Express Shipping',
                'code' => 'express',
                'price' => 45,
                'estimated_days_min' => 1,
                'estimated_days_max' => 2,
                'is_active' => true,
                'sort_order' => 1,
                'free_shipping_min_amount' => 750,
            ],
            [
                'name_ar' => 'شحن عادي',
                'name_en' => 'Standard Shipping',
                'code' => 'standard',
                'price' => 25,
                'estimated_days_min' => 3,
                'estimated_days_max' => 5,
                'is_active' => true,
                'sort_order' => 2,
                'free_shipping_min_amount' => 500,
            ],
        ];

        foreach ($methods as $method) {
            ShippingMethod::updateOrCreate(['code' => $method['code']], $method);
        }
    }
}

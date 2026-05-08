<?php

namespace Database\Seeders;

use App\Models\ProductGroup;
use Illuminate\Database\Seeder;

class ProductGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'name_ar' => 'مجموعة الطاقة الشمسية',
                'name_en' => 'Solar Energy',
                'slug' => 'solar-energy',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name_ar' => 'مجموعة الشاشات',
                'name_en' => 'Screens',
                'slug' => 'screens',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name_ar' => 'مجموعة المستلزمات النسائية',
                'name_en' => 'Women Essentials',
                'slug' => 'women-essentials',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name_ar' => 'مجموعة الإلكترونيات',
                'name_en' => 'Electronics',
                'slug' => 'electronics',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($groups as $group) {
            ProductGroup::updateOrCreate(
                ['slug' => $group['slug']],
                $group
            );
        }
    }
}

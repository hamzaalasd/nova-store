<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'name_ar' => 'ريال سعودي',
                'name_en' => 'Saudi Riyal',
                'code' => 'SAR',
                'symbol_ar' => 'ر.س',
                'symbol_en' => 'SAR',
                'exchange_rate' => 1,
                'is_default' => true,
                'is_active' => true,
                'decimal_places' => 2,
                'symbol_position' => 'after',
                'rounding_mode' => 'none',
                'sort_order' => 1,
            ],
            [
                'name_ar' => 'دولار أمريكي',
                'name_en' => 'US Dollar',
                'code' => 'USD',
                'symbol_ar' => '$',
                'symbol_en' => 'USD',
                'exchange_rate' => 3.75,
                'is_default' => false,
                'is_active' => true,
                'decimal_places' => 2,
                'symbol_position' => 'before',
                'rounding_mode' => 'none',
                'sort_order' => 2,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(['code' => $currency['code']], $currency);
        }
    }
}

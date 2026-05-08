<?php

namespace Tests\Feature\API;

use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_currencies_endpoint_returns_active_currencies_for_the_app(): void
    {
        Currency::create([
            'code' => 'SAR',
            'name_ar' => 'ريال سعودي',
            'name_en' => 'Saudi Riyal',
            'symbol_ar' => 'ر.س',
            'symbol_en' => 'SAR',
            'exchange_rate' => 1,
            'is_default' => true,
            'is_active' => true,
            'decimal_places' => 2,
            'symbol_position' => 'after',
            'sort_order' => 1,
        ]);

        Currency::create([
            'code' => 'YER',
            'name_ar' => 'ريال يمني',
            'name_en' => 'Yemeni Rial',
            'symbol_ar' => '﷼',
            'symbol_en' => 'YER',
            'exchange_rate' => 140,
            'is_active' => true,
            'decimal_places' => 2,
            'symbol_position' => 'before',
            'sort_order' => 2,
        ]);

        Currency::create([
            'code' => 'EUR',
            'name_ar' => 'يورو',
            'name_en' => 'Euro',
            'symbol_ar' => '€',
            'symbol_en' => 'EUR',
            'is_active' => false,
        ]);

        $this->getJson('/api/v1/currencies')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['code' => 'YER'])
            ->assertJsonMissing(['code' => 'EUR']);
    }

    public function test_only_one_currency_can_be_default(): void
    {
        $sar = Currency::create([
            'code' => 'SAR',
            'name_ar' => 'ريال سعودي',
            'name_en' => 'Saudi Riyal',
            'symbol_ar' => 'ر.س',
            'symbol_en' => 'SAR',
            'is_default' => true,
        ]);

        $yer = Currency::create([
            'code' => 'YER',
            'name_ar' => 'ريال يمني',
            'name_en' => 'Yemeni Rial',
            'symbol_ar' => '﷼',
            'symbol_en' => 'YER',
            'is_default' => true,
        ]);

        $this->assertFalse($sar->fresh()->is_default);
        $this->assertTrue($yer->fresh()->is_default);
        $this->assertSame(1, Currency::query()->where('is_default', true)->count());
    }
}

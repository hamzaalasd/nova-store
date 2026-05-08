<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'symbol_ar',
        'symbol_en',
        'exchange_rate',
        'is_default',
        'is_active',
        'decimal_places',
        'symbol_position',
        'rounding_mode',
        'sort_order',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'decimal_places' => 'integer',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(function (Currency $currency): void {
            if (! $currency->is_default) {
                return;
            }

            static::query()
                ->whereKeyNot($currency->getKey())
                ->where('is_default', true)
                ->update(['is_default' => false]);
        });
    }
}

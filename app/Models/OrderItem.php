<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price_base' => 'decimal:2',
        'unit_price_display' => 'decimal:2',
        'subtotal_base' => 'decimal:2',
        'subtotal_display' => 'decimal:2',
        'discount_base' => 'decimal:2',
        'discount_display' => 'decimal:2',
        'total_base' => 'decimal:2',
        'total_display' => 'decimal:2',
        'product_snapshot' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

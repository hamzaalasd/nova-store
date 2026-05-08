<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'subtotal_base' => 'decimal:2',
        'discount_base' => 'decimal:2',
        'shipping_base' => 'decimal:2',
        'tax_base' => 'decimal:2',
        'total_base' => 'decimal:2',
        'subtotal_display' => 'decimal:2',
        'discount_display' => 'decimal:2',
        'shipping_display' => 'decimal:2',
        'tax_display' => 'decimal:2',
        'total_display' => 'decimal:2',
        'shipping_address_snapshot' => 'array',
        'billing_address_snapshot' => 'array',
        'placed_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updated(function (Order $order): void {
            if (! $order->wasChanged('order_status')) {
                return;
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status' => $order->getOriginal('order_status'),
                'new_status' => $order->order_status,
                'changed_by' => auth()->id(),
                'note' => 'تم تحديث الحالة من النظام.',
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
}

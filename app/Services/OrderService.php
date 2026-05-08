<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function createFromUserCart(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data): Order {
            $cart = Cart::query()
                ->where('user_id', $user->id)
                ->with(['items.product', 'items.variant'])
                ->lockForUpdate()
                ->first();

            if (! $cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => ['السلة فارغة ولا يمكن إنشاء طلب.'],
                ]);
            }

            $shippingMethod = $this->activeShippingMethod($data['shipping_method_id'] ?? null);
            $paymentMethod = $this->activePaymentMethod($data['payment_method_id'] ?? null);

            $subtotal = $cart->items->sum(fn ($item) => (float) $item->unit_price * $item->quantity);
            $coupon = $this->activeCoupon($data['coupon_code'] ?? null, $subtotal);
            $shipping = $this->shippingAmount($shippingMethod, $subtotal);
            if ($coupon?->type === 'free_shipping') {
                $shipping = 0.0;
            }
            $discount = $this->couponDiscountAmount($coupon, $subtotal);
            $tax = 0.0;
            $total = $subtotal - $discount + $shipping + $tax;

            $order = Order::create([
                'order_number' => $this->nextOrderNumber(),
                'user_id' => $user->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? $user->email,
                'customer_phone' => $data['customer_phone'] ?? $user->phone,
                'currency_code' => $cart->currency_code ?: 'SAR',
                'exchange_rate' => 1,
                'subtotal_base' => $subtotal,
                'discount_base' => $discount,
                'shipping_base' => $shipping,
                'tax_base' => $tax,
                'total_base' => $total,
                'subtotal_display' => $subtotal,
                'discount_display' => $discount,
                'shipping_display' => $shipping,
                'tax_display' => $tax,
                'total_display' => $total,
                'payment_status' => 'initiated',
                'order_status' => 'pending_payment',
                'shipping_method_id' => $shippingMethod?->id,
                'shipping_address_snapshot' => $data['shipping_address'],
                'billing_address_snapshot' => $data['billing_address'] ?? $data['shipping_address'],
                'coupon_code' => $coupon?->code,
                'customer_notes' => $data['customer_notes'] ?? null,
                'placed_at' => now(),
            ]);

            foreach ($cart->items as $cartItem) {
                $product = Product::query()->lockForUpdate()->findOrFail($cartItem->product_id);
                $variant = $cartItem->product_variant_id
                    ? ProductVariant::query()->lockForUpdate()->findOrFail($cartItem->product_variant_id)
                    : null;

                $this->decrementStock($product, $variant, $cartItem->quantity);
                $lineSubtotal = (float) $cartItem->unit_price * $cartItem->quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name_ar' => $product->name_ar,
                    'product_name_en' => $product->name_en,
                    'sku' => $variant?->sku ?? $product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price_base' => $cartItem->unit_price,
                    'unit_price_display' => $cartItem->unit_price,
                    'subtotal_base' => $lineSubtotal,
                    'subtotal_display' => $lineSubtotal,
                    'discount_base' => 0,
                    'discount_display' => 0,
                    'total_base' => $lineSubtotal,
                    'total_display' => $lineSubtotal,
                    'product_snapshot' => [
                        'product_id' => $product->id,
                        'variant_id' => $variant?->id,
                        'sku' => $variant?->sku ?? $product->sku,
                        'name_ar' => $product->name_ar,
                        'name_en' => $product->name_en,
                        'main_image' => $variant?->image ?? $product->main_image,
                    ],
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethod?->id,
                'payment_number' => 'PAY-'.now()->format('YmdHis').'-'.$order->id,
                'gateway' => $paymentMethod?->code,
                'status' => 'pending',
                'amount_base' => $total,
                'amount_display' => $total,
                'currency_code' => $order->currency_code,
                'exchange_rate' => 1,
            ]);

            if ($coupon) {
                $coupon->increment('used_count');
            }

            $cart->items()->delete();

            return $order->fresh(['items', 'payments']);
        });
    }

    private function decrementStock(Product $product, ?ProductVariant $variant, int $quantity): void
    {
        $stockOwner = $variant ?: $product;
        $stockQuantity = $stockOwner->stock_quantity;

        if (($stockOwner->stock_status ?? 'in_stock') === 'out_of_stock') {
            throw ValidationException::withMessages([
                'cart' => ["المنتج {$product->name_ar} غير متوفر."],
            ]);
        }

        if ($stockQuantity !== null && $quantity > $stockQuantity) {
            throw ValidationException::withMessages([
                'cart' => ["المخزون لا يكفي للمنتج {$product->name_ar}."],
            ]);
        }

        if ($stockQuantity !== null && ($variant || $product->manage_stock)) {
            $stockOwner->stock_quantity = $stockQuantity - $quantity;
            $stockOwner->stock_status = $stockOwner->stock_quantity > 0 ? 'in_stock' : 'out_of_stock';
            $stockOwner->save();
        }
    }

    private function activeShippingMethod(?int $id): ?ShippingMethod
    {
        return $id ? ShippingMethod::query()->where('is_active', true)->findOrFail($id) : null;
    }

    private function activePaymentMethod(?int $id): ?PaymentMethod
    {
        return $id ? PaymentMethod::query()->where('is_active', true)->findOrFail($id) : null;
    }

    private function activeCoupon(?string $code, float $subtotal): ?Coupon
    {
        if (! $code) {
            return null;
        }

        $coupon = Coupon::query()
            ->active()
            ->where('code', strtoupper(trim($code)))
            ->first();

        if (! $coupon) {
            throw ValidationException::withMessages([
                'coupon_code' => ['كود الخصم غير صالح أو منتهي.'],
            ]);
        }

        if ($coupon->minimum_order_amount !== null && $subtotal < (float) $coupon->minimum_order_amount) {
            throw ValidationException::withMessages([
                'coupon_code' => ['قيمة السلة أقل من الحد الأدنى لاستخدام هذا الكوبون.'],
            ]);
        }

        if ($coupon->target_type !== 'all_store') {
            throw ValidationException::withMessages([
                'coupon_code' => ['هذا الكوبون مخصص لعناصر محددة وغير مدعوم في هذه السلة حالياً.'],
            ]);
        }

        return $coupon;
    }

    private function couponDiscountAmount(?Coupon $coupon, float $subtotal): float
    {
        if (! $coupon || $coupon->type === 'free_shipping') {
            return 0.0;
        }

        $discount = match ($coupon->type) {
            'percentage' => $subtotal * ((float) $coupon->value / 100),
            'fixed_amount' => (float) $coupon->value,
            default => 0.0,
        };

        if ($coupon->maximum_discount_amount !== null) {
            $discount = min($discount, (float) $coupon->maximum_discount_amount);
        }

        return min($discount, $subtotal);
    }

    private function shippingAmount(?ShippingMethod $shippingMethod, float $subtotal): float
    {
        if (! $shippingMethod) {
            return 0.0;
        }

        if ($shippingMethod->free_shipping_min_amount !== null && $subtotal >= (float) $shippingMethod->free_shipping_min_amount) {
            return 0.0;
        }

        return (float) $shippingMethod->price;
    }

    private function nextOrderNumber(): string
    {
        return 'NOVA-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}

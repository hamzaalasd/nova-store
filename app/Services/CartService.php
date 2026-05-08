<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function resolveCart(?User $user, ?string $sessionId): Cart
    {
        if ($user) {
            return Cart::firstOrCreate(['user_id' => $user->id], ['currency_code' => 'SAR']);
        }

        if (! $sessionId) {
            throw ValidationException::withMessages([
                'cart_session' => ['cart_session أو X-Cart-Session مطلوب لسلة الضيف.'],
            ]);
        }

        return Cart::firstOrCreate(['session_id' => $sessionId], ['currency_code' => 'SAR']);
    }

    public function addItem(?User $user, ?string $sessionId, int $productId, ?int $variantId, int $quantity): Cart
    {
        return DB::transaction(function () use ($user, $sessionId, $productId, $variantId, $quantity): Cart {
            $cart = $this->resolveCart($user, $sessionId);
            $product = Product::visible()->findOrFail($productId);
            $variant = $variantId ? ProductVariant::query()
                ->where('product_id', $product->id)
                ->where('is_active', true)
                ->findOrFail($variantId) : null;

            $this->assertStockAvailable($product, $variant, $quantity);

            $item = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->where('product_variant_id', $variant?->id)
                ->first();

            if ($item) {
                $newQuantity = $item->quantity + $quantity;
                $this->assertStockAvailable($product, $variant, $newQuantity);
                $item->update(['quantity' => $newQuantity]);
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'quantity' => $quantity,
                    'unit_price' => $variant?->effective_price ?? $product->effective_price,
                ]);
            }

            return $this->freshCart($cart);
        });
    }

    public function updateItem(?User $user, ?string $sessionId, CartItem $item, int $quantity): Cart
    {
        $cart = $this->resolveCart($user, $sessionId);

        abort_unless($item->cart_id === $cart->id, 404);

        $this->assertStockAvailable($item->product, $item->variant, $quantity);
        $item->update(['quantity' => $quantity]);

        return $this->freshCart($cart);
    }

    public function removeItem(?User $user, ?string $sessionId, CartItem $item): Cart
    {
        $cart = $this->resolveCart($user, $sessionId);

        abort_unless($item->cart_id === $cart->id, 404);

        $item->delete();

        return $this->freshCart($cart);
    }

    public function mergeSessionCartIntoUser(User $user, ?string $sessionId): void
    {
        if (! $sessionId) {
            return;
        }

        DB::transaction(function () use ($user, $sessionId): void {
            $guestCart = Cart::query()->where('session_id', $sessionId)->with('items')->first();

            if (! $guestCart) {
                return;
            }

            $userCart = $this->resolveCart($user, null);

            foreach ($guestCart->items as $guestItem) {
                $existing = CartItem::query()
                    ->where('cart_id', $userCart->id)
                    ->where('product_id', $guestItem->product_id)
                    ->where('product_variant_id', $guestItem->product_variant_id)
                    ->first();

                if ($existing) {
                    $existing->update(['quantity' => $existing->quantity + $guestItem->quantity]);
                } else {
                    $guestItem->update(['cart_id' => $userCart->id]);
                }
            }

            $guestCart->delete();
        });
    }

    public function freshCart(Cart $cart): Cart
    {
        return $cart->fresh(['items.product.images', 'items.variant']);
    }

    private function assertStockAvailable(Product $product, ?ProductVariant $variant, int $quantity): void
    {
        $stockStatus = $variant?->stock_status ?? $product->stock_status;
        $stockQuantity = $variant?->stock_quantity ?? $product->stock_quantity;

        if ($stockStatus === 'out_of_stock') {
            throw ValidationException::withMessages([
                'product_id' => ['المنتج غير متوفر حالياً.'],
            ]);
        }

        if ($stockQuantity !== null && $quantity > $stockQuantity) {
            throw ValidationException::withMessages([
                'quantity' => ['الكمية المطلوبة أكبر من المخزون المتاح.'],
            ]);
        }
    }
}

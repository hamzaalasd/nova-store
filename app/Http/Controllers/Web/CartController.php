<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function show(Request $request)
    {
        $cart = $this->cartService->resolveCart($request->user(), $request->session()->getId());

        return view('store.cart', [
            'cart' => $this->cartService->freshCart($cart),
        ]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        try {
            $this->cartService->addItem(
                $request->user(),
                $request->session()->getId(),
                (int) $validated['product_id'],
                isset($validated['product_variant_id']) ? (int) $validated['product_variant_id'] : null,
                (int) $validated['quantity']
            );
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        }

        return redirect()->route('cart.show')->with('status', 'تمت إضافة المنتج للسلة.');
    }

    public function update(Request $request, CartItem $item)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $this->cartService->updateItem($request->user(), $request->session()->getId(), $item, (int) $validated['quantity']);

        return back()->with('status', 'تم تحديث السلة.');
    }

    public function destroy(Request $request, CartItem $item)
    {
        $this->cartService->removeItem($request->user(), $request->session()->getId(), $item);

        return back()->with('status', 'تم حذف المنتج من السلة.');
    }
}

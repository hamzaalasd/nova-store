<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\CheckoutRequest;
use App\Models\Coupon;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService,
    ) {
    }

    public function show(Request $request)
    {
        $cart = $this->cartService->resolveCart($request->user(), $request->session()->getId());

        return view('store.checkout', [
            'cart' => $this->cartService->freshCart($cart),
            'shippingMethods' => ShippingMethod::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'paymentMethods' => PaymentMethod::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'activeCoupons' => Coupon::query()->active()->latest()->take(3)->get(),
        ]);
    }

    public function store(CheckoutRequest $request)
    {
        try {
            $order = $this->orderService->createFromUserCart($request->user(), $request->validated());
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        }

        return redirect()->route('orders.show', $order)->with('status', 'تم إنشاء الطلب بنجاح.');
    }
}

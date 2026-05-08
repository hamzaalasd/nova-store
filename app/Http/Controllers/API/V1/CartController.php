<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\API\V1\Cart\AddCartItemRequest;
use App\Http\Requests\API\V1\Cart\UpdateCartItemRequest;
use App\Http\Resources\API\V1\CartResource;
use App\Models\CartItem;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends ApiController
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function show(Request $request): JsonResponse
    {
        $cart = $this->cartService->resolveCart($this->optionalUser(), $this->cartSession($request));

        return $this->success(new CartResource($this->cartService->freshCart($cart)));
    }

    public function add(AddCartItemRequest $request): JsonResponse
    {
        $cart = $this->cartService->addItem(
            $this->optionalUser(),
            $this->cartSession($request),
            $request->integer('product_id'),
            $request->integer('product_variant_id') ?: null,
            $request->integer('quantity')
        );

        return $this->success(new CartResource($cart), 'تمت إضافة المنتج للسلة');
    }

    public function update(UpdateCartItemRequest $request, CartItem $item): JsonResponse
    {
        $cart = $this->cartService->updateItem(
            $this->optionalUser(),
            $this->cartSession($request),
            $item,
            $request->integer('quantity')
        );

        return $this->success(new CartResource($cart), 'تم تحديث السلة');
    }

    public function destroy(Request $request, CartItem $item): JsonResponse
    {
        $cart = $this->cartService->removeItem($this->optionalUser(), $this->cartSession($request), $item);

        return $this->success(new CartResource($cart), 'تم حذف المنتج من السلة');
    }

    private function cartSession(Request $request): ?string
    {
        return $request->input('cart_session') ?: $request->header('X-Cart-Session');
    }

    private function optionalUser(): ?User
    {
        /** @var User|null $user */
        $user = Auth::guard('sanctum')->user();

        return $user;
    }
}

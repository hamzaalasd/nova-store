<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\API\V1\Orders\CreateOrderRequest;
use App\Http\Resources\API\V1\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends ApiController
{
    public function __construct(private readonly OrderService $orderService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(min((int) $request->integer('per_page', 15), 50));

        return $this->paginated($orders, OrderResource::collection($orders));
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createFromUserCart($request->user(), $request->validated());

        return $this->success(new OrderResource($order), 'تم إنشاء الطلب بنجاح', status: 201);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return $this->success(new OrderResource($order->load(['items', 'payments'])));
    }
}

<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $subtotal = $this->relationLoaded('items')
            ? $this->items->sum(fn ($item) => (float) $item->unit_price * $item->quantity)
            : 0;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'session_id' => $this->session_id,
            'currency_code' => $this->currency_code,
            'items' => $this->whenLoaded('items', fn () => CartItemResource::collection($this->items), []),
            'items_count' => $this->relationLoaded('items') ? $this->items->sum('quantity') : 0,
            'subtotal' => number_format($subtotal, 2, '.', ''),
        ];
    }
}

<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'currency_code' => $this->currency_code,
            'subtotal_base' => $this->subtotal_base,
            'discount_base' => $this->discount_base,
            'shipping_base' => $this->shipping_base,
            'tax_base' => $this->tax_base,
            'total_base' => $this->total_base,
            'payment_status' => $this->payment_status,
            'order_status' => $this->order_status,
            'shipping_address_snapshot' => $this->shipping_address_snapshot,
            'billing_address_snapshot' => $this->billing_address_snapshot,
            'customer_notes' => $this->customer_notes,
            'placed_at' => $this->placed_at,
            'items' => $this->whenLoaded('items', fn () => OrderItemResource::collection($this->items), []),
            'payments' => $this->whenLoaded('payments', fn () => PaymentResource::collection($this->payments), []),
        ];
    }
}

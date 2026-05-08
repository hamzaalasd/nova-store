<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_variant_id' => $this->product_variant_id,
            'product_name_ar' => $this->product_name_ar,
            'product_name_en' => $this->product_name_en,
            'sku' => $this->sku,
            'quantity' => $this->quantity,
            'unit_price_base' => $this->unit_price_base,
            'subtotal_base' => $this->subtotal_base,
            'total_base' => $this->total_base,
            'product_snapshot' => $this->product_snapshot,
        ];
    }
}

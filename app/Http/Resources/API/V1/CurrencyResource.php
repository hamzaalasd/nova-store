<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'symbol_ar' => $this->symbol_ar,
            'symbol_en' => $this->symbol_en,
            'exchange_rate' => $this->exchange_rate,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'decimal_places' => $this->decimal_places,
            'symbol_position' => $this->symbol_position,
            'rounding_mode' => $this->rounding_mode,
            'sort_order' => $this->sort_order,
        ];
    }
}

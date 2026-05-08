<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image_path' => $this->image_path,
            'alt_ar' => $this->alt_ar,
            'alt_en' => $this->alt_en,
            'is_main' => $this->is_main,
            'sort_order' => $this->sort_order,
        ];
    }
}

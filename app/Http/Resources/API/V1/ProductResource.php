<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_group_id' => $this->product_group_id,
            'category_id' => $this->category_id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'short_description_ar' => $this->short_description_ar,
            'short_description_en' => $this->short_description_en,
            'description_ar' => $this->when($request->routeIs('*.products.show'), $this->description_ar),
            'description_en' => $this->when($request->routeIs('*.products.show'), $this->description_en),
            'base_price' => $this->base_price,
            'sale_price' => $this->sale_price,
            'effective_price' => $this->effective_price,
            'stock_quantity' => $this->stock_quantity,
            'stock_status' => $this->stock_status,
            'is_featured' => $this->is_featured,
            'has_variants' => $this->has_variants,
            'main_image' => $this->main_image,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}

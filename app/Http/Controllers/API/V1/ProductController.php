<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\API\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->integer('per_page', 15), 50);

        $query = Product::query()
            ->visible()
            ->with(['category', 'images'])
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->filled('featured'), fn ($query) => $query->where('is_featured', $request->boolean('featured')))
            ->when($request->filled('min_price'), fn ($query) => $query->where('base_price', '>=', $request->input('min_price')))
            ->when($request->filled('max_price'), fn ($query) => $query->where('base_price', '<=', $request->input('max_price')))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = trim((string) $request->input('search'));
                $query->where(function ($query) use ($search): void {
                    $query->where('name_ar', 'like', "%{$search}%")
                        ->orWhere('name_en', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('short_description_ar', 'like', "%{$search}%")
                        ->orWhere('short_description_en', 'like', "%{$search}%");
                });
            });

        match ($request->input('sort', 'newest')) {
            'price_asc' => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            'featured' => $query->orderByDesc('is_featured')->latest(),
            default => $query->latest(),
        };

        $products = $query->paginate($perPage);

        return $this->paginated($products, ProductResource::collection($products));
    }

    public function show(Product $product): JsonResponse
    {
        abort_unless($product->status === 'active', 404);

        $product->load(['category', 'images', 'variants' => fn ($query) => $query->where('is_active', true)]);

        return $this->success(new ProductResource($product));
    }
}

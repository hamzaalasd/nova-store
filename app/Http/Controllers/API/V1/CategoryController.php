<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\API\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return $this->success(CategoryResource::collection($categories));
    }

    public function products(Category $category, Request $request, ProductController $products): JsonResponse
    {
        $request->merge(['category_id' => $category->id]);

        return $products->index($request);
    }
}

<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\API\V1\HomeBannerResource;
use App\Models\HomeBanner;
use Illuminate\Http\JsonResponse;

class HomeBannerController extends ApiController
{
    public function index(): JsonResponse
    {
        $banners = HomeBanner::query()
            ->visible()
            ->orderBy('sort_order')
            ->latest()
            ->get();

        return $this->success(HomeBannerResource::collection($banners));
    }
}

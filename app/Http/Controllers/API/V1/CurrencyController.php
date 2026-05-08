<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\API\V1\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;

class CurrencyController extends ApiController
{
    public function index(): JsonResponse
    {
        $currencies = Currency::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        return $this->success(CurrencyResource::collection($currencies));
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductAnalyticsEventRequest;
use App\Services\Analytics\ProductAnalyticsEventService;
use Illuminate\Http\JsonResponse;

class ProductAnalyticsEventController extends Controller
{
    public function __construct(
        private readonly ProductAnalyticsEventService $events,
    ) {}

    public function store(StoreProductAnalyticsEventRequest $request): JsonResponse
    {
        $event = $this->events->store($request->validated());

        return response()->json([
            'id' => $event->id,
            'status' => 'accepted',
        ], 201);
    }
}

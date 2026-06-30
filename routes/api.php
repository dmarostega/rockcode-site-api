<?php

use App\Http\Controllers\Api\ProductAnalyticsEventController;
use Illuminate\Support\Facades\Route;

Route::get('/messages', function () {
    return response()->json([
        ['id' => 1, 'text' => 'Bem vindo a rockcode labs.'],
        ['id' => 2, 'text' => 'API Laravel Conectada com sucesso.'],
    ]);
});

Route::post('/analytics/events', [ProductAnalyticsEventController::class, 'store'])
    ->middleware('throttle:analytics-events');

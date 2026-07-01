<?php

use App\Http\Controllers\Admin\ProductAnalyticsDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/admin', ProductAnalyticsDashboardController::class)
    ->middleware('admin.basic')
    ->name('admin.analytics');

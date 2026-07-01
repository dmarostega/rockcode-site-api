<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Analytics\ProductAnalyticsDashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductAnalyticsDashboardController extends Controller
{
    private const ALLOWED_PERIODS = [7, 30, 90];

    public function __invoke(Request $request, ProductAnalyticsDashboardService $dashboard): Response
    {
        $periodDays = (int) $request->integer('period', 30);

        if (! in_array($periodDays, self::ALLOWED_PERIODS, true)) {
            $periodDays = 30;
        }

        return response()->view('admin.analytics-dashboard', [
            'summary' => $dashboard->summarize($periodDays),
            'periods' => self::ALLOWED_PERIODS,
        ])->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'X-Robots-Tag' => 'noindex, nofollow, noarchive',
        ]);
    }
}

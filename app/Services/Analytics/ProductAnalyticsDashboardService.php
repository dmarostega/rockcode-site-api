<?php

namespace App\Services\Analytics;

use App\Models\ProductAnalyticsEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductAnalyticsDashboardService
{
    private const TOP_LIMIT = 5;

    /**
     * @return array{
     *     periodDays: int,
     *     totalEvents: int,
     *     eventsByDay: Collection<int, object>,
     *     topPages: Collection<int, object>,
     *     topTools: Collection<int, object>,
     *     topCtas: Collection<int, object>,
     *     topProjects: Collection<int, object>,
     *     hasEvents: bool
     * }
     */
    public function summarize(int $periodDays): array
    {
        $periodStart = now()->subDays($periodDays - 1)->startOfDay();
        $baseQuery = ProductAnalyticsEvent::query()
            ->where('occurred_at', '>=', $periodStart);

        $totalEvents = (clone $baseQuery)->count();

        return [
            'periodDays' => $periodDays,
            'totalEvents' => $totalEvents,
            'eventsByDay' => $this->eventsByDay($periodStart),
            'topPages' => $this->topPages($periodStart),
            'topTools' => $this->topByColumn($periodStart, 'feature', ['tool_card_clicked', 'tool_opened']),
            'topCtas' => $this->topByColumn($periodStart, 'destination', ['cta_clicked']),
            'topProjects' => $this->topByColumn($periodStart, 'destination', ['project_card_clicked']),
            'hasEvents' => $totalEvents > 0,
        ];
    }

    /**
     * @return Collection<int, object>
     */
    private function eventsByDay(Carbon $periodStart): Collection
    {
        return ProductAnalyticsEvent::query()
            ->selectRaw('DATE(occurred_at) as label, COUNT(*) as total')
            ->where('occurred_at', '>=', $periodStart)
            ->groupBy(DB::raw('DATE(occurred_at)'))
            ->orderBy('label')
            ->get();
    }

    /**
     * @return Collection<int, object>
     */
    private function topPages(Carbon $periodStart): Collection
    {
        return ProductAnalyticsEvent::query()
            ->selectRaw('page_path as label, COUNT(*) as total')
            ->where('occurred_at', '>=', $periodStart)
            ->whereNotNull('page_path')
            ->where('page_path', '!=', '')
            ->groupBy('page_path')
            ->orderByDesc('total')
            ->orderBy('label')
            ->limit(self::TOP_LIMIT)
            ->get();
    }

    /**
     * @param  list<string>  $eventNames
     * @return Collection<int, object>
     */
    private function topByColumn(Carbon $periodStart, string $column, array $eventNames): Collection
    {
        return ProductAnalyticsEvent::query()
            ->selectRaw($column.' as label, COUNT(*) as total')
            ->where('occurred_at', '>=', $periodStart)
            ->whereIn('event_name', $eventNames)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->groupBy($column)
            ->orderByDesc('total')
            ->orderBy('label')
            ->limit(self::TOP_LIMIT)
            ->get();
    }
}

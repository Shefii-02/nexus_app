<?php
namespace App\Services\Dashboard;

use App\Repositories\Dashboard\DashboardRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function __construct(protected DashboardRepository $repository)
    {
    }

    /**
     * Everything the dashboard page needs, in one payload.
     */
    public function getDashboardSummary(): array
    {
        return Cache::remember('admin:dashboard:status', now()->addMinutes(3), function () {
            return [
                'stats' => $this->getStats(),
                'enrollments_chart' => $this->getEnrollmentsChart('30d'),
                'revenue_chart' => $this->getRevenueBreakdown('30d'),
                'top_courses' => $this->getTopCourses(5),
                'notifications' => $this->getRecentNotifications(8),
            ];
        });
    }

    protected function getStats(): array
    {
        $current = $this->repository->getCoreCounts();
        $previous = $this->repository->getCoreCounts(
            Carbon::now()->subDays(60),
            Carbon::now()->subDays(30)
        );

        return [
            'total_courses' => [
                'value' => $current['courses'],
                'growth' => $this->growthPercent($previous['courses'], $current['courses']),
                'trend' => $this->repository->getDailyTrend('courses', 'created_at', 7),
            ],
            'total_students' => [
                'value' => $current['students'],
                'growth' => $this->growthPercent($previous['students'], $current['students']),
                'trend' => $this->repository->getDailyTrend('users', 'created_at', 7, null, ['acc_type' => 'student']),
            ],
            'enrollments' => [
                'value' => $current['enrollments'],
                'growth' => $this->growthPercent($previous['enrollments'], $current['enrollments']),
                'trend' => $this->repository->getDailyTrend('enrollments', 'created_at', 7),
            ],
            'revenue' => [
                'value' => $current['revenue'],
                'formatted' => '₹' . number_format($current['revenue']),
                'growth' => $this->growthPercent($previous['revenue'], $current['revenue']),
                'trend' => $this->repository->getDailyTrend('payments', 'created_at', 7, 'amount', ['status' => 'success']),
            ],
        ];
    }

    protected function getEnrollmentsChart(string $range): array
    {
        [$start, $groupBy] = $this->resolveRange($range);
        $rows = $this->repository->getEnrollmentsSeries($start, $groupBy);

        return [
            'range' => $range,
            'labels' => $rows->pluck('label'),
            'values' => $rows->pluck('total'),
        ];
    }

    protected function getRevenueBreakdown(string $range): array
    {
        [$start] = $this->resolveRange($range);
        $rows = $this->repository->getRevenueByCategory($start);

        return [
            'range' => $range,
            'labels' => $rows->pluck('category'),
            'values' => $rows->pluck('total'),
        ];
    }

    protected function getTopCourses(int $limit): array
    {
        return $this->repository->getTopSellingCourses($limit)
            ->map(fn ($course) => [
                'id' => $course->id,
                'name' => $course->name,
                'price' => '₹' . number_format($course->price),
                'sales_count' => $course->enrollments_count,
            ])
            ->toArray();
    }

    protected function getRecentNotifications(int $limit): array
    {
        return $this->repository->getRecentActivity($limit)
            ->map(fn ($item) => [
                'id' => $item->id,
                'message' => $item->message,
                'type' => $item->type,
                'created_at' => $item->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    protected function growthPercent(int|float $previous, int|float $current): string
    {
        if ($previous <= 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $percent = (($current - $previous) / $previous) * 100;
        $sign = $percent >= 0 ? '+' : '';

        return $sign . number_format($percent, 1) . '%';
    }

    protected function resolveRange(string $range): array
    {
        return match ($range) {
            '7d' => [Carbon::now()->subDays(7), 'day'],
            '90d' => [Carbon::now()->subDays(90), 'week'],
            '12m' => [Carbon::now()->subMonths(12), 'month'],
            default => [Carbon::now()->subDays(30), 'day'],
        };
    }
}

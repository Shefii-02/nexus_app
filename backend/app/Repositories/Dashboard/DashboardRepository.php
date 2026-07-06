<?php

namespace App\Repositories\Dashboard;

use App\Models\Admission;
use App\Models\AdmissionPayment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function getCoreCounts(?Carbon $from = null, ?Carbon $to = null): array
    {
        $coursesQuery = Course::query();
        $studentsQuery = User::query()->where('acc_type', 'student');
        $admissionsQuery = Admission::query();
        $revenueQuery = AdmissionPayment::query();
        // ->where('status', 'success');

        if ($from && $to) {
            $coursesQuery->whereBetween('created_at', [$from, $to]);
            $studentsQuery->whereBetween('created_at', [$from, $to]);
            $admissionsQuery->whereBetween('created_at', [$from, $to]);
            $revenueQuery->whereBetween('created_at', [$from, $to]);
        }

        return [
            'courses' => $coursesQuery->count(),
            'students' => $studentsQuery->count(),
            'admissions' => $admissionsQuery->count(),
            'revenue' => (float) $revenueQuery->sum('amount'),
        ];
    }

    /**
     * Daily counts (or sums) for the last N days — powers the stat card sparklines.
     * Always returns exactly $days values, zero-filled for days with no rows.
     */
    public function getDailyTrend(
        string $table,
        string $dateColumn,
        int $days,
        ?string $sumColumn = null,
        array $wheres = []
    ): array {
        $start = Carbon::now()->subDays($days - 1)->startOfDay();

        $query = DB::table($table)
            ->selectRaw("DATE($dateColumn) as day, " . ($sumColumn ? "SUM($sumColumn) as total" : 'COUNT(*) as total'))
            ->where($dateColumn, '>=', $start);

        foreach ($wheres as $column => $value) {
            $query->where($column, $value);
        }

        $rows = $query->groupBy('day')->orderBy('day')->pluck('total', 'day');

        $trend = [];
        for ($i = 0; $i < $days; $i++) {
            $day = Carbon::now()->subDays($days - 1 - $i)->toDateString();
            $trend[] = (float) ($rows[$day] ?? 0);
        }

        return $trend;
    }

    public function getadmissionsSeries(Carbon $start, string $groupBy): Collection
    {
        $format = match ($groupBy) {
            'week' => '%x-W%v',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        return Admission::query()
            ->selectRaw('DATE_FORMAT(created_at, ?) as label, COUNT(*) as total', [$format])
            ->where('created_at', '>=', $start)
            ->groupBy('label')
            ->orderBy('label')
            ->get();
    }

    public function getRevenueByCategory(Carbon $start): Collection
    {
        return AdmissionPayment::query()
            ->join('courses', 'admission_payments.course_id', '=', 'courses.id')
            ->selectRaw('admission_payments.type as category, SUM(paymeadmission_paymentsnts.amount) as total')
            ->whereNotNull('admission_payments.paid_at')
            ->where('admission_payments.created_at', '>=', $start)
            ->groupBy('admission_payments.type')
            ->orderByDesc('amount')
            ->get();
    }

    public function getTopSellingCourses(int $limit): Collection
    {
        return Course::query()
            ->withCount('admissions')
            ->orderByDesc('admissions_count')
            ->limit($limit)
            ->get();
    }

    public function getRecentActivity(int $limit): Collection
    {
        // Swap this for your actual activity/notifications source.
        return DB::table('activity_logs')
            ->select(
                'id',
                'description as message',
                'module as type',
                'action',
                'created_at'
            )
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $item->created_at = Carbon::parse($item->created_at);
                return $item;
            });

    }
}

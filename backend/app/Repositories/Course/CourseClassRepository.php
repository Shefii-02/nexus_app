<?php

namespace App\Repositories\Course;

use App\Models\CourseClass;
use App\Repositories\BaseRepository;
use Carbon\Carbon;

class CourseClassRepository extends BaseRepository implements CourseClassRepositoryInterface
{
    public function __construct(CourseClass $model)
    {
        parent::__construct($model);
    }

    public function getByCourse(int $courseId, array $filters = [])
    {
        $query = $this->model
            ->where('course_id', $courseId)
            ->whereNull('deleted_at')
            ->with(['teacher.user'])
            ->orderBy('scheduled_date');

        return $this->applyFilters($query, $filters)->paginate(50);
    }

    public function getToday(int $userId, string $accType)
    {
        $now        = Carbon::now();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd   = $now->copy()->endOfDay();

        $query = $this->model
            ->with(['course', 'teacher.user'])
            ->whereNull('deleted_at')
            ->where(function ($q) use ($todayStart, $todayEnd) {
                $q->whereBetween('started_at', [$todayStart, $todayEnd])
                  ->orWhereBetween('ended_at', [$todayStart, $todayEnd])
                  ->orWhere(function ($q2) use ($todayStart, $todayEnd) {
                      $q2->where('started_at', '<=', $todayStart)
                         ->where('ended_at', '>=', $todayEnd);
                  });
            });

        if ($accType === 'teacher') {
            $query->where('teacher_id', $userId);
        } else {
            $query->whereHas('course.students', function ($q) use ($userId) {
                $q->where('student_id', $userId);
            });
        }

        return $query->orderBy('started_at')->get();
    }

    protected function applyFilters($query, array $filters)
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}

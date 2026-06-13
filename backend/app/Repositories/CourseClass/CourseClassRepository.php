<?php

namespace App\Repositories\CourseClass;

use App\Models\CourseClass;
use App\Repositories\BaseRepository;

class CourseClassRepository extends BaseRepository implements CourseClassRepositoryInterface
{
    public function __construct(CourseClass $courseClass)
    {
        parent::__construct($courseClass);
    }

    public function getByCourse(int $courseId)
    {
        return $this->model->where('course_id', $courseId)
            ->with(['course', 'teacher.user', 'materials'])
            ->orderBy('scheduled_date')
            ->paginate(15);
    }

    public function getByTeacher(int $teacherId)
    {
        return $this->model->where('teacher_id', $teacherId)
            ->with(['course', 'teacher.user'])
            ->orderBy('scheduled_date')
            ->paginate(15);
    }

    public function getUpcoming()
    {
        return $this->model->upcoming()
            ->with(['course', 'teacher.user'])
            ->paginate(15);
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->model->whereBetween('scheduled_date', [$startDate, $endDate])
            ->with(['course', 'teacher.user'])
            ->orderBy('scheduled_date')
            ->paginate(15);
    }

    protected function applyFilters($query, array $filters)
    {

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if (!empty($filters['record_link'])) {
            if ($filters['record_link'] == 'added') {
                $query->whereNull('record_link');
            } else if ($filters['record_link'] == 'not') {
                $query->whereNotNull('record_link');
            }
        }


        if (!empty($filters['class_no'])) {
            $query->where('class_no', $filters['class_no']);
        }



        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (!empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('scheduled_date', [
                $filters['start_date'],
                $filters['end_date']
            ]);
        }

        return $query;
    }
}

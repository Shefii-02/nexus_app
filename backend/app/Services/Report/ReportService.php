<?php

namespace App\Services\Report;

use App\Models\Admission;
use App\Models\AdmissionRenewal;
use App\Models\CouponUsage;
use App\Models\StaffAttendance;
use App\Models\StaffPayment;
use App\Models\StudentAttendance;
use App\Models\TeacherAttendance;
use App\Models\TeacherPayment;
use App\Models\Transaction;

class ReportService
{
    public function revenue(
        array $filters = []
    ): array {

        $query = Transaction::query()
            ->where('type', 'income');

        if (!empty($filters['from'])) {
            $query->whereDate(
                'transaction_date',
                '>=',
                $filters['from']
            );
        }

        if (!empty($filters['to'])) {
            $query->whereDate(
                'transaction_date',
                '<=',
                $filters['to']
            );
        }

        return [
            'total_revenue' =>
            $query->sum('amount'),

            'transactions' =>
            $query->count(),
        ];
    }

    public function profit(
        array $filters = []
    ): array {

        $income =
            Transaction::query()
            ->where('type', 'income')
            ->sum('amount');

        $expense =
            Transaction::query()
            ->where('type', 'expense')
            ->sum('amount');

        return [

            'income' =>
            $income,

            'expense' =>
            $expense,

            'profit' =>
            $income - $expense,
        ];
    }

    public function teacherEarnings(
        array $filters = []
    ): array {

        $query =
            TeacherPayment::query();

        if (!empty($filters['teacher_id'])) {

            $query->where(
                'teacher_id',
                $filters['teacher_id']
            );
        }

        return [

            'total_paid' =>
            $query->sum('amount'),

            'records' =>
            $query->count(),
        ];
    }

    public function staffSalary(
        array $filters = []
    ): array {

        $query =
            StaffPayment::query();

        if (!empty($filters['staff_id'])) {

            $query->where(
                'staff_id',
                $filters['staff_id']
            );
        }

        return [

            'total_salary' =>
            $query->sum('final_amount'),

            'records' =>
            $query->count(),
        ];
    }

    public function couponUsage(
        array $filters = []
    ): array {

        $query =
            CouponUsage::query();

        return [

            'total_uses' =>
            $query->count(),

            'total_discount' =>
            $query->sum(
                'discount_amount'
            ),
        ];
    }

    public function admissions(
        array $filters = []
    ): array {

        $query =
            Admission::query();

        if (!empty($filters['course_id'])) {

            $query->where(
                'course_id',
                $filters['course_id']
            );
        }

        return [

            'total_admissions' =>
            $query->count(),

            'total_amount' =>
            $query->sum(
                'final_amount'
            ),
        ];
    }

    public function renewals(
        array $filters = []
    ): array {

        $query =
            AdmissionRenewal::query();

        return [

            'total_renewals' =>
            $query->count(),

            'total_amount' =>
            $query->sum(
                'amount'
            ),
        ];
    }

    public function monthlySummary(
        array $filters = []
    ): array {

        $month =
            $filters['month']
            ?? now()->month;

        $year =
            $filters['year']
            ?? now()->year;

        $income =
            Transaction::query()
            ->whereYear(
                'transaction_date',
                $year
            )
            ->whereMonth(
                'transaction_date',
                $month
            )
            ->where(
                'type',
                'income'
            )
            ->sum('amount');

        $expense =
            Transaction::query()
            ->whereYear(
                'transaction_date',
                $year
            )
            ->whereMonth(
                'transaction_date',
                $month
            )
            ->where(
                'type',
                'expense'
            )
            ->sum('amount');

        return [

            'month' => $month,

            'year' => $year,

            'income' =>
            $income,

            'expense' =>
            $expense,

            'profit' =>
            $income - $expense,

            'admissions' =>
            Admission::query()
                ->whereYear(
                    'created_at',
                    $year
                )
                ->whereMonth(
                    'created_at',
                    $month
                )
                ->count(),

            'renewals' =>
            AdmissionRenewal::query()
                ->whereYear(
                    'created_at',
                    $year
                )
                ->whereMonth(
                    'created_at',
                    $month
                )
                ->count(),
        ];
    }

    public function studentAttendance(array $filters = [])
    {
        return StudentAttendance::query()

            ->selectRaw("
            course_id,
            COUNT(*) total,
            SUM(status='present') present_count,
            SUM(status='absent') absent_count
        ")

            ->groupBy('course_id')

            ->with('course')

            ->get();
    }

    public function teacherAttendance(array $filters = [])
    {
        return TeacherAttendance::query()

            ->selectRaw("
            teacher_id,
            COUNT(*) total,
            SUM(status='present') present_count,
            SUM(status='absent') absent_count
        ")

            ->groupBy('teacher_id')

            ->with('teacher')

            ->get();
    }

    public function staffAttendance(array $filters = [])
    {
        return StaffAttendance::query()

            ->selectRaw("
            staff_id,
            COUNT(*) total,
            SUM(status='present') present_count,
            SUM(status='absent') absent_count
        ")

            ->groupBy('staff_id')

            ->with('staff')

            ->get();
    }

    public function courseRevenue(array $filters = [])
    {
        return Admission::query()

            ->selectRaw("
            course_id,
            COUNT(*) admissions,
            SUM(final_amount) revenue
        ")

            ->groupBy('course_id')

            ->with('course')

            ->get();
    }

    public function courseProfit(array $filters = [])
    {
        return Admission::query()

            ->selectRaw("
            course_id,
            SUM(final_amount) revenue
        ")

            ->groupBy('course_id')

            ->with('course')

            ->get()

            ->map(function ($row) {

                $teacherExpense =
                    TeacherPaymentItem::where(
                        'course_id',
                        $row->course_id
                    )->sum('amount');

                return [

                    'course_id' =>
                    $row->course_id,

                    'course_name' =>
                    $row->course?->title,

                    'revenue' =>
                    $row->revenue,

                    'expense' =>
                    $teacherExpense,

                    'profit' =>
                    $row->revenue - $teacherExpense
                ];
            });
    }

    public function teacherWiseRevenue(array $filters = [])
    {
        return TeacherPaymentItem::query()

            ->selectRaw("
            teacher_id,
            SUM(amount) earnings
        ")

            ->groupBy('teacher_id')

            ->with('teacher')

            ->get();
    }


    public function batchWiseRevenue(array $filters = [])
    {
        return Admission::query()

            ->selectRaw("
            batch_id,
            SUM(final_amount) revenue
        ")

            ->groupBy('batch_id')

            ->with('batch')

            ->get();
    }


    public function yearlyRevenue(array $filters = [])
    {
        return Transaction::query()

            ->where('type', 'income')

            ->selectRaw("
            YEAR(transaction_date) year,
            MONTH(transaction_date) month,
            SUM(amount) revenue
        ")

            ->groupByRaw("
            YEAR(transaction_date),
            MONTH(transaction_date)
        ")

            ->orderByRaw("
            YEAR(transaction_date),
            MONTH(transaction_date)
        ")

            ->get();
    }


    public function tax(array $filters = [])
    {
        $revenue =
            Transaction::where(
                'type',
                'income'
            )->sum('amount');

        $taxRate = 18;

        return [

            'revenue' =>
            $revenue,

            'tax_rate' =>
            $taxRate,

            'tax_amount' => ($revenue * $taxRate) / 100
        ];
    }

    public function outstandingRenewals(array $filters = [])
    {
        return AdmissionRenewal::query()

            ->where(
                'status',
                'pending'
            )

            ->with([
                'student',
                'course'
            ])

            ->get();
    }

    public function pendingTeacherPayments(array $filters = [])
    {
        return TeacherPaymentItem::query()

            ->where(
                'status',
                'pending'
            )

            ->with([
                'teacher',
                'course'
            ])

            ->get();
    }


    public function pendingStaffPayments(array $filters = [])
    {
        return StaffPayment::query()

            ->where(
                'status',
                'pending'
            )

            ->with('staff')

            ->get();
    }

    public function monthlyAttendance(
        array $filters = []
    ) {
        $year =
            $filters['year']
            ?? now()->year;

        return StudentAttendance::query()

            ->selectRaw("
            MONTH(attendance_date) month,
            COUNT(*) total,
            SUM(status='present') presents,
            SUM(status='absent') absents
        ")

            ->whereYear(
                'attendance_date',
                $year
            )

            ->groupByRaw(
                'MONTH(attendance_date)'
            )

            ->orderByRaw(
                'MONTH(attendance_date)'
            )

            ->get();
    }

    public function teacherWorkingDays(
        array $filters = []
    ) {
        return TeacherAttendance::query()

            ->selectRaw("
            teacher_id,
            COUNT(*) total_days,
            SUM(status='present') working_days
        ")

            ->groupBy('teacher_id')

            ->with('teacher')

            ->get();
    }

    public function staffWorkingDays(
        array $filters = []
    ) {
        return StaffAttendance::query()

            ->selectRaw("
            staff_id,
            COUNT(*) total_days,
            SUM(status='present') working_days
        ")

            ->groupBy('staff_id')

            ->with('staff')

            ->get();
    }

    public function studentPercentage(
        array $filters = []
    ) {
        return StudentAttendance::query()

            ->selectRaw("
            student_id,
            COUNT(*) total_classes,
            SUM(status='present') present_classes
        ")

            ->groupBy('student_id')

            ->with('student')

            ->get()

            ->map(function ($row) {

                $percentage = 0;

                if (
                    $row->total_classes > 0
                ) {
                    $percentage =
                        round(
                            (
                                $row->present_classes
                                /
                                $row->total_classes
                            ) * 100,
                            2
                        );
                }

                return [

                    'student_id' =>
                    $row->student_id,

                    'student_name' =>
                    $row->student?->name,

                    'total_classes' =>
                    $row->total_classes,

                    'present_classes' =>
                    $row->present_classes,

                    'attendance_percentage' =>
                    $percentage,
                ];
            });
    }

    public function lowAttendanceStudents(
        array $filters = []
    ) {
        $limit =
            $filters['percentage']
            ?? 75;

        return StudentAttendance::query()

            ->selectRaw("
            student_id,
            COUNT(*) total_classes,
            SUM(status='present') present_classes
        ")

            ->groupBy('student_id')

            ->with('student')

            ->get()

            ->filter(function ($row) use ($limit) {

                $percentage =
                    ($row->present_classes /
                        max($row->total_classes, 1))
                    * 100;

                return $percentage < $limit;
            })

            ->values();
    }

    public function todayAbsentStudents()
    {
        return StudentAttendance::query()

            ->whereDate(
                'attendance_date',
                today()
            )

            ->where(
                'status',
                'absent'
            )

            ->with([
                'student',
                'course',
                'batch'
            ])

            ->get();
    }

    public function todayAbsentTeachers()
    {
        return TeacherAttendance::query()

            ->whereDate(
                'attendance_date',
                today()
            )

            ->where(
                'status',
                'absent'
            )

            ->with('teacher')

            ->get();
    }

    public function todayAbsentStaff()
    {
        return StaffAttendance::query()

            ->whereDate(
                'attendance_date',
                today()
            )

            ->where(
                'status',
                'absent'
            )

            ->with('staff')

            ->get();
    }

    public function courseAttendance(
        array $filters = []
    ) {
        return StudentAttendance::query()

            ->selectRaw("
            course_id,
            COUNT(*) total,
            SUM(status='present') presents,
            SUM(status='absent') absents
        ")

            ->groupBy('course_id')

            ->with('course')

            ->get();
    }

    public function batchAttendance(
        array $filters = []
    ) {
        return StudentAttendance::query()

            ->selectRaw("
            batch_id,
            COUNT(*) total,
            SUM(status='present') presents,
            SUM(status='absent') absents
        ")

            ->groupBy('batch_id')

            ->with('batch')

            ->get();
    }


    public function attendanceSummary(
        array $filters = []
    ) {
        $today = today();

        return [

            'students_present' =>
            StudentAttendance::whereDate(
                'attendance_date',
                $today
            )
                ->where(
                    'status',
                    'present'
                )
                ->count(),

            'students_absent' =>
            StudentAttendance::whereDate(
                'attendance_date',
                $today
            )
                ->where(
                    'status',
                    'absent'
                )
                ->count(),

            'teachers_present' =>
            TeacherAttendance::whereDate(
                'attendance_date',
                $today
            )
                ->where(
                    'status',
                    'present'
                )
                ->count(),

            'teachers_absent' =>
            TeacherAttendance::whereDate(
                'attendance_date',
                $today
            )
                ->where(
                    'status',
                    'absent'
                )
                ->count(),

            'staff_present' =>
            StaffAttendance::whereDate(
                'attendance_date',
                $today
            )
                ->where(
                    'status',
                    'present'
                )
                ->count(),

            'staff_absent' =>
            StaffAttendance::whereDate(
                'attendance_date',
                $today
            )
                ->where(
                    'status',
                    'absent'
                )
                ->count(),
        ];
    }


    public function studentAttendanceHistory(
    int $studentId
)
{
    return StudentAttendance::query()

        ->where(
            'student_id',
            $studentId
        )

        ->with([
            'student',
            'course',
            'batch'
        ])

        ->latest(
            'attendance_date'
        )

        ->paginate(50);
}

public function teacherAttendanceHistory(
    int $teacherId
)
{
    return TeacherAttendance::query()

        ->where(
            'teacher_id',
            $teacherId
        )

        ->with('teacher')

        ->latest(
            'attendance_date'
        )

        ->paginate(50);
}

public function staffAttendanceHistory(
    int $staffId
)
{
    return StaffAttendance::query()

        ->where(
            'staff_id',
            $staffId
        )

        ->with('staff')

        ->latest(
            'attendance_date'
        )

        ->paginate(50);
}

public function courseWiseAbsentees()
{
    return StudentAttendance::query()

        ->selectRaw("
            course_id,
            COUNT(*) absent_count
        ")

        ->whereDate(
            'attendance_date',
            today()
        )

        ->where(
            'status',
            'absent'
        )

        ->groupBy(
            'course_id'
        )

        ->with('course')

        ->get();
}

public function batchWiseAbsentees()
{
    return StudentAttendance::query()

        ->selectRaw("
            batch_id,
            COUNT(*) absent_count
        ")

        ->whereDate(
            'attendance_date',
            today()
        )

        ->where(
            'status',
            'absent'
        )

        ->groupBy(
            'batch_id'
        )

        ->with('batch')

        ->get();
}

public function monthlyAttendanceTrend()
{
    return StudentAttendance::query()

        ->selectRaw("
            DATE(attendance_date) day,
            SUM(status='present') present_count,
            SUM(status='absent') absent_count
        ")

        ->whereMonth(
            'attendance_date',
            now()->month
        )

        ->whereYear(
            'attendance_date',
            now()->year
        )

        ->groupByRaw(
            'DATE(attendance_date)'
        )

        ->orderBy(
            'day'
        )

        ->get();
}

public function yearlyAttendanceTrend()
{
    return StudentAttendance::query()

        ->selectRaw("
            MONTH(attendance_date) month,
            SUM(status='present') present_count,
            SUM(status='absent') absent_count
        ")

        ->whereYear(
            'attendance_date',
            now()->year
        )

        ->groupByRaw(
            'MONTH(attendance_date)'
        )

        ->orderByRaw(
            'MONTH(attendance_date)'
        )

        ->get();
}
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ApiResponse;

    public function __construct(
        private ReportService $service
    ) {}

    public function revenue(
        Request $request
    ): JsonResponse {

        return $this->successResponse(
            $this->service->revenue(
                $request->all()
            ),
            'Revenue report generated successfully'
        );
    }

    public function profit(
        Request $request
    ): JsonResponse {

        return $this->successResponse(
            $this->service->profit(
                $request->all()
            ),
            'Profit report generated successfully'
        );
    }

    public function teacherEarnings(
        Request $request
    ): JsonResponse {

        return $this->successResponse(
            $this->service->teacherEarnings(
                $request->all()
            ),
            'Teacher earnings report generated successfully'
        );
    }

    public function staffSalary(
        Request $request
    ): JsonResponse {

        return $this->successResponse(
            $this->service->staffSalary(
                $request->all()
            ),
            'Staff salary report generated successfully'
        );
    }

    public function couponUsage(
        Request $request
    ): JsonResponse {

        return $this->successResponse(
            $this->service->couponUsage(
                $request->all()
            ),
            'Coupon usage report generated successfully'
        );
    }

    public function admissions(
        Request $request
    ): JsonResponse {

        return $this->successResponse(
            $this->service->admissions(
                $request->all()
            ),
            'Admission report generated successfully'
        );
    }

    public function renewals(
        Request $request
    ): JsonResponse {

        return $this->successResponse(
            $this->service->renewals(
                $request->all()
            ),
            'Renewal report generated successfully'
        );
    }

    public function monthlySummary(
        Request $request
    ): JsonResponse {

        return $this->successResponse(
            $this->service->monthlySummary(
                $request->all()
            ),
            'Monthly summary generated successfully'
        );
    }

    public function studentAttendance(Request $request)
    {
        return $this->successResponse(
            $this->service->studentAttendance($request->all())
        );
    }

    public function teacherAttendance(Request $request)
    {
        return $this->successResponse(
            $this->service->teacherAttendance($request->all())
        );
    }

    public function staffAttendance(Request $request)
    {
        return $this->successResponse(
            $this->service->staffAttendance($request->all())
        );
    }

    public function courseRevenue(Request $request)
    {
        return $this->successResponse(
            $this->service->courseRevenue($request->all())
        );
    }

    public function courseProfit(Request $request)
    {
        return $this->successResponse(
            $this->service->courseProfit($request->all())
        );
    }

    public function teacherWiseRevenue(Request $request)
    {
        return $this->successResponse(
            $this->service->teacherWiseRevenue($request->all())
        );
    }

    public function batchWiseRevenue(Request $request)
    {
        return $this->successResponse(
            $this->service->batchWiseRevenue($request->all())
        );
    }

    public function yearlyRevenue(Request $request)
    {
        return $this->successResponse(
            $this->service->yearlyRevenue($request->all())
        );
    }

    public function tax(Request $request)
    {
        return $this->successResponse(
            $this->service->tax($request->all())
        );
    }

    public function outstandingRenewals(Request $request)
    {
        return $this->successResponse(
            $this->service->outstandingRenewals($request->all())
        );
    }

    public function pendingTeacherPayments(Request $request)
    {
        return $this->successResponse(
            $this->service->pendingTeacherPayments($request->all())
        );
    }

    public function pendingStaffPayments(Request $request)
    {
        return $this->successResponse(
            $this->service->pendingStaffPayments($request->all())
        );
    }


    public function monthlyAttendance(Request $request)
    {
        return $this->successResponse(
            $this->service->monthlyAttendance(
                $request->all()
            )
        );
    }

    public function teacherWorkingDays(Request $request)
    {
        return $this->successResponse(
            $this->service->teacherWorkingDays(
                $request->all()
            )
        );
    }

    public function staffWorkingDays(Request $request)
    {
        return $this->successResponse(
            $this->service->staffWorkingDays(
                $request->all()
            )
        );
    }

    public function studentPercentage(Request $request)
    {
        return $this->successResponse(
            $this->service->studentPercentage(
                $request->all()
            )
        );
    }

    public function lowAttendanceStudents(Request $request)
{
    return $this->successResponse(
        $this->service->lowAttendanceStudents(
            $request->all()
        )
    );
}

public function todayAbsentStudents(Request $request)
{
    return $this->successResponse(
        $this->service->todayAbsentStudents()
    );
}

public function todayAbsentTeachers(Request $request)
{
    return $this->successResponse(
        $this->service->todayAbsentTeachers()
    );
}

public function todayAbsentStaff(Request $request)
{
    return $this->successResponse(
        $this->service->todayAbsentStaff()
    );
}

public function courseAttendance(Request $request)
{
    return $this->successResponse(
        $this->service->courseAttendance(
            $request->all()
        )
    );
}

public function batchAttendance(Request $request)
{
    return $this->successResponse(
        $this->service->batchAttendance(
            $request->all()
        )
    );
}

public function attendanceSummary(Request $request)
{
    return $this->successResponse(
        $this->service->attendanceSummary(
            $request->all()
        )
    );
}

public function studentAttendanceHistory(
    int $studentId
)
{
    return $this->successResponse(
        $this->service
            ->studentAttendanceHistory(
                $studentId
            )
    );
}

public function teacherAttendanceHistory(
    int $teacherId
)
{
    return $this->successResponse(
        $this->service
            ->teacherAttendanceHistory(
                $teacherId
            )
    );
}

public function staffAttendanceHistory(
    int $staffId
)
{
    return $this->successResponse(
        $this->service
            ->staffAttendanceHistory(
                $staffId
            )
    );
}

public function courseWiseAbsentees()
{
    return $this->successResponse(
        $this->service
            ->courseWiseAbsentees()
    );
}

public function batchWiseAbsentees()
{
    return $this->successResponse(
        $this->service
            ->batchWiseAbsentees()
    );
}

public function monthlyAttendanceTrend()
{
    return $this->successResponse(
        $this->service
            ->monthlyAttendanceTrend()
    );
}

public function yearlyAttendanceTrend()
{
    return $this->successResponse(
        $this->service
            ->yearlyAttendanceTrend()
    );
}


}

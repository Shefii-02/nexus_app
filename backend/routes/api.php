<?php

use App\Http\Controllers\API\Admin\AdmissionController;
use App\Http\Controllers\API\Admin\AdmissionPaymentController;
use App\Http\Controllers\API\Admin\AdmissionRenewalController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Admin\TeacherController;
use App\Http\Controllers\API\Admin\StudentController;
use App\Http\Controllers\API\Admin\StaffController;
use App\Http\Controllers\API\Admin\CourseController;
use App\Http\Controllers\API\Admin\CourseClassController;
use App\Http\Controllers\API\Admin\PaymentController;
use App\Http\Controllers\API\Admin\AnnouncementController;
use App\Http\Controllers\API\Admin\ConversationController;
use App\Http\Controllers\API\Admin\CouponController;
use App\Http\Controllers\API\Admin\CourseMaterialController;
use App\Http\Controllers\API\Admin\LeaveRequestController;
use App\Http\Controllers\API\Admin\MessageController;
use App\Http\Controllers\API\Admin\NotificationController;
use App\Http\Controllers\API\Admin\RoleController;
use App\Http\Controllers\API\Admin\TeacherPaymentController;
use App\Http\Controllers\API\Admin\TransactionController;
use App\Http\Controllers\API\Admin\UserController;
use App\Http\Controllers\API\Admin\StaffPaymentController;
use App\Http\Controllers\API\Admin\TeacherPaymentItemController;
use App\Http\Controllers\API\Admin\ReportController;
use App\Http\Controllers\API\AppPaymentController;
// routes/api.php
use Illuminate\Support\Facades\Broadcast;

Route::post('/broadcasting/auth', function () {
    return Broadcast::auth(request());
})->middleware('auth:api');


Route::post('send-otp', [AuthController::class, 'sendOtp']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);


// Public Auth Routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

// Protected Auth Routes
Route::middleware('auth:api')->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('logout', [AuthController::class, 'logout']);
});

// Admin Routes - CRUD Operations
Route::middleware(['auth:api'])->group(function () {

    Route::get('users/search', [UserController::class, 'allUsers']);
    Route::get('all-users', [UserController::class, 'allUsers']);

    // Teacher Management
    Route::apiResource('teachers', TeacherController::class);

    // Student Management
    Route::apiResource('students', StudentController::class);

    // Staff Management
    Route::apiResource('staff', StaffController::class);

    Route::apiResource('roles', RoleController::class);

    // Course Management
    Route::apiResource('courses', CourseController::class);
    Route::post('courses/{course}/attach-student/{student}', [CourseController::class, 'attachStudent']);
    Route::delete('courses/{course}/detach-student/{student}', [CourseController::class, 'detachStudent']);

    // Course Classes Management
    Route::get('course-classes/upcoming', [CourseClassController::class, 'upcoming']);
    Route::get('course-classes/by-date-range', [CourseClassController::class, 'byDateRange']);
    Route::apiResource('courses/{courseId}/classes', CourseClassController::class);
    Route::apiResource('courses/{courseId}/materials', CourseMaterialController::class);

    // Payment Management
    Route::apiResource('payments', PaymentController::class);
    Route::post('payments/{payment}/verify', [PaymentController::class, 'verify']);
    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject']);
    Route::get('payments/student/{student}', [PaymentController::class, 'studentPayments']);

    // Announcement Management
    Route::apiResource('announcements', AnnouncementController::class);
    Route::get('announcements/published', [AnnouncementController::class, 'published']);
    Route::post('announcements/{announcement}/publish', [AnnouncementController::class, 'publish']);
    Route::post('announcements/{announcement}/archive', [AnnouncementController::class, 'archive']);

    Route::get('updates', [AnnouncementController::class, 'appIndex']);
    Route::get('updates/{id}', [AnnouncementController::class, 'AppShow']);
    Route::post('updates/{id}/click', [AnnouncementController::class, 'markClicked']);


    Route::get('permissions', [RoleController::class, 'permissions']);



    Route::delete('/conversations/{id}', [ConversationController::class, 'destroy']);

    Route::prefix('conversations')->group(function () {
        Route::post('/', [ConversationController::class, 'store']);
        Route::get('/', [ConversationController::class, 'index']);
        Route::get('{id}/show', [ConversationController::class, 'show']);
        Route::post('{id}/report', [ConversationController::class, 'report']);
        Route::post('{id}/toggle-mute', [ConversationController::class, 'toggleMute']);
        Route::delete('{id}', [ConversationController::class, 'destroy']);
        Route::get('{id}', [ConversationController::class, 'edit']);
        Route::put('{id}', [ConversationController::class, 'update']);
        Route::post('clear/{conversationId}', [ConversationController::class, 'clearForMe']);
        Route::post('clear-global/{conversationId}', [ConversationController::class, 'clearGlobal']);
    });

    Route::prefix('messages')->group(function () {
        Route::post('/', [MessageController::class, 'store']);
        Route::put('/{id}', [MessageController::class, 'update']);
        Route::post('/{id}/report', [MessageController::class, 'report']);
        Route::post('/{id}/pin', [MessageController::class, 'pin']);
        Route::delete('/{id}', [MessageController::class, 'destroy']);
    });


    Route::prefix('payments')->group(function () {

        // ── Student ──────────────────────────────────────────────────────────────
        // GET /api/payments/student?student_id=5
        Route::get('student', [AppPaymentController::class, 'studentPayments'])
            ->name('payments.student');

        // ── Teacher ──────────────────────────────────────────────────────────────
        // GET /api/payments/teacher?teacher_id=3
        Route::get('teacher', [AppPaymentController::class, 'teacherPayments'])
            ->name('payments.teacher');

        // ── Admin / Staff (all 4 tabs) ────────────────────────────────────────────
        // GET /api/payments/admin
        Route::get('admin', [AppPaymentController::class, 'adminPayments'])
            ->middleware('role:admin,staff')
            ->name('payments.admin');

        // ── Receipt generation ────────────────────────────────────────────────────
        // GET /api/payments/receipt/{type}/{id}
        // type: admission | renewal | teacher | staff
        Route::get('receipt/{type}/{id}', [AppPaymentController::class, 'receipt'])
            ->name('payments.receipt');

        // ── Admin release actions ─────────────────────────────────────────────────
        Route::middleware('role:admin,staff')->group(function () {
            // POST /api/payments/teacher/{id}/release
            Route::post('teacher/{id}/release', [AppPaymentController::class, 'releaseTeacherPayment'])
                ->name('payments.teacher.release');

            // POST /api/payments/staff/{id}/release
            Route::post('staff/{id}/release', [AppPaymentController::class, 'releaseStaffPayment'])
                ->name('payments.staff.release');
        });
    });


    // routes/api.php

    Route::prefix('payments')->group(function () {

        Route::apiResource(
            'admissions',
            AdmissionController::class
        );

        Route::get(
            'admissions/{id}/payments',
            [AdmissionController::class, 'payments']
        );

        Route::prefix('renewals')
            ->group(function () {

                Route::get(
                    '/',
                    [AdmissionRenewalController::class, 'index']
                );

                Route::get(
                    '/due',
                    [AdmissionRenewalController::class, 'due']
                );

                Route::post(
                    '/',
                    [AdmissionRenewalController::class, 'store']
                );

                Route::get(
                    '/{id}',
                    [AdmissionRenewalController::class, 'show']
                );

                Route::post(
                    '/{id}/mark-paid',
                    [AdmissionRenewalController::class, 'markPaid']
                );
            });


        Route::prefix('admission-payments')
            ->group(function () {

                Route::get(
                    '/',
                    [AdmissionPaymentController::class, 'index']
                );

                Route::post(
                    '/',
                    [AdmissionPaymentController::class, 'store']
                );

                Route::get(
                    '/{id}',
                    [AdmissionPaymentController::class, 'show']
                );
            });


        // Admission
        // Route::post('/admission', [PaymentController::class, 'storeAdmission']);
        // Route::get('/admissions', [PaymentController::class, 'admissionList']);

        // Renewal
        // Route::post('/renewal', [PaymentController::class, 'storeRenewal']);
        // Route::get('/renewals', [PaymentController::class, 'renewalList']);

        // Transactions
        Route::get('/transactions', [PaymentController::class, 'transactions']);

        // Invoice
        Route::get('/invoice/{type}/{id}', [PaymentController::class, 'invoice']);
    });

    Route::prefix('transactions')
        ->group(function () {

            Route::get(
                '/',
                [TransactionController::class, 'index']
            );

            Route::get(
                '/summary',
                [TransactionController::class, 'summary']
            );

            Route::get(
                '/{id}',
                [TransactionController::class, 'show']
            );
        });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/', [NotificationController::class, 'store']);

        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);

        Route::get('/{notification}', [NotificationController::class, 'show']);
        Route::put('/{notification}', [NotificationController::class, 'update']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead']);
    });


    Route::prefix('announcements')->group(function () {
        Route::post('/', [AnnouncementController::class, 'store']);
        Route::get('/', [AnnouncementController::class, 'index']);
        Route::get('/{id}', [AnnouncementController::class, 'show']);
        Route::put('/{id}', [AnnouncementController::class, 'update']);
        Route::delete('/{id}', [AnnouncementController::class, 'destroy']);
    });
    //  Route::get('announcements/active', [AnnouncementController::class, 'active']);


    Route::prefix('teacher-payment-items')->group(function () {

        Route::get(
            '/',
            [TeacherPaymentItemController::class, 'index']
        );

        Route::get(
            '/pending',
            [TeacherPaymentItemController::class, 'pending']
        );

        Route::get(
            '/teacher/{teacherId}/pending',
            [TeacherPaymentItemController::class, 'pendingByTeacher']
        );

        Route::get(
            '/{id}',
            [TeacherPaymentItemController::class, 'show']
        );

        Route::post(
            '/',
            [TeacherPaymentItemController::class, 'store']
        );

        Route::put(
            '/{id}',
            [TeacherPaymentItemController::class, 'update']
        );

        Route::delete(
            '/{id}',
            [TeacherPaymentItemController::class, 'destroy']
        );

        Route::get(
            '/teacher-payment-items/teacher/{teacherId}/summary',
            [TeacherPaymentItemController::class, 'summary']
        );
    });


    Route::prefix('teacher-payments')->group(function () {

        Route::get(
            '/',
            [TeacherPaymentController::class, 'index']
        );

        Route::get(
            '/history',
            [TeacherPaymentController::class, 'history']
        );

        Route::get(
            '/statement/{teacherId}',
            [TeacherPaymentController::class, 'statement']
        );

        Route::get(
            '/{id}',
            [TeacherPaymentController::class, 'show']
        );

        Route::post(
            '/release',
            [TeacherPaymentController::class, 'release']
        );
    });

    Route::prefix('coupons')->group(function () {

        Route::get(
            '/',
            [CouponController::class, 'index']
        );

        Route::get(
            '/active',
            [CouponController::class, 'active']
        );

        Route::post(
            '/',
            [CouponController::class, 'store']
        );

        Route::post(
            '/validate',
            [CouponController::class, 'validateCoupon']
        );

        Route::get(
            '/{id}',
            [CouponController::class, 'show']
        );

        Route::put(
            '/{id}',
            [CouponController::class, 'update']
        );

        Route::delete(
            '/{id}',
            [CouponController::class, 'destroy']
        );

        Route::get(
            '/{id}/usage-history',
            [CouponController::class, 'usageHistory']
        );
    });


    Route::prefix('staff-payments')->group(function () {

        Route::get(
            '/',
            [StaffPaymentController::class, 'index']
        );

        Route::get(
            '/pending',
            [StaffPaymentController::class, 'pending']
        );

        Route::get(
            '/history',
            [StaffPaymentController::class, 'history']
        );

        Route::get(
            '/{id}',
            [StaffPaymentController::class, 'show']
        );

        Route::post(
            '/',
            [StaffPaymentController::class, 'store']
        );

        Route::put(
            '/{id}',
            [StaffPaymentController::class, 'update']
        );

        Route::post(
            '/{id}/release',
            [StaffPaymentController::class, 'release']
        );

        Route::delete(
            '/{id}',
            [StaffPaymentController::class, 'destroy']
        );
    });


    Route::prefix('reports')->group(function () {

        Route::get(
            '/revenue',
            [ReportController::class, 'revenue']
        );

        Route::get(
            '/profit',
            [ReportController::class, 'profit']
        );

        Route::get(
            '/teacher-earnings',
            [ReportController::class, 'teacherEarnings']
        );

        Route::get(
            '/staff-salary',
            [ReportController::class, 'staffSalary']
        );

        Route::get(
            '/coupon-usage',
            [ReportController::class, 'couponUsage']
        );

        Route::get(
            '/admissions',
            [ReportController::class, 'admissions']
        );

        Route::get(
            '/renewals',
            [ReportController::class, 'renewals']
        );

        Route::get(
            '/monthly-summary',
            [ReportController::class, 'monthlySummary']
        );

        Route::get('/student-attendance', [ReportController::class, 'studentAttendance']);
        Route::get('/teacher-attendance', [ReportController::class, 'teacherAttendance']);
        Route::get('/staff-attendance', [ReportController::class, 'staffAttendance']);

        Route::get('/course-revenue', [ReportController::class, 'courseRevenue']);
        Route::get('/course-profit', [ReportController::class, 'courseProfit']);

        Route::get('/teacher-wise-revenue', [ReportController::class, 'teacherWiseRevenue']);

        Route::get('/batch-wise-revenue', [ReportController::class, 'batchWiseRevenue']);

        Route::get('/yearly-revenue', [ReportController::class, 'yearlyRevenue']);

        Route::get('/tax', [ReportController::class, 'tax']);

        Route::get('/outstanding-renewals', [ReportController::class, 'outstandingRenewals']);

        Route::get('/pending-teacher-payments', [ReportController::class, 'pendingTeacherPayments']);

        Route::get('/pending-staff-payments', [ReportController::class, 'pendingStaffPayments']);

        Route::get(
            '/monthly-attendance',
            [ReportController::class, 'monthlyAttendance']
        );

        Route::get(
            '/teacher-working-days',
            [ReportController::class, 'teacherWorkingDays']
        );

        Route::get(
            '/staff-working-days',
            [ReportController::class, 'staffWorkingDays']
        );

        Route::get(
            '/student-percentage',
            [ReportController::class, 'studentPercentage']
        );

        Route::get('/low-attendance-students', [ReportController::class, 'lowAttendanceStudents']);

        Route::get('/today-absent-students', [ReportController::class, 'todayAbsentStudents']);

        Route::get('/today-absent-teachers', [ReportController::class, 'todayAbsentTeachers']);

        Route::get('/today-absent-staff', [ReportController::class, 'todayAbsentStaff']);

        Route::get('/course-attendance', [ReportController::class, 'courseAttendance']);

        Route::get('/batch-attendance', [ReportController::class, 'batchAttendance']);

        Route::get('/attendance-summary', [ReportController::class, 'attendanceSummary']);

        Route::get(
            '/student-attendance-history/{studentId}',
            [ReportController::class, 'studentAttendanceHistory']
        );

        Route::get(
            '/teacher-attendance-history/{teacherId}',
            [ReportController::class, 'teacherAttendanceHistory']
        );

        Route::get(
            '/staff-attendance-history/{staffId}',
            [ReportController::class, 'staffAttendanceHistory']
        );

        Route::get(
            '/course-wise-absentees',
            [ReportController::class, 'courseWiseAbsentees']
        );

        Route::get(
            '/batch-wise-absentees',
            [ReportController::class, 'batchWiseAbsentees']
        );

        Route::get(
            '/monthly-attendance-trend',
            [ReportController::class, 'monthlyAttendanceTrend']
        );

        Route::get(
            '/yearly-attendance-trend',
            [ReportController::class, 'yearlyAttendanceTrend']
        );
    });


    Route::prefix(
        'leave-requests'
    )->group(function () {

        Route::get(
            '/',
            [LeaveRequestController::class, 'index']
        );

        Route::post(
            '/',
            [LeaveRequestController::class, 'store']
        );

        Route::get(
            '/{id}',
            [LeaveRequestController::class, 'show']
        );

        Route::post(
            '/{id}/approve',
            [LeaveRequestController::class, 'approve']
        );

        Route::post(
            '/{id}/reject',
            [LeaveRequestController::class, 'reject']
        );

        Route::delete(
            '/{id}',
            [LeaveRequestController::class, 'destroy']
        );
    });


    Route::prefix('attendance')->group(function () {

        Route::prefix('students')->group(function () {

            Route::get('/');
            Route::post('/');
            Route::put('/{id}');
            Route::delete('/{id}');
        });

        Route::prefix('teachers')->group(function () {

            Route::get('/');
            Route::post('/');
            Route::put('/{id}');
            Route::delete('/{id}');
        });

        Route::prefix('staff')->group(function () {

            Route::get('/');
            Route::post('/');
            Route::put('/{id}');
            Route::delete('/{id}');
        });

        // Route::prefix('leave-requests')->group(function () {

        //     Route::get('/');

        //     Route::post('/');

        //     Route::put('/{id}/approve');

        //     Route::put('/{id}/reject');
        // });
    });
});


require __DIR__ . '/chat.php';

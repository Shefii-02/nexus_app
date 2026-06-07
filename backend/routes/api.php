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
use Illuminate\Support\Facades\Log;

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

    Route::post('/user/fcm-token', function (Request $request) {
        $request->validate(['fcm_token' => 'required|string', 'platform' => 'required|string']);
        Log::info('-------------------------------');
        Log::info($request->all());
        Log::info('-------------------------------');
        // $request->user()->update(['fcm_token' => $request->fcm_token]);
        return response()->json(['success' => true]);
    });

    Route::delete('/user/fcm-token', function (Request $request) {
        Log::info('-------------------------------');
        Log::info($request->all());
        Log::info('-------------------------------');
        //   $request->user()->update(['fcm_token' => null]);
        return response()->json(['success' => true]);
    });


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


    Route::get('/my_courses', function () {
        $getCourses = [
            'success' => true,
            'data' => [
                [
                    'id'                => 1,
                    'name'              => 'Flutter & Dart – Complete Bootcamp',
                    'description'       => 'Build stunning cross-platform apps from scratch. Covers widgets, state management, APIs and deployment.',
                    'teacher_name'      => 'Arjun Menon',
                    'teacher_avatar'    => '👨‍🏫',
                    'category'          => 'Mobile Development',
                    'total_classes'     => 24,
                    'completed_classes' => 14,
                    'expires_at'        => '2025-08-10T00:00:00.000000Z',
                    'status'            => 'active',
                ],
                [
                    'id'                => 2,
                    'name'              => 'Laravel REST API Masterclass',
                    'description'       => 'Design and build production-grade REST APIs with Laravel, Sanctum, queues and real-time events.',
                    'teacher_name'      => 'Priya Nair',
                    'teacher_avatar'    => '👩‍💻',
                    'category'          => 'Backend Development',
                    'total_classes'     => 18,
                    'completed_classes' => 18,
                    'expires_at'        => '2025-02-20T00:00:00.000000Z',
                    'status'            => 'expired',
                ],
                [
                    'id'                => 3,
                    'name'              => 'UI/UX Design Fundamentals',
                    'description'       => 'Master Figma, design systems, typography and user research.',
                    'teacher_name'      => 'Sneha Pillai',
                    'teacher_avatar'    => '🎨',
                    'category'          => 'Design',
                    'total_classes'     => 12,
                    'completed_classes' => 3,
                    'expires_at'        => '2025-09-10T00:00:00.000000Z',
                    'status'            => 'active',
                ],
                [
                    'id'                => 4,
                    'name'              => 'Python for Data Science',
                    'description'       => 'Pandas, NumPy, Matplotlib and scikit-learn.',
                    'teacher_name'      => 'Rahul Krishnan',
                    'teacher_avatar'    => '🧑‍🔬',
                    'category'          => 'Data Science',
                    'total_classes'     => 20,
                    'completed_classes' => 0,
                    'expires_at'        => '2025-10-10T00:00:00.000000Z',
                    'status'            => 'active',
                ],
            ],
        ];

        return response()->json([
            'data' =>  $getCourses
        ]);
    });

    Route::get('/classes/today', function () {
        $getCourseDetail = [
            'success' => true,
            'data' => [

                'course' => [
                    'id'                => 1,
                    'name'              => 'Flutter & Dart – Complete Bootcamp',
                    'description'       => 'Build stunning cross-platform apps from scratch. Covers widgets, state management, APIs and deployment.',
                    'teacher_name'      => 'Arjun Menon',
                    'teacher_avatar'    => '👨‍🏫',
                    'category'          => 'Mobile Development',
                    'total_classes'     => 24,
                    'completed_classes' => 14,
                    'expires_at'        => '2025-08-10T00:00:00.000000Z',
                    'status'            => 'active',
                ],

                'classes' => [
                    // ── LIVE ──────────────────────────────────────────────────────
                    [
                        'id'               => 101,
                        'title'            => 'Riverpod State Management – Deep Dive',
                        'description'      => 'We cover providers, notifiers, and async state patterns with real examples.',
                        'scheduled_at'     => '2025-06-07T05:00:00.000000Z', // now - 15 min
                        'duration_minutes' => 90,
                        'status'           => 'live',           // 'upcoming' | 'live' | 'completed'
                        'meeting_url'      => 'https://meet.google.com/abc-defg-hij',
                        'recording_url'    => null,
                        'teacher_name'     => 'Arjun Menon',
                        'attendance_count' => 22,
                    ],
                    // ── UPCOMING ─────────────────────────────────────────────────
                    [
                        'id'               => 102,
                        'title'            => 'Navigation 2.0 & GoRouter',
                        'description'      => 'Declarative routing, deep links and nested navigators.',
                        'scheduled_at'     => '2025-06-09T06:00:00.000000Z',
                        'duration_minutes' => 75,
                        'status'           => 'upcoming',
                        'meeting_url'      => null,
                        'recording_url'    => null,
                        'teacher_name'     => 'Arjun Menon',
                        'attendance_count' => 0,
                    ],
                    [
                        'id'               => 103,
                        'title'            => 'Flutter Animations – Implicit & Explicit',
                        'description'      => 'AnimationController, Tween, Hero transitions and custom painters.',
                        'scheduled_at'     => '2025-06-12T06:00:00.000000Z',
                        'duration_minutes' => 90,
                        'status'           => 'upcoming',
                        'meeting_url'      => null,
                        'recording_url'    => null,
                        'teacher_name'     => 'Arjun Menon',
                        'attendance_count' => 0,
                    ],
                    // ── COMPLETED ────────────────────────────────────────────────
                    [
                        'id'               => 105,
                        'title'            => 'Introduction to Flutter & Dart Basics',
                        'description'      => null,
                        'scheduled_at'     => '2025-05-18T06:00:00.000000Z',
                        'duration_minutes' => 60,
                        'status'           => 'completed',
                        'meeting_url'      => null,
                        'recording_url'    => 'https://youtu.be/sample1',
                        'teacher_name'     => 'Arjun Menon',
                        'attendance_count' => 34,
                    ],
                    [
                        'id'               => 106,
                        'title'            => 'Widgets, Layouts & Theming',
                        'description'      => null,
                        'scheduled_at'     => '2025-05-21T06:00:00.000000Z',
                        'duration_minutes' => 75,
                        'status'           => 'completed',
                        'meeting_url'      => null,
                        'recording_url'    => 'https://youtu.be/sample2',
                        'teacher_name'     => 'Arjun Menon',
                        'attendance_count' => 30,
                    ],
                ],

                'materials' => [
                    // ── PDF ───────────────────────────────────────────────────────
                    [
                        'id'             => 201,
                        'title'          => 'Course Syllabus & Schedule',
                        'description'    => 'Complete 24-week syllabus with topics, assignments and resources.',
                        'type'           => 'pdf',
                        'file_extension' => 'pdf',
                        'file_url'       => 'https://yourdomain.com/storage/materials/syllabus.pdf',
                        'file_size_mb'   => 0.4,
                        'uploaded_at'    => '2025-04-15T00:00:00.000000Z',
                        'uploaded_by'    => 'Arjun Menon',
                    ],
                    [
                        'id'             => 202,
                        'title'          => 'Flutter Widget Cheat Sheet',
                        'description'    => 'Quick reference for commonly used Flutter widgets.',
                        'type'           => 'pdf',
                        'file_extension' => 'pdf',
                        'file_url'       => 'https://yourdomain.com/storage/materials/cheatsheet.pdf',
                        'file_size_mb'   => 1.2,
                        'uploaded_at'    => '2025-04-22T00:00:00.000000Z',
                        'uploaded_by'    => 'Arjun Menon',
                    ],
                    // ── DOCX ──────────────────────────────────────────────────────
                    [
                        'id'             => 203,
                        'title'          => 'Project Brief – Todo App',
                        'description'    => 'Assignment 1: Build a feature-complete Todo application.',
                        'type'           => 'docx',
                        'file_extension' => 'docx',
                        'file_url'       => 'https://yourdomain.com/storage/materials/assignment1.docx',
                        'file_size_mb'   => 0.2,
                        'uploaded_at'    => '2025-05-05T00:00:00.000000Z',
                        'uploaded_by'    => 'Arjun Menon',
                    ],
                    // ── AUDIO ─────────────────────────────────────────────────────
                    [
                        'id'             => 205,
                        'title'          => 'Lecture – Dart Null Safety Explained',
                        'description'    => 'Audio explanation of null safety, nullable types and the ! operator.',
                        'type'           => 'mp3',
                        'file_extension' => 'mp3',
                        'file_url'       => 'https://yourdomain.com/storage/materials/null-safety.mp3',
                        'file_size_mb'   => 8.4,
                        'uploaded_at'    => '2025-05-25T00:00:00.000000Z',
                        'uploaded_by'    => 'Arjun Menon',
                    ],
                    // ── IMAGE ─────────────────────────────────────────────────────
                    [
                        'id'             => 207,
                        'title'          => 'App Architecture Diagram',
                        'description'    => 'Visual overview of the clean architecture pattern used in this course.',
                        'type'           => 'image',
                        'file_extension' => 'png',
                        'file_url'       => 'https://yourdomain.com/storage/materials/architecture.png',
                        'file_size_mb'   => 0.3,
                        'uploaded_at'    => '2025-05-28T00:00:00.000000Z',
                        'uploaded_by'    => 'Arjun Menon',
                    ],
                ],
            ],
        ];
        return response()->json($getCourseDetail);
    });






    // Route::get('/classes/today', [CourseClassController::class, 'today']);
    Route::get('/classes/today', function () {
        $now = now();

        return response()->json([
            'data' => [
                // ── LIVE class ──────────────────────────────────────────────
                [
                    'id'          => 1,
                    'title'       => 'Advanced Mathematics',
                    'description' => 'Calculus and differential equations — Chapter 7 continuation.',
                    'start_time'  => $now->copy()->subMinutes(20)->toISOString(),
                    'end_time'    => $now->copy()->addMinutes(40)->toISOString(),
                    'status'      => 'live',
                    'class_link'  => 'https://meet.google.com/abc-defg-hij',
                    'record_link' => null,
                    'teacher'     => [
                        'id'     => 101,
                        'name'   => 'Dr. Sarah Johnson',
                        'avatar' => null,
                    ],
                ],

                // ── UPCOMING class ───────────────────────────────────────────
                [
                    'id'          => 2,
                    'title'       => 'Physics — Quantum Mechanics',
                    'description' => 'Introduction to wave-particle duality and Heisenberg uncertainty principle.',
                    'start_time'  => $now->copy()->addHours(1)->addMinutes(30)->toISOString(),
                    'end_time'    => $now->copy()->addHours(3)->toISOString(),
                    'status'      => 'upcoming',
                    'class_link'  => 'https://zoom.us/j/123456789',
                    'record_link' => null,
                    'teacher'     => [
                        'id'     => 102,
                        'name'   => 'Prof. Alan Turing',
                        'avatar' => null,
                    ],
                ],

                // ── UPCOMING class (no description) ──────────────────────────
                [
                    'id'          => 3,
                    'title'       => 'English Literature',
                    'description' => null,
                    'start_time'  => $now->copy()->addHours(4)->toISOString(),
                    'end_time'    => $now->copy()->addHours(5)->toISOString(),
                    'status'      => 'upcoming',
                    'class_link'  => 'https://teams.microsoft.com/l/meetup-join/abc',
                    'record_link' => null,
                    'teacher'     => [
                        'id'     => 103,
                        'name'   => 'Ms. Emily Clarke',
                        'avatar' => null,
                    ],
                ],

                // ── COMPLETED with recording ──────────────────────────────────
                [
                    'id'          => 4,
                    'title'       => 'Chemistry — Organic Reactions',
                    'description' => 'Nucleophilic substitution reactions SN1 and SN2 mechanisms.',
                    'start_time'  => $now->copy()->subHours(3)->toISOString(),
                    'end_time'    => $now->copy()->subHours(2)->toISOString(),
                    'status'      => 'completed',
                    'class_link'  => 'https://meet.google.com/xyz-abcd-efg',
                    'record_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'teacher'     => [
                        'id'     => 104,
                        'name'   => 'Dr. Marie Curie',
                        'avatar' => null,
                    ],
                ],

                // ── COMPLETED without recording (triggers WhatsApp sheet) ─────
                [
                    'id'          => 5,
                    'title'       => 'Computer Science — Data Structures',
                    'description' => 'Binary trees, AVL trees and heap sort algorithms.',
                    'start_time'  => $now->copy()->subHours(5)->toISOString(),
                    'end_time'    => $now->copy()->subHours(4)->toISOString(),
                    'status'      => 'completed',
                    'class_link'  => 'https://zoom.us/j/987654321',
                    'record_link' => null,
                    'teacher'     => [
                        'id'     => 105,
                        'name'   => 'Mr. Dennis Ritchie',
                        'avatar' => null,
                    ],
                ],

                // ── COMPLETED without recording (no teacher) ──────────────────
                [
                    'id'          => 6,
                    'title'       => 'History — World War II',
                    'description' => null,
                    'start_time'  => $now->copy()->subHours(6)->toISOString(),
                    'end_time'    => $now->copy()->subHours(5)->toISOString(),
                    'status'      => 'completed',
                    'class_link'  => null,
                    'record_link' => null,
                    'teacher'     => null,
                ],
            ],
        ]);
    });




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
        // Route::get('student', [AppPaymentController::class, 'studentPayments'])
        //     ->name('payments.student');
        Route::get('/student', function () {


            return response()->json([
                'success' => true,
                'data' => [

                    // Tab 1 — Paid
                    'paid' => [
                        [
                            'id'             => 1001,
                            'admission_id'   => 10,
                            'student_id'     => 5,
                            'student_name'   => 'Amal Joshy',
                            'course_id'      => 1,
                            'course_name'    => 'Flutter & Dart – Complete Bootcamp',
                            'amount'         => 4500.00,
                            'payment_method' => 'upi',
                            'transaction_no' => 'UPI2024031501',
                            'remarks'        => 'Admission fee',
                            'paid_at'        => '2025-04-23T10:30:00.000000Z',
                            'received_by'    => 'Admin',
                            'created_at'     => '2025-04-23T10:30:00.000000Z',
                        ],
                        [
                            'id'             => 1002,
                            'admission_id'   => 10,
                            'student_id'     => 5,
                            'student_name'   => 'Amal Joshy',
                            'course_id'      => 3,
                            'course_name'    => 'UI/UX Design Fundamentals',
                            'amount'         => 3200.00,
                            'payment_method' => 'bank_transfer',
                            'transaction_no' => 'TXN20240210',
                            'remarks'        => null,
                            'paid_at'        => '2025-03-24T09:00:00.000000Z',
                            'received_by'    => 'Admin',
                            'created_at'     => '2025-03-24T09:00:00.000000Z',
                        ],
                        [
                            'id'             => 1003,
                            'admission_id'   => 9,
                            'student_id'     => 5,
                            'student_name'   => 'Amal Joshy',
                            'course_id'      => 2,
                            'course_name'    => 'Laravel REST API Masterclass',
                            'amount'         => 3800.00,
                            'payment_method' => 'cash',
                            'transaction_no' => null,
                            'remarks'        => 'Paid in office',
                            'paid_at'        => '2025-02-07T11:00:00.000000Z',
                            'received_by'    => 'Reception',
                            'created_at'     => '2025-02-07T11:00:00.000000Z',
                        ],
                    ],

                    // Tab 2 — Pending (renewals due)
                    'pending' => [
                        [
                            'id'                  => 501,
                            'admission_id'        => 10,
                            'student_id'          => 5,
                            'student_name'        => 'Amal Joshy',
                            'course_id'           => 1,
                            'course_name'         => 'Flutter & Dart – Complete Bootcamp',
                            'current_expiry_date' => '2025-08-10',
                            'renewal_from'        => '2025-08-11',
                            'renewal_to'          => '2025-10-10',
                            'amount'              => 4500.00,
                            'discount_amount'     => 450.00,
                            'final_amount'        => 4050.00,
                            'paid_at'             => null,
                            'status'              => 'pending',  // 'pending' | 'paid' | 'expired'
                            'remarks'             => '10% early renewal discount',
                            'created_at'          => '2025-06-01T00:00:00.000000Z',
                        ],
                        [
                            'id'                  => 502,
                            'admission_id'        => 11,
                            'student_id'          => 5,
                            'student_name'        => 'Amal Joshy',
                            'course_id'           => 3,
                            'course_name'         => 'UI/UX Design Fundamentals',
                            'current_expiry_date' => '2025-06-02',
                            'renewal_from'        => '2025-06-02',
                            'renewal_to'          => '2025-08-02',
                            'amount'              => 3200.00,
                            'discount_amount'     => 0.00,
                            'final_amount'        => 3200.00,
                            'paid_at'             => null,
                            'status'              => 'pending',
                            'remarks'             => null,
                            'created_at'          => '2025-05-20T00:00:00.000000Z',
                        ],
                    ],
                ],
            ]);
        });

        // ── Teacher ──────────────────────────────────────────────────────────────
        // GET /api/payments/teacher?teacher_id=3
        // Route::get('teacher', [AppPaymentController::class, 'teacherPayments'])
        //     ->name('payments.teacher');
        Route::get('/teacher', function () {
            return response()->json([
                'success' => true,
                'data' => [

                    // Tab 1 — Pending release
                    'pending_release' => [
                        [
                            'id'               => 301,
                            'teacher_id'       => 7,
                            'teacher_name'     => 'Arjun Menon',
                            'period_start'     => '2025-05-01',
                            'period_end'       => '2025-05-31',
                            'total_classes'    => 8,
                            'gross_amount'     => 12000.00,
                            'deduction_amount' => 0.00,
                            'deduction_reason' => null,
                            'amount'           => 12000.00,
                            'payment_method'   => null,
                            'payment_reference' => null,
                            'transaction_no'   => null,
                            'payment_date'     => null,
                            'remarks'          => null,
                            'status'           => 'pending',
                            'paid_at'          => null,
                            'released_by_name' => null,
                            'created_by_name'  => 'Admin',
                            'items' => [
                                [
                                    'id'               => 3011,
                                    'course_id'        => 1,
                                    'course_name'      => 'Flutter & Dart – Complete Bootcamp',
                                    'month'            => 'May 2025',
                                    'calculation_type' => 'percentage', // 'percentage' | 'per_class' | 'fixed'
                                    'student_count'    => 14,
                                    'course_revenue'   => 63000.00,
                                    'share_percentage' => 15.0,
                                    'amount'           => 9450.00,
                                    'remarks'          => null,
                                    'status'           => 'pending',
                                ],
                                [
                                    'id'               => 3012,
                                    'course_id'        => 4,
                                    'course_name'      => 'Python for Data Science',
                                    'month'            => 'May 2025',
                                    'calculation_type' => 'per_class',
                                    'student_count'    => 8,
                                    'course_revenue'   => 24000.00,
                                    'share_percentage' => 0.0,
                                    'amount'           => 2550.00,
                                    'remarks'          => null,
                                    'status'           => 'pending',
                                ],
                            ],
                        ],
                    ],

                    // Tab 2 — Released
                    'released' => [
                        [
                            'id'               => 302,
                            'teacher_id'       => 7,
                            'teacher_name'     => 'Arjun Menon',
                            'period_start'     => '2025-04-01',
                            'period_end'       => '2025-04-30',
                            'total_classes'    => 9,
                            'gross_amount'     => 13500.00,
                            'deduction_amount' => 500.00,
                            'deduction_reason' => 'One class cancelled without notice',
                            'amount'           => 13000.00,
                            'payment_method'   => 'bank_transfer',
                            'payment_reference' => null,
                            'transaction_no'   => 'NEFT20240201',
                            'payment_date'     => '2025-05-03',
                            'remarks'          => 'On time payment',
                            'status'           => 'released',
                            'paid_at'          => '2025-05-03T10:00:00.000000Z',
                            'released_by_name' => 'Admin',
                            'created_by_name'  => 'Admin',
                            'items' => [
                                [
                                    'id'               => 3021,
                                    'course_id'        => 1,
                                    'course_name'      => 'Flutter & Dart – Complete Bootcamp',
                                    'month'            => 'Apr 2025',
                                    'calculation_type' => 'percentage',
                                    'student_count'    => 13,
                                    'course_revenue'   => 58500.00,
                                    'share_percentage' => 15.0,
                                    'amount'           => 8775.00,
                                    'remarks'          => null,
                                    'status'           => 'released',
                                ],
                                [
                                    'id'               => 3022,
                                    'course_id'        => 4,
                                    'course_name'      => 'Python for Data Science',
                                    'month'            => 'Apr 2025',
                                    'calculation_type' => 'per_class',
                                    'student_count'    => 8,
                                    'course_revenue'   => 24000.00,
                                    'share_percentage' => 0.0,
                                    'amount'           => 4225.00,
                                    'remarks'          => null,
                                    'status'           => 'released',
                                ],
                            ],
                        ],
                        [
                            'id'               => 303,
                            'teacher_id'       => 7,
                            'teacher_name'     => 'Arjun Menon',
                            'period_start'     => '2025-03-01',
                            'period_end'       => '2025-03-31',
                            'total_classes'    => 10,
                            'gross_amount'     => 15000.00,
                            'deduction_amount' => 0.00,
                            'deduction_reason' => null,
                            'amount'           => 15000.00,
                            'payment_method'   => 'upi',
                            'payment_reference' => null,
                            'transaction_no'   => 'UPI20240101',
                            'payment_date'     => '2025-04-02',
                            'remarks'          => null,
                            'status'           => 'released',
                            'paid_at'          => '2025-04-02T11:30:00.000000Z',
                            'released_by_name' => 'Admin',
                            'created_by_name'  => 'Admin',
                            'items'            => [],
                        ],
                    ],
                ],
            ]);
        });

        // ── Admin / Staff (all 4 tabs) ────────────────────────────────────────────
        // GET /api/payments/admin
        Route::get('/admin', function () {
            return response()->json([
                'success' => true,
                'data' => [

                    // Tab 1 — Pending collection (student renewals not yet paid)
                    'pending_collection' => [
                        [
                            'id'                  => 601,
                            'admission_id'        => 10,
                            'student_id'          => 5,
                            'student_name'        => 'Amal Joshy',
                            'course_id'           => 1,
                            'course_name'         => 'Flutter & Dart – Complete Bootcamp',
                            'current_expiry_date' => '2025-08-10',
                            'renewal_from'        => '2025-08-11',
                            'renewal_to'          => '2025-10-10',
                            'amount'              => 4500.00,
                            'discount_amount'     => 450.00,
                            'final_amount'        => 4050.00,
                            'paid_at'             => null,
                            'status'              => 'pending',
                            'remarks'             => '10% early renewal',
                            'created_at'          => '2025-06-01T00:00:00.000000Z',
                        ],
                        [
                            'id'                  => 602,
                            'admission_id'        => 12,
                            'student_id'          => 6,
                            'student_name'        => 'Meera Thomas',
                            'course_id'           => 3,
                            'course_name'         => 'UI/UX Design Fundamentals',
                            'current_expiry_date' => '2025-06-02',
                            'renewal_from'        => '2025-06-02',
                            'renewal_to'          => '2025-08-02',
                            'amount'              => 3200.00,
                            'discount_amount'     => 0.00,
                            'final_amount'        => 3200.00,
                            'paid_at'             => null,
                            'status'              => 'pending',
                            'remarks'             => null,
                            'created_at'          => '2025-05-20T00:00:00.000000Z',
                        ],
                        [
                            'id'                  => 603,
                            'admission_id'        => 14,
                            'student_id'          => 8,
                            'student_name'        => 'Rahul Varghese',
                            'course_id'           => 4,
                            'course_name'         => 'Python for Data Science',
                            'current_expiry_date' => '2025-05-26',
                            'renewal_from'        => '2025-05-26',
                            'renewal_to'          => '2025-07-25',
                            'amount'              => 3500.00,
                            'discount_amount'     => 0.00,
                            'final_amount'        => 3500.00,
                            'paid_at'             => null,
                            'status'              => 'pending',
                            'remarks'             => 'Overdue – follow up needed',
                            'created_at'          => '2025-05-10T00:00:00.000000Z',
                        ],
                    ],

                    // Tab 2 — Pending release
                    'pending_release' => [

                        'teachers' => [
                            [
                                'id'               => 301,
                                'teacher_id'       => 7,
                                'teacher_name'     => 'Arjun Menon',
                                'period_start'     => '2025-05-01',
                                'period_end'       => '2025-05-31',
                                'total_classes'    => 8,
                                'gross_amount'     => 12000.00,
                                'deduction_amount' => 0.00,
                                'amount'           => 12000.00,
                                'status'           => 'pending',
                                'paid_at'          => null,
                                'created_by_name'  => 'Admin',
                                'items'            => [],
                            ],
                            [
                                'id'               => 304,
                                'teacher_id'       => 9,
                                'teacher_name'     => 'Priya Nair',
                                'period_start'     => '2025-05-01',
                                'period_end'       => '2025-05-31',
                                'total_classes'    => 6,
                                'gross_amount'     => 9000.00,
                                'deduction_amount' => 0.00,
                                'amount'           => 9000.00,
                                'status'           => 'pending',
                                'paid_at'          => null,
                                'created_by_name'  => 'Admin',
                                'items'            => [],
                            ],
                        ],

                        'staff' => [
                            [
                                'id'               => 401,
                                'staff_id'         => 11,
                                'staff_name'       => 'Sanu Mathew',
                                'month'            => 'May 2025',
                                'salary_month'     => 'May 2025',
                                'salary_amount'    => 22000.00,
                                'bonus_amount'     => 1000.00,
                                'deduction_amount' => 0.00,
                                'deduction_reason' => null,
                                'final_amount'     => 23000.00,
                                'status'           => 'pending',
                                'paid_at'          => null,
                                'payment_method'   => null,
                                'transaction_no'   => null,
                                'payment_date'     => null,
                                'remarks'          => null,
                                'released_by_name' => null,
                            ],
                            [
                                'id'               => 402,
                                'staff_id'         => 12,
                                'staff_name'       => 'Divya Raj',
                                'month'            => 'May 2025',
                                'salary_month'     => 'May 2025',
                                'salary_amount'    => 18000.00,
                                'bonus_amount'     => 0.00,
                                'deduction_amount' => 500.00,
                                'deduction_reason' => 'Half-day leave x2',
                                'final_amount'     => 17500.00,
                                'status'           => 'pending',
                                'paid_at'          => null,
                                'payment_method'   => null,
                                'transaction_no'   => null,
                                'payment_date'     => null,
                                'remarks'          => null,
                                'released_by_name' => null,
                            ],
                        ],
                    ],

                    // Tab 3 — Released
                    'released' => [

                        'teachers' => [
                            [
                                'id'               => 302,
                                'teacher_id'       => 7,
                                'teacher_name'     => 'Arjun Menon',
                                'period_start'     => '2025-04-01',
                                'period_end'       => '2025-04-30',
                                'total_classes'    => 9,
                                'gross_amount'     => 13500.00,
                                'deduction_amount' => 500.00,
                                'deduction_reason' => 'One class cancelled without notice',
                                'amount'           => 13000.00,
                                'payment_method'   => 'bank_transfer',
                                'transaction_no'   => 'NEFT20240201',
                                'payment_date'     => '2025-05-03',
                                'status'           => 'released',
                                'paid_at'          => '2025-05-03T10:00:00.000000Z',
                                'released_by_name' => 'Admin',
                                'items'            => [],
                            ],
                            [
                                'id'               => 305,
                                'teacher_id'       => 9,
                                'teacher_name'     => 'Priya Nair',
                                'period_start'     => '2025-04-01',
                                'period_end'       => '2025-04-30',
                                'total_classes'    => 7,
                                'gross_amount'     => 10500.00,
                                'deduction_amount' => 0.00,
                                'amount'           => 10500.00,
                                'payment_method'   => 'upi',
                                'transaction_no'   => 'UPI20240202',
                                'payment_date'     => '2025-05-04',
                                'status'           => 'released',
                                'paid_at'          => '2025-05-04T09:30:00.000000Z',
                                'released_by_name' => 'Admin',
                                'items'            => [],
                            ],
                        ],

                        'staff' => [
                            [
                                'id'               => 403,
                                'staff_id'         => 11,
                                'staff_name'       => 'Sanu Mathew',
                                'month'            => 'Apr 2025',
                                'salary_month'     => 'Apr 2025',
                                'salary_amount'    => 22000.00,
                                'bonus_amount'     => 0.00,
                                'deduction_amount' => 0.00,
                                'deduction_reason' => null,
                                'final_amount'     => 22000.00,
                                'status'           => 'paid',
                                'paid_at'          => '2025-05-02T10:00:00.000000Z',
                                'payment_method'   => 'bank_transfer',
                                'transaction_no'   => 'NEFT20240205',
                                'payment_date'     => '2025-05-02',
                                'remarks'          => null,
                                'released_by_name' => 'Admin',
                            ],
                        ],
                    ],

                    // Tab 4 — Collected (admission payments received from students)
                    'collected' => [
                        [
                            'id'             => 1001,
                            'admission_id'   => 10,
                            'student_id'     => 5,
                            'student_name'   => 'Amal Joshy',
                            'course_id'      => 1,
                            'course_name'    => 'Flutter & Dart – Complete Bootcamp',
                            'amount'         => 4500.00,
                            'payment_method' => 'upi',
                            'transaction_no' => 'UPI2024031501',
                            'remarks'        => 'Admission fee',
                            'paid_at'        => '2025-04-23T10:30:00.000000Z',
                            'received_by'    => 'Admin',
                            'created_at'     => '2025-04-23T10:30:00.000000Z',
                        ],
                        [
                            'id'             => 1004,
                            'admission_id'   => 12,
                            'student_id'     => 6,
                            'student_name'   => 'Meera Thomas',
                            'course_id'      => 3,
                            'course_name'    => 'UI/UX Design Fundamentals',
                            'amount'         => 3200.00,
                            'payment_method' => 'cash',
                            'transaction_no' => null,
                            'remarks'        => null,
                            'paid_at'        => '2025-04-30T11:00:00.000000Z',
                            'received_by'    => 'Reception',
                            'created_at'     => '2025-04-30T11:00:00.000000Z',
                        ],
                        [
                            'id'             => 1005,
                            'admission_id'   => 14,
                            'student_id'     => 8,
                            'student_name'   => 'Rahul Varghese',
                            'course_id'      => 4,
                            'course_name'    => 'Python for Data Science',
                            'amount'         => 3500.00,
                            'payment_method' => 'bank_transfer',
                            'transaction_no' => 'NEFT20240315',
                            'remarks'        => null,
                            'paid_at'        => '2025-05-18T09:00:00.000000Z',
                            'received_by'    => 'Admin',
                            'created_at'     => '2025-05-18T09:00:00.000000Z',
                        ],
                        [
                            'id'             => 1006,
                            'admission_id'   => 15,
                            'student_id'     => 9,
                            'student_name'   => 'Anjali Suresh',
                            'course_id'      => 1,
                            'course_name'    => 'Flutter & Dart – Complete Bootcamp',
                            'amount'         => 4500.00,
                            'payment_method' => 'upi',
                            'transaction_no' => 'UPI20240320',
                            'remarks'        => null,
                            'paid_at'        => '2025-05-28T14:00:00.000000Z',
                            'received_by'    => 'Admin',
                            'created_at'     => '2025-05-28T14:00:00.000000Z',
                        ],
                        [
                            'id'             => 1007,
                            'admission_id'   => 16,
                            'student_id'     => 10,
                            'student_name'   => 'Vishnu Kumar',
                            'course_id'      => 2,
                            'course_name'    => 'Laravel REST API Masterclass',
                            'amount'         => 3800.00,
                            'payment_method' => 'upi',
                            'transaction_no' => 'UPI20240328',
                            'remarks'        => null,
                            'paid_at'        => '2025-06-05T16:00:00.000000Z',
                            'received_by'    => 'Admin',
                            'created_at'     => '2025-06-05T16:00:00.000000Z',
                        ],
                    ],
                ],
            ]);
        });
        // Route::get('admin', [AppPaymentController::class, 'adminPayments'])
        //     // ->middleware('role:admin,staff')
        //     ->name('payments.admin');

        // ── Receipt generation ────────────────────────────────────────────────────
        // GET /api/payments/receipt/{type}/{id}
        // type: admission | renewal | teacher | staff
        Route::get('receipt/{type}/{id}', function () {
            $receiptResponse = [
                'success' => true,
                'data' => [
                    'receipt_url'    => 'https://yourdomain.com/storage/receipts/receipt_admission_1001_abc123.pdf',
                    'filename'       => 'receipt_admission_1001_abc123.pdf',
                    'whatsapp_url'   => 'https://wa.me/?text=YourApp%20Payment%20Receipt%0AAmount%20Paid%3A%20%E2%82%B94500%20%7C%20Flutter%20%26%20Dart%20Bootcamp%0ADownload%3A%20https%3A%2F%2Fyourdomain.com%2Fstorage%2Freceipts%2Freceipt.pdf',
                    'preview_base64' => null, // optional PNG thumbnail
                ],
            ];
            return response()->json([$receiptResponse]);
        });
        // Route::get('receipt/{type}/{id}', [AppPaymentController::class, 'receipt'])
        //     ->name('payments.receipt');

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


    // Payment Management
    Route::apiResource('payments', PaymentController::class);
    Route::post('payments/{payment}/verify', [PaymentController::class, 'verify']);
    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject']);
    Route::get('payments/student/{student}', [PaymentController::class, 'studentPayments']);


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

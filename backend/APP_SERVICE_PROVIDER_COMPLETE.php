<?php

/**
 * COMPLETE AppServiceProvider.php EXAMPLE
 *
 * Copy this content into app/Providers/AppServiceProvider.php
 * This registers all module services and repositories for dependency injection.
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// ============ TEACHER BINDINGS ============
use App\Repositories\Teacher\TeacherRepositoryInterface;
use App\Repositories\Teacher\TeacherRepository;
use App\Services\Teacher\TeacherService;

// ============ STUDENT BINDINGS ============
use App\Repositories\Student\StudentRepositoryInterface;
use App\Repositories\Student\StudentRepository;
use App\Services\Student\StudentService;

// ============ STAFF BINDINGS ============
use App\Repositories\Staff\StaffRepositoryInterface;
use App\Repositories\Staff\StaffRepository;
use App\Services\Staff\StaffService;

// ============ COURSE BINDINGS ============
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\Course\CourseRepository;
use App\Services\Course\CourseService;

// ============ COURSE CLASS BINDINGS ============
use App\Repositories\CourseClass\CourseClassRepositoryInterface;
use App\Repositories\CourseClass\CourseClassRepository;
use App\Services\CourseClass\CourseClassService;

// ============ PAYMENT BINDINGS ============
use App\Repositories\Payment\PaymentRepositoryInterface;
use App\Repositories\Payment\PaymentRepository;
use App\Services\Payment\PaymentService;

// ============ ANNOUNCEMENT BINDINGS ============
use App\Repositories\Announcement\AnnouncementRepositoryInterface;
use App\Repositories\Announcement\AnnouncementRepository;
use App\Services\Announcement\AnnouncementService;

// ============ NOTIFICATION BINDINGS ============
use App\Repositories\Notification\NotificationRepositoryInterface;
use App\Repositories\Notification\NotificationRepository;
use App\Services\Notification\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This method is called before the application boots and is where
     * you can register service bindings and repository implementations.
     */
    public function register(): void
    {
        // ========================================
        // TEACHER SERVICE BINDINGS
        // ========================================

        $this->app->bind(TeacherRepositoryInterface::class, TeacherRepository::class);

        $this->app->singleton(TeacherService::class, function ($app) {
            return new TeacherService($app->make(TeacherRepositoryInterface::class));
        });


        // ========================================
        // STUDENT SERVICE BINDINGS
        // ========================================

        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);

        $this->app->singleton(StudentService::class, function ($app) {
            return new StudentService($app->make(StudentRepositoryInterface::class));
        });


        // ========================================
        // STAFF SERVICE BINDINGS
        // ========================================

        $this->app->bind(StaffRepositoryInterface::class, StaffRepository::class);

        $this->app->singleton(StaffService::class, function ($app) {
            return new StaffService($app->make(StaffRepositoryInterface::class));
        });


        // ========================================
        // COURSE SERVICE BINDINGS
        // ========================================

        $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);

        $this->app->singleton(CourseService::class, function ($app) {
            return new CourseService($app->make(CourseRepositoryInterface::class));
        });


        // ========================================
        // COURSE CLASS SERVICE BINDINGS
        // ========================================

        $this->app->bind(CourseClassRepositoryInterface::class, CourseClassRepository::class);

        $this->app->singleton(CourseClassService::class, function ($app) {
            return new CourseClassService($app->make(CourseClassRepositoryInterface::class));
        });


        // ========================================
        // PAYMENT SERVICE BINDINGS
        // ========================================

        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService($app->make(PaymentRepositoryInterface::class));
        });


        // ========================================
        // ANNOUNCEMENT SERVICE BINDINGS
        // ========================================

        $this->app->bind(AnnouncementRepositoryInterface::class, AnnouncementRepository::class);

        $this->app->singleton(AnnouncementService::class, function ($app) {
            return new AnnouncementService($app->make(AnnouncementRepositoryInterface::class));
        });


        // ========================================
        // NOTIFICATION SERVICE BINDINGS
        // ========================================

        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);

        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService($app->make(NotificationRepositoryInterface::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * This method is called after all services have been registered
     * and is where you can perform actions when the application boots.
     */
    public function boot(): void
    {
        // Perform any required actions when the application boots
        // This might include registering observers, publishing views, etc.
    }
}

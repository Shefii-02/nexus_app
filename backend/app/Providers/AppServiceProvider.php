<?php

namespace App\Providers;

use App\Repositories\Admission\AdmissionRepository;
use App\Repositories\Admission\AdmissionRepositoryInterface;
use Illuminate\Support\ServiceProvider;

use App\Repositories\Teacher\TeacherRepositoryInterface as TeacherRepositoryInterfaceContract;
use App\Repositories\Teacher\TeacherRepository as TeacherRepositoryImplementation;
use App\Services\Teacher\TeacherService;

use App\Repositories\Student\StudentRepositoryInterface as StudentRepositoryInterfaceContract;
use App\Repositories\Student\StudentRepository as StudentRepositoryImplementation;
use App\Services\Student\StudentService;

use App\Repositories\Staff\StaffRepositoryInterface as StaffRepositoryInterfaceContract;
use App\Repositories\Staff\StaffRepository as StaffRepositoryImplementation;
use App\Services\Staff\StaffService;

use App\Repositories\Course\CourseRepositoryInterface as CourseRepositoryInterfaceContract;
use App\Repositories\Course\CourseRepository as CourseRepositoryImplementation;
use App\Services\Course\CourseService;

use App\Repositories\CourseClass\CourseClassRepositoryInterface as CourseClassRepositoryInterfaceContract;
use App\Repositories\CourseClass\CourseClassRepository as CourseClassRepositoryImplementation;
use App\Services\CourseClass\CourseClassService;

use App\Repositories\Payment\PaymentRepositoryInterface as PaymentRepositoryInterfaceContract;
use App\Repositories\Payment\PaymentRepository as PaymentRepositoryImplementation;
use App\Services\Payment\PaymentService;

use App\Repositories\Announcement\AnnouncementRepositoryInterface as AnnouncementRepositoryInterfaceContract;
use App\Repositories\Announcement\AnnouncementRepository as AnnouncementRepositoryImplementation;
use App\Services\Announcement\AnnouncementService;

use App\Repositories\Notification\NotificationRepositoryInterface as NotificationRepositoryInterfaceContract;
use App\Repositories\Notification\NotificationRepository as NotificationRepositoryImplementation;
use App\Services\Auth\OtpService;
use App\Services\Notification\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Teacher bindings
        $this->app->bind(TeacherRepositoryInterfaceContract::class, TeacherRepositoryImplementation::class);
        $this->app->singleton(TeacherService::class, function ($app) {
            return new TeacherService($app->make(TeacherRepositoryInterfaceContract::class));
        });

        // Student bindings
        $this->app->bind(StudentRepositoryInterfaceContract::class, StudentRepositoryImplementation::class);
        $this->app->singleton(StudentService::class, function ($app) {
            return new StudentService($app->make(StudentRepositoryInterfaceContract::class));
        });

        // Staff bindings
        $this->app->bind(StaffRepositoryInterfaceContract::class, StaffRepositoryImplementation::class);
        $this->app->singleton(StaffService::class, function ($app) {
            return new StaffService($app->make(StaffRepositoryInterfaceContract::class));
        });

        // Course bindings
        $this->app->bind(CourseRepositoryInterfaceContract::class, CourseRepositoryImplementation::class);
        $this->app->singleton(CourseService::class, function ($app) {
            return new CourseService($app->make(CourseRepositoryInterfaceContract::class));
        });

        // Course class bindings
        $this->app->bind(CourseClassRepositoryInterfaceContract::class, CourseClassRepositoryImplementation::class);
        $this->app->singleton(CourseClassService::class, function ($app) {
            return new CourseClassService($app->make(CourseClassRepositoryInterfaceContract::class));
        });

        // Payment bindings
        $this->app->bind(PaymentRepositoryInterfaceContract::class, PaymentRepositoryImplementation::class);
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService($app->make(PaymentRepositoryInterfaceContract::class));
        });

        // Announcement bindings
        $this->app->bind(AnnouncementRepositoryInterfaceContract::class, AnnouncementRepositoryImplementation::class);
        $this->app->singleton(AnnouncementService::class, function ($app) {
            return new AnnouncementService($app->make(AnnouncementRepositoryInterfaceContract::class));
        });

        // Notification bindings
        $this->app->bind(NotificationRepositoryInterfaceContract::class, NotificationRepositoryImplementation::class);
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService($app->make(NotificationRepositoryInterfaceContract::class));
        });

        // ── Course Class ──────────────────────────────────────────────────────────────
        $this->app->bind(
            \App\Repositories\Course\CourseClassRepositoryInterface::class,
            \App\Repositories\Course\CourseClassRepository::class,
        );

        // ── Course Material ───────────────────────────────────────────────────────────
        $this->app->bind(
            \App\Repositories\Course\CourseMaterialRepositoryInterface::class,
            \App\Repositories\Course\CourseMaterialRepository::class,
        );


        $this->app->singleton(OtpService::class);

        //Admission bindings
        $this->app->bind(
            AdmissionRepositoryInterface::class,
            AdmissionRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Services\Cron;

use App\Models\CourseClass;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;

class ClassReminderService
{
    public function sendUpcomingClassReminders(): int
    {
        $count = 0;

        $start = now()->addMinutes(5)->startOfMinute();
        $end = now()->addMinutes(5)->endOfMinute();

        $classes = CourseClass::with([
            'course.admissions.student.user'
        ])
        ->whereBetween('started_at', [
            $start,
            $end
        ])
        ->get();

        foreach ($classes as $class) {

            foreach ($class->course->admissions as $admission) {

                if ($admission->status !== 'active')
                    continue;

                /*
                 * Your Push Notification
                 */

                // app(NotificationService::class)
                //     ->toUser(
                //         $admission->student->user_id,
                //         [
                //             'title' => 'Class starts in 5 minutes',
                //             'body' => $class->course->name
                //         ],
                //         [
                //             'type' => 'class_reminder',
                //             'course_class_id' => $class->id
                //         ]
                //     );

                $count++;
            }
        }

        return $count;
    }
}

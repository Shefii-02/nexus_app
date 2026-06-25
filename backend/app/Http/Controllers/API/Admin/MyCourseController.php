<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\DTOs\MessageDTO;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\MyCourseResource;
use App\Models\Course;
use App\Models\DeletedMessage;
use App\Models\Message;
use App\Models\MessageReport;
use App\Services\Message\MessageService;
use Illuminate\Http\Request;

class MyCourseController extends Controller
{

    public function index(Request $request)
    {

        $user = $request->user();

        if ($user->acc_type == 'teacher') {
            $myCourses = Course::with('teachers')->whereHas('teachers', function ($p0) use ($user) {
                $p0->where('teacher_id', $user->id);
            })->get();
        } else {
            $myCourses = Course::with('students')->whereHas('students', function ($p0) use ($user) {
                $p0->where('teacher_id', $user->id);
            })->get();
        }


        $my_courses = MyCourseResource::collection($myCourses);

        $getCourses = [
            'success' => true,
            'data' =>  $my_courses,
            // [
            // [
            //     'id'                => 1,
            //     'name'              => 'Flutter & Dart – Complete Bootcamp',
            //     'description'       => 'Build stunning cross-platform apps from scratch. Covers widgets, state management, APIs and deployment.',
            //     'teacher_name'      => 'Arjun Menon',
            //     'teacher_avatar'    => '👨‍🏫',
            //     'category'          => 'Mobile Development',
            //     'total_classes'     => 24,
            //     'completed_classes' => 14,
            //     'expires_at'        => '2025-08-10T00:00:00.000000Z',
            //     'status'            => 'active',
            // ],
            // [
            //     'id'                => 2,
            //     'name'              => 'Laravel REST API Masterclass',
            //     'description'       => 'Design and build production-grade REST APIs with Laravel, Sanctum, queues and real-time events.',
            //     'teacher_name'      => 'Priya Nair',
            //     'teacher_avatar'    => '👩‍💻',
            //     'category'          => 'Backend Development',
            //     'total_classes'     => 18,
            //     'completed_classes' => 18,
            //     'expires_at'        => '2025-02-20T00:00:00.000000Z',
            //     'status'            => 'expired',
            // ],
            // [
            //     'id'                => 3,
            //     'name'              => 'UI/UX Design Fundamentals',
            //     'description'       => 'Master Figma, design systems, typography and user research.',
            //     'teacher_name'      => 'Sneha Pillai',
            //     'teacher_avatar'    => '🎨',
            //     'category'          => 'Design',
            //     'total_classes'     => 12,
            //     'completed_classes' => 3,
            //     'expires_at'        => '2025-09-10T00:00:00.000000Z',
            //     'status'            => 'active',
            // ],
            // [
            //     'id'                => 4,
            //     'name'              => 'Python for Data Science',
            //     'description'       => 'Pandas, NumPy, Matplotlib and scikit-learn.',
            //     'teacher_name'      => 'Rahul Krishnan',
            //     'teacher_avatar'    => '🧑‍🔬',
            //     'category'          => 'Data Science',
            //     'total_classes'     => 20,
            //     'completed_classes' => 0,
            //     'expires_at'        => '2025-10-10T00:00:00.000000Z',
            //     'status'            => 'active',
            // ],
            // ],
        ];

        return response()->json(
            $getCourses
        );
    }
}
